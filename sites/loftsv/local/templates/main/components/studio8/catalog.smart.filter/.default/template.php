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
$this->setFrameMode(true);

?>
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
<?//p($arResult);?>

<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
<?if(($arItem['CODE'] == 'BRAND' && $arParams['DISABLE_BRAND']) || !$arItem['VALUES']) continue;?>
<aside class="widget operating-system box-shadow mb-30 row" id="<?=$arItem['CODE'];?>">
    <p class="h6 widget-title border-left mb-20 col-sm-3 col-md-12">
    	<span class="hidden-xs"><?=$arItem['NAME']?></span>
    	<a href="javascript:void(0);" class="mobile-filter-toggle visible-xs-inline"><?=$arItem['NAME']?></a>
    </p>
    <div class="hidden-xs col-sm-9 col-md-12">
    	<?foreach($arItem['VALUES'] as $item):?>
        <label data-role="label_<?=$item["CONTROL_ID"]?>" class="bx-filter-param-label <? echo $item["DISABLED"] ? 'disabled': '' ?>" for="<? echo $item["CONTROL_ID"] ?>">
        	<input
        		type="checkbox"
				value="<? echo $item["HTML_VALUE"] ?>"
				name="<? echo $item["CONTROL_NAME"] ?>"
				id="<? echo $item["CONTROL_ID"] ?>"
				url="/<? echo $item["URL_ID"] ?>/"
				<? echo $item["CHECKED"]? 'checked="checked"': '' ?>
        	><?=$item['VALUE']?>
        </label>
        <?endforeach;?>
    </div>
</aside>
<?endforeach;?>


	<input class="btn btn-themes" type="submit" name="set_filter" value="Применить">
	<input
		class="btn btn-link"
		type="submit"
		id="del_filter"
		name="del_filter"
		value="<?=GetMessage("CT_BCSF_DEL_FILTER")?>"
	/>
</form>