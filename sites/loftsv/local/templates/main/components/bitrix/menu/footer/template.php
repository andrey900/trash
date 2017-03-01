<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (empty($arResult)) return;?>

<p class="h4 footer-title border-left"><?=array_shift($arResult)['TEXT']?></p>
<ul class="footer-menu">
<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
	<li><a href="<?=$arItem["LINK"]?>" <?=(!$arItem["SELECTED"])?:'class="active"';?>>
	<i class="zmdi zmdi-circle"></i><span><?=$arItem["TEXT"]?></span></a></li>
	
<?endforeach?>
</ul>