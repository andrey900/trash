<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$Property = $arParams["PROPERTY"];?>

<?$cnt = $Property->ValuesCount();
if($cnt > 0):?>
<!-- One filter -->
	<div class="filt filter-section marg_b_15">
  	<a href="javascript: void(0);" class="bg_grey com_show filter_prop_<?=$Property->GetID()?>" onclick="$(function(){$('.comments_container.filter_prop_<?=$Property->GetID()?>').fadeOut(500); $('.com_hide.filter_prop_<?=$Property->GetID()?>').fadeIn(0); $('.com_show.filter_prop_<?=$Property->GetID()?>').fadeOut(0);})"><span class="plus"></span><?=$Property->GetParam("TITLE")?></a>
    <a href="javascript: void(0);" class="com_hide filter_prop_<?=$Property->GetID()?>" onclick="$(function(){$('.comments_container.filter_prop_<?=$Property->GetID()?>').fadeIn(1000); $('.com_show.filter_prop_<?=$Property->GetID()?>').fadeIn(0); $('.com_hide.filter_prop_<?=$Property->GetID()?>').fadeOut(0);})" style="display:none;"><span class="plus"></span><?=$Property->GetParam("TITLE")?></a>

    <div class="comments_container filter_prop_<?=$Property->GetID()?> bg_sand">
    	<div class="use-filt">
        <?foreach($Property->GetValues() as $arVal):?>
	      	<div class="one-ch1 one-line">
	        	<label value="<?=$arVal['ID']?>">
	          	<b><?=$arVal["NAME"]?> <span>(<?=$arVal["~COUNT"]?>)</span></b>
  	      		<?$is_check = $Property->IsValueSelected($arVal['ID'])?'disabled="disabled" checked="checked"':"";?>
	          	<input onchange="ReCalcCountInFilter(this)" <?=$is_check?> type="checkbox" name="<?=$arParams["FILTER_NAME"]?>[<?=$Property->GetID()?>][]" value="<?=$arVal['ID']?>" id="label_<?=$Property->GetID()?>_<?=$arVal['ID']?>" /> 
	          </label>
	        </div>
				<?endforeach;?>
 	    </div>
    </div>
  </div>
<!-- The end One filter -->
<?endif;?>
