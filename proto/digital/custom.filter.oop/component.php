<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
//подключаем модель фильтра и доп. функции
include_once "include.php";

$arParams["IBLOCK_ID"]		= (int)$arParams["IBLOCK_ID"];
$arParams["OFFERS_EXIST"]	= $arParams["OFFERS_EXIST"] == "Y";
$arParams["MAIN_IBLOCK"]	= $arParams["MAIN_IBLOCK"]?$arParams["MAIN_IBLOCK"]:$arParams["IBLOCK_ID"];
$arParams["FILTER_NAME"]	= $arParams["FILTER_NAME"]?$arParams["FILTER_NAME"]:"filter";

if(!CModule::IncludeModule("iblock")){
	ShowError("Модуль инфоблоков не установлен");die;
}


if($arParams["OFFERS_EXIST"] === true && !CModule::IncludeModule("catalog")){
	ShowError("Модуль торгового каталога не установлен");die;
}
elseif($arParams["OFFERS_EXIST"] === true)
{
	$rsCatalog = CCatalog::GetList(array(), array("PRODUCT_IBLOCK_ID" => $arParams["IBLOCK_ID"]));
	if($arCatalog = $rsCatalog->Fetch())
	{
		$arParams["OFFERS_IBLOCK_ID"]	= $arCatalog["IBLOCK_ID"];
		$arParams["OFFERS_PROPERTY_ID"]	= $arCatalog["SKU_PROPERTY_ID"];
	}
}

//Создаем экземпляр фильтра и добавляем в него фильтруемые свойства
$Filter = CustomFilters::Add($arParams["FILTER_NAME"], array(
	"MAIN_IBLOCK"			=> $arParams["MAIN_IBLOCK"],
	"PRODUCTS_IBLOCK_ID"	=> $arParams['IBLOCK_ID'],
	"OFFERS_IBLOCK_ID"		=> $arParams["OFFERS_IBLOCK_ID"],
	"OFFERS_PROPERTY_ID"	=> $arParams["OFFERS_PROPERTY_ID"],
	"SECTION_ID"			=> $arParams["SECTION_ID"],
	"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
	"MORE_PROPERTY"			=> $arParams["MORE_PROPERTY"],
));

$Filter->AddProperties($arParams["IBLOCK_PROPERTIES"])
	   ->AddProperties($arParams["OFFERS_PROPERTIES"], "offer")
	   ->AddProperties($arParams["VIRTUAL_PROPERTIES"], "virtual")
	   ->ObtainPropertiesData();

//Парсим параметры компонента и добавляем пользовательские параметры к фильтруемым свойствам
foreach($arParams as $key => $value)
{
	if(is_string($value)){
		$value		= trim($value);
	}
	$subkeys	= explode("_", $key);
	
	if($subkeys[0] == "PROPERTY")
	{
		$propertyID = is_numeric($subkeys[1])?$subkeys[1]:$subkeys[2];
		$paramName	= end($subkeys);
		if(!empty($propertyID)){
			$Filter->GetProperty($propertyID)->SetParam($paramName, $value);
		}


	}
}

//Сортируем свойства
$Filter->SortProperties();

//Включаем обработку свойств( логика + буферизация HTML)
$Filter->Build();
$arResult["FILTER"] = $Filter;

$this->IncludeComponentTemplate();

return $Filter->ObtainFullBitrixFilter();
//return $Filter->GetSelectedValues()->CreateBitrixFilter();
?>
