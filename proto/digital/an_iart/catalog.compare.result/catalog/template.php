<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//p($arResult);?>

<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/style_table_compare.css">
<?/*скрипт покупки*/?>
<script src="<?=SITE_TEMPLATE_PATH?>/components/aniart/buy.with.this.product/details.buy.with/script.js" type="text/javascript" charset="utf-8" async defer></script>

<div class="char">

<p>
	<?php if( (int)$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]['ITEMS']>0 ):?>
		<a class="delete-all-compare" href="?action=DELETE_ALL_COMPARE_ELEMENTS"><?=GetMessage('REFRESH_COMPARE')?></a>
	<?php endif;?>
	<?php if( !empty($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"]) && $_REQUEST['show_all']!='Y'):?>
		<a class="show-all-compare" href="?show_all=Y"><?=GetMessage('SHOW_COMPARE_ALL_CATEGORIES');?></a>
	<?php endif;?>
</p>

<?if( empty($arResult['ITEMS']) )
	echo '<p style="margin-top:5px;">'.GetMessage("CATALOG_COMPARE_LIST_EMPTY").'</p>';
?>

<?foreach ($arResult['ITEMS'] as $arElementsInSection):
	$table = array();
	$arPieceTable['CHARACTERISTICS'] = '';
	$arDiffCompare = array();
	$arDiffComp = array();
	$intCountElements = $arElementsInSection['ALL_COUNT_ELEMENT_COMPARE'];
	$intCountShow = $arElementsInSection['COUNT_ELEM_COMP'];

	// Name section
	$strNameSection = $arElementsInSection['SECTION_INFO']['NAME'];
	$strIdSection = $arElementsInSection['SECTION_INFO']['ID'];

	$arPieceTable = array('TH'=>'', 'CHARACTERISTICS'=>'', 'BUY'=>'', 'TH-Price'=>'', 'TH-Image'=>'');
	
	$ar_res = $arElementsInSection['RES_ELEM_COMP'];
	$arPropName = $arElementsInSection['PRIMARY_PROP_NAME'];
	$arIdElements = $arElementsInSection['ID_ELEMENTS_COMPARE'];

	/* Хожу по массиву свойств. Формирую начало строки таблицы с первой колонкой(наименование) */
	foreach ($arPropName as $code => $name)
	{
		if($code != '0')
		{
			// Не показывать пустые строки M-A-X
			$null_val_count = 0;
			foreach ($arIdElements as $ElementID)
			{
				if(empty($ar_res[$ElementID]['PROPERTY_'.$code.'_VALUE']) || in_array($ar_res[$ElementID]['PROPERTY_'.$code.'_VALUE'], array('Нет', 'нет'))
				)
					$null_val_count++;
			}

			if($null_val_count == count($arIdElements))
			{
				unset($arPropName[$code]);
				continue;
			}
			// !Не показывать пустые строки M-A-X
		
			
			$arPieceTable[$code] = '<tr class="#'.$code.'#"><td class="char-atr">'.$name.'</td>';
		}
		
	}

	/* Хожу по входному массиву элементов(для сохр. посл. как в админ. панели). 
		данные беру из доп. сформированого массива. Формирование происходит по столбцам
	*/
	foreach ($arIdElements as $arValue)
	{
		if(!$ar_res[$arValue]['PROPERTY_GOODS_SOLD_VALUE'])
		{
			$goodsid = $ar_res[$arValue]['OFFERS']['ID'];
			$goodsmess = GetMessage('CATALOG_COMPARE_BUY');
			$goodsclass = '';
		}
		else
		{
			$goodsclass = '_off';
			$goodsid = '';
			$goodsmess = GetMessage('GOODS_SOLD');
		}
		
		ob_start();?>
			<th scope="col">
				<div class="delete-element">
					<a element-id="<?=$arValue?>" href="?action=DELETE_FROM_COMPARE_ELEMENT&id=<?=$arValue?>">Удалить</a>
				</div>
				<?/*
				<div class="sr">
					<div class="sr-thumb">
						<?if($ar_res[$arValue]['PREVIEW_PICTURE']):?>
							<img src="<?=CFile::GetPath($ar_res[$arValue]['PREVIEW_PICTURE'])?>" width="66" alt="<?=$ar_res[$arValue]['NAME']?>">
						<?endif;?>
					</div>
					<div class="sr-tit">
						<a href="<?=$ar_res[$arValue]['DETAIL_PAGE_URL']?>" title="<?=$ar_res[$arValue]['NAME']?>"><?=TruncateText($ar_res[$arValue]['NAME'], 27)?></a>
						<?if ( !empty($ar_res[$arValue]['GIFTS']['FULL_NAME']) ):?>
							<p class='gift-text'>+<br/><?=$ar_res[$arValue]['GIFTS']['FULL_NAME']?></p>
						<?endif;?>
					</div>
				</div>

				<? #FormatCurrency($ar_res[$arValue]['PROPERTY_MINIMUM_PRICE_VALUE'], 'KZT');
				$int_price = $ar_res[$arValue]['PROPERTY_MINIMUM_PRICE_VALUE'];
				printf('<div class="price_loc">%d<span>%d</span></div>',($int_price - fmod($int_price, 1000))/1000, fmod($int_price, 1000));
				?>
				
				<span id="add_to_basket_with_prod<?=$goodsclass?>" class="fancybox-dialog fancybox.ajax ui-link bx_bt_button bx_bt<?=$goodsclass?>" rel="nofollow" href="/catalog/ajax/add_to_basket_with_prod.php" id_parent="<?=$goodsid?>"><?=$goodsmess?></span>
				<br>
				*/?>
			</th>
		<? $arPieceTable['TH'] .= ob_get_contents();
		ob_end_clean();
		ob_start();?>
			<th scope="col"<div class="sr">
				<div class="sr-thumb">
					<?if($ar_res[$arValue]['PREVIEW_PICTURE']):?>
						<img src="<?=CFile::GetPath($ar_res[$arValue]['PREVIEW_PICTURE'])?>" width="66" alt="<?=$ar_res[$arValue]['NAME']?>">
					<?endif;?>
				</div>
				<div class="sr-tit">
					<a href="<?=$ar_res[$arValue]['DETAIL_PAGE_URL']?>" title="<?=$ar_res[$arValue]['NAME']?>"><?=TruncateText($ar_res[$arValue]['NAME'], 27)?></a>
					<?if ( !empty($ar_res[$arValue]['GIFTS']['FULL_NAME']) ):?>
						<p class='gift-text'>+<br/><?=$ar_res[$arValue]['GIFTS']['FULL_NAME']?></p>
					<?endif;?>
				</div>
			</div></th>
		<? $arPieceTable['TH-Image'] .= ob_get_contents();
		ob_end_clean();
		ob_start();?>
			<th scope="col" class="bottom">
			<? #FormatCurrency($ar_res[$arValue]['PROPERTY_MINIMUM_PRICE_VALUE'], 'KZT');?>
			<?
			/*Prices*/
			//$int_price = $ar_res[$arValue]['PROPERTY_MINIMUM_PRICE_VALUE'];
			//printf('<div class="price_loc">%d<span>%d</span></div>',($int_price - fmod($int_price, 1000))/1000, fmod($int_price, 1000));
			?>
			<?if(!$ar_res[$arValue]['PRICES']['INTERNET_PRICE']["PRINT_VALUE_VAT"] == "") {	
				if(empty($ar_res[$arValue]['PRICES']['HOT_PRICE']["PRINT_VALUE_VAT"])){
					$price_internet = explode(" ", $ar_res[$arValue]['PRICES']['INTERNET_PRICE']["PRINT_VALUE_VAT"]);
				} else {
					$price_internet = explode(" ", $ar_res[$arValue]['PRICES']['HOT_PRICE']["PRINT_VALUE_VAT"]);
				}
			?>
				<div>
					<div class="price_loc"><?if($price_internet[2] == 'тг.'){ echo $price_internet[0] . '<span>' . $price_internet[1] . '</span>'; } else { echo $price_internet[0] . '' . $price_internet[1] . '<span>' . $price_internet[2] . '</span>'; }?></div>
						
				</div>
			<?}?>
			<?if($ar_res[$arValue]['PRICES']['INTERNET_PRICE']["VALUE_VAT"] != $ar_res[$arValue]['PRICES']['REFERAL_PRICE']["VALUE_VAT"]) {
				if($ar_res[$arValue]['PRICES']['REFERAL_PRICE']["PRINT_VALUE_VAT"] != "") {
					$price_referal = explode(" ", $ar_res[$arValue]['PRICES']['REFERAL_PRICE']["PRINT_VALUE_VAT"]);
					?>
					<div>
						<div class="price_loc"><?echo $price_referal[0] . ' ' . $price_referal[1]; if($price_referal[2] == 'тг.'){ } else {echo ' ' . $price_referal[2];}?></div>
					</div>
				<?}
			}?>
			<?/*End Prices*/?>
			<span class="ui-link bx_bt_button bx_bt" rel="nofollow" href="javascript:void(0)" onclick="addToBasket(<?=$goodsid?>)"><?=$goodsmess?></span>
			<br></th>
		<? $arPieceTable['TH-Price'] .= ob_get_contents();
		ob_end_clean();
		/*Заполняю строки списока основных свойств*/
		foreach ($arPropName as $code => $name) {
			/*Проверяю свойство цены, или тип чекбокс*/
			if( stristr($code, 'YN_CHECKBOX') || stristr($code, 'PRICE'))
			{
				if(stristr($code, 'PRICE'))
					$strPropName = FormatCurrency($ar_res[$arValue]['PROPERTY_'.$code.'_VALUE'], 'KZT');
				else
					$strPropName = ConvertYesNoToHtmlString($ar_res[$arValue]['PROPERTY_'.$code.'_VALUE']);

			/*Проверяю свойство на тип мудьтивыбора, с привязкой*/
			}
			elseif (stristr($code, 'DICTIONARY_MUL'))
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

			/*Формирую строку тоблицы*/
			$arPieceTable[$code] .= sprintf('<td %s>%s</td>', $class, $strPropName);
		}

		foreach($ar_res[$arValue]['PROPERTY_56_VALUE'] as $k => $value)
		{
			//$propertyes[$arValue][$k] = array('NAME' => $ar_res[$arValue]['PROPERTY_152_DESCRIPTION'][$k], 'VALUE' => $value);
			if( $ar_res[$arValue]['PROPERTY_56_DESCRIPTION'][$k]!='#' ){
				$table[$ar_res[$arValue]['PROPERTY_56_DESCRIPTION'][$k]][$arValue] = $value;
			} else {
				$table[$ar_res[$arValue]['PROPERTY_56_DESCRIPTION'][$k].$value][$arValue['ID']] = $value;
			}
			
			/* Массив разности значений*/
			$arDiffCompare[$ar_res[$arValue]['PROPERTY_56_DESCRIPTION'][$k]][$value] = $value;
		}

	} // endforeach
	 
	/*Формирую строки из доп массива свойств*/
	foreach ($table as $k => $value)
	{
		// Не показывать пустые строки M-A-X
		if(empty($value))
			continue;
		
		$null_val_count = 0;
		foreach($value as $val)
		{
			if(in_array($val, array('Нет', 'нет', '<>', '&lt;&gt;')))
				$null_val_count++;	
		}
		
		if($null_val_count == count($value))
			continue;
		// !Не показывать пустые строки M-A-X
		
		if(stripos($k, '#')===false ){
			$class = ( count($arDiffCompare[$k]) > 1 )?'class="difference"':'';

			$arPieceTable['CHARACTERISTICS'] .= '<tr '.$class.' ><td class="char-atr">'.$k.'</td>';
			foreach ($arIdElements as $evalue)
			{
				if($evalue == $firstElement)
					$class = 'class="choose-tel"';
				else
					$class = '';
					
				if($k == 'Цвет')
					$value[$evalue] = ConvertColorToHtmlString($value[$evalue]);

				$convPropValue = $value[$evalue] == '&lt;&gt;' ? '' :  	ConvertYesNoToHtmlString($value[$evalue]); // Добавил строку SMA для того чтобы не выводить на сайт <>
				
				$arPieceTable['CHARACTERISTICS'] .= "<td $class>".$convPropValue.'</td>';
			}
		}
		else
		{
			if(!empty($value[$firstElement]))
				$arPieceTable['CHARACTERISTICS'] .= '<tr><td class="nameCharacter" colspan="'.$arResult['COUNT_ELEM_COMP'].'">'.$value[$firstElement].'</td>';
		}
		$arPieceTable['CHARACTERISTICS'] .= "</tr>\r\n";

	}
	
	unset($arPieceTable[0]);
?>

<div class="det-block-tit slide-block" data-block="<?=$strIdSection?>">
	<div class="label">
		<?=$strNameSection;?>
		<i></i>
		<?php printf('<div class="info">Показано <span class="show-elem">%s</span> из <span class="all-elem">%s</span>.</div>', $intCountShow, $intCountElements);?>
	</div>
	<div class="label1">
		<?php echo GetMessage('SHOW_ONLY_DIFFERENCES');?>
		<i></i>			
	</div>
</div>

<div id="<?=$strIdSection;?>" class="hide">
<table width="100%" border="1">
	<tr class="top-tab">
		<th scope="col">
			<!--<a href="javascript:void(0)" class="char-name"><?php echo GetMessage('SHOW_ONLY_DIFFERENCES');?></a> -->
		</th>
		<?php echo $arPieceTable['TH'];?>
	</tr>
	<tr class="top-tab">
		<th scope="col"></th>
		<?php echo $arPieceTable['TH-Image'];?>
	</tr>
	<tr class="top-tab">
		<th scope="col" class="bottom"></th>
		<?php echo $arPieceTable['TH-Price'];?>
	</tr>
	<!--<tr><td colspan="<?=$arResult['COUNT_ELEM_COMP']?>" style="border-right: none;"><b><?php echo GetMessage('PRIMARY_CHARACTERISTICS');?></b></td></tr>	-->

	<? 
	unset($arPropName[0]);
	
	foreach ($arPropName as $code => $name)
	{
		// Не выводим цену
		if(stristr($code, 'PRICE'))
			continue;
		
		$diff = '';
		if(count($arDiffComp[$code]) > 1)
		{
			$diff = 'difference';
		}
		 
		$arPieceTable[$code] = preg_replace('/#'.$code.'#/', $diff, $arPieceTable[$code]);

		echo $arPieceTable[$code].'</tr>';
	}
	
	echo $arPieceTable['CHARACTERISTICS'];
	?>
</table>
</div>

<?endforeach;?>
</div>
<div class="clearfloat" style="margin-bottom:50px;"></div>