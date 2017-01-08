<?php
ini_set("soap.wsdl_cache_enabled", 0);
ini_set("max_execution_time", 0);

//error_reporting(E_ALL);
include_once('include/class/c_soapclientport.php');
include_once('include/class/c_soapclientfromultima.php');
include_once('include/class/c_fastsoapclientfromultima.php');
include_once('include/class/c_ultimatotaldatainfo.php');

$ultimaConnect = new UltimaTotalDataInfo();

$ultimaConnect->config->warehouseIds = array(1);    // номер склада
$ultimaConnect->config->priceCatIds  = array(195);  // номер ценовой категории
$ultimaConnect->config->nameGoodsId  = 'GoodId';    // название поля для определения ИД товара
$ultimaConnect->config->countRequest = 900;		    // количество элементов в пакете(<1000)
$ultimaConnect->config->scriptUpdateTime = 4*60*60;   // интервал с которым вызывается скрипт

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

$arLastMod = $arLastRemainds + $arLastModPrice;

//$arLastMod = $ultimaConnect->GetAllGoods();

unset($arLastModPrice);unset($arLastRemainds);unset($ultimaConnect);
// Подключаю тут, что б не останавливать работу сайта, поскольку получение данных может затягиваться до нескольких минут
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once('include/class/c_ultimaandbitrixintegration.php');

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog"); 

/*
$PRODUCT_ID = 10921; // id товара
$arFields = array('QUANTITY' => 14,
				  'QUANTITY_RESERVED' => 11,
				  'WIDTH'  => 0,
				  'LENGTH' => 0,
				  'HEIGHT' => 0,
				  'WEIGHT' => 0);
CCatalogProduct::Update($PRODUCT_ID, $arFields);
*/
/*
$arFields = Array(
    "PRODUCT_ID" => $PRODUCT_ID,
    "CATALOG_GROUP_ID" => 1,
    "PRICE" => 29.95,
    "CURRENCY" => "RUB",
);
CPrice::Add($arFields);*/
//CPrice::Delete(112387);

//$arElement = CAniartTools::_GetInfoElements(false, array('ID', 'XML_ID'), array('IBLOCK_ID'=>IBLOCK_CATALOG, 'EXTERNAL_ID'=>47438/*$arExtId*/, 'INCLUDE_SUBSECTIONS'=>'Y'));
//$arPrice = CAniartTools::_GetPriceElements(10921);
//$ar_res = CCatalogProduct::GetByID(10921);


$ultimabitrix = new UltimaAndBitrixIntegration($arLastMod);
$ultimabitrix->config->iblock_id = IBLOCK_CATALOG;  // ИД инфоблока для каталога
$ultimabitrix->config->section_id = 1109;			// Секция для новых элементов
$ultimabitrix->config->pricecataloggroupid = 1;		// группа ценовых предложений
$ultimabitrix->config->currency = 'RUB';			// Валюта
$ultimabitrix->config->price 	= 'Price';			// Название цены в массиве из ультимы
$ultimabitrix->config->quantity = 'Free';			// Название доспутного количества в массиве из ультимы
$ultimabitrix->config->weight   = 'Weight';			// Название веса в массиве из ультимы
$ultimabitrix->config->height   = 'Height';			// Название ширины в массиве из ультимы
$ultimabitrix->config->length   = 'Length';			// Название высоты в массиве из ультимы
$ultimabitrix->config->width 	= 'Width';			// Название длинны в массиве из ультимы
$ultimabitrix->config->quantityres = 'Reserve';		// Название зарезервированое кол-во в массиве из ультимы

//$ultimabitrix->execute();


echo "<pre>";
$ultimabitrix->execute();
//$ultimabitrix->updatePrice(8738, 418.56);

//$t = CAniartTools::_GetInfoElements(false, array('ID', 'XML_ID'), array('IBLOCK_ID'=>$ultimabitrix->config->iblock_id, 'INCLUDE_SUBSECTIONS'=>'Y'));
//echo count($t);
//print_r($ultimabitrix->arElement);
//print_r($arElement);
//echo count($arElement);
//print_r($arPrice);
//echo count($arPrice);
//print_r($ar_res);
//print_r($ultimaConnect->GetLastModRemains());
//print_r($ultimaConnect->GetLastModPrice());