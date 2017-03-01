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

$t = new XmlParser("sols_min.xml");

$t->setStrategy($itemUniversalStrategy);

foreach ($t->getXml()->Details as $item) {
   // p($item, 1, 1);
   // p($t->getProp($product, 'ModelName'));

}

// Get all props by file
foreach ($t->getXml()->Details as $product) {
    $t->setStrategy($itemUniversalStrategy);
    /*if( $t->getProp($product, 'brand_name') )
        $arPropsInFile['brand'][$t->getProp($product, 'brand_id')] = $t->getProp($product, 'brand_name');*/

    $arPropsInFile['color'][$t->getProp($product, 'Color')] = $t->getProp($product, 'Color');
}

createPropertyInDB('sols');

initProps();

foreach ($t->getXml()->Details as $product) {
    $elem = new ElementCreator();
    $t->setStrategy($itemAddStrategy);
    $elem->setField("NAME", $t->getProp($product, 'ModelName').' - '.$t->getProp($product, 'ProductRef'));
    $elem->setField("CODE", translit($t->getProp($product, 'ModelName')).'-'.$t->getProp($product, 'ProductRef'));
    $elem->setField("XML_ID", 'sols_'.$t->getProp($product, 'ProductRef'));
    $elem->setProp("CML2_ARTICLE", $t->getProp($product, 'ProductRef'));
    $elem->setField("IBLOCK_SECTION_ID", 412);
    /*$elem->price = $t->getProp($product, 'price');
    $elem->currency = "EUR";*/
    $elem->setProp("SHIPPER", 'sols');
    /*$t->setStrategy($itemUniversalStrategy);
    if( $t->getProp($product, 'images_image1') ){
        $elem->setField('DETAIL_PICTURE', \CFile::MakeFileArray($t->getProp($product, 'images_image1')));
    }*/
//Добавили транлит перед получением нашего имени
    $field = "sols_".translit($t->getProp($product, 'Color'));
    $field = current(Helper::findNameInCollection($GLOBALS['arPropsInSite']['color'], $field, 'UF_XML_ID'));
    $elem->setProp("COLOR_REF2", $field['UF_XML_ID']);

    $t->setStrategy($itemAddStrategy);
    $elem->setProp("CML2_SIZE", $t->getProp($product, 'Size'));

    /*if($t->getProp($product, 'images_image2')){
        $elem->arPictures = getPhotosForEG($product->images);
    }*/

    $elem->save(true);
    //p($t->getProp($product, 'ProductName'));
}


