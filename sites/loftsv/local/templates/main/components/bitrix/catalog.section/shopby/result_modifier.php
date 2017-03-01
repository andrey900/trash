<?
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arSections = array();
if($arResult['ITEMS']){
	foreach($arResult['ITEMS'] as $item){
		$arSections[$item['IBLOCK_SECTION_ID']] = $item['IBLOCK_SECTION_ID'];
	}
	$arFilter = array(
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"ID" => $arSections
	);
	$db_list = CIBlockSection::GetList(Array("ID"=>"ASC"), $arFilter);
	while($arItem = $db_list->GetNext())
	{
		$arSections[$arItem['ID']] = $arItem;
	}
	foreach($arResult['ITEMS'] as &$item){

		$item['IBLOCK_SECTION_NAME'] = $arSections[$item['IBLOCK_SECTION_ID']]['NAME'];
		$item['IBLOCK_SECTION_XML_ID'] = $arSections[$item['IBLOCK_SECTION_ID']]['XML_ID'];

		$item['CAN_BUY_U'] = ($item['CATALOG_QUANTITY'])?1:0;
		$item['ACTIVE'] = ($item['ACTIVE']=="Y")?1:0;
		$item['DETAIL_PICTURE_SRC'] = $item['DETAIL_PICTURE']['SRC'];
		$item['DETAIL_PAGE_URL'] = 'http://myshop.by'.$item['DETAIL_PAGE_URL'];

		$item['SEO_TITLE'] = $item['IPROPERTY_VALUES']['ELEMENT_META_TITLE'];
		$item['SEO_KEYWORDS'] = $item['IPROPERTY_VALUES']['ELEMENT_META_KEYWORDS'];
		$item['SEO_DESCRIPTION'] = $item['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'];
	}	
}
