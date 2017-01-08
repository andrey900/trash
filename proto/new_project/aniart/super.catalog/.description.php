<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Супер каталог",
	"DESCRIPTION" => "Пустой компонент каталога для создания любой логики",
	"ICON" => "/images/catalog.gif",
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "vidicom",
		"CHILD" => array(
			"ID" => "super",
			"NAME" => "Супер компоненты"
		)
	),
);

?>