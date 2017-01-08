<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

	<div class="item_slider">
		<div class="ribbons">
			<?if (is_array($arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
				<?if( in_array("HIT", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) ){?><span class="ribon_hit"></span><?}?>
				<?if( in_array("STOCK", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) || is_array($arResult["STOCK"]) && !empty($arResult["STOCK"])){?><span class="ribon_action"></span><?}?>
				<?if( in_array("NEW", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="ribon_new"></span><?}?>
				<?if( in_array("RECOMMEND", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="ribon_recomend"></span><?}?>
				<?if( in_array("XML_GIFT", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="ribon_present"></span><?}?>		
			<?endif;?>				
		</div>
		<ul class="slides">
			<?
			$images = array();
			if( is_array( $arResult["DETAIL_PICTURE"] ) ){$images[] = $arResult["DETAIL_PICTURE"];}
			foreach( $arResult["MORE_PHOTO"] as $arPhoto ){	$images[] = $arPhoto;}
			?>
			<?foreach( $images as $key => $arPhoto ){?>
				<li id="photo-<?=$key?>" <?=$key == 0 ? 'class="current"' : ''?>>
					<?$img = CFile::ResizeImageGet( $arPhoto, array( "width" => 800, "height" => 600 ), BX_RESIZE_IMAGE_PROPORTIONAL, true, array() );?>
					<a href="<?=$img["src"]?>" rel="item_slider" class="fancy">
						<span class="zoom" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>"><i></i></span><b class="triangle"></b>
						<div class="marks">
							<?if (is_array($arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
								<?if( in_array("HIT", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) ){?><span class="mark hit"></span><?}?>
								<?if( in_array("STOCK", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) || is_array($arResult["STOCK"]) && !empty($arResult["STOCK"])){?><span class="mark share"></span><?}?>
								<?if( in_array("NEW", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="mark hit"></span><?}?>
								<?if( in_array("RECOMMEND", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="mark like"></span><?}?>	
							<?endif;?>								
						</div>
						<?$img = CFile::ResizeImageGet( $arPhoto, array( "width" => 310, "height" => 310 ), BX_RESIZE_IMAGE_PROPORTIONAL, true, array() );?>
						<img border="0" src="<?=$img["src"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" />
					</a>
				</li>
			<?}?>
			<?if( count($images) == 0 ){?>
				<li class="current">
					<div class="marks">
						<?if (is_array($arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
							<?if( in_array("STOCK", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) || is_array($arResult["STOCK"]) && !empty($arResult["STOCK"])){?><span class="mark share"></span><?}?>
							<?if( in_array("HIT", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) ){?><span class="mark hit"></span><?}?>
							<?if( in_array("RECOMMEND", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="mark like"></span><?}?>
							<?if( in_array("NEW", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="mark new"></span><?}?>
						<?endif;?>	
					</div>
					<img border="0" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" />
				</li>
			<?}?>
		</ul>
		<?if( count($images) > 1 ){?>
			<div class="thumbs">
				<ul id="thumbs">
					<?foreach( $images as $key => $arPhoto ){?>
						<?$img = CFile::ResizeImageGet( $arPhoto, array( "width" => 72, "height" => 72 ), BX_RESIZE_IMAGE_PROPORTIONAL, true, array() );?>
						<li <?=$key == 0 ? 'class="current"' : ''?>>
							<i class="triangle"><b></b></i>
							<a >
								<img border="0" src="<?=$img["src"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" />
							</a>
						</li>
					<?}?>
					<?if (count($images)>3):?><?endif;?>
				</ul>
			</div>
			<span class="thumbs_navigation"></span>
			<script>
				
			</script>
		<?}?>
	</div>
	
	<script>
		$(".thumbs").flexslider({
			animation: "slide",
			selector: "#thumbs > li",
			slideshow: false,
			animationSpeed: 600,
			directionNav: true,
			controlNav: false,
			pauseOnHover: true,
			itemWidth: 99, 
			animationLoop: false, 
			controlsContainer: ".thumbs_navigation",
		});
		$('.item_slider .thumbs li').first().addClass('current');
		$('.item_slider .thumbs').delegate('li:not(.current)', 'click', function(){
			$(this).addClass('current').siblings().removeClass('current').parents('.item_slider').find('.slides li').fadeOut(333);
			$(this).parents('.item_slider').find('.slides li').eq($(this).index()).addClass("current").stop().fadeIn(333);
	})	
	</script>
	<? // for mobile devices?>
	<div class="item_slider flex">
		<div class="ribbons">
			<?if (is_array( $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
				<?if( in_array("HIT", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) ):?><span class="ribon_hit"></span><?endif;?>
				<?if( in_array("RECOMMEND", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="ribon_recomend"></span><?endif;?>
				<?if( in_array("NEW", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="ribon_new"></span><?endif;?>
				<?if( in_array("STOCK", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) || is_array($arResult["STOCK"]) && !empty($arResult["STOCK"])):?><span class="ribon_action"></span><?endif;?>
				<?if( in_array("XML_GIFT", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?><span class="ribon_present"></span><?endif;?>
			<?endif;?>
		</div>
		<ul class="slides">
			<?
			$images = array();
			if( is_array( $arResult["DETAIL_PICTURE"] ) ){$images[] = $arResult["DETAIL_PICTURE"];}
			foreach( $arResult["MORE_PHOTO"] as $arPhoto ){	$images[] = $arPhoto;}
			?>
			<?foreach( $images as $key => $arPhoto ){?>
				<li id="photo-<?=$key?>" <?=$key == 0 ? 'class="current"' : ''?>>
					<?$img = CFile::ResizeImageGet( $arPhoto, array( "width" => 800, "height" => 600 ), BX_RESIZE_IMAGE_PROPORTIONAL, true, array() );?>
					<a href="<?=$img["src"]?>" rel="item_slider" class="fancy">
						<span class="zoom" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>"><i></i></span><b class="triangle"></b>
						<div class="marks">
							<?if (is_array( $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
								<?if( in_array("STOCK", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) || is_array($arResult["STOCK"]) && !empty($arResult["STOCK"])){?><span class="mark share"></span><?}?>
								<?if( in_array("HIT", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) ){?><span class="mark hit"></span><?}?>
								<?if( in_array("RECOMMEND", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="mark like"></span><?}?>
								<?if( in_array("NEW", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="mark new"></span><?}?>
							<?endif;?>
						</div>
						<?$img = CFile::ResizeImageGet( $arPhoto, array( "width" => 310, "height" => 310 ), BX_RESIZE_IMAGE_PROPORTIONAL, true, array() );?>
						<img border="0" <?=$key == 0 ? 'itemprop="image"' : ''?> src="<?=$img["src"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" />
					</a>
				</li>
			<?}?>
			<?if( count($images) == 0 ){?>
				<li class="current">
					<div class="marks">
						<?if (is_array( $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
							<?if( in_array("STOCK", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) || is_array($arResult["STOCK"]) && !empty($arResult["STOCK"])){?><span class="mark share"></span><?}?>
							<?if( in_array("HIT", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"]) ){?><span class="mark hit"></span><?}?>
							<?if( in_array("RECOMMEND", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="mark like"></span><?}?>
							<?if( in_array("NEW", $arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])){?><span class="mark new"></span><?}?>
						<?endif;?>
					</div>
					<img border="0" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" />
				</li>
			<?}?>
		</ul>
	</div>
	
	<script>
		$(".item_slider.flex").flexslider({
			animation: "slide",
			slideshow: true,
			slideshowSpeed: 10000,
			animationSpeed: 600,
			directionNav: false,
			pauseOnHover: true
		});	
	</script>