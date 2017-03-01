<?php

require "../loader.php";

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

error_reporting(E_ERROR|E_PARSE|E_COMPILE_ERROR );

define(API_KEY, '1ff2adceaa5050cd8dafab4824038e3e');

function getQuantityInfo($id){
	$url = 'http://api.macma.pl/pl/' . API_KEY . '/quantity/'.(int)$id.'/json';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, $url);
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result)[0];
}

$products = file_get_contents('./data/products.json');
$products = json_decode($products);

foreach($products as $product){
    // $stock1 = $t->getProp($product, 'quantity_24h');
    // $stock2 = $t->getProp($product, 'quantity_37days');
    $quantity = getQuantityInfo($product->id)->quantity_delivery;
    Helper::updateQuantity("project_111_".$product->id, $quantity);
}