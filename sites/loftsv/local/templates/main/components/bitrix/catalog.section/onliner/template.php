<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult['ITEMS'])
	return;

function makeTag($name, $value, $param="", $param_name = "name")
{
	if( $param )
		$param = ' '.$param_name.'="'.$param.'"';

	if(!preg_match("/^<(.*)>$/s", $value)){
		$value = preg_replace("/\p{Cc}+/u", " ", $value);
		return "\t\t\t<$name$param>".htmlspecialchars($value)."</$name>".PHP_EOL;
	}
	return "<$name$param>".PHP_EOL.$value."</$name>".PHP_EOL;
}

function createItem($arData)
{
	
	$arAssocFields = array(
		"url" => "DETAIL_PAGE_URL",
		"price" => 'PROPERTY_CATALOG_PRICE',
		"currencyId" => 'CATALOG_CURRENCY_1',
		"categoryId" => "IBLOCK_SECTION_ID",
		"picture" => "DETAIL_PICTURE_SRC",
		"name" => "NAME",
		"vendor" => "PROPERTY_BRAND",
		"param" => "PROPERTY_ARTICLE",
	);
	$arDefaultValue = array(
		"CATALOG_CURRENCY_1" => "BYN",
	);
	
	$arData = array_merge($arDefaultValue, $arData);
	
	$str = "\t\t<offer id=\"".$arData['ID']."\" available=\"true\">\r\n";
	foreach($arAssocFields as $field=>$dataItem){
		$param = "";
		if( strpos($dataItem, 'ROPERTY_CATALOG_PRICE') ){
			$prop = substr($dataItem, 9);
			$str .= makeTag($field, number_format($arData['PROPERTIES'][$prop]['VALUE'], 2, ".", ""));
			continue;
		}
		if( $field == "param" ){
			if( $dataItem == "PROPERTY_ARTICLE" ){
				$param = "Артикул";
			}
		}
		if( strpos($dataItem, 'ROPERTY_') ){
			$prop = substr($dataItem, 9);
			$str .= makeTag($field, $arData['PROPERTIES'][$prop]['VALUE'], $param);
		} else {
			$str .= makeTag($field, $arData[$dataItem], $param);
		}
	}
	$str .= "\t\t</offer>".PHP_EOL;

	return $str;
	// return makeTag('offer', $str);
}


if($_REQUEST['make']){
	$flag = 0;
	$str  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
	$str .= "\t<yml_catalog date=\"2016-02-05 17:22\">";
	$str .= "\t\t<shop>\r\n";
	$str .= "\t\t<name>LoftSvet</name>\r\n";
	$str .= "\t\t<company>LoftSvet.by</company>\r\n";
	$str .= "\t\t<url>https://loftsvet.by/</url>\r\n";
	$str .= "\t\t<currencies>\r\n";
	$str .= "\t\t\t<currency id=\"BYN\" rate=\"1\"/>\r\n";
	$str .= "\t\t</currencies>\r\n";
	$str .= "\t\t<categories>\r\n";
	foreach ($arResult['SECTIONS'] as $section) {
		$str .= makeTag("category", $section['NAME'], $section['ID'], "id");
	}
	$str .= "\t\t</categories>\r\n";
	$str .= "\t\t<offers>\r\n";
} else {
	$str = "";
	$flag = FILE_APPEND;
}

foreach($arResult['ITEMS'] as $arItem){
	$str .= createItem($arItem);
}

file_put_contents($_SERVER ["DOCUMENT_ROOT"]."/export/onliner.xml", $str, $flag);

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
	echo "<div style='color:red'>Не закрывайте вкладку - происходит формирование выгрузки на <b>onliner.by</b></div>";
	echo "<a href=\"$page\" style=\"display:none;\" id=\"pagen\">page</a>";
}else {
	$str = "\t</offers>\r\n</shop>\r\n</yml_catalog>";
	file_put_contents($_SERVER ["DOCUMENT_ROOT"]."/export/onliner.xml", $str, FILE_APPEND);
	echo "<div style='color:green'>Спасибо за ожидание. Файл был успешно сформирован по пути <a href='https://".$_SERVER['HTTP_HOST']."/export/onliner.xml'>/export/onliner.xml</a></div>";
	echo "<p><a href='https://".$_SERVER['HTTP_HOST']."/export/onliner.php'>вернутся обратно</a></p>";
}
