<?php

require "../loader.php";

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

/** Block strategy **/
$itemAddStrategy = function($propName, $xml){
    return $xml->baseinfo->$propName;
};

$itemUniversalStrategy = function($propName, $xml){
    $arFiled = explode("_", $propName);
    $t1 = $xml->{$arFiled[0]};
    return $t1->{$arFiled[1]};
};
/** ---- END ---- **/

//$t = new XmlParser("easy_gifts.xml");
$t = new XmlParser("easy_gifts.xml");

// Get all props by file
foreach ($t->getXml()->product as $product) {
    $t->setStrategy($itemUniversalStrategy);
    $arPropsInFile['color'][$t->getProp($product, 'color_id')] = $t->getProp($product, 'color_name');
}

$arPropsInFile['color'] = array_filter($arPropsInFile['color']);
foreach($arPropsInFile['color'] as $color){
//    createColor($color, 'easy_gifts');
}