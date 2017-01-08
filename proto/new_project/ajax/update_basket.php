<?define("NO_KEEP_STATISTIC", true); // Отключение сбора статистики для AJAX-запросов
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "in_header", Array(
	"PATH_TO_BASKET" => "/personal/basket/",	// Страница корзины
	"PATH_TO_ORDER" => "/personal/order/",	// Страница оформления заказа
	"SHOW_DELAY" => "Y",	// Показывать отложенные товары
	"SHOW_NOTAVAIL" => "Y",	// Показывать товары, недоступные для покупки
	"SHOW_SUBSCRIBE" => "Y",	// Показывать товары, на которые подписан покупатель
	),
	false
);?> 
