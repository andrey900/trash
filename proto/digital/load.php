#!/usr/bin/php
<?
define('ANIART_DEBUG', true);
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define('BX_NO_ACCELERATOR_RESET', true);
define("LOG_FILENAME", dirname(__FILE__).'/log/cron_work.txt');

define('IMPORT_ACTIONS', false);
define('IMPORT_GOODS', true);
define('IMPORT_OFFERS', false);

// UID документа и инфоблоков
$const_uid_import = 'f7eb5058-715c-11e4-bdee-0025906944e4';
$const_uid_action = 'xxxxxxxx-715c-11e4-bdee-0025906944e4';

@set_time_limit(0);
ini_set("memory_limit", "2048M");

if (!$_SERVER["DOCUMENT_ROOT"]) {
	// забираем отсюда конфиг
	require(dirname(__FILE__).'/config.php');
} else {
	die("Run only in console. Stop program...");  // только с консоли
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

error_reporting(E_ERROR | E_PARSE);
ini_set('DisplayError',1);

require(dirname(__FILE__).'/classes/Strategy.php');
require(dirname(__FILE__).'/StructureData.php');

require(dirname(__FILE__).'/classes/CSimpleAttribute.php');
require(dirname(__FILE__).'/classes/SimpleDecimalAttribute.php');
require(dirname(__FILE__).'/classes/DimensionsAttribute.php');
require(dirname(__FILE__).'/classes/CheckboxYNAttribute.php');
require(dirname(__FILE__).'/classes/SimpleDictionaryAttribute.php');
require(dirname(__FILE__).'/classes/BrandAttribute.php');
require(dirname(__FILE__).'/classes/DictionaryAttribute.php');
require(dirname(__FILE__).'/classes/DictionaryMultiAttribute.php');
require(dirname(__FILE__).'/classes/DecimalRangeDictionaryAttribute.php');
require(dirname(__FILE__).'/classes/DecimalDimansialRangeDictionaryAttribute.php');
require(dirname(__FILE__).'/classes/DictionaryColorAttribute.php');
require(dirname(__FILE__).'/classes/ParseNameDictionaryAttribute.php');
require(dirname(__FILE__).'/classes/DictionaryListValueAttribute.php');
require(dirname(__FILE__).'/classes/CParseRegDictionaryAttribute.php');

require(dirname(__FILE__).'/classes/CDictionaryMultyAttributeMinMax.php');

require(dirname(__FILE__).'/classes/PropertyController.php');

require(dirname(__FILE__).'/load_classes.php');
require(dirname(__FILE__).'/classes.php');
require(dirname(__FILE__).'/classes/XMLDocument.php');


$str_zip_file_name = '1CFILES.zip';

$str_file_name_work_flag = dirname(__FILE__).'/cron_work.pid';
$str_file_load_dir = $_SERVER["DOCUMENT_ROOT"].'/upload/1c_work_catalog/';
$str_file_archive_dir = $_SERVER["DOCUMENT_ROOT"].'/upload/archive/';
$str_file_load_materials = $_SERVER["DOCUMENT_ROOT"].'/upload/1c_work_catalog/import.xml';
$str_file_load_offers = $_SERVER["DOCUMENT_ROOT"].'/upload/1c_work_catalog/offers.xml';
$str_offers_filename = '';

if ( file_exists($str_file_name_work_flag)) {
	// TODO если файлу много часов, надо слать уведомление о проблемах
	die('runing...');
}

$b_need_load = false;
$dir = new DirectoryIterator($_SERVER["DOCUMENT_ROOT"]."/upload/anyart_up/");

foreach ($dir as $fileinfo) {
	// смотрим текущее состояние
	$path_parts = pathinfo( $fileinfo->getPathname() );
	$int_size = $fileinfo->getSize();

	// если нашли ZIP
	if ( !strcasecmp($path_parts['basename'],$str_zip_file_name) ) {
		sleep(ANIART_DEBUG ? 0 : 5);
		if ($int_size != filesize($fileinfo->getPathname())) {
			continue;
		}
	
		$zip = new ZipArchive;
		$res = $zip->open($fileinfo->getPathname());
		if ($res === TRUE) {

		  $zip->extractTo($str_file_load_dir);
		  $zip->close();
		  AddMessage2Log("Найден $str_zip_file_name  и распакован для загрузки");
		} else {
		  AddMessage2Log("Ошибка при распаковке $str_zip_file_name  Процесс импорта прерван!   Свяжитесь с администратором 1С");
		  //mail('andrewleshchynskyy@gmail.com,renata.mussina@gmail.com, m.sultanayev@gmail.com, sl-vladimir@hotmail.com', 'Error in site import process', 'Attention!  Error in site import process');
		  die();
		}
		//TODO
		//rename($fileinfo->getPathname(),$str_file_archive_dir.date('Ymd_Hi').'.zip');
		$b_need_load = true;
	}
}



if ($b_need_load ) {
	// ставим флаг что мы работаем
    if (!ANIART_DEBUG )
	    $h_file = fopen($str_file_name_work_flag,'w+');
} else {
	die('No work ...');
}


global $DB;
CModule::IncludeModule ('iblock');
$str_error = '';

$ar_calalog_SKU_UID = Array ();


////////////////////////////////////////////////////////////////////////////////
/// Читаем справочники и акции
////////////////////////////////////////////////////////////////////////////////

// Создаем новый документ и позиционируемся в нем
$obj_newdoc = new CXMLDocument($const_uid_import, 'Гибкий каталог');
$newdoc = $obj_newdoc->doc;
$group_root_for_insert  = $obj_newdoc->GetPositionGroup();


// Создаем новый документ ЦЕН и позиционируемся в нем
$obj_doc_price = new CXMLOffersDocument($const_uid_import, 'Гибкий каталог');
$newdoc_price = $obj_doc_price->doc;


// Создаем новый документ АКЦИИ и позиционируемся в нем
$obj_doc_action = new CXMLDocument($const_uid_action, 'Подарки');
$newdoc_action = $obj_doc_action->doc;


//=====================


$i=0;

// Документ - источник 
$doc = new DOMDocument;
$doc->preserveWhiteSpace = false;
$doc->load($str_file_load_materials);
$xpath = new DOMXPath($doc);
$nodes_level_1 = $xpath->query("/КоммерческаяИнформация/Классификатор/Группы/Группа");
if ($nodes_level_1->length) {
	$node_level_1 = $nodes_level_1->item(0); 
}

$nodes_action = $xpath->query("/КоммерческаяИнформация/Классификатор/Акции/Акция");

$nodes_tovar= $xpath->query('/КоммерческаяИнформация/Каталог/Товары/Товар');


//////////////////////////////////////////////////////////////////////////////
// Этап №1 прокручиваем все разделы и раскладываем по инфоблокам
//////////////////////////////////////////////////////////////////////////////


// читаем структуру секций, которые уже есть на сайте
$ar_site_catalog_structure = GetCatalogStructure( SHARE_CATALOG_IBLOCK_ID );
$ar_site_catalog_elements = GetCatalogXMl2ID( SHARE_CATALOG_IBLOCK_ID );



// импортируем акции
if(IMPORT_ACTIONS){
	$j=0;
	$ar_action = array();
	$date_now = new DateTime();
	$ar_action_tovars = array();
	foreach($nodes_action as $node_action) {
	    $node_tovar = $obj_doc_action->GetNewEntry();
	
	    // установка стандартных полей
	    CXMLNodeWork::CopyNodeValueByName($newdoc_action, $node_tovar,$node_action,'Ид');
	    CXMLNodeWork::CopyNodeValueByName($newdoc_action, $node_tovar,$node_action,'Наименование');
	    $UID = CXMLNodeWork::GetNodeValueByName($node_action,'Ид');
		
	    $ar_tovars = array();
	    $i=0;
	    $node_sub_tovars = CXMLNodeWork::GetNodeByName($node_action,'Товары');
	    foreach($node_sub_tovars->childNodes as $node_sub_tovar) {
	        $xml_id = CXMLNodeWork::GetNodeValueByName($node_sub_tovar,'Ид');
		$ar_action_tovars[] = $xml_id;
	        if ( $id = $ar_site_catalog_elements[$xml_id] ) {
	            $ar_tovars[] = Array($xml_id,++$i);
	        }
	    }
	
	    $node_list_property = $newdoc_action->createElement('ЗначенияСвойств','');
	
	    // Акции	
	    if( $str_logic = CXMLNodeWork::GetNodeValueByName($node_action,'Логика') ) {
	
		if ( !strcasecmp($str_logic,'ИЛИ') ) {
			$str_logic = 'OR';
		} elseif( !strcasecmp($str_logic,'И') ) {
			$str_logic = 'AND';
		} else {
			$str_logic = 'OR';
		}
	        $node_date = CXMLNodeWork::AddPropertyNode($newdoc_action,'LOGIC', $str_logic );
	
	    } else {
	        $node_date = CXMLNodeWork::AddPropertyNode($newdoc_action,'LOGIC', 'OR' );
	    }
	    $node_list_property->appendChild( $node_date );
	
	    //CML2_ACTIVE_FROM
	    if ( $start = CXMLNodeWork::GetNodeValueByName($node_action,'датаначала') ) {
	        $date_start = DateTime::createFromFormat('Y-m-d',$start);
	        $node_date = CXMLNodeWork::AddPropertyNode($newdoc_action,'CML2_ACTIVE_FROM', $start );
	    } else {
	        $date_start = new DateTime();
	        $node_date = CXMLNodeWork::AddPropertyNode($newdoc_action,'CML2_ACTIVE_FROM', $date_start->format('Y-m-d') );
	    }
	    $node_list_property->appendChild( $node_date );
	    
	    //CML2_ACTIVE_TO
	    if ( $finish = CXMLNodeWork::GetNodeValueByName($node_action,'датаокончания') ) {
	        $date_finish = DateTime::createFromFormat('Y-m-d',$finish);
	        $node_date = CXMLNodeWork::AddPropertyNode($newdoc_action,'CML2_ACTIVE_TO', $finish );
	    } else {
	        $date_finish = new DateTime();
	        $date_finish->add(new DateInterval('P10D'));
	        $node_date = CXMLNodeWork::AddPropertyNode($newdoc_action,'CML2_ACTIVE_TO', $date_finish->format('Y-m-d') );
	    }
	    $node_list_property->appendChild( $node_date );
	
	    //if ( $UID !='5847245e-d8e9-11e3-b097-0019999914fa' ) continue;
	    //printf("%d \t %s \t start:%s    Now: %s          finish:%s\n",count($ar_tovars),$UID,$date_start->format('Y-m-d H:i:s'), $date_now->format('Y-m-d H:i:s'), $date_finish->format('Y-m-d H:i:s') );
	    
	    // акция должна быть активной
	    if ( count($ar_tovars) && ($date_now->getTimestamp() >= $date_start->getTimestamp()) && ($date_now->getTimestamp()+1 < $date_finish->getTimestamp()) ) {
	        $node_list = CXMLNodeWork::AddMulPropertyNode($newdoc_action,'LINK_TO_PRODUCT',$ar_tovars);
	    	//$node_list = CXMLNodeWork::AddMulPropertyNode($newdoc_action,'GOODS',$ar_tovars);
	        $node_list_property->appendChild( $node_list );
	        $node_active = CXMLNodeWork::AddPropertyNode($newdoc_action,'CML2_ACTIVE', 'true' );
	        $ar_action[$UID] = array(1);
	
	    } else {
	        $node_active = CXMLNodeWork::AddPropertyNode($newdoc_action,'CML2_ACTIVE', 'false' );
	    }
	    $node_list_property->appendChild( $node_active );
	    //
	
	    $node_tovar->appendChild( $node_list_property );
	    $obj_doc_action->InsertEntry($node_tovar);
	}
	
	$UID_value["FILE"] = $_SERVER["DOCUMENT_ROOT"]."upload/1c_work_catalog/_action.xml";
	$obj_doc_action->SaveDoc( $UID_value["FILE"] );
	//TODO
	$obj_doc_action->LoadToIBLOCK();
	unset($obj_doc_action);
}


//////////////////////////////////////////
// Этап № 2   товары
// 
if(IMPORT_GOODS){
	// импортируем структуры
	$ar_group_UID = array();
	foreach ($nodes_level_1 as $node_group)
	{
	    // TODO здесь будем проверять на новые секции
	    // TODO раскомментить  $group_root_for_insert->appendChild(CStructureCopy::IterationNode($newdoc,$node_group));
	    if ( in_array(CXMLNodeWork::GetNodeValueByName($node_group,'Ид'),$const_ar_uid_section_import) ) {
	        $ar_group_UID = array_merge($ar_group_UID,CStructureCopy::GetGroupsUID($node_group));
	    }
	}
	
	$obj_current_tovar = StorageSingleton::getInstance();
	$obj_PropertyController = new CPropertyController($ar_structure_data,$newdoc, $doc);
	
	
	//$obj = new CBrandAttribute($newdoc, $doc, 'BRAND', Array(), 2);
	//print_r($obj->ar_data); die();
	
	$i=0;
	$k=0;
	$ar_uid = array(); // товары которые есть в выгрузке
	$ar_tovar_to_catalog = Array();
	$str_report_tovars_no_picture = '';
	// импортируем товары
	foreach ($nodes_tovar as $node_old_tovar) {
	
		//первые 50 берем
		if($i >49)
			break;
	
		$DEL = CXMLNodeWork::GetNodeValueByName($node_old_tovar,'Статус');
		if($DEL == "Удален")
			continue; //пропускаем товары со статусом "удален".
		
	    $k++;
	    // фиксируем присутствие товара
	    $UID = CXMLNodeWork::GetNodeValueByName($node_old_tovar,'Ид');
	    $ar_uid[$UID] = array("ID"=>$UID);
	
	    $node = CXMLNodeWork::GetNodeByName($node_old_tovar,'Группы');
	    $str_group = CXMLNodeWork::GetNodeValueByName($node,'Ид');
	
	    // ограничение по импортируемым товарным группам
	    if ( ANIART_DEBUG && !in_array($str_group,$ar_group_UID) ) {
	        continue;
	    }
	
	    // ограничение для тестирования
	    if ( !IsProcessImportGroup($str_group) ) {
	        continue;
	    }
	
	    $node_tovar = $obj_newdoc->GetNewEntry();
	    $node_offer = $obj_doc_price->GetNewEntry();
	
	    
	    // GROUPS
	    // проверка, когда товар приходит для несуществующей группы - вставляем в специальную группу для ручной обработки XXX
	    $node = CXMLNodeWork::GetNodeByName($node_old_tovar,'Группы');   
	    
	    
	    if( $node && $str_group_UID = CXMLNodeWork::GetNodeValueByName($node,'Ид') ) {
	
	        if ( isset($ar_site_catalog_structure[$str_group_UID]) ) {
	            // копируем принадлежность к группе
	            // так как не может быть групп у товара несколько, можно переписать более просто через CopyXMLSubTree
	            $uid_group = CXMLNodeWork::CopyGroupsNode($newdoc, $node_tovar,$node_old_tovar);
	        } else {
	            // новая группа, которой нет в структуре = в XXX
	            $node_group = $newdoc->createElement('Группы', '');
	            $node_group->appendChild( $newdoc->createElement('Ид', 'XXX') );
	            $node_tovar->appendChild($node_group);
	        }
	    } else {
	        // товар без группы = в XXX
	        $node_group = $newdoc->createElement('Группы', '');
	        $node_group->appendChild( $newdoc->createElement('Ид', 'XXX') );
	        $node_tovar->appendChild($node_group);
	    }
	    //
	
	      
	
	    $node_list_property = $newdoc->createElement('ЗначенияСвойств','');
	    $node_old_property = CXMLNodeWork::GetNodeByName($node_old_tovar,'ЗначенияСвойств');
	    CXMLNodeWork::AddPropertyNode($newdoc,'CML2_ACTIVE','true');
	    //
	    $obj_current_tovar::setOldTovar( $node_old_tovar );
	
	
	    // копирование стандартных полей
	    CXMLNodeWork::CopyNodeValueByName($newdoc, $node_tovar,$node_old_tovar,'Ид');
	
		//TODO
	    //if ($UID != 'caaf8dbe-2cbe-11e3-9858-00199999144e') continue;
	
	    CXMLNodeWork::CopyNodeValueByName($newdoc, $node_tovar,$node_old_tovar,'ШтрихКод');
	    CXMLNodeWork::CopyNodeValueByName($newdoc, $node_tovar,$node_old_tovar,'Артикул');
	    CXMLNodeWork::CopyNodeValueByName($newdoc, $node_tovar,$node_old_tovar,'Наименование');
	    CXMLNodeWork::CopyNodeValueByName($newdoc, $node_tovar,$node_old_tovar,'Описание');
	    
	    CXMLNodeWork::CopyNodeValueByName($newdoc, $node_tovar,$node_old_tovar,'Статус');
	      
	    // тоже для цен
	    CXMLNodeWork::CopyNodeValueByName($newdoc_price, $node_offer,$node_old_tovar,'Ид');
	    CXMLNodeWork::CopyNodeValueByName($newdoc_price, $node_offer,$node_old_tovar,'ШтрихКод');
	    CXMLNodeWork::CopyNodeValueByName($newdoc_price, $node_offer,$node_old_tovar,'Артикул');
	    CXMLNodeWork::CopyNodeValueByName($newdoc_price, $node_offer,$node_old_tovar,'Наименование');
	    CXMLNodeWork::CopyXMLSubTree($newdoc_price,$node_old_tovar,$node_offer,"Цены");
	    CXMLNodeWork::CopyNodeValueByName($newdoc_price, $node_offer,$node_old_tovar,'Количество');
	    $node_offer->appendChild( $newdoc_price->createElement('Количество', IntVal($ar_sku_count[$UID]['CNT'])) );
	
	    if(CXMLNodeWork::GetNodeValueByName($node_old_tovar,'Архивный') == "true")
	     	$node_tovar->appendChild( $newdoc->createElement('Статус', 'Удален' ) ); // снимаем активность
	    	
	    // устанавливаем свойство если товар вновь появился GOODS_LOW_STOCK
	    $node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc, 'GOODS_LOW_STOCK', ($ar_sku_count[$UID]['CNT'] < 20) && ($ar_sku_count[$UID]['CNT']>0) ? 'GOODS_LOW_STOCK_YES':'') );
	
	    // копирование "ЗначенияРеквизитов"
	    CXMLNodeWork::CopyXMLSubTree($newdoc,$node_old_tovar,$node_tovar,"ЗначенияРеквизитов");
	
	    // копирование "СтавкиНалогов"
	    CXMLNodeWork::CopyXMLSubTree($newdoc,$node_old_tovar,$node_tovar,"СтавкиНалогов");
	
	    // копирование "ХарактеристикиТовара"
	    CXMLNodeWork::CopyXMLSubTree($newdoc,$node_old_tovar,$node_tovar,"ХарактеристикиТовара");
	
	    // копируем ID_HDImages
	    if ($node_list = CXMLNodeWork::CopyNodeByNameFromPropertyList($newdoc, $node_old_tovar, 'ID_HDImages') ) {
	        $node_list_property->appendChild( $node_list );
	    }
	
	    /*// копируем Новинки
	    if ($node_list = CXMLNodeWork::CopyNodeByNameFromPropertyList($newdoc, $node_old_tovar, 'ID_СправочникНовинки') ) {
	        $node_list_property->appendChild( $node_list );
	    } else {
	        $node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'ID_СправочникНовинки', '') );
	    }
	
	    // копируем Спецпредложения
	    if ($node_list = CXMLNodeWork::CopyNodeByNameFromPropertyList($newdoc, $node_old_tovar, 'ID_СправочникСпецпредложение') ) {
	        $node_list_property->appendChild( $node_list );
	    } else {
	        $node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'ID_СправочникСпецпредложение', '') );
	    }*/
	
		// копируем Наличие на сайте
	    if(CXMLNodeWork::GetNodeValueByName($node_old_tovar,'НаличиеНаСайте') == "true"){
	        $node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'ID_СправочникНаличиеНаСайте', 'ID_СправочникНаличиеНаСайте_Значение') );
	        $node_tovar->appendChild( $newdoc->createElement('Количество', DEFAULT_QUANTITY) ); // Установка количества
	    } else {
	        $node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'ID_СправочникНаличиеНаСайте', '') );
	    }
	    
	    // копируем Архивный
	    if(CXMLNodeWork::GetNodeValueByName($node_old_tovar,'Архивный') == "true"){
	    	$node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'ID_СправочникАрхивный', 'ID_СправочникАрхивный_Значение') );
	    } else {
	    	$node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'ID_СправочникАрхивный', '') );
	    }
	    
	    // копируем ПОДАРОК по особенному
	    $ar_active_action = array();
	    $ar_val = CXMLNodeWork::GetNodeMulValueByNameFromPropertyList($node_old_property,'ID_Акции');
	    if ( count($ar_val) ) {
	        while ($val = array_pop($ar_val) ) {
	            if ( $ar_action[$val] ) {
			$ar_active_action[] = Array($val,'');
	
	            }
	        }
	        if (count($ar_active_action)) {
			$node_list_property->appendChild( CXMLNodeWork::AddMulPropertyNode($newdoc,'ID_Акции', $ar_active_action ));
	        }
	    } else {
			$node_list_property->appendChild( CXMLNodeWork::AddMulPropertyNode($newdoc,'ID_Акции', '' ));
	    }
	
	    // формируем отдельное свойство MARKETING
	    unset($ar);
	    $ar[] = array('');
	    if ( CXMLNodeWork::GetNodeValueByNameFromPropertyList($node_old_property,'ID_СправочникНовинки') ) {
	        $ar[] = Array('NEW','Новинка');
	    }
	    if ( CXMLNodeWork::GetNodeValueByNameFromPropertyList($node_old_property,'ID_СправочникСпецпредложение') ) {
	        $ar[] = Array('RECOMMEND','Спецпредложение');
	    }
	
	    $ar_val = CXMLNodeWork::GetNodeMulValueByNameFromPropertyList($node_old_property,'ID_Акции');
	    if ( count($ar_val) ) {
	        while ($val = array_pop($ar_val) ) {
	            if ( $ar_action[$val] ) {
	                $ar[] = Array('XML_GIFT','Товар с подарком');
	                break;
	            }
	        }
	    }
	     
		//ТОЛЬКО У НАС
	    if ( CXMLNodeWork::GetNodeValueByNameFromPropertyList($node_old_property,'ID_СправочникТолькоУНАС') ) {
	       $ar[] = Array('XML_ONLY','Только в DigitalVideo');
	    }
	
	    //HOT_PRICE
	    if ( CXMLNodeWork::GetNodeValueByNameFromPropertyList($node_old_property,'ID_СправочникГорящаяЦена') ) {
	    	$ar[] = Array('XML_HOT_PRICE','Горящие цены');
	    }
	    //END HOT_PRICE
	    if (count($ar)) {
	        $node_list_property->appendChild( CXMLNodeWork::AddMulPropertyNode($newdoc,'HIT', $ar) );
	    }
	
	    if ( $str = CXMLNodeWork::GetNodeValueByNameFromPropertyList($node_old_property,'ID_URLКартинок') ) {
	        $node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'URL_KARTINOK', $str) );
		    $ar_image_path = explode(';',$str);
		    $str_image_path = preg_replace('#^/#','',$ar_image_path[0]);
		    $node_tovar->appendChild( $newdoc->createElement('Картинка', $str_image_path ) );
		    
		    $ar_images = array();
		    if(count($ar_image_path)>1){
		    	foreach($ar_image_path as $i_image_path => $image_path){
		    		if($i_image_path > 0){
		    			$ar_images[] =  Array($image_path, "");
		    		}
		    	}
		    	$node_list_property->appendChild( CXMLNodeWork::AddMulPropertyNode($newdoc,'CML2_PICTURES', $ar_images) );
		    }
		    
	
			// формирование PREVIEW также идет за счет настроек инфоблока при импорте
			/* Убрали, так как 130-130 формируется только при просмотре
			$destinationFile= preg_replace('#(.+)/(.+)$#','$1',$str_image_path);
			$node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'CML2_PREVIEW_PICTURE', $destinationFile.'/small130x130.01.jpg') );
			*/
			/*относительные пути*/
			/*$destinationFile= preg_replace('#(.+)/(.+)$#','$1',$str_image_path);
			CFile::ResizeImageFile($str_image_path,$str_new_file = $destinationFile.'/small130x130.01.jpg', array("width" => 130, "height" => 130),BX_RESIZE_IMAGE_PROPORTIONAL_ALT );
			$node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'CML2_PREVIEW_PICTURE', $str_new_file ) );*/
				 
			$destinationFile= preg_replace('#(.+)/(.+)$#','$1',$str_image_path);
			$str_image_path = $str_file_load_dir.$str_image_path;
			 
			CFile::ResizeImageFile($str_image_path,$str_new_file = $str_file_load_dir.$destinationFile.'/small130x130.01.jpg', array("width" => 130, "height" => 130),BX_RESIZE_IMAGE_PROPORTIONAL_ALT );
			if(file_exists($str_new_file))
				$str_new_file = $destinationFile.'/small130x130.01.jpg';
			$node_list_property->appendChild( CXMLNodeWork::AddPropertyNode($newdoc,'CML2_PREVIEW_PICTURE', $str_new_file ) );
			 
			/*конец относительные пути*/
	    } else {
	       // отчет о товарах без картинок
			if (in_array($UID,$ar_action_tovars)) {
				$str_report_tovars_no_picture.= sprintf("%-60s\t %-140s\n",$UID, CXMLNodeWork::GetNodeValueByName($node_old_tovar,'Наименование') );
			}
	    }
	
	    
	    // выполняем разбор Характеристик на свойства
	    $obj_PropertyController->ProcessTovarAttributes($uid_group,$node_old_tovar,$node_list_property);
	
	    // фиксируем свойства у товара
	    $node_tovar->appendChild( $node_list_property );
	
	    // копирование "Цены"
	    CXMLNodeWork::CopyXMLSubTree($newdoc,$node_old_tovar,$node_tovar,"Цены");
	    
	    // фиксируем товар в документе
	    $obj_newdoc->InsertEntry($node_tovar);
	
	    // фиксируем цену в документе
	    $obj_doc_price->InsertEntry($node_offer);
	    $ar_tovar_to_catalog[$UID] = $str_group;
	
	    $i++;
	    //if (ANIART_DEBUG && ($i >= 10) ) break;
	
	}
	
	$UID_value["FILE"] = $_SERVER["DOCUMENT_ROOT"]."upload/1c_work_catalog/_import.xml";
	$obj_newdoc->SaveDoc( $UID_value["FILE"] );
	//TODO
	
	$obj_newdoc->LoadToIBLOCK();
	
	unset($obj_newdoc);
	AddMessage2Log(sprintf('=======================  Loaded %d tovars',$i));
	
	if (ANIART_DEBUG) {
	    echo "\n>>$i   total:$k\n";
	}
}
////// ===========================================================================================================


////////////////////////////////////////// формируем SKU
// Этап № 3   теперь в первую очередь обрабатываем SKU
// Создаем новый документ ЦЕН и позиционируемся в нем
if(IMPORT_OFFERS){
	$obj_newdoc_sku = new CXMLOffersSKUDocument($const_uid_import, 'Пакет предложений НОВЫЙ');
	$newdoc_sku = $obj_newdoc_sku->doc;
	
	
	$doc_price = new DOMDocument;
	$doc_price->load($str_file_load_offers);
	$xpath_doc_price = new DOMXPath($doc_price);
	$nodes = $xpath_doc_price->query("/КоммерческаяИнформация/ПакетПредложений/Предложения/Предложение");
	
	$ii=0;
	$jj=0;
	$ar_sku_uid = array();
	$ar_sku_count = array();
	foreach ($nodes as $node_old_offer) {
	
		$UID = CXMLNodeWork::GetNodeValueByName($node_old_offer,'Ид');
		$uid_master = preg_replace("/#(.+)/",'',$UID);
	
		$ar_sku_uid[$UID] = $UID;
		$jj++;
	
		//TODO
		//if ($uid_master != 'caaf8dbe-2cbe-11e3-9858-00199999144e') continue;
	
		/*
		 if ( !$ar_tovar_to_catalog[$uid_master] ) {
		continue;
		}
		*/
		$int_cnt = Intval(CXMLNodeWork::GetNodeValueByName($node_old_offer,'Количество'));
		$ar_sku_count[$uid_master]['CNT']+=$int_cnt;
	
		$node_old_property = CXMLNodeWork::GetNodeByName($node_old_offer,'ЗначенияСвойств');
	
		$node_offer = $newdoc_sku->importNode($node_old_offer, true);
		$obj_newdoc_sku->InsertEntry($node_offer);
	
		$ii++;
	}
	
	$UID_value["FILE"] = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_work_catalog/_offers_sku.xml";
	$obj_newdoc_sku->SaveDoc( $UID_value["FILE"] );
	//TODO
	$obj_newdoc_sku->LoadToIBLOCK();
	
	AddMessage2Log(sprintf(' ======================= Loaded %d  SKU (total: %d)',$ii, $jj));
	unset($obj_newdoc_sku);
	////// ===========================================================================================================
	
	
	$UID_value["FILE"] = $_SERVER["DOCUMENT_ROOT"]."upload/1c_work_catalog/_offers.xml";
	$obj_doc_price->SaveDoc( $UID_value["FILE"] );
	//TODO
	$obj_doc_price->LoadToIBLOCK();
	
	unset($obj_doc_price);
}


/*
// ПОСТОБРАБОТКА файла ЦЕНЫ ТОВАРОВ
// пройтись по всем товарам и поставить им количество
$xpath_price = new DOMXPath($obj_doc_price->doc);
$nodes_level_1 = $xpath_price->query("/КоммерческаяИнформация/ПакетПредложений/Предложения/Предложение");

for ($i=0; $i < $nodes_level_1->length; $i++) {
	$UID = CXMLNodeWork::GetNodeValueByName($nodes_level_1->item($i),'Ид');
	if ( $UID )  {
		$nodes_level_1->item($i)->appendChild( $obj_doc_price->doc->createElement('Количество', IntVal($ar_sku_count[$UID])) );
	}
}
*/

die(); // ОПАСНО ! если делать тестовые загрузки, эту часть кода нужно отсекать, так как все улетит в ТОВАР ПРОДАН


//////////////////////////////////////////
// Этап №3
global $DB;
$res = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>SHARE_CATALOG_IBLOCK_ID,"INCLUDE_SUBSECTIONS"=>"Y","ACTIVE"=>"Y"),false, false, Array("ID","ACTIVE","XML_ID") );
for($j=0, $i=0; $ar = $res->GetNext(); $i++) {
    if( !is_array( $ar_uid[$ar["XML_ID"]] ) ) {
        //$DB->Query( $str = sprintf("UPDATE b_iblock_element SET ACTIVE='N' WHERE ID=%d",$ar["ID"]) );
	// не деактивируем, а ставим соответствующий признак
        $DB->Query( $str = sprintf("UPDATE  b_iblock_element_prop_s19 SET PROPERTY_293 = 1649 WHERE IBLOCK_ELEMENT_ID=%d",$ar["ID"]) );
        $j++;
    }
}

AddMessage2Log(sprintf(' ======================= Поставлено на НЕТ ТОВАРА  для %d  устаревших ТОВАРОВ  (total:%d)',$j, $i));



//////////////////////////////////////////
//// - чистим старые SKU
$be = new CIBlockElement;
$res = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>SHARE_SKU_IBLOCK_ID,"INCLUDE_SUBSECTIONS"=>"Y","ACTIVE"=>""),false, false, Array("ID","XML_ID") );
for($j=0, $i=0; $ar = $res->GetNext(); $i++) {
    if( !strlen($ar_sku_uid[$ar["XML_ID"]]) ) {
        if ($be->Delete( $ar["ID"] ) ) {
            $j++;
        } else {
            AddMessage2Log($be->LAST_ERROR);
        }
    }
}
unset($be);

/*
 * Проверка брендов на дубли. По Наименованию и XML_ID*/
$strDublicatBrandsByName = "";
$strDublicatBrandsByXmlId = "";
$strDublicatBrands = "";
$arDublicateBrands = array();
$arBrandNames = array();
$arBrandXmlIds = array();
$arSelectEl = Array("ID", "XML_ID", "NAME");
$arFilterEl = Array("IBLOCK_ID"=>BRANDS_IBLOCK_ID);
$resEl = CIBlockElement::GetList(Array(), $arFilterEl, false, false, $arSelectEl);
while($obEl = $resEl->GetNextElement())
{
	$arFieldsEl = $obEl->GetFields();

	if(in_array($arFieldsEl['NAME'], $arBrandNames)){
		$arDublicateBrands["NAME"][] = $arFieldsEl["NAME"];
	}
	if(in_array($arFieldsEl['XML_ID'], $arBrandXmlIds)){
		$arDublicateBrands["XML_ID"][] = $arFieldsEl["XML_ID"];
	}

	$arBrandNames[] = $arFieldsEl['NAME'];
	$arBrandXmlIds[] = $arFieldsEl['XML_ID'];
}

$arDublicateBrands["NAME"] = array_unique($arDublicateBrands["NAME"]);
$arDublicateBrands["XML_ID"] = array_unique($arDublicateBrands["XML_ID"]);

if(count($arDublicateBrands["NAME"])>0){
	foreach($arDublicateBrands["NAME"] as $name){
		$strDublicatBrandsByName .= $name."\n";
	}
}
if(count($arDublicateBrands["XML_ID"])>0){
	foreach($arDublicateBrands["XML_ID"] as $xml){
		$strDublicatBrandsByXmlId .= $xml."\n";
	}
}
if($strDublicatBrandsByName){
	$strDublicatBrandsByName = "Дублируются по 'Наименованию' бренды:\n".$strDublicatBrandsByName;
}
if($strDublicatBrandsByXmlId){
	$strDublicatBrandsByXmlId = "Дублируются по 'XML_ID' бренды:\n".$strDublicatBrandsByXmlId;
}
$strDublicatBrands = $strDublicatBrandsByName.$strDublicatBrandsByXmlId;

/*
 * Конец проверка брендов на дубли. По Наименованию и XML_ID*/

// REPORTS

//1
AddMessage2Log(sprintf(' ======================= Удалено  %d  устаревших SKU  (total:%d)',$j, $i));



//2
$str_email_send = 'andrew@aniart.com.ua, renata.mussina@gmail.com, m.sultanayev@gmail.com, sl-vladimir@hotmail.com,slv@technodom.kz,sma@technodom.kz,rmu@technodom.kz,sab@technodom.kz';

if ($str_report_tovars_no_picture) {
	$str_report_tovars_no_picture = sprintf("%-80s\t%-120s\n%'-200s\n%s%'-200s\n",'UID','Название','-',$str_report_tovars_no_picture,'-');
	mail($str_email_send, 'Tovars without picture', $str_report_tovars_no_picture);
}
//2.1
if ($strDublicatBrands) {	
	mail($str_email_send, 'Brands dublicats', $strDublicatBrands);
}

//3
mail($str_email_send, 'Success site import process', 'Импорт успешнен. Завершились все процессы.');

//4
AddMessage2Log('Импорт успешнен. Завершились все процессы.');



// снимаем флаги, удаляем файлы
if ( !ANIART_DEBUG) {
    unlink($str_file_name_work_flag);
    unlink($str_file_load_offers);
    unlink($str_file_load_materials);
}

// Очищаем неуправляемый кеш
// ak@
BXClearCache(true);
