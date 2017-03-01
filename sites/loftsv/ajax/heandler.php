<?php
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$ajaxFactory = new \Studio8\Main\AjaxFactory();

$ajaxFactory->makeAjaxHeandler()->start();
