<?
/**
 * подключаем скрипты
 * vars.php содержит набор переменных и констант, используемых в текущем проекте
 * utils.php содержит стандартный набор функций, которые могут применяться в любом проекте
 * misc.php содержит набор функций, использующих исключительно в рамках данного проекта
 */

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/vars.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/utils.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/misc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/classes/CAniartTools.php");