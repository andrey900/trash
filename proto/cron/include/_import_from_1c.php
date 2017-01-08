<?
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// Классы
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class CCustomImport1C {
	/**
	 * Метод обрабатывает offers.xml и извлекает из него данные о курсе валют и обновляет эти значения в 1С Битрикс
	 * 
	 * @param string $fileName
	 */
	function SetCurrencyRate($fileName) {
		$xml = simplexml_load_file($fileName);
		
		foreach($xml->ПакетПредложений->ТипыЦен->ТипЦены as $arPrice) {
			if (!empty($arPrice->Валюта) && !empty($arPrice->Курс))
			{
				Add2Log("[php] Обновляем курс валют. Валюта: ".$arPrice->Валюта.". Курс: ".$arPrice->Курс);
				CCurrency::Update($arPrice->Валюта, array("CURRENCY" => $arPrice->Валюта, "AMOUNT" => $arPrice->Курс));
			}
		}
	}
	
	/**
	 * Метод проверяет атрибут СодержитТолькоИзменения в теге Каталог и возвращает его значение
	 * 
	 * @param string $fileName -- имя файла
	 * @param boolean $offers -- торговые предложения
	 * 
	 */
	function GetModeExchange($fileName, $offers = false) {
		$xml = simplexml_load_file($fileName);

		$result = true;
		
		if ($offers)
		{
			foreach($xml->ПакетПредложений->attributes() as $name => $value) {
				echo $name.'='.$value."\n";
				if ($name == "СодержитТолькоИзменения")
				{
					$result = $value;
					break;
				}
			}
		}
		else
		{
			foreach($xml->Каталог->attributes() as $name => $value) {
				echo $name.'='.$value."\n";
				if ($name == "СодержитТолькоИзменения")
				{
					$result = $value;
					break;
				}
			}
		}				
		return $result;
	}
	
	/**
	 * Метод импортирует указанный XML-файл, используя кастомизированный компонент импорта
	 * 
	 * @param string $fileName -- имя файла
	 * @param boolean $offers -- торговые предложения
	 */
	function Execute($fileName, $offers = false) {
		$_GET["mode"] = "import";
		$_GET["filename"] = $fileName;
	
		if (offers) self::SetCurrencyRate($fileName);
		
		$isFullExchange = self::GetModeExchange($fileName, $offers);

		Add2Log("[php] Импортируем файл ".$fileName);
		Add2Log("[php] Выгрузка ".(($isFullExchange)?"полная":"частичная").".");

		// запуск загрузки
		global $APPLICATION, $USER;
		
		unset($_SESSION);
		$USER->Logout();
		$USER->Authorize(ID_USER_CRON);
		unset($_SESSION["BX_CML2_IMPORT"]);
			
		$APPLICATION->IncludeComponent("aniart:catalog.import.1c", "", Array(
				"IBLOCK_TYPE" => COption::GetOptionString("catalog", "1C_IBLOCK_TYPE", "-"),
				"SITE_LIST" => array(COption::GetOptionString("catalog", "1C_SITE_LIST", "-")),
				"INTERVAL" => 0,
				"GROUP_PERMISSIONS" => explode(",", COption::GetOptionString("catalog", "1C_GROUP_PERMISSIONS", "")),
				"GENERATE_PREVIEW" => COption::GetOptionString("catalog", "1C_GENERATE_PREVIEW", "Y"),
				"PREVIEW_WIDTH" => COption::GetOptionString("catalog", "1C_PREVIEW_WIDTH", "300"),
				"PREVIEW_HEIGHT" => COption::GetOptionString("catalog", "1C_PREVIEW_HEIGHT", "300"),
				"DETAIL_RESIZE" => COption::GetOptionString("catalog", "1C_DETAIL_RESIZE", "Y"),
				"DETAIL_WIDTH" => COption::GetOptionString("catalog", "1C_DETAIL_WIDTH", "800"),
				"DETAIL_HEIGHT" => COption::GetOptionString("catalog", "1C_DETAIL_HEIGHT", "800"),
				"ELEMENT_ACTION" => ($isFullExchange)?"A":"N", // A -- деактивировать, N -- ничего не делать
				"SECTION_ACTION" => ($isFullExchange)?"A":"N",
				"FILE_SIZE_LIMIT" => COption::GetOptionString("catalog", "1C_FILE_SIZE_LIMIT", 200*1024),
				"USE_CRC" => COption::GetOptionString("catalog", "1C_USE_CRC", "Y"),
				"USE_ZIP" => COption::GetOptionString("catalog", "1C_USE_ZIP", "Y"),
				"USE_OFFERS" => COption::GetOptionString("catalog", "1C_USE_OFFERS", "N"),
				"USE_IBLOCK_TYPE_ID" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_TYPE_ID", "N"),
				"USE_IBLOCK_PICTURE_SETTINGS" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_PICTURE_SETTINGS", "N"),
				"TRANSLIT_ON_ADD" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_ADD", "N"),
				"TRANSLIT_ON_UPDATE" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_UPDATE", "N"),
			)
		);
	}
	
	function ExecuteRegion($fileName)
	{
		Add2Log("[php] Импортируем файл ".$fileName);

		if(!CModule::IncludeModule('sale'))
		{
			Add2Log('[php] Модуль sale не загружен');
		}

		$regions_xml = simplexml_load_file($fileName);

		$arElem = array(
			'ORDER_PROPS_ID' => REGION_PROPERTY_ID,
		);

		$AddedCount = 0;
		$UpdatedCount = 0;
		$arCount = array(
			'ADDED' => 0,
			'UPDATED' => 0,
			'DELETED' => 0,
		);
		$arExistedID = array();

		//CSaleOrderPropsVariant::DeleteAll(REGION_PROPERTY_ID);

		foreach($regions_xml->Регион as $arRegion)
		{
			$arElem['NAME'] = $arRegion->Наименование;
			$arElem['VALUE'] = $arRegion->Код;

			// Проверяем элементы
			$arVal = CSaleOrderPropsVariant::GetByValue(REGION_PROPERTY_ID, $arElem['VALUE']);
			
			// Существует
			if($arVal)
			{
				$arExistedID[] = $arVal['ID'];
				// Обновляем имя, если изменилось
				
				if($arVal['NAME'] != $arElem['NAME'])
				{
					$res = CSaleOrderPropsVariant::Update($arVal['ID'], $arElem);
					if($res)
						$arCount['UPDATED']++;;
				}
			}
			// Не существует
			else
			{
				// Добавляем
				$res = CSaleOrderPropsVariant::Add($arElem);
				if($res)
				{
					$arExistedID[] = $res;
					$arCount['ADDED']++;;
				}
					
			}
		}

		// Удаляем отсутствующие в файле
		$db_props = CSaleOrderPropsVariant::GetList(false, array('ORDER_PROPS_ID' => REGION_PROPERTY_ID, '!ID' => $arExistedID));
		while ($props = $db_props->Fetch())
		{
			if(CSaleOrderPropsVariant::Delete($props['ID']))
				$arCount['DELETED']++;
		}

		Add2Log('[php] Добавлено '.$arCount['ADDED'].' регионов, обновлено '.$arCount['UPDATED'].' регионов, удалено '.$arCount['DELETED'].' регионов');
	}
}

?>
