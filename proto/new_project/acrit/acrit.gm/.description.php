<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ACRIT_GOOGLEMERCHANT_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_GOOGLEMERCHANT_DESC"),
	"ICON" => "/images/export.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "acrit",
		"NAME" => GetMessage("ACRIT_ROOT_NAME"),
	),
);

?>