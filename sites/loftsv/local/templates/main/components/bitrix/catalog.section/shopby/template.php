<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult['ITEMS'])
	return;

function makeLine($value, $endLine = false)
{
	if( $endLine==false ){
		$value = preg_replace("/\p{Cc}+/u", " ", $value);
		$value = preg_replace("/[;]+/u", " ", $value);
		return $value.";";
	}

	if( $endLine==true )
		return substr($value, 0, -1).PHP_EOL;
}

function createItem($arData, $firstLine = false)
{
	$arAssocFields = array(
		"headline" => "NAME",
		"name" => "NAME",
		"item_code" => "CODE",
		"image" => "DETAIL_PICTURE_SRC",
		"price" => "CATALOG_PRICE_1",
		"abstract" => "PREVIEW_TEXT",
		"info" => "DETAIL_TEXT",
		"status" => "CAN_BUY_U",
		"add_link2" => "",
		"url" => "DETAIL_PAGE_URL",
		"catalog_id" => "IBLOCK_SECTION_XML_ID",
		"seo.title" => "SEO_TITLE",
		"seo.keywords" => "SEO_KEYWORDS",
		"seo.description" => "SEO_DESCRIPTION"
	);
	$arDefaultValue = array(
		"SEO_DESCRIPTION" => "",
		"SEO_KEYWORDS" => "",
		"SEO_TITLE" => "",
		"ACTIVE" => "1",
	);
	
	if( $firstLine ){
		$str = '';
		foreach($arAssocFields as $k=>$v){
			$str .= makeLine($k);
		}
		return makeLine($str, 1);
	}
	
	$arData = array_merge($arDefaultValue, $arData);
	
	$str = '';
	foreach($arAssocFields as $field=>$dataItem){
		if( strpos($dataItem, 'ROPERTY_') ){
			$prop = substr($dataItem, 9);
			$str .= makeLine($arData['PROPERTIES'][$prop]['VALUE']);
		} else {
			$str .= makeLine($arData[$dataItem]);
		}
	}

	return makeLine($str, 1);
}


if($_REQUEST['make']){
	$flag = 0;
	$str  = "";
/*
	$str  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
	$str .= "<price-list version=\"1.0\">\r\n";
	$str .= "<items-list>\r\n";
*/
} else {
	$str = "";
	$flag = FILE_APPEND;
}

if($flag===0){
	$str .= createItem(array(), 1);
}

foreach($arResult['ITEMS'] as $arItem){
	$str .= createItem($arItem);
}

file_put_contents($_SERVER ["DOCUMENT_ROOT"]."/imports/shopby.csv", $str, $flag);

if(($cnt = $arResult['NAV_RESULT']->NavPageCount) > 0){
	$pagen = "PAGEN_".$arResult['NAV_RESULT']->NavNum;

	$pageNext = $arResult['NAV_RESULT']->NavPageNomer + 1;

	if( $_REQUEST[$pagen] && $_REQUEST[$pagen] < $cnt ){
		$page = $APPLICATION->GetCurPageParam("$pagen=$pageNext", array($pagen, "make")); 
	}elseif(!isset($_REQUEST[$pagen]) && $cnt > 1)
		$page = $APPLICATION->GetCurPageParam("$pagen=$pageNext", array($pagen, "make")); 
	else
		$page = false;
}

$percent = (int)$arResult['NAV_RESULT']->NavPageNomer / (int)$cnt * 100;

if( $page ){
	echo "Выполнено: ".round($percent, 2)."%";
	echo "<div style='color:red'>Не закрывайте вкладку - происходит формирование выгрузки на <b>shop.by</b></div>";
	echo "<a href=\"$page\" style=\"display:none;\" id=\"pagen\">page</a>";
}else {
	//$str = "</items-list>\r\n</price-list>";
	//file_put_contents($_SERVER ["DOCUMENT_ROOT"]."/imports/onliner.xml", $str, FILE_APPEND);
	echo "<div style='color:green'>Спасибо за ожидание. Файл был успешно сформирован по пути <a href='http://".$_SERVER['HTTP_HOST']."/imports/shopby.csv'>/imports/shopby.csv</a></div>";
	echo "<p><a href='http://".$_SERVER['HTTP_HOST']."/imports/shopby.php'>вернутся обратно</a></p>";
}
