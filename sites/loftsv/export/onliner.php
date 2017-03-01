<?php
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

if(!$GLOBALS['USER']->IsAdmin()){
	die;
}

if( !$_REQUEST['process'] ):?>
	<div>Получить файл выгрузки вы можете по ссылке <a href='http://<?=$_SERVER['HTTP_HOST']?>/export/onliner.xml'>http://<?=$_SERVER['HTTP_HOST']?>/export/onliner.xml</a></div>
	<p>Хотите сформировать новый файл? <p>
	<form>
		<input type="hidden" name="process" value="1">
		<input type="submit" name="make" value="Хочу">
	</form>
	<?die;?>
<?endif;
// global $modelfilter;
// $modelfilter=array("!=PROPERTY_ONLINER_MODEL" => false,'>CATALOG_QUANTITY'=>0);

$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"onliner", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "4",
		"ELEMENT_SORT_FIELD" => "id",
		"ELEMENT_SORT_ORDER" => "desc",
		"ELEMENT_SORT_FIELD2" => "",
		"ELEMENT_SORT_ORDER2" => "",
		"PROPERTY_CODE" => array("CATALOG_PRICE"),
		"META_KEYWORDS" => "",
		"META_DESCRIPTION" => "",
		"BROWSER_TITLE" => "-",
		"SET_LAST_MODIFIED" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"BASKET_URL" => "",
		"ACTION_VARIABLE" => "",
		"PRODUCT_ID_VARIABLE" => "",
		"SECTION_ID_VARIABLE" => "",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PRODUCT_PROPS_VARIABLE" => "",
		"FILTER_NAME" => "",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "1",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"SET_TITLE" => "N",
		"MESSAGE_404" => "",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"FILE_404" => "",
		"DISPLAY_COMPARE" => "N",
		"PAGE_ELEMENT_COUNT" => "500",
		"LINE_ELEMENT_COUNT" => "",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "",
		"PRICE_VAT_INCLUDE" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"ADD_PROPERTIES_TO_BASKET" => "N",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_BASE_LINK" => "",
		"PAGER_PARAMS_NAME" => "",
		"OFFERS_CART_PROPERTIES" => "",
		"OFFERS_FIELD_CODE" => "",
		"OFFERS_PROPERTY_CODE" => "",
		"OFFERS_SORT_FIELD" => "",
		"OFFERS_SORT_ORDER" => '',
		"OFFERS_SORT_FIELD2" => "",
		"OFFERS_SORT_ORDER2" => "",
		"OFFERS_LIMIT" => "0",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"CONVERT_CURRENCY" => "N",
		"CURRENCY_ID" => "BYN",
		"HIDE_NOT_AVAILABLE" => "N",
		"LABEL_PROP" => "SUVENIR_NAZNACH",
		"ADD_PICT_PROP" => "FILES",
		"PRODUCT_DISPLAY_MODE" => "",
		"OFFER_ADD_PICT_PROP" => "",
		"OFFER_TREE_PROPS" => "",
		"PRODUCT_SUBSCRIPTION" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "N",
		"MESS_BTN_BUY" => "",
		"MESS_BTN_ADD_TO_BASKET" => "",
		"MESS_BTN_SUBSCRIBE" => "",
		"MESS_BTN_DETAIL" => "",
		"MESS_NOT_AVAILABLE" => "",
		"TEMPLATE_THEME" => "",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"SHOW_CLOSE_POPUP" => "N",
		"COMPARE_PATH" => "",
		"BACKGROUND_IMAGE" => "",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"COMPONENT_TEMPLATE" => "onliner",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SHOW_ALL_WO_SECTION" => "Y",
		"MESS_BTN_COMPARE" => "",
		"SEF_MODE" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "Y",
		"SET_META_DESCRIPTION" => "N"
	),
	false
);
