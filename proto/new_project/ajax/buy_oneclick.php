<?php
include_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php';

function CreateUserPassword($phonenumber){

    if( strripos($phonenumber, '@') )
        return preg_replace('/[-.,_*\'@+|]/', '', $phonenumber);

    $arlatters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j');
    $arnumbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $phonenumber = preg_replace('/[^0-9]/', '', $phonenumber);
    return str_replace($arnumbers, $arlatters, $phonenumber);

}

/**
 * Поиск пользователя в БД и возврат массива адресов и телефонов
 **/
function findUser($phone){
    $arFalseUserId = $arUsers = array();
    $order = array('id' => 'asc');
    $tmp = 'sort'; // параметр проигнорируется методом, но обязан быть
    $arFilter = Array(
       "PERSONAL_PHONE" => "_%", // пропускаем пустые телефоны
   );
    $dbUsers = CUser::GetList($order, $tmp, $arFilter);
    while ($arUser = $dbUsers->GetNext()) 
    {
        if( !preg_match("/@ssr-russia.ru/", $arUser['EMAIL']) && // мнимые емаилы
            !preg_match("/^[+7]+$/", $arUser['PERSONAL_PHONE'])  // пустые телефоны
        ){
            $match1 = preg_replace("/\D/", "",$arUser['PERSONAL_PHONE']);
            $match2 = preg_replace("/\D/", "",$phone);
            $r = array("ID" => $arUser['ID'], 
                        "EMAIL" => $arUser['EMAIL'], 
                        "PHONE" => $arUser['PERSONAL_PHONE']
                       );
            if($match2 == $match1)
                $arUsers[$arUser['ID']] = $r;
        }
    }

    return $arUsers;
}

//получили номер мобильного
$phone = preg_replace('/[^0-9]/', '', $_REQUEST['phone']);
$email = filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL);

if( (empty($phone) && !check_email($email)) || (int)$_REQUEST['id'] <= 0){
    die('Error request');
}

if (!CModule::IncludeModule("catalog")) die();
if (!CModule::IncludeModule("sale")) die();

/***********************************************************************
 *
 *      Создали юзера
 *
 ***********************************************************************/
if( !empty($phone) ){
    $user = CUser::GetList(($by="personal_country"), ($order="desc"), array('PERSONAL_PHONE' => $phone) )->GetNext();
    $pwd = CreateUserPassword($phone);
    $strType = 'phone';
    $TYPE = 'номеру телефона';
    $VALUE = $phone;
    $S_VALUE = $phone;
} else {
    $user = CUser::GetList(($by="personal_country"), ($order="desc"), array('EMAIL' => $email) )->GetNext();
    $strType = 'email';
    $TYPE = 'электронному адресу';
    $VALUE = $email;
    $S_VALUE = "<a href='mailto:$email'>$email</a>";
    $pwd = CreateUserPassword($email);
}

//var_dump($user);

if (!$user) {
        $data['LOGIN'] = (!empty($phone))?$phone:$email;
        $data['PERSONAL_PHONE'] = $phone;
        $data['EMAIL'] = (!empty($phone))?$pwd. '@ssr-russia.ru':$email;
        $data['ACTIVE'] = 'Y';
        $data['GROUP_ID'] = array(2, 9);
        $data['PASSWORD'] = $pwd;
        $data['CONFIRM_PASSWORD'] = $pwd;
        
        $u = new CUser;
        $user['ID'] = $u->Add($data);
}

//$groups = CUser::GetUserGroup($user['ID']);
// var_dump($groups);

// $dVal = -0.01;
// if (in_array('7', $groups) ) $dVal = -3;
// if (in_array('8', $groups) ) $dVal = -5;
// if ( in_array('9', $groups) || in_array('10', $groups) ) $dVal = -7;
// if ( in_array('1', $groups) || in_array('11', $groups) ) $dVal = -10;
//$dVal = false;//discount value
// if (!$USER->isAuthorized()) CUser::Authorize($user['ID']);


/////////////


//////////////////

if ($_REQUEST['func'] == 'buy_item' ) {
        // собираем старую корзину
        $tmp_basket = CSaleBasket::GetList( array("NAME" => "ASC", "ID" => "ASC"), array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"), false, false, array("PRODUCT_ID", "QUANTITY"));
        while ($arItems = $tmp_basket->Fetch())
        {
            $tmp_items[] = $arItems;
        }

        CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());
        $item_id = (int)$_REQUEST['id'];
        Add2BasketByProductID($item_id, 1);
}
/*
$total_qty = 0;
$total_sum = 0;
$dbBasketItems = CSaleBasket::GetList(
        array("NAME" => "ASC","ID" => "ASC"),
        array("FUSER_ID" => CSaleBasket::GetBasketUserID(),"LID" => SITE_ID,"ORDER_ID" => "NULL"),
        false, false, array()
);

while ($arBasketItems = $dbBasketItems->GetNext()) {
    p($arBasketItems);
}
*/

$dbBasketItems = CSaleBasket::GetList(
                array(
                                'DATE_INSERT' => 'ASC',
                                "NAME" => "ASC",
                                "ID" => "ASC"
                        ),
                array(
                                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                                "LID" => SITE_ID,
                                "ORDER_ID" => "NULL"
                        ),
                false,
                false,
                array("ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE", "CATALOG_XML_ID", "PRODUCT_XML_ID", "SUBSCRIBE", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS")
        );
while ($arItems = $dbBasketItems->GetNext())
{
        $arItems['QUANTITY'] = $arParams['QUANTITY_FLOAT'] == 'Y' ? number_format(DoubleVal($arItems['QUANTITY']), 2, '.', '') : IntVal($arItems['QUANTITY']);

        $arItems["PROPS"] = Array();
        if(in_array("PROPS", $arParams["COLUMNS_LIST"]))
        {
                $dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arItems["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
                while($arProp = $dbProp -> GetNext())
                        $arItems["PROPS"][] = $arProp;
        }

        $arItems["PRICE_VAT_VALUE"] = (($arItems["PRICE"] / ($arItems["VAT_RATE"] +1)) * $arItems["VAT_RATE"]);
        $arItems["PRICE_FORMATED"] = SaleFormatCurrency($arItems["PRICE"], $arItems["CURRENCY"]);
        $arItems["WEIGHT"] = DoubleVal($arItems["WEIGHT"]);
        $arItems["WEIGHT_FORMATED"] = roundEx(DoubleVal($arItems["WEIGHT"]/$arParams["WEIGHT_KOEF"]), SALE_VALUE_PRECISION)." ".$arParams["WEIGHT_UNIT"];

        if ($arItems["DELAY"] == "N" && $arItems["CAN_BUY"] == "Y")
        {
                $allSum += ($arItems["PRICE"] * $arItems["QUANTITY"]);
                $allWeight += ($arItems["WEIGHT"] * $arItems["QUANTITY"]);
                $allVATSum += roundEx($arItems["PRICE_VAT_VALUE"] * $arItems["QUANTITY"], SALE_VALUE_PRECISION);
        }

        if ($arItems["DELAY"] == "N" && $arItems["CAN_BUY"] == "Y")
        {
                $bShowReady = True;
                if(DoubleVal($arItems["DISCOUNT_PRICE"]) > 0)
                {
                        $arItems["DISCOUNT_PRICE_PERCENT"] = $arItems["DISCOUNT_PRICE"]*100 / ($arItems["DISCOUNT_PRICE"] + $arItems["PRICE"]);
                        $arItems["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arItems["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
                        $DISCOUNT_PRICE_ALL += $arItems["DISCOUNT_PRICE"] * $arItems["QUANTITY"];
                }
                $arResult["ITEMS"]["AnDelCanBuy"][] = $arItems;
        }
        elseif ($arItems["DELAY"] == "Y" && $arItems["CAN_BUY"] == "Y")
        {
                $bShowDelay = True;
                $arResult["ITEMS"]["DelDelCanBuy"][] = $arItems;
        }
        elseif ($arItems["CAN_BUY"] == "N" && $arItems["SUBSCRIBE"] == "Y")
        {
                $bShowSubscribe = True;
                $arResult["ITEMS"]["ProdSubscribe"][] = $arItems;
        }
        else
        {
                $bShowNotAvail = True;
                $arResult["ITEMS"]["nAnCanBuy"][] = $arItems;
        }

        $arBasketItems[] = $arItems;
}

$arOrder = array(
        'SITE_ID' => SITE_ID,
        'USER_ID' => $user['ID'],
        'ORDER_PRICE' => $allSum,
        'ORDER_WEIGHT' => $allWeight,
        'BASKET_ITEMS' => $arResult["ITEMS"]["AnDelCanBuy"]
);

$arOptions = array(
        'COUNT_DISCOUNT_4_ALL_QUANTITY' => 'Y',
);

$arErrors = array();
/*
// Discount
CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors, $dVal);

$allSum = 0;
$allWeight = 0;
$allVATSum = 0;
$DISCOUNT_PRICE_ALL = 0;
foreach ($arOrder['BASKET_ITEMS'] as &$arOneItem)
{
        $allSum += ($arOneItem["PRICE"] * $arOneItem["QUANTITY"]);
        $arOneItem["PRICE_FORMATED"] = SaleFormatCurrency($arOneItem["PRICE"], $arOneItem["CURRENCY"]);
        $arOneItem["DISCOUNT_PRICE_PERCENT"] = $arOneItem["DISCOUNT_PRICE"]*100 / ($arOneItem["DISCOUNT_PRICE"] + $arOneItem["PRICE"]);
        $arOneItem["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arOneItem["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
        $DISCOUNT_PRICE_ALL += $arOneItem["DISCOUNT_PRICE"] * $arOneItem["QUANTITY"];
}
if (isset($arOneItem))
        unset($arOneItem);
*/
$arResult["ITEMS"]["AnDelCanBuy"] = $arOrder['BASKET_ITEMS'];

$arResult["allSum"] = $allSum;
$arResult["allWeight"] = $allWeight;
//$arResult["allWeight_FORMATED"] = roundEx(DoubleVal($allWeight/$arParams["WEIGHT_KOEF"]), SALE_VALUE_PRECISION)." ".$arParams["WEIGHT_UNIT"];
$arResult["allSum_FORMATED"] = SaleFormatCurrency($allSum, $allCurrency);
//$arResult["DISCOUNT_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DISCOUNT_PRICE"], $allCurrency);

$order_price = $arOrder['ORDER_PRICE'];

$arFields = array(
        'LID'                   => SITE_ID,
        'PERSON_TYPE_ID'        => 1,
        'STATUS_ID'             => 'N',
        'PRICE'                 => $order_price,
        'CURRENCY'              => 'RUB',
        'USER_ID'               => $user['ID'],
        /*'PAY_SYSTEM_ID'         => 1,
        'DELIVERY_ID'           => 1,*/
);

if( $order_price > 0 ){
    $ORDER_ID = CSaleOrder::Add($arFields);
    $ORDER_ID = IntVal($ORDER_ID);
    CSaleBasket::OrderBasket($ORDER_ID, CSaleBasket::GetBasketUserID(), SITE_ID);
} else {
    $ORDER_ID = 0;
}

if( $ORDER_ID > 0 ){

    $arEventFields = array(
        "ORDER_ID"   => $ORDER_ID,
        "ORDER_DATE" => date("d.m.Y H:i:s"),
        "ITEM_NAME"  => $arOrder['BASKET_ITEMS'][0]['NAME'],
        "ITEM_ID"    => $arOrder['BASKET_ITEMS'][0]['ID'],
        "ITEM_DET_P" => $arOrder['BASKET_ITEMS'][0]['DETAIL_PAGE_URL'],
        "PRICE_SUM"  => SaleFormatCurrency($order_price, $arFields['CURRENCY']),//$arResult["allSum_FORMATED"],
        "TYPE"       => $TYPE,
        "VALUE"      => $VALUE,
        "S_VALUE"    => $S_VALUE,
        "SALE_EMAIL" => 'zakaz@ssr-russia.ru',
    );
    $idpostmess = CEvent::SendImmediate("SALE_STATUS_CHANGED_N", SITE_ID, $arEventFields, "N", MAIL_T_BUY_ONE_CLICK);
    
    // Отправка емаил пользователю
    if( $strType=='email' ){        // если указал сразу емаил
        $arEventFields['EMAIL'] = $VALUE;
        $iduserpostmess = CEvent::SendImmediate("ORDER_BY_ONE_CLICK", SITE_ID, $arEventFields, "N", MAIL_T_U_BUY_ONE_CLICK);
    } elseif( $strType=='phone' ){ // если указал номер телефона, поиск пользователя с таким телефоном
        //$arUsersEmail = findUser($VALUE);
        if( !empty($arUsersEmail) ){
            foreach ($arUsersEmail as $arUserEmail) {
                $arEventFields['EMAIL'] = $arUserEmail['EMAIL'];
                $iduserpostmess = CEvent::SendImmediate("ORDER_BY_ONE_CLICK", SITE_ID, $arEventFields, "N", MAIL_T_U_BUY_ONE_CLICK);
            }
        }
    }

}

$rsOrderProp = CSaleOrderProps::GetList(array(), array('CODE' => 'tel', 'PERSON_TYPE_ID' => $arFields['PERSON_TYPE_ID']));
if($arOrderProp = $rsOrderProp->Fetch()){
        CSaleOrderPropsValue::Add(array('ORDER_ID' => $ORDER_ID, 'ORDER_PROPS_ID'       => $arOrderProp['ID'], 'NAME' => $arOrderProp['NAME'], 'VALUE' => $phone, 'CODE' => $arOrderProp['CODE'] ));
}
if ($user['LAST_NAME'] || $user['SECOND_NAME']) {
        $rsOrderProp = CSaleOrderProps::GetList(array(), array('CODE' => 'FIO', 'PERSON_TYPE_ID' => $arFields['PERSON_TYPE_ID']));
        if($arOrderProp = $rsOrderProp->Fetch()){
                CSaleOrderPropsValue::Add(array('ORDER_ID' => $ORDER_ID, 'ORDER_PROPS_ID'       => $arOrderProp['ID'], 'NAME' => $arOrderProp['NAME'], 'VALUE' => $user['NAME'] . ' ' . $user['LAST_NAME'], 'CODE' => $arOrderProp['CODE'] ));
        }
}

//CSaleBasket::OrderBasket($ORDER_ID, CSaleBasket::GetBasketUserID(), SITE_ID);

// Возвращаем корзину назад
if ($_REQUEST['func'] == 'buy_item' ) {
        // CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());
        // var_dump(111);
        foreach ($tmp_items as $item) {
                Add2BasketByProductID($item['PRODUCT_ID'], $item['QUANTITY']);
        }
}

if( $ORDER_ID > 0 ) {
    $return['order']['id'] = $ORDER_ID;
    $return['order']['rev'] = $order_price;
    foreach ($arOrder['BASKET_ITEMS'] as $value) {
        $return['bi'][$value['PRODUCT_ID']]['name'] = $value['~NAME'];
        $return['bi'][$value['PRODUCT_ID']]['price'] = $value['PRICE'];
        $return['bi'][$value['PRODUCT_ID']]['sku'] = $value['PRODUCT_ID'];
        $return['bi'][$value['PRODUCT_ID']]['quantity'] = $value['QUANTITY'];
    }
    $return['order']['text'] = 'Заказ успешно оформлен. Идентефикатор вашего заказа';
} else {
    $return['order']['id'] = $ORDER_ID;
    $return['order']['rev'] = $order_price;
    $return['order']['text'] = 'Заказ не может быть оформлен. Товар под заказ.';
}
echo json_encode( $return );

// var_dump($tmp_items);