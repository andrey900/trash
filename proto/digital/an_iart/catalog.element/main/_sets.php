<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

	<div class="set_wrapp">
			<div class="title"><?=GetMessage("GROUP_PARTS_TITLE")?></div>
			<ul>
				<?foreach ($arResult["SET_ITEMS"] as $iii => $arSetItem):?>	
					<li class="item">
						<div class="image">
							<a href="<?=$arSetItem["DETAIL_PAGE_URL"]?>">
								<?if( !empty($arSetItem["PREVIEW_PICTURE"]) ):?>
									<?$img = CFile::ResizeImageGet($arSetItem["PREVIEW_PICTURE"], array( "width" => 140, "height" => 140 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
									<img border="0" src="<?=$img["src"]?>" alt="<?=$arSetItem["NAME"];?>" title="<?=$arSetItem["NAME"];?>" />	
								<?elseif( !empty($arSetItem["DETAIL_PICTURE"])):?>
									<?$img = CFile::ResizeImageGet($arSetItem["DETAIL_PICTURE"], array( "width" => 140, "height" => 140 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
									<img border="0" src="<?=$img["src"]?>" alt="<?=$arSetItem["NAME"];?>" title="<?=$arSetItem["NAME"];?>" />		
								<?else:?>
									<img border="0" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_small.png" alt="<?=$arSetItem["NAME"];?>" title="<?=$arSetItem["NAME"];?>" />
								<?endif;?>
							</a>
						</div>
						<div class="item_info">
							<div class="item-title">
								<a href="<?=$arSetItem["DETAIL_PAGE_URL"]?>"><span><?=$arSetItem["NAME"]?></span></a>
							</div>
							<?if ($arParams["SHOW_KIT_PARTS_PRICES"]=="Y"):?>								
								<div class="cost clearfix">	
									<?foreach($arSetItem["PRICES"] as $key => $arPrice ){?>
										<? $arCountPricesCanAccess = 0; foreach( $arItem["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} } ?>
										<?foreach( $arSetItem["PRICES"] as $key => $arPrice ){?>
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
							<?endif;?>
						</div>
					</li>
					<?if($arResult["SET_ITEMS"][$iii+1]):?><li class="separator">&nbsp;</li><?endif;?>
				<?endforeach;?>
			</ul>
		</div>
		<script>
		$('.set_wrapp').ready(function(){
			//$('.set_wrapp').equalize({children: '.item .cost', reset: true}); 
			$('.set_wrapp').equalize({children: '.item .item-title', reset: true}); 
			$('.set_wrapp').equalize({children: 'li', reset: true});
		})
		</script>