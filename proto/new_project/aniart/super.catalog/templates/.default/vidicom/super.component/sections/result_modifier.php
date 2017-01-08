<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// component text here

/*
if (!CModule::IncludeModule("iblock")) return false;

if (intval($arParams["IBLOCK_ID"]) <= 0)
{
	ShowMessage("Не указан инфоблок.");
	return;
}

$arFilter = array(
	"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
	"ACTIVE" => "Y",
	"ACTIVE_DATE" => "Y",
);
$arOrder = array(
	"SORT" => "ASC",
);
$arSelect = array(
	"ID", 
	"NAME", 
	"IBLOCK_ID",
	"PREVIEW_PICTURE",
	"PROPERTY_PARTNER_TYPE",
	"DETAIL_PAGE_URL",
);

$db_elements = CIblockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
$db_elements->SetUrlTemplates($arParams["DETAIL_URL"]);

while ($arElement = $db_elements->GetNext())
{
	$arElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
	$arResult["ITEMS"][] = $arElement;
}

$db_elements->LANG_ID = LANGUAGE_ID;
$arResult["NAV_STRING"] = $db_elements->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
$arResult["NAV_RESULT"] = $db_elements;

*/



// saving template name to cache array
$arResult["__TEMPLATE_FOLDER"] = $this->__folder;

// writing new $arResult to cache file
$this->__component->arResult = $arResult; 
?>