<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");


$id = Add2BasketByProductID(
    $_REQUEST['ids'][0],
    1,
    array(),
    array(
        array(
            "NAME" => "Акционное предложение",
            "CODE" => "SET",
            "VALUE" => $_REQUEST['ids'][1]
        )
    )
);
echo $id;