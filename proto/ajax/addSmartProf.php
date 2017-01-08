<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( !isset($_REQUEST['profession']) || empty($_REQUEST['profession']) )
	die('');

$name = trim($_REQUEST['profession']);
$arFilter = array('IBLOCK_ID'=>'29', 'NAME'=>$name);
$arRes = CIBlockExt::GetListElements($arFilter);
if( !empty($arRes) )
	die('');

$el = new CIBlockElement;

$arLoadProductArray = Array(
  "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
  "IBLOCK_ID"      => 29,
  "NAME"           => $name,
  "ACTIVE"         => "N",            // no активен
  );

if($PRODUCT_ID = $el->Add($arLoadProductArray))
  echo "New ID: ".$PRODUCT_ID;
else
  echo "Error: ".$el->LAST_ERROR;
