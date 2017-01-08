<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$services = '';
//print_r($_REQUEST['services']);

$i = 0;


if (!empty($_REQUEST['services'])) {
    $cnt = count ($_REQUEST['services']);

    foreach ($_REQUEST['services'] as $k=>$v) {
        if ($i == 0) {
            $services .= $v;
        } else {
            $services .= ' ,'.$v;
        }
        $i++;
    }
    $arProductParams = array(
        array(
            "NAME" => "Сервисы",
            "CODE" => "SERVICES",
            "VALUE" => $services
        )
    );

}
/*print_r($arProductParams);

die();*/
$id = Add2BasketByProductID(
    $_REQUEST['id'],
    $_REQUEST['quantity'],
    array(),
    (isset($arProductParams)) ? $arProductParams : array()
);
echo $id;