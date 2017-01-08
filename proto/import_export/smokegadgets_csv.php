<?php 

CModule::IncludeModule('subscribe');
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
 
function parseCSVFile($path){
    $row = 1;
    $arHeaders = $arResult = array();
    
    if (($handle = fopen($path, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
            $num = count($data);
            if( empty($data[0]) )
                continue;
            if( $row == 1 ){
                foreach ($data as $k=>$value) {
                    $arHeaders[$k] = strtoupper($value);
                }
                $arResult['INFO']['HANDBOOKS'] = createHandbooksFields($arHeaders);
                $intCntFields = count($arHeaders);
                $row++;
                continue;
            }
            foreach ($data as $k=>$value) {
                $arResult['ITEMS'][$row][$arHeaders[$k]] = $value;
            }

            $row++;
        }
        fclose($handle);
        $arResult['INFO']['COUNT_FIELDS'] = $intCntFields;
        $arResult = clearCSVData( $arResult );
        return $arResult;
    } else 
     return array();
}

function createHandbooksFields($arFields){
    $arHB = array();
    foreach ($arFields as $key => $value) {
        if( defined('IBLOCK_'.$value.'_ID') )
            $arHB[$value] = array('FIELD_NAME'=>$value, 'IBLOCK_ID'=>constant('IBLOCK_'.$value.'_ID') );
    }
    return $arHB;
}

function clearCSVData($arRes){
    $intCntFields = $arRes['INFO']['COUNT_FIELDS'];
    foreach ( $arRes['ITEMS'] as $k => $arItem ) {
        if(count($arItem) != $intCntFields)
            unset($arRes['ITEMS'][$k]);
    }
    $arRes['ITEMS'] = array_values($arRes['ITEMS']);
    return $arRes;
}

function updateElement($arResultId){
	$IBLOCK_ID = 1;
	foreach ($arResultId as $itemId => $arItems) {
//		print_r($itemId);
		CIBlockElement::SetPropertyValues($itemId, $IBLOCK_ID, $arItems, 'BUY_WITH_T_ITEM');
	}
}

function addElement($arFields)
{
	$el = new CIBlockElement;
	$arFields = merginAddFields($arFields);
	if( $id = $el->Add($arFields) )
		echo "Create element id: ".$id."<BR>";
	else
		echo "Error: ".$el->LAST_ERROR;
	return $id;
}

function addSKUElement($productId, $measure)
{
	$el = new CCatalogProduct;
	$arFields = array(
                  "ID" => $productId, 
                  "QUANTITY" => 1,
                  "MEASURE"  => (int)$measure,
                  );
	if( $id = $el->Add($arFields) )
		echo "Add SKU to element($productId) id: ".$id."<br>";
	else
		echo "Error: ".$el->LAST_ERROR;
	return $id;
}

function merginAddFields($arFields)
{
	$arLoadProductArray = Array(
	  "MODIFIED_BY"    => 1, // элемент изменен текущим пользователем
	  "IBLOCK_SECTION_ID" => 37,          // элемент лежит в корне раздела
	  "IBLOCK_ID"      => 4,
	  //"PROPERTY_VALUES"=> $PROP,
	  "NAME"           => "Элемент",
	  "ACTIVE"         => "Y",            // активен
	  "PREVIEW_TEXT"   => "текст для списка элементов",
	  "DETAIL_TEXT"    => "текст для детального просмотра",
	  //"DETAIL_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/image.gif")
	  );
	
	return array_merge($arLoadProductArray, $arFields);
}

function getMeasure()
{
	$m_list = CCatalogMeasure::getList();
	while($ar_result = $m_list->GetNext()){
		$arMeasure[$ar_result['SYMBOL_RUS']] = $ar_result; 
	}
	return $arMeasure;
}

/**
 * Функция траслитерирует строку, заменяя пробелы на символы подчёркивания
 * и преобразуя символы к нижнему регистру
 *
 * @param string $str
 * @return string
 */
function translitStr($str) {
	$params = Array(
			"max_len" => "100", 								// обрезает символьный код до 100 символов
			"change_case" => "L", 							// буквы преобразуются к нижнему регистру
			"replace_space" => "_", 						// меняем пробелы на нижнее подчеркивание
			"replace_other" => "_", 						// меняем левые символы на нижнее подчеркивание
			"delete_repeat_replace" => "true", 	// удаляем повторяющиеся нижние подчеркивания
	);

	return CUtil::translit($str, "ru", $params);
}

$file = $_SERVER['DOCUMENT_ROOT']."/upload/tmp/export.csv";
$arResult = parseCSVFile($file);
$arMeasure = getMeasure();
/*
unset($arResult['ITEMS'][0]);
foreach ($arResult['ITEMS'] as $arV) {
	$arF = array('NAME'=>$arV['NAME'], 'CODE'=>translitStr($arV['NAME']), 'PREVIEW_TEXT'=>$arV['FULL_NAME'], 'XML_ID'=>$arV['XML_ID']);
	$intMeasId = $arMeasure[strtolower($arV['BASE_TYPE'])]['ID'];
	addSKUElement(addElement($arF), $intMeasId);
}
 */
//p($arResult);