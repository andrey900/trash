<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$Property = $arParams["PROPERTY"];
$index = $Property->GetParam("SORT");
$arParams['SELECTED_VALUES']->BanHiddenFieldFor($Property->GetID());
?>
<?$cnt = $Property->ValuesCount();
if($cnt > 0):?>
    <div class="form-part">
        <span class="form-text"><?=$Property->GetParam("TITLE");?></span>
        <div class="price-form">              
            <input id="min_price" name="filter[<?=$Property->GetID();?>][f]" onfocus="if(this.value == '<?=GetMessage("FROM");?>'){this.value = '';}" type="text" onblur="if(this.value == ''){this.value='<?=GetMessage("FROM");?>';}" value="<?=(int)$_REQUEST['filter'][$Property->GetID()]['f']>0?$Property->GetValue('MIN'):GetMessage("FROM");?>" class="in-price fir">
            <input id="max_price" name="filter[<?=$Property->GetID();?>][t]" onfocus="if(this.value == '<?=GetMessage("TO");?>'){this.value = '';}" type="text" onblur="if(this.value == ''){this.value='<?=GetMessage("TO");?>';}" value="<?=(int)$_REQUEST['filter'][$Property->GetID()]['t']>0?$Property->GetValue('MAX'):GetMessage("TO");?>" class="in-price">    
            <input type="submit" value="ок" style="width: 25px; padding: 0 0 1px 3px; margin-top: -2px; margin-left: 5px;">
        </div>
    </div>
<?endif;?>