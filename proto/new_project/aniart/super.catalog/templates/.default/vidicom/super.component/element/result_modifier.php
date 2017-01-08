<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// component text here

// getting data
if (!CModule::IncludeModule("iblock")) 
	return false;

// getting element details
$arFilter = array(
	"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"CODE" => $arParams["ELEMENT_CODE"],
	"ID" => $arParams["ELEMENT_ID"],
	);
$arSelect = array(
	"ID",	
	"NAME",	
	"DETAIL_TEXT",	
	"DETAIL_PICTURE",	
	"DETAIL_PAGE_URL",
);


$db_el = CIblockElement::GetList(array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
if ($arElement = $db_el->GetNext(true, false))
{
	$arResult = $arElement;
}
else
{	
	$this->__component->AbortResultCache();
	ShowError("Элемент не найден.");
	@define("ERROR_404", "Y");
	CHTTP::SetStatus("404 Not Found");
}

// saving template name to cache array
$arResult["__TEMPLATE_FOLDER"] = $this->__folder;

// writing new $arResult to cache file
$this->__component->arResult = $arResult;
?>