<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

//CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

Add2BasketByProductID(
    $_REQUEST['id'],
    1,
    array(),
    array()
);


/*    $arFields = array(
        "PRODUCT_ID" => $_REQUEST['id'],
        "QUANTITY" => 1
    );

    $id = CSaleBasket::Add($arFields);

echo $id;*/
