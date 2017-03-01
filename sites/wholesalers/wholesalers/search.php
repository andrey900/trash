<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/WholesalerProduct.php");
CModule::IncludeModule('highloadblock');

if( !$GLOBALS['USER']->IsAuthorized() ){
	die;
}

if( !$_GET['q'] )
	die( json_encode(array()) );

$arBrands = CElectrodomTools::GetGroupDecomoBrands(66, 1091);
$arLBrands = array();
foreach ($arBrands as $item) {
	$arLBrands[$item] = strtolower($item);
}

$strSearch = $_GET['q'];
$search = strtolower($strSearch);

$arBrandNames = array();
foreach ($arBrands as $e) {
	if( preg_match("/".$e."/ui", $strSearch) ){
		$arBrandNames[] = $e;
		$search = preg_replace("/".$e."/i", "", $search);
		$search = trim($search);
	}
}

$arSelect = Array("*", "PROPERTY_*");
$arFilter = Array("IBLOCK_ID" => 66, "NAME" => "%".$search."%", ">PROPERTY_PRICE" => 0);

if( $arBrandNames ){
	$arFilter['PROPERTY_1091'] = $arBrandNames;
}

$arNavParams = array(
    "nPageSize" => '15',
    "iNumPage" => $_REQUEST['p']?(int)$_REQUEST['p']:1,
    "bShowAll" => 'N',
);
$count = CIBlockElement::GetList(Array(), $arFilter, []);

if( $count == 0 ){
	unset($arFilter['NAME']);
	$arFilter['PROPERTY_ARTICLE'] = "%$search%";
}

$res = CIBlockElement::GetList(Array(), $arFilter, false, $arNavParams, $arSelect);
$strNav = $res->GetPageNavStringEx($navComponentObject, 'Страницы', 'ajax', 'Y');
$arRes = array("items"=>[], "pagen"=>$strNav);
while($ob = $res->Fetch())
{
	$product = new WholesalerProduct($ob['ID']);
	$product->productInBasket(1, $_SESSION['BASKET']['currency']);
	
	$arRes['items'][] = $product->getProductForBasket();
}

header('Content-Type: application/json');

die( json_encode($arRes) );