#!/usr/bin/php
<?
	// 15-05-2014
	// модифицирован для запуска на crone 
	// для показа новых URL

@set_time_limit(0);

/////////////////////////////////////////////////////////////////////////////////////
// Константы
/////////////////////////////////////////////////////////////////////////////////////
define("NOT_CHECK_PERMISSIONS", true);
define("NO_KEEP_STATISTIC", true);

/////////////////////////////////////////////////////////////////////////////////////////////////////
// Чтобы не приходилось на разных хост-площадках прописывать руками $_SERVER["DOCUMENT_ROOT"]
// расчитываем пути к корню сайта. Логика скрипта считает, что скрипт выполняется в каталоге 1-го уровня
/////////////////////////////////////////////////////////////////////////////////////////////////////
$need_auth = false;

$f_info = pathinfo(__FILE__);

define("SCRIPT_SHORT_NAME", $f_info["filename"]);
define("LOG_FILENAME_SHORT",$f_info["filename"].".log"); // краткое имя файла логов
define("LOCK_FNAME", dirname(__FILE__)."/".SCRIPT_SHORT_NAME.".lock"); // файл-блокиратор
define("LOG_FILENAME", dirname(__FILE__)."/".SCRIPT_SHORT_NAME.".log"); // файл логов

if(!$_SERVER["DOCUMENT_ROOT"]) {
	$ar_parts_path = explode("/",$f_info["dirname"]);
	$path_root = "";

	for($i = 1; $i < count($ar_parts_path) - 1; $i++) {
		$path_root .= "/".$ar_parts_path[$i];
	}

	$_SERVER["DOCUMENT_ROOT"] = $path_root;
} else {
	$need_auth = true;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;

// если скрипт запущен через веб-интерфейс проверяем относится и пользователь к группе администраторов
if(($need_auth) && (!$USER->IsAdmin())) {
    die("Недостаточно прав для выполнения скрипта");
}
 

	global $APPLICATION;

	$META_DATA_FILE_NAME_SAVE = rel2abs($_SERVER["DOCUMENT_ROOT"], 'sitemap_000.xml');
	$META_DATA_FILE_NAME_SAVE_YANDEX = rel2abs($_SERVER["DOCUMENT_ROOT"], 'sitemap_yandex.xml');
	
	if (!CModule::IncludeModule('iblock'))
		return false;



	$str_MainMap = '<?xml version="1.0" encoding="UTF-8"?>
	<sitemapindex xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84	http://www.google.com/schemas/sitemap/0.84/siteindex.xsd">
		<sitemap>
			<loc>http://'.$_SERVER["SERVER_NAME"].'/sitemap_000.xml</loc>
			<lastmod>'.date('c').'</lastmod>
		</sitemap>
	</sitemapindex>';

	if ( $file_id = fopen(rel2abs($_SERVER["DOCUMENT_ROOT"], 'sitemap_index.xml'), "wb") ) {
		$int_content = fwrite($file_id, $str_MainMap );
		fclose($file_id);
	} else {
		echo 'Ошибка записи файла sitemap_index.xml';
	}
	//



	
	$str_template = "
		<url>
			<loc>http://%s%s</loc>
			<lastmod>%s</lastmod>
		</url>
	";


	$str_template_yandex = "
		<url>
			<loc>http://%s%s</loc>
			<lastmod>%s</lastmod>
			<priority>%s</priority>
			<changefreq>weekly</changefreq>
		</url>
	";

	$str_Content = '';
	$str_Content_yandex = '';

	
	
	
	$str_Content_header = '<?xml version="1.0" encoding="UTF-8"?>
	<urlset xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84	http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">';

	if ( $file_id = fopen($META_DATA_FILE_NAME_SAVE, "wb") ) {
		$int_content = fwrite($file_id, $str_Content_header );
	} else {
		echo 'Ошибка записи файла '.$META_DATA_FILE_NAME_SAVE;
	}

	//

	$str_Content_yandex = '<?xml version="1.0" encoding="UTF-8"?>
			<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	if ( $file_id_yandex = fopen($META_DATA_FILE_NAME_SAVE_YANDEX, "wb") ) {
		$int_content = fwrite($file_id_yandex, $str_Content_yandex );
	} else {
		echo 'Ошибка записи файла '.$META_DATA_FILE_NAME_SAVE_YANDEX;
	}


        	// статьи	
        	$filter_10 = Array("IBLOCK_ID" => CATALOG_IBLOCK_ID, 'ACTIVE' => 'Y');
        	$res_10 = CIBlockElement::GetList(Array("SORT"=>"ASC"), $filter_10);
        	for ($i=0; $ar_10 = $res_10->GetNext(); $i++) {

        		$str_Content= sprintf($str_template
        				,$_SERVER["SERVER_NAME"]
        				,$ar_10['DETAIL_PAGE_URL']
        				,date('c')
        				);



         		$str_Content_yandex= sprintf($str_template_yandex
         				,$_SERVER["SERVER_NAME"]
         				,$ar_10['DETAIL_PAGE_URL']
         				,date('Y-m-d')
         				,'0.'.rand(3,9)
         				);

        		
			$int_content = fwrite($file_id, $str_Content );        		
			$int_content = fwrite($file_id_yandex, $str_Content_yandex );        		
//	if($i==1) break;
        	}
        //

		$int_content = fwrite($file_id, '</urlset>' );
		fclose($file_id);

		//

		$int_content = fwrite($file_id_yandex, '</urlset>' );
		fclose($file_id_yandex);
//echo $i+$y;
?>
