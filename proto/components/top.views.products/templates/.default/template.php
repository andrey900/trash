<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<? //p($arParams);?>

<?php foreach ($arResult['ELEMENTS'] as $arElement):?>
<div class="element">
	<div class="image">
	<a href="<?php echo $arElement['DETAIL_PAGE_URL'];?>">
		<img width="130" height="130" 
				alt="<?php echo $arElement['NAME'];?>"
				title="<?php echo $arElement['NAME'];?>" 
				src="<?php echo CFILE::GetPath($arElement['DETAIL_PICTURE']);?>">
	</a>
	</div>
	<div class="price"><?php echo substr($arElement['PROPERTY_MAXIMUM_PRICE_VALUE'], 0, -3)."<span>".substr($arElement['PROPERTY_MAXIMUM_PRICE_VALUE'], -3)."</span>";?></div>
	<h2>
		<a class="name" href="<?php echo $arElement['DETAIL_PAGE_URL'];?>">
			<?php echo $arElement['NAME'];?>
		</a>
	</h2>
	<div class="text"><?php echo substr($arElement['DETAIL_TEXT'], 0, 100);?></div>
	<div class="count_show"><b>+<?php echo $arElement['INFO_VIEWS']['day'];?></b> (<span><?php echo $arElement['INFO_VIEWS']['all'];?></span>)</div>
</div>
<?php endforeach;?>