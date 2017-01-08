<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 
$arComponentDescription = array(
    "NAME" => GetMessage("WEBSLON_PROMOCODES_COMPONENT_NAME"),
    "DESCRIPTION" => GetMessage("WEBSLON_PROMOCODES_COMPONENT_DESCR"),
    "ICON" => "/images/icon_popup.gif",
    "CACHE_PATH" => "Y",
    "SORT" => 100,
    "PATH" => array(
		"ID" => "webslonComponentsMenu",
		"NAME" => GetMessage("WEBSLON_COMPONENTS_MENU"),
		"CHILD" => array(
			"ID" => "webslonComponentsAdanalysisMenu",
			"NAME" => GetMessage("WEBSLON_COMPONENTS_ADANALYSIS_MENU"),
		),
	),
);
?>
