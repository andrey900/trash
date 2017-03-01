<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;

CModule::IncludeModule('highloadblock');
Cmodule::IncludeModule('catalog');
Cmodule::IncludeModule('iblock');

$t = new XmlParser("pr.xml");

$itemAddStrategy = function($propName, $xml){
	return $xml->baseinfo->$propName;
};

$itemPropertyAddStrategy = function($propName, $xml){
	return $xml->$propName->name;
};

$itemPropertyIdAddStrategy = function($propName, $xml){
	return $xml->$propName->id;
};

$itemUniversalStrategy = function($propName, $xml){
	$arFiled = explode("_", $propName);
	$t1 = $xml->{$arFiled[0]};
	return $t1->{$arFiled[1]};
	// return $xml->$propName->id;
};

$i = 0;
$arBrands = [];
$arColor = [];
$arModelName =[];
foreach ($t->getXml()->product as $product) {
	// items for IBlockElementAdd
	$t->setStrategy($itemUniversalStrategy);
	if( $t->getProp($product, 'brand_name') )
		$arBrands[$t->getProp($product, 'brand_id')] = $t->getProp($product, 'brand_name');

	$arColor[$t->getProp($product, 'color_id')] = $t->getProp($product, 'color_name');

	$arModelName[$t->getProp($product, 'Details_ModelName')] = $t->getProp($product, 'Details_ModelName');

	// p($t->getProp($product, 'id'), 0);
	// $r = new ElementCreator();
	$i++;
}
// p($arBrands);
// p($arColor);
p($arModelName);
// echo $i;


$arBrandsInSite = Studio8\Main\Helpers::_GetInfoElements(false, ["ID", "NAME", "XML_ID"], ['IBLOCK_ID' => 9]);
$arBrandsInSiteName = array_map(function($e){return $e['NAME'];}, $arBrandsInSite);

$arColorsInSite = Studio8\Main\Helpers::_GetHLItems(2);
$arColorsInSiteName = array_map(function($e){return $e['UF_NAME'];}, $arColorsInSite);
p($arColorsInSiteName);

// die;
foreach($arColor as $brand){
	if( in_array($brand, $arColorsInSiteName) )
		continue;

	p($brand);
	$elem = new ElementCreator();

	$elem->setField("IBLOCK_ID", 9);
	// $elem->quantity = 2;
	$elem->setField("NAME", $brand);
	$elem->setField("CODE", \CUtil::translit($brand, 'ru', array(
			"max_len" => 275,
			"change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
			"replace_space" => '_',
			"replace_other" => '_',
			"delete_repeat_replace" => true,
		)));
	// $elem->save();
}

// die;
foreach($arColor as $color){
	if( in_array($color, $arColorsInSiteName) )
		continue;

	p($color);
	$elem = new ElementCreator('AsproOptimusColorReference');

	$elem->setField("UF_NAME", $color);
	$elem->setField("UF_XML_ID", \CUtil::translit($color, 'ru', array(
			"max_len" => 275,
			"change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
			"replace_space" => '_',
			"replace_other" => '_',
			"delete_repeat_replace" => true,
		)));
	// $elem->save();
}

// $arBrandsInSite = Studio8\Main\Helpers::_GetInfoElements(false, ["ID", "NAME", "XML_ID"], ['IBLOCK_ID' => 9]);




// p($t->getXml()->product[0]->baseinfo->id, 0);
