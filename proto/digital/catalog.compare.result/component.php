<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
$this->setFrameMode(false);

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}
if( isset($_REQUEST["ajax"]) )
	$APPLICATION->RestartBuffer();
/*************************************************************************
	Processing of received parameters
*************************************************************************/
unset($arParams["IBLOCK_TYPE"]); //was used only for IBLOCK_ID setup with Editor
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["NAME"]=trim($arParams["NAME"]);
if(strlen($arParams["NAME"])<=0)
	$arParams["NAME"] = "CATALOG_COMPARE_LIST";

if(strlen($arParams["ELEMENT_SORT_FIELD"])<=0)
	$arParams["ELEMENT_SORT_FIELD"]="sort";

if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["ELEMENT_SORT_ORDER"]))
	$arParams["ELEMENT_SORT_ORDER"]="asc";

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);
$arParams["BASKET_URL"]=trim($arParams["BASKET_URL"]);
if(strlen($arParams["BASKET_URL"])<=0)
	$arParams["BASKET_URL"] = "/personal/basket.php";

$arParams["ACTION_VARIABLE"]=trim($arParams["ACTION_VARIABLE"]);
if(strlen($arParams["ACTION_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["ACTION_VARIABLE"]))
	$arParams["ACTION_VARIABLE"] = "action";

$arParams["PRODUCT_ID_VARIABLE"]=trim($arParams["PRODUCT_ID_VARIABLE"]);
if(strlen($arParams["PRODUCT_ID_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_ID_VARIABLE"]))
	$arParams["PRODUCT_ID_VARIABLE"] = "id";

$arParams["SECTION_ID_VARIABLE"]=trim($arParams["SECTION_ID_VARIABLE"]);
if(strlen($arParams["SECTION_ID_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["SECTION_ID_VARIABLE"]))
	$arParams["SECTION_ID_VARIABLE"] = "SECTION_ID";

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["PROPERTY_CODE"][$k]);

if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["FIELD_CODE"][$k]);

if(!is_array($arParams["OFFERS_FIELD_CODE"]))
	$arParams["OFFERS_FIELD_CODE"] = array();
foreach($arParams["OFFERS_FIELD_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["OFFERS_FIELD_CODE"][$k]);

if(!is_array($arParams["OFFERS_PROPERTY_CODE"]))
	$arParams["OFFERS_PROPERTY_CODE"] = array();
foreach($arParams["OFFERS_PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["OFFERS_PROPERTY_CODE"][$k]);

if(!in_array("NAME", $arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"][]="NAME";
if(!is_array($arParams["PRICE_CODE"]))
	$arParams["PRICE_CODE"] = array();

$arParams["USE_PRICE_COUNT"] = $arParams["USE_PRICE_COUNT"]=="Y";
$arParams["SHOW_PRICE_COUNT"] = intval($arParams["SHOW_PRICE_COUNT"]);
if($arParams["SHOW_PRICE_COUNT"]<=0)
	$arParams["SHOW_PRICE_COUNT"]=1;

$arParams["DISPLAY_ELEMENT_SELECT_BOX"] = $arParams["DISPLAY_ELEMENT_SELECT_BOX"]=="Y";
if (empty($arParams["ELEMENT_SORT_FIELD_BOX"]))
	$arParams["ELEMENT_SORT_FIELD_BOX"]="sort";
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["ELEMENT_SORT_ORDER_BOX"]))
	$arParams["ELEMENT_SORT_ORDER_BOX"]="asc";
if (empty($arParams["ELEMENT_SORT_FIELD_BOX2"]))
	$arParams["ELEMENT_SORT_FIELD_BOX2"] = "id";
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["ELEMENT_SORT_ORDER_BOX2"]))
	$arParams["ELEMENT_SORT_ORDER_BOX2"] = "desc";

if (empty($arParams['HIDE_NOT_AVAILABLE']))
	$arParams['HIDE_NOT_AVAILABLE'] = 'N';
elseif ('Y' != $arParams['HIDE_NOT_AVAILABLE'])
	$arParams['HIDE_NOT_AVAILABLE'] = 'N';

$arParams["PRICE_VAT_INCLUDE"] = $arParams["PRICE_VAT_INCLUDE"] !== "N";

if($arParams["LINK_IBLOCK_ID"] >	0 && strlen($arParams["LINK_PROPERTY_SID"]) > 0)
{
	if(!is_array($arParams["LINK_PROPERTY_CODE"]))
		$arParams["LINK_PROPERTY_CODE"] = array();
	foreach($arParams["LINK_PROPERTY_CODE"] as $k=>$v)
		if($v==="")
			unset($arParams["LINK_PROPERTY_CODE"][$k]);
	if(!is_array($arParams["LINK_FIELD_CODE"]))
		$arParams["LINK_FIELD_CODE"] = array();
	foreach($arParams["LINK_FIELD_CODE"] as $k=>$v)
		if($v==="")
			unset($arParams["LINK_FIELD_CODE"][$k]);
}
else
{
	unset($arParams["LINK_PROPERTY_CODE"]);
	unset($arParams["LINK_FIELD_CODE"]);
}

$arParams['CONVERT_CURRENCY'] = (isset($arParams['CONVERT_CURRENCY']) && 'Y' == $arParams['CONVERT_CURRENCY'] ? 'Y' : 'N');
$arParams['CURRENCY_ID'] = trim(strval($arParams['CURRENCY_ID']));
if ('' == $arParams['CURRENCY_ID'])
{
	$arParams['CONVERT_CURRENCY'] = 'N';
}
elseif ('N' == $arParams['CONVERT_CURRENCY'])
{
	$arParams['CURRENCY_ID'] = '';
}

$arID = array();
if(isset($_REQUEST["ID"]))
{
	$arID = $_REQUEST["ID"];
	if(!is_array($arID))
		$arID = array($arID);
}
$arPR = array();
if(isset($_REQUEST["pr_code"]))
{
	$arPR = $_REQUEST["pr_code"];
	if(!is_array($arPR))
		$arPR = array($arPR);
}
$arOF = array();
if(isset($_REQUEST["of_code"]))
{
	$arOF = $_REQUEST["of_code"];
	if(!is_array($arOF))
		$arOF = array($arOF);
}
$arOP = array();
if(isset($_REQUEST["op_code"]))
{
	$arOP = $_REQUEST["op_code"];
	if(!is_array($arOP))
		$arOP = array($arOP);
}

$arResult = array();

/*************************************************************************
			Handling the Compare button
*************************************************************************/
if(isset($_REQUEST["action"]))
{
	switch($_REQUEST["action"])
	{
		case "ADD_TO_COMPARE_RESULT":
			if(
				intval($_REQUEST["id"]) > 0
				&& !array_key_exists($_REQUEST["id"], $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])
			)
			{
				$arOffers = CIBlockPriceTools::GetOffersIBlock($arParams["IBLOCK_ID"]);
				$OFFERS_IBLOCK_ID = $arOffers? $arOffers["OFFERS_IBLOCK_ID"]: 0;

				//SELECT
				$arSelect = array(
					"ID",
					"IBLOCK_ID",
					"IBLOCK_SECTION_ID",
					"NAME",
					"DETAIL_PAGE_URL",
				);
				//WHERE
				$arFilter = array(
					"ID" => intval($_REQUEST["id"]),
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"IBLOCK_LID" => SITE_ID,
					"IBLOCK_ACTIVE" => "Y",
					"ACTIVE_DATE" => "Y",
					"ACTIVE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
					"MIN_PERMISSION" => "R"
				);
				if($OFFERS_IBLOCK_ID > 0)
					$arFilter["IBLOCK_ID"] = array($arParams["IBLOCK_ID"], $OFFERS_IBLOCK_ID);
				else
					$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];

				$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
				$rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);
				$arElement = $rsElement->GetNext();

				$arMaster = false;
				if($arElement && $arElement["IBLOCK_ID"] == $OFFERS_IBLOCK_ID)
				{
					$rsMasterProperty = CIBlockElement::GetProperty($arElement["IBLOCK_ID"], $arElement["ID"], array(), array("ID" => $arOffers["OFFERS_PROPERTY_ID"], "EMPTY" => "N"));
					if($arMasterProperty = $rsMasterProperty->Fetch())
					{
						$rsMaster = CIBlockElement::GetList(
							array()
							,array(
								"ID" => $arMasterProperty["VALUE"],
								"IBLOCK_ID" => $arMasterProperty["LINK_IBLOCK_ID"],
								"ACTIVE" => "Y",
							)
						,false, false, $arSelect);
						$rsMaster->SetUrlTemplates($arParams["DETAIL_URL"]);
						$arMaster = $rsMaster->GetNext();
					}
				}

				if($arMaster)
				{
					$arMaster["NAME"] = $arElement["NAME"];
					$arMaster["DELETE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&id=".$arMaster["ID"], array("action", "id")));
					$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$_REQUEST["id"]] = $arMaster;
				}
				elseif($arElement)
				{
					$arElement["DELETE_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&id=".$arElement["ID"], array("action", "id")));
					$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$_REQUEST["id"]] = $arElement;
				}
			}
			break;
		case "DELETE_FROM_COMPARE_RESULT":
			foreach($arID as $ID)
				unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$ID]);
			break;
		case "ADD_FEATURE":
			foreach($arPR as $ID)
				unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"][$ID]);

			foreach($arOF as $ID)
				unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"][$ID]);

			foreach($arOP as $ID)
				unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"][$ID]);
			break;
		case "DELETE_FEATURE":
			foreach($arPR as $ID)
				$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"][$ID]=true;

			foreach($arOF as $ID)
				$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"][$ID]=true;

			foreach($arOP as $ID)
				$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"][$ID]=true;
			break;
		case "ADD_TO_COMPARE_ANIART":
			if(
				intval($_REQUEST["id"]) > 0
				&& !array_key_exists($_REQUEST["id"], $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])
			){	
				$arOffers = CIBlockPriceTools::GetOffersIBlock($arParams["IBLOCK_ID"]);
				$OFFERS_IBLOCK_ID = $arOffers? $arOffers["OFFERS_IBLOCK_ID"]: 0;

				//SELECT
				$arSelect = array(
					"ID",
					"IBLOCK_ID",
					"IBLOCK_SECTION_ID",
					"NAME",
					"DETAIL_PAGE_URL",
				);
				//WHERE
				$arFilter = array(
					"ID" => intval($_REQUEST["id"]),
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"IBLOCK_LID" => SITE_ID,
					"IBLOCK_ACTIVE" => "Y",
					"ACTIVE_DATE" => "Y",
					"ACTIVE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
					"MIN_PERMISSION" => "R"
				);

				$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
				$rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);
				$arElement = $rsElement->GetNext();
				//array('OFFERS'=>$arOffers, 'SELECT'=>$arSelect, 'FILTER'=>$arFilter)
				if( $arElement ){
					/*
					if( count($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"]) > (int)$arParams['MAX_ELEMENT_COUNT'] )
						unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][key($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])]);
					*/
					$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$arElement["ID"]] = $arElement["ID"];
				}

			}
			break;
		case "DELETE_FROM_COMPARE_ELEMENT":
			//foreach($arID as $ID)
			if(	intval($_REQUEST["id"]) > 0 )
				unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][(int)$_REQUEST["id"]]);
			break;
		case "DELETE_ALL_COMPARE_ELEMENTS":
			//foreach($arID as $ID)
				unset($_SESSION[$arParams["NAME"]]);
			break;
	}
}

if( isset($_REQUEST["ajax"]) )
	die();
/*
if(!isset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"]))
	$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"] = false;
if(isset($_REQUEST["DIFFERENT"]))
	$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"] = $_REQUEST["DIFFERENT"]=="Y";
$arResult["DIFFERENT"] = $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"];
*/
/*************************************************************************
			Processing of the Buy link
*************************************************************************/
$strError = "";
if (array_key_exists($arParams["ACTION_VARIABLE"], $_REQUEST) && array_key_exists($arParams["PRODUCT_ID_VARIABLE"], $_REQUEST))
{
	$action = strtoupper($_REQUEST[$arParams["ACTION_VARIABLE"]]);
	$productID = intval($_REQUEST[$arParams["PRODUCT_ID_VARIABLE"]]);
	if (($action == "COMPARE_ADD2BASKET" || $action == "COMPARE_BUY") && $productID > 0)
	{
		if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
		{
			$QUANTITY = 1;
			$product_properties = array();
			if(is_array($arParams["OFFERS_CART_PROPERTIES"]))
			{
				foreach($arParams["OFFERS_CART_PROPERTIES"] as $i => $pid)
					if($pid === "")
						unset($arParams["OFFERS_CART_PROPERTIES"][$i]);

				if(!empty($arParams["OFFERS_CART_PROPERTIES"]))
				{
					$product_properties = CIBlockPriceTools::GetOfferProperties(
						$productID,
						$arParams["IBLOCK_ID"],
						$arParams["OFFERS_CART_PROPERTIES"]
					);
				}
			}

			if (Add2BasketByProductID($productID, $QUANTITY, $product_properties))
			{
				if ($action == "COMPARE_BUY")
					LocalRedirect($arParams["BASKET_URL"]);
				else
					LocalRedirect($APPLICATION->GetCurPageParam("", array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
			}
			else
			{
				if ($ex = $APPLICATION->GetException())
					$strError = $ex->GetString();
				else
					$strError = GetMessage("CATALOG_ERROR2BASKET").".";
			}
		}
	}
}
if(strlen($strError)>0)
{
	ShowError($strError);
	return;
}
/*************************************************************************
** START EXECUTE
**************************************************************************/
$arCompare = $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"];

/*проверка на секцию*/
if( !empty($arParams['SECTION_CODE']) || isset($_REQUEST['section']) ){
	// тут будет обработка секций
	$sectionCode = ( !empty($arParams['SECTION_CODE']) )?$arParams['SECTION_CODE']:$_REQUEST['section'];
	
	$res = CIBlockSection::GetList(array('ID' => 'asc'),array('CODE'=>$sectionCode, 'IBLOCK_ID'=>$arParams['IBLOCK_ID']), false, array('ID', 'CODE', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'DEPTH_LEVEL', 'ACTIVE'));
	// есть ли совпадение
	if($ar_res = $res->GetNext()){
		//добавляю в массив секций
		$arSectionId[] = $ar_res['ID'];
		//наличие подсекций
		if($ar_res['LEFT_MARGIN']+1 != $ar_res['RIGHT_MARGIN']){
			 $arFilter = array('IBLOCK_ID' => $this->arParams['IBLOCK_ID'],'>=LEFT_MARGIN' => $ar_res['LEFT_MARGIN'],'<RIGHT_MARGIN' => $ar_res['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $ar_res['DEPTH_LEVEL']); // выберет потомков без учета активности
			 $res = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter, false, array('ID'));
			 while ($arSect = $res->GetNext())
			 {
				 //добавляю в массив секций
				 $arSectionId[] = $arSect['ID'];
			 }
		}
	}
}

// Приоритет гет параметров над сессией
if( $arParams['USE_PRIORITY_GET_SESSION']=='Y' ){
	if( isset($_REQUEST['id']) && is_array($_REQUEST['id']) ){
		$arCompare = array();
		foreach( $_REQUEST['id'] as $idElement ){
			/*
			if( count($arCompare) > (int)$arParams['MAX_ELEMENT_COUNT'] )
				unset($arCompare[key($arCompare)]);
			*/
			$arCompare[(int)$idElement] = (int)$idElement;
		}
	} else {
		$arCompare = $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"];
	}
}

//p($arCompare);

//не пустой ли массив для сравнения
if(is_array($arCompare) && count($arCompare)>0)
{

	
	/*$arFilter = array('ID'=>$arCompare, 'ACTIVE'=>'Y');
	$arSelect = array('ID', 'IBLOCK_SECTION_ID');
	//p($arFilter);
	$obElems = CIBlockElement::GetList(array('ID'=>'ASC'), $arFilter, false, false, $arSelect);
	
	// группирую элементы по секциям
	while ($arElem = $obElems->GetNext() ) {
		
		// Если элементов больше чем указано в параметрах выкидываем первые элементы
		if( count($arGroupSection['ITEMS'][$arElem['IBLOCK_SECTION_ID']]) >= (int)$arParams['MAX_ELEMENT_COUNT'] ){
			unset($arGroupSection['ITEMS'][$arElem['IBLOCK_SECTION_ID']][key($arGroupSection['ITEMS'][$arElem['IBLOCK_SECTION_ID']])]);
			$arResult['MORE_LIMITED'] = true;
		}

		$arGroupSection['ITEMS'][$arElem['IBLOCK_SECTION_ID']][] = $arElem['ID'];
		$arGroupSection['ALL_COUNT_ELEMENT_COMPARE'][$arElem['IBLOCK_SECTION_ID']]++;
	}*/
	foreach($arCompare as $arElem){
		// Если элементов больше чем указано в параметрах выкидываем первые элементы
		//if( count($arGroupSection['ITEMS'][$arElem['IBLOCK_SECTION_ID']]) >= (int)$arParams['MAX_ELEMENT_COUNT'] ){
		//	unset($arGroupSection['ITEMS'][$arElem['IBLOCK_SECTION_ID']][key($arGroupSection['ITEMS'][$arElem['IBLOCK_SECTION_ID']])]);
		//	$arResult['MORE_LIMITED'] = true;
		//}
		
		$arGroupSection['ITEMS'][$arElem['IBLOCK_SECTION_ID']][] = $arElem['ID'];
		$arGroupSection['ALL_COUNT_ELEMENT_COMPARE'][$arElem['IBLOCK_SECTION_ID']]++;
	}
	//p($arGroupSection);
	foreach ($arGroupSection['ITEMS'] as $sectionId => $arElementsInSection)
	{
		$arSection['INFO'] = CIBlockSection::GetByID($sectionId)->GetNext();

		if( count($arElementsInSection)<=1 ){
			continue;
		}

		/*проверка на секцию*/
		if( !empty($arParams['SECTION_CODE']) || isset($_REQUEST['section']) ){
			if( !in_array($arSection['INFO']['ID'],$arSectionId) )
				continue;
		}

		/*******************************************
		 *Формирую доп массив для сравнения товаров
		 *******************************************/
		

		/*Узнаю ИБЛОК торг предложений по текущему*/
		$arInfo = CCatalogSKU::GetInfoByProductIBlock($arParams["IBLOCK_ID"]);

		/* Формирую массив основных параметров и начальное условие для выборки*/
		$arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", 'DETAIL_PAGE_URL', "PROPERTY_GOODS_SOLD", "IBLOCK_ID", "PROPERTY_".PROPERTY_CHARACTERISTICS, "PROPERTY_MARKETING", "PROPERTY_GIFTS");

		global $ar_structure_data;
		
		$arSection['ID']=$sectionId;
		$arIdElements = $arElementsInSection;
		//$arResult['SECTION']["XML_ID"] = $arSection['INFO']['XML_ID'];

		foreach ($ar_structure_data[$arSection['INFO']['XML_ID']] as $prop=>$arValue)
		{
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
		$arFilter = Array("IBLOCK_ID"=>IntVal($arParams["IBLOCK_ID"]), "ACTIVE"=>"Y", "ID"=>$arIdElements);
		foreach($arPRICES as $price){
			$arFilter["CATALOG_SHOP_QUANTITY_".$price["ID"]] = 1;
		}

		//$arFilter = Array("IBLOCK_ID"=>IntVal($arParams["IBLOCK_ID"]), "ACTIVE"=>"Y", "ID"=>$arIdElements);
		/*end Prices*/
		$res = CIBlockElement::GetList(array('ID'=>'ASC'), $arFilter, false, false, $arSelect);

		$count = 0;

		while($ob = $res->GetNextElement())
		{
			/*Получаю содержимое выборки*/
			$arFields = $ob->GetFields();
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
			/*записываю данный масив к общему*/
			$arResult['ITEMS'][$sectionId]['RES_ELEM_COMP'][$arFields['ID']] = $arFields;
			
			/*добавляю акции*/
			if (array_key_exists(PROPERTY_MARKETING_ENUM_GIFT, $arFields["PROPERTY_MARKETING_VALUE"])) {
			    $actions = new CCustomGiftAction(SHARE_CATALOG_IBLOCK_ID, GIFTS_IBLOCK_ID, $arFields["PROPERTY_GIFTS_VALUE"]);
				$arResult['ITEMS'][$sectionId]['RES_ELEM_COMP'][$arFields['ID']]['GIFTS'] = $actions->GetInfo();
				$arResult['ITEMS'][$sectionId]['RES_ELEM_COMP'][$arFields['ID']]['GIFTS']['IDs'] = $arFields["PROPERTY_GIFTS_VALUE"];
			}

			$count++;
		}
		//p($arResult['ITEMS']);
		/* Проверка на торг каталог(подстраховка) */
		if (is_array($arInfo)) 
		{	 /* Формирую доп. массив с торг предложениями для данного города и укладываю его в ранее сформированный */
			 $rsOffers = CIBlockElement::GetList(array(),array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $arIdElements), false, false, array('ID', 'PROPERTY_CML2_LINK'));
			 while ($arOffer = $rsOffers->Fetch()) 
			{	$arResult['ITEMS'][$sectionId]['RES_ELEM_COMP'][$arOffer['PROPERTY_CML2_LINK_VALUE']]['OFFERS'] = $arOffer;	} 
		}
		
		//p($arResult['RES_ELEM_COMP']);
		$arResult['ITEMS'][$sectionId]['COUNT_ELEM_COMP'] = $count;
		$arResult['ITEMS'][$sectionId]['PRIMARY_PROP_NAME'] = $arPropName;
		$arResult['ITEMS'][$sectionId]['ID_ELEMENTS_COMPARE'] = $arIdElements;
		$arResult['ITEMS'][$sectionId]['SECTION_INFO'] = $arSection['INFO'];
		$arResult['ITEMS'][$sectionId]['GIFTS'] = $arElement['GIFTS'];
		$arResult['ITEMS'][$sectionId]['ALL_COUNT_ELEMENT_COMPARE'] = $arGroupSection['ALL_COUNT_ELEMENT_COMPARE'][$sectionId];
		unset($arFields); unset($arPropName); unset($arIdElements);unset($arOffer);

	} //endForeach
	
//	p($arResult);
	$this->IncludeComponentTemplate();
}
else
{
	$this->IncludeComponentTemplate();
	//ShowNote(GetMessage("CATALOG_COMPARE_LIST_EMPTY"));
}
?>