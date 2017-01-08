<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams['CACHE_TIME'])){
	$arParams['CACHE_TIME'] = 3600;
}

$arOrder = array(
	'SORT' => 'ASC'
);
	
$arFilter = array(
	'ACTIVE'	=> 'Y',
	'!DETAIL_PICTURE' => false,	
	'IBLOCK_ID'	=> IBLOCK_SLIDER_SPECIAL_OFFERS
);

$arSelect = array(
	'ID', 'DETAIL_PICTURE', 'PROPERTY_LINK',"NAME", "PREVIEW_PICTURE"
);

if($this->StartResultCache(false, array($USER->GetGroups()))){
	$rsSlides = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
	while($arSlide = $rsSlides->GetNext()){
	
		$Slide = array(
			'ID'				=> $arSlide['ID'],
			'DETAIL_PICTURE'			=> CFile::GetFileArray($arSlide['DETAIL_PICTURE']),
			'NAME'				=> $arSlide['NAME'],
			'LINK' 	=> $arSlide['PROPERTY_LINK_VALUE'],
			'PREVIEW_PICTURE' =>  CFile::GetFileArray($arSlide['PREVIEW_PICTURE'])
		);
		$arResult['ELEMENTS'][] = $Slide;
	}
	$this->SetResultCacheKeys(array(
		"ELEMENTS"
	));
	$this->IncludeComponentTemplate();
}
?>