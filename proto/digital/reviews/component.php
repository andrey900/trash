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

/** @global CCacheManager $CACHE_MANAGER */
global $CACHE_MANAGER;

//CJSCore::Init(array('popup'));

CPageOption::SetOptionString("main", "nav_page_in_session", "N");


unset($_REQUEST['BRAND_CODE']);
unset($_GET['BRAND_CODE']);
unset($_REQUEST['SECTION_CODE']);
unset($_GET['SECTION_CODE']);

//if($_REQUEST["SECTION_CODE"]){
	
	//p($_REQUEST);
	//foreach()
	
	//$pathRedirect = $APPLICATION->GetCurPageParam("", array("SECTION_CODE"));
	//$pathRedirect = $APPLICATION->GetCurPage();
	
	//LocalRedirect($pathRedirect);
//}

global $USER;

$arParams['DEFAULT_LEFT_MARGIN'] = 10;
$arParams['USER_ID'] = $USER->GetID()?$USER->GetID():0;
global $curCity;
$arParams['CUR_CITY'] = $curCity;


if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if(strlen($arParams["IBLOCK_TYPE"])<=0)
	$arParams["IBLOCK_TYPE"] = "news";
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);

$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
if(strlen($arParams["SORT_BY1"])<=0)
	$arParams["SORT_BY1"] = "ACTIVE_FROM";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
	$arParams["SORT_ORDER1"]="DESC";

if(strlen($arParams["SORT_BY2"])<=0)
	$arParams["SORT_BY2"] = "SORT";
if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
	$arParams["SORT_ORDER2"]="ASC";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = array();
}

if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $key=>$val)
	if(!$val)
		unset($arParams["FIELD_CODE"][$key]);

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $key=>$val)
	if($val==="")
		unset($arParams["PROPERTY_CODE"][$key]);

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);

$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
if($arParams["NEWS_COUNT"]<=0)
	$arParams["NEWS_COUNT"] = 20;

$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;


$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
if(strlen($arParams["ACTIVE_DATE_FORMAT"])<=0)
	$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));


$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = array(
		"nPageSize" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
	if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
		$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
}
else
{
	$arNavParams = array(
		"nTopCount" => $arParams["NEWS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	);
	$arNavigation = false;
}

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $USER->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}

if($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arNavigation, $arrFilter)))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if(is_numeric($arParams["IBLOCK_ID"]))
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"ID" => $arParams["IBLOCK_ID"],
		));
	}
	else
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"CODE" => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		));
	}
	if($arResult = $rsIBlock->GetNext())
	{
		$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
		//SELECT
		$arSelect = array_merge($arParams["FIELD_CODE"], array(
			"ID",
			"IBLOCK_ID",
			"NAME",
			"ACTIVE_FROM",
			"DETAIL_PAGE_URL",
			"DETAIL_TEXT",
			"DETAIL_TEXT_TYPE",
			"PREVIEW_TEXT",
			"PREVIEW_TEXT_TYPE",
		));
		$bGetProperty = count($arParams["PROPERTY_CODE"])>0;
		if($bGetProperty)
			$arSelect[]="PROPERTY_*";
		//WHERE
		//p($arParams['USER_ID']);
		
		$O_status_id = $this->GetStatusIdByXML("XML_REVIEW_STATUS_O");
		$S_status_id = $this->GetStatusIdByXML("XML_REVIEW_STATUS_S");
		
		$arFilter = array (
			"IBLOCK_ID" => $arResult["ID"],
			"IBLOCK_LID" => SITE_ID,
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
			"PROPERTY_OBJECT_ID"=>$arParams["OBJECT_ID"],
			"PROPERTY_COMMENT_ID" => false,
			//"PROPERTY_STATUS" => $O_status_id
			//"!PROPERTY_USER_ID" => false,
			//"PROPERTY_USER_ID" => $arParams["USER_ID"],
			array(        
				"LOGIC" => "OR",        
				array("PROPERTY_STATUS" => $O_status_id),        
				array("PROPERTY_USER_ID" => $arParams['USER_ID'], "!PROPERTY_STATUS" => $S_status_id),    
			),
		);
		
		$arResult["SECTION"]= false;
		
	
		//ORDER BY
		$arSort = array(
			$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
			$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
		);
		if(!array_key_exists("ID", $arSort))
			$arSort["ID"] = "DESC";

		$arResult["ITEMS"] = array();
		
		$rsElement = CIBlockElement::GetList($arSort, array_merge($arFilter, $arrFilter), false, $arNavParams, $arSelect);
		$rsElement->SetUrlTemplates($arParams["DETAIL_URL"], "", $arParams["IBLOCK_URL"]);
		while($obElement = $rsElement->GetNextElement())
		{
			$arItem = $obElement->GetFields();
			
			if(strlen($arItem["ACTIVE_FROM"])>0)
				$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
			else
				$arItem["DISPLAY_ACTIVE_FROM"] = "";
			
			$arItem["LEFT_MARGIN"] = 0;			
			$arItem["PROPERTIES"] = $obElement->GetProperties();
			
			$items[] = $arItem;
		}
		
		foreach($items as $item){
			$item["SUB_ITEMS"] = $this->GetSubElems($item, $item["LEFT_MARGIN"]+$arParams['DEFAULT_LEFT_MARGIN'], $arSelect, $arSort);
			
			$item["SUB_ITEMS_COUNT"] = $this->GetCountSubElems($item["SUB_ITEMS"]);
			$arResult["ITEMS"][] = $item;
		}
		
		$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
		$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
		$arResult["NAV_RESULT"] = $rsElement;
		$this->SetResultCacheKeys(array(
			"ID",
			"NAV_CACHED_DATA",
			"AUTH_SERVICES",
			"CURRENT_SERVICE",
			"AUTH_URL",
			"POST"
		));
				
		$arParams['PAGE'] = $APPLICATION->GetCurPageParam('', array("parent"));
		$arParams['FIRST_PAGE'] = $APPLICATION->GetCurPageParam('', array("PAGEN_1", "PAGEN_2"));
		
		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->AbortResultCache();
		ShowError(GetMessage("T_NEWS_NEWS_NA"));
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}
?>