<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<table>
	<tr>
		<td style="border-right: 1px dashed #FF8D28; padding-right: 5px;">
			<b>Выберите рубрики, для которых Вы хотите получить прайс-лист с розничными ценами</b>
			<br><br><br>
		
			<form method='post' class="selected_sections">
				<?if(!empty($arResult["SECTIONS"])):?>
					<div >
						<div>
							Выберите разделы
							<span class='price-select-all' data-selected='0'>Выделить все</span>				
							
							<span class='price-toggle-all' data-toggle='0'>Развернуть все</span>
											
						</div>
						<?$previousLevel = 0;?>
						<?$previousID = 0;?>
						<div class='price-sections'>
							<?foreach($arResult["SECTIONS"] as $SectionID => $arSection):?>
								<?// Закрытие внутренних уровней?>
								<?if($arSection['DEPTH_LEVEL'] < $previousLevel):?><?=str_repeat("</div>", $previousLevel - $arSection["DEPTH_LEVEL"]);?><?endif;?>

								<?// Открытие внутренних уровней?>
								<?if($previousLevel && $arSection['DEPTH_LEVEL'] > $previousLevel):?><div class="child-level" id="section-<?=$previousID?>"><?endif;?>
								
								<label class='level-<?=$arSection['DEPTH_LEVEL']?>'><input type='checkbox' name='sections[]' value="<?=$arSection['ID']?>" /> <?=$arSection['NAME']?></label> 
								
								<?if($arResult['SECTIONS_HAVE_CHILDS'][$SectionID]):?>
									<div class='show-subsections' data-id='<?=$arSection['ID']?>' data-visible='0'><span class="price-plus-minus">+</span> <span class="price-toggle">(развернуть раздел)</span></div>
								<?endif;?>
								<br>
								
								<?$previousLevel = $arSection['DEPTH_LEVEL']?>
								<?$previousID = $SectionID?>
							<?endforeach;?>
						</div>
					
					</div>
				<?endif;?>
				<br><br>
				
				<?if($arParams['SHOW_PARAMS'] == 'Y'):?>
					<label><input name='SHOW_IMAGES_PATH' value='Y' type="checkbox"> Создать ссылку на скачивание папки с изображенями товаров</label><br>
					<label><input name='SHOW_IMAGES' value='Y' type="checkbox"> Показывать рисунки</label><br>
					<label><input name='SHOW_LINKS' value='Y' type="checkbox"> Показывать ссылки</label>
				<?endif;?>
				<br>
				
				<input type='submit' onclick="yaCounter10795495.reachGoal('price-rozn'); _gaq.push(['_trackEvent', 'price', 'roznic']); return true" class="get-price" name='get-price' value="Получить">
			</form>
		
		</td>
		<td style="width: 400px; padding-left: 10px;">
			<b>Для получения прайс-листа с оптовыми ценами, заполните имя, e-mail и реквизиты компании. В течение рабочего дня с Вами свяжется наш специалист.</b><br><br><br>
			
<?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new", 
	".default", 
	array(
		"CACHE_TYPE" => "N",
		"WEB_FORM_ID" => "1",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"USE_EXTENDED_ERRORS" => "N",
		"SEF_MODE" => "N",
		"SEF_FOLDER" => "/price-list-2/",
		"CACHE_TIME" => "3600",
		"LIST_URL" => "",
		"EDIT_URL" => "",
		"SUCCESS_URL" => "addok.php",
		"CHAIN_ITEM_TEXT" => "",
		"CHAIN_ITEM_LINK" => "",
		"VARIABLE_ALIASES" => array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID",
		)
	),
	false
);?>
		</td>
	</tr>
</table>
