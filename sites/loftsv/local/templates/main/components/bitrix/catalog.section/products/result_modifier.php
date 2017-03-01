<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Studio8\Main\Product;

if( !$arResult['ITEMS'] ) return;

$arProducts = [];
foreach ($arResult['ITEMS'] as $arItem) {
	// p($arItem['DETAIL_PICTURE']);
	$arProducts[$arItem['ID']] = new Product($arItem);
}

$arResult['ITEMS'] = $arProducts;