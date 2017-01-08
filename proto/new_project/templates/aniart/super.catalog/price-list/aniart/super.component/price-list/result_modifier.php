<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// component text here
if (!CModule::IncludeModule("iblock")) 
	return false;

if (intval($arParams["IBLOCK_ID"]) <= 0)
{
	ShowMessage("Не указан инфоблок.");
	return;
}

$fileName = date("Ymd_H_i_s");
$fileName = $_SERVER['DOCUMENT_ROOT'].'/upload/catalog_images/waiting_files/'.$fileName.'.txt';

// Получаем разделы
$arFilter = array(
	"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
	"ACTIVE" => "Y",
	"GLOBAL_ACTIVE" => 'Y',
	'CNT_ACTIVE' => 'Y',
);

if(!empty($arParams['SECTIONS']))
{
	$arFilter['ID'] = $arParams['SECTIONS']; 
}

$arOrder = array(
	"LEFT_MARGIN" => "ASC",
);
$arSelect = array(
	"ID", 
	"NAME",
	"DEPTH_LEVEL",
	"LEFT_MARGIN",
	'RIGHT_MARGIN', 
);

// Получаем подразделы
$arFilterSub = array(
	"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
	"ACTIVE" => "Y",
	"GLOBAL_ACTIVE" => 'Y',
	'CNT_ACTIVE' => 'Y',
);
$arSelectSub = array(
	'ID',
	'NAME',
	"CODE",
);

$arResult['FILE_NAME'] = 'Прайс лист ';

$db_sections = CIblockSection::GetList($arOrder, $arFilter, true, $arSelect);
while ($arSectionSelected = $db_sections->GetNext(true, false))
{
	$arSectionNames[] = $arSectionSelected['NAME'];
	$arFilterSub['>=LEFT_MARGIN'] = $arSectionSelected['LEFT_MARGIN'];  
	$arFilterSub['<=RIGHT_MARGIN'] = $arSectionSelected['RIGHT_MARGIN'];  

	$db_sections_sub = CIblockSection::GetList($arOrder, $arFilterSub, true, $arSelectSub);
	while ($arSection = $db_sections_sub->GetNext(true, false))
	{
		if(empty($arSection['ELEMENT_CNT']))
			continue;

		$arResult["PRICE_SECTIONS"][$arSection['ID']] = $arSection;
	}
}

if(!empty($arSectionNames) && !empty($arParams['SECTIONS']))
	$arResult['FILE_NAME'].=implode(', ', $arSectionNames);

if(mb_strlen($arResult['FILE_NAME']) > 220)
	$arResult['FILE_NAME'] = mb_substr($arResult['FILE_NAME'], 0, 220).'...';
	
$arResult['FILE_NAME'].=' от '.date('d.m.Y');
$arResult['FILE_NAME'].='.xls';

// ПОлучаем элементы
$arFilterElements = array(
	"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
	"ACTIVE" => "Y",
	"SECTION_GLOBAL_ACTIVE" => 'Y',
	"INCLUDE_SUBSECTIONS" => "Y",
);

if(!empty($arParams['SECTIONS']))
{
	$arFilterElements['SECTION_ID'] = $arParams['SECTIONS']; 
}

$arSelectElements = array(
	'ID',
	'SECTION_ID',
	'NAME',
	'XML_ID',
	'DETAIL_PAGE_URL',
	'DETAIL_PICTURE',
	'CATALOG_GROUP_1',
);
$arOrderElements = array(
	'NAME' => 'ASC',
);

$db_element = CIblockElement::GetList($arOrderElements, $arFilterElements, false, false, $arSelectElements);
while ($arElement = $db_element->GetNext(true, false))
{
	$Element['ID'] = $arElement['ID'];
	$Element['XML_ID'] = $arElement['XML_ID'];
	$Element['NAME'] = $arElement['NAME'];
	$Element['DETAIL_PAGE_URL'] = $arElement['DETAIL_PAGE_URL'];
	$Element['PRICE'] = $arElement['CATALOG_PRICE_1'];
	
	if($arParams['SHOW_IMAGES'] == 'Y' && $arElement['DETAIL_PICTURE'])
		$Element['DETAIL_PICTURE'] = CFile::ResizeImageGet($arElement['DETAIL_PICTURE'], array('width' => 60, 'height' => 60), BX_RESIZE_IMAGE_PROPORTIONAL, true); 
	if($arParams['SHOW_IMAGES_PATH'] == 'Y' && $arElement['DETAIL_PICTURE']){
		/*$arResult['ZIP_ARCHIVE'] = array('DATE_ACCESS'=>date('d-m-Y H:i', time()+3600*2), 
										 'LINK_TO_ZIP'=>'ftp://asdf.asdf.zip',
										 'ITEMS'=>array());*/
		$Element['DETAIL_PICTURE_ORIGIN'] = CFile::GetPath($arElement['DETAIL_PICTURE']);
		$Element['DETAIL_PICTURE_PATH'] = $arResult["PRICE_SECTIONS"][$arElement['IBLOCK_SECTION_ID']]['CODE'].'/'.basename(CFile::GetPath($arElement['DETAIL_PICTURE']));
	}
	$arResult['ITEMS'][$arElement['IBLOCK_SECTION_ID']][] = $Element;
}

if( $arParams['SHOW_IMAGES_PATH'] == 'Y' ){
	$fp = fopen($fileName, 'w');
	$arrSection = array();
	foreach( $arResult['ITEMS'] as $sectId => $arElement ){
		$arrSection[] = $arResult["PRICE_SECTIONS"][$sectId]['CODE'];
		foreach( $arElement as $arItem ){
			fwrite($fp, $arResult["PRICE_SECTIONS"][$sectId]['CODE'].' ');
			fwrite($fp, $arItem['DETAIL_PICTURE_PATH'].' ');
			fwrite($fp, $arItem['DETAIL_PICTURE_ORIGIN']);
			fwrite($fp, "\r\n");
		}
	}
	fclose($fp);
	
	$strCodes = implode(" ", $arrSection);
	$arResult['ZIP_ARCHIVE']['LINK_TO_ZIP'] = 'http://'.$_SERVER['SERVER_NAME'].'/upload/catalog_images/finished_archives/'.md5(' '.$strCodes).'.zip';
	$arResult['ZIP_ARCHIVE']['DATE_ACCESS'] = date('d-m-Y H:i', time()+3600*2);
}

//p($arrSection, false);die;
$arResult["__TEMPLATE_FOLDER"] = $this->__folder; // saving template name to cache array
$this->__component->arResult = $arResult; // writing new $arResult to cache file
