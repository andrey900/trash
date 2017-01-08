<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if( count( $arResult["ITEMS"] ) >= 1 ){?>
	<?
	/*$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
	$arNotify = unserialize($notifyOption);*/
	?>				
	<table class="module_products_list">
		<tbody>
			
			<?foreach($arResult["ITEMS"]  as $arItem){
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
				<tr class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<?if( !empty($arItem["DETAIL_PICTURE"]) ){?>
						<td class="foto-cell">
							<?$img_preview = CFile::ResizeImageGet( $arItem["DETAIL_PICTURE"], array( "width" => 50, "height" => 50 ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);?>
							<?if ($arParams["LIST_DISPLAY_POPUP_IMAGE"]=="Y"):?>
								<a class="popup_image fancy" href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" title="<?=$arItem["NAME"]?>">
									<img src="<?=$img_preview["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" />
								</a>
							<?else:?>
								<img src="<?=$img_preview["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" />
							<?endif;?>							
						</td>						
					<?}elseif( !empty($arItem["PREVIEW_PICTURE"])){?>
						<td class="foto-cell">
							<?$img_preview = CFile::ResizeImageGet( $arItem["PREVIEW_PICTURE"], array( "width" => 50, "height" => 50 ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);?>
							<?if ($arParams["LIST_DISPLAY_POPUP_IMAGE"]=="Y"):?>
								<a class="popup_image fancy" href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["NAME"]?>">
									<img src="<?=$img_preview["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
									<i class="triangle"></i>
								</a>
							<?else:?>
								<img src="<?=$img_preview["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
							<?endif;?>
						</td>							
					<?}else{?>
						<td class="foto-cell"><img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_small.png" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></td>
					<?}?>
					
					<td class="item-name-cell">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
						<?//aniart?>
						<span class="good_id">ID#<span class="product-article" item-id="<?=$arItem['ID']?>"><?=$arItem['ID']?></span></span>
						<?//end aniart?>
						<span class="slices">
							<?if (is_array($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
								<?if( in_array("HIT", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) ):?><span class="slice-hit"></span><?endif;?>
								<?if( in_array("RECOMMEND", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="slice-recomend"></span><?endif;?>
								<?if( in_array("NEW", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="slice-new"></span><?endif;?>
								<?/*if( in_array("STOCK", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="slice-share"></span><?endif;*/?>
								<?if( in_array("XML_GIFT", $arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="slice-present"></span><?endif;?>					
							<?endif;?>
							<?if($arItem["MIN_PRODUCT_DISCOUNT_PRICE"]):?>
								<span class="slice-share"></span>
							<?endif;?>
						</span>
						<!--adaptive properties begin-->
							<div class="extra_properties">
								<?if(strlen($arQuantityData["TEXT"])):?>
									<div class="availability-row"><?=$arQuantityData["HTML"]?></div>
								<?endif;?>
								<div class="buy-block">
									<!--price-->
									<div class="price-cell<?if( $arItem["OFFERS"] && (CSite::InDir(SITE_DIR.'search/tires') || CSite::InDir(SITE_DIR.'search/wheels') || CSite::InDir(SITE_DIR.'catalog/tires') || CSite::InDir(SITE_DIR.'catalog/wheels'))){echo " ws";}?>">
										<?$count_in_stores = 0;?>
										<?if( $arItem["OFFERS"]){?> 
											<?foreach( $arItem["OFFERS"] as $arOffer ){$count_in_stores += $arOffer["CATALOG_QUANTITY"];}?>
											<?if (CSite::InDir(SITE_DIR.'search/tires') || CSite::InDir(SITE_DIR.'search/wheels') || CSite::InDir(SITE_DIR.'catalog/tires') || CSite::InDir(SITE_DIR.'catalog/wheels')):?>
												<span class="offers_error"><?=GetMessage("OFFERS_ERROR");?></span>
											<?else:?>
												<?=GetMessage("FROM")?> 
												<?
												$symb =substr($arItem["MIN_PRODUCT_OFFER_PRICE_PRINT"], strrpos($arItem["MIN_PRODUCT_OFFER_PRICE_PRINT"], ' '));
												echo str_replace($symb, "", $arItem["MIN_PRODUCT_OFFER_PRICE_PRINT"])."<span>".$symb."</span>";
												?>
											<?endif;?>
										<?} elseif ( $arItem["PRICES"] ){?>
											<?	
											$count_in_stores = $arItem["CATALOG_QUANTITY"];
											$arCountPricesCanAccess = 0;
											foreach( $arItem["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} }
											?>
											<?foreach( $arItem["PRICES"] as $key => $arPrice ){?>
												<?if( $arPrice["CAN_ACCESS"] ){?>
													<?$price = CPrice::GetByID($arPrice["ID"]); ?>
													<div class="cost">
														<?if($arCountPricesCanAccess>1):?><div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div><?endif;?>
														<?if( $arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] ){?>
															<div class="price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></div>
															<div class="price discount"><strike><?=$arPrice["PRINT_VALUE"]?></strike></div>
														<?}else{?>
															<div class="price"><?=$arPrice["PRINT_VALUE"]?></div>
														<?}?>
													</div>
												<?}?>
											<?}?>				
										<?}?>
									</div>
								
									<!--product quantity & buy buttons-->
									<div class="buy_buttons_wrapp">
										<!--product quantity-->
										<?if($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && !count($arItem["OFFERS"]) && $arItem["CAN_BUY"] && intval($count_in_stores) > 0):?>
											<!--noindex-->
											<span class="quantity-cell">
												<select name="counter" class="counter">
													<?
													$max_count = $count_in_stores;
													$max_count_settings = intval($arParams['MAX_COUNT']);
													if($max_count_settings != 0) $max_count = min($max_count, $max_count_settings);
													for($i=1;$i<=$max_count;$i++){
														?>
														<?if($max_count>=intval($arParams["DEFAULT_COUNT"])){?>
															<option value="<?=$i?>" <?=(($i==intval($arParams["DEFAULT_COUNT"])))? 'selected' : ''?>><?=$i?></option>
														<?}else{?>
															<option value="<?=$i?>" <?=(($i==$max_count))? 'selected' : ''?>><?=$i?></option>
														<?}?>
													<?}?>
												</select>										
												<span class="measure"><?=$sMeasure;?></span>&nbsp;
											</span>
											<!--/noindex-->
										<?endif;?>
										
										<!--buy buttons-->
										<span class="but-cell item_<?=$arItem["ID"]?>">
											<!--noindex-->
												<?=$arAddToBasketData["HTML"]?>
											<!--/noindex-->
										</span>  
									</div>
								</div>
							</div>
						<!--adaptive properties end-->
					</td>
					
					<?if(strlen($arQuantityData["TEXT"])):?>
						<td class="availability-row"><?=$arQuantityData["HTML"]?></td>
					<?endif;?>
										
					<td class="price-cell">
						<?$count_in_stores = 0;?>
						<?if( $arItem["OFFERS"]){?> 
							<?foreach( $arItem["OFFERS"] as $arOffer ){$count_in_stores += $arOffer["CATALOG_QUANTITY"];}?>
								<div class="cost">
									<div class="price"><?=GetMessage("FROM")?> <?=$arItem["MIN_PRODUCT_OFFER_PRICE_PRINT"]?></div>
								</div>
						<?} elseif ( $arItem["PRICES"] ){?>
							<?	
							$count_in_stores = $arItem["CATALOG_QUANTITY"];
							$arCountPricesCanAccess = 0;
							foreach( $arItem["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} }
							?>
							<?foreach( $arItem["PRICES"] as $key => $arPrice ){?>
								<?if( $arPrice["CAN_ACCESS"] ){?>
									<?$price = CPrice::GetByID($arPrice["ID"]); ?>
									<div class="cost">
										<?if($arCountPricesCanAccess>1):?><div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div><?endif;?>
										<?if( $arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] ){?>
											<div class="price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></div>
											<div class="price discount"><strike><?=$arPrice["PRINT_VALUE"]?></strike></div>
										<?}else{?><div class="price"><?=$arPrice["PRINT_VALUE"]?></div><?}?>
									</div>
								<?}?>
							<?}?>				
						<?}?>
					</td>
					
					<td class="quantity-cell">
						<?if($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && !count($arItem["OFFERS"]) && $arAddToBasketData["ACTION"] == "ADD"):?>
							<div class="counter_block" data-item="<?=$arItem["ID"];?>">
								<span class="minus">-</span>
								<input type="text" class="text" name="count_items" value="<?=($arParams["DEFAULT_COUNT"] > 0 ? $arParams["DEFAULT_COUNT"] : 1)?>" />
								<span class="plus">+</span>
							</div>
						<?endif;?>
					</td>
				
					<td class="but-cell item_<?=$arItem["ID"]?>">					
						<!--noindex-->
							<?=$arAddToBasketData["HTML"]?>
							<?if($arAddToBasketData["ACTION"] == "ADD"):?>
								<a class="basket_button one_click" data-item="<?=$arItem["ID"]?>" data-quantity="<?=($totalCount >= $arParams["DEFAULT_COUNT"] ? $arParams["DEFAULT_COUNT"] : $totalCount)?>" onclick="oneClickBuy('<?=$arItem["ID"]?>', '<?=$arParams["IBLOCK_ID"]?>', this)">
									<span><?=GetMessage('ONE_CLICK_BUY')?></span>
								</a>
							<?endif;?>
						<!--/noindex-->
					</td>  
					<?if($arItem["CAN_BUY"] && ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y")):?>
						<td class="like_icons <?=(($arParams["DISPLAY_WISH_BUTTONS"] != "N" && $arParams["DISPLAY_COMPARE"] == "Y") ? " full" : "")?>">
							<?if(empty($arItem["OFFERS"]) && $arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
								<a title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item" rel="nofollow" data-item="<?=$arItem["ID"]?>"><i></i></a>
							<?endif;?>								
							<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
								<a title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item" rel="nofollow" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" href="<?=$arItem["COMPARE_URL"]?>"><i></i></a>
							<?endif;?>
						</div>
					<?endif;?>
				</tr>
			<?}?>
		</tbody>
	</table>
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
			"SHOW_SECTIONS_LIST_PREVIEW" => "N",
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