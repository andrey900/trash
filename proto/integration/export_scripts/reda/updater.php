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

$t = new XmlParser("stock_reda.xml");

foreach($t->getXml()->goods as $product){
    $t->setStrategy($itemUniversalStrategy);
    $stock1 = $t->getProp($product, 'STOCK');
    $quantity = intval($stock1);
    Helper::updateQuantity('reda_'.$t->getProp($product, 'CODE'), $quantity);
}
