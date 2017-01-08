<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="container head-sing">
	<h3>Основная фишка</h3>
	<div class="head-sing-thumb">
		<div class="head-sing-thumb-in">
			<img class="replace-2x"
				<?if(isPictureWorksafe($arElement['PROPERIES']['MF_PICTURE'])):?>
				worksafe="Y"
				<?endif;?> 
				src="<?=$arElement['PROPERTIES']['MF_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES']['MF_PICTURE']['WIDTH']?>"
				height="<?=$arElement['PROPERTIES']['MF_PICTURE']['HEIGHT']?>" alt="<?=$arElement['~NAME'];?>">
		</div>
	</div>
	<div class="col-md-6 text-f">
		<p>
			<?=$arElement['PROPERTIES']['MF_LEFT_COLUMN']['~VALUE']['TEXT']?>
		</p>
	</div>
	<div class="col-md-6 text-f">
		<p>
			<?=$arElement['PROPERTIES']['MF_RIGHT_COLUMN']['~VALUE']['TEXT']?>
		</p>
	</div>
	<?showProductPricesHtml($arElement['PROPERTIES']['BASE_PRICE']['VALUE'], $arElement['PROPERTIES']['DISCOUNT_PRICE']['VALUE'], 'detail-feature');?>
</div>