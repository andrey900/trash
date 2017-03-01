<?
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;
use Bitrix\Iblock;

use Studio8\Main\Product;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$product = new Product($arResult);

$arResult['product'] = $product;

$this->__component->SetResultCacheKeys(array('product'));