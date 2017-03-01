<?php

if( $arResult['ITEMS'] ){
	foreach ($arResult['ITEMS'] as &$arItem) {
		$prop = CIBlockElement::GetProperty($arItem['IBLOCK_ID'], $arItem['ID'], [], ['CODE' => 'BUTTON_NAME'])->Fetch();
		$arItem['PROPERTIES'][$prop['CODE']] = $prop;
	}
}