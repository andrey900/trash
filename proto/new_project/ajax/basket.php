<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//$APPLICATION->AddHeadString('	<script language="JavaScript" src="/js/cart_xp_nd1.js  " type="text/javascript"></script> ',true);

if(stripos($_SERVER['HTTP_REFERER'] ,'/personal/basket.php')!==false)
{
 	define('DOP_TOVAR_INSERT',1);
}

if($_POST['clearBasket']=='Y')
{
	 CModule::IncludeModule("sale");
	 CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID(), false);

}
                                                          //basket
$APPLICATION->IncludeComponent("bitrix:sale.basket.basket", "basket", Array(
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",	// Рассчитывать скидку для каждой позиции (на все количество товара)
	"COLUMNS_LIST" => array(	// Выводимые колонки
		0 => "NAME",
		1 => "PROPS",
		2 => "PRICE",
		3 => "TYPE",
		4 => "QUANTITY",
		5 => "DELETE",
		6 => "DELAY",
		7 => "WEIGHT",
		8 => "DISCOUNT",
	),
	"PATH_TO_ORDER" => "/personal/order.php",	// Страница оформления заказа
	"HIDE_COUPON" => "N",	// Спрятать поле ввода купона
	"QUANTITY_FLOAT" => "N",	// Использовать дробное значение количества
	"PRICE_VAT_SHOW_VALUE" => "N",	// Отображать значение НДС
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	),
	false
);
 ?>