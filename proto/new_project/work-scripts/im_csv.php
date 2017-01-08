<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog"); 
//error_reporting(E_ALL);
//$file = $_SERVER['DOCUMENT_ROOT']."/upload/import_csv/1456.csv";
global $USER;
$ERROR = '';

if( !$USER->IsAuthorized() || !$USER->IsAdmin() ){
	header('Location: /');
}

if( isset($_GET['getempty']) ){
	$arSiteInfo = CSite::GetByID(SITE_ID)->Fetch();
	header('Content-type: text/csv; charset=CP1251');
	header('Content-Disposition: attachment; filename="tovari-'.date("d-m-Y_H:i").'.csv"');
	echo iconv("UTF-8", "CP1251", "ОСНОВНОЙ;СОПУТКА;ID;ИМЯ;ЛИНК;\n");
	$arResult = getEmptyBWTI();
	foreach ($arResult as $value) {
		 $line = $value['EXTERNAL_ID'].';'.
			 ''.';'.
			 $value['ID'].';'.
			 $value['NAME'].';'.
			 $arSiteInfo['SERVER_NAME'].$value['DETAIL_PAGE_URL'].";\n";
		echo iconv("UTF-8", "CP1251", $line);
	}

	die();
}

if( isset($_FILES) && !empty($_FILES['csv-file']['name']) ){
	$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/upload/import_csv/';
	$uploadfile = $uploaddir . basename($_FILES['csv-file']['name'], ".csv").date('d-m-Y_H:i').'.csv';

	if ( !move_uploaded_file($_FILES['csv-file']['tmp_name'], $uploadfile)) {
	    $ERROR .= "Ошибка загрузки файла!\n<br>";
	}
} else{
	$ERROR .= "Не был указан для загрузки файл!\n<br>";
}

//$file = "upload/export/categories.xml";

//$xml = simplexml_load_file($file);

if( is_file($uploadfile) ){
	$arResult = parseCSVFile($uploadfile);
	if( empty($arResult) ){
		$ERROR .= "Не удалось получить данные из файла!\n<br>";
	} else {
		$arFirst  = getElementsInfo($arResult['FIRST']);
		$arSecond = getElementsInfo($arResult['SECOND']);
		$arResultId = arMergeFirstSecond($arResult, $arFirst, $arSecond);
		if( empty($arResult) ){
			$ERROR .= "Не удалось сопоставить элементы друг-другу!\n<br>";
		} else {
			if( !isset($_POST['update']) ){
				$ERROR .= "Не указан атрибут для обновления!\n<br>";
			} else {
				updateElement($arResultId);
			}
		}
	}
} else {
	$ERROR .= "В ходе выполнения возникли ошибки!\n<br>";
}


//echo'<pre>';print_r($arResultId, false);echo"</pre>";
//$arResult = getAllHandbooksUsedImport($arResult);
//$arResult = getInfoHandbooks($arResult);
//$arResult = createHandbooks($arResult);
//p($arResult);

function parseCSVFile($path){
	$row = 1;
	$arHeaders = $arResult = array();
	
	if (($handle = fopen($path, "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
	        $num = count($data);
	        if( empty($data[0]) )
	        	continue;

	        if( $row == 1 ){
	        	$row++;
	        	continue;
	        }

	        $arResult['ITEMS'][$data[0]][] = $data[1];
	        if( !in_array($data[0], $arResult['FIRST']) )
	        	$arResult['FIRST'][] = $data[0];

	        if( !in_array($data[1], $arResult['SECOND']) )
	        	$arResult['SECOND'][] = $data[1];

	        $row++;
	    }
	    fclose($handle);

	    return $arResult;
	} else {
	 $ERROR .= 'No find file';
	 return array();
	}
}

function getElementsInfo($arInput){
	$arSelect = array( 'ID', 'NAME', 'XML_ID' );
	$arFilter = array( 'IBLOCK'=>1, 'XML_ID'=> $arInput);
	$arResult = CAniartTools::_GetInfoElements(false, $arSelect, $arFilter);
	
	foreach ($arResult as $arItem) {
		$key = array_search($arItem['XML_ID'], $arInput);
		$arRes[$arItem['XML_ID']] = $arItem;
	}
	
	return $arRes;
	//p($arRes);
}

function arMergeFirstSecond($arResult, $arFirst, $arSec){
	$arRes = array();
	foreach ($arResult['ITEMS'] as $key => $arItems) {
		foreach ($arItems as $val) {
			if( isset($arFirst[$key]) && isset($arSec[$val]) )
				$arRes[$arFirst[$key]['ID']][] = $arSec[$val]['ID'];
		}
	}
	return $arRes;
}

function updateElement($arResultId){
	$IBLOCK_ID = 1;
	foreach ($arResultId as $itemId => $arItems) {
//		print_r($itemId);
		CIBlockElement::SetPropertyValues($itemId, $IBLOCK_ID, $arItems, 'BUY_WITH_T_ITEM');
	}
}

function getEmptyBWTI(){
	$arSelect = array( 'ID', 'NAME', 'XML_ID', 'DETAIL_PAGE_URL' );
	$arFilter = array( 'IBLOCK_ID'=>1, 'PROPERTY_BUY_WITH_T_ITEM'=> false, 'ACTIVE'=>'Y', 'INCLUDE_SUBSECTIONS'=>'Y');
	$arResult = CAniartTools::_GetInfoElements(false, $arSelect, $arFilter);
	
	return $arResult;
}

require($_SERVER["DOCUMENT_ROOT"]."/include/import_csv.tpl");