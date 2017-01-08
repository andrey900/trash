<?php
ini_set("soap.wsdl_cache_enabled", 0);
ini_set("max_execution_time", 0);
error_reporting(E_ALL);

/////////////////////////////////////////////////////////////////////////////////////////////////////
// Чтобы не приходилось на разных хост-площадках прописывать руками $_SERVER["DOCUMENT_ROOT"]
// расчитываем пути к корню сайта. Логика скрипта считает, что скрипт выполняется в каталоге 1-го уровня
/////////////////////////////////////////////////////////////////////////////////////////////////////
$f_info = pathinfo(__FILE__);

if (!$_SERVER["DOCUMENT_ROOT"]) {

        $ar_parts_path = explode("/",$f_info["dirname"]);

        $path_root = "";

        for ($i = 1; $i < count($ar_parts_path) - 1; $i++) {
                $path_root .= "/".$ar_parts_path[$i];
        }

        $_SERVER["DOCUMENT_ROOT"] = $path_root;

}
//echo $_SERVER['DOCUMENT_ROOT'];

include_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/include/classes/CFileLogger.php');
include_once('include/class/c_soapclientport.php');
include_once('include/class/c_soapclientfromultima.php');
include_once('include/class/c_fastsoapclientfromultima.php');
include_once('include/class/c_ultimatotaldatainfo.php');

$log = new CFileLogger('/log/csoap_'.date('d-m-Y').'.log');
$log->WriteIntoLog('======================================================================='.PHP_EOL);
$log->WriteIntoLog('New log from date '.date("Y-m-d H:i:s").PHP_EOL);
$log->WriteIntoLog('======================================================================='.PHP_EOL);

$ultimaConnect = new UltimaTotalDataInfo();

$ultimaConnect->config->warehouseIds = array(1);    // номер склада
$ultimaConnect->config->priceCatIds  = array(195);  // номер ценовой категории
$ultimaConnect->config->nameGoodsId  = 'GoodId';    // название поля для определения ИД товара
$ultimaConnect->config->countRequest = 900;		    // количество элементов в пакете(<1000)

//$ultimaConnect->config->scriptUpdateTime = 2*24*60*60; // интервал с которым вызывается скрипт(ч:м:с)
$ultimaConnect->config->scriptUpdateTime = 1*60*60+(60*15); // интервал с которым вызывается скрипт(ч:м:с)//ssr
//echo date("Y-m-d H:i:s", time()-$ultimaConnect->config->scriptUpdateTime);
//var_dump( $ultimaConnect->FGetGoodsPricesFromId(array(21830)) );
//die;
$log->WriteNotice('Get data from date: '.date("Y-m-d H:i:s", time()-$ultimaConnect->config->scriptUpdateTime));

//print_r(date("d-m-Y H:i:s", time() - $ultimaConnect->config->scriptUpdateTime));
//print_r($ultimaConnect->GetAllGoods());
//print_r($ultimaConnect->FGetLastGoodsRemains());
//print_r($ultimaConnect->FGetAllGoodsRemains());
//print_r($ultimaConnect->GetGoodsPricesFromId());
/*echo "<pre>";
$t = $ultimaConnect->FGetAllGoodsRemains();
echo "Количество элементов по методу FGetAllGoodsRemains: ".count($t[1])."<br>";
$t = $ultimaConnect->FGetAllGoodsPrices();
echo "Количество элементов по методу FGetAllGoodsPrices: ".count($t[1])."<br>";
$t = $ultimaConnect->FGetAllGoodsAndSizes();
echo "Количество элементов по методу FGetAllGoodsAndSizes: ".count($t[1])."<br>";
*/

$arLastRemainds = $ultimaConnect->GetLastModRemains();
$arLastModPrice = $ultimaConnect->GetLastModPrice();
$arLastGAndSizes = $ultimaConnect->GetLastGoodsAndSizes();


$arLastMod = $arLastRemainds + $arLastModPrice;
$arLastMod = $arLastMod + $arLastGAndSizes;
//$arLastMod = $ultimaConnect->GetAllGoods();

$arStatus = $ultimaConnect->GetLastModStatus();

unset($arLastModPrice);unset($arLastRemainds);unset($arLastGAndSizes);unset($ultimaConnect);
// Подключаю тут, что б не останавливать работу сайта, поскольку получение данных может затягиваться до нескольких минут
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once('include/class/c_ultimaandbitrixintegration.php');

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog"); 

if( !empty($arLastMod) ){
  $log->WriteNotice('Data is successfully obtained');
  $log->PushData($arLastMod);
} else {
  $log->WriteError('Data is empty!');
}

$ultimabitrix = new UltimaAndBitrixIntegration($arLastMod);
$ultimabitrix->config->iblock_id = IBLOCK_CATALOG;  // ИД инфоблока для каталога
$ultimabitrix->config->section_id = 1159;			// Секция для новых элементов
$ultimabitrix->config->pricecataloggroupid = 1;		// группа ценовых предложений
$ultimabitrix->config->currency = 'RUB';			// Валюта
$ultimabitrix->config->price 	= 'Price';			// Название цены в массиве из ультимы
$ultimabitrix->config->quantity = 'Free';			// Название доспутного количества в массиве из ультимы
$ultimabitrix->config->weight   = 'Weight';			// Название веса в массиве из ультимы
$ultimabitrix->config->height   = 'Height';			// Название ширины в массиве из ультимы
$ultimabitrix->config->length   = 'Length';			// Название высоты в массиве из ультимы
$ultimabitrix->config->width 	= 'Width';			// Название длинны в массиве из ультимы
$ultimabitrix->config->quantityres = 'Reserve';		// Название зарезервированое кол-во в массиве из ультимы
$ultimabitrix->config->active = 'IsActive';   // Название активности в массиве из ультимы

$ultimabitrix->config->property = array(            // bitrix:#PROPERTY_CODE#, ultima:#field_code#
                           'WEIGHT' => $ultimabitrix->config->weight,
                           'HEIGHT' => $ultimabitrix->config->height,
                           'LENGTH' => $ultimabitrix->config->length,
                           'WIDTH'  => $ultimabitrix->config->width);

//$ultimabitrix->execute();


//echo "<pre>";
//print_r($arLastMod);
//$ultimabitrix->updatePrice(19330, 245.2);
$ultimabitrix->execute();

if( !empty($arStatus) ){
  $log->WriteNotice('Data status is successfully obtained');
  $log->PushData($arStatus);
  $ultimabitrixstatus = new UltimaAndBitrixIntegration($arStatus);
  $ultimabitrixstatus->config = $ultimabitrix->config;
  $ret = $ultimabitrixstatus->execUpdateActiveStatus();
  $log->WriteNotice('Logger element change status:'.PHP_EOL.$ret.PHP_EOL);
} else {
  $log->WriteError('Data status is empty!');
}

$log->WriteNotice('Data is successfully updated'.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL);