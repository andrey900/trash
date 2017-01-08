<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arProperties = &$arResult['ELEMENT']['PROPERTIES'];
//ресайз детальной
$arResult['ELEMENT']['DETAIL_PICTURE'] = ResizeInitialPicture($arResult['ELEMENT']['DETAIL_PICTURE'], 250, 500);
//цвет фона
if(empty($arProperties['BACKGROUND_COLOR']['VALUE'])){
	$arProperties['BACKGROUND_COLOR']['VALUE'] = GetRandomColor(true);
}
else{
	$arProperties['BACKGROUND_COLOR']['VALUE'] = GetColorByID($arProperties['BACKGROUND_COLOR']['VALUE'], true);
}
//ресайз картинки для "основной фишки"
$arProperties['MF_PICTURE'] = ResizeInitialPicture($arProperties['MF_PICTURE']['VALUE'], 500, 350);

unset($arProperties);
?>
