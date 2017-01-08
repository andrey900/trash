<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$arElement = $arResult['ELEMENTS'][0];?>
<div class="bg-sing" style="background-color: <?=$arResult['BACKGROUND_COLOR'];?>;">
	<?if(!empty($arElement)):?>
	<div class="col-md-7 sing-thumb">
		<div class="sing-thumb-in">
			<img class="replace-2x" class="replace-2x"
				<?if(isPictureWorksafe($arElement['DETAIL_PICTURE']['ID'])):?>
				worksafe = "Y" 
				<?endif;?>
				src="<?=$arElement['DETAIL_PICTURE']['src']?>" width="<?=$arElement['DETAIL_PICTURE']['width']?>"
				height="<?=$arElement['DETAIL_PICTURE']['height']?>" alt="<?=$arElement['~NAME']?>">
		</div>
	</div>
	<div class="col-md-5 sing-info">
		<div class="sing-info-in">
			<?=showProductPricesHtml($arElement['PROPERTY_BASE_PRICE_VALUE'], $arElement['PROPERTY_DISCOUNT_PRICE_VALUE'], 'monthly');?>
			<div class="sing-tit">
				<a href="<?=$arElement['DETAIL_PAGE_URL']?>"> <span> <?=$arElement['~NAME'];?></span>
				</a>
			</div>
			<div class="sing-text"><?=$arElement['~PREVIEW_TEXT']?></div>
			<div class="det-slid sing-by">
				<a href="#">В корзину</a>
			</div>
		</div>
	</div>
	<?endif;?>
</div>
