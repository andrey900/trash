<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog"); 

/* Update element code*/
$arFilter = array("ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y", "%CODE"=>"_", "IBLOCK_ID"=>10);
$arRes = CAniartTools::_GetInfoElements(false, array("ID", "NAME", "CODE"), $arFilter);
foreach ($arRes as $arItem) {
	$t = preg_replace("/_/", "-", $arItem['CODE']);
	$arLoadProductArray = Array(
	  "CODE"           => $t,
	  );

	$el = new CIBlockElement;
	$PRODUCT_ID = $arItem['ID'];  // изменяем элемент с кодом (ID) 2
	$res = $el->Update($PRODUCT_ID, $arLoadProductArray);
	//var_dump($res);
	p($el->LAST_ERROR);
}

/* Update section code*/
$arFilter = array("ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y", "%CODE"=>"_", "IBLOCK_ID"=>10);
//$arRes = CAniartTools::_GetInfoElements(false, array("ID", "NAME", "CODE"), $arFilter);

$db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, false, array("ID", "NAME", "CODE"));

while ($arItem = $db_list->GetNext()) {
	$t = preg_replace("/_/", "-", $arItem['CODE']);
	$arLoadProductArray = Array(
	  "CODE"           => $t,
	  );
	$bs = new CIBlockSection;
	$res = $bs->Update($arItem['ID'], $arLoadProductArray);
	p($bs->LAST_ERROR);
}
