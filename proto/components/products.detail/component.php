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
	$rsElement = CIBlockElement::GetList(array(), $arFilter);
	if($obElement = $rsElement->GetNextElement()){
		$arElement = $obElement->GetFields();
		$arElement['PROPERTIES']		= $obElement->GetProperties();
		
		if(!empty($arElement['PREVIEW_PICTURE'])){
			$arElement['PREVIEW_PICTURE'] = CFile::GetFileArray($arElement['PREVIEW_PICTURE']);
		}
		if(!empty($arElement['DETAIL_PICTURE'])){
			$arElement['DETAIL_PICTURE'] = CFile::GetFileArray($arElement['DETAIL_PICTURE']);
		}
		
		//Находим отображаемые значения для свойств
		$arElement["DISPLAY_PROPERTIES"] = array();
		foreach($arElement['PROPERTIES'] as &$prop)
		{
			if((is_array($prop["VALUE"]) && count($prop["VALUE"])>0) ||
			(!is_array($prop["VALUE"]) && strlen($prop["VALUE"])>0))
			{
				$DisplayValues = CIBlockFormatProperties::GetDisplayValue($arElement, $prop, "catalog_out");
				$prop['DISPLAY_VALUE'] = $DisplayValues['DISPLAY_VALUE'];
			}
		}
		unset($prop);
		
		//Находим секции к которой принадлежит товар
		if(!empty($arElement['IBLOCK_SECTION_ID'])){
			$rsSection = CIBlockSection::GetList(array(), array('IBLOCK_ID' => IBLOCK_PRODUCT_ID, 'ID' => $arElement['IBLOCK_SECTION_ID']), false, array('UF_*'));
			if($arSection = $rsSection->GetNext()){
				$arResult['SECTION'] = $arSection;
			}
		}
		//Вытягиваем торговые предложения для этого товара
		$arOffers = CIBlockPriceTools::GetOffersArray(
			IBLOCK_PRODUCT_ID,
			$arElement['ID'],
			array('SORT' => 'ASC', 'NAME' => 'DESC'),
			array('ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'),
			array(),
			null,
			array()
		);
		if(!empty($arOffers)){
			foreach($arOffers as $OfferIndex => $arOffer){
				if($arOffer['ACTIVE'] != 'Y'){
					continue;
				}
				if(!empty($arOffer['PREVIEW_PICTURE'])){
					$arOffer['PREVIEW_PICTURE'] = CFile::GetFileArray($arOffer['PREVIEW_PICTURE']);
				}
				if(!empty($arOffer['DETAIL_PICTURE'])){
					$arOffer['DETAIL_PICTURE'] = CFile::GetFileArray($arOffer['DETAIL_PICTURE']);
				}
				$arElement['OFFERS'][] = $arOffer;
			}
		}
	}
	$arResult['ELEMENT'] = $arElement;
	
	$this->SetResultCacheKeys(array(
		'ELEMENT',
		'SECTION',
	));
	
	$this->IncludeComponentTemplate();
}

if(!empty($arResult['ELEMENT']['ID'])){
	//Увеличиваем счетчик "просматриваемости"
	$APPLICATION->SetTitle(SITE_NAME.' - '.$arResult['ELEMENT']['~NAME']);
	CIBlockElement::CounterInc($arResult['ELEMENT']['ID']);
}
?>