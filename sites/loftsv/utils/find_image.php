<?php

define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Studio8\Main\Helpers;

$pictures = scandir('images');

unset($pictures[0], $pictures[1]);

$pictures1 = array_map(function($e){ return str_replace('.jpg', '', $e);}, $pictures);

$arElements = Helpers::_GetInfoElements(false, ["ID", 'CODE'], ['IBLOCK_ID' => IBLOCK_CATALOG_ID, 'ACTIVE' => "Y", "CODE" => $pictures1]);

//$arElementsIds = array_map(function($e){ return $e['ID']; }, $arElements);

p([count($pictures1), count($arElements)]);
$arRes = [];

foreach($arElements as $item){
    $arRes[$item['ID']] = $item['CODE'].'.jpg';
}
p($arRes);

foreach ($pictures1 as $value) {
	if( !array_search($value.'.jpg', $arRes) )
		p($value);
}
//p(array_combine($arElementsIds, $pictures));
