<?
class CCustomFilterProperty
{
	var $iblockID;
	var $arProperties;
	var $arStructureData;
	
	function  __construct($iblockID) {
		$this->iblockID = $iblockID;
		$this->arProperties = array();
		
		$dbList = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$this->iblockID));
		while ($dbItem = $dbList->GetNext())
		{				
			$this->arProperties[$dbItem["CODE"]] = $dbItem;
		}
		
		include($_SERVER["DOCUMENT_ROOT"]."/work-scripts/StructureData.php");
		$this->arStructureData = $ar_structure_data;
	}
	
	function  GetPropertyList($sectionXmlId) 
	{
		$result = array();
		if (isset($this->arStructureData[$sectionXmlId])) 
		{
			$propertiesSection = $this->arStructureData[$sectionXmlId];
			$count = 0;
			foreach ($propertiesSection as $key => $property) {
				if (gettype($key) == "integer" && $key == 0) continue; // название группы свойств для раздела пропускаем
				
				// обрабатываем свойство
				$count++;
				$result["IBLOCK_PROPERTIES"][] = $this->arProperties[$key]["ID"];
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_TITLE"] = $property[0]; // индекс 0 - имя
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_TYPE"] = ".default";
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_SORT"] = $count;
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_TEMPLATE"] = (empty($property[3])?"default":$property[3]);  // индекс 3 - имя шаблона дял секции фильтра
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_MULTIPLE"] = (($key == "MINIMUM_PRICE")?"N":"Y");
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_SHOWCOUNT"] = (($key == "MINIMUM_PRICE")?"N":"Y");
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_LINKIBLOCKID"] = $property[4]; // индекс 4 - ID инфоблока для справочника
				if ($property[1]) {
					$result["PROPERTY_".$this->arProperties[$key]["ID"]."_SECTIONCODE"] = $property[1]; // XML_ID родительской секции если она находится в общем инфоблоке
				}
			}
			
			global $general_property_filter;
			foreach ($general_property_filter as $key => $propertyInfo) 
			{
				$result["IBLOCK_PROPERTIES"][] = $this->arProperties[$key]["ID"];
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_TITLE"] = $propertyInfo["TITLE"];
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_TYPE"] = ".default";
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_SORT"] = $propertyInfo["SORT"];
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_TEMPLATE"] = $propertyInfo["TEMPLATE"];
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_MULTIPLE"] = "Y";
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_SHOWCOUNT"] = "Y";
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_LINKIBLOCKID"] = $propertyInfo["LINK_IBLOCK_ID"];
			}
		}

//p($result);
		return $result;
	}
	
	function  GetPropertyByID($sectionXmlId, $propertyID) 
	{
		$result = array();
		if (isset($this->arStructureData[$sectionXmlId])) 
		{
			$propertiesSection = $this->arStructureData[$sectionXmlId];
			$count = 0;
			foreach ($propertiesSection as $key => $property) {
				if ((gettype($key) == "integer" && $key == 0) || $this->arProperties[$key]["ID"] != $propertyID) continue; // название группы свойств для раздела пропускаем
				
				// обрабатываем свойство
				$count++;
				$result["IBLOCK_PROPERTIES"][] = $this->arProperties[$key]["ID"];
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_TITLE"] = $property[0]; // индекс 0 - имя
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_TYPE"] = ".default";
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_SORT"] = $count;
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_TEMPLATE"] = (empty($property[3])?"default":$property[3]);  // индекс 3 - имя шаблона дял секции фильтра
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_MULTIPLE"] = (($key == "MINIMUM_PRICE")?"N":"Y");
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_SHOWCOUNT"] = (($key == "MINIMUM_PRICE")?"N":"Y");
				$result["PROPERTY_".$this->arProperties[$key]["ID"]."_LINKIBLOCKID"] = $property[4]; // индекс 4 - ID инфоблока для справочника
				if ($property[1]) {
					$result["PROPERTY_".$this->arProperties[$key]["ID"]."_SECTIONCODE"] = $property[1]; // XML_ID родительской секции если она находится в общем инфоблоке
				}
			}
		}

		return $result;
	}
	
	

}
?>