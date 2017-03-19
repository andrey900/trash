<?php

require "../loader.php";

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

error_reporting(E_ERROR|E_PARSE|E_COMPILE_ERROR );

define(API_KEY, '1ff24adceaa5050cd8dafab48240398e3e');


	$url = 'http://2868_xmlexport:@api2.gifts.ru/export/v2/catalogue/product.xml';

/*	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;*/


/*
function getQuantityInfo($id){
	$url = 'http://2868_xmlexport:@api2.gifts.ru/export/v2/catalogue/product.xml';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}*/

$result = file_get_contents($url);
p($result);
//$products = json_decode($products);
/*
foreach($products as $product){
    // $stock1 = $t->getProp($product, 'quantity_24h');
    // $stock2 = $t->getProp($product, 'quantity_37days');
    $quantity = getQuantityInfo($product->id)->quantity_delivery;
    Helper::updateQuantity("macma_".$product->id, $quantity);
}*/
