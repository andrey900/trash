<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:sale.basket.basket.line",
    "mini_cart",
    Array(
        "PATH_TO_BASKET" => "/cart/",
        "PATH_TO_PERSONAL" => "/personal/",
        "SHOW_PERSONAL_LINK" => "N",
        "SHOW_NUM_PRODUCTS" => "Y",
        "SHOW_TOTAL_PRICE" => "Y",
        "SHOW_EMPTY_VALUES" => "Y",
        "SHOW_PRODUCTS" => "N",
        "POSITION_FIXED" => "N"
    )
);?>