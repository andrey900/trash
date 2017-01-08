<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule("fileman");


$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"CACHE_TIME"  =>  Array("DEFAULT"=>180),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BPR_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);

?>
