<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/vars.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/utils.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/misc.php");
CModule::IncludeModule("iblock");
// подключаем класс обрабатывающий свойства инфоблока
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/classes/property_iblock.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/uf_property/uf_iblock_prop_section.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/classes/gift_action.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/work-scripts/memcache/cacheenginememcache.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/classes/statictics.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/classes/resize_image.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/classes/iblock_ext.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/classes/catalog_ext.php");

//autoload for custom classes
\Bitrix\Main\Loader::registerAutoLoadClasses(null, array(
    'CAniartNewPost' => '/bitrix/php_interface/include/aniart/classes/CAniartNewPost.php',
    'CAniartTools' => '/bitrix/php_interface/include/aniart/classes/CAniartTools.php',
    'NewPost' => '/bitrix/php_interface/include/aniart/classes/DeliveryNewPost.php',
	'dBug' => '/bitrix/php_interface/include/aniart/classes/dBug.php'
));

global $CML2_CURRENCY;
$CML2_CURRENCY["грн"] = "UAH";

//пересчет цены доставки Новой почты
AddEventHandler("sale", "OnSaleComponentOrderOneStepProcess", 'orderOneStepDelivery');

function orderOneStepDelivery(&$arResult) {
    if(!empty($arResult['DELIVERY'])) {
        foreach($arResult['DELIVERY'] as $val) {
            if($val['CHECKED'] == 'Y') {
                if($val['ID'] == SELF_DELIVERY_ID) {
                    foreach($arResult['ORDER_PROP']['USER_PROPS_Y'] as $arUserProp) {
                        if($arUserProp['CODE'] == 'LOCATION') {
                            foreach($arUserProp['VARIANTS'] as $arCity) {
                                if($arCity['SELECTED'] == 'Y') {

                                    //считаем стоимость доставки
                                    $deliveryPrice = null;
                                    foreach($arResult['BASKET_ITEMS'] as $dprice) {
                                        if(
                                                !empty($dprice['WEIGHT']) || 
                                                !empty($dprice['DIMENSIONS']['HEIGHT']) || 
                                                !empty($dprice['DIMENSIONS']['WIDTH']) || 
                                                !empty($dprice['DIMENSIONS']['LENGTH'])
                                        ) {
                                            $deliveryPrice += NewPost::price(
                                                $arCity['CITY_NAME'], 
                                                $dprice['WEIGHT'] / 1000, //в кг
                                                $dprice['PRICE'], 
                                                date('d.m.Y'), 
                                                $dprice['DIMENSIONS']['HEIGHT'] / 10, //в сантиметрах
                                                $dprice['DIMENSIONS']['WIDTH'] / 10,
                                                $dprice['DIMENSIONS']['LENGTH'] / 10
                                            )->cost;
                                        } else {
                                            $deliveryPrice += 30;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $arResult['DELIVERY'][$val['ID']]['PRICE'] = $deliveryPrice;
                    $arResult['DELIVERY'][$val['ID']]['PRICE_FORMATED'] = SaleFormatCurrency($deliveryPrice, $arResult["BASE_LANG_CURRENCY"]);
                    $arResult['DELIVERY_PRICE'] = $deliveryPrice;
                    $arResult['DELIVERY_PRICE_FORMATED'] = SaleFormatCurrency($deliveryPrice, $arResult["BASE_LANG_CURRENCY"]);
                    $arResult["ORDER_TOTAL_PRICE"] = $arResult["ORDER_PRICE"] + $arResult['DELIVERY_PRICE'];
                    $arResult["ORDER_TOTAL_PRICE_FORMATED"] = SaleFormatCurrency($arResult["ORDER_TOTAL_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
                }
            }
        }
    } 
    return $arResult;
}


/*AddEventHandler("iblock", "OnBeforeIBlockElementDelete", array("CCustomHookEvent", "OnBeforeIBlockElementDeleteHandler"));

AddEventHandler("catalog", "OnProductAdd", array("CCustomHookEvent", "OnProductModifyHandler"));
AddEventHandler("catalog", "OnProductUpdate", array("CCustomHookEvent", "OnProductModifyHandler"));

class CCustomHookEvent {

	function OnProductModifyHandler($elementID, $arFields)
	{
		$elemenetInfo = GetElementByID($elementID);
		// Обрабатываем инфоблок товара
		if ($elemenetInfo["IBLOCK_ID"] == SHARE_CATALOG_IBLOCK_ID) {
			$productQuantity = GetQuantityProduct(SHARE_SKU_IBLOCK_ID, $elementID);
			CIBlockElement::SetPropertyValues($arFields["ID"], SHARE_CATALOG_IBLOCK_ID, $productQuantity, "TOTAL_QUANTITY");
		}
		// Обрабатываем инфоблок торговых предложений
		if ($elemenetInfo["IBLOCK_ID"] == SHARE_SKU_IBLOCK_ID) {
			// Обновляем данные об остатках на складе
			$productID = GetProductIDBySkuID(SHARE_SKU_IBLOCK_ID, $elementID);
			$productQuantity = GetQuantityProduct(SHARE_SKU_IBLOCK_ID, $productID);
			$offersQuantity = GetQuantitySKU(SHARE_SKU_IBLOCK_ID, $elementID);
			CIBlockElement::SetPropertyValues($productID, SHARE_CATALOG_IBLOCK_ID, $productQuantity, "TOTAL_QUANTITY");
			CIBlockElement::SetPropertyValues($elementID, SHARE_SKU_IBLOCK_ID, $offersQuantity, "QUANTITY");
		}
	}
	
	**
	 * Объединяем обработчик добавления и изменения элемента инфоблока
	 *
	 * @param unknown $arFields
	 *
	function OnBeforeIBlockElementDeleteHandler($elementID) {
		$elemenetInfo = GetElementByID($elementID);
		// Обрабатываем инфоблок торговых предложений
		if ($elemenetInfo["IBLOCK_ID"] == SHARE_SKU_IBLOCK_ID) {
				
			// Обновляем данные об остатках на складе
			$productID = GetProductIDBySkuID(SHARE_SKU_IBLOCK_ID, $elementID);
			// почему такой кривой вызов функции, читайте в описании функции
			$offersQuantity = GetQuantitySKU(SHARE_SKU_IBLOCK_ID,$elementID);
			$productQuantity = GetQuantityProduct(SHARE_SKU_IBLOCK_ID, $productID) - $offersQuantity;
			CIBlockElement::SetPropertyValues($productID, SHARE_CATALOG_IBLOCK_ID, $productQuantity, "TOTAL_QUANTITY");
		}
	}
}*/

AddEventHandler("catalog", "OnPriceAdd", "DoIBlockAfterSaveEl");
AddEventHandler("catalog", "OnPriceUpdate", "DoIBlockAfterSaveEl");

AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "DoIBlockAfterSaveEl");
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "DoIBlockAfterSaveEl");

function DoIBlockAfterSaveEl($arg1, $arg2 = false)
{
	
	$ELEMENT_ID = false;
	$IBLOCK_ID = false;
	$OFFERS_IBLOCK_ID = false;
	$OFFERS_PROPERTY_ID = false;
	if (CModule::IncludeModule('currency'))
		$strDefaultCurrency = CCurrency::GetBaseCurrency();

	//Check for catalog event
	if(is_array($arg2) && $arg2["PRODUCT_ID"] > 0)
	{
		//Get iblock element
		$rsPriceElement = CIBlockElement::GetList(
				array(),
				array(
						"ID" => $arg2["PRODUCT_ID"],
				),
				false,
				false,
				array("ID", "IBLOCK_ID")
		);
		if($arPriceElement = $rsPriceElement->Fetch())
		{
			$arCatalog = CCatalog::GetByID($arPriceElement["IBLOCK_ID"]);
			if(is_array($arCatalog))
			{
				//Check if it is offers iblock
				if($arCatalog["OFFERS"] == "Y")
				{
					//Find product element
					$rsElement = CIBlockElement::GetProperty(
							$arPriceElement["IBLOCK_ID"],
							$arPriceElement["ID"],
							"sort",
							"asc",
							array("ID" => $arCatalog["SKU_PROPERTY_ID"])
					);
					$arElement = $rsElement->Fetch();
					if($arElement && $arElement["VALUE"] > 0)
					{
						$ELEMENT_ID = $arElement["VALUE"];
						$IBLOCK_ID = $arCatalog["PRODUCT_IBLOCK_ID"];
						$OFFERS_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
						$OFFERS_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
					}
				}
				//or iblock which has offers
				elseif($arCatalog["OFFERS_IBLOCK_ID"] > 0)
				{
					$ELEMENT_ID = $arPriceElement["ID"];
					$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
					$OFFERS_IBLOCK_ID = $arCatalog["OFFERS_IBLOCK_ID"];
					$OFFERS_PROPERTY_ID = $arCatalog["OFFERS_PROPERTY_ID"];
				}
				//or it's regular catalog
				else
				{
					$ELEMENT_ID = $arPriceElement["ID"];
					$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
					$OFFERS_IBLOCK_ID = false;
					$OFFERS_PROPERTY_ID = false;
				}
			}
		}
	}
	//Check for iblock event
	elseif(is_array($arg1) && $arg1["ID"] > 0 && $arg1["IBLOCK_ID"] > 0)
	{
		//Check if iblock has offers
		$arOffers = CIBlockPriceTools::GetOffersIBlock($arg1["IBLOCK_ID"]);
		if(is_array($arOffers))
		{
			$ELEMENT_ID = $arg1["ID"];
			$IBLOCK_ID = $arg1["IBLOCK_ID"];
			$OFFERS_IBLOCK_ID = $arOffers["OFFERS_IBLOCK_ID"];
			$OFFERS_PROPERTY_ID = $arOffers["OFFERS_PROPERTY_ID"];
		}
	}

	if($ELEMENT_ID)
	{
		static $arPropCache = array();
		if(!array_key_exists($IBLOCK_ID, $arPropCache))
		{
			//Check for MINIMAL_PRICE property
			$rsProperty = CIBlockProperty::GetByID("MINIMUM_PRICE", $IBLOCK_ID);
			$arProperty = $rsProperty->Fetch();
			if($arProperty)
				$arPropCache[$IBLOCK_ID] = $arProperty["ID"];
			else
				$arPropCache[$IBLOCK_ID] = false;
		}

		if($arPropCache[$IBLOCK_ID])
		{
			//Compose elements filter
			if($OFFERS_IBLOCK_ID)
			{
				$rsOffers = CIBlockElement::GetList(
						array(),
						array(
								"IBLOCK_ID" => $OFFERS_IBLOCK_ID,
								"PROPERTY_".$OFFERS_PROPERTY_ID => $ELEMENT_ID,
						),
						false,
						false,
						array("ID")
				);
				while($arOffer = $rsOffers->Fetch())
					$arProductID[] = $arOffer["ID"];

				if (!is_array($arProductID))
					$arProductID = array($ELEMENT_ID);
			}
			else
				$arProductID = array($ELEMENT_ID);

			$minPrice = false;
			$maxPrice = false;
			//Get prices
			$rsPrices = CPrice::GetList(
					array(),
					array(
							"PRODUCT_ID" => $arProductID,
					)
			);
			while($arPrice = $rsPrices->Fetch())
			{
				if (CModule::IncludeModule('currency') && $strDefaultCurrency != $arPrice['CURRENCY'])
					$arPrice["PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $strDefaultCurrency);

				$PRICE = $arPrice["PRICE"];

				if($minPrice === false || $minPrice > $PRICE)
					$minPrice = $PRICE;

				if($maxPrice === false || $maxPrice < $PRICE)
					$maxPrice = $PRICE;
			}

			$is_price = array("VALUE"=>false);
			if($minPrice>0)
				$is_price = array("VALUE"=>"144");

			CIBlockElement::SetPropertyValuesEx(
			$ELEMENT_ID,
			$IBLOCK_ID,
				array(
					"MINIMUM_PRICE" => $minPrice>0?$minPrice:false,
					"MAXIMUM_PRICE" => $maxPrice>0?$minPrice:false,
					"IS_PRICE" => $is_price,
				)
			);
		}
	}
}

AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "FromGiftsIblock");
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "FromGiftsIblock");

//для упрощения заполняется свойство "SECTIONS" для элементов которые попали под акцию
function FromGiftsIblock(&$arFields)
{
	if( $arFields['IBLOCK_ID']!=GIFTS_IBLOCK_ID )
		return true;

	$arItems = $arSectionId = array();
	// собираем воедино все ид акц товаров
	foreach( $arFields['PROPERTY_VALUES'][PROPERTY_SECTIONS_FOR_GIFTS] as $itemId ){
		if( !empty($itemId['VALUE']) )
			$arItems[$itemId['VALUE']] = $itemId['VALUE'];
	}

	if( empty($arItems) ) $arItems = array(0);
	// получаем информацию о них
	$arRes = CAniartTools::_GetInfoElements($arItems, array('ID', 'IBLOCK_SECTION_ID', 'NAME', 'CODE'));

	// собираем воедино все ид секций товаров
	if( !is_array($arRes) ) $arRes = array();
	foreach ($arRes as $arElems) {
		if( !empty($arElems['IBLOCK_SECTION_ID']) )
				$arSectionId[$arElems['IBLOCK_SECTION_ID']] = $arElems['IBLOCK_SECTION_ID'];
	}

	CIBlockElement::SetPropertyValuesEx(
			$arFields['ID'],
			$arFields['IBLOCK_ID'],
				array(
					"SECTIONS" => $arSectionId,
				)
			);

	return true;
}

AddEventHandler('main', 'OnEpilog', 'onEpilogMeta', 1);
function onEpilogMeta(){
    global $APPLICATION;
    $arPageProp = $APPLICATION->GetPagePropertyList();
    $arSite = CSite::GetByID(SITE_ID)->Fetch();
    $APPLICATION->AddHeadString('<meta property="og:site_name" content="'.htmlspecialchars($arSite['NAME']).'">',$bUnique=true);
    $arMetaPropName = array('og:title','og:description', 'og:site_name', 'og:image','og:type','og:url');
    foreach ($arMetaPropName as $name){
	$key = mb_strtoupper($name, 'UTF-8');
        if (isset($arPageProp[$key])){
            $APPLICATION->AddHeadString('<meta property="'.$name.'" content="'.htmlspecialchars($arPageProp[$key]).'">',$bUnique=true);
        }
    }
}

?>