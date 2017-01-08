<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if( count( $arResult["ITEMS"] ) >= 1 ){?>
	<?
	/*$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
	$arNotify = unserialize($notifyOption);*/
	?>
	<div class="catalog_block_def">	
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
			<div class="catalog_item_wrapp">
				<div class="catalog_item <?=(($_GET['q'])) ? 's' : ''?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">				
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
					<div class="image">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb">
							<?if( !empty($arItem["PREVIEW_PICTURE"]) ):?>
								<img border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
							<?elseif( !empty($arItem["DETAIL_PICTURE"])):?>
								<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 165, "height" => 165 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
								<img border="0" src="<?=$img["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />		
							<?else:?>
								<img border="0" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
							<?endif;?>
						</a>
					</div>
					
					<div class="item_info">
						<?//aniart?>
						<div class="good_id"><?="ID#".$arItem['ID']?></div>
						<?//end aniart?>
						<div class="item-title">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span><?=$arItem["NAME"]?></span></a>
						</div>
						<?if(strlen($arQuantityData["TEXT"])):?>
							<div class="availability-row"><?=$arQuantityData["HTML"]?></div>
						<?endif;?>
						<div class="cost clearfix">
							<?if( $arItem["OFFERS"]){?> 
								<div class="price_block">
									<div class="price"><?=GetMessage("CATALOG_FROM");?> <?=$arItem["MIN_PRODUCT_OFFER_PRICE_PRINT"]?></div>
								</div>
							<?} elseif ( $arItem["PRICES"] ){?>
								<? $arCountPricesCanAccess = 0; foreach( $arItem["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} } ?>
								<?foreach( $arItem["PRICES"] as $key => $arPrice ){?>
									<?if( $arPrice["CAN_ACCESS"] ){?>
										<?$price = CPrice::GetByID($arPrice["ID"]); ?>
										<?if($arCountPricesCanAccess>1):?><div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div><?endif;?>
										<?if( $arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] ){?>
											<div class="price"><?=$arPrice["PRINT_DISCOUNT_VALUE"];?></div>
											<div class="price discount">
												<?$symb=substr($arPrice["PRINT_VALUE"], strrpos($arPrice["PRINT_VALUE"], ' '));?>
												<strike><?=str_replace($symb, "", $arPrice["PRINT_VALUE"]);?></strike>
											</div>
										<?}else{?><div class="price"><?=$arPrice["PRINT_VALUE"];?></div><?}?>
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
						<div class="buttons_block clearfix">
							<!--noindex-->
								<?php if($arParams["USE_DEFAULT_ACTION_BUTTON"] == "Y"){?>
									<a alt="<?=$arItem["NAME"]?>" href="<?=$arItem["ADD_URL"]?>" class="basket_button"><i></i><span>В корзину</span></a>
								<?php }else{?>
									<?=$arAddToBasketData["HTML"]?>
								<?php }?>
								<?if($arAddToBasketData["ACTION"] == "ADD"):?>
									<a class="basket_button one_click" data-item="<?=$arItem["ID"]?>" data-quantity="<?=($totalCount >= $arParams["DEFAULT_COUNT"] ? $arParams["DEFAULT_COUNT"] : $totalCount)?>" onclick="oneClickBuy('<?=$arItem["ID"]?>', '<?=$arParams["IBLOCK_ID"]?>', this)">
										<span><?=GetMessage('ONE_CLICK_BUY')?></span>
									</a>
								<?endif;?>
								<?if($arItem["CAN_BUY"] && ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y")):?>
									<div class="like_icons">								
										<?if(empty($arItem["OFFERS"]) && $arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
											<a title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item" rel="nofollow" data-item="<?=$arItem["ID"]?>"><i></i></a>
										<?endif;?>
										<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
											<a title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item" rel="nofollow" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" href="<?=$arItem["COMPARE_URL"]?>"><i></i></a>
										<?endif;?>
									</div>
								<?endif;?>
							<!--/noindex-->
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		<?}?>
	</div>
	<div class="bottom_nav">
		<?if( $arParams["DISPLAY_BOTTOM_PAGER"] == "Y" ){?><?=$arResult["NAV_STRING"]?><?}?>
	</div>
<?}?>

<div class="clear"></div>
<script>
	var fRand = function() {return Math.floor(arguments.length > 1 ? (999999 - 0 + 1) * Math.random() + 0 : (0 + 1) * Math.random());};
	var waitForFinalEvent = (function () 
	{
	  var timers = {};
	  return function (callback, ms, uniqueId) 
	  {
		if (!uniqueId) {
		  uniqueId = fRand();
		}
		if (timers[uniqueId]) {
		  clearTimeout (timers[uniqueId]);
		}
		timers[uniqueId] = setTimeout(callback, ms);
	  };
	})();
	
	
	
	$('.catalog_block_def').ready(function()
	{
		$('.catalog_block_def').equalize({children: '.catalog_item .cost', reset: true}); 
		$('.catalog_block_def').equalize({children: '.catalog_item .item-title', reset: true}); 
		$('.catalog_block_def').equalize({children: '.catalog_item .counter_block', reset: true}); 
		$('.catalog_block_def').equalize({children: '.catalog_item', reset: true});
	})

</script>
