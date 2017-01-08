<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams['IBLOCK_ID']		= (int)$arParams['IBLOCK_ID'];
if($arParams['IBLOCK_ID'] <= 0){
	ShowError('Не задан идентификтор товара');
	return;
}

if ( (int)$arParams["CACHE_TIME"] < 0)
	$arParams["CACHE_TIME"] = 3600;

$arFilter = array(
	'ACTIVE'	=> 'Y',
	'IBLOCK_ID'	=> $arParams['IBLOCK_ID'],
	"ACTIVE_DATE"=>"Y"
);

$arSection = array();

if( $this->StartResultCache(false, array($USER->GetGroups(), $arFilter)) ){

	$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "IBLOCK_SECTION_ID", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "SORT");

	if( !empty($arParams['PROPERTY']) ){
	foreach ($arParams['PROPERTY'] as $prop) {
		$arSelect[] = 'PROPERTY_'.$prop;
	}
	}

	$res = CIBlockElement::GetList(Array("SORT"=>"ASC", "ID"=>"ASC"), $arFilter, false, false, $arSelect);

	while($arItem = $res->GetNext()){
		$arResult['ELEMENTS'][$arItem['ID']] = $arItem;
		if( (int)$arItem['IBLOCK_SECTION_ID'] > 0 && 
			!in_array($arItem['IBLOCK_SECTION_ID'], $arSection) ){
			//$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']] = CIBlockSection::GetByID($arItem['IBLOCK_SECTION_ID'])->GetNext();
			$arSection[] = $arItem['IBLOCK_SECTION_ID'];
		}
	}

	$arSelect = Array("ID", "NAME", "IBLOCK_ID", "CODE", "SORT");
	$arFilter = array('ID'=>$arSection);

	$res = CIBlockSection::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter, false, $arSelect);

	while($arItem = $res->GetNext()){
		$arResult['SECTIONS'][$arItem['ID']] = $arItem;
	}

	unset($arItem);

	$this->SetResultCacheKeys(array(
		'ELEMENTS',
		'SECTIONS',
	));
	
	$this->IncludeComponentTemplate();
}
?>