<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arPrice = array();
$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => "Идентификатор товара",
			"TYPE" => "STRING",
			"DEFAULT" => "",			
		),
		"ELEMENT_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => "Символьный код товара",
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"SECTION_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => "Символьный код секции",
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"CACHE_TIME" => array("DEFAULT" => "3600"),
	),
);
?>
