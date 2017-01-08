<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// component text here
if (!CModule::IncludeModule("iblock")) 
	return false;

if (intval($arParams["IBLOCK_ID"]) <= 0)
{
	ShowMessage("Не указан инфоблок.");
	return;
}

$arFilter = array(
	"IBLOCK_ID" => intval($arParams["IBLOCK_ID"]),
	"ACTIVE" => "Y",
	"GLOBAL_ACTIVE" => 'Y',
	'CNT_ACTIVE' => 'Y',
	'<=DEPTH_LEVEL' => 3,
);
$arOrder = array(
	"LEFT_MARGIN" => "ASC",
);
$arSelect = array(
	"ID", 
	"NAME",
	"DEPTH_LEVEL", 
);

$db_sections = CIblockSection::GetList($arOrder, $arFilter, true, $arSelect);

$previousLevel = 0;
$previousID = 0;

$skip = false;

while ($arSection = $db_sections->GetNext(true, false))
{
	if($arSection['DEPTH_LEVEL'] == 1 && $arParams['SECTION_ID'])
	{
		$skip = !in_array($arSection['ID'], $arParams['SECTION_ID']);
	}
	
	if($skip || empty($arSection['ELEMENT_CNT']))
		continue;
	
	$arResult["SECTIONS"][$arSection['ID']] = $arSection;
	
	if($previousLevel && $arSection['DEPTH_LEVEL'] > $previousLevel)
	{
		$arResult['SECTIONS_HAVE_CHILDS'][$previousID] = true;	
	}	
	
	$previousLevel = $arSection['DEPTH_LEVEL'];
	$previousID = $arSection['ID'];
}

$arResult["__TEMPLATE_FOLDER"] = $this->__folder; // saving template name to cache array
$this->__component->arResult = $arResult; // writing new $arResult to cache file
