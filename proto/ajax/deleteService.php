<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


CModule::IncludeModule("sale");

$product = $_REQUEST['id_product'];
$service = $_REQUEST['id_service'];

$arItems = CSaleBasket::GetByID($product);

// выбираем все сервисы товара
$db_res = CSaleBasket::GetPropsList(
    array(
        "SORT" => "ASC",
        "NAME" => "ASC"
    ),
    array("BASKET_ID" => $product, "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID"))
);
while ($ar_res = $db_res->Fetch())
{
    // удаляем сервис
    if ($ar_res['CODE'] == 'SERVICES') {
        $services = explode(",", $ar_res['VALUE']);
        unset($services[array_search($service, $services)]);
    }

}

if (count($services) > 1) {
    $prop_services = '';

    $i = 0;
    $cnt = count ($services);

    foreach ($services as $v) {
        if ($i == 0) {
            $prop_services .= $v;
        } else {
            $prop_services .= ' ,'.$v;
        }
        $i++;
    }
} else {
    $services = array_values($services);
    $prop_services = $services[0];
}
// обновляем свойство сервисов у товара
if ($prop_services == NULL) {
    $arFields = array(
        "PROPS" => array(
            array()
        )
    );
} else {
    $arFields = array(
        "PROPS" => array(
            array(
                "NAME" => "Сервисы",
                "CODE" => "SERVICES",
                "VALUE" => $prop_services)
        )
    );
}



CSaleBasket::Update($product, $arFields);