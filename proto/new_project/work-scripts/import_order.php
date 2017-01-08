<?php
define('ANIART_DEBUG', false);
define('BX_NO_ACCELERATOR_RESET', true);

@set_time_limit(0);
ini_set("memory_limit", "512M");

if (!$_SERVER["DOCUMENT_ROOT"]) {
        // забираем отсюда конфиг
        $_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
		//error_reporting(E_ALL);
}/* else {
        die("Run only in console. Stop program...");  // только с консоли
}*/

include_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/include/classes/CFileLogger.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/work-scripts/include/class/c_soapclientport.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/work-scripts/include/class/c_soapclientfromultima.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/work-scripts/include/class/c_fastsoapclientfromultima.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/work-scripts/include/class/c_ultimatotaldatainfo.php');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once($_SERVER["DOCUMENT_ROOT"].'/work-scripts/include/class/c_ultimaandbitrixintegration.php');

include_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/include/classes/CAniartULBX.php');
/*
$str_file_name_work_flag = dirname(__FILE__).'/cron_work.pid';

if ( file_exists($str_file_name_work_flag)) {
    // TODO если файлу много часов, надо слать уведомление о проблемах
    die('runing...');
}

$b_need_load = true;
/*
if ($b_need_load ) {
    // ставим флаг что мы работаем  
    $h_file = fopen($str_file_name_work_flag,'w+');
} else {
    die('No work ...');
}
*/
CModule::IncludeModule("sale");

$arOrdersUltima = CAniartULBX::GetOrdersNoUltima();
//$arOrdersUltima[2841] = CAniartOrdersUltima::GetByOrderId(2841);
//p($arOrdersUltima);die();

$arOrdersId = array();
foreach( $arOrdersUltima as $arOrder ){
	$arOrdersId[$arOrder['ORDER_ID']] = $arOrder['ORDER_ID'];
}
$arOrders = CAniartULBX::GetOrderInfo($arOrdersId);
//p($arOrders);

$wsdl = "http://89.249.18.174:50080/bitrix/OtherWebService.asmx?WSDL";
$soap = new SoapClientPort($wsdl, array());
$arOrders = CAniartULBX::CheckUser($arOrders, $soap);
$arOrders = CAniartULBX::CheckAddress($arOrders, $soap);
$arOrders = CAniartULBX::MergingOrders($arOrders, $arOrdersUltima);

$arOrders = CAniartULBX::CreateReserve($arOrders, $soap);

$arOrders = array();
$filter = Array
(
    "DATE_REGISTER_1" => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), time()),
    "DATE_REGISTER_2" => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), time()-30*60),
    "ACTIVE"          => "Y",
    "UF_AGENT"        => false,
);
$arSelect = array('SELECT' => array("UF_*") );
$order = array('id' => 'asc');
$tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
$rsUsers = CUser::GetList($order, $tmp, $filter, $arSelect);
while($arUser = $rsUsers->Fetch())
    $arOrders[]['USER_INFO'] = $arUser;

if(!empty($arOrders))
    CAniartULBX::CheckUser($arOrders, $soap);

//p($arOrdersUltima);
//p($arOrders);
