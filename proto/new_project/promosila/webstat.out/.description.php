<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("WEBSTAT_LIST"),
	"DESCRIPTION" => GetMessage("WEBSTAT_DESC"),
	"ICON" => "/images/webstat.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 40,
	"PATH" => array(
		"ID" => "Promosila",
		/*"CHILD" => array(
			"ID" => "SEO",
			"NAME" => GetMessage("WEBSTAT"),
			"SORT" => 20,
		)*/
	),
);

?>