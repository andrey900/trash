<?
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arBrands = [];

if( $_REQUEST['make'] ){
	$arFilter = array(
        "IBLOCK_ID" => IBLOCK_CATALOG_ID,
        "ACTIVE" => "Y",
        "GLOBAL_ACTIVE" => "Y",
    );
	$dbRes = \CIBlockSection::GetList(array(), $arFilter);
	$arResult['SECTIONS'] = [];
	while ($arRes = $dbRes->Fetch()) {
		$arResult['SECTIONS'][] = $arRes;
	}
}

if( $arResult['ITEMS'] ){
	foreach ($arResult['ITEMS'] as &$product) {
		$product['DETAIL_PICTURE_SRC'] = 'https://'.$_SERVER['SERVER_NAME'].$product['DETAIL_PICTURE']['SRC'];
		$brandId = $product['PROPERTIES']['BRAND']['VALUE'];
		if( !$arBrands[$brandId] ){
			$t = Studio8\Main\Helpers::GetInfoElements($brandId);
			$arBrands[$brandId] = array(
				"name" => $t[$brandId]['NAME'],
				"code" => $t[$brandId]['CODE']
			);
		}

		$product['PROPERTIES']['BRAND']['VALUE'] = $arBrands[$brandId]['name'];
		$product['DETAIL_PAGE_URL'] = "https://loftsvet.by".str_replace("#ELEMENT_BRAND#", $arBrands[$brandId]['name'], $product['DETAIL_PAGE_URL']);
	}
	unset($product);
}

