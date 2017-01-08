<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arComponentDescription = array(
	"NAME" => GetMessage("EDOST_CATALOG_DELIVERY_NAME"),
	"DESCRIPTION" => GetMessage("EDOST_CATALOG_DELIVERY_DESC"),
	"ICON" => "/images/ico.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => GetMessage("EDOST_CATALOG_DELIVERY_ID"),
		"NAME" => GetMessage("EDOST_CATALOG_DELIVERY_PATHNAME"),
	),
);
?>