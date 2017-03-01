<?php

require "../loader.php";

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

/** Block strategy **/
$itemAddStrategy = function($propName, $xml){
    return $xml->$propName;
};

$itemUniversalStrategy = function($propName, $xml){
    $arFiled = explode("_", $propName);
    $t1 = $xml->{$arFiled[0]};
    return $t1->{$arFiled[1]};
};
/** ---- END ---- **/


$t = new XmlParser("sols.xml");

// Get all props by file
foreach ($t->getXml()->Details as $product) {
    $t->setStrategy($itemAddStrategy);
    $arPropsInFile['color'][$t->getProp($product, 'Color')] = $t->getProp($product, 'Color');
}

$arPropsInFile['color'] = array_filter($arPropsInFile['color']);
foreach($arPropsInFile['color'] as $color){
    createColor($color, 'sols');
}