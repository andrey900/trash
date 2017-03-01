<?php

require "../loader.php";

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

/** Block strategy **/
$itemAddStrategy = function($propName, $xml){
    return $xml->baseinfo->$propName;
};
$itemAddStrategySize = function($propName, $xml){
    return $xml->attributes->$propName;
};

$itemAddStrategyMaterial = function($propName, $xml){
    return $xml->materials->material->$propName;
};

$itemAddStrategyTypeOfApp = function($propName, $xml){
    return $xml->markgroups->markgroup->$propName;
};

$itemUniversalStrategy = function($propName, $xml){
    $arFiled = explode("_", $propName);
    $t1 = $xml->{$arFiled[0]};
    return $t1->{$arFiled[1]};
};
/** ---- END ---- **/

$t = new XmlParser("easy_gifts_minimal.xml");


// Get all props by file
foreach ($t->getXml()->product as $product) {
    $t->setStrategy($itemUniversalStrategy);
    if( $t->getProp($product, 'brand_name') )
        $arPropsInFile['brand'][$t->getProp($product, 'brand_id')] = $t->getProp($product, 'brand_name');

    $arPropsInFile['color'][$t->getProp($product, 'color_id')] = ($t->getProp($product, 'color_name'));
}

/*$arPropsInFile['color'][3333] = "New color";
$arPropsInFile['brand'][3333] = "New brand";
$arPropsInFile['brand'][3334] = "New brand 1";*/

createPropertyInDB('easy_gifts');

initProps();

$cnts = count($t->getXml()->product);

$products = [];
foreach ($t->getXml()->product as $item) {
    $products[] = $item;
}
$materials = [];
foreach ($t->getXml()->product->materials as $item) {
    $materials[] = $item;
}


echo "<pre>All count: ".$cnts.PHP_EOL;

$page = 0;
if( $_GET['page'] )
    $page = (int)$_GET['page'];

$pages = ceil($cnts / 50);

$products = array_slice($products, $page*50, 50);
$i = 0;

foreach ($products as $product) {
    //p($product);
    $elem = new ElementCreator();
    $t->setStrategy($itemAddStrategy);
    $elem->setField("NAME", $t->getProp($product, 'name').' - '.$t->getProp($product, 'code_short'));

    $elem->setField("DETAIL_TEXT", $t->getProp($product, 'intro'));
    $elem->setField("IBLOCK_SECTION_ID", 411);

    $elem->setField("CODE", translit($t->getProp($product, 'name')).'-'.$t->getProp($product, 'id'));
    $elem->setField("XML_ID", 'easy_gifts_'.$t->getProp($product, 'id'));



    $elem->setProp("CML2_ARTICLE", $t->getProp($product, 'code_full'));

    $elem->price = $t->getProp($product, 'price');
    $elem->currency = "EUR";
    $elem->setProp("SHIPPER", 'easy_gifts');
    $t->setStrategy($itemUniversalStrategy);
    if( $t->getProp($product, 'images_image1') ){
        $elem->setField('DETAIL_PICTURE', \CFile::MakeFileArray($t->getProp($product, 'images_image1')));
    }

    $field = "easy_gifts_".translit($t->getProp($product, 'color_name'));
    $field = current(Helper::findNameInCollection($GLOBALS['arPropsInSite']['color'], $field, 'UF_XML_ID'));
    $elem->setProp("COLOR_REF2", $field['UF_XML_ID']);

    $field = $t->getProp($product, 'brand_name');
    $field = current(Helper::findNameInCollection($GLOBALS['arPropsInSite']['brand'], $field));
    $elem->setProp("CML2_MANUFACTURER", $field['ID']);

    if($t->getProp($product, 'images_image2')){
        $elem->arPictures = getPhotosForEG($product->images);
    }

    $t->setStrategy($itemAddStrategySize);
    $elem->setProp("CML2_SIZE", $t->getProp($product, 'size'));


    if( $product->materials ){
        $arMaterials = [];
        foreach ($product->materials->material as $value) {
            $arMaterials[] = $value->name;
        }
        $elem->setProp("CML2_MATERIAL", $arMaterials);
    }

    if( $product->markgroups ){
        $arMarkroups = [];
        foreach ($product->markgroups->markgroup as $value) {
            $arMarkroups[] = $value->name;
        }
        $elem->setProp("CML2_TYPE_OF_APPLICATION", $arMarkroups);
    }


    /*$t->setStrategy($itemAddStrategyMaterial);
    $elem->setProp("CML2_MATERIAL", $t->getProp($product, 'name'));

    $t->setStrategy($itemAddStrategyTypeOfApp);
    $elem->setProp("CML2_TYPE_OF_APPLICATION", $t->getProp($product, 'name'));*/

    $elem->save(true);
    $i++;
    //p($elem, 1, 1);
}

echo "All created items: ".$i.PHP_EOL;
echo "All pages: ".$pages;

if( $page < $pages ){
    echo "<script>";
    echo 'window.location.href = window.location.origin + window.location.pathname + "?page='.($page+1).'"';
    echo "</script>";
}
