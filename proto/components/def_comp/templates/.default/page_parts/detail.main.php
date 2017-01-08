<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="container">
	<div class="bg-sing" style="background-color: <?=$arElement['PROPERTIES']['BACKGROUND_COLOR']['VALUE']?>;">
		<div class="col-md-6 sing-thumb">
			<div class="sing-thumb-in">
				<img class="replace-2x"
					<?if(IsPictureWorksafe($arElement['DETAIL_PICTURE'])):?>
					worksafe="Y"
					<?endif;?>	 
					src="<?=$arElement['DETAIL_PICTURE']['SRC']?>" width="<?=$arElement['DETAIL_PICTURE']['WIDTH']?>"
					height="<?=$arElement['DETAIL_PICTURE']['HEIGHT']?>" alt="<?=$arElement['~NAME']?>" />
			</div>
		</div>
		<div class="col-md-6 sing-info">
			<div class="sing-info-in">
				<div class="sing-tree">
					<a href="/catalog/"><span>Магазин</span></a> / <a href="<?=$arResult['SECTION']['SECTION_PAGE_URL']?>"><span><?=$arResult['SECTION']['~NAME']?></span></a>
				</div>
				<div class="sing-tit">
					<a href="#"> <span><?=$arElement['~NAME']?></span>
					</a>
				</div>
				<div class="sing-text"><?=$arElement['~DETAIL_TEXT'] ? $arElement['~DETAIL_TEXT'] : $arElement['~PREVIEW_TEXT']?></div>
				<?showProductPricesHtml($arElement['PROPERTIES']['BASE_PRICE']['VALUE'], $arElement['PROPERTIES']['DISCOUNT_PRICE']['VALUE'], 'detail');?>
			</div>
		</div>
	</div>
</div>