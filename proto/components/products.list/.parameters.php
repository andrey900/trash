<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"ELEMENTS_COUNT" => Array(
			"PARENT" => "BASE",
			"NAME" => "К-во элементов",
			"TYPE" => "STRING",
			"DEFAULT" => "10",
		),
		"SORT_NAME" => Array(
			"PARENT" => "BASE",
			"NAME" => "Название переменной для сортировки",
			"TYPE" => "STRING",
			"DEFAULT" => ""
		),
		"FILTER_NAME" => Array(
			"PARENT" => "BASE",
			"NAME" => "Название переменной для фильтрации",
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"SELECT_NAME" => Array(
			"PARENT" => "BASE",
			"NAME" => "Название переменной для выбора полей",
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"GET_OFFERS" => array(
			"PARENT" => "BASE",
			"NAME" => "Доставать информацию о торговых предложениях",
			"TYPE" => "CHECKBOX",
			"DEFAULT" => ""
		),
		"SHOW_PAGINATION" => Array(
			"PARENT" => "BASE",
			"NAME" => "Показывать постраничную навигацию",
			"TYPE" => "CHECKBOX",
			"VALUE" => "N"
		),			
		"NAV_TEMPLATE" => array(
			"PARENT" => "BASE",
			"NAME" => "Шаблон для постранички",
			"TYPE" => "STRING",
			"VALUE" => ""
		),
		"NAV_PAGE_VAR" => array(
			"PARENT" => "BASE",
			"NAME" => "Название GET переменной, в которой хранится текущая страница",
			"TYPE" => "STRING",
			"DEFAULT" => "page",
			"VALUE" => ""
		),
		"CACHE_TIME" => array("DEFAULT" => "3600"),
	),
);
?> 