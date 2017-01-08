<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

	<table width="100%" border="0"><tr><td>
		<div class="info_block clearfix">
			<?if ($arParams["USE_RATING"]=="Y"){?>
				<div class="rating">
					<b class="block_title"><?=GetMessage("RATING");?>:</b>
					<?$APPLICATION->IncludeComponent(
					   "bitrix:iblock.vote",
					   "element_rating",
					   Array(
						  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						  "IBLOCK_ID" => $arResult["IBLOCK_ID"],
						  "ELEMENT_ID" =>$arResult["ID"],
						  "MAX_VOTE" => 5,
						  "VOTE_NAMES" => array(),
						  "CACHE_TYPE" => $arParams["CACHE_TYPE"],
						  "CACHE_TIME" => $arParams["CACHE_TIME"],
						  "DISPLAY_AS_RATING" => 'vote_avg'
					   ),
					   $component, array("HIDE_ICONS" =>"Y")
					);?>
				</div>
			<?}?>
			<?/*if( !empty($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) ){?>
				<div class="article">
					<b class="block_title"><?=GetMessage("ARTICLE");?>:</b> 
					<?=$arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?>
				</div>
			<?}*/?>
			<?//aniart?>
			<div class="article">
				<b class="block_title"><?=GetMessage("ARTICLE");?>:</b> 
				#ID
				<span item-id="<?=$arResult['ID'];?>" class="product-article"><?=$arResult['ID']?></span>
			</div>
			<?//end aniart?>
			<?if( !empty($arResult["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"]) ){?>
				<?	$rsBrand = CIBlockElement::GetList(
                                            array(), 
                                            array("IBLOCK_ID" => $arResult["PROPERTIES"]["BRAND"]["LINK_IBLOCK_ID"], "ID" => $arResult["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"] )
                                        );
					$arBrand = $rsBrand->GetNext();?>
                                        
				<div class="brand">
					<? //p($arResult); ?>
					<?if (($arParams["SHOW_BRAND_PICTURE"]!="Y")||(!($arBrand["PREVIEW_PICTURE"]||$arBrand["DETAIL_PICTURE"]))):?>
						<b class="block_title"><?=GetMessage("BRAND");?>:</b>
						<a href="<?=$arBrand["DETAIL_PAGE_URL"]?>"><?=$arBrand["NAME"]?></a>
					<?else:?>
						<?	
							$img = array();
							if($arBrand["PREVIEW_PICTURE"]) { $img = CFile::ResizeImageGet( $arBrand["PREVIEW_PICTURE"], array( "width" => 120, "height" => 40 ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true );}
							elseif($arBrand["DETAIL_PICTURE"]) { $img = CFile::ResizeImageGet( $arBrand["DETAIL_PICTURE"], array( "width" => 120, "height" => 40 ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true );}
						?>
						<a class="brand_picture" href="<?=$arBrand["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$img["src"]?>" alt="<?=$arBrand["NAME"]?>" title="<?=$arBrand["NAME"]?>" /></a>
					<?endif;?>
				</div>
			<?}?>
		</div>	
		<hr />	
		</td></tr>
		
		<?if ($arResult["CAN_BUY"] || !empty( $arResult["OFFERS"]) || !empty( $arResult["PRICES"])){?>
		<tbody  itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<tr><td>
			<div class="price_block_wrapp">
					<?if( !empty( $arResult["OFFERS"] ) ){?>
						<div class="price_block">
							<div itemprop="price" class="price"><?=GetMessage("CATALOG_FROM");?> <?=$arResult["MIN_PRODUCT_OFFER_PRICE_PRINT"]?></div>
						</div>
					<?}elseif( !empty( $arResult["PRICES"] )){?>
						<? $arCountPricesCanAccess = 0; foreach( $arResult["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} } ?>
						<?foreach( $arResult["PRICES"] as $key => $arPrice ){?>
							<div class="price_block">				
								<?if( $arPrice["CAN_ACCESS"] ){?>
									<?$price = CPrice::GetByID($arPrice["ID"]); ?>
									<?if($arCountPricesCanAccess>1):?><div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div><?endif;?>
									<?if( $arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] ){?>									
										<div itemprop="price" class="price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?><?if (($arParams["SHOW_MEASURE"]=="Y")&&$arMeasure["SYMBOL_RUS"]):?><small>/<?=$arMeasure["SYMBOL_RUS"]?></small><?endif;?></div>
										<div class="price discount"><?=GetMessage("WITHOUT_DISCOUNT")?>: <strike><?=$arPrice["PRINT_VALUE"]?></strike></div>
									<?}else{?><div itemprop="price" class="price"><?=$arPrice["PRINT_VALUE"]?><?if (($arParams["SHOW_MEASURE"]=="Y")&&$arMeasure["SYMBOL_RUS"]):?><small>/<?=$arMeasure["SYMBOL_RUS"]?></small><?endif;?></div><?}?>
								<?}?>
							</div>
						<?}?>
					<?}?>
				</div>	
				<meta itemprop="priceCurrency" content="<?=$arResult["PRICES"]['BASE']['CURRENCY']?>">
				<meta itemprop="availability" content="<?=($arResult['PROPERTIES']['ON_SITE']['VALUE'])?'http://schema.org/InStock':'out_of_stock';?>">
				</td></tr>
                                <? //p($arAddToBasketData); ?>
				<?if( empty( $arResult["OFFERS"] ) && ($arResult["CAN_BUY"] || ($arResult["CATALOG_SUBSCRIBE"]=="Y" /*&& $arNotify[SITE_ID]['use'] == 'Y'*/))){?>
					<tr><td>
						<table width="100%" cellspacing="0" cellpadding="0" border="0" class="buttons_block"><tr>
							<!--noindex-->
								<?if($arAddToBasketData["ACTION"] == "ADD"):?>
									<td class="counter_block" data-item="<?=$arResult["ID"];?>">
										<?if($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] && !count($arResult["OFFERS"]) && $arAddToBasketData["ACTION"] == "ADD"):?>
											<span class="minus">-</span>
											<input type="text" class="text" name="count_items"  id="count_items_sel" value="<?=($arParams["DEFAULT_COUNT"] ? $arParams["DEFAULT_COUNT"] : "1");?>" />
											<span class="plus">+</span>
											
											<select onchange="$('.buttons_block #count_items_sel').val($('#count_items_sel_phone').val());" id="count_items_sel_phone" style="display: none;">
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
											</select>
										<?endif;?>
									</td>
								<?endif;?>
								<?if($arAddToBasketData["ACTION"] !== "NOTHING"):?>
									<td class="buy_buttons_wrapp clearfix<?=(($arAddToBasketData["ACTION"] == "SUBSCRIBE" || $arAddToBasketData["ACTION"] == "ORDER") ? " subscribe" : "")?>">
										<?=$arAddToBasketData["HTML"]?>
										<?if($arAddToBasketData["ACTION"] == "ADD"):?>
											<a class="basket_button button30 one_click" data-item="<?=$arResult["ID"]?>" data-quantity="<?=($totalCount >= $arParams["DEFAULT_COUNT"] ? $arParams["DEFAULT_COUNT"] : $totalCount)?>" onclick="oneClickBuy('<?=$arResult["ID"]?>', '<?=$arParams["IBLOCK_ID"]?>', this)">
												<span><?=GetMessage('ONE_CLICK_BUY')?></span>
											</a>
										<?endif;?>
									</td>
								<?endif;?>
								<?if($arAddToBasketData["ACTION"] == "SUBSCRIBE"):?>
									<td class="description">
										<?$APPLICATION->IncludeFile(SITE_DIR."include/subscribe_description.php", array(), array("MODE" => "text", "NAME" => GetMessage("SUBSCRIBE_DESCRIPTION")));?>
									</td>
								<?endif;?>
								<?if($arAddToBasketData["ACTION"] == "ORDER"):?>
									<td class="description">
										<?$APPLICATION->IncludeFile(SITE_DIR."include/order_description.php", array(), array("MODE" => "text", "NAME" => GetMessage("ORDER_DESCRIPTION")));?>
									</td>
								<?endif;?>
							<!--/noindex-->	
						</tr></table>
					</td></tr>
				<?}?>
				
				<tr><td>
					<div class="extended_info clearfix<?=($arParams["USE_STORE"] == "Y" ? " open_stores" : "")?>">
						<?if(strlen($arQuantityData["TEXT"])):?>
							<div class="availability-row"><?=$arQuantityData["HTML"]?></div>
						<?endif;?>
						<?if ((empty($arResult["OFFERS"]) && $arResult["CAN_BUY"] && ($arParams["DISPLAY_WISH_BUTTONS"]!="N"))||($arParams["DISPLAY_COMPARE"]=="Y")){?>
							<div class="like_icons">
								<!--noindex-->
									<?if (empty($arResult["OFFERS"])&&$arResult["CAN_BUY"]):?>
										<a rel="nofollow" class="wish_item" data-item="<?=$arResult["ID"]?>">
											<span class="icon"><i></i></span><b class="triangle"></b>
											<span class="value pseudo"><?=GetMessage('CT_BCE_CATALOG_IZB')?></span>
											<span class="value pseudo added"><?=GetMessage('CT_BCE_CATALOG_IZB_ADDED')?></span>
										</a>
									<?endif;?>
									<?if( $arParams["DISPLAY_COMPARE"] == "Y" ){?>
										<a rel="nofollow" data-item="<?=$arResult["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>" href="<?=$arResult["COMPARE_URL"]?>" class="compare_item">
											<span class="icon"><i></i></span><b class="triangle"></b>
											<span class="value pseudo"><?=GetMessage('CT_BCE_CATALOG_COMPARE')?></span>
											<span class="value pseudo added"><?=GetMessage('CT_BCE_CATALOG_COMPARE_ADDED')?></span>
										</a>
									<?}?>
								<!--/noindex-->
							</div>
						<?}?>
					</div>
					<div class="adaptive_extended_info_wrapp">
							<?if( !empty($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) || !empty($arResult["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"])):?>
								<div class="adaptive_extended_info">
									<?if( !empty($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) ){?>
										<div class="article">
											<b class="block_title"><?=GetMessage("ARTICLE");?>:</b> 
											<?=$arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?>
										</div>
									<?}?>
									<?if( !empty($arResult["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"]) ){?>
										<?	$rsBrand = CIBlockElement::GetList( array(), array("IBLOCK_ID" => $arResult["PROPERTIES"]["BRAND"]["LINK_IBLOCK_ID"], "ID" => $arResult["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"] ));
											$arBrand = $rsBrand->GetNext();?>
										<div class="brand">
											<?if (($arParams["SHOW_BRAND_PICTURE"]!="Y")||(!($arBrand["PREVIEW_PICTURE"]||$arBrand["DETAIL_PICTURE"]))):?>
												<b class="block_title"><?=GetMessage("BRAND");?>:</b>
												<a href="<?=$arBrand["DETAIL_PAGE_URL"]?>"><?=$arBrand["NAME"]?></a>
											<?else:?>
												<?	
													$img = array();
													if($arBrand["PREVIEW_PICTURE"]) { $img = CFile::ResizeImageGet( $arBrand["PREVIEW_PICTURE"], array( "width" => 120, "height" => 40 ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true );}
													elseif($arBrand["DETAIL_PICTURE"]) { $img = CFile::ResizeImageGet( $arBrand["DETAIL_PICTURE"], array( "width" => 120, "height" => 40 ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true );}
												?>
												<a class="brand_picture" href="<?=$arBrand["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$img["src"]?>" alt="<?=$arBrand["NAME"]?>" title="<?=$arBrand["NAME"]?>" /></a>
											<?endif;?>
										</div>
									<?}?>
								</div>
							<?endif;?>
							<img class="shadow" src="<?=SITE_TEMPLATE_PATH?>/images/shadow_bottom.png">
					</div>
				</td></tr>
				
				<? if($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']){?>
				<tr>
					<td style="padding:0px 10px">
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="props_table">
							<tr>
								<td class="char_name"><span>Артикул</span></td>
								<td class="char_value"><span><?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?></span></td>
							</tr>
						</table>
					</td>
				</tr>
				<? }?>
				
				<?if ($arResult["PREVIEW_TEXT"] && $arResult["NAME"] != $arResult["PREVIEW_TEXT"]):?>
					<tr><td class="preview_text"><?=$arResult["PREVIEW_TEXT"]?></td></tr>
				<?endif;?>
				</tbody>				
		<?}?>
		
		</table>
			
		<hr class="separator" />	

		<div class="element_detail_text">
			<?$APPLICATION->IncludeFile(SITE_DIR."include/element_detail_text.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('CT_BCE_CATALOG_DOP_DESCR')));?>	
			<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON')));?>
		</div>