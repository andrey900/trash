<?php

require "../loader.php";

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

/** Block strategy **/
$itemUniversalStrategy = function($propName, $xml){
    return $xml->$propName;
};
/** ---- END ---- **/

$t = new XmlParser("sols.xml");

foreach($t->getXml()->Details as $product){
    $t->setStrategy($itemUniversalStrategy);
    $stock1 = $t->getProp($product, 'Stock');
    $quantity = intval($stock1);
    Helper::updateQuantity('sols_'.$t->getProp($product, 'ProductRef'), $quantity);
}
