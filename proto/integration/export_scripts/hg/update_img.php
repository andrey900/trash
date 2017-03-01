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
$t = Studio8\Main\Helpers::_GetInfoElements(false, ["ID", "NAME", "XML_ID", "PROPERTY_ADDIT_CODE"], ['IBLOCK_ID' => 14, "PROPERTY_SHIPPER" => "happy_gifts"]);
echo count($t);
$i = 0;
foreach($t as $item){
    $id = false;
    $path = false;
    if( is_file($_SERVER['DOCUMENT_ROOT'].'/upload/happy-img/'.$item['PROPERTY_ADDIT_CODE_VALUE'].'.jpg') ){
        $i++;
        $id = $item['ID'];
        $path = $_SERVER['DOCUMENT_ROOT'].'/upload/happy-img/'.$item['PROPERTY_ADDIT_CODE_VALUE'].'.jpg';
//        p($item['NAME']);
    } elseif( is_file($_SERVER['DOCUMENT_ROOT'].'/upload/happy-img/'.$item['PROPERTY_ADDIT_CODE_VALUE'].'.JPG') ){
        $i++;
        $path = $_SERVER['DOCUMENT_ROOT'].'/upload/happy-img/'.$item['PROPERTY_ADDIT_CODE_VALUE'].'.JPG';
        $id = $item['ID'];
//        p($item['NAME']);
    }

    if( $id ){
        $el = new CIBlockElement;
        $res = $el->Update($id, ["DETAIL_PICTURE" => CFile::MakeFileArray($path)]);
//        p($item);
//        die;
    }
}
echo '<br>'.$i;


