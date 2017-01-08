<?
if( $arResult["nStartPage"] > 1 )
$APPLICATION->AddHeadString("<link rel='prev' href='".$arResult["sUrlPath"]."?".$strNavQueryString."PAGEN_".$arResult["NavNum"]."=".($arResult["nStartPage"]-1)."'>");
if( $arResult["NavPageNomer"] < $arResult["NavPageCount"] )
$APPLICATION->AddHeadString("<link rel='next' href='".$arResult["sUrlPath"]."?".$strNavQueryString."PAGEN_".$arResult["NavNum"]."=".($arResult["nStartPage"]+1)."'>");
?> 
