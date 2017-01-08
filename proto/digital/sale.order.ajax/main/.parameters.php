<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"DISPLAY_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "90",
	),
	"DISPLAY_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "90",
	),
	"SHOW_STORES_IMAGES" => Array(
		"NAME" => GetMessage("T_SHOW_STORES_IMAGES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"N",
		"PARENT" => "BASE",
	),
);
?>
