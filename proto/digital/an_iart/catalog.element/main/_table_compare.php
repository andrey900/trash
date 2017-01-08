<?php
$arPieceTable = array('TH'=>'', 'CHARACTERISTICS'=>'', 'BUY'=>'');

$ar_res = $arResult['RES_ELEM_COMP'];
$arPropName = $arResult['PRIMARY_PROP_NAME'];
$arIdElements = $arResult['ID_ELEMENTS_COMPARE'];

/* Хожу по массиву свойств. Формирую начало строки таблицы с первой колонкой(наименование) */
foreach ($arPropName as $code => $name)
{
	if($code != '0')
		$arPieceTable[$code] = '<tr class="#'.$code.'#"><td class="char-atr">'.$name.'</td>';
}

/* Хожу по входному массиву элементов(для сохр. посл. как в админ. панели). 
	данные беру из доп. сформированого массива. Формирование происходит по столбцам
*/

foreach ($arIdElements as $arValue)
{
	/*Формирую шапку таблицы*/
	ob_start();?>
		<th scope="col">
			<div class="sr">
					<?if($ar_res[$arValue]['PREVIEW_PICTURE']):?>
					<div class="sr-thumb">
						<img src="<?=CFile::GetPath($ar_res[$arValue]['PREVIEW_PICTURE'])?>" width="66" alt="<?=$ar_res[$arValue]['NAME']?>">
					</div>
					<?endif;?>
					<div class="sr-tit">
						<a href="<?=$ar_res[$arValue]['DETAIL_PAGE_URL']?>" title="<?=$ar_res[$arValue]['NAME']?>"><?=TruncateText($ar_res[$arValue]['NAME'], 27)?></a>
					</div>
				</div>
		</th>
	<?
	$arPieceTable['TH'] .= ob_get_contents();
	ob_end_clean();
	
	// Обработка кнопок вынесена в скрипт /catalog/ajax/composite/page_detail.php
	// ak@
	$goodsid = $arValue;
	$goodsmess = GetMessage('TO_BASKET');
	$goodsclass = '';
	
	$arPieceTable['BUY'] .= sprintf(
		'<td>
			<span product_id=%s class="btn_buy_in_compare bx_bt_button bx_bt%s" rel="nofollow" href="javascript:void(0)"  onclick="addToBasket(%s)">%s</span>
		</td>', $goodsid, $goodsclass, $goodsid, $goodsmess);
	
	/*Заполняю строки списока основных свойств*/
	foreach ($arPropName as $code => $name)
	{
		// Не показывать пустые строки M-A-X
		$null_val_count = 0;
		foreach ($arIdElements as $ElementID)
		{
			if(empty($ar_res[$ElementID]['PROPERTY_'.$code.'_VALUE']) || in_array($ar_res[$ElementID]['PROPERTY_'.$code.'_VALUE'], array('Нет', 'нет'))
			)
			{
				$null_val_count++;
			}			
		}

		if($null_val_count == count($arIdElements))
		{
			unset($arPropName[$code]);
			continue;
		}
		// !Не показывать пустые строки M-A-X
		
		/*Проверяю свойство цены, или тип чекбокс*/
		if( stristr($code, 'YN_CHECKBOX') || stristr($code, 'PRICE'))
		{
			if( stristr($code, 'PRICE') ){
				/*Prices*/
				//$strPropName = FormatCurrency($ar_res[$arValue]['PROPERTY_'.$code.'_VALUE'], 'KZT');
				
				if(!$ar_res[$arValue]['PRICES']['BASE']["PRINT_VALUE_VAT"] == "") {
					if(empty($ar_res[$arValue]['PRICES']['HOT_PRICE']["PRINT_VALUE_VAT"])){
						$strPropName = '<b>'.$ar_res[$arValue]['PRICES']['BASE']["PRINT_VALUE_VAT"].'</b>';
					} else {
						$strPropName = '<b>'.$ar_res[$arValue]['PRICES']['HOT_PRICE']["PRINT_VALUE_VAT"].'</b>';
					}
				}
				if($ar_res[$arValue]['PRICES']['BASE']["VALUE_VAT"] != $ar_res[$arValue]['PRICES']['REFERAL_PRICE']["VALUE_VAT"]) {
					if($ar_res[$arValue]['PRICES']['REFERAL_PRICE']["PRINT_VALUE_VAT"] != "") {
						$strPropName = '<b>'.$ar_res[$arValue]['PRICES']['REFERAL_PRICE']["PRINT_VALUE_VAT"].'</b>';
					}
				}
				if($strPropName == "")
					$strPropName = '<b>'.FormatCurrency($ar_res[$arValue]['PROPERTY_'.$code.'_VALUE'], 'RUB').'</b>';
				/*end Prices*/
			}
			else
				$strPropName = ConvertYesNoToHtmlString($ar_res[$arValue]['PROPERTY_'.$code.'_VALUE']);

		/*Проверяю свойство на тип мудьтивыбора, с привязкой*/
		}
		elseif ( stristr($code, 'DICTIONARY_MUL') )
		{
			$strPropName = '';
			foreach( $ar_res[$arValue]['PROPERTY_'.$code.'_VALUE'] as $propelem )
			{
				$strPropName .= $ar_res[$arValue]['PROPERTY_'.$code.'_INFO'][$propelem]['NAME'].'<br/>';
			}

		/*Стандартное свойство*/
		}
		else
		{
			$strPropName = $ar_res[$arValue]['PROPERTY_'.$code.'_NAME'];
		}
		
		if( $arValue == $arResult['ID'] ) $class = 'class="choose-tel"'; else $class = '';

		/* Массив разницы в значениях */
		$arDiffComp[$code][$strPropName] = $strPropName;
		
		$arPieceTable[$code] .= sprintf('<td %s>%s</td>', $class, $strPropName);
	}
} // endforeach

unset($arPieceTable[0]);
?>

<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/style_table_compare.css">
<h3 class="compare-h3">Сравните с похожими товарами</h3>
<div class="clear"></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="compare_table">
	<tr class="top-tab">
		<th scope="col">
			<div class="char-name"></div>
		</th>
		<?php echo $arPieceTable['TH'];?>
	</tr>
	<tr>
		<td colspan="<?=$arResult['COUNT_ELEM_COMP']?>" style="border-right: none;"><b><?php echo GetMessage('PRIMARY_CHARACTERISTICS');?></b></td>
	</tr>
	<?php 
		unset($arPropName[0]);
		foreach ($arPropName as $code => $name) 
		{
			$diff = '';
			
			if( count($arDiffComp[$code]) > 1 ){
				$diff = 'difference';
			}
			 
			$arPieceTable[$code] = preg_replace('/#'.$code.'#/', $diff, $arPieceTable[$code]);
			echo $arPieceTable[$code].'</tr>';
		}

	?>
	<tr class="choose-by">
		<td colspan="<?=$arResult['COUNT_ELEM_COMP']?>" align='center' style="border: none;">
			<div class="more-char">
				<a href="#" class="ui-link" id="show_all_char"><span></span>Еще показать характеристики</a>
			</div>
		</td>
	</tr>
</table>



<script type="text/javascript">

$('.sr-tit a').tooltip();

$('#show_all_char').on('click', function(){
	var our_string = $(location).attr('href');
	if(our_string.indexOf('clear_cache=Y') + 1) 
		 var cache = 'Y';
	else
		 var cache = 'N';
	 
	$.ajax({
		type: "GET",
		url: "/catalog/ajax/show_all_character_compare.php",
		data: { id : <?=json_encode($arIdElements)?>, clear_cache : cache }
	}).done(function(data) {
		$(".choose-by").before(data);
		$('.more-char').remove();
	});
	return false;
});
</script>