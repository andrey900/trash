<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams['ELEMENT_ID']		= (int)$arParams['ELEMENT_ID'];
if($arParams['ELEMENT_ID'] <= 0 && empty($arParams['ELEMENT_CODE'])){
	ShowError('Не задан идентификтор товара');
	return;
}

$arFilter = array(
	'ACTIVE'	=> 'Y',
	'IBLOCK_ID'	=> IBLOCK_PRODUCT_ID,
);

if(!empty($arParams['ELEMENT_ID'])){
	$arFilter = $arParams['ELEMENT_ID'];
}
if(!empty($arParams['ELEMENT_CODE'])){
	$arFilter['CODE'] = $arParams['ELEMENT_CODE'];
}
if(!empty($arParams['SECTION_CODE'])){
	$arFilter['SECTION_CODE'] = $arParams['SECTION_CODE'];
}

if($this->StartResultCache(false, array($USER->GetGroups(), $arFilter))){
	
	$this->SetResultCacheKeys(array(
		'ELEMENT',
		'SECTION',
	));
	
	$this->IncludeComponentTemplate();
}

?>