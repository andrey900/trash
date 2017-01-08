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

include_once('include/class/c_soapclientport.php');
include_once('include/class/c_soapclientfromultima.php');
include_once('include/class/c_fastsoapclientfromultima.php');
include_once('include/class/c_ultimatotaldatainfo.php');

$ultimaConnect = new UltimaTotalDataInfo();

$ultimaConnect->config->warehouseIds = array(1);    // номер склада
$ultimaConnect->config->priceCatIds  = array(195);  // номер ценовой категории
$ultimaConnect->config->nameGoodsId  = 'GoodId';    // название поля для определения ИД товара
$ultimaConnect->config->countRequest = 900;		    // количество элементов в пакете(<1000)
$ultimaConnect->config->scriptUpdateTime = 1*15*60; // интервал с которым вызывается скрипт(ч:м:с)

//$arLastRemainds = $ultimaConnect->GetLastModRemains();
//$arLastModPrice = $ultimaConnect->GetLastModPrice();
//$arLastGAndSizes = $ultimaConnect->GetLastGoodsAndSizes();

//$arLastMod = $arLastRemainds + $arLastModPrice;
//$arLastMod = $arLastMod + $arLastGAndSizes;

// Подключаю тут, что б не останавливать работу сайта, поскольку получение данных может затягиваться до нескольких минут
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once('include/class/c_ultimaandbitrixintegration.php');

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog"); 

$arElement = CAniartTools::_GetInfoElements(false, array('ID', 'XML_ID'), array('IBLOCK_ID'=>IBLOCK_CATALOG, '!EXTERNAL_ID'=>false, 'INCLUDE_SUBSECTIONS'=>'Y', 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'));
$arExtId = array();
foreach ($arElement as $key => $value) {
    $arExtId[$value['XML_ID']] = $value['XML_ID'];
}

echo count($arExtId);
$arLastMod = $ultimaConnect->GetAllGoodsSizesFromId($arExtId);
echo "<br>".count($arLastMod);

unset($arLastModPrice);unset($arLastRemainds);unset($arLastGAndSizes);unset($ultimaConnect);

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
$ultimabitrix->config->property = array(            // bitrix:#PROPERTY_CODE#, ultima:#field_code#
                           'WEIGHT' => $ultimabitrix->config->weight,
                           'HEIGHT' => $ultimabitrix->config->height,
                           'LENGTH' => $ultimabitrix->config->length,
                           'WIDTH'  => $ultimabitrix->config->width);

//$ultimabitrix->execute();
$ultimabitrix->execUpdateSize();