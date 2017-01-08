<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_ID" => Array(
			"PARENT" => "BASE",
			"NAME" => "Инфоблок(id)",
			"TYPE" => "STRING",
			"DEFAULT" => "62",
		),
		"CODE"		=> Array(
			"PARENT" => "BASE",
			"NAME" => "Символьный код элемента",
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"CACHE_TIME" => array("DEFAULT" => "3600"),
	),
);
?> 