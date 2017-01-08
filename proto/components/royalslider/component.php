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
	'IBLOCK_ID'	=> IBLOCK_ROYAL_SLIDER_ID
);

$arSelect = array(
	'ID', 'IBLOCK_ID', 'DETAIL_PICTURE',
	'PROPERTY_PARENT_ENTITY',
	'PROPERTY_PARENT_ENTITY.IBLOCK_ID', 'PROPERTY_PARENT_ENTITY.NAME', 'PROPERTY_PARENT_ENTITY.PREVIEW_TEXT', 'PROPERTY_PARENT_ENTITY.DETAIL_PAGE_URL',
);

$arResult = array(
	'ELEMENT' => array()
);
if($this->StartResultCache(false, array($USER->GetGroups()))){
	$rsSlides = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
	while($arSlide = $rsSlides->GetNext()){
		$Slide = array(
			'ID'				=> $arSlide['ID'],
			'PICTURE'			=> CFile::GetFileArray($arSlide['DETAIL_PICTURE']),
			'ENTITY_ID'			=> $arSlide['PROPERTY_PARENT_ENTITY_VALUE'],
			'ENTITY_IBLOCK_ID'	=> $arSlide['PROPERTY_PARENT_ENTITY_IBLOCK_ID'],
			'NAME'				=> $arSlide['~PROPERTY_PARENT_ENTITY_NAME'],
			'TEXT'				=> $arSlide['~PROPERTY_PARENT_ENTITY_PREVIEW_TEXT'],
			'DETAIL_PAGE_URL' 	=> $arSlide['~PROPERTY_PARENT_ENTITY_DETAIL_PAGE_URL']
		);
		//Достаем цвета фона
		$ColorID = 0;
		$rsBackgroundColor = CIBlockElement::GetProperty($Slide['ENTITY_IBLOCK_ID'], $Slide['ENTITY_ID'], array(), array('CODE' => 'BACKGROUND_COLOR'));
		if($arBackgroundColor = $rsBackgroundColor->Fetch()){
			$ColorID = (int)$arBackgroundColor['VALUE'];
		}
		if($ColorID > 0){
			$Color = GetColorByID($ColorID);
			$Slide['BACKGROUND_COLOR'] = $Color['CODE'];
		}
		else{
			$Slide['BACKGROUND_COLOR'] = GetRandomColor(true);
		}
		
		//Определяем название сущности
		$EntityName = '';
		switch($Slide['ENTITY_IBLOCK_ID']){
			case IBLOCK_PRODUCT_ID	: $EntityName = 'товар'; break;
			case IBLOCK_ARTICLES_ID	: $EntityName = 'статья'; break;
			case IBLOCK_STORIES_ID	: $EntityName = 'история'; break;
		}
		$Slide['ENTITY_NAME'] = $EntityName;
		$arResult['ELEMENTS'][] = $Slide;
	}
	$this->SetResultCacheKeys(array(
		"ELEMENTS"
	));
	$this->IncludeComponentTemplate();
}
?>