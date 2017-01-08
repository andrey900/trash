<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<?    
	$Filter		= $arResult["FILTER"];
	$is_disks	= $Filter->GetParam("MAIN_IBLOCK") == 13;
?>
<noindex>
	<div id="itemsCounter">
		<div class="itemsCounter">
			<div class="itemsNum">
				Выбрано товаров:
				<span id="count-products">0</span>
			</div>
			<div class="itemsShow">
				<a id="link_submit" href="javascript:void(0)" onclick="$('#submit-filter').click();">Показать</a>
			</div>
			<div class="itemsPreload">
				<img width="16" height="16" title="preload" src="/bitrix/panel/main/images_old/wait.gif">
			</div>
		</div>
	</div>
</noindex>

<form rel="1" onsubmit="CheckFields(this)" id="form_<?=$arParams["FILTER_NAME"]?>" method="get" action="<?=$arParams["REQUEST_PAGE_URL"] ? $arParams['REQUEST_PAGE_URL'] : '/catalog/'.$_REQUEST['SECTION_CODE'].'/'?>">


<?if($Filter->GetSelectedValues()->SelectedValuesCount() > 0):?>
<!-- Selected filter -->
	<div class="filt all-filt" style="border-top: none; border-bottom: 1px solid #dadada;">
  	<a href="javascript: void(0);" class="com_show" onclick="$(function(){$('.comments_container').fadeOut(500); $('.com_hide').fadeIn(0); $('.com_show').fadeOut(0);})"><span class="l_text">Выбранные фильтры</span><span class="plus"></span></a>
    <a href="javascript: void(0);" class="com_hide" onclick="$(function(){$('.comments_container').fadeIn(1000); $('.com_show').fadeIn(0); $('.com_hide').fadeOut(0);})" style="display:none;"><span class="l_text">Выбранные фильтры</span><span class="plus"></span></a>
            
		<div class="comments_container">
    	<div class="use-filt">
      	<div class="one-use-filt">
    		<?foreach($Filter->GetSelectedValues()->Get() as $PropertID => $PropertyData):?>
    			<div class="group-value">
    			<?if(empty($PropertyData['VALUES'])) continue;?>
    					<span><?=$PropertyData["NAME"]?>:</span><br />
    					<?if ($PropertyData["NAME"] == "Цена"):?> <?// !!! к сожалению привязку приходится делать к имени свойства /ak@/?>
    						<p class="filter_selected_price">
									<a  onclick="ClearFilter('<?=$Filter->GetName();?>[<?=$PropertID?>]',false, this, true);" href="javascript:void(0);">
    							<?foreach($PropertyData["VALUES"] as $ValueID => $ValueData):?>
										<?$SelectedValues = $Filter->GetSelectedValues()->Get();?>
											<?if(!empty($ValueData["NAME"])):?>
												<?if($ValueData["INDEX"] === "f"):?>
												от
												<?elseif($ValueData["INDEX"] === "t"):?>
												до
												<?endif;?>
											<?endif;?>
											<?=str_replace("_", "/", $ValueData["NAME"]);?>
		    					<?endforeach;?>
		    					</a>
		    					<?foreach($PropertyData["VALUES"] as $ValueID => $ValueData):?>
										<input rel="skip" type="hidden" name="<?=$Filter->GetName()?>[<?=$PropertID?>]<?=isset($ValueData["INDEX"])?"[".$ValueData["INDEX"]."]":"";?>" value="<?=$ValueID?>">
		    					<?endforeach;?>
	    					</p>
    					<?else:?>
								<?foreach($PropertyData["VALUES"] as $ValueID => $ValueData):?>
    							<p>
    								<a onclick="ClearFilter('<?=$Filter->GetName();?>[<?=$PropertID?>]',<?=$ValueID?>, this, <?/*=(!empty($ValueData["INDEX"])?"true":"false")*/?>false);" href="javascript:void(0);"><?=str_replace("_", "/", $ValueData["NAME"]);?></a>
    							</p>
									<?if($Filter->GetSelectedValues()->NeedHiddenFieldFor($PropertID, $ValueID)):?>
										<?
										$arElement = CIBlockElement::GetList(array('CODE'), array('IBLOCK_ID' => BRANDS_IBLOCK_ID, 'ACTIVE' => 'Y', 'ID' => $ValueID))->Fetch();
										$brandCode = "";
										if($arElement){
											$brandCode = $arElement["CODE"];
										}
										?>
									
										<?if($PropertyData["MULTIPLE"] == "Y"):?>
										<input type="hidden" name="<?=$Filter->GetName()?>[<?=$PropertID?>][]" value="<?=$ValueID?>" <?if($brandCode):?> url="<?='/catalog/'.$_REQUEST['SECTION_CODE'].'/'?><?=$brandCode?>/" <?endif;?>>
										<?else:?>
										<input type="hidden" name="<?=$Filter->GetName()?>[<?=$PropertID?>]<?=isset($ValueData["INDEX"])?"[".$ValueData["INDEX"]."]":"";?>" value="<?=$ValueID?>">
										<?endif;?>
									<?endif;?>
								<?endforeach;?>
   					<?endif;?>
   					</div>
        	<?endforeach;?>
        </div>
        <a class="sbr" href="javascript:void(0);" onclick="ClearAllFilters(this)">Сбросить все</a>
      </div>
    </div>
  </div>
<!-- The end Selected filter -->
<?endif?>
<?if($Filter->PropertiesCount()>0):?>
	<?foreach($Filter->GetProperties() as $obProperty):?>
	<?=$obProperty->GetHtml();?>
	<?endforeach;?>
<?endif;?>

<div class="basket_button bt">
	<a id="submit-filter" class="send" onclick='CustomFilterSubmitForm(false, false, this, false)'  href="javascript:void(0);">Применить</a>
</div>        

</form>
<?
/*
 * @todo 
 * 1) Необходимо добавить в GET-параметры диапазон цен
 * 2) Повесить обработчик события отпускания кнопки на кнопках слайдера цен с задержкой в 0,5сек. 
 * И указать вызов функции ReCalcCountInFilter 
 */  
?>
<script>
	var close_timestamp = new Date().getTime();
	var brand_prop = <?=PROPERTY_BRAND_ID?>;
	
	function ReCalcCountInFilter(obj)
	{
		var form = $("#form_<?=$arParams["FILTER_NAME"]?>");
		var offset_form = form.offset();
		var offset_obj = $(obj).offset();
		
		$(".itemsCounter").css("display", "block").css("top", (offset_obj.top - offset_form.top - 13)  + "px");;
		
		var get_params = "?";
		// добавляем цены
		get_params = 
			get_params + 
			$("#min_price").attr("name") + "=" + $("#min_price").val() +
			"&" +
			$("#max_price").attr("name") + "=" + $("#max_price").val();

		var inputs = [];

		$( ".filt.all-filt input[type=hidden]" ).each(function( index ) {
			if ($(this).attr("rel") !== "skip"){
				get_params = get_params + "&" + $( this ).attr("name") + "=" + $( this ).val();
				//if($(this).attr("name") == "filter["+brand_prop+"][]"){
				//	inputs.push($(this));
				//}
			}
		});
			
		$( ".filter-section input[type=checkbox]" ).each(function( index ) {	
			if ($(this).attr("rel") !== "skip" && $(this).attr("checked") == "checked"){
				get_params = get_params + "&" + $( this ).attr("name") + "=" + $( this ).val();
				//if($(this).attr("name") == "filter["+brand_prop+"][]"){
				//	inputs.push($(this));
				//}
			}
		});
		get_params = get_params + "&section_id=" + <?=$arParams["SECTION_ID"]?>;
		
		if(inputs.length == 1){
			if(url = inputs[0].attr('url')){
				form.data('action', [form.attr('action')]);
				form.attr('action', url);
				//inputs[0].data('name', inputs[0].attr('name'));
				//inputs[0].attr('name', '');
			}
		}
		else{
			if(action = form.data('action')){
				form.attr('action', action[0]);
				form.data('action', null);
			}
			/*inputs.forEach(function($this){
				if(name = $this.data('name')){
					$this.attr('name', name);
					$this.data('name', null)
				}
			});*/
		}
		
		BX.ajax({
			url: "/catalog/ajax/count_product_in_filter.php" + get_params,
	    method: 'GET',
	    dataType: 'html',
	    cache: false,
	    onsuccess: function(data){
				$("#count-products").text(data);
				$(".itemsCounter .itemsPreload").css("display", "none");	    	
				$(".itemsCounter .itemsNum, .itemsCounter .itemsShow").css("display", "block");
				close_timestamp = new Date().getTime();
				setTimeout(function() {
					// финт ушами, который позволяет обработать несколько выборов в фильтре корректно
					current_timestamp = new Date().getTime();
					if (current_timestamp - close_timestamp > 2000*0.9) $(".itemsCounter").css("display", "none"); 
				}, 2000);
	    },
	    onfailure: function(){	 
		    
	    }
		});
		
	}

	$( document ).ready(function() {
		// Учитывая, что курсор мышки может сместится, запоминаем признак того, что кнопка мышки была нажата
		// на одной из кнопок слайдера цен
		
			
		var is_mouse_down_slider = false;
		
		$('.ui-slider-handle').mousedown(function(eventObject){
			  if (eventObject.which == 1) is_mouse_down_slider = true;
		});		

		
		$('body').mouseup(function(eventObject){ 
				if (is_mouse_down_slider)
				{
					is_mouse_down_slider = false;
					ReCalcCountInFilter($('.ui-slider-handle'));
				}
		});		
	});	
</script>
