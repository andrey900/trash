<?php
AddEventHandler("iblock", "OnAfterIBlockElementAdd", 'DoIBlockAfterSave');
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", 'DoIBlockAfterSave');

AddEventHandler("catalog", "OnPriceAdd", "DoIBlockAfterSave");
AddEventHandler("catalog", "OnPriceUpdate", "DoIBlockAfterSave");

function DoIBlockAfterSave($arg1, $arg2 = false)
{
	$ELEMENT_ID = false;
	$IBLOCK_ID = false;
	$OFFERS_IBLOCK_ID = false;
	$OFFERS_PROPERTY_ID = false;
	$CURRENT_IBLOCK_ID = false;
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
						$CURRENT_IBLOCK_ID = $arPriceElement['IBLOCK_ID'];
					}
				}
				//or iblock which has offers
				elseif($arCatalog["OFFERS_IBLOCK_ID"] > 0)
				{
					$ELEMENT_ID = $arPriceElement["ID"];
					$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
					$OFFERS_IBLOCK_ID = $arCatalog["OFFERS_IBLOCK_ID"];
					$OFFERS_PROPERTY_ID = $arCatalog["OFFERS_PROPERTY_ID"];
					$CURRENT_IBLOCK_ID = $arPriceElement['IBLOCK_ID'];
				}
				//or it's regular catalog
				else
				{
					$ELEMENT_ID = $arPriceElement["ID"];
					$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
					$OFFERS_IBLOCK_ID = false;
					$OFFERS_PROPERTY_ID = false;
					$CURRENT_IBLOCK_ID = $arPriceElement['IBLOCK_ID'];
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
			$CURRENT_IBLOCK_ID =  $arg1["IBLOCK_ID"];
		}
	}

	if($ELEMENT_ID)
	{
		static $arPropCache = array();
		if(!array_key_exists($IBLOCK_ID, $arPropCache))
		{
			//Check for BASE_PRICE property
			$rsProperty = CIBlockProperty::GetByID("BASE_PRICE", $IBLOCK_ID);
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
			
			//Get catalog groups
			$CatalogGroups = $Prices = array();
			$rsCatalogGroups = CCatalogGroup::GetList();
			while($arCatalogGroup = $rsCatalogGroups->Fetch()){
				$CatalogGroups[$arCatalogGroup['ID']] = $arCatalogGroup['NAME'];
			}
			if(!empty($CatalogGroups)){
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
					
					$PriceName				= $CatalogGroups[$arPrice['CATALOG_GROUP_ID']];
					$Prices[$PriceName][]	= $arPrice['PRICE'];
				}
				if(!empty($Prices)){
					$Properties = array();
					//Calc avarage price
					foreach($Prices as $PriceName => $OffersPrices){
						$Price = array_sum($OffersPrices) / count($OffersPrices);
						$Properties[$PriceName] = round($Price, 2);
					}
					//Update product properties
					CIBlockElement::SetPropertyValuesEx(
						$ELEMENT_ID,
						$IBLOCK_ID,
						$Properties
					);
				}
			}
		}
	}
}
?>