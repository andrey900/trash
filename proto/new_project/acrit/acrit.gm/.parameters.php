<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => array(
			"ELEMENT_ID" => array("NAME" => GetMessage("ELEMENT_ID_DESC")),
		),
		"SEF_MODE" => array(
				"element" => array(
				"NAME" => GetMessage("DETAIL_PAGE"),
				"DEFAULT" => "#ID#",
				"VARIABLES" => array("ID"=>"ID"),
			),
		),
	),
	);
?>