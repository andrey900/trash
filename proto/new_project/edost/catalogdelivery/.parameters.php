<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
		"EDOST_CD" => array(
			"NAME" => GetMessage("EDOST_CATALOG_DELIVERY_ERR_CITYNOTFILL"),
		),
	),
	"PARAMETERS" => array(

		"SET_JQUERY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("EDOST_CATALOG_DELIVERY_JQUERY"),
			"TYPE" => "STRING",
			"DEFAULT" => "Y",
		),
		"SHOW_QTY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("EDOST_CATALOG_DELIVERY_SHOW_QTY"),
			"TYPE" => "STRING",
			"DEFAULT" => "N",
		),

/*		"FRAME_X" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("EDOST_CATALOG_DELIVERY_FX"),
			"TYPE" => "STRING",
			"DEFAULT" => 780,
		),
		"FRAME_Y" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("EDOST_CATALOG_DELIVERY_FY"),
			"TYPE" => "STRING",
			"DEFAULT" => 650,
		),
*/
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),

	),

);


?>
