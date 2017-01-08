<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

$productPrice = $productInfo ["CATALOG_PRICE_" . PRICE_BASE_ID];

$arImages = explode ( ";", $productInfo ["PROPERTY_URL_KARTINOK_VALUE"] );

$countGiftProduct = 0;
foreach ( $actionsInfo ["GIFT_ACTIONS"] as $giftID => $arGift ) {
	$countGiftProduct += count ( $arGift ["LIST_GIFT"] );
}

if ($countGiftProduct == 0)
	return;

//p($actionsInfo);
?>



<div class="clearfloat"></div>

	<?foreach ($actionsInfo["LINK_GIFT_ACTIONS"] as $period => $arGifts){?>
	
		<?$dateToUnix = $actionsInfo["LINK_GIFT_ACTIONS"][$period]["DATE_TO"];?>
		<?if ($dateToUnix <= time()) return;?>
	
		<?$actionsInfoName = "";?>
		
		<?foreach ( $arGifts ["GIFT_ID"] as $giftID => $nameGift ) {
			// формируем наименование акции
			if (empty ( $actionsInfoName )) {
				$actionsInfoName = $nameGift;
			} else {
				$actionsInfoName .= " + " . $nameGift;
			}
			// ищем комплекты
			if ($actionsInfo ["GIFT_ACTIONS"] [$giftID] ["IS_SET"] == "Y")
				$isSet = true;
		}
		?>
		<?if (empty($actionsInfo["GIFT_ACTIONS"][$giftID]["LIST_GIFT"])) continue;?>
		
		<?$firstGift = current($actionsInfo["GIFT_ACTIONS"][$giftID]["LIST_GIFT"]);?>
		
		<div class="table_gifts_preview">	
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr valign="middle">
					<td class="action_gift_img"><div><img src="<?=CImageEx::Resize ( 
							array(
								"SOURCE" => $firstGift["PREVIEW_PICTURE"],
								"WIDTH" => 50,
								"HEIGHT" => 50 
								) 
							);?>"
							title="<?=$firstGift["NAME"]?>"
							alt="<?=$firstGift["NAME"]?>" /></div></td>
					<td>
						<div class="action_gift_name"><?=$actionsInfoName?></div>
						<div class="action_gift_good_name"><a href="<?=$firstGift['DETAIL_PAGE_URL']?>"><?=$firstGift['NAME']?></a></div>
					</td>
				    <td width="185px" align="right">
				    	<div class="countdown_act">
							<div class="countdown-title-head">До окончания акции осталось:</div>
							<div id="countdown-<?=$giftID?>"></div>
							<div class="clearfloat"></div>
					
							<table class="countdown-title">
								<tr>
									<td style="padding-left: 0px;">&nbsp;дней&nbsp;&nbsp;</td>
									<td>часов&nbsp;</td>
									<td>минут</td>
									<td style="padding-right: 0px;">секунд</td>
								</tr>
							</table>
						</div>
						<script>
							$(function(){
								ts = new Date(<?=$dateToUnix*1000?>);
								var zero_time = false;
															
								$('#countdown-'+<?=$giftID?>).countdown({
									timestamp	: ts,
									callback	: function(days, hours, minutes, seconds){
										last_time = days*24*60*60 + hours*60*60 + minutes*60 + seconds;
										if (zero_time) return;
										if(last_time <= 0) zero_time = true;
										if (zero_time) {
											<?// "финт ушами", который позволяет сбросить кеш компонента, если время акции истекло?>
											$.post( 
												"<?=$APPLICATION->GetCurDir()?>", 
												{ clear_cache: '<?=$_SESSION["BX_SESSION_SIGN"]?>'}, 
												function( data ) { location.reload(); }
											);
										}
									}
								});
							});
						</script>
				    </td>
				</tr>
			</table>
		</div>
		<div class="pp-promotion-more"><a name="togglePromotionPopup" href="javascript:void(0)" onclick="ShowGiftActions('<?=$period?>', '<?=$productInfo['ID']?>')" class="xhr lightblue">Подробнее об&nbsp;акции</a></div>
	<? }?>

<?php /*?>
<div id="product-gift-from" class='gift-info detail-page' style="display: none">
	<div class='gift-description'>
		<div class='title'>Акционные предложения</div>
	</div>
	<div class='gift-list'>
		<div class="jcarousel-pagination" id="detail-jcarousel-gift-pagination"></div>
		<div class='list'>
			<div class="jcarousel-gift" id="detail-jcarousel-gift">
				<ul>
				<?$count = 0;?>
				<?foreach ($actionsInfo["LINK_GIFT_ACTIONS"] as $period => $arGifts):?>
					<li period_hash="<?=base64_encode($period)?>" class="gift-proposition">
					
						<?$count++;?>
						<?$actionsInfoName = "";?>
  	    				<?$isSet = false;?>
  	    				
						<?foreach ( $arGifts ["GIFT_ID"] as $giftID => $nameGift ) {
							// формируем наименование акции
							if (empty ( $actionsInfoName )) {
								$actionsInfoName = $nameGift;
							} else {
								$actionsInfoName .= " + " . $nameGift;
							}
							// ищем комплекты
							if ($actionsInfo ["GIFT_ACTIONS"] [$giftID] ["IS_SET"] == "Y")
								$isSet = true;
						}
						?>
						<?if (empty($actionsInfo["GIFT_ACTIONS"][$giftID]["LIST_GIFT"])) continue;?>
						
						<div class="title-name">
							<p class="first">Купи комплект сейчас</p>
							<p class="second"><?=$actionsInfoName?></p>
						</div>
						<div class="product-info">
							<div class="product-image">
								<img
									src="<?=CImageEx::Resize ( array ("SOURCE" => $arImages [0],"WIDTH" => 100,"HEIGHT" => 100 ) );?>"
									title="<?=$productInfo["NAME"]?>"
									alt="<?=$productInfo["NAME"]?>" />
							</div>

							<div class="product-name">
								<a href="<?=$productInfo["DETAIL_PAGE_URL"]?>">
									<?=TruncateStr($productInfo["NAME"], 50)?>
								</a>
							</div>
							<?
							if ($isSet)
								$totalPrice = $actionsInfo ["GIFT_ACTIONS"] [$giftID] ["PRICE_PRODUCT_1_SET"];
							else
								$totalPrice = $productPrice;
							
							$fullTotalPrice = $productPrice;
							?>
							
		  	    			<?if($isSet):?>
			  	    			<div class="product-price old">
			  	    				<?=GetPriceHTMLFormat($productPrice)?>
			  	    			</div>
								<div class="product-price set">
							  	  	<?=GetPriceHTMLFormat($actionsInfo["GIFT_ACTIONS"][$giftID]["PRICE_PRODUCT_1_SET"])?>
							  	</div>
		  	    			<?endif;?>
						</div>
						<?$firstProduct = true;?>
						<?foreach ($arGifts["GIFT_ID"] as $giftID => $giftName):?>
							<?$firstGift = current($actionsInfo["GIFT_ACTIONS"][$giftID]["LIST_GIFT"]);?>
							<div class="product-info">
							<div class="plus <?=($firstProduct)?"first":""?>">+</div>
								<?$firstProduct = false;?>
								<?if (count($actionsInfo["GIFT_ACTIONS"][$giftID]["LIST_GIFT"]) > 1 ):?>
									<div class="up"
								id="detail-jcarousel-product-<?=$period."-".$giftID?>-up"></div>
							<div class="down"
								id="detail-jcarousel-product-<?=$period."-".$giftID?>-down"></div>
								<?endif;?>
								<div class="jcarousel-vertical jcarousel-product"
								id="detail-jcarousel-product-<?=$period."-".$giftID?>"
								link_period_hash="<?=base64_encode($period)?>" data-selected='0'>
								<ul>
										<?foreach ($actionsInfo["GIFT_ACTIONS"][$giftID]["LIST_GIFT"] as $productID => $arProduct):?>
											<li product_id="<?=$productID?>">
										<div
											class="product-image show-tooltip-<?=$productInfo["ID"]?>">
											<img
												src="<?=CImageEx::Resize ( array ("SOURCE" => $arProduct ["PREVIEW_PICTURE"],"WIDTH" => 100,"HEIGHT" => 100 ) );?>"
												title="<?=$productInfo["NAME"]?>"
												alt="<?=$productInfo["NAME"]?>" />
										</div>
												
										<div class="product-name">
											<a href="<?=$arProduct["DETAIL_PAGE_URL"]?>">
												<?=TruncateStr($arProduct["NAME"], 50)?>
											</a>
										</div>
												
				  	    				<?$fullTotalPrice += $arProduct["CATALOG_PRICE_".PRICE_BASE_ID];?>
				  	    				<?if($isSet):?>
				  	    				
				  	    					<?$giftPrice = $actionsInfo["GIFT_ACTIONS"][$giftID]["PRICE_PRODUCT_2_SET"];?>

					  	    				<div class="product-price old">
					  	    					<?=GetPriceHTMLFormat($arProduct["CATALOG_PRICE_".PRICE_BASE_ID])?>
					  	    				</div>

											<div class="product-price set">
					  	    					<?=GetPriceHTMLFormat($giftPrice)?>
					  	    				</div>
					  	    			<?else:?>
					  	    				<?$giftPrice = $actionsInfo["GIFT_ACTIONS"][$giftID]["PRICE_GIFT"];?>
					  	    			<?endif;?>
												
											</li>
										<?endforeach;?>
										<?$totalPrice += $giftPrice;?>
									</ul>
							</div>
								<script>
									$('#detail-jcarousel-product-<?=$period."-".$giftID?>').jcarousel({
										wrap: 'circular',
										vertical: true,
									});
							
									$("#detail-jcarousel-product-<?=$period."-".$giftID?>-up").on("click", function() {
										$("#detail-jcarousel-product-<?=$period."-".$giftID?>").jcarousel('scroll', '-=1');
										
										return false;
									});
										
									$("#detail-jcarousel-product-<?=$period."-".$giftID?>-down").on("click", function() {
										$("#detail-jcarousel-product-<?=$period."-".$giftID?>").jcarousel('scroll', '+=1');
										
										return false;
									});	
								</script>
						</div>
						<?endforeach;?>
						<div class="product-info total">
							<div class="plus equal">=</div>
							<div class="product-price">
								<?=GetPriceHTMLFormat($totalPrice)?>
							</div>
							
							<?if(!$productInBasket & !$notAvailable):?>
								<div class="pay_button_in_gift">
								<?=GetMessageByPattern ( BUTTON_BUY_WITH_GIFT_IN_SLADER, array ("#PRODUCT_ID#" => $productInfo ["ID"],"#PRICE#" => $productPrice,"#GIFTS_ID#" => base64_encode ( serialize ( $productInfo ["PROPERTY_GIFTS_VALUE"] ) ) ) );?>
								</div>
							<?endif;?>
							
							<?if($isSet):?>
								<div class="economy">
									Экономия: <?=GetPriceHTMLFormat($fullTotalPrice - $totalPrice, "тг.", false)?>
								</div>
							<?endif;?>	  	    				
						</div>
						
						<?$firstProduct = count($actionsInfo["GIFT_ACTIONS"][$giftID]["LIST_GIFT"]) ? current($actionsInfo["GIFT_ACTIONS"][$giftID]["LIST_GIFT"]): current($actionsInfo["GIFT_ACTIONS"][$giftID]["LIST_GIFT_FULL"]);?>
			
					</li>
					<?endforeach;?>
				</ul>
			</div>

			<?if (count($actionsInfo["LINK_GIFT_ACTIONS"]) > 1):?>
				<a href="#" id="detail-jcarousel-gift-prev"></a> <a href="#"
				id="detail-jcarousel-gift-next"></a>
			<?endif;?>
			</div>
	</div>

</div>
<div style="display: none;">
	<div id="add-to-basket-answer"></div>
	<a id="add-to-basket-link" href="#add-to-basket-answer"></a>
</div>


<script>
$(function() {
	$('#detail-jcarousel-gift').jcarousel({
		wrap: 'circular',
	});
		
	$("#detail-jcarousel-gift-prev").on("click", function() {
		$("#detail-jcarousel-gift").jcarousel('scroll', '-=1');
		return false;
	});
		
	$("#detail-jcarousel-gift-next").on("click", function() {
		$("#detail-jcarousel-gift").jcarousel('scroll', '+=1');
		return false;
	});
});
</script>

<?*/?>


