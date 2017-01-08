<?php
include_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php';
?>


<?
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

CSaleBasket::Delete($_REQUEST['id']);
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:sale.basket.basket",
    "radio",
    array(
        "PATH_TO_ORDER" => "/personal/order.php",
        "HIDE_COUPON" => "Y",
        "COLUMNS_LIST" => array(
            0 => "NAME",
            1 => "PRICE",
            2 => "QUANTITY",
        ),
        "PRICE_VAT_SHOW_VALUE" => "N",
        "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
        "USE_PREPAYMENT" => "N",
        "QUANTITY_FLOAT" => "N",
        "SET_TITLE" => "N",
        "ACTION_VARIABLE" => "action"
    ),
    false
);?>