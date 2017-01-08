<? define("NO_KEEP_STATISTIC", true); // Отключение сбора статистики для AJAX-запросов
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/include/sphinxSearch.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/classes/CAniartTools.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/classes/CAniartMorfology.php");

$searchphrase = CAniartTools::full_trim($_REQUEST["value"]);
$morfology = new CAniartMorfology();
$searchphrase = $morfology->stem_word($searchphrase);

$ar_elem_id = SphinxSearch($searchphrase);

if (count($ar_elem_id) == 0)
{
	$arSP = explode(' ', $searchphrase);
	$Sphrases = array();
	foreach ($arSP as $phrase)
	{
		$Sphrases[] = $morfology->stem_word($phrase);
	}

	unset($searchphrase);
	unset($result);
	
	$result = array("m_total_found" => 0);
	
	$ar_elem_id = array();
	$ar_iblock_id = array();

	foreach ($Sphrases as $searchphrase)
	{
		$ar_elem_idst = SphinxSearch($searchphrase, 10);
		foreach ($ar_elem_idst as $key => $value)
		{
			$ar_elem_id[] = $value;
		}
	}//endforeach
}

if (!empty($ar_elem_id) == 0)
{
	$json_out = array(
		"query" => $_GET['query'],
		"suggestions" => array("совпадений не найдено"),
		"data" => array('совпадений не найдено')
	);
	
	echo json_encode($json_out);
	die();
}

$arSelect = Array(
	"ID", 
	"NAME", 
	"PROPERTY_PICTURE", 
	"DETAIL_PAGE_URL"
);
$arFilter = Array(
	"IBLOCK_ID" => CATALOG_IBLOCK_ID, 
	"ACTIVE" => "Y", 
	"ID" => $ar_elem_id
);

$ar_results = array();

$list = CIBlockElement::GetList(array("SORT" => "DESC"), $arFilter, false, Array("nPageSize" => MAX_COUNT_SEARCH), $arSelect);
$list->NavStart(MAX_COUNT_SEARCH);
while($item = $list->GetNext())
{
	if (!empty($item["PROPERTY_PICTURE_VALUE"]))
	{
		$item["DETAIL_PICTURE"] = CFile::ResizeImageGet($item['PROPERTY_PICTURE_VALUE'][0], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	}
	
	$arResult[] = array(
		"value" => $item['NAME'],
		"image" => $item['DETAIL_PICTURE'],
		"url" => $item['DETAIL_PAGE_URL']
	);
}

$json_out = array(
	"query" => $_REQUEST['value'],
	"data"  => $arResult,
);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($json_out);
die();
