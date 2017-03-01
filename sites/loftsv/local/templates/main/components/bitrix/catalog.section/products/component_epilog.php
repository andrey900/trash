<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION;

use Bitrix\Iblock\InheritedProperty\ElementValues;

// $APPLICATION->ghostItem - /_catalog/catalog.php

if($arParams["SET_TITLE"] && $APPLICATION->ghostItem)
{
	$_t = new ElementValues($APPLICATION->ghostItem['IBLOCK_ID'], $APPLICATION->ghostItem['ID']);
	$values = $_t->getValues();

	$title = $APPLICATION->ghostItem['NAME'];
	if( $values['ELEMENT_META_TITLE'] )
		$title = $values['ELEMENT_META_TITLE'];
	$APPLICATION->SetTitle($title);
}