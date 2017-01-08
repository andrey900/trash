<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arElement = &$arResult['ELEMENTS'][0];
if(!empty($arElement)){
	$arResult['BACKGROUND_COLOR'] = $arElement['PROPERTY_BACKGROUND_COLOR_CODE'];
	if(!empty($arElement['DETAIL_PICTURE'])){
		$DetailPictureID = $arElement['DETAIL_PICTURE'];
		$arElement['DETAIL_PICTURE'] = CFile::ResizeImageGet($DetailPictureID, array('width' => 250, 'height' => 500), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
		if(!empty($arElement['DETAIL_PICTURE'])){
			$arElement['DETAIL_PICTURE']['ID'] = $DetailPictureID;
		}
	}
}

if(empty($arResult['BACKGROUND_COLOR'])){
	$arResult['BACKGROUND_COLOR'] = GetRandomColor(true);
}
unset($arElement);
?>