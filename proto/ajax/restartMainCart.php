<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:sale.basket.basket",
    "cart",
    Array(
        "COLUMNS_LIST" => array("PRICE","QUANTITY","PROPERTY_IN_SET","PROPERTY_IN_STOCK","PROPERTY_GUARANTEE"),
        "PATH_TO_ORDER" => "/order/",
        "HIDE_COUPON" => "Y",
        "PRICE_VAT_SHOW_VALUE" => "N",
        "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
        "USE_PREPAYMENT" => "N",
        "QUANTITY_FLOAT" => "N",
        "SET_TITLE" => "Y",
        "ACTION_VARIABLE" => "action"
    )
);?>