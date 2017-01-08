<?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])){
	$basePriceType = CCatalogGroup::GetBaseGroup();
	$basePriceTypeName = $basePriceType["NAME"];

	$arOffersIblock = CIBlockPriceTools::GetOffersIBlock($arResult["IBLOCK_ID"]);
	$OFFERS_IBLOCK_ID = is_array($arOffersIblock)? $arOffersIblock["OFFERS_IBLOCK_ID"]: 0;
	$dbOfferProperties = CIBlock::GetProperties($OFFERS_IBLOCK_ID, Array("sort" => "asc"), Array("!XML_ID" => "CML2_LINK"));
	$arIblockOfferProps = array();
	$offerPropsExists = false;
	while($arOfferProperties = $dbOfferProperties->Fetch())
	{
		if (!in_array($arOfferProperties["CODE"],$arParams["OFFERS_PROPERTY_CODE"]))
			continue;
		$arIblockOfferProps[] = array("CODE" => $arOfferProperties["CODE"], "NAME" => $arOfferProperties["NAME"]);
		$offerPropsExists = true;
	}

	$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
	$arNotify = unserialize($notifyOption);

	$arSku = array();
	$arResult["OFFERS_CATALOG_QUANTITY"] = 0;
	foreach($arResult["OFFERS"] as $arOffer)
	{		
		$arResult["OFFERS_CATALOG_QUANTITY"]  += $arOffer["CATALOG_QUANTITY"];
        foreach($arOffer["PRICES"] as $code=>$arPrice)
        {
            if($arPrice["CAN_ACCESS"])
            {
                if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"])
                {
                    $minOfferPrice = $arPrice["DISCOUNT_VALUE"];
                    $minOfferPriceFormat = $arPrice["PRINT_DISCOUNT_VALUE"];
                }
                else
                {
                    $minOfferPrice = $arPrice["VALUE"];
                    $minOfferPriceFormat = $arPrice["PRINT_VALUE"];
                }

                if ($minItemPrice > 0 && $minOfferPrice < $minItemPrice)
                {
                    $minItemPrice = $minOfferPrice;
                    $minItemPriceFormat = $minOfferPriceFormat;
                }
                elseif ($minItemPrice == 0)
                {
                    $minItemPrice = $minOfferPrice;
                    $minItemPriceFormat = $minOfferPriceFormat;
                }
            }
        }
	}

	// check for OFFERS`s PRICE OFFERS
	$arOffers = array();
	foreach($arResult["OFFERS"] as $arOffer){
		$arOffers[$arOffer["ID"]] = $arOffer;
	}
	$arResult["OFFERS"] = $arOffers;

	foreach($arResult["OFFERS"] as $arOffer)
	{
		$arSkuTmp = array();
		$arSkuTmp["NAME"] = (isset($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"]);
		$arSkuTmp["CATALOG_QUANTITY"] = $arOffer["CATALOG_QUANTITY"];
		if ($offerPropsExists)
		{
			foreach($arIblockOfferProps as $key => $arProp){
				$arIblockOfferProps[$key]["IS_EMPTY"] = true;
				if (!array_key_exists($arProp["CODE"], $arOffer["PROPERTIES"]) || empty($arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"])){
					$arSkuTmp[] = GetMessage("EMPTY_VALUE_SKU");
					continue;
				}
				if (is_array($arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"])){
					$arSkuTmp[] = implode("/", $arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"]);
				}
				else{
					$arSkuTmp[] = $arOffer["PROPERTIES"][$arProp["CODE"]]["VALUE"];
				}
				$arIblockOfferProps[$key]["IS_EMPTY"] = false;
			}
		}
		else
		{
			if (in_array("NAME", $arParams["OFFERS_FIELD_CODE"]))
				$arSkuTmp[] = $arOffer["NAME"];
			else
				break;
		}
		$arSkuTmp["ID"] = $arOffer["ID"];
		if (is_array($arOffer["PRICES"][$basePriceTypeName]))
		{
			if ($arOffer["PRICES"][$basePriceTypeName]["DISCOUNT_VALUE"] < $arOffer["PRICES"][$basePriceTypeName]["VALUE"])
			{
				$arSkuTmp["PRICE"] = $arOffer["PRICES"][$basePriceTypeName]["PRINT_VALUE"];
				$arSkuTmp["DISCOUNT_PRICE"] = $arOffer["PRICES"][$basePriceTypeName]["PRINT_DISCOUNT_VALUE"];
			}
			else
			{
				$arSkuTmp["PRICE"] = $arOffer["PRICES"][$basePriceTypeName]["PRINT_VALUE"];
				$arSkuTmp["DISCOUNT_PRICE"] = "";
			}
		}
		if (CModule::IncludeModule('sale'))
		{
			$dbBasketItems = CSaleBasket::GetList(
				array(
					"ID" => "ASC"
				),
				array(
					"PRODUCT_ID" => $arOffer['ID'],
					"FUSER_ID" => CSaleBasket::GetBasketUserID(),
					"LID" => SITE_ID,
					"ORDER_ID" => "NULL",
				),
				false,
				false,
				array()
			);
			$arSkuTmp["CART"] = "";
			if ($arBasket = $dbBasketItems->Fetch())
			{
				if($arBasket["DELAY"] == "Y")
					$arSkuTmp["CART"] = "delay";
				elseif ($arBasket["SUBSCRIBE"] == "Y" &&  $arNotify[SITE_ID]['use'] == 'Y')
					$arSkuTmp["CART"] = "inSubscribe";
				else
					$arSkuTmp["CART"] = "inCart";
			}
		}
		$arSkuTmp["CAN_BUY"] = $arOffer["CAN_BUY"];
		$arSkuTmp["ADD_URL"] = htmlspecialcharsback($arOffer["ADD_URL"]);
		$arSkuTmp["SUBSCRIBE_URL"] = htmlspecialcharsback($arOffer["SUBSCRIBE_URL"]);
		$arSkuTmp["CATALOG_SUBSCRIBE"] = htmlspecialcharsback($arOffer["CATALOG_SUBSCRIBE"]);
		$arSkuTmp["COMPARE"] = "";
		if (isset($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$arOffer["ID"]]))
			$arSkuTmp["COMPARE"] = "inCompare";
		$arSkuTmp["COMPARE_URL"] = htmlspecialcharsback($arOffer["COMPARE_URL"]);
		$arSku[$arOffer["ID"]] = $arSkuTmp;
	}
	
    $arResult["MIN_PRODUCT_OFFER_PRICE"] = $minItemPrice;
    $arResult["MIN_PRODUCT_OFFER_PRICE_PRINT"] = $minItemPriceFormat;


	if ((!is_array($arIblockOfferProps) || empty($arIblockOfferProps)) && is_array($arSku) && !empty($arSku))
		$arIblockOfferProps[] = array("CODE" => "TITLE", "NAME" => GetMessage("CATALOG_OFFER_NAME"));
	$arResult["SKU_ELEMENTS"] = $arSku;
	$arResult["SKU_PROPERTIES"] = $arIblockOfferProps;
}

if ($arParams['USE_COMPARE'])
{
	$delimiter = strpos($arParams['COMPARE_URL'], '?') ? '&' : '?';

	//$arResult['COMPARE_URL'] = str_replace("#ACTION_CODE#", "ADD_TO_COMPARE_LIST",$arParams['COMPARE_URL']).$delimiter."id=".$arResult['ID'];

	$arResult['COMPARE_URL'] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_TO_COMPARE_LIST&id=".$arResult['ID'], array("action", "id")));
}

$db_res = CCatalogStore::GetList(array(), array("ACTIVE" => "Y"), false, false, array());
$arStores = array();
while ($res = $db_res-> GetNext()) { $arStores[] = $res;}	
$arResult["STORES_COUNT"] = count($arStores);


if ($arParams["SHOW_KIT_PARTS"]=="Y")
{
	//const TYPE_SET = 1;
	//const TYPE_GROUP = 2;
	$arSetItems = array();
	
	$arSets = CCatalogProductSet::getAllSetsByProduct($arResult["ID"], 1);
	
	if (is_array($arSets) && !empty($arSets))
	{
		foreach( $arSets as $key => $set) { foreach($set["ITEMS"] as $i=>$val) { $arSetItems[] = $val["ITEM_ID"]; } }
	}
	
	$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
	
	$arSelect = Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PREVIEW_PICTURE", "DETAIL_PICTURE");
	foreach($arResultPrices as &$value)
	{
		if ($value['CAN_VIEW'] && $value['CAN_BUY']) { $arSelect[] = $value["SELECT"]; }
	}
	if (!empty($arSetItems))
	{
		$db_res = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("ID"=>$arSetItems), false, false, $arSelect);
		while ($res = $db_res->GetNext()) { $arResult["SET_ITEMS"][] = $res; }
	}
		
	$arConvertParams = array();
	if ('Y' == $arParams['CONVERT_CURRENCY'])
	{
		if (!CModule::IncludeModule('currency'))
		{
			$arParams['CONVERT_CURRENCY'] = 'N';
			$arParams['CURRENCY_ID'] = '';
		}
		else
		{
			$arResultModules['currency'] = true;
			$arCurrencyInfo = CCurrency::GetByID($arParams['CURRENCY_ID']);
			if (!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo)))
			{
				$arParams['CONVERT_CURRENCY'] = 'N';
				$arParams['CURRENCY_ID'] = '';
			}
			else
			{
				$arParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
				$arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
			}
		}
	}
	
	$bCatalog = CModule::IncludeModule('catalog');
	
	if (is_array($arResult["SET_ITEMS"]) && !empty($arResult["SET_ITEMS"]))
	{
		foreach($arResult["SET_ITEMS"] as $key => $setItem)
		{
			if($arParams["USE_PRICE_COUNT"])
			{
				if($bCatalog)
				{
					$arResult["SET_ITEMS"][$key]["PRICE_MATRIX"] = CatalogGetPriceTableEx($arResult["SET_ITEMS"][$key]["ID"], 0, $arPriceTypeID, 'Y', $arConvertParams);
					foreach($arResult["SET_ITEMS"][$key]["PRICE_MATRIX"]["COLS"] as $keyColumn=>$arColumn)
						$arResult["SET_ITEMS"][$key]["PRICE_MATRIX"]["COLS"][$keyColumn]["NAME_LANG"] = htmlspecialcharsbx($arColumn["NAME_LANG"]);
				}
			}
			else
			{
				$arResult["SET_ITEMS"][$key]["PRICES"] = CIBlockPriceTools::GetItemPrices($arParams["IBLOCK_ID"], $arResultPrices, $arResult["SET_ITEMS"][$key], $arParams['PRICE_VAT_INCLUDE'], $arConvertParams);
				if (!empty($arResult["SET_ITEMS"][$key]["PRICES"]))
				{
					foreach ($arResult["SET_ITEMS"][$key]['PRICES'] as &$arOnePrice)
					{ if ('Y' == $arOnePrice['MIN_PRICE']) { $arResult["SET_ITEMS"][$key]['MIN_PRICE'] = $arOnePrice; break;} }
					unset($arOnePrice);
				}

			}
		}
	}
}

if (intVal($arParams["IBLOCK_STOCK_ID"])) 
{ 
	$arSelect = array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "DETAIL_PAGE_URL", "DATE_ACTIVE_TO");
    $arFilter = array("ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_STOCK_ID"], "PROPERTY_GOODS" => $arResult["ID"], ">=DATE_ACTIVE_TO" =>ConvertTimeStamp(false, "FULL"));
	$dbRes = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while($res = $dbRes->GetNext()) { $arResult["STOCK"][] = $res;}
}

if( !empty($arResult["PROPERTIES"]["SERVICES"]["VALUE"]) )
{
	$arSelect = array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "DETAIL_PAGE_URL");
	$dbRes = CIBlockElement::GetList( array(), array("ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "IBLOCK_ID" => $arResult["PROPERTIES"]["SERVICES"]["LINK_IBLOCK_ID"], "ID" => $arResult["PROPERTIES"]["SERVICES"]["VALUE"] ), false, false, $arSelect);
	while ($res = $dbRes->GetNext()) { 	
	$arResult["SERVICES"][] = $res; }
}

/*ANIART*/
/*******************************************
 *Формирую доп массив для сравнения товаров
*******************************************/


/*Узнаю ИБЛОК торг предложений по текущему*/
$arInfo = CCatalogSKU::GetInfoByProductIBlock($arResult['IBLOCK_ID']);

/* Массив элементов для сравнения */
if( !empty($arResult['PROPERTIES']['PRODUCTS_TO_COMPARE']['VALUE']) ){
	if( is_array($arResult['PROPERTIES']['PRODUCTS_TO_COMPARE']['VALUE']) )
		$arIdElements = $arResult['PROPERTIES']['PRODUCTS_TO_COMPARE']['VALUE'];
	else
		$arIdElements = array($arResult['PROPERTIES']['PRODUCTS_TO_COMPARE']['VALUE']);
} else $arIdElements = array();

/* Помещаю первым элементом текущий */
array_unshift($arIdElements, $arResult['ID']);

/* Формирую массив основных параметров и начальное условие для выборки*/
$arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", 'DETAIL_PAGE_URL', "PROPERTY_GOODS_SOLD", "IBLOCK_ID");

global $ar_structure_data;

foreach ($ar_structure_data[$arResult['SECTION']["XML_ID"]] as $prop=>$arValue) {
	/* Присутствуют ли множественные поля*/
	if( stristr($prop, 'DICTIONARY_MUL') ){
		/* @arMultiPropName вспомогательный массив */
		$arMultiPropName[] = $prop;
		$arSelect[] = 'PROPERTY_'.$prop;
	} else {
		$arSelect[] = 'PROPERTY_'.$prop;
		$arSelect[] = 'PROPERTY_'.$prop.'.NAME';
	}

	$arPropName[$prop] = $arValue[0];
}

$arSelect[] = "PROPERTY_BRAND";

/*Prices*/
$arSelect[] = "CATALOG_QUANTITY";
$arPRICES = CIBlockPriceTools::GetCatalogPrices($arResult["IBLOCK_ID"], $arParams['PRICE_CODE']);

$arConvertParams = array();
if ('Y' == $arParams['CONVERT_CURRENCY'])
{
	if (!\Bitrix\Main\Loader::includeModule('currency'))
	{
		$arParams['CONVERT_CURRENCY'] = 'N';
		$arParams['CURRENCY_ID'] = '';
	}
	else
	{
		$arResultModules['currency'] = true;
		$arCurrencyInfo = CCurrency::GetByID($arParams['CURRENCY_ID']);
		if (!empty($arCurrencyInfo) && is_array($arCurrencyInfo))
		{
			$arParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
			$arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
		}
		else
		{
			$arParams['CONVERT_CURRENCY'] = 'N';
			$arParams['CURRENCY_ID'] = '';
		}
	}
}

$arFilter = Array("IBLOCK_ID"=>IntVal($arResult["IBLOCK_ID"]), /*"ACTIVE"=>"Y",*/ "ID"=>$arIdElements);
foreach($arPRICES as $price){
	$arFilter["CATALOG_SHOP_QUANTITY_".$price["ID"]] = 1;
}
/*end Prices*/

$res = CIBlockElement::GetList(array('ID'=>'ASC'), $arFilter, false, false, $arSelect);

$count = 1;

while($ob = $res->GetNextElement())
{
	/*Получаю содержимое выборки*/
	$arFields = $ob->GetFields();

	/*Prices*/
	$arFields["PRICES"] = array();
	$arFields["PRICES"] = CIBlockPriceTools::GetItemPrices(
			$arFields["IBLOCK_ID"],
			$arPRICES,
			$arFields,
			"N",
			$arConvertParams
	);
	/*end Prices*/

	
	
	/*если было хоть одно множ свойство получаю имя данных элементов справочника*/
	if( isset($arMultiPropName) ){
		/* Прохожусь по всем множ свойствам */
		foreach ($arMultiPropName as $name) {
			/* Из полученого массива получаю все значения данного свойства */
			foreach ($arFields['PROPERTY_'.$name.'_VALUE'] as $id) {
				/* Вспомогательный массив для уже обработанных элементов */
				if( !isset($arMPropName[$id]) ){
					$arProp = CIBlockElement::GetByID($id)->GetNext();
					$arMPropName[$id] = array('ID' => $arProp['ID'], 'NAME' => $arProp['NAME']);
				}
				/*Записываю данный массив к основному элемента*/
				$arFields['PROPERTY_'.$name.'_INFO'] = $arMPropName;
			}
		}
	}

	/*записываю данный масив к общему*/
	$arResult['RES_ELEM_COMP'][$arFields['ID']] = $arFields;
	$count++;
}

/* Проверка на торг каталог(подстраховка) */
if (is_array($arInfo))
{
	/* Формирую доп. массив с торг предложениями для данного города и укладываю его в ранее сформированный */
	$rsOffers = CIBlockElement::GetList(array(),array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $arIdElements), false, false, array('ID', 'PROPERTY_CML2_LINK'));
	while ($arOffer = $rsOffers->Fetch())
	{
		$arResult['RES_ELEM_COMP'][$arOffer['PROPERTY_CML2_LINK_VALUE']]['OFFERS'] = $arOffer;
	}
}

$arResult['COUNT_ELEM_COMP'] = $count;
$arResult['PRIMARY_PROP_NAME'] = $arPropName;
$arResult['ID_ELEMENTS_COMPARE'] = $arIdElements;

//p($arResult['RES_ELEM_COMP']);
unset($arFields);
unset($arPropName);
unset($arIdElements);
unset($arOffer);

$arResult['PROPERTIES']['BRAND']['DISPLAY_VALUE'] = $arResult['DISPLAY_PROPERTIES']['BRAND']['LINK_ELEMENT_VALUE'][$arResult['PROPERTIES']['BRAND']['VALUE']]['NAME'];
GTMDataCollector('detail', $arResult);

$cp = $this->__component; // объект компонента
//p($arResult);
if (is_object($cp))
{
   // добавим в arResult компонента два поля
   $cp->arResult['PREVIEW_PICTURE'] = $arResult['PREVIEW_PICTURE'];
   $cp->arResult['DETAIL_PICTURE']  = $arResult['DETAIL_PICTURE'];
   $cp->arResult['DETAIL_PAGE_URL'] = $arResult['DETAIL_PAGE_URL'];
   $cp->arResult['PRICES'] = $arResult['PRICES'];
   $cp->arResult['PROPERTIES']['BRAND']['DISPLAY_VALUE'] = $arResult['DISPLAY_PROPERTIES']['BRAND']['LINK_ELEMENT_VALUE'][$arResult['PROPERTIES']['BRAND']['VALUE']]['NAME'];
   //array('PREVIEW_PICTURE','DETAIL_PICTURE', 'DETAIL_PAGE_URL')
   $cp->SetResultCacheKeys(array('PREVIEW_PICTURE','DETAIL_PICTURE', 'DETAIL_PAGE_URL', 'PRICES'));
}
?>