<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/WholesalerProduct.php");
CModule::IncludeModule('highloadblock');

if( !$GLOBALS['USER']->IsAuthorized() ){
	die;
}

if( !isset($_SESSION['BASKET']) ){
	$_SESSION['BASKET'] = array(
		'quantity' => 0,
		'items' => array(),
		'total_price' => 0,
		'currency' => "RUB"
	);
}

$params = new \stdClass();
$params->product_id = (int)$_REQUEST['product_id'];
$params->quantity = ($_REQUEST['quantity'] > 0)?(int)$_REQUEST['quantity']:0;
$params->method = isset($_REQUEST['method'])?$_REQUEST['method']:null;

$arRes = array('quantity'=>$_SESSION['BASKET']['quantity'], 'total_price'=>$_SESSION['BASKET']['total_price'], 'items' => $_SESSION['BASKET']['items'], 'currency' => $_SESSION['BASKET']['currency']);

if( $params->product_id <= 0 ){
	header('Content-Type: application/json');
	die( json_encode($arRes) );
}

if( $params->method == 'editQuantity' ){
	if($params->quantity <= 0){
		unset($_SESSION['BASKET']['items'][$params->product_id]);
		unset($arRes['items'][$params->product_id]);
		$arRes['quantity'] -= $_SESSION['BASKET']['ITEMS_INFO'][$params->product_id]->quantity;
		$arRes['total_price'] -= $_SESSION['BASKET']['ITEMS_INFO'][$params->product_id]->byCurrency->fullPrice;
		unset($_SESSION['BASKET']['ITEMS_INFO'][$params->product_id]);
		$_SESSION['BASKET']['quantity'] = $arRes['quantity'];
		$_SESSION['BASKET']['total_price'] = $arRes['total_price'];
		header('Content-Type: application/json');
		die( json_encode($arRes) );
	} else {
		/*$item = &$_SESSION['BASKET']['ITEMS_INFO'][$params->product_id];
		$arRes['total_price'] = $arRes['total_price'] - $item->byCurrency->fullPrice;
		$item->byCurrency->fullPrice = round($item->byCurrency->userPrice * $params->quantity, 2);
		$arRes['total_price'] = $arRes['total_price'] + $item->byCurrency->fullPrice;
		$_SESSION['BASKET']['total_price'] = $arRes['total_price'];
		$arRes['quantity'] -= $item->quantity;
		$item->quantity = $params->quantity;
		$arRes['quantity'] += $params->quantity;
		$_SESSION['BASKET']['quantity'] = $arRes['quantity'];
		$arRes['item'] = $item;*/
		$product = new WholesalerProduct($params->product_id);
		$product->productInBasket($params->quantity, $arRes['currency']);
		$_SESSION['BASKET']['ITEMS_INFO'][$params->product_id] = $product->getProductForBasket();
		$_SESSION['BASKET']['total_price'] = 0;
		$_SESSION['BASKET']['quantity'] = 0;
		foreach ($_SESSION['BASKET']['ITEMS_INFO'] as $item) {
			$_SESSION['BASKET']['total_price'] += $item->byCurrency->fullPrice;
			$_SESSION['BASKET']['quantity'] += $item->quantity;
		}
		$arRes['item'] = $product->getProductForBasket();
		$arRes['total_price'] = $_SESSION['BASKET']['total_price'];
		$arRes['quantity'] = $_SESSION['BASKET']['quantity'];
		header('Content-Type: application/json');
		die( json_encode($arRes) );
	}
}

if( $params->quantity <= 0 ){
	header('Content-Type: application/json');
	die( json_encode($arRes) );
}
/*
$arSelect = Array("*", "PROPERTY_*");
$arFilter = Array("IBLOCK_ID" => 66, "ID" => $params->product_id);
$arItem = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect)->Fetch();*/

$product = new WholesalerProduct($params->product_id);

if( $product->realPrice <= 0 ){
	header('Content-Type: application/json');
	die( json_encode($arRes) );
}

$product->productInBasket($params->quantity, $arRes['currency']);
/*$price = toInt($arItem['PROPERTY_1090']) - toInt($arItem['PROPERTY_1090']) * getUserDiscount() / 100;*/

$arRes['items'][$params->product_id] = $params->product_id;
$arRes['quantity'] += $params->quantity;
$arRes['total_price'] += $product->byCurrency->userPrice * $params->quantity;

/*$itemInfo = new \stdClass();
$itemInfo->id = $arItem['ID'];
$itemInfo->name = $arItem['NAME'];
$itemInfo->brand = $arItem['PROPERTY_1091'];
$itemInfo->price = toInt($arItem['PROPERTY_1090']);
$itemInfo->article = $arItem['PROPERTY_1087'];
$itemInfo->date_add = time();
$itemInfo->stock_quantity = toInt($arItem['PROPERTY_1088']) + toInt($arItem['PROPERTY_1089']);
$itemInfo->quantity = $params->quantity;
$itemInfo->user_price = round($price, 2);
$itemInfo->full_price = round($itemInfo->user_price * $params->quantity, 2);
*/

if( isset($_SESSION['BASKET']['ITEMS_INFO'][$params->product_id]) ){
	$_t = $_SESSION['BASKET']['ITEMS_INFO'][$params->product_id];
	$product->productInBasket((int)$_t->quantity + (int)$params->quantity, $arRes['currency']);
}

$_SESSION['BASKET']['ITEMS_INFO'][$params->product_id] = $product->getProductForBasket();

$_SESSION['BASKET'] = array_merge($_SESSION['BASKET'], $arRes);

header('Content-Type: application/json');
die( json_encode($arRes) );