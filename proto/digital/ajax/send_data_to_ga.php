<?//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock")) { echo "failure"; return;}?>
<?
/*
CModule::IncludeModule("sale");
CModule::IncludeModule("iblock");
$arOrder = array();
$arBasketItems = array();
$arOrder = CSaleOrder::GetByID($_REQUEST['ORDER_ID']);
$dbBasketItems = CSaleBasket::GetList( array( "NAME" => "ASC", "ID" => "ASC" ), array("ORDER_ID" =>$_REQUEST["ORDER_ID"] ), false, false, array() );
while ( $arItems = $dbBasketItems->Fetch() )
{
	$arProductInfo = array();
	$arProductInfo = array(
			'PRODUCT_ID' => $arItems['PRODUCT_ID'],
			'NAME' => $arItems['NAME'],
			'PRICE' => $arItems['PRICE'],
			'QUANTITY' => $arItems['QUANTITY']
	);
	$db_section = CIBlockElement::GetElementGroups($arItems["PRODUCT_ID"], true);
	while($arSection = $db_section->Fetch())
	{
		$arProductInfo['CATEGORY'] = $arSection['NAME'];
	}
	$arBasketItems[] = $arProductInfo;
}
$arResult['ORDER'] = $arOrder;
$arResult['BASKET'] = $arBasketItems;
echo json_encode($arResult);
*/
if( (int)$_REQUEST['ORDER_ID']<=0 ){
	$arRes['data'] = array();
	$arRes['order'] = array();
	echo json_encode($arRes);
	die;
}

$arResult['ORDER'] = CSaleOrder::GetByID((int)$_REQUEST['ORDER_ID']);

$orderGTM = new stdClass();
$orderGTM->id =  $arResult['ORDER']['ID'];
$orderGTM->tax = $arResult['ORDER']['TAX_VALUE'];
$orderGTM->action = 'purchase';
$orderGTM->revenue = $arResult['ORDER']['PRICE'];
$orderGTM->shipping = $arResult['ORDER']['PRICE_DELIVERY'];
$orderGTM->affiliation = 'DigitalVideo';

$arBasketItems = array();

$dbBasketItems = CSaleBasket::GetList(
        array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => $arResult['ORDER']['ID']
            ),
        false,
        false,
        array("ID", "CALLBACK_FUNC", "MODULE", 
              "PRODUCT_ID", "QUANTITY", "DELAY", 
              "CAN_BUY", "PRICE", "WEIGHT", "NAME")
    );
$arBasket = array();
while ($arItems = $dbBasketItems->Fetch())
{
	$arProduct = CIBlockExt::GetElementInfo($arItems['PRODUCT_ID']);
	$arSection = CIBlockSection::GetById($arProduct['IBLOCK_SECTION_ID'])->GetNext();

	$arBasket[$arItems['ID']]['ID'] = $arItems['PRODUCT_ID'];
	$arBasket[$arItems['ID']]['NAME'] = $arItems['NAME'];
	$arBasket[$arItems['ID']]['PRICES']['BASE']['VALUE'] = $arItems['PRICE'];
	$arBasket[$arItems['ID']]['DISPLAY_PROPERTIES']['BRAND']['LINK_ELEMENT_VALUE'][]['NAME'] = $arProduct['PROPERTY_BRAND_NAME'];
	$arBasket[$arItems['ID']]['QUANTITY'] = $arItems['QUANTITY'];
	$arBasket[$arItems['ID']]['SECTION_NAME'] = $arSection['NAME'];
}

GTMDataCollector('impressions', $arBasket, 'order');

$arRes['data'] = $GLOBALS['GTM_DATA'];
$arRes['order'] = $orderGTM;

echo json_encode($arRes);
die;
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>


