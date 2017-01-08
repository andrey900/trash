<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$Property = $arParams["PROPERTY"];
$index = $Property->GetParam("SORT");
$arParams['SELECTED_VALUES']->BanHiddenFieldFor($Property->GetID());
?>
<?$cnt = $Property->ValuesCount();
if($cnt > 0):?>
<?$minValue = (int)$_REQUEST['filter'][$Property->GetID()]['f']>0?(int)$_REQUEST['filter'][$Property->GetID()]['f']:$Property->GetValue('MIN');?>
<?$maxValue = (int)$_REQUEST['filter'][$Property->GetID()]['t']>0?(int)$_REQUEST['filter'][$Property->GetID()]['t']:$Property->GetValue('MAX');?>
	<!-- One filter -->
  <div id="filter_price" class="filt" style="border-top: 0px">
  	<a href="javascript: void(0);" class="com_show filter_prop_<?=$Property->GetID()?>" onclick="$(function(){$('.comments_container.filter_prop_<?=$Property->GetID()?>').fadeOut(500); $('.com_hide.filter_prop_<?=$Property->GetID()?>').fadeIn(0); $('.com_show.filter_prop_<?=$Property->GetID()?>').fadeOut(0);})"><span class="l_text"><?=$Property->GetParam("TITLE");?></span><span class="plus"></span></a>
    <a href="javascript: void(0);" class="com_hide filter_prop_<?=$Property->GetID()?>" onclick="$(function(){$('.comments_container.filter_prop_<?=$Property->GetID()?>').fadeIn(1000); $('.com_show.filter_prop_<?=$Property->GetID()?>').fadeIn(0); $('.com_hide.filter_prop_<?=$Property->GetID()?>').fadeOut(0);})" style="display:none;"><span class="l_text"><?=$Property->GetParam("TITLE");?></span><span class="plus"></span></a>
            
    <div class="comments_container filter_prop_<?=$Property->GetID()?>">
    	<div class="use-filt">
      	<div class="filt-slider">
        	<form>
          	<div class="one-f">
            	<div class="ot">от</div>
            	<input id="min_price" init_price="<?=$minValue?>" name="filter[<?=$Property->GetID();?>][f]" type="text" class="sliderValue" data-index="0" value="<?=$minValue?>" />
            </div>
            <div class="one-f las">
            	<div class="ot">&nbsp;до</div>
            	<input id="max_price" init_price="<?=$maxValue?>" name="filter[<?=$Property->GetID();?>][t]" type="text" class="sliderValue" data-index="1" value="<?=$maxValue?>" />
            </div>
            <br />
            <div id="slider"></div>
        </div>
      </div>
    </div>    
  </div>
  <!-- The end One filter -->
<?endif;?>


<script>
$(document).ready(function() {
    $("#slider").slider({
		range: true,
        min: <?=$minValue?>,
        max: <?=$maxValue?>,
        step: 1,
        values: [<?=$minValue?>,<?=$maxValue?>],
        slide: function(event, ui) {
            for (var i = 0; i < ui.values.length; ++i) {
                $("input.sliderValue[data-index=" + i + "]").val(ui.values[i]);
            }
        }
    });

    $("input.sliderValue").change(function() {
        var $this = $(this);
        $("#slider").slider("values", $this.data("index"), $this.val());
    });
});
</script>
