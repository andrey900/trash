<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
		"OVERALL" => array(
			"NAME" => "Общие настройки",
			"SORT" => 10,
		),
		"DATA_SOURSE" => array(
			"NAME" => "Источник данных",
			"SORT" => 20,
		),
		"STYLE_SETTINGS" => array(
			"NAME" => "Стили",
			"SORT" => 30,
		),
	),
	"PARAMETERS" => array(
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		/*
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => "Учитывать группу пользователя",
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		*/
	),
);
?>
