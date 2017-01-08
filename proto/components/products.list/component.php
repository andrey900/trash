<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams['ELEMENTS_COUNT'] = (int)$arParams['ELEMENTS_COUNT'];
if($arParams['ELEMENTS_COUNT'] <= 0){
	$arParams['ELEMENTS_COUNT'] = 10;
}
$arParams['GET_OFFERS'] = $arParams['GET_OFFERS'] == 'Y';
$arParams['SHOW_PAGINATION'] = $arParams['SHOW_PAGINATION'] == 'Y';
$arParams["NAV_TEMPLATE"] = trim($arParams['NAV_TEMPLATE']);
if(empty($arParams['NAV_TEMPLATE'])){
	$arParams['NAV_TEMPLATE'] = '.default';
}
$arParams['NAV_PAGE_VAR'] = trim($arParams['NAV_PAGE_VAR']);
if(empty($arParams['NAV_PAGE_VAR'])){
	$arParams['NAV_PAGE_VAR'] = 'page';
}

$arSort = array(
	'ID'	=> 'ASC'
);
$arrSort = $GLOBALS[$arParams['SORT_NAME']];
if(is_array($arrSort) && !empty($arrSort)){
	$arSort = array_merge($arrSort, $arSort);
}
$arFilter = array(
	'ACTIVE'	=> 'Y',
	'IBLOCK_ID'	=> IBLOCK_PRODUCT_ID
);
$arrFilter =  $GLOBALS[$arParams['FILTER_NAME']];
if(is_array($arrFilter) && !empty($arrFilter)){
	$arFilter = array_merge($arFilter, $arrFilter);
}

$arSelect = array(
	'ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL',
	'PREVIEW_TEXT', 'PREVIEW_PICTURE',
);
$arrSelect =  $GLOBALS[$arParams['SELECT_NAME']];
if(is_array($arrSelect) && !empty($arrSelect)){
	$arSelect = array_merge($arSelect, $arrSelect);
}

CPageOption::SetOptionString("main", "nav_page_in_session", "N"); //запрещаем хранить номер страницы в сессии
$arNavParams = array(
	'iNumPage'	=> (int)$_REQUEST[$arParams['NAV_PAGE_VAR']],
	'bShowAll'	=> false,
	'nPageSize' => $arParams['ELEMENTS_COUNT']
);
$arNavigation = CDBResult::GetNavParams($arNavParams);

if($this->StartResultCache(false, array($USER->GetGroups(), $arFilter, $arSort, $arrSelect, $arNavigation))){
	//Получаем товары
	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
	while($arElement = $rsElements->GetNext()){
		if(!empty($arElement['PREVIEW_PICTURE'])){
			$arElement['PREVIEW_PICTURE'] = CFile::GetFileArray($arElement['PREVIEW_PICTURE']);
		}
		$arResult['ELEMENTS'][] = $arElement;
		$arElementsID[] = $arElement['ID'];
	}

	//Получаем торговые предложения для каждого товара
	if(!empty($arResult['ELEMENTS']) && $arParams['GET_OFFERS']){
		$OffersSort = array(
			'SORT'	=> 'ASC',
			'NAME'	=> 'ASC'
		);
		//Очень, очень тяжелая штука: тысячи запросов, секунды отработки, тем не менее битрикс рекомендует использовать именно
		//эту функцию - возвращает торговые предложения для элемнтов вместе с ценами, учитывающими скидки, уровни доступа и т.д.
		$arOffers = CIBlockPriceTools::GetOffersArray(
			IBLOCK_PRODUCT_ID,
			$arElementsID,
			$OffersSort,
			array('ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'LINK_ELEMENT_ID'),
			array('COLOR', 'SIZE', 'MODELS'),
			null,
			array()
		);

		if(!empty($arOffers)){
			$arElementOffer		= array();
			$arElementProperty	= array();
			foreach($arElementsID as $i => $id)
			{
				$arResult["ELEMENTS"][$i]["OFFERS"] = array();
				$arElementOffer[$id]	= &$arResult['ELEMENTS'][$i]['OFFERS'];
			}

			foreach($arOffers as $OfferIndex => $arOffer){
				if($arOffer['ACTIVE'] != 'Y'){
					continue;
				}
				$arElementOffer[$arOffer['LINK_ELEMENT_ID']][] = $arOffer;
			}
		}
	}
		
	//Работа с постраничкой
	$arResult['NAV_STRING'] = '';
	$arResult['PAGER_PARAMS'] = array();
	if($arParams['SHOW_PAGINATION']){
		$rsElements->nPageWindow = 3;
		ob_start();
		$navComponentObject = $APPLICATION->IncludeComponent(
			"bitrix:system.pagenavigation",
			$arParams["NAV_TEMPLATE"],
			array(
				"NAV_TITLE" 	=> $navigationTitle,
				"NAV_RESULT"	=> $rsElements,
				"SHOW_ALWAYS"	=> false,
				"NAV_PAGE_VAR"	=> $arParams['NAV_PAGE_VAR']
			),
			false,
			array(
				"HIDE_ICONS" => "Y"
			)
		);
		$arResult['NAV_STRING'] = ob_get_contents();
		ob_end_clean();
		foreach($rsElements as $Key => $Value){
			if(!is_array($Value) && !is_object($Value)){
				$arResult['PAGER_PARAMS'][$Key] = $Value;
			}
		}
	}
	$this->SetResultCacheKeys(array(
		'NAV_STRING',
		'PAGER_PARAMS'
	));
	$this->IncludeComponentTemplate();
}
?>