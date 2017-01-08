<?
/**
 * Класс расширяющий и упрощающий работу с каталогом
 * @author ak
 *
 */
class CCatalogExt 
{

	/**
	 * Метод возвращает списков товаров в корзине или в заказе
	 */
	function GetListItemsBasket($orderID = NULL)
	{
		$result = array();
	
		if (CModule::IncludeModule("sale"))
		{
			$arBasketItems = array();
				
			$arFilter = array(
				"LID" => SITE_ID,
				"ORDER_ID" => $orderID
			);
			
			if (empty($orderID))
				$arFilter["FUSER_ID"] = CSaleBasket::GetBasketUserID();
			
			$dbBasketItems = CSaleBasket::GetList(
					array(
							"NAME" => "ASC",
							"ID" => "ASC"
					),
					$arFilter,
					false,
					false,
					array("ID", "NAME", "PRODUCT_ID", "QUANTITY", "DELAY",
							"CAN_BUY", "PRICE", "CURRENCY")
			);
			while ($arBasketItem = $dbBasketItems->Fetch())
			{
				$dbList = CSaleBasket::GetPropsList(
						array(
								"SORT" => "ASC",
								"NAME" => "ASC"
						),
						array("BASKET_ID" => $arBasketItem["ID"])
				);
				$arProperty = array();
				
				while ($dbItem = $dbList->Fetch())
				{
					if (!empty($arProperty) && $dbItem["CODE"] == $arProperty["CODE"] && $dbItem["VALUE"] == $arProperty["VALUE"])
						$found = true;
				
					$arProperty[$dbItem["CODE"]] = $dbItem;
				}
				
				$result[] = array_merge(
					$arBasketItem,
					array("PROPERTIES" => $arProperty)
				);
			}
		}
	
		return $result;
	}
	
	/**
	 * Метод рассчитывает общую сумму товаров в корзине для текущего пользователя. Цены конветрируются в валюту по умолчанию 
	 */
	function GetTotalSumBasket() 
	{
		$result = 0;
		
		if (CModule::IncludeModule("sale"))
		{
			$arBasketItems = array();
			
			$dbBasketItems = CSaleBasket::GetList(
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
					array("ID", "PRODUCT_ID", "QUANTITY", "DELAY",
							"CAN_BUY", "PRICE", "CURRENCY")
			);
			while ($arItems = $dbBasketItems->Fetch())
			{
				$result += CCurrencyRates::ConvertCurrency($arItems["PRICE"], $arItems["CURRENCY"], CCurrency::GetBaseCurrency()) * $arItems["QUANTITY"];
			}
		}
		
		return $result;
	}
	
	/**
	 * ID типов цен, которые являются базовыми. Речь идёт о том, что есть рознична цена в UAH и EUR, но в 1С такие цены хранятся отдельно.
	 * @return array:number
	 */
	function GetBasePricesID() { return array( PRICE_BASE_ID );	}

	/**
	 * Возвращает ID приоритетной базовой цены
	 * @return number
	 */
	function GetBasePriceID() { return PRICE_BASE_ID;	}
	
	/**
	 * Возвращаем список базовых цен
	 * 
	 * @param unknown $productID
	 * @return multitype:unknown
	 */
	function GetBasePrices($productID)
	{
		$result = array();
		
		if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog"))
		{
			$dbList = CPrice::GetList(array(), array("PRODUCT_ID" => $productID));
			
			while ($dbItem = $dbList->Fetch())
			{
				foreach ($dbItem as $arPrice) 
				{
					if (in_array($dbItem["CATALOG_GROUP_ID"], self::GetBasePricesID()))
					{
						$result[$dbItem["CATALOG_GROUP_ID"]] = $dbItem;
					}
				}
			}
		}
		
		return  $result;
	}
	
	/**
	 * Возвращаем основные данные по товару и формируем список цен, являющихся базовыми
	 * 
	 * @param unknown $productID
	 * @return Ambigous <multitype:, unknown>
	 */
	function GetProduct($productID, $arSelectAdd = array())
	{
		$result = array();
		
		if (CModule::IncludeModule("iblock"))
		{
			$arFilter = array("ID" => $productID);
			$arSelect = array("ID", "IBLOCK_ID", "NAME", "CODE", "DETAIL_PAGE_URL");

			$dbList = CIBlockElement::GetList(array(), $arFilter, false, false, array_merge($arSelect, $arSelectAdd));

			if ($dbItem = $dbList->GetNext())
			{
				$result = $dbItem;
				$result["BASE_PRICES"] = self::GetBasePrices($productID);
				
				foreach ($result["BASE_PRICES"] as $arPrice) 
				{
					$price = CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], CCurrency::GetBaseCurrency());
					if (empty($result["MIN_PRICE"]) || $price < $result["MIN_PRICE"]) $result["MIN_PRICE"] = $price;    
				}
			}
		}
		return $result;
	}
}
?>