<? 
/**
 * Функция производит разбор URL для каталога и возвращает массив, состоящий из двух
 * элементов -- код секции и код элемента
 * 
 * @return array
 */
function ParseCatalogURL()
{
	$arUrl = parse_url($_SERVER["REQUEST_URI"]);
	$arSections = explode("/", $arUrl["path"]);
	return array("SECTION_CODE" => $arSections[2], "ELEMENT_CODE" => $arSections[3]); 
}

/**
 * Функция устанавивает хлебные крошки для раздела
 *
 * @param integer $sectionID
 */
function SetBreadcrumbSection($sectionID)
{
	global $APPLICATION;
	$dbList = CIBlockSection::GetByID($sectionID);

	if ($dbItem = $dbList->GetNext())
	{
		$nameSection = $dbItem["NAME"];
		$urlSection = $dbItem["SECTION_PAGE_URL"];

		$dbList = CIBlockSection::GetNavChain($dbItem["$IBLOCK_ID"], $dbItem["IBLOCK_SECTION_ID"]);

		while ($dbItem = $dbList->GetNext())
		{
			$APPLICATION->AddChainItem($dbItem["NAME"],$dbItem["SECTION_PAGE_URL"]);
		}
		if(!$GLOBALS['BRAND_CHAINE_ITEM'])
			$APPLICATION->AddChainItem($nameSection, "");
		else{
			$APPLICATION->AddChainItem($nameSection, $urlSection);
			$APPLICATION->AddChainItem($GLOBALS['BRAND_CHAINE_ITEM'], "");
		}
	}
}

/**
 * Function for convertion "да | нет"
 * to html format string
 */
function ConvertYesNoToHtmlString($strValue){
	$strValueLower = strtolower($strValue);

	switch ($strValueLower) {
		case 'да':
			return '<input type="checkbox" value="1" checked disabled >';
			$checked = 'checked';
			break;

		case 'нет':
			return '';
			$checked = '';
			break;

		default:
			return $strValue;
	}

	//return sprintf('<input type="radio" value="1" %s disabled >', $checked);
}

/**
 * Function for convertion color
 * to html+css format string
 */
function ConvertColorToHtmlString($strValue){
	$strValueLower = strtolower($strValue);

	if( strlen($strValueLower) == 3 || strlen($strValueLower) == 6 )
		return sprintf('<div class="color-block" style="background-color:#%s;">', $strValueLower);

	return $strValue;
}

function getOptionalUrl($optionId){
	if(CModule::IncludeModule('iblock') && ($arIBlockElement = GetIBlockElement($optionId, 'banner_option')))
	{
		$return  = '';
		$return .= '<a href="' . $arIBlockElement[PROPERTIES][ALT_URL_ALT_URL][VALUE] . '"><div style="display: block;position: absolute;width:' . $arIBlockElement[PROPERTIES][ALT_URL_WIDTH][VALUE] . 'px;height:' . $arIBlockElement[PROPERTIES][ALT_URL_HEIGHT][VALUE] . 'px;top:' . $arIBlockElement[PROPERTIES][ALT_URL_COORD_Y][VALUE] . 'px;left:' . $arIBlockElement[PROPERTIES][ALT_URL_COORD_X][VALUE] . 'px;"></div></a>';
		$totalReturn = $return;
		echo $totalReturn;
	}
}

/**
 * Функция возвращает кол-во товара в торговом предложении
 *
 * @param integer $skuIBlockID -- ID инфоблока торговых предложений
 * @param integer $productID -- ID товара
 *
 * @return integer
 */
function GetQuantitySKU($skuIBlockID = NULL, $skuID)
{
	$result = 0;

	$arFilter = array(
			"ACTIVE" => "Y",
			"ID" => $skuID
	);

	if (!empty($skuIBlockID)) $arFilter["IBLOCK_ID"] = $skuIBlockID;

	$arSelect = array("ID", "IBLOCK_ID", "CATALOG_QUANTITY");

	$dbList = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	if ($dbItem = $dbList->GetNext())
	{
		$result = $dbItem["CATALOG_QUANTITY"];
	}

	return $result;
}

/**
 * Функция возвращает кол-во товара (производит подсчёт в SKU)
 *
 * @param integer $skuIBlockID -- ID инфоблока торговых предложений
 * @param integer $productID -- ID товара
 *
 * @return integer
 */
function GetQuantityProduct($skuIBlockID, $productID)
{
	$result = 0;

	$arFilter = array(
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $skuIBlockID,
			"PROPERTY_CML2_LINK" => $productID
	);

	$arSelect = array("ID", "IBLOCK_ID", "CATALOG_QUANTITY");

	$dbList = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while ($dbItem = $dbList->GetNext())
	{
		$result += $dbItem["CATALOG_QUANTITY"];
	}

	return $result;
}

/**
 * Функция удаляет из массива элементы, чьи ключи начинаеются с ~
 *
 * @param array $ar
 * @return array
 */
function RemoveDuplicateKeyFromDBArray($ar)
{
	foreach ($ar as $key => $value)
	{
		if (substr($key, 0, 1) == "~") unset($ar[$key]);
	}
	return $ar;
}

/**
 * Функция обрезает строку и дописывает в конец троеточие
 *
 * @param string $str
 * @param integer $length
 * @return string
 */
function TruncateStr($str, $length)
{
	if (mb_strlen($str) > $length)
		return mb_substr($str, 0, $length)."...";
	else
		return $str;
}

/**
 * Функция форматирует число HTML-код следующего вида: 133<span>457</span>, если
 * число меньше 1000, то 547
 *
 * @param unknown $price
 * @param string $currency
 * @return string
 */
function GetPriceHTMLFormat($price, $currency = false, $htmlFormat = true)
{
	if (!$htmlFormat)
		$result = number_format( $price, 0 , '.', ' ' );
	else
	{
		if ($price < 1000)
			return "<span>".$price."</span>". (!$currency ? "" : $currency);
		else
		{
			$priceFormat = number_format( $price, 0 , '.', ' ' );
			$arPriceFormat = explode(" ", $priceFormat);
			foreach ($arPriceFormat as $key => $value)
			{
				if ($key == (count($arPriceFormat) - 1))
					$result .= "<span>".$value."</span>";
				else
					$result .= $value;
			}
		}
	}

	return $result." ".(!$currency?"":$currency);
}

/**
 * Функция изменяет строку согласно параметрам шаблона, описанным
 * в $aReplace
 *
 * @param string $s -- входящая строка, содержащая описание переменых шаблона
 * @param mixed $aReplace -- массив вида array( шаблон => значение, ... )
 *
 * @return int ID инфоблока или -1
 */
function GetMessageByPattern($s, $aReplace=false, $clearPattern=true)
{
	if($aReplace!==false && is_array($aReplace)) {
		foreach($aReplace as $search=>$replace) {
			$s = str_replace($search, $replace, $s);
		}
	}

	if ($clearPattern) {
		//! Удаляем из строки шаблоны, которые не были использованы и знаки препинания за ними
		$matches = array();

		if (preg_match_all("/\#(.*?)\#(,|.)/", $s, $matches))
		{
			foreach ($matches[0] as $value) {
				$s = str_replace($value,"",$s);
			}
		}
	}

	return trim($s);
}

function GetElementPrices($ID){
	
	CModule::IncludeModule("iblock");
	CModule::IncludeModule("catalog");
	CModule::IncludeModule("sale");
	CModule::IncludeModule("currency");	
	
	$arResult = array();
	$arPrices = array(0=>PRICE_BASE_CODE);
	
	$arParams["PRICES"] = CIBlockPriceTools::GetCatalogPrices(SHARE_CATALOG_IBLOCK_ID, $arPrices);
	$arParams["CONVERT_CURRENCY"] = "Y";
	$arParams["CURRENCY_ID"] = CURRENCY_BASE;
	
	
	$arSelectElems = Array(
			"ID",
			"IBLOCK_ID",
			"NAME",
			"DETAIL_PAGE_URL",
			"CATALOG_QUANTITY"
	);
	$arFilterElems = Array(
			"IBLOCK_TYPE"=>"aspro_kshop_catalog",
			"IBLOCK_ID"=>SHARE_CATALOG_IBLOCK_ID,
			"ACTIVE"=>"Y",
			"ID"=>$ID,
			"INCLUDE_SUBSECTIONS" => "Y"
	);
	foreach($arParams["PRICES"] as $price){
		$arFilterElems["CATALOG_SHOP_QUANTITY_".$price["ID"]] = 1;
	}
	
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
	
	$resElems = CIBlockElement::GetList(
			Array("NAME"=>"DESC"), 
			$arFilterElems, 
			false, 
			false, 
			$arSelectElems
	);
	while($obElems = $resElems->GetNextElement())
	{
		$arItem = $obElems->GetFields();
		
		$arItem["PRICES"] = array();
		$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices(
				$arItem["IBLOCK_ID"],
				$arParams["PRICES"],
				$arItem,
				"N",
				$arConvertParams
		);
		
		$arResult[] = $arItem;	
	}
	
	return $arResult[0]["PRICES"];
}

/**
 * Функция возвращает поля элемента по ID
 *
 * @param integer $sectionId
 * @return array|NULL
 */
function GetElementInfoByID($elementId)
{
	$dbItem = array();

	if (CModule::IncludeModule("iblock"))
	{
		$dbList = CIBlockElement::GetByID($elementId);
		$dbItem = $dbList->GetNext();
	}
	return $dbItem;
}

/**
 * Функция возвращает поля раздела по ID
 *
 * @param integer $sectionId
 * @return array|NULL
 */
function GetSectionInfoByID($sectionId)
{
	$dbList = CIBlockSection::GetByID($sectionId);
	if ($dbItem = $dbList->GetNext())
	{
		return $dbItem;
	}
	else
	{
		return NULL;
	}
}

/**
 * Возвращает массив описывающий элемент инфоблока
 */
function GetIblockItemData($IblockId, $ItemId, $arSelectAdd = array(), $arSort = array(), $is_ID_key=false, $return_props = array())
{
	$Result = array();

	$arSelect = array('IBLOCK_ID', 'ID', 'NAME');
	$arSelect = array_merge($arSelect, $arSelectAdd);

	if(!empty($IblockId) && is_numeric($IblockId)){
		$arFilter = array('IBLOCK_ID' => $IblockId, 'ACTIVE' => 'Y', 'INCLUDE_SUBSECTIONS'=>'Y');
		if(is_numeric($ItemId) || is_array($ItemId)){
			$arFilter['ID'] = $ItemId;
		}else{
			$arFilter['CODE'] = $ItemId;
		}
		if(count($arFilter) > 1){
			$rsItems = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
			while($obItems = $rsItems->GetNextElement())
			{
				$arFields = $obItems->GetFields();
				
				if(in_array("IBLOCK_SECTION_ID", $arSelectAdd)){
					$arFields['SECTION'] = GetSectionInfoByID($arFields['IBLOCK_SECTION_ID']);
				}
				if(!empty($return_props)){
					$arFields['PROPERTIES'] = array();
					foreach($return_props as $prop_code){
						$property = array();
						$property = $obItems->GetProperty($prop_code);
						if(!empty($property)){
							$arFields['PROPERTIES'][$property['CODE']] = $property;
						}				
					}
				}				
				
				if(is_array($ItemId)){
					if($is_ID_key == false)
						$Result[] = $arFields;
					else
						$Result[$arFields['ID']] = $arFields;
				}else
					$Result = $arFields;
			}
		}
	}

	return $Result;
}

function getGTMElementDataByID($ElementID){
	if(!$ElementID)
		return false;
	
	$elementIblockID = CIBlockElement::GetIBlockByID($ElementID);
		
	$ElementData = GetIblockItemData(
		$elementIblockID, 
		$ElementID,
		array('IBLOCK_SECTION_ID', 'PROPERTY_BRAND.NAME')
	);
	return $ElementData;
}

function GTMNavPosition($container, $arResItems, $list, $arResultNavRes){
	if((int)$arResultNavRes->NavPageNomer > 0 && (int)$arResultNavRes->NavPageSize > 0){
		$GTM_items = array();
		foreach($arResItems as $key => $Item){
			$GTM_items[$arResultNavRes->NavPageSize*$arResultNavRes->NavPageNomer-$arResultNavRes->NavPageSize+$key+1] = $Item;
		}
		GTMDataCollector($container, $GTM_items, $list);
	}else{
		GTMDataCollector($container, $arResItems, $list);
	} 
}

/*формируем и сохраняем данные в глобальный массив для GTM*/
function GTMDataCollector($type='', $arData=array(), $list_type=''){
	if( empty($arData)  )
		return false;

	if( preg_match('/detail/i', $type) ){ // для детального товара
		
		$GTMElementData = getGTMElementDataByID($arData['ID']);
		
		$info 		    = new stdClass();
		$info->id       = (string)$arData['ID'];
		$info->name     = (string)$arData['NAME'];
		$info->price    = (string)$arData['PRICES']['BASE']['VALUE'];
		$info->brand    = (string)($arData['PROPERTIES']['BRAND']['DISPLAY_VALUE'])?$arData['PROPERTIES']['BRAND']['DISPLAY_VALUE']:$GTMElementData['PROPERTY_BRAND_NAME'];
		$info->category = (string)($arData['SECTION']['NAME'])?$arData['SECTION']['NAME']:$GTMElementData['SECTION']['NAME'];
		//$info->position = $GLOBALS['GTM_POSITION']['detail']++;
		
		$GLOBALS['GTM_DATA']['detail'][$info->id] = $info;
	}elseif( preg_match('/impressions/i', $type) ){  // для каталога товаров
		foreach($arData as $position=>$arItem){
			
			$GTMElementData = getGTMElementDataByID(($arItem['PRODUCT_ID'])?$arItem['PRODUCT_ID']:$arItem['ID']);
		
		  	$info 		  = new stdClass();
		  	$info->id       = (string)($arItem['PRODUCT_ID'])?$arItem['PRODUCT_ID']:$arItem['ID'];
		  	$info->name     = (string)$arItem['NAME'];
		  	$info->list     = (string)($list_type)?$list_type:'catalog';
		  	$info->price    = (string)(empty($arItem['PRICES']['BASE']['VALUE']))?0:$arItem['PRICES']['BASE']['VALUE'];
		  	$info->brand    = (string)$GTMElementData['PROPERTY_BRAND_NAME'];
		  	$info->position = (string)$position;//$GLOBALS['GTM_POSITION']['impressions']++;
		  	$info->category = (string)$GTMElementData['SECTION']['NAME'];
		  
		  	if( isset($arItem['QUANTITY']) )
		  		$info->quantity = $arItem['QUANTITY'];

		  	//$GLOBALS['GTM_DATA']['impressions'][$info->id] = $info;
		  	$GLOBALS['GTM_DATA']['impressions'][$GLOBALS['GTM_POSITION']['impressions']++] = $info;
		}
	}elseif( preg_match('/promoView/i', $type) ){ // для промо товаров
		foreach($arData as $position=>$arItem){
			
			$GTMElementData = getGTMElementDataByID(($arItem['PRODUCT_ID'])?$arItem['PRODUCT_ID']:$arItem['ID']);
			
		  	$info 		  = new stdClass();
		  	$info->id       = (string)($arItem['PRODUCT_ID'])?$arItem['PRODUCT_ID']:$arItem['ID'];
		 	$info->name     = (string)$arItem['NAME'];
		  	$info->creative = (string)($list_type)?$list_type:'viewed_products';
		  	$info->position = (string)$position;//$GLOBALS['GTM_POSITION']['promoView']++;
		  	if($GTMElementData['PROPERTY_BRAND_NAME'])
		  		$info->brand    = (string)$GTMElementData['PROPERTY_BRAND_NAME'];
		  	if($GTMElementData['SECTION']['NAME'])
		  		$info->category = (string)$GTMElementData['SECTION']['NAME'];
		  	$info->list     = ($list_type)?$list_type:'viewed_products';
			
		  	//$GLOBALS['GTM_DATA']['promoView'][$info->id] = $info;
		  	$GLOBALS['GTM_DATA']['promoView'][$GLOBALS['GTM_POSITION']['promoView']++] = $info;
		}
	}
}
?>