<?define("NO_KEEP_STATISTIC", true); // Отключение сбора статистики для AJAX-запросов ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
if (!(
	CModule::IncludeModule("iblock") && 
	CModule::IncludeModule("sale") && 
	CModule::IncludeModule("catalog") && 
	isset($_REQUEST["id"])
)) return;

/* создаем объект */
$obCache = new CPHPCache; 

/* время кеширования - 60*24 минут */
$life_time = 60*24*60;

/* формируем идентификатор кеша в зависимости от всех параметров 
	которые могут повлиять на результирующий HTML	*/
$cache_id = serialize($_REQUEST["id"]); 

/* Очистка кеша */
if( $_REQUEST['clear_cache'] == 'Y')
	$obCache->CleanDir();

/* если кеш есть и он ещё не истек то */
if($obCache->InitCache($life_time, $cache_id, "/")) :
	/* получаем закешированные переменные */
	$vars = $obCache->GetVars();
	$table = $vars["table"];
	$arResult['COUNT_ELEM_COMP'] = $vars['count_elem_comp'];
else :
	/* иначе обращаемся к базе */
	$arIdElements = $_REQUEST['id'];
		
	/* Формирую массив основных параметров и начальное условие для выборки*/
	$arSelect = Array("ID", "NAME", "IBLOCK_ID", "PROPERTY_".PROPERTY_CHARACTERISTICS);

	$arFilter = Array(/*"ACTIVE" => "Y",*/ "ID" => $arIdElements );
	$res = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, $arSelect);

	$count = 1;

	while($ob = $res->GetNextElement())
	{
		/*Получаю содержимое выборки*/
		$arFields = $ob->GetFields();

		/*записываю данный масив к общему*/
		$ar_res[$arFields['ID']] = $arFields;

		foreach($ar_res[$arFields['ID']]['PROPERTY_'.PROPERTY_CHARACTERISTICS.'_VALUE'] as $k=>$value)
		{
			//$propertyes[$arValue][$k] = array('NAME' => $ar_res[$arValue]['PROPERTY_152_DESCRIPTION'][$k], 'VALUE' => $value);
			if( $ar_res[$arFields['ID']]['PROPERTY_'.PROPERTY_CHARACTERISTICS.'_DESCRIPTION'][$k]!='#' )
			{
				$table[$ar_res[$arFields['ID']]['PROPERTY_'.PROPERTY_CHARACTERISTICS.'_DESCRIPTION'][$k]][$arFields['ID']] = $value;
			}
			else
			{
				$table[$ar_res[$arFields['ID']]['PROPERTY_'.PROPERTY_CHARACTERISTICS.'_DESCRIPTION'][$k].$value][$arFields['ID']] = $value;
			}
			
			/* Массив разности значений*/
			$arDiffCompare[$ar_res[$arFields['ID']]['PROPERTY_'.PROPERTY_CHARACTERISTICS.'_DESCRIPTION'][$k]][$value] = $value;
		}

		$count++;
	}

	$arResult['COUNT_ELEM_COMP'] = $count;

endif;

/* начинаем буферизирование вывода */
if($obCache->StartDataCache()):
	/* выбираем из базы параметры элемента инфо-блока */
	$arPieceTable['CHARACTERISTICS'] = '<tr><td colspan="'.$arResult['COUNT_ELEM_COMP'].'" align="center" class="all_char" style="padding: 7px 10px 5px 10px"><b>Все характеристики</b></td></tr>';

	/*Формирую доп. массив для полного списка хар-к товара. 
		Заполняю все свойства которые описаны для данного товара*/
	$firstElement = array_shift($arIdElements);
	array_unshift($arIdElements, $firstElement);
	/*Формирую строки из доп массива свойств*/

	foreach ($table as $k => $value) 
	{
		if( stripos($k, '#')===false )
		{
			// Не показывать пустые строки M-A-X
			$null_val_count = 0;
			foreach($value as $val)
			{
				if(in_array($val, array('Нет', 'нет', '<>', '&lt;&gt;')))
					$null_val_count++;	
			}
			
			if($null_val_count == count($value))
				continue;
			// !Не показывать пустые строки M-A-X

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
					
				$convPropValue = ($value[$evalue] == '&lt;&gt;') ? '' : ConvertYesNoToHtmlString($value[$evalue]); // Добавил строку SMA для того чтобы не выводить на сайт <>
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

	echo $arPieceTable['CHARACTERISTICS'];
	
	// записываем предварительно буферизированный вывод в файл кеша
	// вместе с дополнительной переменной
	$obCache->EndDataCache(array(
		"table"		=> $table,
		'count_elem_comp'=>$arResult['COUNT_ELEM_COMP']
	)); 
endif;
?>
