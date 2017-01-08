<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arResult['backgroundColors'] = array(
	'#7ecce3', '#dbe695', '#dbbee8', '#5ec8ff'
);
$ElementsDiff = abs(count($arResult['ELEMENTS']) - $arParams['ELEMENTS_COUNT']);
if($ElementsDiff > 0){
	for($i = 0; $i < $ElementsDiff; $i++){
		$arResult['ELEMENTS'][] = array();
	}
}
?>