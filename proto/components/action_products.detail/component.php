<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams['IBLOCK_ID']		= (int)$arParams['IBLOCK_ID'];
if($arParams['IBLOCK_ID'] <= 0 || empty($arParams['CODE']) ){
	ShowError('Не заданы обязательные параметры');
	return;
}

if ( (int)$arParams["CACHE_TIME"] < 0)
	$arParams["CACHE_TIME"] = 3600;

$arFilter = array(
	'ACTIVE'	=> 'Y',
	'IBLOCK_ID'	=> $arParams['IBLOCK_ID'],
	"ACTIVE_DATE"=>"Y",
	"ACTIVE"	=> "Y", 
	"CODE"		=> $arParams['CODE']
);

$arSection = array();

if( $this->StartResultCache(false, array($USER->GetGroups(), $arFilter)) ){

	$arSelect = Array("ID", "NAME", "CODE", "DATE_CREATE", "CREATED_DATE", "DATE_ACTIVE_FROM", "ACTIVE", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE", "PREVIEW_PICTURE", "DETAIL_PICTURE", "DETAIL_TEXT", "DETAIL_TEXT_TYPE", "DETAIL_PAGE_URL", "IBLOCK_ID", "IBLOCK_NAME", "IBLOCK_TYPE_ID", "IBLOCK_SECTION_ID", "LIST_PAGE_URL");
	if( isset($arParams['PROPERTY_CODE']) && is_array($arParams['PROPERTY_CODE']) ){
		foreach ($arParams['PROPERTY_CODE'] as $prop_code) {
			$arSelect[] = 'PROPERTY_'.$prop_code;
		}
	}
	//$arSelect = array();
	//$arFilter = Array("IBLOCK_ID"=>$arParams['IBLOCK_ID'], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "CODE"=>$arParams['CODE']);
	
	$res = CIBlockElement::GetList(Array("SORT"=>"ASC", "ID"=>"ASC"), $arFilter, false, false, $arSelect);//->GetNext();
	
	if($obEl = $res->GetNextElement()){
	    $arResult = $obEl->GetFields();
	    $arResult['PROPERTIES'] = $obEl->GetProperties();
	}

	foreach ($arResult['PROPERTIES'] as $arProp) {
		if( $arProp['PROPERTY_TYPE'] == 'E' && 
			$arProp['MULTIPLE'] && 
			$arProp['LIST_TYPE'] &&
			!empty($arProp['VALUE']) ){
			foreach ($arProp['VALUE'] as $value) {
				$arResult['ITEMS'][$value] = CIBlockExt::GetElementInfo( $value );
				$arResult['ITEMS'][$value]['DETAIL_PICTURE'] = CFile::GetPath($arResult['ITEMS'][$value]['DETAIL_PICTURE']);
				$arResult['ITEMS'][$value]['PREVIEW_PICTURE'] = CFile::GetPath($arResult['ITEMS'][$value]['PREVIEW_PICTURE']);
				$arResult['ITEMS'][$value]['DETAIL_PAGE_URL'] = CUrlExt::GetProductURL($value);
				$arResult['ITEMS'][$value]['PRICE'] = GetCatalogProductPrice($value, 5);
				$arResult['ITEMS'][$value]['ACTION_PRICE'] = GetCatalogProductPrice($value, 6);
			}
		}
	}

	$this->SetResultCacheKeys();
	
	$this->IncludeComponentTemplate();
} 
if( !empty($arResult) )
	$APPLICATION->SetTitle($arResult['NAME']);
?>