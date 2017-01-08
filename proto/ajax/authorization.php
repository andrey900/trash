<?php
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

global $USER;

$login = $_REQUEST['params']['login'];
$pass = $_REQUEST['params']['pass'];

$arAuthResult = $USER->Login($login, $pass, "Y");

if($arAuthResult['TYPE'] == 'ERROR') {
    echo $arAuthResult['MESSAGE'];
} else {
    echo "Y"; 
}

?>