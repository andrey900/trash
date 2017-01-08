<?
class CIBlockExt 
{
	const TAG_CACHE = "iblock_ext";
	const TTL_CACHE = 7200;

	/**
	 * Возвращаем список разделов инфоблока. По умолчанию возвращает 25 разделов. Если $limit = false, то возвращаем
	 * весь список. Список проиндексирован по ID разделов
	 *
	 * @param array $arFilter
	 * @param array $arSelect
	 * @param array $arSort
	 * @param number/boolean $limit
	 * @param string $useCache
	 * @return array
	 */
	function GetListSections($arFilter, $arSelect = array(), $arSort = array("sort" => "asc", "name" => "asc"), $limit = 25, $useCache = true, $useManagedCache = false, $useSEO = true)
	{
		global $CACHE_MANAGER;
	
		$arListSections = array();
	
		if (empty($arFilter)) return;
	
		if (!is_array($arFilter)) $arFilter = array("IBLOCK_ID" => $arFilter);
	
		$arSelect = array_merge(array("IBLOCK_ID", "ID", "NAME", "CODE"), $arSelect);
	
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			if (!$limit)
				$arNavParams = $limit;
			else
				$arNavParams = array("nPageSize" => $limit);
				
			if (!$useCache)
			{
				$dbList = CIBlockSection::GetList($arSort, $arFilter, false, $arSelect, $arNavParams);
	
				while ($dbItem = $dbList->GetNext())
				{
					self::RemoveTildaFields($dbItem);
					
					if ($useSEO)
					{
						$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($dbItem["IBLOCK_ID"], $dbItem["ID"]);
						$ipropSEO = $ipropValues->getValues();
						$dbItem = array_merge($dbItem, $ipropSEO);
						unset($ipropValues);
					}
						
					$arListSections[$dbItem["ID"]] = $dbItem;
				}
			}
			else
			{
				$cacheTag = self::TAG_CACHE;
	
				$obCache = new CPHPCache();
				if ($obCache->InitCache(self::TTL_CACHE, serialize(array(__FUNCTION__, $arFilter, $arSelect, $arNavParams)), $cacheTag))
				{
					$arListSections = $obCache->GetVars();
				}
				elseif ($obCache->StartDataCache())
				{
	
					$dbList = CIBlockSection::GetList($arSort, $arFilter, false, $arSelect, $arNavParams);
	
					while ($dbItem = $dbList->GetNext())
					{
						self::RemoveTildaFields($dbItem);
						$arListSections[$dbItem["ID"]] = $dbItem;
	
						if(defined("BX_COMP_MANAGED_CACHE") && $useManagedCache)
						{
							$CACHE_MANAGER->StartTagCache($cacheTag);
							$CACHE_MANAGER->RegisterTag("iblock_id = ".$dbItem["IBLOCK_ID"]." section_id = ".$dbItem["ID"]);
							$CACHE_MANAGER->EndTagCache();
						}
					}
					$obCache->EndDataCache($arListSections);
				}
			}
		}
	
		return $arListSections;
	}
	
	/**
	 * Возвращаем список элементов инфоблока. По умолчанию возвращает 25 элементов. Если $limit = false, то возвращаем 
	 * весь список. Список элементов проиндексирован по ID элементов
	 * 
	 * @param array $arFilter
	 * @param array $arSelect
	 * @param array $arSort
	 * @param number/boolean $limit
	 * @param string $useCache
	 * @return array
	 */
	function GetListElements($arFilter, $arSelect = array(), $arSort = array("sort" => "asc", "name" => "asc"), $limit = false, $useCache = true, $useSEO = true)
	{
		global $CACHE_MANAGER;
		
		$arListElements = array();
		
		if (empty($arFilter)) return;
		
		if (!is_array($arFilter)) $arFilter = array("IBLOCK_ID" => $arFilter);

		$arSelect = array_merge(array("IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL", "CODE", "DETAIL_PICTURE"), $arSelect);
		
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			if (!$limit) 
				$arNavParams = $limit;
			else
				$arNavParams = array("nPageSize" => $limit);
			
			if (!$useCache)
			{
				$dbList = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
				
				while ($dbItem = $dbList->GetNext())
				{
					self::RemoveTildaFields($dbItem);

					$arProperties = self::GetListPropertiesValueElement($dbItem["IBLOCK_ID"], $dbItem["ID"]);
					
					$dbItem = array_merge($dbItem, $arProperties);
						
					if ($useSEO)
					{
						$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($dbItem["IBLOCK_ID"], $dbItem["ID"]);
						$ipropSEO = $ipropValues->getValues();
						$dbItem = array_merge($dbItem, $ipropSEO);
						unset($ipropValues);
					}
						
					$arListElements[$dbItem["ID"]] = $dbItem;
				}
			}
			else
			{
				$cacheTag = self::TAG_CACHE;

				$obCache = new CPHPCache();
				if ($obCache->InitCache(self::TTL_CACHE, serialize(array(__FUNCTION__, $arFilter, $arSelect, $arNavParams)), $cacheTag))
				{
					$arListElements = $obCache->GetVars();
				}
				elseif ($obCache->StartDataCache())
				{
						
					$dbList = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
						
					while ($dbItem = $dbList->GetNext())
					{
						self::RemoveTildaFields($dbItem);
						
						$arProperties = self::GetListPropertiesValueElement($dbItem["IBLOCK_ID"], $dbItem["ID"]);
						
						$dbItem = array_merge($dbItem, $arProperties);
						
						$arListElements[$dbItem["ID"]] = $dbItem;
		
						if(defined("BX_COMP_MANAGED_CACHE") && $useManagedCache)
						{
							$CACHE_MANAGER->StartTagCache($cacheTag);
							$CACHE_MANAGER->RegisterTag("iblock_id = ".$dbItem["IBLOCK_ID"]." element_id = ".$dbItem["ID"]);
							$CACHE_MANAGER->EndTagCache();
						}
					}
					$obCache->EndDataCache($arListElements);
				}
			}
		}
		
		return $arListElements;
	}

	/**
	 * Проверяет существует ли элемент
	 *
	 * @param array $arFilter
	 * @return array
	 */
	function ElementExist($arFilter = array())
	{
		$result = false;
	
		if (!empty($arFilter))
		{
			$dbResult = CIBlockElement::GetList(array(), $arFilter);
			$result = $dbItem = $dbResult->GetNext();
		}
	
		return $result;
	}
	
	/**
	 * Возвращает информацию элемента (поля + свойства) 
	 *
	 * @param integer $iblockID
	 * @return array
	 */
	function GetElementInfo($arFilter = array(), $useCache = true, $useSEO = true)
	{
		if (empty($arFilter)) return;
		
		if (!is_array($arFilter)) $arFilter = array("ID" => $arFilter);
		 
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			if (!$useCache)
			{
				$dbList = CIBlockElement::GetList(array(), $arFilter);
				
				if ($dbItem = $dbList->GetNext()) {
					self::RemoveTildaFields($dbItem);
					
					if ($useSEO)
					{
						$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($dbItem["IBLOCK_ID"], $dbItem["ID"]);
						$ipropSEO = $ipropValues->getValues();
						$dbItem = array_merge($dbItem, $ipropSEO);
						unset($ipropValues);
					}
						
					$arElementInfo = $dbItem;
				}
			}
			else 
			{
				$cacheTag = self::TAG_CACHE;
				
				$obCache = new CPHPCache();
				if ($obCache->InitCache(self::TTL_CACHE, serialize(array(__FUNCTION__, $arFilter)), $cacheTag))
				{
					$arElementInfo = $obCache->GetVars();
				}
				elseif ($obCache->StartDataCache())
				{
					$dbList = CIBlockElement::GetList(array(), $arFilter);
				
					if ($dbItem = $dbList->GetNext()) {
						self::RemoveTildaFields($dbItem);
						
						if ($useSEO)
						{
							$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($dbItem["IBLOCK_ID"], $dbItem["ID"]);
							$ipropSEO = $ipropValues->getValues();
							$dbItem = array_merge($dbItem, $ipropSEO);
							unset($ipropValues);
						}
						
						$arElementInfo = $dbItem;
					}
				
					$arProperties = self::GetListPropertiesValueElement($dbItem["IBLOCK_ID"], $dbItem["ID"]);
				
					$arElementInfo = array_merge($arElementInfo, $arProperties);
				
					if(defined("BX_COMP_MANAGED_CACHE") && $useManagedCache)
					{
						global $CACHE_MANAGER;
						$CACHE_MANAGER->StartTagCache($cacheTag);
						$CACHE_MANAGER->RegisterTag("iblock_id = ".$dbItem["IBLOCK_ID"]." element_id = ".$dbItem["ID"]);
						$CACHE_MANAGER->EndTagCache();
					}
					$obCache->EndDataCache($arElementInfo);
				}
			}
		}
		
		return $arElementInfo;
	}

	/**
	 * Возвращает поля раздела
	 *
	 * @param integer $sectionID
	 * @return array
	 */
	function GetSectionInfo($arFilter = array(), $useCache = true, $useSEO = true)
	{
		if (empty($arFilter)) return;
		
		if (!is_array($arFilter)) $arFilter = array("ID" => $arFilter);
		 
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			if (!$useCache)
			{
				$dbList = CIBlockSection::GetList(array(), $arFilter);
				
				if ($dbItem = $dbList->GetNext()) {
					self::RemoveTildaFields($dbItem);
					$arSectiontInfo = $dbItem;
				}
			}
			else 
			{
				$cacheTag = self::TAG_CACHE;
				
				$obCache = new CPHPCache();
				if ($obCache->InitCache(self::TTL_CACHE, serialize(array(__FUNCTION__, $arFilter)), $cacheTag))
				{
					$arSectionInfo = $obCache->GetVars();
				}
				elseif ($obCache->StartDataCache())
				{
					$dbList = CIBlockSection::GetList(array(), $arFilter);
				
					if ($dbItem = $dbList->GetNext()) {
						self::RemoveTildaFields($dbItem);
						
						if ($useSEO)
						{
							$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($dbItem["IBLOCK_ID"], $dbItem["ID"]);
							$ipropSEO = $ipropValues->getValues();
							$dbItem = array_merge($dbItem, $ipropSEO);
							unset($ipropValues);
						}
						
						$arSectionInfo = $dbItem;
					}
				
					if(defined("BX_COMP_MANAGED_CACHE") && $useManagedCache)
					{
						global $CACHE_MANAGER;
						$CACHE_MANAGER->StartTagCache($cacheTag);
						$CACHE_MANAGER->RegisterTag("iblock_id = ".$dbItem["IBLOCK_ID"]." section_id = ".$dbItem["ID"]);
						$CACHE_MANAGER->EndTagCache();
					}
					$obCache->EndDataCache($arSectionInfo);
				}
			}
		}
		
		return $arSectionInfo;
	}
		
	/**
	 * Возвращает информацию об инфоблоке(ах). Если результат выборки из БД один, то возвращается
	 * массив полей. В противном случае возвразается список массивов
	 *
	 * @param integer $iblockID
	 * @return array
	 */
	function GetIBlockInfo($arFilter = array())
	{
		if (empty($arFilter)) return;
		
		if (!is_array($arFilter)) $arFilter= array("ID" => $arFilter);
		
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			global $APPLICATION, $CACHE_MANAGER;
				
			$cacheTag = self::TAG_CACHE;
		
			$obCache = new CPHPCache();
			if ($obCache->InitCache(self::TTL_CACHE, serialize(array(__FUNCTION__, $arFilter)), $cacheTag))
			{
				$arIBlockInfo = $obCache->GetVars();
			}
			elseif ($obCache->StartDataCache())
			{
				$dbList = CIBlock::GetList(array("SORT" => "ASC", "NAME" => "ASC"), $arFilter);
				
				while ($dbItem = $dbList->GetNext()) 
				{
					self::RemoveTildaFields($dbItem);
		
					$arIBlockInfo[] = $dbItem;
					
					if(defined("BX_COMP_MANAGED_CACHE") && $useManagedCache)
					{
						$CACHE_MANAGER->StartTagCache($cacheTag);
						$CACHE_MANAGER->RegisterTag("iblock_id = ".$dbItem["ID"]);
						$CACHE_MANAGER->EndTagCache();
					}
				}

				if (count($arIBlockInfo) == 1)
					$arIBlockInfo = array_shift($arIBlockInfo);
				
				$obCache->EndDataCache($arIBlockInfo);
			}
		}
		
		return $arIBlockInfo;
	}
	
	/**
	 * Формируем список свойст элементов инфоблока
	 * 
	 * @param integer $iblockID
	 * @return array
	 */
	function GetListPropertiesElement($iblockID)
	{
		if (empty($iblockID)) return;
		
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			global $APPLICATION, $CACHE_MANAGER;
		
			$cacheTag = self::TAG_CACHE;
		
			$arFilter = array("IBLOCK_ID" => $iblockID);
			
			$obCache = new CPHPCache();
			if ($obCache->InitCache(self::TTL_CACHE, serialize(array(__FUNCTION__, $arFilter)), $cacheTag))
			{
				$arProperties = $obCache->GetVars();
			}
			elseif ($obCache->StartDataCache())
			{
				$dbList = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), $arFilter);
				while ($dbItem = $dbList->GetNext()) {
					self::RemoveTildaFields($dbItem);
					
					$arProperties[$dbItem["ID"]] = $dbItem;
					
					if(defined("BX_COMP_MANAGED_CACHE") && $useManagedCache)
					{
						$CACHE_MANAGER->StartTagCache($cacheTag);
						$CACHE_MANAGER->RegisterTag("iblock_id = ".$iblockID);
						$CACHE_MANAGER->EndTagCache();
					}
				}
				
				$obCache->EndDataCache($arProperties);
			}
		}
		
		return $arProperties;
	}
	
	/**
	 * Формируем список значений свойст элемента
	 *
	 * @param integer $iblockID
	 * @return array
	 */
	function GetListPropertiesValueElement($iblockID, $elementID, $arLinkFields = array("NAME", "CODE"))
	{
		$arProperties = self::GetListPropertiesElement($iblockID);
		
		$arFilter = array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"ID" => $elementID
		);
		
		$arSelect = array("ID", "NAME", "IBLOCK_ID");
		foreach ($arProperties as $arProperty) {
			
			if ($arProperty["PROPERTY_TYPE"] == "E")
			{
				foreach ($arLinkFields as $field) $arSelect[] = "PROPERTY_".$arProperty["CODE"].".".$field;
			}
			$arSelect[] = "PROPERTY_".$arProperty["CODE"];
		}

		$dbList = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

		$arValues = array();
		if ($dbItem = $dbList->Fetch()) {
			foreach ($dbItem as $key => $value)
			{
				if (strpos($key, "ROPERTY") == 1) $arValues[$key] = $value;
			} 
		}
		
		return $arValues;
	}

	/**
	 * Формируем список свойст секций инфоблока
	 *
	 * @param integer $iblockID
	 * @return array
	 */
	function GetListPropertiesSection($iblockID)
	{
		$arProperties = array();
		$arFilter = array("ENTITY_ID" => "IBLOCK_".$iblockID."_SECTION");
		$dbList = CUserTypeEntity::GetList(array("sort"=>"asc", "name"=>"asc"), $arFilter);
		while ($dbItem = $dbList->GetNext()) $arProperties[] = $dbItem;
		return $arProperties;
	}
	/**
	
	 * Формируем список значений свойст секции
	 *
	 * @param integer $iblockID
	 * @return array
	 */
	function GetListPropertiesValueSection($iblockID, $sectionID)
	{
		$arProperties = self::GetListPropertiesSection($iblockID);
	
		$arFilter = array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"ID" => $sectionID
		);
	
		$arSelect = array("ID", "NAME", "IBLOCK_ID");
		foreach ($arProperties as $arProperty) $arSelect[] = $arProperty["FIELD_NAME"];
	
		$dbList = CIBlockSection::GetList(array(), $arFilter, false, $arSelect);
	
		$arValues = array();
		if ($dbItem = $dbList->Fetch()) {
			foreach ($dbItem as $key => $value)
			{
				if (strpos($key, "F_") == 1) $arValues[$key] = $value;
			}
		}
	
		return $arValues;
	}

	/**
	 * Функция формирует хлебные крошки для элемента
	 *
	 * @param integer $elementID
	 */
	function SetBreadcrumbElement($elementID, $nameElement = false)
	{
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			global $APPLICATION;
			
			$cacheTag = self::TAG_CACHE;
				
			$obCache = new CPHPCache();
			if ($obCache->InitCache(self::TTL_CACHE, serialize(array(__FUNCTION__, $elementID)), $cacheTag))
			{
				$arBreadcrumbs = $obCache->GetVars();
			}
			elseif ($obCache->StartDataCache())
			{
				$dbList = CIBlockElement::GetByID($elementID);
					
				if ($dbItem = $dbList->GetNext())
				{
					if (!$nameElement) $nameElement = $dbItem["NAME"];
						
					$iblockInfo = self::GetIBlockInfo($dbItem["IBLOCK_ID"]);

					$firstItem = true;
			
					$dbList = CIBlockSection::GetNavChain($dbItem["$IBLOCK_ID"], $dbItem["IBLOCK_SECTION_ID"]);
						
					while ($dbItem = $dbList->GetNext())
					{
						if ($firstItem)
							$arBreadcrumbs[] = array(
									"NAME" => $iblockInfo["NAME"],
									"LINK" => $dbItem["LIST_PAGE_URL"]
							);
								
						$arBreadcrumbs[] = array(
								"NAME" => $dbItem["NAME"],
								"LINK" => $dbItem["SECTION_PAGE_URL"]
						);
						
						$firstItem = false;
					}
					
					$arBreadcrumbs[] = array(
							"NAME" => $nameElement,
							"LINK" => ""
					);
				}
			
				if(defined("BX_COMP_MANAGED_CACHE") && $useManagedCache)
				{
					global $CACHE_MANAGER;
					$CACHE_MANAGER->StartTagCache($cacheTag);
					$CACHE_MANAGER->RegisterTag("iblock_id = ".$dbItem["IBLOCK_ID"]." element_id = ".$elementID);
					$CACHE_MANAGER->EndTagCache();
				}
				$obCache->EndDataCache($arBreadcrumbs);
			}
			
			foreach ($arBreadcrumbs as $arItemBreadcrumbs)
				$APPLICATION->AddChainItem($arItemBreadcrumbs["NAME"],$arItemBreadcrumbs["LINK"]);
		}
	}

	/**
	 * Функция формирует хлебные крошки для элемента
	 *
	 * @param integer $elementID
	 */
	function SetBreadcrumbSection($iblockID, $sectionID, $nameSection = false)
	{
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			global $APPLICATION;
				
			$cacheTag = self::TAG_CACHE;
	
			$obCache = new CPHPCache();
			if ($obCache->InitCache(self::TTL_CACHE, serialize(array(__FUNCTION__, $elementID)), $cacheTag))
			{
				$arBreadcrumbs = $obCache->GetVars();
			}
			elseif ($obCache->StartDataCache())
			{
				$iblockInfo = self::GetIBlockInfo($iblockID);

				if (empty($sectionID))
				{
					$arBreadcrumbs[] = array(
							"NAME" => $iblockInfo["NAME"],
							"LINK" => ""
					);
				}
				else 
				{
					$dbList = CIBlockSection::GetByID($sectionID);
						
					if ($dbItem = $dbList->GetNext())
					{
						if (!$nameSection) $nameSection = $dbItem["NAME"];
					
						$arBreadcrumbs[] = array(
								"NAME" => $iblockInfo["NAME"],
								"LINK" => $dbItem["LIST_PAGE_URL"]
						);
					
						$dbList = CIBlockSection::GetNavChain($dbItem["$IBLOCK_ID"], $dbItem["IBLOCK_SECTION_ID"]);
							
						while ($dbItem = $dbList->GetNext())
						{
							$arBreadcrumbs[] = array(
									"NAME" => $dbItem["NAME"],
									"LINK" => $dbItem["SECTION_PAGE_URL"]
							);
						}
					
						$arBreadcrumbs[] = array(
								"NAME" => $nameSection,
								"LINK" => ""
						);
					}
				}
					
				if(defined("BX_COMP_MANAGED_CACHE") && $useManagedCache)
				{
					global $CACHE_MANAGER;
					$CACHE_MANAGER->StartTagCache($cacheTag);
					$CACHE_MANAGER->RegisterTag("iblock_id = ".$dbItem["IBLOCK_ID"]." section_id = ".$sectionID);
					$CACHE_MANAGER->EndTagCache();
				}
				$obCache->EndDataCache($arBreadcrumbs);
			}
				
			foreach ($arBreadcrumbs as $arItemBreadcrumbs)
				$APPLICATION->AddChainItem($arItemBreadcrumbs["NAME"],$arItemBreadcrumbs["LINK"]);
		}
	}
	
	/**
	 * Извлекаем из инфоблока-справочника список вида [ID] => array(NAME, PREVIEW_PICTURE) 
	 *
	 * @param integer $iblockID
	 * @return array
	 */
	function GetDictionary($iblockID)
	{
		$result = array();
	
		$arSort = array("sort" => "asc", "name" => "asc");
		$arFilter = array("IBLOCK_ID" => $iblockID, "ACTIVE" => "Y"); 
		$arSelect = array("ID", "NAME", "PREVIEW_PICTURE");
		
		$dbResult = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
		while($dbItem = $dbResult->GetNext()) 
			$result[$dbItem["ID"]] = array(
				"ID" => $dbItem["ID"],
				"NAME" => $dbItem["NAME"],
				"PREVIEW_PICTURE" => (empty($dbItem["PREVIEW_PICTURE"])?"":CFile::GetPath($dbItem["PREVIEW_PICTURE"])),
			);
	
		return $result;
	}

	/**
	 * Удаляем поля начинающиеся с ~ и другой хлам
	 * 
	 * @param array $dbItem
	 */
	private function RemoveTildaFields(&$dbItem)
	{
		unset($dbItem["SEARCHABLE_CONTENT"]);
		
		foreach ($dbItem as $key => $value)
		if (preg_match("/^~/i", $key)) unset($dbItem[$key]);
	}
} 
