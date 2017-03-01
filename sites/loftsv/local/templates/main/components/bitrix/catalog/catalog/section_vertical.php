<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager,
	Bitrix\Main\Context;

$request = Context::getCurrent()->getRequest();

if( !isset($_SESSION['goods_order']) ){
	$_SESSION['goods_order'] = new \stdClass();
	$_SESSION['goods_order']->val = 'price-asc';
	$_SESSION['goods_order']->order = 'asc';
	$_SESSION['goods_order']->field = 'PROPERTY_CATALOG_PRICE';
}

if( isset($_REQUEST['order']) ){
	if( preg_match("/(price|name)-(asc|desc)/", $_REQUEST['order'], $m) ){
		$_SESSION['goods_order']->val = $m[0];
		if( $m[1] == 'price' ){
			$_SESSION['goods_order']->field = 'PROPERTY_CATALOG_PRICE';
		} elseif( $m[1] == 'name' ){
			$_SESSION['goods_order']->field = 'NAME';
		}
		$_SESSION['goods_order']->order = $m[2];
	}
}
?>

<!-- Start page content -->
<div id="page-content" class="page-wrapper">

<!-- SHOP SECTION START -->
<div class="shop-section mb-80">
    <div class="container">
        <div class="row">
			<div class="col-md-3 col-sm-12">
            <?$APPLICATION->IncludeComponent(
				"bitrix:catalog.smart.filter",
				"",
				array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"SECTION_ID" => $arCurSection['ID'],
					"FILTER_NAME" => $arParams["FILTER_NAME"],
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"SAVE_IN_SESSION" => "N",
					"FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
					"XML_EXPORT" => "Y",
					"SECTION_TITLE" => "NAME",
					"SECTION_DESCRIPTION" => "DESCRIPTION",
					'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
					"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
					'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
					'CURRENCY_ID' => $arParams['CURRENCY_ID'],
					"SEF_MODE" => "N",//$arParams["SEF_MODE"],
					"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
					"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
					"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
					"INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
				),
				$component,
				array('HIDE_ICONS' => 'Y')
			);?>
        </div>

            <div class="col-md-9 col-sm-12">
                <div class="shop-content">
                    <!-- shop-option start -->
                    <div class="shop-option box-shadow mb-30 clearfix">
                        <div class="short-by f-left text-center">
                            <span>Сортировать по:</span>
                            <select class="order-change" name="order">
                                <option value="price-asc" <?=($_SESSION['goods_order']->val == "price-asc")?'selected':'';?>>Возростанию цены</option>
                                <option value="price-desc" <?=($_SESSION['goods_order']->val == "price-desc")?'selected':'';?>>Убыванию цены</option>
                                <option value="name-asc" <?=($_SESSION['goods_order']->val == "name-asc")?'selected':'';?>>Наименованию А-Я</option>
                                <option value="name-desc" <?=($_SESSION['goods_order']->val == "name-desc")?'selected':'';?>>Наименованию Я-А</option>
                            </select>
                        </div> 
                        <!-- showing -->
                        <div class="showing f-right text-right">
<?if($arParams['SECTION_COUNT_ELEMENTS'] == "Y"){
	// $cnt = Studio8\Main\Helpers::_countItemInSection(array_merge($GLOBALS[$arParams["FILTER_NAME"]], ["IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y"]));
	
	echo '<span class="cnt-items">Всего товаров: '.$cnt.'</span>';
}
?>
                        </div>                                   
                    </div>
                    <!-- shop-option end -->

					<!-- Tab Content start -->
					<div class="tab-content">
					    <!-- grid-view -->
					    <div role="tabpanel" class="tab-pane active" id="grid-view">
					        <div class="row">
					            <!-- product-item start -->
<?$intSectionID = $APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		"products",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => $_SESSION['goods_order']->field,
			"ELEMENT_SORT_ORDER" => $_SESSION['goods_order']->order,
			"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
			"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
			"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
			"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
			"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
			"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
			"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
			"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"FILTER_NAME" => $arParams["FILTER_NAME"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_FILTER" => $arParams["CACHE_FILTER"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"MESSAGE_404" => $arParams["MESSAGE_404"],
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"SHOW_404" => $arParams["SHOW_404"],
			"FILE_404" => $arParams["FILE_404"],
			"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
			"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
			"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
			"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
			"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

			"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arParams["PAGER_TITLE"],
			"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
			"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
			"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
			"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
			"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
			"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
			"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
			"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],

			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
			"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
			'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
			'CURRENCY_ID' => $arParams['CURRENCY_ID'],
			'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

			'LABEL_PROP' => $arParams['LABEL_PROP'],
			'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
			'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

			'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
			'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
			'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
			'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
			'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
			'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
			'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
			'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

			'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
			"ADD_SECTIONS_CHAIN" => "N",
			'ADD_TO_BASKET_ACTION' => $basketAction,
			'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
			'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
			'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
			'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : '')
		),
		$component
	);?>
		</div>
	</div>
	</div>

	<div class="pagination-block"></div>

		</div>
	</div>

	</div>
	</div>
	</div>



</div>

<script type="text/javascript">
	Studio8.Widget('productsList', {
		init: function(){
			$('.cnt-items').text('Показаны товары: ' + $('.hidden .start-item').text() + ' - ' + $('.hidden .stop-item').text() + ' из ' + $('.hidden .all-items').text());
			$('.pagination-block').append($('.hidden.shop-pagination').removeClass('hidden'));
		}
	});
</script>

<?
if($arParams["SET_TITLE"])
{
	// if ($arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "")
		// $APPLICATION->SetTitle($arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $arTitleOptions);
	// elseif(isset($arResult["NAME"]))
		// $APPLICATION->SetTitle("Rfnfkju");
}
/*
if ($arParams["SET_BROWSER_TITLE"] === 'Y')
{
	$browserTitle = \Bitrix\Main\Type\Collection::firstNotEmpty(
		$arResult, $arParams["BROWSER_TITLE"]
		,$arResult["IPROPERTY_VALUES"], "SECTION_META_TITLE"
	);
	if (is_array($browserTitle))
		$APPLICATION->SetPageProperty("title", implode(" ", $browserTitle), $arTitleOptions);
	elseif ($browserTitle != "")
		$APPLICATION->SetPageProperty("title", $browserTitle, $arTitleOptions);
}*/?>