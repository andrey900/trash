<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if(!empty($arResult['ELEMENTS'])):?>
<div class="row">
	<?foreach($arResult['ELEMENTS'] as $arElement):
		$backgroundColor = $arElement['PROPERTY_BACKGROUND_COLOR_CODE'];
		if(!$backgroundColor){
			$backgroundColor = $arResult['backgroundColors'][rand(0, 3)];
		}
	?>
	<div class="col-md-6 col-sm-6 col-xs-12 one-top">
		<div class="one-item" style="background-color: <?=$backgroundColor?>">
			<?if(!empty($arElement)):?>
			<div class="top-thumb">
				<div class="top-thumb-in">
					<img class="replace-2x" src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>"
						<?if(isPictureWorksafe($arElement['PREVIEW_PICTURE'])):?>
						worksafe = "Y"
						<?endif;?>
						height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['~NAME']?>">
				</div>
			</div>
			<div class="top-info">
				<?showProductPricesHtml($arElement['PROPERTY_BASE_PRICE_VALUE'], $arElement['PROPERTY_DISCOUNT_PRICE_VALUE'], 'main')?>
				<div class="top-tit"><?=$arElement['~NAME']?></div>
				<div class="top-tag">
					<a href="#" class="tag">
					<?foreach($arElement['PROPERTY_SELLING_FEATURE_VALUE'] as $SellingFeature):?>
					<?=$SellingFeature;?>
					<br />
					<?endforeach;?>
					</a>
				</div>
			</div>
			<?endif;?>
		</div>
	</div>
	<?endforeach;?>
</div>
<?endif;?>