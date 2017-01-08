<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

require_once($_SERVER["DOCUMENT_ROOT"]."/work_scripts/structure-data.php");

/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
//$this->setFrameMode(false);

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
$arParams["NAME"] = trim($arParams["NAME"]);

if(strlen($arParams["NAME"])<=0)
	$arParams["NAME"] = "CATALOG_COMPARE_LIST";

$arResult = array();

/*************************************************************************
			Handling the Compare button
*************************************************************************/
if(isset($_REQUEST["action"]))
{
	$_SESSION[$arParams["NAME"]]["ITEMS"] = &$_SESSION[$arParams["NAME"]]["ITEMS"];
	$_SESSION[$arParams["NAME"]]["LAST_IBLOCK_ID"] = $_SESSION[$arParams["NAME"]]["LAST_IBLOCK_ID"];
	
	switch($_REQUEST["action"])
	{
		case "ADD_TO_COMPARE_ANIART":
			if(
				intval($_REQUEST["id"]) > 0
				&& !array_key_exists($_REQUEST["id"], $_SESSION[$arParams["NAME"]]["ITEMS"])
			)
			{
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
					//"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"IBLOCK_LID" => SITE_ID,
					"IBLOCK_ACTIVE" => "Y",
					"ACTIVE_DATE" => "Y",
					"ACTIVE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
					"MIN_PERMISSION" => "R"
				);
				
				$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
				$arElement = $rsElement->GetNext();
				if($arElement)
				{
					$_SESSION[$arParams["NAME"]]["LAST_IBLOCK_ID"] = $arElement['IBLOCK_ID'];
					$_SESSION[$arParams["NAME"]]["ITEMS"][$arElement["ID"]] = $arElement["ID"];
					$_SESSION[$arParams["NAME"]]["ITEMS_IBLOCK"][$arElement['IBLOCK_ID']][$arElement['ID']] = $arElement["ID"];
				}
			}
			break;
		case "DELETE_FROM_COMPARE_ANIART":
			//SELECT
			$arSelect = array(
				"ID",
				"IBLOCK_ID",
			);
			//WHERE
			$arFilter = array(
				"ID" => intval($_REQUEST["id"]),
			);

			$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
			$arResult['DELETE_ID'] = $_REQUEST["id"];
			if($arElement = $rsElement->GetNext())
			{
				unset($_SESSION[$arParams["NAME"]]["ITEMS"][$arElement['ID']]);
				unset($_SESSION[$arParams["NAME"]]["ITEMS_IBLOCK"][$arElement['IBLOCK_ID']][$arElement['ID']]);
			}
		break;			
		case "DELETE_ALL_COMPARE_ANIART":
			unset($_SESSION[$arParams["NAME"]]['ITEMS']);
			unset($_SESSION[$arParams["NAME"]]['ITEMS_IBLOCK']);
		break;
	}
}

if(isset($_REQUEST["ajax"]))
{
	$arResult['COUNT'] = count($_SESSION[$arParams["NAME"]]["ITEMS_IBLOCK"][$_SESSION[$arParams["NAME"]]["LAST_IBLOCK_ID"]]);
	
	echo json_encode($arResult);
	die();	
}
	

/*************************************************************************
** START EXECUTE
**************************************************************************/
$arCompare = $_SESSION[$arParams["NAME"]]["ITEMS"];

// Приоритет гет паратеров над сессией
if($arParams['USE_PRIORITY_GET_SESSION'] == 'Y')
{
	if( isset($_REQUEST['id']) && is_array($_REQUEST['id']) )
	{
		$arCompare = array();
		foreach($_REQUEST['id'] as $idElement)
		{
			$arCompare[(int)$idElement] = (int)$idElement;
		}
	}
	else
	{
		$arCompare = $_SESSION[$arParams["NAME"]]["ITEMS"];
	}
}

$arResult['IBLOCK_INFO'] = CIBlock::GetByID($_SESSION[$arParams["NAME"]]["LAST_IBLOCK_ID"])->GetNext();


//не пустой ли массив для сравнения
if(is_array($arCompare) && count($arCompare)>0)
{
	/*******************************************
	 *Формирую доп массив для сравнения товаров
	 *******************************************/

	/* Формирую массив основных параметров и начальное условие для выборки*/
	$arSelect = Array(
		"ID",
		"IBLOCK_ID",
		"NAME",
		"PREVIEW_PICTURE",
		"CATALOG_GROUP_".BASE_PRICE_ID,
	);
	
	foreach ($ar_structure_data[$arResult['IBLOCK_INFO']['XML_ID']] as $prop => $arValue)
	{
		$arSelect[] = 'PROPERTY_'.$prop;
		$arSelect[] = 'PROPERTY_'.$prop.'.NAME';
		
		$arPropName[$prop] = $arValue[0];
	}
		
	$arFilter = array(
		"ACTIVE" => "Y",
		"ID" => $arCompare,
		'IBLOCK_ID' => $_SESSION[$arParams["NAME"]]["LAST_IBLOCK_ID"]
	);
	
	$res = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, array('nTopCount' => 20), $arSelect);
	$count = $res->SelectedRowsCount();
	while($arFields = $res->GetNext(true, false))
	{
		$arIdElements[] = $arFields['ID'];
		$arFields['DETAIL_PAGE_URL'] = CUrlExt::GetProductURL($arFields['ID']); 
		$arFields['PREVIEW_PICTURE'] = CFile::ResizeImageGet($arFields['PREVIEW_PICTURE'], array('width' => 120, 'height' => 100), BX_RESIZE_IMAGE_PROPORTIONAL, true); 
		$arResult['ITEMS']['RES_ELEM_COMP'][$arFields['ID']] = $arFields;
	}

	$arResult['ITEMS']['PRIMARY_PROP_NAME'] = $arPropName;
	$arResult['ITEMS']['ID_ELEMENTS_COMPARE'] = $arIdElements;
	$arResult['ITEMS']['ALL_COUNT_ELEMENT_COMPARE'] = $arGroupSection['ALL_COUNT_ELEMENT_COMPARE'];
	
}

if(empty($arResult['ITEMS']) || count($arResult['ITEMS']['RES_ELEM_COMP']) == 1)
{
	LocalRedirect('/'.$arResult['IBLOCK_INFO']['CODE'].'/');
	exit();
}

	
$this->IncludeComponentTemplate();
?>