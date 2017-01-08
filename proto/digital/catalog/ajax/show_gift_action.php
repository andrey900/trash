<?define("NO_KEEP_STATISTIC", true); // Отключение сбора статистики для AJAX-запросов ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?


if (!(CModule::IncludeModule("iblock") && isset($_REQUEST["id"]) && isset($_REQUEST["product_id"]))) return;


global $USER;
//$_REQUEST["id"] = unserialize(base64_decode($_REQUEST["id"]));

$actions = new CCustomGiftAction(SHARE_CATALOG_IBLOCK_ID, GIFTS_IBLOCK_ID, $_REQUEST["id"]);
$action = $actions->GetInfo();


$arProductPrice = CCatalogProduct::GetOptimalPrice($_REQUEST["product_id"], 1, $USER->GetUserGroupArray(), "N");
$productPrice = $arProductPrice['PRICE']['PRICE'];

//$productPrice = GetElementPrices($_REQUEST["product_id"]); //prices array

//p($arPrice);

$productInfo = CIBlockExt::GetElementInfo($_REQUEST["product_id"]);

$arImages = explode(";", $productInfo["PROPERTY_URL_KARTINOK_VALUE"]);

?>
<div class="popup-intro">
	<div class="pop-up-title">Акционные предложения</div>
</div>
<a class="jqmClose close"><i></i></a>

<div class='gift-info' data-dom-cache="false" data-cache="never">

	<div class='gift-list'>
		<div class="jcarousel-pagination" id="jcarousel-gift-pagination"></div>
			<div class='list'>
			    <div class="jcarousel-gift" id="jcarousel-gift">
			  	    <ul>
			  	    	<?$count = 0;?>
						<?foreach ($action["LINK_GIFT_ACTIONS"] as $period => $arGifts):?>
			  	    	<li period_hash="<?=base64_encode($period)?>" class="gift-proposition">
			  	    		<?$count++;?>
							<?$actionName = "";?>
			  	    		<?$isSet = false;?>
			  	    		<?$countSets = 0;?>
							<?foreach ($arGifts["GIFT_ID"] as $giftID => $nameGift) 
							{
								// формируем наименование акции
								if (empty($actionName)) { $actionName = $nameGift; } else { $actionName .= " + ".$nameGift; }
								// ищем комплекты
								if ($action["GIFT_ACTIONS"][$giftID]["IS_SET"] == "Y") {
									$isSet = true;
									$countSets++;
								}
							}
							?>
							<div class="title-name"><?=$actionName?></div>
							<table width="100%" cellpadding="0" cellspacing="0" border="1" class="gift-table">
								<tr valign="top">
									<td class="gift-good_block">
										<!-- main product -->
										<div class="gift-product-info">
						  	    			<div class="product-image">
						  	    				<img src="<?=CImageEx::Resize(array(
															"SOURCE" =>  CFile::GetPath($productInfo['DETAIL_PICTURE']),
															"WIDTH" => 130,
															"HEIGHT" => 130,
															));?>" />
						  	    			</div>
						  	    			
						  	    			<?
						  	    			if ($isSet)
						  	    				$totalPrice = $action["GIFT_ACTIONS"][$giftID]["PRICE_PRODUCT_1_SET"];
						  	    			else
						  	    				$totalPrice = $productPrice;
						  	    			
						  	    			$fullTotalPrice = $productPrice;
						  	    			?>
						  	    			
						  	    			<div class="product-name">
						  	    				<a href="<?=$productInfo["DETAIL_PAGE_URL"]?>">
						  	    					<?=TruncateStr($productInfo["NAME"], 80)?>
						  	    				</a>
						  	    			</div>
						  	    			
						  	    			<?if($isSet):?>
							  	    			<div class="product-price old">
							  	    				<?=GetPriceHTMLFormat($productPrice)?>
							  	    			</div>
											  	<div class="product-price set">
											  		<?=GetPriceHTMLFormat($action["GIFT_ACTIONS"][$giftID]["PRICE_PRODUCT_1_SET"])?>
											  	</div>
						  	    			<?endif;?>
						  	    			
						  	    		</div>
						  	    		
						  	    		<!-- end main product -->
									</td>
									<td>
										<div class="gift-plus_gift <?=($firstProduct)?"first":""?>">+</div>
									</td>
									<td>
										<div class="jcarousel-product-ar">
											<?if (count($action["GIFT_ACTIONS"][$giftID]["LIST_GIFT"]) > 1 ):?>
									  	    		<div class="up" id="jcarousel-product-<?=$period."-".$giftID?>-up" data-cache="false"></div>
									  	    		<div class="down" id="jcarousel-product-<?=$period."-".$giftID?>-down" data-cache="false"></div>
									  	    		
									  	    		<script>
										  	    			$(document).ready( function(){
											  	  	      		$('#jcarousel-product-<?=$period."-".$giftID?>').jcarousel({
												  		        	wrap: 'circular',
												  		        	vertical: true,
												  		        });
							
																$("#jcarousel-product-<?=$period."-".$giftID?>-up").on("click", function(event) {
																	$("#jcarousel-product-<?=$period."-".$giftID?>").jcarousel('scroll', '-=1');
																});
																
																$("#jcarousel-product-<?=$period."-".$giftID?>-down").on("click", function(event) {
																	$("#jcarousel-product-<?=$period."-".$giftID?>").jcarousel('scroll', '+=1');
																});	
										  	    			});
									  	    			</script>
									  	    		
								  	    		<?endif;?>
								  	    </div>
									</td>
									<td>
										<?$firstProduct = true;?>
										
						  	    		<?foreach ($arGifts["GIFT_ID"] as $giftID => $giftName):?>
						  	    			<div>
							  	    			<?$firstProduct = false;?>
							  	    			<div class="gift-jcarousel-skin-default">
										            <div class="gift-jcarousel-vertical">
										                <div class="gift-jcarousel" id="jcarousel-product-<?=$period."-".$giftID?>" link_period_hash="<?=base64_encode($period)?>" data-selected='0'>
								  	    					<ul>
								  	    						<?foreach ($action["GIFT_ACTIONS"][$giftID]["LIST_GIFT"] as $productID => $arProduct):?>
										  	    					<li product_id="<?=$productID?>" style="height:170px">   					
										  	    						<table cellpadding="0" cellspacing="0" border="0" width="100%" height="170px;">
										  	    							<tr valign="top">
										  	    								<td class="gift-good_block">
															  	    				<div class="product-image show-tooltip-<?=$productInfo["ID"]?>">
															  	    					<img src="<?=CImageEx::Resize(array(
																									"SOURCE" => $arProduct["PREVIEW_PICTURE"],
																									"WIDTH" => 130,
																									"HEIGHT" => 130,
																									));?>" />
															  	    				</div>				
															  	    				
																  	    			<div class="product-name">
																  	    				<a href="<?=$arProduct["DETAIL_PAGE_URL"]?>">
																  	    					<?=TruncateStr($arProduct["NAME"], 80)?>
																  	    				</a>
																  	    			</div>
															  	    				
															  	    				<?$fullTotalPrice += $arProduct["CATALOG_PRICE_".PRICE_BASE_ID];?>
															  	    				<?if($isSet):?>
															  	    					<?$giftPrice = $action["GIFT_ACTIONS"][$giftID]["PRICE_PRODUCT_2_SET"];?>
										
																  	    				<div class="product-price old">
																  	    					<?=GetPriceHTMLFormat($arProduct["CATALOG_PRICE_".PRICE_BASE_ID])?>
																  	    				</div>
															  	    					
																  	    				<div class="product-price set">
																  	    					<?=GetPriceHTMLFormat($giftPrice)?>
																  	    				</div>
																  	    			<?else:?>
																  	    				<?$giftPrice = $action["GIFT_ACTIONS"][$giftID]["PRICE_GIFT"];?>
																  	    			<?endif;?>
																  	    		</td>
																  	    		<td>
																  	    			<div class="gift-plus_gift">=</div>
																  	    		</td>
																  	    		<td class="gift-good_block" align="right">
																	  	    		<div class="product-info total">
																	  	    			<div class="product-price">
																	  	    				<?=CurrencyFormat($totalPrice, CURRENCY_BASE);?>
																	  	    			</div>
																						<?if($isSet):?>
																							<div class="economy">
																								Экономия: <?=CurrencyFormat($fullTotalPrice - $totalPrice, CURRENCY_BASE)?>
																							</div>
																						<?endif;?>	  	    				
																	  	    		</div>
															  	    		
																					<?if(isset($_REQUEST["buy"])):?>
																						<div style="clear:both"></div>
																						<div class="i-want-it">
																							<span class="add_to_basket_with_gift_ajax"> Хочу это! </span>
																						</div>	
																					<?endif;?>	
																					<a data-quantity="1" data-item="6021" href="javascript:void(0)" rel="nofollow" class="basket_button to-cart button30"><i></i><span>В корзину</span></a>
																  	    		</td>
																  	    	</tr>	
													  	    			</table>
												  	    			</li>
								  	    						<?endforeach;?>
								  	    						<?$totalPrice += $giftPrice;?>
										  	    			</ul>
										  	    		</div>
										  	    	</div>
							  	    			</div>
						  	    			</div>
						  	    		<?endforeach;?>
									</td>
									<?php /*?>
									<td>
										<?if (count($action["GIFT_ACTIONS"][$giftID]["LIST_GIFT"]) > 1 ):?>
									  	    		<div class="up" id="jcarousel-product-<?=$period."-".$giftID?>-up" data-cache="false"></div>
									  	    		<div class="down" id="jcarousel-product-<?=$period."-".$giftID?>-down" data-cache="false"></div>
									  	    		
									  	    		<script>
										  	    			$(document).ready( function(){
											  	  	      		$('#jcarousel-product-<?=$period."-".$giftID?>').jcarousel({
												  		        	wrap: 'circular',
												  		        	vertical: true,
												  		        });
							
																$("#jcarousel-product-<?=$period."-".$giftID?>-up").on("click", function(event) {
																	$("#jcarousel-product-<?=$period."-".$giftID?>").jcarousel('scroll', '-=1');
																});
																
																$("#jcarousel-product-<?=$period."-".$giftID?>-down").on("click", function(event) {
																	$("#jcarousel-product-<?=$period."-".$giftID?>").jcarousel('scroll', '+=1');
																});	
										  	    			});
									  	    			</script>
									  	    		
								  	    		<?endif;?>
									</td>
									<td>
										<div class="gift-plus_gift">=</div>
									</td>
									<td class="gift-good_block">
						  	    		<div class="product-info total">
						  	    			<div class="product-price">
						  	    				<?=CurrencyFormat($totalPrice, CURRENCY_BASE);?>
						  	    			</div>
											<?if($isSet):?>
												<div class="economy">
													Экономия: <?=CurrencyFormat($fullTotalPrice - $totalPrice, CURRENCY_BASE)?>
												</div>
											<?endif;?>	  	    				
						  	    		</div>
				  	    		
										<?if(isset($_REQUEST["buy"])):?>
											<div style="clear:both"></div>
											<div class="i-want-it">
												<span class="add_to_basket_with_gift_ajax"> Хочу это! </span>
											</div>	
										<?endif;?>	
									</td>
									<?*/?>
								</tr>
							</table>
			  	    	</li>
			  	    	<?endforeach;?>
			        </ul>
				</div>
		
			<?if (count($action["LINK_GIFT_ACTIONS"]) > 1):?>
			      <a href="#" id="jcarousel-gift-prev"></a>
			      <a href="#" id="jcarousel-gift-next"></a>
		    <?endif;?>
		</div>
	</div>
</div>
<script>
$(document).ready( function(){
    $('#jcarousel-gift').jcarousel({
	   	wrap: 'circular',
    });
        
		$("#jcarousel-gift-prev").on("click", function() {
			$("#jcarousel-gift").jcarousel('scroll', '-=1');
			return false;
		});
	
		$("#jcarousel-gift-next").on("click", function() {
			$("#jcarousel-gift").jcarousel('scroll', '+=1');
			return false;
		});

		<?/*if(isset($_REQUEST["buy"])):?>
			$(".add_to_basket_ajax").on("click", function(){
				$.post(
					"/catalog/ajax/add_to_basket.php",
					{
						product_id: <?=$_REQUEST["product_id"]?>,
					},"html").done(function( data ){
						alert(data);
						$(".gift-info.popup").html(data);
						$.fancybox.update();
						setTimeout( function () { location.reload(); }, 3000);
					});
			});
			
			$(".add_to_basket_with_gift_ajax").on("click", function(){
				var selected_action = $('#jcarousel-gift').jcarousel('visible');
				period_hash = selected_action.attr("period_hash"); 
				var ar_product_id = [];
				
				$('.gift-info.popup [link_period_hash="'+period_hash+'"]').each( function( index ) {
					var selected_product = $(this).jcarousel('visible');
					ar_product_id[index] = selected_product.attr("product_id");
				});

				$.post(
					"/catalog/ajax/add_to_basket_with_gift.php",
					{
						product_id: <?=$_REQUEST["product_id"]?>,
						period_hash: period_hash,
						ar_product_id: ar_product_id,
						ar_gift_id: "<?=base64_encode(serialize($_REQUEST["id"]))?>",  
					},"html").done(function( data ){
						$(".gift-info.popup").html(data);
						$.fancybox.update();
						setTimeout( function () { location.reload(); }, 3000);
					});
			});
		<?endif;*/?>
});

</script>
