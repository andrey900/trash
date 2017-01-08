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

include_once('include/class/c_soapclientport.php');
include_once('include/class/c_soapclientfromultima.php');
include_once('include/class/c_fastsoapclientfromultima.php');
include_once('include/class/c_ultimatotaldatainfo.php');

$ultimaConnect = new UltimaTotalDataInfo();

$ultimaConnect->config->warehouseIds = array(1);    // номер склада
$ultimaConnect->config->priceCatIds  = array(65);   // номер ценовой категории
$ultimaConnect->config->nameGoodsId  = 'GoodId';    // название поля для определения ИД товара
$ultimaConnect->config->countRequest = 900;		    // количество элементов в пакете(<1000)
$ultimaConnect->config->scriptUpdateTime = 1*45*60; // интервал с которым вызывается скрипт(ч:м:с)

$arLastMod = $ultimaConnect->GetAllLastMod();       // получаем последние измененные товары из ультимы
//$arLastMod = $ultimaConnect->GetById(47530);

unset($ultimaConnect);

// Подключаю тут, что б не останавливать работу сайта, поскольку получение данных может затягиваться до нескольких минут
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once('include/class/c_ultimaandbitrixintegration.php');

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog"); 

$ultimabitrix = new UltimaAndBitrixIntegration($arLastMod);
$ultimabitrix->config->iblock_id = CATALOG_IBLOCK_ID;  // ИД инфоблока для каталога
$ultimabitrix->config->createNewItem = true;        // Создавать новых элементы если ид не найдены
$ultimabitrix->config->section_id    = 1115;		// Секция для новых элементов
$ultimabitrix->config->useExtId = false;            // Поиск элементов по внешнему коду если нет необходимо указать параметр свойства(propertyExtId), а так же добавить в массив для обновления свойств
$ultimabitrix->config->propertyExtId = 'ARTIKUL';   // Свойство вместо EXT_ID
$ultimabitrix->config->pricecataloggroupid = 1;		// группа ценовых предложений
$ultimabitrix->config->currency = 'RUB';			// Валюта
$ultimabitrix->config->price 	= 'Price';			// Название цены в массиве из ультимы
$ultimabitrix->config->quantity = 'Free';			// Название доспутного количества в массиве из ультимы
$ultimabitrix->config->weight   = 'Weight';			// Название веса в массиве из ультимы
$ultimabitrix->config->height   = 'Height';			// Название ширины в массиве из ультимы
$ultimabitrix->config->length   = 'Length';			// Название высоты в массиве из ультимы
$ultimabitrix->config->width 	= 'Width';			// Название длинны в массиве из ультимы
$ultimabitrix->config->quantityres = 'Reserve';		// Название зарезервированое кол-во в массиве из ультимы

$ultimabitrix->config->property = array(            // bitrix:#PROPERTY_CODE#, ultima:#field_code#
                           'ARTIKUL'=> 'GoodId',
                           'BASE_PRICE'=> $ultimabitrix->config->price,
                           //'WEIGHT' => $ultimabitrix->config->weight,
                           'HEIGHT' => $ultimabitrix->config->height,
                           'THICKNESS' => $ultimabitrix->config->length,
                           'WIDTH'  => $ultimabitrix->config->width);

//$ultimabitrix->execute();
//p($ultimabitrix->getElementsId());
//p($ultimabitrix->arExtId);
//p($arLastMod);
//die();
echo "<pre>";
print_r($arLastMod);
//$ultimabitrix->updatePrice(19330, 245.2);
$ultimabitrix->execute();

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
