<?php

/**
 * Тут будет общая логика для загрузки парсинга, подключаемые файлы и тд
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
require "prop_loader.php";

set_time_limit(0);

Cmodule::IncludeModule('catalog');

$arBrands = [];
$arColors = [];
$arMaterials = [];

/**
 * Property find in files
 * array(
 *   @type => [...],
 * )
 */
$arPropsInFile = [
    'brand' => [],
    'color' => []
];

/**
 * Make variable @arPropsInSite | array
 * array(
 *   @type => [...],
 * )
 */
initProps();