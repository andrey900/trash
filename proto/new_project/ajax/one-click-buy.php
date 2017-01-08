<?php
include_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php';

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");
CModule::IncludeModule("currency");

//$phone = preg_replace('/[^0-9]/', '', $_REQUEST['PERSONAL_PHONE']);
$phone = $_REQUEST['PERSONAL_PHONE'];
if( empty($phone) ){
    die();
}

if($_REQUEST['page'] == "list")
	$page = "Список товаров";
elseif($_REQUEST['page'] == "detail")
	$page = "Детальная страница товара";
elseif($_REQUEST['page'] == "basket")
	$page = "Корзина";
/*
 * user
 */

if(!$USER->IsAuthorized()){
	/*get viewed*/
	
	$defSes = array();
	$defSes['IBLOCK_COUNTER'] = $_SESSION['IBLOCK_COUNTER'];
	$defSes['VIEWED_PRODUCT'] = $_SESSION['VIEWED_PRODUCT'];
	
	$user = CUser::GetList(
		($by="personal_country"),
		($order="desc"),
		array('PERSONAL_PHONE' => '+'.$phone) 
	)->GetNext();

	if(!$user) {
		$pwd = CreateUserPassword($phone);
		$login = preg_replace('/[^0-9]/', '', $phone);
		
	    $data['LOGIN'] = $login; 
	    $data['PERSONAL_PHONE'] = '+'.$phone;
	    $data['EMAIL'] = $login.'@'.preg_replace("/www./", '', $_SERVER['SERVER_NAME']);
	    $data['ACTIVE'] = 'Y';
	    $data['GROUP_ID'] = explode(",", COption::GetOptionString("main", "new_user_registration_def_group", GROUP_ALL_USERS));
	    $data['PASSWORD'] = $pwd;
	    $data['CONFIRM_PASSWORD'] = $pwd;
	        
	    $u = new CUser;
	    $user['ID'] = $u->Add($data);
	    $USER->Authorize($user['ID']);
	    $_SESSION = array_merge($_SESSION,$defSes);
	} else {
        $USER->Authorize($user['ID']);
    }
}else{
	$rsUser = CUser::GetByID($USER->GetID());
	$arUser = $rsUser->Fetch();
	$user = $arUser;
}


if ($_REQUEST['func'] == 'buy_item' ) {
        // собираем старую корзину
        $tmp_basket = CSaleBasket::GetList( 
        	array(
        		"NAME" => "ASC", 
        		"ID" => "ASC"),
        	array(
        		"FUSER_ID" => CSaleBasket::GetBasketUserID(), 
        		"LID" => SITE_ID, 
        		"ORDER_ID" => "NULL"
        	), 
        	false, 
        	false, 
        	array(
        		"PRODUCT_ID", 
        		"QUANTITY"
        	)
        );
        
        while ($arItems = $tmp_basket->Fetch())
        {
            $tmp_items[] = $arItems;
        }

        CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());
        
        /*add new*/
        $item_id = (int)$_REQUEST['id'];
        Add2BasketByProductID($item_id, 1);
}

$dbBasketItems = CSaleBasket::GetList(
	array(
    	"DATE_INSERT" => "ASC",
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
    array(
    	"ID", 
    	"NAME", 
    	"CALLBACK_FUNC", 
    	"MODULE", 
    	"PRODUCT_ID", 
    	"QUANTITY", 
    	"DELAY", 
    	"CAN_BUY", 
    	"PRICE", 
    	"WEIGHT", 
    	"DETAIL_PAGE_URL", 
    	"NOTES", 
    	"CURRENCY", 
    	"VAT_RATE", 
    	"CATALOG_XML_ID", 
    	"PRODUCT_XML_ID", 
    	"SUBSCRIBE", 
    	"DISCOUNT_PRICE",
		"PRODUCT_PROVIDER_CLASS"
	)
);
while ($arItems = $dbBasketItems->GetNext())
{
	//$arItems['QUANTITY'] = $arParams['QUANTITY_FLOAT'] == 'Y' ? number_format(DoubleVal($arItems['QUANTITY']), 2, '.', '') : IntVal($arItems['QUANTITY']);

    $arItems["PROPS"] = Array();
    if(in_array("PROPS", $arParams["COLUMNS_LIST"]))
    {
    	$dbProp = CSaleBasket::GetPropsList(
    			Array("SORT" => "ASC", "ID" => "ASC"), 
    			Array(
    					"BASKET_ID" => $arItems["ID"], 
    					"!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID"
    			)
    	));
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
	//'COUNT_DISCOUNT_4_ALL_QUANTITY' => 'Y',
);

$arErrors = array();
$arResult["ITEMS"]["AnDelCanBuy"] = $arOrder['BASKET_ITEMS'];
$arResult["allSum"] = $allSum;
$arResult["allWeight"] = $allWeight;
$arResult["allSum_FORMATED"] = SaleFormatCurrency($allSum, $allCurrency);

$order_price = $arOrder['ORDER_PRICE'];
$arFields = array(
        'LID'                   => SITE_ID,
        'PERSON_TYPE_ID'        => 1,
        'STATUS_ID'             => 'N',
        'PRICE'                 => $order_price,
        'CURRENCY'              => 'RUB',
        'USER_ID'               => $user['ID'],
		/*'USER_DESCRIPTION'		=> 'Быстрый заказ'
        'PAY_SYSTEM_ID'         => 1,
        'DELIVERY_ID'           => 1,*/
);

if( $order_price > 0 ){
    $ORDER_ID = CSaleOrder::Add($arFields);
    $ORDER_ID = IntVal($ORDER_ID);
} else {
    $ORDER_ID = 0;
}

$rsOrderProp = CSaleOrderProps::GetList(
		array(), 
		array('CODE' => 'PHONE', 'PERSON_TYPE_ID' => $arFields['PERSON_TYPE_ID'])
);
if($arOrderProp = $rsOrderProp->Fetch()){
        CSaleOrderPropsValue::Add(
        	array('ORDER_ID' => $ORDER_ID, 
        	'ORDER_PROPS_ID' => $arOrderProp['ID'], 
        	'NAME' => $arOrderProp['NAME'], 
        	'VALUE' => $phone, 
        	'CODE' => $arOrderProp['CODE']
        	)
     	);
}
$rsOrderProp = CSaleOrderProps::GetList(
		array(),
		array('CODE' => 'LOCATION', 'PERSON_TYPE_ID' => $arFields['PERSON_TYPE_ID'])
);
if($arOrderProp = $rsOrderProp->Fetch()){
	CSaleOrderPropsValue::Add(
	array('ORDER_ID' => $ORDER_ID,
	'ORDER_PROPS_ID' => $arOrderProp['ID'],
	'NAME' => $arOrderProp['NAME'],
	'VALUE' => GetCurrentCityLocationID(),
	'CODE' => $arOrderProp['CODE']
	)
	);
}
if ($user['LAST_NAME'] || $user['NAME']) {
        $rsOrderProp = CSaleOrderProps::GetList(
        	array(), 
        	array('CODE' => 'CONTACT_PERSON', 'PERSON_TYPE_ID' => $arFields['PERSON_TYPE_ID'])
        );
        if($arOrderProp = $rsOrderProp->Fetch()){
                CSaleOrderPropsValue::Add(
                	array(
                		'ORDER_ID' => $ORDER_ID, 
                		'ORDER_PROPS_ID' => $arOrderProp['ID'], 
                		'NAME' => $arOrderProp['NAME'], 
                		'VALUE' => $user['NAME'] . ' ' . $user['LAST_NAME'], 
                		'CODE' => $arOrderProp['CODE']
                	)
                );
        }
}

$rsOrderProp = CSaleOrderProps::GetList(
		array(),
		array('CODE' => 'FAST_ORDER', 'PERSON_TYPE_ID' => $arFields['PERSON_TYPE_ID'])
);
if($arOrderProp = $rsOrderProp->Fetch()){
	CSaleOrderPropsValue::Add(
	array('ORDER_ID' => $ORDER_ID,
	'ORDER_PROPS_ID' => $arOrderProp['ID'],
	'NAME' => $arOrderProp['NAME'],
	'VALUE' => 'Y',
	'CODE' => $arOrderProp['CODE']
	)
	);
}

$rsOrderProp = CSaleOrderProps::GetList(
		array(),
		array('CODE' => 'FAST_ORDER_PAGE', 'PERSON_TYPE_ID' => $arFields['PERSON_TYPE_ID'])
);
if($arOrderProp = $rsOrderProp->Fetch()){
	CSaleOrderPropsValue::Add(
	array('ORDER_ID' => $ORDER_ID,
	'ORDER_PROPS_ID' => $arOrderProp['ID'],
	'NAME' => $arOrderProp['NAME'],
	'VALUE' => $page,
	'CODE' => $arOrderProp['CODE']
	)
	);
}
/*
CSaleBasket::OrderBasket($ORDER_ID, CSaleBasket::GetBasketUserID(), SITE_ID);

if( $ORDER_ID > 0 ) {
    $return['order']['id'] = $ORDER_ID;
    $return['order']['rev'] = $order_price;
    foreach ($arOrder['BASKET_ITEMS'] as $value) {
            $return['bi'][$value['PRODUCT_ID']]['name'] = $value['~NAME'];
            $return['bi'][$value['PRODUCT_ID']]['price'] = $value['PRICE'];
            $return['bi'][$value['PRODUCT_ID']]['sku'] = $value['PRODUCT_ID'];
            $return['bi'][$value['PRODUCT_ID']]['quantity'] = $value['QUANTITY'];
    }

    $return['order']['text'] = 'Заказ оформлен.<br />Номер Вашего заказа №';
} else {
    $return['order']['id'] = $ORDER_ID;
    $return['order']['rev'] = $order_price;
    $return['order']['text'] = 'Заказ не может быть оформлен. Товар под заказ.';
}
/*if( isset($login) )
    $USER->Logout();*/

// Возвращаем корзину назад
    /*
if ($_REQUEST['func'] == 'buy_item' ) {
    foreach ($tmp_items as $item) {
        Add2BasketByProductID($item['PRODUCT_ID'], $item['QUANTITY']);
    }
}

echo json_encode( $return );
*/
CSaleBasket::OrderBasket($ORDER_ID, CSaleBasket::GetBasketUserID(), SITE_ID);

if( $ORDER_ID > 0 ) {
    $return['order']['id'] = $ORDER_ID;
    $return['order']['rev'] = $order_price;
    foreach ($arOrder['BASKET_ITEMS'] as $value) {
            $return['bi'][$value['PRODUCT_ID']]['name'] = $value['~NAME'];
            $return['bi'][$value['PRODUCT_ID']]['price'] = $value['PRICE'];
            $return['bi'][$value['PRODUCT_ID']]['sku'] = $value['PRODUCT_ID'];
            $return['bi'][$value['PRODUCT_ID']]['quantity'] = $value['QUANTITY'];
    }

    $return['order']['text'] = 'Заказ оформлен.<br />Номер Вашего заказа №';
    
    ///////////
    $email = "videoohrana@9894434.ru";
    //$email = "ba_ndrey@aniart.com.ua";
    
    $body = "Заказ в 1 клик с videoohrana.ru \r\n";
    $body .= "Оформлен новый заказ №".$ORDER_ID." (через форму в 1 клик)\n";
    
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

    $subject = "Заказ в 1 клик с videoohrana.ru";
    mail($email, $subject, $body, $headers);
    ///////////
} else {
    $return['order']['id'] = $ORDER_ID;
    $return['order']['rev'] = $order_price;
    $return['order']['text'] = 'Заказ не может быть оформлен. Товар под заказ.';
}

if( isset($login) )
    $USER->Logout();

// Возвращаем корзину назад
if ($_REQUEST['func'] == 'buy_item' ) {
    foreach ($tmp_items as $item) {
        Add2BasketByProductID($item['PRODUCT_ID'], $item['QUANTITY']);
    }
}

echo json_encode( $return );
?>