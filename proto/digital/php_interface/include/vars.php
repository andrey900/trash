<?
////////////////////////////////////////////////////////////////////////////////
// Список обших констант
////////////////////////////////////////////////////////////////////////////////
define("CURRENCY_BASE","UAH"); // основная валюта, используемая в проекте
define("PRICE_BASE_ID", 1); // ID базовой цены
define("PRICE_BASE_CODE", "BASE"); // ID базовой цены

define("PHONE_PREFIX", "+8"); // префикс для телефона

define("SHARE_CATALOG_IBLOCK_ID",11); // ID инфоблока разделённого каталога
define("SHARE_SKU_IBLOCK_ID",23); // ID инфоблока торговых предложений разделённого каталога
//define("BRANDS_IBLOCK_ID", 15);
define("BRANDS_IBLOCK_ID", 4);
define('DICTIONARY_IBLOCK', 18);
define('COLOR_IBLOCK', 16);
define('COUNTRY_IBLOCK', 17);

define('SOURCE_TRAFIC_IBLOCK', 26);

define("DELIVERY_DAYS_IBLOCK_ID", 51); // ID справочника сроков доставки отсутствующего товара
define("DEFAULT_DELIVERY_DAYS", 14); // кол-во дней по умолчанию для доставки отсутствующего товара

define("GIFTS_IBLOCK_ID", 24); // ID инфоблока акционных подарков
define("PRODUCT_OF_DAY_IBLOCK_ID", 14); // ID инфоблока товаров дня
define("BANNERS_IBLOCK_ID", 42); // ID инфоблока баннеров
define("ACTION_IBLOCK_ID", 8); // ID инфоблока акций

define("PROPERTY_BRAND_ID", 29); // ID свойства производитель в разделённом каталоге
define("PROPERTY_MIN_PRICE_ID", 26); // ID свойства минимальная цена
define("PROPERTY_GIFTS_ID", 74); // ID свойства "Подарки"
define("PROPERTY_GIFTS_LIST_PRODUCTS_ID", 74); // ID свойства "Привязка к товарам" инфоблока "Подарки"
define("PROPERTY_MARKETING_ID", 73); // ID свойства "Маркетинговые признаки"

// Значение свойства Подарочные комплекты
define("PROPERTY_MARKETING_ENUM_NOVINKI", 132); // Новинки
define("PROPERTY_MARKETING_ENUM_SPETSPREDLOZHENIE", 133); // Спецпредложение
define("PROPERTY_MARKETING_ENUM_ONLY", 134); // Только в "Технодом"
define("PROPERTY_MARKETING_ENUM_GIFT", 135); // Подарочные комплекты

define("SITE_PREFIX_MOBILE" , "/m");  	//префикс пути мобильной версии сайта

define("ALMATY_LOCATION_ID", 1); // ID местоположения Алматы

define("PROPERTY_SECTIONS_FOR_GIFTS", 336); // Код свойства "секций" инф подраков
define("PROPERTY_CHARACTERISTICS", 56); // Код свойства с характеристиками
define("PRODUCTS_TO_COMPARE_CODE", 'PRODUCTS_TO_COMPARE'); // Code property

define("DEFAULT_GIFT_PRICE", 10); // стоимость подарка по умолчанию

define("PATH_TO_404", "404.php");//путь к странице с 404 ошибкой

// свойства заказов
define("PERSON_TYPE_PHYS_ID", 1); // ID физ. лица
define("PERSON_TYPE_LEGAL_ID", 2); // ID юр. лица

define("LOCATION_PERSON_ORDER_PROP_ID", 1); // ID свойства местоположения физ.лица
define("LOCATION_LEGAL_ORDER_PROP_ID", 8); // ID свойства местоположения юр.лица

define("PHONE_PERSON_ORDER_PROP_ID", 4); // ID свойства телефона физ.лица
define("PHONE_LEGAL_ORDER_PROP_ID", 13); // ID свойства телефона юр.лица

define("EMAIL_PERSON_ORDER_PROP_ID", 6); // ID свойства e-mail физ.лица
define("EMAIL_LEGAL_ORDER_PROP_ID", 12); // ID свойства e-mail юр.лица

// ID служб доставки
define("STANDART_DELIVERY_ID", 1);
define("STANDART_DELIVERY_ID1", 4);
define("SELF_DELIVERY_ID", 3);

// ID платёжных систем 
define("CASH_PAYMENT_ID", 1);
define("EPAY_PAYMENT_ID", 2);
define("CASHLESS_PAYMENT_ID", 3);

define("GROUP_ALL_USERS" ,2); // ID гуппы 'все пользователи'

define("LENGTH_PHONE", 11); // кол-во цифр в номере телефона -- +8(123)456-78-99

define("DEFAULT_QUANTITY", 100);

define("DOP_NEW_POST_KEY", '82b7e8c0ee63d0e606a2d6f330a7895a'); // ключ новой почты

$GLOBALS['GTM_DATA'] = array('detail'=>array(), 'impressions'=>array(), 'promoView'=>array());
$GLOBALS['GTM_POSITION'] = array('detail'=>0, 'impressions'=>0, 'promoView'=>0);
?>
