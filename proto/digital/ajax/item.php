<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock")) { echo "failure"; return;}


//session_start();
//session_name('PHPSESSID');

if( !empty( $_REQUEST["add_item"] ) )
{
	if( $_REQUEST["add_item"] == "Y" )
	{
		$dbBasketItems = CSaleBasket::GetList(
			array( "NAME" => "ASC", "ID" => "ASC" ),
			array( "PRODUCT_ID" => $_REQUEST["item"], "FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL" ),
			false, false, array("ID", "DELAY")
		)->Fetch();
		
		if( !empty( $dbBasketItems ) && $dbBasketItems["DELAY"] == "Y" ) { $arFields = array("DELAY" => "N", "SUBSCRIBE" => "N"); CSaleBasket::Update($dbBasketItems["ID"], $arFields); } 
		else { Add2BasketByProductID( $_REQUEST["item"], $_REQUEST["quantity"] ); }
	}
	else
	{
		
	}
}
if( !empty( $_REQUEST["subscribe_item"] ) )
{
	if( $_REQUEST["subscribe_item"] == "Y" )
	{
		$dbBasketItems = CSaleBasket::GetList
		(
			array( "NAME" => "ASC", "ID" => "ASC"),
			array( "PRODUCT_ID" => $_REQUEST["item"], "FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
			false, false, array("ID", "PRODUCT_ID", "SUBSCRIBE", "CAN_BUY")
		)->Fetch();
		
		if( !empty( $dbBasketItems ) && $dbBasketItems["SUBSCRIBE"] == "N" )
		{
			$arFields = array("SUBSCRIBE" => "Y", "CAN_BUY" => "N", "DELAY" => "N"); 
			CSaleBasket::Update($dbBasketItems["ID"], $arFields); 
		}
		elseif( !empty( $dbBasketItems ) && $dbBasketItems["SUBSCRIBE"] == "Y" )
		{	
			CSaleBasket::Delete($dbBasketItems["ID"]); 
		}
		else
		{
			$arRewriteFields = array("SUBSCRIBE" => "Y", "CAN_BUY" => "N", "DELAY" => "N");	
			Add2BasketByProductID(intVal($_REQUEST["item"]), 1, $arRewriteFields, array());
		}
	}
}
elseif( !empty( $_REQUEST["wish_item"] ) )
{ 
	if( $_REQUEST["wish_item"] == "Y" )
	{
		$dbBasketItems = CSaleBasket::GetList
		(
			array( "NAME" => "ASC", "ID" => "ASC"),
			array( "PRODUCT_ID" => $_REQUEST["item"], "FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL", "CAN_BUY" => "Y", "SUBSCRIBE" => "N"),
			false, false, array("ID", "PRODUCT_ID", "DELAY")
		)->Fetch();
		if( !empty( $dbBasketItems ) && $dbBasketItems["DELAY"] == "N" )
		{
			$arFields = array("DELAY" => "Y", "SUBSCRIBE" => "N"); 
			CSaleBasket::Update($dbBasketItems["ID"], $arFields); 
		}
		elseif( !empty( $dbBasketItems ) && $dbBasketItems["DELAY"] == "Y" )
		{	
			CSaleBasket::Delete($dbBasketItems["ID"]); 
		}
		else
		{
			$id = Add2BasketByProductID($_REQUEST["item"], $_REQUEST["quantity"]);
			$arFields = array("DELAY" => "Y", "SUBSCRIBE" => "N");		
			CSaleBasket::Update($id, $arFields);
		}
	}
}
elseif( !empty( $_REQUEST["compare_item"] ) )
{
	$iblock_id = $_REQUEST["iblock_id"];
	if( !empty( $_SESSION["CATALOG_COMPARE_LIST"] ) && !empty($_SESSION["CATALOG_COMPARE_LIST"][$iblock_id]) && array_key_exists($_REQUEST["item"], $_SESSION["CATALOG_COMPARE_LIST"][$iblock_id]["ITEMS"]) )
	{ unset( $_SESSION["CATALOG_COMPARE_LIST"][$iblock_id]["ITEMS"][$_REQUEST["item"]] ); }
	else { $_SESSION["CATALOG_COMPARE_LIST"][$iblock_id]["ITEMS"][$_REQUEST["item"]] = CIBlockElement::GetByID($_REQUEST["item"])->Fetch(); }
}
elseif ( !empty( $_REQUEST["delete_item"] ) )
{
	$dbBasketItems = CSaleBasket::GetList
	(
		array( "NAME" => "ASC", "ID" => "ASC"),
		array( "PRODUCT_ID" => $_REQUEST["item"], "FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL" ),
		false, false, array("ID", "DELAY")
	)->Fetch();
	
	if( !empty( $dbBasketItems ) ){	CSaleBasket::Delete($dbBasketItems["ID"]); }
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>