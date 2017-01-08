<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

//Массив групп
$arMGroups = array();

//Массив параметров
$arMParams = array(
	'TEXT_TO_CHECK' => Array(
		'NAME' => GetMessage("NSANDREY_TEXTCHECKER_TEKST_DLA_PROVERKI"),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
		'PARENT' => 'BASE'
	)
);

$arComponentParameters = array('GROUPS' => $arMGroups, 'PARAMETERS' => $arMParams);