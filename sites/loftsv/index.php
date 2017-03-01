<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "В нашем интернет магазине собраны светильники в стиле Loft и также люстры loft. Стиль Loft знанял очень крепкие позиции в дизайне интерьеров. Выбрать освещение Лофт стало проще!!!");
$APPLICATION->SetPageProperty("keywords", "Люстры loft, светильники Loft, интернет магазин лофт");
$APPLICATION->SetPageProperty("title", "Люстры и светильники в стиле Loft, освещение в стиле Лофт - loftsvet.by");
$APPLICATION->SetTitle("Освещение в стиле Лофт");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:news.line", 
	"slider3", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"DETAIL_URL" => "",
		"FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_TEXT",
			2 => "PREVIEW_PICTURE",
			3 => "",
		),
		"IBLOCKS" => array(
			0 => "1",
		),
		"IBLOCK_TYPE" => "content",
		"NEWS_COUNT" => "4",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"SHOW_DIRECTION" => "N",
		"COMPONENT_TEMPLATE" => "slider2"
	),
	false
);?>

        <!-- Start page content -->
        <section id="page-content" class="page-wrapper">
            <?$APPLICATION->IncludeComponent(
	"bitrix:news.line", 
	"brands", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000",
		"CACHE_TYPE" => "A",
		"DETAIL_URL" => "",
		"FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_TEXT",
			2 => "PREVIEW_PICTURE",
			3 => "",
		),
		"IBLOCKS" => array(
			0 => "3",
		),
		"IBLOCK_TYPE" => "content",
		"NEWS_COUNT" => "7",
		"SORT_BY1" => "NAME",
		"SORT_BY2" => "ID",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "DESC",
		"SHOW_NAME" => "N",
		"COMPONENT_TEMPLATE" => "brands"
	),
	false
);?>
            
<!-- PRODUCT TAB SECTION START -->
            <div class="product-tab-section mb-50">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="section-title text-left mb-40">
                                <h2 class="uppercase"><?$APPLICATION->IncludeFile(
    SITE_DIR."include/home/product_list_title.php",
    Array(),
    Array("MODE"=>"html", "NAME" => "заголовок")
);?></h2>
                                <p class="h6"><?$APPLICATION->IncludeFile(
    SITE_DIR."include/home/product_list_description.php",
    Array(),
    Array("MODE"=>"html", "NAME" => "заголовок")
);?></p>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="pro-tab-menu text-right">
                                <!-- Nav tabs -->
                                <ul class="" >
                                    <li><a href="#popular-product" data-toggle="tab">Популярное</a></li>
                                    <li class="active"><a href="#new-arrival" data-toggle="tab">Новинки</a></li>
                                    <li><a href="#special-offer"  data-toggle="tab">Специальные предложения</a></li>
                                </ul>
                            </div>                       
                        </div>
                    </div>
                    <div class="product-tab">
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!-- popular-product start -->
                            <div class="tab-pane" id="popular-product">
                                <div class="row">
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
        "ELEMENT_SORT_FIELD" => "name",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER2" => "desc",
        "FILTER_NAME" => "",
        "INCLUDE_SUBSECTIONS" => "A",
        "SHOW_ALL_WO_SECTION" => "N",
        "PAGE_ELEMENT_COUNT" => "8",
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
                            <!-- popular-product end -->
                            <!-- new-arrival start -->
                            <div class="tab-pane active" id="new-arrival">
                                <div class="row">
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
        "ELEMENT_SORT_FIELD" => "date_create",
        "ELEMENT_SORT_ORDER" => "desc",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER2" => "desc",
        "FILTER_NAME" => "",
        "INCLUDE_SUBSECTIONS" => "A",
        "SHOW_ALL_WO_SECTION" => "N",
        "PAGE_ELEMENT_COUNT" => "8",
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
    <p class="text-right" style="font-size:16px;"><a href="/novelty/"><u>Посмотреть все новинки</u></a></p>
                                </div>                                        
                            </div>
                            <!-- new-arrival end -->
                            <!-- special-offer start -->
                            <div class="tab-pane" id="special-offer">
                                <div class="row">
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
        "ELEMENT_SORT_FIELD" => "name",
        "ELEMENT_SORT_ORDER" => "desc",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER2" => "desc",
        "FILTER_NAME" => "",
        "INCLUDE_SUBSECTIONS" => "A",
        "SHOW_ALL_WO_SECTION" => "N",
        "PAGE_ELEMENT_COUNT" => "8",
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
                            <!-- special-offer end -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- PRODUCT TAB SECTION END -->


<?php
$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "articles",
    Array(
        "IBLOCK_TYPE" => IBLOCK_CONTENT_TYPE,
        "IBLOCK_ID" => IBLOCK_ARTICLES_ID,
        "NEWS_COUNT" => 4,

        "SORT_BY1" => "ID",
        "SORT_ORDER1" => "DESC",
        "SORT_BY2" => "NAME",
        "SORT_ORDER2" => "ASC",

        "FILTER_NAME" => "N",
        "FIELD_CODE" => array(),
        "PROPERTY_CODE" => array(),
        "CHECK_DATES" => "Y",
        "IBLOCK_URL" => "/articles/",
        "SECTION_URL" => "/articles/",
        "DETAIL_URL" => "/articles/#ELEMENT_ID#/",
        "SEARCH_PAGE" => "",

        "CACHE_TYPE" => "A",
        "CACHE_TIME" => 36000,
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "N",

        "PREVIEW_TRUNCATE_LEN" => "120",
        "ACTIVE_DATE_FORMAT" => "",
        "SET_TITLE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_META_DESCRIPTION" => "N",
        "MESSAGE_404" => "",
        "SET_STATUS_404" => "N",
        "SHOW_404" => "N",
        "FILE_404" => "N",
        "SET_LAST_MODIFIED" => "N",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "ADD_SECTIONS_CHAIN" => "N",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",

        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "INCLUDE_SUBSECTIONS" => "Y",

        "DISPLAY_DATE" => "N",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "MEDIA_PROPERTY" => "",
        "SLIDER_PROPERTY" => "Y",

        "PAGER_TEMPLATE" => "pagination",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "PAGER_TITLE" => "",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => 36000,
        "PAGER_SHOW_ALL" => "N",
        "PAGER_BASE_LINK_ENABLE" => "",
        "PAGER_BASE_LINK" => "",
        "PAGER_PARAMS_NAME" => "",

        "USE_RATING" => "N",
        "DISPLAY_AS_RATING" => "N",
        "MAX_VOTE" => 0,
        "VOTE_NAMES" => "",

        "USE_SHARE" => "N",
        "SHARE_HIDE" => "",
        "SHARE_TEMPLATE" => "",
        "SHARE_HANDLERS" => "",
        "SHARE_SHORTEN_URL_LOGIN" => "",
        "SHARE_SHORTEN_URL_KEY" => "",

        "TEMPLATE_THEME" => "",
    ),
    false,
    array(
        "ACTIVE_COMPONENT"=>"N"
    )
);?>
        </section>
        <!-- End page content -->

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>