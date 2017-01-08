<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");


$arFields = array(
    "QUANTITY" => $_REQUEST['quantity']
);

CSaleBasket::Update($_REQUEST['id'], $arFields);