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

$t = new XmlParser("reda_min.xml");

$t->setStrategy($itemUniversalStrategy);

foreach ($t->getXml()->goods as $item) {
   // p($item, 1, 1);
  //  p($t->getProp($product, 'id'));
}

// Get all props by file
foreach ($t->getXml()->goods as $product) {
    $t->setStrategy($itemUniversalStrategy);
    /*if( $t->getProp($product, 'brand_name') )
        $arPropsInFile['brand'][$t->getProp($product, 'brand_id')] = $t->getProp($product, 'brand_name');*/

    $arPropsInFile['color'][$t->getProp($product, 'COLOUR')] = $t->getProp($product, 'COLOUR');
}

createPropertyInDB('reda');

initProps();

foreach ($t->getXml()->goods as $product) {
    $elem = new ElementCreator();
    $t->setStrategy($itemAddStrategy);
    $elem->setField("NAME", $t->getProp($product, 'TITLE').' - '.$t->getProp($product, 'CODE'));

    $elem->setField("DETAIL_TEXT", $t->getProp($product, 'DESCRIPTION'));
    $elem->setField("IBLOCK_SECTION_ID", 409);
    $elem->setField("CODE", translit($t->getProp($product, 'TITLE')).'-'.$t->getProp($product, 'CODE'));

    $elem->setField("XML_ID", 'reda_'.$t->getProp($product, 'CODE'));
    $elem->setProp("CML2_ARTICLE", $t->getProp($product, 'CODE'));
    $elem->price = $t->getProp($product, 'CATALOGUE_PRICE_EUR');
    $elem->currency = "EUR";
    $elem->setProp("SHIPPER", 'reda');
    /*$t->setStrategy($itemUniversalStrategy);
    if( $t->getProp($product, 'images_image1') ){
        $elem->setField('DETAIL_PICTURE', \CFile::MakeFileArray($t->getProp($product, 'images_image1')));
    }*/
//Добавили транлит перед получением нашего имени
    $field = "reda_".translit($t->getProp($product, 'COLOUR'));
    $field = current(Helper::findNameInCollection($GLOBALS['arPropsInSite']['color'], $field, 'UF_XML_ID'));
    $elem->setProp("COLOR_REF2", $field['UF_XML_ID']);

    $elem->setProp("CML2_SIZE", $t->getProp($product, 'SIZE'));
    $elem->setProp("CML2_MATERIAL", $t->getProp($product, 'MATERIAL'));

    /*if($t->getProp($product, 'images_image2')){
        $elem->arPictures = getPhotosForEG($product->images);
    }*/

    $elem->save(true);
    p($elem);
}


