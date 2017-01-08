<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
?>
<?if($arParams["USE_REVIEW"]=="Y" && IsModuleInstalled("forum") && $arResult["ID"]):?>
	<?php /*?><div id="reviews_content">
		<?$APPLICATION->IncludeComponent(
			"bitrix:forum.topic.reviews",
			"element_reviews",
			Array(
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
				"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
				"FORUM_ID" => $arParams["FORUM_ID"],
				"ELEMENT_ID" => $arResult["ID"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
				"SHOW_RATING" => "N",
				"SHOW_MINIMIZED" => "Y",
				"SECTION_REVIEW" => "Y",
				"POST_FIRST_MESSAGE" => "Y",
				"MINIMIZED_MINIMIZE_TEXT" => GetMessage("HIDE_FORM"),
				"MINIMIZED_EXPAND_TEXT" => GetMessage("ADD_REVIEW"),
				"SHOW_AVATAR" => "N",
				"SHOW_LINK_TO_FORUM" => "N",
				"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
			),	false
		);?>
	</div>
	<?*/?>
	<?if ($arParams["SHOW_COMPARE"]):?>
		<div class="compare" id="compare">
			<?$APPLICATION->IncludeComponent(
				"bitrix:catalog.compare.list",
				"preview",
				Array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
					"COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
					"NAME" => "CATALOG_COMPARE_LIST",
					"AJAX_OPTION_ADDITIONAL" => ""
				)
			);?>
		</div>
	<?endif;?>
	<?if (($arParams["SHOW_ASK_BLOCK"]=="Y")&&(intVal($arParams["ASK_FORM_ID"]))):?>
		<div id="ask_block_content">
			<?$APPLICATION->IncludeComponent(
				"bitrix:form.result.new",
				"inline",
				Array(
					"WEB_FORM_ID" => $arParams["ASK_FORM_ID"],
					"IGNORE_CUSTOM_TEMPLATE" => "N",
					"USE_EXTENDED_ERRORS" => "N",
					"SEF_MODE" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "3600",
					"LIST_URL" => "",
					"EDIT_URL" => "",
					"SUCCESS_URL" => "?send=ok",
					"CHAIN_ITEM_TEXT" => "",
					"CHAIN_ITEM_LINK" => "",
					"VARIABLE_ALIASES" => Array("WEB_FORM_ID" => "WEB_FORM_ID", "RESULT_ID" => "RESULT_ID")
				)
			);?>
		</div>
	<?endif;?>
	<script>
		if ($(".specials_tabs_section.specials_slider_wrapp").length && $("#reviews_content").length) 
			{ $("#reviews_content").after($(".specials_tabs_section.specials_slider_wrapp")); }
		if ($("#ask_block_content").length && $("#ask_block").length) 
			{ $("#ask_block").html($("#ask_block_content").html()); $("#ask_block_content").remove(); }		
		if ($("#reviews_content").length && !$(".tabs_section ul.tabs_content li.cur").length) 
			{ $(".shadow.common").hide(); $("#reviews_content").show(); }
	</script>
<?endif?>


<? /* Отзывы */?>
<div id="preview_reviews_content">
<?
	$APPLICATION->IncludeComponent(
	"aniart:reviews", 
	"preview_list", 
	array(
		"AJAX_MODE" => "Y",
		"IBLOCK_TYPE" => "aspro_kshop_content",
		"IBLOCK_ID" => "25",
		"NEWS_COUNT" => "2",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "reviewsFilter",
		"FIELD_CODE" => array(
			0 => "DATE_ACTIVE_FROM",
			1 => "ACTIVE_FROM",
			2 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "USER_ID",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000",
		"CACHE_NOTES" => "",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y H:i",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Комментарии",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"OBJECT_ID" => $arResult['ID'],
		"CAN_USER_DEL_REVIEW" => "N"
	),
	false
	);
?>
</div>

<? /* Аксессуары */?>
<div id="expandables_content">
	<?$GLOBALS['arrFilterExpandables'] = array( "ID" => $arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"] );?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.top", "product_list_acsessuars", array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => "PROPERTY_ON_SITE",
		"ELEMENT_SORT_ORDER" => "asc,nulls",//$arParams["ELEMENT_SORT_ORDER"],
		"ELEMENT_SORT_FIELD2" => "PROPERTY_IS_PRICE",//$arParams["ELEMENT_SORT_FIELD2"],
		"ELEMENT_SORT_ORDER2" => "asc,nulls",//$arParams["ELEMENT_SORT_ORDER2"],
		"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
		"ELEMENT_COUNT" => "200",
		"LINE_ELEMENT_COUNT" => "4",
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
		"SECTION_URL" => $arParams["SECTION_URL"],
		"DETAIL_URL" => $arParams["DETAIL_URL"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
		"USE_PRODUCT_QUANTITY" =>$arParams["USE_PRODUCT_QUANTITY"],
		"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
		"CURRENCY_ID" => $arParams["CURRENCY_ID"],
		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"FILTER_NAME" => "arrFilterExpandables",
		"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
		),
		false
	);?> 
</div>

<? /* Аксессуары small */?>
<div id="expandables_small_content">
	<?$GLOBALS['arrFilterExpandables'] = array( "ID" => $arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"] );?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.top", "product_list_small_acsessuars", array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => "PROPERTY_ON_SITE",
		"ELEMENT_SORT_ORDER" => "asc,nulls",//$arParams["ELEMENT_SORT_ORDER"],
		"ELEMENT_SORT_FIELD2" => "PROPERTY_IS_PRICE",//$arParams["ELEMENT_SORT_FIELD2"],
		"ELEMENT_SORT_ORDER2" => "asc,nulls",//$arParams["ELEMENT_SORT_ORDER2"],
		"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
		"ELEMENT_COUNT" => "2",
		"LINE_ELEMENT_COUNT" => "2",
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
		"SECTION_URL" => $arParams["SECTION_URL"],
		"DETAIL_URL" => $arParams["DETAIL_URL"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_COMPARE" => "N",
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
		"USE_PRODUCT_QUANTITY" =>$arParams["USE_PRODUCT_QUANTITY"],
		"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
		"CURRENCY_ID" => $arParams["CURRENCY_ID"],
		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"FILTER_NAME" => "arrFilterExpandables",
		"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
		"DISPLAY_WISH_BUTTONS" => "N"
		),
		false
	);?>  
</div>

<script>
	if ($("#preview_reviews_content").length && $("#preview_reviews_content_block").length) 
		{ $("#preview_reviews_content_block").html($("#preview_reviews_content").html()); $("#preview_reviews_content").remove();}

	if ($("#expandables_content").length && $("#expandables_block").length) 
		{ $("#expandables_block").html($("#expandables_content").html()); $("#expandables_content").remove();}

	if ($("#expandables_small_content").length && $("#expandables_small_block").length) 
		{ $("#expandables_small_block").html($("#expandables_small_content").html()); $("#expandables_small_content").remove();}
</script>

<?
// set og:meta
//p($arResult['IPROPERTY_VALUES']);
//p($arResult);

$APPLICATION->SetPageProperty('og:type', 'product');
$APPLICATION->SetPageProperty('og:title', $arResult['IPROPERTY_VALUES']['ELEMENT_META_TITLE']);
$APPLICATION->SetPageProperty('og:description', $arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION']);
$image = ( !empty($arResult['DETAIL_PICTURE']) )?$arResult['DETAIL_PICTURE']['SRC']:$arResult['PREVIEW_PICTURE']['SRC'];
$APPLICATION->SetPageProperty('og:image', 'http://'.$_SERVER['HTTP_HOST'].$image);
$APPLICATION->SetPageProperty('og:url', 'http://'.$_SERVER['HTTP_HOST'].$arResult['DETAIL_PAGE_URL']);
