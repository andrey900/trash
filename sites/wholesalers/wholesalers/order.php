<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/WholesalerProduct.php");
CModule::IncludeModule('highloadblock');


function toInt($str){
	$str = preg_replace("/[^\d]+/", "", $str);
	return (int)$str;
}

if( !$GLOBALS['USER']->IsAuthorized() ){
	die;
}

if( !isset($_SESSION['BASKET']) )
	$_SESSION['BASKET'] = array();

$arRes = array();

if( !isset($_REQUEST['method']) || $_REQUEST['method'] != "add" ){
	$arRes['items'] = array_values($_SESSION['BASKET']['items']);
	$arRes['itemsInfo'] = $_SESSION['BASKET']['ITEMS_INFO'];
	$arRes['total_price'] = $_SESSION['BASKET']['total_price'];//$sum;

	header('Content-Type: application/json');
	die( json_encode($arRes) );
}


$el = new CIBlockElement;

$arItems = array();
$summ_r = 0;
$summ = 0;
$strItems = "";
$strSmallItems = "";
$_SESSION['BASKET']['total_price'] = 0;
foreach ($_SESSION['BASKET']['ITEMS_INFO'] as $item) {
	$arItems[] = array("VALUE"=>$item->quantity, "DESCRIPTION"=>$item->name);
	$summ_r += $item->realPrice * $item->quantity;
	$summ += $item->byCurrency->realPrice * $item->quantity;
	$_SESSION['BASKET']['total_price'] += $item->byCurrency->fullPrice;
	$strItems .= "&nbsp;&nbsp;&nbsp;Артикул: ".$item->article.", <br>&nbsp;&nbsp;&nbsp;Наименование: ".$item->name.", <br>&nbsp;&nbsp;&nbsp;Количество: ".$item->quantity.", <br>&nbsp;&nbsp;&nbsp;Общая цена: ".$item->byCurrency->fullPrice.";&nbsp;&nbsp;&nbsp;<br><br>";
	$strSmallItems .= "&nbsp;&nbsp;&nbsp;Артикул: ".$item->article.", <br>&nbsp;&nbsp;&nbsp;Наименование: ".$item->name.", <br>&nbsp;&nbsp;&nbsp;Количество: ".$item->quantity.";&nbsp;&nbsp;&nbsp;<br><br>";
}

$prop = array();
// $prop['DISCOUNT'] = getUserDiscount();
$prop["ITEMS"] = array_keys($_SESSION['BASKET']['ITEMS_INFO']);
$prop["ITEMS_INFO"] = $arItems;
$prop["TOTAL_PRICE"] = $_SESSION['BASKET']['total_price'];
$prop["FULL_PRICE_RUB"] = $summ_r;
$prop["FULL_PRICE"] = $summ;
$prop["CURRENCY"] = $_SESSION['BASKET']['currency'];

$arLoadProductArray = Array(
  "MODIFIED_BY"    => $GLOBALS['USER']->GetID(),
  "IBLOCK_SECTION_ID" => false,
  "IBLOCK_ID"      => 67,
  "NAME"           => "Заказ от ".$GLOBALS['USER']->GetFullName()." - ".date("d-m-Y H:i"),
  "ACTIVE"         => "Y",
  "PREVIEW_TEXT"   => json_encode($_SESSION['BASKET'], JSON_PRETTY_PRINT),
  "PROPERTY_VALUES"=> $prop,
);

$userInfo = CElectrodomTools::_GetInfoUser($GLOBALS['USER']->GetID());
$arEventFields = array(
    "user_name"       => $GLOBALS['USER']->GetFullName(),
    "user_email"      => $userInfo['EMAIL'],
    "user_phone"      => $userInfo['PERSONAL_PHONE'],
    "order_list"      => "<br>".$strItems,
    "order_small_list"=> "<br>".$strSmallItems,
    "total_quantity"  => $_SESSION['BASKET']['quantity'],
    "total_price"     => $_SESSION['BASKET']['total_price'],
    "currency"        => $_SESSION['BASKET']['currency'],
);

// p($prop);die;

if($PRODUCT_ID = $el->Add($arLoadProductArray)){
	// CEvent::Send("DEKOMO_ORDER", SITE_ID, $arEventFields, "N", 51);
	CEvent::Send("DEKOMO_ORDER", SITE_ID, $arEventFields, "N");
	$arRes['msg'] = 'Спасибо. ваш заказ успешно оформлен.';
	$arRes['status'] = 'success';
	$c = $_SESSION['BASKET']['currency'];
	unset($_SESSION['BASKET']);
	$_SESSION['BASKET'] = array('currency'=>$c, 'items' => array(), 'quantity' => 0, 'total_price' => 0);
} else{
	$arRes['msg'] = $el->LAST_ERROR;
	$arRes['status'] = 'error';
}

header('Content-Type: application/json');
die( json_encode($arRes) );
