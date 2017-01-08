<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//p($arParams, false)?>
<input size="<?=$arParams["SIZE1"]?>" placeholder="Начните вводить название города" name="<?echo $arParams["CITY_INPUT_NAME"]?>_val" id="<?echo $arParams["CITY_INPUT_NAME"]?>_val" value="<?=$arResult["LOCATION_STRING"]?>" class="search-suggest" type="text" autocomplete="off" onfocus="loc_sug_CheckThis(this, this.id);" />
<input type="hidden" name="<?echo $arParams["CITY_INPUT_NAME"]?>" id="<?echo $arParams["CITY_INPUT_NAME"]?>" value="<?=$_REQUEST["ORDER_PROP_".$arParams["ORDER_PROPS_ID"]]?$_REQUEST["ORDER_PROP_".$arParams["ORDER_PROPS_ID"]]:$arResult["LOCATION_DEFAULT"]?>">

<div class="delivery-list">
<?
//favorites
foreach($arParams['LOC_DEFAULT'] as $arItem):?>
    <div class="delivery-pre city-pre">
        <input type="radio" onclick="" checked="" value="<?=$arItem['ID']?>" name="city_favor" id="ID_CITY_<?=$arItem['ID']?>">
        <label onclick="" for="ID_CITY_<?=$arItem['ID']?>">
            <div class="desc">
                <div class="paysystem_name" style="width: auto; cursor: pointer;">
                    <span><?=$arItem['CITY_NAME_LANG']?></span>
                </div>
                <div class="desc"></div>
                <div class="clear"></div>
            </div>
        </label>
        <div class="clear"></div>
	</div>
<?endforeach;?>
</div>

<script src="/bitrix/js/main/cphttprequest.js"></script>
<script type="text/javascript">

	if (typeof oObject != "object")
		window.oObject = {};

	document.loc_sug_CheckThis = function(oObj, id)
	{
		try
		{
			if(SuggestLoaded)
			{
				window.oObject[oObj.id] = new JsSuggest(oObj, '<?echo $arResult["ADDITIONAL_VALUES"]?>', '', '', '<?=CUtil::JSEscape($arParams["ONCITYCHANGE"])?>');
				return;
			}
			else
			{
				setTimeout(loc_sug_CheckThis(oObj, id), 10);
			}
		}
		catch(e)
		{
			setTimeout(loc_sug_CheckThis(oObj, id), 10);
		}
	}
</script>
