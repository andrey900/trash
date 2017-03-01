<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

if( !$_SERVER['DOCUMENT_ROOT'] ){
	$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// некоторые начальные данные
// ===========================
$iblockId = 66;
$fileName = "dekomo.json";
$fileLog = "sync.log";
// ===========================

// получение аргументов из командной строки
$params = array();
foreach ($argv as $arg) {
	if( !strpos($arg, "=") ) continue;

	$_t = explode("=", $arg);
	$params[$_t[0]] = $_t[1];
}
if( !isset($params['cntInPage']) && isset($_REQUEST['cntInPage']) ) $params['cntInPage'] = $_REQUEST['cntInPage'];
if( !isset($params['page']) ) $params['page'] = 0;
if( isset($params['fileName']) ) $fileName = $params['fileName'];

// Прочитаем файл с данными
$f = file($fileName);

$arItems = array();
$arArticles = array();
// получим часть данных - для текущей страницы
for ($i=$params['page']*$params['cntInPage']; (isset($f[$i])) && $i < $params['cntInPage']*($params['page']+1); $i++) { 
	$item = json_decode($f[$i]);
	$arArticles[] = $item->article;
	$arItems[] = $item;
}

// поищем элементы в БД, что есть отложим для дальнейшей работы
$arFilter = Array("IBLOCK_ID"=>IntVal($iblockId), "XML_ID"=>$arArticles);
$res = CIBlockElement::GetList(Array(), $arFilter);
$arRes = array();
while($arFields = $res->GetNext()){ 
	$arRes[$arFields['XML_ID']] = $arFields['ID'];
}

// пройдемся по всем элементам синхронизации и или добавим или обновим существующий
foreach ($arItems as $item) {
	$arToDB = array();

	$el = new CIBlockElement();
	$arToDB = makeArrayOfObj($item);

	if( !$arToDB ) continue;

	if( isset($arRes[$item->article]) ){	
		$el->update($arRes[$item->article], $arToDB, false, false, false, false);
	} else {
		$el->add($arToDB, false, false, false);
	}
	
	if($el->LAST_ERROR)
		file_put_contents($fileLog, "Error: ".$el->LAST_ERROR.PHP_EOL.json_encode($arToDB).PHP_EOL, FILE_APPEND);
}

// формирование массива для обновления или добавления из обьекта
function makeArrayOfObj($obj){
	if( !$obj->name || !$obj->article ) return false;

	$arData = array();
	$arData['NAME'] = $obj->name;
	$arData['XML_ID'] = $obj->article;
	$arData['IBLOCK_ID'] = $GLOBALS['iblockId'];

	$article = $obj->article;
	if( ($n = strpos($obj->article, "_")) && $n <= 3 )
		$article = substr($obj->article, $n+1);

	$arProp = array(
		"BRAND" => $obj->brand,
		"PRICE" => $obj->price,
		"ARTICLE" => $article,
		"IN_STOCK" => $obj->inStock,
		"IN_REMOTE_STOCK" => $obj->remoteStock,
	);

	$arData['PROPERTY_VALUES'] = $arProp;

	return $arData;
}