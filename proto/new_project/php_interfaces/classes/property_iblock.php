<?
/**
 * Класс обрабатывающий свойства инфоблоков и формирующий список свойст для компонента 
 * custom.fiter.oop
 * 
 * @author Alexander Kuprin
 *
 */
class CCustomFilterProperty
{
	var $arProperties;
	var $catalogIblockID;
	var $offersIblockID;
	var $arTemplates = array(
		"BASE_PRICE" => "price",
		"SIZE" => "property_sku",
		"COLOR" => "property_sku",
		"AVAILABILITY" => "availability",
	);
	var $arSort = array(
			"BASE_PRICE" => -100,
	);
	
	function  __construct($catalogIblockID, $offersIblockID) {
		if (!empty($catalogIblockID)) 
		{
			$this->catalogIblockID = $catalogIblockID; 
			$arIblockID[] = $catalogIblockID;
		}
		if (!empty($offersIblockID))
		{
			$this->offersIblockID = $offersIblockID;
			$arIblockID[] = $offersIblockID;
		}
		
		$dbList = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y"));
		while ($dbItem = $dbList->GetNext())
		{
			if (in_array($dbItem["IBLOCK_ID"], $arIblockID))
			{
				$this->arProperties[$dbItem["ID"]] = $dbItem;
			}
		}
		
	}
	
	function  GetPropertyList($sectionID) 
	{
		$result = array();
		
		$arPropertiesInSection = array();
		
		if (!empty($this->catalogIblockID)) 
			$arPropertiesInSection = CIBlockSectionPropertyLink::GetArray($this->catalogIblockID,$sectionID);
		
		if (!empty($this->offersIblockID))
			$arPropertiesInSection = array_merge($arPropertiesInSection, CIBlockSectionPropertyLink::GetArray($this->offersIblockID,$sectionID));

		foreach ($arPropertiesInSection as $propertyShort) 
		{
			if (isset($this->arProperties[$propertyShort["PROPERTY_ID"]]) && $propertyShort["SMART_FILTER"] == "Y" && $this->arProperties[$propertyShort["PROPERTY_ID"]]["CODE"] != "DUMMY")
			{
				$propertyID = $propertyShort["PROPERTY_ID"];
				$propertyDetail = $this->arProperties[$propertyID];

				if ($propertyDetail["IBLOCK_ID"] == $this->catalogIblockID)
				{
					$suffix = "";
					$result["IBLOCK_PROPERTIES"][] = $propertyID;
					
				}
				else
				{
					$suffix = "OFFER_";
					$result["OFFERS_PROPERTIES"][] = $propertyID;
				}
				
				$result["PROPERTY_".$suffix.$propertyID."_TITLE"] = $propertyDetail["NAME"];
				$result["PROPERTY_".$suffix.$propertyID."_TYPE"] = ".default";
				$result["PROPERTY_".$suffix.$propertyID."_SORT"] = (isset($this->arSort[$propertyDetail["CODE"]])?$this->arSort[$propertyDetail["CODE"]]:$propertyDetail["SORT"]);
				$result["PROPERTY_".$suffix.$propertyID."_TEMPLATE"] = (isset($this->arTemplates[$propertyDetail["CODE"]])?$this->arTemplates[$propertyDetail["CODE"]]:"default");
				$result["PROPERTY_".$suffix.$propertyID."_MULTIPLE"] = ($propertyDetail["PROPERTY_TYPE"] == "N" ? "N" : "Y");
				$result["PROPERTY_".$suffix.$propertyID."_SHOWCOUNT"] = ($propertyDetail["PROPERTY_TYPE"] == "N" ? "N" : "Y");
				
				if (!empty($propertyDetail["LINK_IBLOCK_ID"]))
					$result["PROPERTY_".$suffix.$propertyID."_LINKIBLOCKID"] = $propertyDetail["LINK_IBLOCK_ID"];
			}	
		}
		/*$result["IBLOCK_PROPERTIES"][] = "CATALOG_PRICE_1";
		$result["PROPERTY_CATALOG_PRICE_1_TITLE"] = "Цена";
		$result["PROPERTY_CATALOG_PRICE_1_TYPE"] = ".default";
		$result["PROPERTY_CATALOG_PRICE_1_SORT"] = 10;
		$result["PROPERTY_CATALOG_PRICE_1_TEMPLATE"] = "price";
		$result["PROPERTY_CATALOG_PRICE_1_MULTIPLE"] = "N";
		$result["PROPERTY_CATALOG_PRICE_1_SHOWCOUNT"] = "N";*/
		//p($result);
	return $result;
	}
}
?>