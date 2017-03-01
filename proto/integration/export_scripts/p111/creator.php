<?php
if( !$_SERVER['DOCUMENT_ROOT'] )
     $_SERVER['DOCUMENT_ROOT'] = '/home/krasavik/www/krasavik.studio8.by/htdocs/';

require "../loader.php";

initProps(['UF_SHIPPER' => 'project_111']);

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

/**
* немного больной производитель что-то за раз не захотел затягиватся 
* пришлось реализовать постраничку
* так же бывают проблемы просто останавливается, почему так ХЗ...
**/

/** Block strategy **/
$brandNameCheker = function($e){ if($e == "B&C") $e = "B & C"; return $e; };
/** ---- END ---- **/

error_reporting(E_ERROR|E_PARSE|E_COMPILE_ERROR );

$collection = require './data/brands.php';

// Get all props by file
foreach ($collection as $brand) {
    if( $brand['name'] )
        $arPropsInFile['brand'][$brand['id']] = $brand['name'];
}

$collection = require './data/colors.php';

// Get all props by file
foreach ($collection as $color) {
    if( $color['name'] )
        $arPropsInFile['color'][$color['id']] = $color['name'];
}

$arPropsInFile['brand'] = array_map($brandNameCheker, $arPropsInFile['brand']);

createPropertyInDB('project_111');
initProps(['UF_SHIPPER' => 'project_111']);

$products = file_get_contents('./data/products.json');
$products = json_decode($products);

$cnts = count($products);
echo "<pre>All count: ".$cnts.PHP_EOL;

$page = 0;
if( $_GET['page'] )
    $page = (int)$_GET['page'];

$pages = ceil($cnts / 50);

$products = array_slice($products, $page*50, 50);
$i = 0;
foreach ($products as $product) {
    $elem = new ElementCreator();
    $elem->setField("NAME", $product->name.' - '.$product->code_full);
    $elem->setField("IBLOCK_SECTION_ID", 415);

    $elem->setField("DETAIL_TEXT", $product->intro);

    $elem->setField("CODE", translit($product->name.'-'.$product->id));
    $elem->setField("XML_ID", "project_111_".$product->id);
    $elem->setProp("SHIPPER", 'project_111');
    $elem->setProp("CML2_ARTICLE", $product->code_full);
    $elem->setProp("CML2_SIZE", $product->size);

    if( $product->materials ){
	    $arMaterials = [];
	    foreach ($product->materials as $value) {
	    	$arMaterials[] = $value->name;
	    }
    	$elem->setProp("CML2_MATERIAL", $arMaterials);
    }

	if( $product->markgroups ){
	    $arMarkroups = [];
	    foreach ($product->markgroups as $value) {
	    	$arMarkroups[] = $value->name;
	    }
    	$elem->setProp("CML2_TYPE_OF_APPLICATION", $arMarkroups);
    }


    $elem->price = str_replace(",", ".", $product->price_catalog);
    $elem->currency = "EUR";

    if( $product->images && isset($product->images[0]) ){
        $elem->setField('DETAIL_PICTURE', \CFile::MakeFileArray($product->images[0]));
    }

    $field = $product->color_name;
    // $field = 'project_111_'.translit($field);
    if( $field ){
        $field = current(Helper::findNameInCollection($GLOBALS['arPropsInSite']['color'], $field, 'UF_NAME'));
        $elem->setProp("COLOR_REF2", $field['UF_XML_ID']);
    }

    $field = $product->brand;
    if($field){
        $field = current(Helper::findNameInCollection($GLOBALS['arPropsInSite']['brand'], $brandNameCheker($field)));
        $elem->setProp("CML2_MANUFACTURER", $field['ID']);
    }

    if($product->images){
        $elem->arPictures = $product->images;
    }

    $elem->save(true);
    echo $elem->getId().PHP_EOL;
    $i++;

    // p($elem, 1, 1);
}

echo "All created items: ".$i.PHP_EOL;
echo "All pages: ".$pages;

if( $page < $pages ){
    echo "<script>";
    echo 'window.location.href = window.location.origin + window.location.pathname + "?page='.($page+1).'"';
    echo "</script>";
}