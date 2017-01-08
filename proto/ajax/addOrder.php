<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

CModule::IncludeModule("sale");
$arFields = array(
    "STATUS_ID" => "P",
    "PERSON_TYPE_ID" => 1,
    "PAYED" => "N",
    'CURRENCY' => 'RUB',
    "LID"=>SITE_ID,
    "PRICE"=>str_replace(" ","",substr($_REQUEST['sum'], 0, -4)),
    "USER_ID" => IntVal($_REQUEST['user']),
    "USER_DESCRIPTION" => $_REQUEST['name']
);

/*// add Guest ID
if (CModule::IncludeModule("statistic"))
    $arFields["STAT_GID"] = CStatistic::GetEventParam();*/

// add Guest ID
if (CModule::IncludeModule("statistic"))
    $arFields["STAT_GID"] = CStatistic::GetEventParam();

$ORDER_ID = CSaleOrder::Add($arFields);
$ORDER_ID = IntVal($ORDER_ID);

CSaleBasket::OrderBasket($ORDER_ID, $_SESSION["SALE_USER_ID"], SITE_ID);

echo $ORDER_ID;