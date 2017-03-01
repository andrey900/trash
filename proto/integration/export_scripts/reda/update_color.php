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
    return $xml->$propName;
};
/** ---- END ---- **/

$t = new XmlParser("reda.xml");

$t->setStrategy($itemUniversalStrategy);

// Get all props by file
foreach ($t->getXml()->goods as $product) {
    $t->setStrategy($itemUniversalStrategy);

    $arPropsInFile['color'][$t->getProp($product, 'COLOUR')] = $t->getProp($product, 'COLOUR');
}

initProps();

$t = Studio8\Main\Helpers::_GetInfoElements(false, ["ID", "NAME", "XML_ID", "PROPERTY_COLOR_REF2"], ['IBLOCK_ID' => 14, "PROPERTY_SHIPPER" => "reda"]);

foreach( $t as $item ){
    $field = 'reda_'.translit($item['PROPERTY_COLOR_REF2_VALUE']);

    $field = current(Helper::findNameInCollection($GLOBALS['arPropsInSite']['color'], $field, 'UF_XML_ID'));

    if( $item['PROPERTY_COLOR_REF2_VALUE'] ){
//        CIBlockElement::SetPropertyValuesEx($item['ID'], 14, array( "COLOR_REF2" => $field['UF_XML_ID']));
    }
}
