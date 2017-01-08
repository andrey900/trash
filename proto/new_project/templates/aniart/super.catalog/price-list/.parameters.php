<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// Находим список инфоблоков
if(!CModule::IncludeModule("iblock")) return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();
$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()):
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
endwhile;

$db_sections = CIblockSection::GetList(array('NAME' => 'ASC'), array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"], "ACTIVE" => "Y", 'DEPTH_LEVEL' => 1), true, array('ID', 'NAME'));
while ($arSection = $db_sections->GetNext(true, false))
{
	$arSections[$arSection["ID"]] = "[".$arSection["ID"]."] ".$arSection["NAME"];
}

$arTemplateParameters = array(
	"IBLOCK_TYPE" => array(
		"PARENT" => "DATA_SOURSE",
		"NAME" => "Тип инфоблока",
		"TYPE" => "LIST",
		"VALUES" => $arIBlockType,
		"REFRESH" => "Y",
	),
	"IBLOCK_ID" => array(
		"PARENT" => "DATA_SOURSE",
		"NAME" => "Код инфоблока",
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y",
	),
	"SECTION_ID" => array(
		"PARENT" => "DATA_SOURSE",
		"NAME" => "Разделы",
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"SIZE" => 15,
		"VALUES" => $arSections,
		"MULTIPLE" => 'Y',
		"REFRESH" => "Y",
	),
	"SHOW_PARAMS" => array(
		"PARENT" => "OVERALL",
		"NAME" => "Показывать настройки",
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);
?>