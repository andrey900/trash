<?php

use Bitrix\Main\Loader;

/**
* 
*/
class GetDataFactory
{
	protected static $IBLOCK_ID = 39;
	protected static $HB_IBLOCK_ID = 65;

	public static function getBrands(){
		$arFilter = array('IBLOCK_ID' => self::$IBLOCK_ID, 'ACTIVE' => 'Y', "DEPTH_LEVEL" => 1, "UF_NO_PODBOR"=>false, "!ID" => array(747, 878, 1258, 731)); 
		$rsSection = CIBlockSection::GetList(Array('NAME'=>"ASC"), $arFilter, false);
		$arSections = array(); 
		while($arSection = $rsSection->Fetch()) {
		   $arSections[$arSection['ID']] = $arSection;
		}

		return $arSections;
	}

	public static function getCollectionsByBrand($intSection){
		$intSection = (int)$intSection;
		$depthLevel = 2;
		if($intSection == 846){
			$intSection = 849;
			$depthLevel = 3;
		}
		$arFilter = array("IBLOCK_ID"=>self::$IBLOCK_ID, "ACTIVE"=>"Y", "SECTION_ID"=>$intSection, "DEPTH_LEVEL"=>$depthLevel, "UF_NO_PODBOR"=>false);
		$dbRes = CIBlockSection::GetList(Array("NAME"=>"ASC"), $arFilter, false);
		$arRes = array();
		while ($arItem = $dbRes->GetNext()) {
			$arRes[] = self::makeObject($arItem);
		}
		return $arRes;
	}

	public static function getSectionById($intSection){
		$intSection = (int)$intSection;
		return self::makeObject(CIBlockSection::GetByID($intSection)->GetNext());
	}

	public static function getSectionByCode($code){
		$arFilter = array("IBLOCK_ID"=>self::$IBLOCK_ID, "ACTIVE"=>"Y", "CODE"=>$code, "DEPTH_LEVEL"=>1);
		
		return self::makeObject(CIBlockSection::GetList(Array("NAME"=>"ASC"), $arFilter)->GetNext());
	}

	public static function getFramesOrPushItems($id, $type){
		$arFilter = array("IBLOCK_ID"=>self::$HB_IBLOCK_ID, "ACTIVE"=>"Y", "PROPERTY_COLLECTION"=>$id, "SECTION_CODE"=>$type);
		
		$arRes = array();

		$dbRes = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter);
		while ($arItem = $dbRes->GetNext()) {
			$arItem['PREVIEW_PICTURE'] = CFile::GetPath($arItem['PREVIEW_PICTURE']);
			$arRes[] = self::makeObject($arItem, array("PREVIEW_PICTURE"));
		}

		return $arRes;
	}

	public static function getArticlesItems($id, $vendor){
		$arFilter = array("IBLOCK_ID"=>self::$HB_IBLOCK_ID, "ACTIVE"=>"Y", "ID"=>$id);
		// p([$arItem['PROPERTY_ARTICLES_VALUE'], $vendor]);
		$dbRes = CIBlockElement::GetList(Array("NAME"=>"ASC"), $arFilter, false, false, array("ID", "PROPERTY_ARTICLES"));
		if ($arItem = $dbRes->GetNext()) {
			if( $arItem['PROPERTY_ARTICLES_VALUE'] ){
				$arRes = CElectrodomTools::GetInfoElements(false, array("ID", "NAME", "DETAIL_PAGE_URL", "CODE", "DETAIL_PICTURE", "CATALOG_GROUP_1"), array("IBLOCK_ID"=>self::$IBLOCK_ID, "PROPERTY_ARTICLE"=>$arItem['PROPERTY_ARTICLES_VALUE'], "PROPERTY_PRODUCER_VALUE"=>$vendor));
			}
		}

		if(Loader::includeModule('currency') && $arRes){
			foreach ($arRes as &$item) {
				$item['CATALOG_PRICE_1'] = CCurrencyRates::ConvertCurrency($item['CATALOG_PRICE_1'], $item['CATALOG_CURRENCY_1'], "BYN");
				$item['CATALOG_CURRENCY_1'] = "BYN";
			}
			unset($item);
		}

		return $arRes;
	}

	protected static function makeObject(array $arData, array $arFields = array()){
		$obj = new \stdClass();
		$arFields = array_merge(array("ID", "NAME", "CODE"), $arFields);
		foreach ($arFields as $v) {
			$n = strtolower($v);
			$obj->$n = $arData[$v];
		}

		return $obj;
	}
}

/**
* 
*/
class SeoFactory
{
	protected static $arTemplatesSEO = array(
		"default" => array(
			"title" => "Подбор коллекции",
			"description" => "Описание для подбора коллекции"
		),
		"brand" => array(
			"title" => "Подбор по бренду - #brandName#",
			"description" => "Описание для подбора коллекции по #brandName#"
		),
		"collection" => array(
			"title" => "Подбор #brandName# по коллекции - #collectionName#",
			"description" => "Описание для подбора коллекции по #collectionName#"
		),
		"colorFrame" => array(
			"title" => "Подбор по цвету кнопки(#colorPushName#) и рамки(#colorFrameName#)",
			"description" => "Подбор по цвету кнопки(#colorPushName#) и рамки(#colorFrameName#)"
		),
		"colorPush" => array(
			"title" => "Подбор по цвету кнопки(#colorPushName#) и рамки(#colorFrameName#)",
			"description" => "Подбор по цвету кнопки(#colorPushName#) и рамки(#colorFrameName#)"
		),
	);

	protected static $replacment = array("#brandName#", "#collectionName#", "#colorFrameName#", "#colorPushName#");

	public static function getTemplates(){
		return self::$arTemplatesSEO;
	}

	public static function getTitleByType($type, array $replace = array()){
		return str_replace(self::$replacment, $replace, self::$arTemplatesSEO[$type]['title']);
	}

	public static function getDescriptionByType($type, array $replace = array()){
		return str_replace(self::$replacment, $replace, self::$arTemplatesSEO[$type]['description']);
	}
}