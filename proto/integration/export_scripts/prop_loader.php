<?php

use Studio8\Main\Integration\ElementCreator;

/**
 * функции для загрузки и создания свойств...
 */

/**
 * Make globals array @arPropsInSite
 * array(
 *   @type => [...],
 * )
 */
function initProps($filter = []){
    $GLOBALS['arPropsInSite']['brand'] = Studio8\Main\Helpers::_GetInfoElements(false, ["ID", "NAME", "XML_ID"], ['IBLOCK_ID' => 9]);
    $GLOBALS['arPropsInSite']['color'] = Studio8\Main\Helpers::_GetHLItems(2, $filter);
}

/**
 * Get array names from @arPropsInSite by @type
 * @param $type
 * @return array
 */
function getArNames($type){
    if( $type == 'color' )
        return Studio8\Main\Integration\Helper::getNameCollection($GLOBALS['arPropsInSite'][$type], "UF_NAME");

    return Studio8\Main\Integration\Helper::getNameCollection($GLOBALS['arPropsInSite'][$type]);
}

/**
 * Find property name in @arPropsInSite
 * @param $type
 * @param $find
 * @return bool
 */
function findPropInSite($type, $find){
    return in_array($find, getArNames($type));
}

/**
 * Trasliterate function
 * @param $str
 * @return string
 */
function translit($str){
    return \CUtil::translit($str, 'ru', array(
        "max_len" => 275,
        "change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
        "replace_space" => '_',
        "replace_other" => '_',
        "delete_repeat_replace" => true,
    ));
}

/**
 * Проверяет наличие значения свойства на сайте, если нет, создает его благодаря магическим ф-ям
 */
function createPropertyInDB($vendor=false){
    foreach ($GLOBALS['arPropsInFile'] as $type => $arItems) {
        foreach ($arItems as $item) {
            if( findPropInSite($type, $item) )
                continue;

            $fnName = 'create'.ucfirst($type);
            if( $type && $fnName == "createColor" )
                $fnName($item, $vendor);
            else
                $fnName($item);
        }
    }
}

function getPhotosForEG(\SimpleXMLElement $arPictures){
    $arPics = [];
    for($i=1; $i < 10; $i++){
        $t = 'image'.$i;
        if( $arPictures->$t ){
            $arPics[] = (string)$arPictures->$t;
        } else {
            break;
        }
    }
    return $arPics;
}




/**
 * Magic functions, name must be start  'create' + @type(first letter upper case)
 * @param $name
 */

function createBrand($name){
    $elem = new ElementCreator();

    $elem->setField("IBLOCK_ID", 9);
    // $elem->quantity = 2;
    $elem->setField("NAME", $name);
    $elem->setField("CODE", translit($name));
    $elem->save();
}

/**
 * @param $name
 */
function createColor($name, $shipper=false){
    $elem = new ElementCreator('AsproOptimusColorReference');

    $elem->setField("UF_NAME", $name);

    $elem->setField("UF_XML_ID", translit($name));
    if( $shipper ){
        $elem->setField("UF_SHIPPER", $shipper);
        $elem->setField("UF_XML_ID", translit($shipper).'_'.translit($name));
    }

    $elem->save();
}


