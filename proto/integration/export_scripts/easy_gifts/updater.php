<?php

require "../loader.php";

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

/** Block strategy **/
$itemUniversalStrategy = function($propName, $xml){
    return $xml->stock->$propName;
};
/** ---- END ---- **/

$t = new XmlParser("stock_easy_gifts.xml");

foreach($t->getXml()->product as $product){
    $t->setStrategy($itemUniversalStrategy);
    $stock1 = $t->getProp($product, 'quantity_24h');
    $stock2 = $t->getProp($product, 'quantity_37days');
    $quantity = intval($stock1 + $stock2);
    Helper::updateQuantity('easy_gifts_'.$t->getProp($product, 'id'), $quantity);
}