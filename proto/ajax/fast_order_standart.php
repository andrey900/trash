<?php
define("NO_KEEP_STATISTIC", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("iblock")) die();
if(!CModule::IncludeModule("catalog")) die();
if(!CModule::IncludeModule("sale")) die();
if(!CModule::IncludeModule("currency")) die();

$itemId = (int)$_REQUEST['id'];
$phone = $_REQUEST['phone'];
$pagen = $_REQUEST['params']['pagen'];
$funct = 'buy_item';
$html = '';


if($pagen == "detail") {
    $page = "Детальная страница товара";
}

// user
if(!$USER->IsAuthorized()) {
	//get viewed
	
	$user = CUser::GetList(
		($by="personal_country"),
		($order="desc"),
		array('EMAIL' => $_REQUEST['email'])
	)->GetNext();
	if(!$user) {
        $pwd = CreateUserPassword($phone);
	    $data['LOGIN'] = $_REQUEST['email'];
	    $data['PERSONAL_PHONE'] = $phone;
	    $data['EMAIL'] = $_REQUEST['email'];
	    $data['ACTIVE'] = 'Y';
	    $data['GROUP_ID'] = array(2);
	    $data['PASSWORD'] = $pwd;
	    $data['CONFIRM_PASSWORD'] = $pwd;
	        
	    $u = new CUser;
	    $user['ID'] = $u->Add($data);
	    $USER->Authorize($user['ID']);
	}
} else {
	$rsUser = CUser::GetByID($USER->GetID());
	$arUser = $rsUser->Fetch();
	$user = $arUser;
}

if($funct == 'buy_item' ) {
    // собираем старую корзину
    $tmp_basket = CSaleBasket::GetList( 
        array(
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
            '*'
        )
    );

    while ($arItems = $tmp_basket->Fetch()) {
        //затащим свойства товара из корзины во времяночку
        $tmpProp = array();
        $basketProp = CSaleBasket::GetPropsList(
            array(
                "SORT" => "ASC",
                "NAME" => "ASC"
            ),
            array("BASKET_ID" => $arItems['ID'])
        );
        while ($arBasketProp = $basketProp->Fetch()) {
            $tmpProp[] = $arBasketProp;
        }
        
        $tmp_items[] = array(
            'PRODUCT_ID' => $arItems['PRODUCT_ID'],
            'QUANTITY' => $arItems['QUANTITY'],
            'PROPERTY' => $tmpProp
        );
    }
    //d($tmp_items);

    CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());
    
    //add new
    Add2BasketByProductID(
        $itemId, 
        1,
        array()
    );
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
        'USER_ID'               => $user['ID']
);

//d($arFields);

$ORDER_ID = CSaleOrder::Add($arFields);
$ORDER_ID = IntVal($ORDER_ID);

//d($ORDER_ID);

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
	'VALUE' => '',
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
if($arOrderProp = $rsOrderProp->Fetch()) {
    CSaleOrderPropsValue::Add(
	array(
            'ORDER_ID' => $ORDER_ID,
            'ORDER_PROPS_ID' => $arOrderProp['ID'],
            'NAME' => $arOrderProp['NAME'],
            'VALUE' => $page,
            'CODE' => $arOrderProp['CODE']
	)
    );
}

CSaleBasket::OrderBasket($ORDER_ID, CSaleBasket::GetBasketUserID(), SITE_ID);

// Возвращаем корзину назад
if ($funct == 'buy_item' ) {
    foreach ($tmp_items as $item) {
        
        $arAuthor = array();
        $arPublish = array();
        
        foreach($item['PROPERTY'] as $prop) {
            if($prop['CODE'] == 'RUSSIA_NAME_AUTHOR') {
                $arAuthor = array(
                    'NAME' => $prop['NAME'],
                    'CODE' => $prop['CODE'],
                    'VALUE' => $prop['VALUE']
                );
            }
            if($prop['CODE'] == 'PUBLISHING') {
                $arPublish = array(
                    'NAME' => $prop['NAME'],
                    'CODE' => $prop['CODE'],
                    'VALUE' => $prop['VALUE']
                );
            }
        }
        
        Add2BasketByProductID($item['PRODUCT_ID'], $item['QUANTITY'], array($arAuthor, $arPublish));
    }
}

/*
$return['order']['id'] = $ORDER_ID;
$return['order']['rev'] = $order_price;
foreach ($arOrder['BASKET_ITEMS'] as $value) {
        $return['bi'][$value['PRODUCT_ID']]['name'] = $value['~NAME'];
        $return['bi'][$value['PRODUCT_ID']]['price'] = $value['PRICE'];
        $return['bi'][$value['PRODUCT_ID']]['sku'] = $value['PRODUCT_ID'];
        $return['bi'][$value['PRODUCT_ID']]['quantity'] = $value['QUANTITY'];
}

$return['order']['text'] = '<p><span style="color:green">Ваш заказ #'.$ORDER_ID.' успешно отправился к нам в офис, менеджер перезвонит в течении 15 минут</span></p>';
echo json_encode( $return );*/

if(!empty($ORDER_ID)) {
    /*
    $html .= '<p><span style="color:#6EAA0F;font-size:16px;font-weight:bold;">';
    $html .= 'Ваш заказ успешно отправлен к нам в офис, менеджер перезвонит в течении 15 минут';
    $html .= '</span></p>';*/
    $html = "Y";
} else {
    /*$html .= '<p><span style="color:#990000">';
    $html .= 'Возникла непредвиденная ошибка. Обратитесь к администратору.';
    $html .= '</span></p>';*/
    $html = 'N';
}

//$html .= '<input id="close_fast_ord" class="sub-bt" value="Закрыть" type="button" style="margin: 18px 0px 0 154px!important;">';

echo $html;
?>