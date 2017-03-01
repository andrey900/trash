<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "В нашем интернет магазине собраны светильники в стиле Loft и также люстры loft. Стиль Loft знанял очень крепкие позиции в дизайне интерьеров. Выбрать освещение Лофт стало проще!!!");
$APPLICATION->SetPageProperty("keywords", "Люстры loft, светильники Loft, интернет магазин лофт");
$APPLICATION->SetPageProperty("title", "Новинки люстр и светильников в стиле Loft, интернет магазин освещения в стиле лофт - loftsvet.by");
$APPLICATION->SetTitle("Освещение в стиле Лофт");
?>

<!-- Start page content -->
<section id="page-content" class="page-wrapper">
	<div class="container">
		<div class="row">

		<h1 class="text-center">Новинки на сайте</h1>

<?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section", 
    "products", 
    array(
        "COMPONENT_TEMPLATE" => "products",
        "IBLOCK_TYPE" => IBLOCK_CATALOG_TYPE,
        "IBLOCK_ID" => IBLOCK_CATALOG_ID,
        "SECTION_ID" => "",
        "SECTION_CODE" => "",
        "SECTION_USER_FIELDS" => array(
            0 => "",
            1 => "",
        ),
        "ELEMENT_SORT_FIELD" => "DATE_CREATED",
        "ELEMENT_SORT_ORDER" => "desc",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER2" => "desc",
        "FILTER_NAME" => "",
        "INCLUDE_SUBSECTIONS" => "A",
        "SHOW_ALL_WO_SECTION" => "N",
        "PAGE_ELEMENT_COUNT" => "52",
        "LINE_ELEMENT_COUNT" => "4",
        "PROPERTY_CODE" => array(
            0 => "ARTICLE",
            1 => "BRAND",
            2 => "CATALOG_QUANTITY",
            3 => "COLLECTION",
            4 => "POWER",
            5 => "COLOR",
            6 => "CATALOG_PRICE",
            7 => "",
        ),
        "OFFERS_LIMIT" => "1",
        "BACKGROUND_IMAGE" => "-",
        "TEMPLATE_THEME" => "blue",
        "ADD_PICT_PROP" => "-",
        "LABEL_PROP" => "-",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "SECTION_URL" => "",
        "DETAIL_URL" => "",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SEF_MODE" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
        "CACHE_GROUPS" => "N",
        "SET_TITLE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "BROWSER_TITLE" => "-",
        "SET_META_KEYWORDS" => "N",
        "META_KEYWORDS" => "-",
        "SET_META_DESCRIPTION" => "N",
        "META_DESCRIPTION" => "-",
        "SET_LAST_MODIFIED" => "N",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "ADD_SECTIONS_CHAIN" => "N",
        "CACHE_FILTER" => "N",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRICE_CODE" => array(
        ),
        "USE_PRICE_COUNT" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "PRICE_VAT_INCLUDE" => "N",
        "BASKET_URL" => "/personal/basket.php",
        "USE_PRODUCT_QUANTITY" => "N",
        "PRODUCT_QUANTITY_VARIABLE" => "",
        "ADD_PROPERTIES_TO_BASKET" => "N",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRODUCT_PROPERTIES" => array(
        ),
        "PAGER_TEMPLATE" => ".default",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "PAGER_TITLE" => "Товары",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "SET_STATUS_404" => "N",
        "SHOW_404" => "N",
        "MESSAGE_404" => "",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N"
    ),
    false
);?>
		</div>
	</div>
</section>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>