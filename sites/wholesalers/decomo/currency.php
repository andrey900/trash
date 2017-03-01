<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

if( !$_SERVER['DOCUMENT_ROOT'] ){
	$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://www.cbr.ru/scripts/XML_daily.asp');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$out = curl_exec($curl);
curl_close($curl);

$currencies = new SimpleXMLElement($out);

foreach($currencies as $item){
	if( in_array($item->CharCode, ["BYN", "EUR", "USD"]) ){
		$v = str_replace(",", '.', $item->Value);
		setCurrency((string)$item->CharCode, $v / $item->Nominal);
	}
}

function setCurrency($code, $curr){
	$id = 4;
	$hlblock = HL\HighloadBlockTable::getById( $id )->fetch();
	$entity  = HL\HighloadBlockTable::compileEntity( $hlblock );
	$entity_data_class = $entity->getDataClass();
	$rsData = $entity_data_class::getList(array(
	   "select" => array("*"),
	   "filter" => array("UF_CODE" => $code)
	));

	while ($ar = $rsData->Fetch()) {
	    $result = $entity_data_class::update($ar['ID'], array(
		  'UF_AMOUNT' => $curr
		));
	}
}