<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if( count( $arResult["ITEMS"] ) >= 1 ){?>
	<?
	/*$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
	$arNotify = unserialize($notifyOption);*/
	?>
	<div class="display_list">
		<?foreach($arResult["ITEMS"] as $arItem){
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
			$totalCount = CKShop::GetTotalCount($arItem);
			$arQuantityData = CKShop::GetQuantityArray($totalCount);
			$arAddToBasketData = CKShop::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"]);
			?>
			<?			
			if (($arParams["SHOW_MEASURE"]=="Y")&&($arItem["CATALOG_MEASURE"]))
			{ $arMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arItem["CATALOG_MEASURE"]), false, false, array())->GetNext(); }
			?>
			<div class="list_item_wrapp" itemscope itemtype="http://schema.org/Offer">
				<table class="list_item" id="<?=$this->GetEditAreaId($arItem['ID']);?>" cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr class="adaptive_name">
						<td colspan="2">
							<div class="desc_name"><a itemprop="url" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span itemprop="name"><?=$arItem["NAME"]?></span></a></div>
						</td>
					</tr>
					<tr>
					<td class="image">
						<div class="ribbons">
							<?if (is_array($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
								<?if( in_array("HIT", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) ):?><span class="ribon_hit"></span><?endif;?>
								<?if( in_array("RECOMMEND", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="ribon_recomend"></span><?endif;?>
								<?if( in_array("NEW", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="ribon_new"></span><?endif;?>
								<?/*if( in_array("STOCK", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="ribon_action"></span><?endif;*/?>
								<?if( in_array("XML_GIFT", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="ribon_present"></span><?endif;?>
							<?endif;?>
							<?if($arItem["MIN_PRODUCT_DISCOUNT_PRICE"]):?>
								<span class="ribon_action"></span>
							<?endif;?>
						</div>
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb">
							<?if( !empty($arItem["PREVIEW_PICTURE"]) ):?>
								<img itemprop="image" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
							<?elseif( !empty($arItem["DETAIL_PICTURE"])):?>
								<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 170, "height" => 170 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
								<img itemprop="image" border="0" src="<?=$img["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />		
							<?else:?>
								<img itemprop="image" border="0" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
							<?endif;?>
						</a>
					</td>
					
					<td class="description_wrapp">
						<div class="description">
							<div class="desc_name">
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span><?=$arItem["NAME"]?></span></a>
								<?//aniart?>
								<div class="good_id">ID#<span class="product-article" item-id="<?=$arItem['ID']?>"><?=$arItem['ID']?></span></div>
								<?//end aniart?>
							</div>
							
							<?if ($arParams["USE_RATING"]=="Y"){?>
								<div class="rating">
									<?$APPLICATION->IncludeComponent( "bitrix:iblock.vote", "element_rating",
									   Array(
										  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
										  "IBLOCK_ID" => $arResult["IBLOCK_ID"],
										  "ELEMENT_ID" =>$arResult["ID"],
										  "MAX_VOTE" => 5,
										  "VOTE_NAMES" => array(),
										  "CACHE_TYPE" => $arParams["CACHE_TYPE"],
										  "CACHE_TIME" => $arParams["CACHE_TIME"],
										  "DISPLAY_AS_RATING" => 'vote_avg'
									   ),  $component, array("HIDE_ICONS" =>"Y")
									);?>
								</div>
							<?}?>
							<?if ($arItem["PREVIEW_TEXT"] && $arItem["PREVIEW_TEXT"] != $arItem["NAME"]):?> <div class="preview_text"><?=$arItem["PREVIEW_TEXT"]?></div> <?endif;?>
							<?if ($arItem["DISPLAY_PROPERTIES"]):?>
								<div class="show_props">
									<a><i class="icon"><b></b></i><span class="pseudo"><?=GetMessage('PROPERTIES')?></span></a>
								</div>
								<div class="props_list_wrapp">
									<table class="props_list">
										<?foreach( $arItem["DISPLAY_PROPERTIES"] as $arProp ){?>
											<?if( !empty( $arProp["VALUE"] ) ){?>
												<tr>
													<td><?=$arProp["NAME"]?>:</td>
													<td>
														<?
														if(count($arProp["DISPLAY_VALUE"])>1) { foreach($arProp["DISPLAY_VALUE"] as $key => $value) { if ($arProp["DISPLAY_VALUE"][$key+1]) {echo $value.", ";} else {echo $value;} }} 
														else { echo $arProp["DISPLAY_VALUE"]; }
														?>
													</td>
												</tr>
											<?}?>
										<?}?>
									</table>
								</div>
							<?endif;?>
						</div>
					</td>

					<td class="information_wrapp">
						<div class="information">
							<?if(strlen($arQuantityData["TEXT"])):?>
								<div class="available_block">
									<div class="availability-row"><?=$arQuantityData["HTML"]?></div>
								</div>
							<?endif;?>

							<div class="price_block">
								<?if( count( $arItem["OFFERS"] ) > 0 ){?>
									<div class="price"><?=GetMessage("CATALOG_FROM");?> <?=$arItem["MIN_PRODUCT_OFFER_PRICE_PRINT"]?></div>
								<?}else{?>
									<?
									$arCountPricesCanAccess = 0;
									foreach( $arItem["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} }
									?>
									<?foreach( $arItem["PRICES"] as $key => $arPrice ){?>
										<?if($arPrice["CAN_ACCESS"]){?>
											<?$price = CPrice::GetByID($arPrice["ID"]); ?>
											<?if($arCountPricesCanAccess>1):?><div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div><?endif;?>
												<?$prefix = count( $arItem["OFFERS"] ) > 1 ? GetMessage("CATALOG_FROM").'&nbsp;' : '';?>
												<?if( $arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] ){?>
													<div itemprop="price" class="price"><?=$prefix?><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></div>
													<div class="price discount"><?=GetMessage("WITHOUT_DISCOUNT")?>: <?=$prefix?><strike><?=$arPrice["PRINT_VALUE"]?></strike></div>
												<?}else{?>
													<div itemprop="price" class="price"><?=$prefix?><?=$arPrice["PRINT_VALUE"]?></div>
												<?}?>
										<?}?>
									<?}?>
								<?}?>
							</div>
							
							<div class="counter_block" data-item="<?=$arItem["ID"];?>">
								<?if($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && !count($arItem["OFFERS"]) && $arAddToBasketData["ACTION"] == "ADD"):?>
									<span class="minus">-</span>
									<input type="text" class="text" name="count_items" value="<?=($arParams["DEFAULT_COUNT"] > 0 ? $arParams["DEFAULT_COUNT"] : 1)?>" />
									<span class="plus">+</span>
								<?endif;?>
							</div>
						
							<?if(strlen($arQuantityData["TEXT"])):?>
								<div class="availability-row"><?=$arQuantityData["HTML"]?></div>
							<?endif;?>
						
							<div class="button_block">
								<!--noindex-->
									<?=$arAddToBasketData["HTML"]?>
									
								<?if($arAddToBasketData["ACTION"] == "ADD"):?>
									<a class="basket_button one_click" data-item="<?=$arItem["ID"]?>" data-quantity="<?=($totalCount >= $arParams["DEFAULT_COUNT"] ? $arParams["DEFAULT_COUNT"] : $totalCount)?>" onclick="oneClickBuy('<?=$arItem["ID"]?>', '<?=$arParams["IBLOCK_ID"]?>', this)">
										<span><?=GetMessage('ONE_CLICK_BUY')?></span>
									</a>
								<?endif;?>
								<!--/noindex-->
							</div>
							
							
							
							<?if($arItem["CAN_BUY"] && ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y")):?>
								<div class="likes_icons">
									<!--noindex-->
										<?if(empty($arItem["OFFERS"]) && $arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
											<a rel="nofollow" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arParams["IBLOCK_ID"]?>" class="wish_item">
												<span class="icon"><i></i></span>
												<span class="value pseudo"><?=GetMessage('CATALOG_WISH')?></span>
												<span class="value pseudo added"><?=GetMessage('CATALOG_WISH_ADDED')?></span>
											</a>
										<?endif;?>
										<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
											<a rel="nofollow" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arParams["IBLOCK_ID"]?>" href="<?=$arItem["COMPARE_URL"]?>" class="compare_item">
												<span class="icon"><i></i></span>
												<span class="value pseudo"><?=GetMessage('CATALOG_COMPARE')?></span>
												<span class="value pseudo added"><?=GetMessage('CATALOG_COMPARE_ADDED')?></span>
											</a>
										<?endif;?>
									<!--/noindex-->
								</div>
							<?endif;?>
						</div>
					</td></tr>
				</table>
			</div>
		<?}?>
	</div>
	<div class="bottom_nav">
		<?if( $arParams["DISPLAY_BOTTOM_PAGER"] == "Y" ){?><?=$arResult["NAV_STRING"]?><?}?>
		<?
			$show=$arParams["PAGE_ELEMENT_COUNT"];
			if (array_key_exists("show", $_REQUEST))
			{
				if ( intVal($_REQUEST["show"]) && in_array(intVal($_REQUEST["show"]), array(20, 40, 60, 80, 100)) ) {$show=intVal($_REQUEST["show"]); $_SESSION["show"] = $show;}
				elseif ($_SESSION["show"]) {$show=intVal($_SESSION["show"]);}
			}
		?>
		<div class="show_number">
			<span class="show_title"><?=GetMessage("CATALOG_DROP_TO")?></span>
			<span class="number_list">
				<?for( $i = 20; $i <= 100; $i+=20 ){?>
					<a rel="nofollow" <?if ($i == $show):?>class="current"<?endif;?> href="<?=$APPLICATION->GetCurPageParam('show='.$i, array('show', 'mode'))?>"><span><?=$i?></span></a>
				<?}?>
			</span>
		</div>
	</div>
<?}else{?>
	<p class="no_products"><?$APPLICATION->IncludeFile(SITE_DIR."include/section_no_products.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('EMPTY_CATALOG_DESCR')));?></p>
	<?$APPLICATION->IncludeComponent(
		"bitrix:catalog.section.list",
		"sections_list",
		Array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
			"TOP_DEPTH" => 2,
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"SHOW_SECTIONS_LIST_PREVIEW" => $arParams["SHOW_SECTIONS_LIST_PREVIEW"],
			"SECTIONS_LIST_PREVIEW_PROPERTY" => $arParams["SECTIONS_LIST_PREVIEW_PROPERTY"],
			"SHOW_SECTION_LIST_PICTURES" => $arParams["SHOW_SECTION_LIST_PICTURES"],
		), $component
	);?>
<?}?>


<?if ($arResult["~DESCRIPTION"]):?>
	<div class="group_description">
		<img class="shadow" src="<?=SITE_TEMPLATE_PATH?>/images/shadow_bottom.png" />
		<div><?=$arResult["~DESCRIPTION"]?></div>
	</div>
<?else:?>
	<?$arSection = CIBlockSection::GetList(array(), array( "IBLOCK_ID" => $arResult["IBLOCK_ID"], "ID" => $arResult["ID"] ), false, array( "ID", "UF_SECTION_DESCR"))->GetNext();?>
	<?if ($arSection["UF_SECTION_DESCR"]):?>
		<div class="group_description">
			<img class="shadow" src="<?=SITE_TEMPLATE_PATH?>/images/shadow_bottom.png" />
			<div><?=$arSection["UF_SECTION_DESCR"]?></div>
		</div>
	<?endif;?>
<?endif;?>
<script>
	$(".show_props").on("click", function()
	{
		$(this).find("a").toggleClass("opened");
		$(this).next(".props_list_wrapp").slideToggle(333);
	});
</script>
