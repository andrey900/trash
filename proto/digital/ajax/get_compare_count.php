<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	header('Content-type: application/json');	
	$APPLICATION->IncludeComponent("bitrix:catalog.compare.list","empty",Array("IBLOCK_TYPE" => "aspro_kshop_catalog","IBLOCK_ID" => "11"));
?>