<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])) {
	if ($arResult['OFFER_GROUP']) { foreach ($arResult['OFFERS'] as $arOffer) { if (!$arOffer['OFFER_GROUP']) continue;
	?>
	<span id="<? echo $arItemIDs['OFFER_GROUP'].$arOffer['ID']; ?>" style="display: none;">
			<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "main",
				array(
					"IBLOCK_ID" => $arResult["OFFERS_IBLOCK"],
					"ELEMENT_ID" => $arOffer['ID'],
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"BASKET_URL" => $arParams["BASKET_URL"],
					"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				), $component, array("HIDE_ICONS" => "Y")
			);?>
	</span>
<?}}} else {?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "main",
			array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_ID" => $arResult["ID"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			), $component, array("HIDE_ICONS" => "Y")
		);?>
<?}?>