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

$t = new XmlParser("stock_happy_gifts.xml");

foreach($t->getXml()->Товары->Товар as $product){
    $t->setStrategy($itemUniversalStrategy);
    $stock = $t->getProp($product, 'Свободный');
    $quantity = intval($stock);
    Helper::updateQuantity('happy_gifts_'.$t->getProp($product, 'ИД'), $quantity);
}