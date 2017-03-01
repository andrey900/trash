<?php

require "../loader.php";

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

/** Block strategy **/
$itemAddStrategy = function($propName, $xml){
    return $xml->$propName;
};

$itemAddStrategyTypeOfApp = function($propName, $xml){
    return $xml->ТипыНанесения->$propName;
};
$itemUniversalStrategy = function($propName, $xml){
    $arFiled = explode("_", $propName);
    $t1 = $xml->{$arFiled[0]};
    return $t1->{$arFiled[1]};
};
/** ---- END ---- **/

$t = new XmlParser("happy_gifts_min.xml");

// Get all props by file
foreach ($t->getXml()->Номенклатура->Элемент as $product) {
    $t->setStrategy($itemAddStrategy);
    if( $t->getProp($product, 'Бренд') ){
        $arPropsInFile['brand'][$t->getProp($product, 'Бренд')] = $t->getProp($product, 'Бренд');
    }

    $arPropsInFile['color'][$t->getProp($product, 'Цвет')] = $t->getProp($product, 'Цвет');
}

/*$arPropsInFile['color'][3333] = "New color";
$arPropsInFile['brand'][3333] = "New brand";
$arPropsInFile['brand'][3334] = "New brand 1";*/

$arBrandsMap = [];
foreach ($t->getXml()->Бренды->Бренд as $brand) {
    $arBrandsMap[(string)$brand->ИД] = (string)$brand->Наименование;
}

createPropertyInDB('happy_gifts');


initProps();

foreach ($t->getXml()->Номенклатура->Элемент as $product) {

    $elem = new ElementCreator();
    $t->setStrategy($itemAddStrategy);
    $elem->setField("NAME", $t->getProp($product, 'Наименование'));

    $elem->setField("DETAIL_TEXT", $t->getProp($product, 'Описание'));
    $elem->setField("IBLOCK_SECTION_ID", 414);

    $elem->setField("CODE", translit($t->getProp($product, 'Наименование')).'-'.translit($t->getProp($product, 'Артикул')));
    $elem->setField("XML_ID", 'happy_gifts_'.$t->getProp($product, 'ИД'));
    $elem->setProp("CML2_ARTICLE", $t->getProp($product, 'Артикул'));
    $elem->price = $t->getProp($product, 'РозничнаяЦена');
    $elem->currency = "RUB";
    $elem->setProp("SHIPPER", 'happy_gifts');
    $t->setStrategy($itemAddStrategy);
    /*if( $t->getProp($product, 'images_image1') ){
        $elem->setField('DETAIL_PICTURE', \CFile::MakeFileArray($t->getProp($product, 'images_image1')));
    }*/

    $field = "happy_gifts_".translit($t->getProp($product, 'Цвет'));
    $field = current(Helper::findNameInCollection($GLOBALS['arPropsInSite']['color'], $field, 'UF_XML_ID'));
    $elem->setProp("COLOR_REF2", $field['UF_XML_ID']);

    $field = $t->getProp($product, 'Бренд');
    $field = current(Helper::findNameInCollection($GLOBALS['arPropsInSite']['brand'], $arBrandsMap[$field]));
    $elem->setProp("CML2_MANUFACTURER", $field['ID']);


    if( $product->ТипыНанесения ){
        $arMarkroups = [];
        foreach ($product->ТипыНанесения->ТипНанесения as $value) {
            $arMarkroups[] = (string)$value;
        }
        $elem->setProp("CML2_TYPE_OF_APPLICATION", $arMarkroups);
    }


    $t->setStrategy($itemAddStrategy);
    $elem->setProp("CML2_SIZE", $t->getProp($product, 'Размер'));
    $elem->setProp("CML2_MATERIAL", $t->getProp($product, 'Материал'));
    $elem->setProp("ADDIT_CODE", $t->getProp($product, 'ИД'));


   /* if($t->getProp($product, 'images_image2')){
        $elem->arPictures = getPhotosForEG($product->images);
    }*/

    //p($elem, 1, 1);
    $elem->save(true);
}


