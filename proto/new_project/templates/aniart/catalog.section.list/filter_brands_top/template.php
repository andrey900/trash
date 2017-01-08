<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);?>

<? if( !empty($arResult['SECTION']['IBLOCK_SECTION_ID']) ):?>
	<!--<pre><?print_r($arResult['SECTION']['UF_FILTER_BRANDS_COUNT'])?></pre>-->
	<?if( $arResult['SECTION']['UF_FILTER_BRANDS_COUNT'] > 0 ):?>
	<h2 class="brand-filter-name">Производители</h2>
	<form method="get">
		<ul class="bl_sub" style="display: block;">
			<?php
			for ($i=1; $i < 10; $i++) {?>
				<?if( isset($arResult['SECTION']['UF_FILTER_BRANDS_'.$i]['ID']) ):?>
				<li class="f_brands"><label><span><input type="checkbox" name="vendors[]" value="<?=$arResult['SECTION']['UF_FILTER_BRANDS_'.$i]['ID']?>" <?=(in_array($arResult['SECTION']['UF_FILTER_BRANDS_'.$i]['ID'], $_REQUEST['vendors']))?'checked':''?> code-name="<?=$arResult['SECTION']['UF_FILTER_BRANDS_'.$i]['CODE']?>" ></span><p><?=$arResult['SECTION']['UF_FILTER_BRANDS_'.$i]['NAME']?></p></label></li>
				<?endif;?>
			<?} //endFor?>
			<?foreach ($arResult['SECTION']['ALL_BRANDS_FILTER'] as $value):?>
				<li class="filter-brands f_brands" style="display:none;"><label><span><input type="checkbox" name="vendors[]" value="<?=$value['ID']?>" <?=(in_array($value['ID'], $_REQUEST['vendors']))?'checked':''?> code-name="<?=$value['CODE']?>" ></span><p><?=$value['NAME']?></p></label></li>
			<?endforeach;?>
		</ul>
		<div class="show-all-brands"><a href="javascript:void(0)" data-active="Скрыть все бренды" data-noactive="Показать все бренды">Показать все бренды</a></div>
		<input type="submit" value="Применить">
		<?if( !empty($_REQUEST['vendors']) ):?>
			<div class="clear-all-brands"><a href="javascript:void(0)">Сбросить все фильтры</a></div>
		<?endif;?>
	</form><div class="st_line"></div>
	<?endif;?>
<? endif;?>
