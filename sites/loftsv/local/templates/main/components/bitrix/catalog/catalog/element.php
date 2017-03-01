<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$this->setFrameMode(true);
// p($arResult);die;
?>

<!-- Start page content -->
<section id="page-content" class="page-wrapper">
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element", 
	"main", 
	array(
		"COMPONENT_TEMPLATE" => "main",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => IBLOCK_CATALOG_ID,
		"PROPERTY_CODE" => $GLOBALS['PROPERTY_CODES'],
		"OFFERS_LIMIT" => "0",
		"BACKGROUND_IMAGE" => "-",
		"TEMPLATE_THEME" => "",
		"ADD_PICT_PROP" => "-",
		"LABEL_PROP" => "-",
		"DISPLAY_NAME" => "Y",
		"DETAIL_PICTURE_MODE" => "IMG",
		"ADD_DETAIL_TO_SLIDER" => "N",
		"DISPLAY_PREVIEW_TEXT_MODE" => "E",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_BTN_COMPARE" => "Сравнить",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"USE_VOTE_RATING" => "Y",
		"USE_COMMENTS" => "N",
		"BRAND_USE" => "N",
		"SECTION_URL" => "/#SECTION_CODE#/",
		"DETAIL_URL" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"CHECK_SECTION_ID_VARIABLE" => "N",
		"SEF_MODE" => "Y",
		"SEF_RULE" => "#SECTION_CODE#",
		"ELEMENT_ID" => "",
		"ELEMENT_CODE" => $arResult['VARIABLES']['ELEMENT_CODE'],
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"SECTION_CODE_PATH" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "N",
		"SET_TITLE" => "Y",
		"SET_CANONICAL_URL" => "N",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_LAST_MODIFIED" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"VOTE_DISPLAY_AS_RATING" => "rating",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"DISPLAY_COMPARE" => "N",
		"PRICE_CODE" => array(
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"BASKET_URL" => "/personal/basket.php",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"ADD_PROPERTIES_TO_BASKET" => "N",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "",
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "Y",
		"MESSAGE_404" => "",
		"USE_ELEMENT_COUNTER" => "Y",
		"SHOW_DEACTIVATED" => "N",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"FILE_404" => ""
	),
	false
);?>
</section>