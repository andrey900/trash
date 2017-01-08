<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if(!empty($arResult['ELEMENTS'])):?>
<div class="can-be">
	<div class="container cat-h">
		<div class="container carus">
			<div class="can-be-tit">Сопутствующие товары</div>
			<div class="bxslider-carus row">
				<!-- Один товар -->
				<?foreach($arResult['ELEMENTS'] as $arElement):?>
				<div class="col-md-3 col-sm-6 col-xs-6 one-it">
					<a href="<?=$arElement['DETAIL_PAGE_URL'];?>">
						<div class="one-it-thumb">
							<div class="one-it-thumb-in">
								<img class="replace-2x"
									<?if(isPictureWorksafe($arElement['PREVIEW_PICTURE'])):?>
									worksafe="Y"
									<?endif;?>
									src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>"
									width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" 
									alt="<?=$arElement['~NAME']?>">
							</div>
						</div>
						<div class="one-it-tit">
							<span><?=$arElement['~NAME']?></span>
						</div>
						<?=showProductPricesHtml($arElement['PROPERTY_BASE_PRICE_VALUE'], $arElement['PROPERTY_DISCOUNT_PRICE_VALUE']);?>
					</a> 
					<a href="#">
						<div class="one-it-by">В корзину</div>
					</a>
				</div>
				<?endforeach;?>
			</div>
		</div>
	</div>
</div>
<?endif;?>
