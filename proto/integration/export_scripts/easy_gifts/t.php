<?php

require "../loader.php";

use Studio8\Main\Integration\XmlParser;
use Studio8\Main\Integration\ElementCreator;
use Studio8\Main\Integration\Helper;

/** Block strategy **/
$itemAddStrategy = function($propName, $xml){
    return $xml->baseinfo->$propName;
};
$itemAddStrategySize = function($propName, $xml){
    return $xml->attributes->$propName;
};

$itemAddStrategyMaterial = function($propName, $xml){
    return $xml->materials->material->$propName;
};

$itemAddStrategyTypeOfApp = function($propName, $xml){
    return $xml->markgroups->markgroup->$propName;
};

$itemUniversalStrategy = function($propName, $xml){
    $arFiled = explode("_", $propName);
    $t1 = $xml->{$arFiled[0]};
    return $t1->{$arFiled[1]};
};

$getCategoriesStratery = function($propName, $xml){
	$arCategory = [];
	$arSCategory = [];
	$arT = [];

	foreach($xml->categories->category as $category){
		$ccc = [];
		if( (string)$category->id && (string)$category->name ){
			$arCategory[(string)$category->id] = (string)$category->name;
			
			$ccc['id'] = (string)$category->id;
			$ccc['name'] = (string)$category->name;
			$ccc['parent_id'] = 0;
			$arT[$ccc['id']] = $ccc;
		}
		if( $category->subcategory ){
			foreach($category->subcategory as $scategory){
				if( (string)$scategory->id && (string)$scategory->name ){
					$arSCategory[(string)$scategory->id] = (string)$scategory->name;

					$ccc['id'] = (string)$scategory->id;
					$ccc['name'] = (string)$scategory->name;
					$ccc['parent_id'] = (string)$category->id;
					$arT[$ccc['id']] = $ccc;
				}
			}
		}
	}
	
	return $arT;
};
/** ---- END ---- **/

/*$t = new XmlParser("easy_gifts_min.xml");

$arCategories = [];
foreach ($t->getXml()->product as $product) {
    $t->setStrategy($getCategoriesStratery);
    $result = $t->getProp($product, false);
    foreach ($result as $item) {
    	if( !$arCategories[$item['id']] )
    		$arCategories[$item['id']] = $item;
    }
}
*/

$arCategories = Array
(
    Array
        (
            'id' => '1',
            'name' => 'test',
            'intro' => '',
            'content' => '',
            'parent_id' => 0,
        ),
    Array
        (
            'id' => '2',
            'name' => 'Ручки и письменные наборы',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '3',
            'name' => 'Mark Twain',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '4',
            'name' => 'Аксессуары для офиса',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '5',
            'name' => 'Часы и электроника',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '6',
            'name' => 'Зонты и непромокаемые пальто',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '7',
            'name' => 'Туристические аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '8',
            'name' => 'Спортивные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '9',
            'name' => 'Косметика и уход за телом',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '10',
            'name' => 'Предметы домашнего обихода',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '11',
            'name' => 'Ящики и аксессуары для бара',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '12',
            'name' => 'Фонарики и инструменты',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '13',
            'name' => 'Ferraghini',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '15',
            'name' => 'Остатки серии',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '16',
            'name' => 'Акриловые брелки и значки',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '17',
            'name' => 'Металлические значки',
            'intro' => '',
            'content' => '',
            'parent_id' => '16',
        ),
    Array
        (
            'id' => '119',
            'name' => 'одежда для рекламы',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '124',
            'name' => 'Футболка',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '140',
            'name' => 'Рекламные кружки',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '19',
            'name' => 'Необычные гаджеты',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '20',
            'name' => 'Открывалки и ножы',
            'intro' => '',
            'content' => '',
            'parent_id' => '11',
        ),
    Array
        (
            'id' => '21',
            'name' => 'Фляги',
            'intro' => '',
            'content' => '',
            'parent_id' => '11',
        ),
    Array
        (
            'id' => '22',
            'name' => 'Ящики и наборы для вина',
            'intro' => '',
            'content' => '',
            'parent_id' => '11',
        ),
    Array
        (
            'id' => '24',
            'name' => 'Кухонные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '10',
        ),
    Array
        (
            'id' => '25',
            'name' => 'Аксессуары для ванной комнаты',
            'intro' => '',
            'content' => '',
            'parent_id' => '10',
        ),
    Array
        (
            'id' => '26',
            'name' => 'Керамические кружки',
            'intro' => '',
            'content' => '',
            'parent_id' => '140',
        ),
    Array
        (
            'id' => '27',
            'name' => 'Мебель и обстановка',
            'intro' => '',
            'content' => '',
            'parent_id' => '10',
        ),
    Array
        (
            'id' => '28',
            'name' => 'Уход за обувью',
            'intro' => '',
            'content' => '',
            'parent_id' => '10',
        ),
    Array
        (
            'id' => '29',
            'name' => 'Контейнеры и мельницы для специй',
            'intro' => '',
            'content' => '',
            'parent_id' => '10',
        ),
    Array
        (
            'id' => '180',
            'name' => 'Зимние шапки',
            'intro' => '',
            'content' => '',
            'parent_id' => '8',
        ),
    Array
        (
            'id' => '32',
            'name' => 'Свечи и ароматические наборы',
            'intro' => '',
            'content' => '',
            'parent_id' => '10',
        ),
    Array
        (
            'id' => '33',
            'name' => 'Аксессуары для путешествий',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '34',
            'name' => 'Кепки',
            'intro' => '',
            'content' => '',
            'parent_id' => '8',
        ),
    Array
        (
            'id' => '35',
            'name' => 'Игры и развлечения',
            'intro' => '',
            'content' => '',
            'parent_id' => '8',
        ),
    Array
        (
            'id' => '36',
            'name' => 'Одеяла и шезлонги',
            'intro' => '',
            'content' => '',
            'parent_id' => '8',
        ),
    Array
        (
            'id' => '37',
            'name' => 'Матрасы и коврики',
            'intro' => '',
            'content' => '',
            'parent_id' => '8',
        ),
    Array
        (
            'id' => '202',
            'name' => 'емкость 2200 mAh',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '203',
            'name' => 'емкость 2400 mAh',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '39',
            'name' => 'Очки и футляры для очков',
            'intro' => '',
            'content' => '',
            'parent_id' => '8',
        ),
    Array
        (
            'id' => '112',
            'name' => 'Зеркальце',
            'intro' => '',
            'content' => '',
            'parent_id' => '9',
        ),
    Array
        (
            'id' => '42',
            'name' => 'Акриловые брелки',
            'intro' => '',
            'content' => '',
            'parent_id' => '16',
        ),
    Array
        (
            'id' => '43',
            'name' => 'Другие',
            'intro' => '',
            'content' => '',
            'parent_id' => '16',
        ),
    Array
        (
            'id' => '45',
            'name' => 'Металлические брелки',
            'intro' => '',
            'content' => '',
            'parent_id' => '16',
        ),
    Array
        (
            'id' => '46',
            'name' => 'Цветные карандаши и карандаши',
            'intro' => '',
            'content' => '',
            'parent_id' => '2',
        ),
    Array
        (
            'id' => '47',
            'name' => 'Металлические ручки',
            'intro' => '',
            'content' => '',
            'parent_id' => '2',
        ),
    Array
        (
            'id' => '111',
            'name' => 'Пляжные зонты',
            'intro' => '',
            'content' => '',
            'parent_id' => '6',
        ),
    Array
        (
            'id' => '49',
            'name' => 'Пластиковые ручки',
            'intro' => '',
            'content' => '',
            'parent_id' => '2',
        ),
    Array
        (
            'id' => '50',
            'name' => 'Маркеры и фломастеры',
            'intro' => '',
            'content' => '',
            'parent_id' => '2',
        ),
    Array
        (
            'id' => '51',
            'name' => 'Письменные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '2',
        ),
    Array
        (
            'id' => '52',
            'name' => 'Аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '13',
        ),
    Array
        (
            'id' => '53',
            'name' => 'Офисные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '13',
        ),
    Array
        (
            'id' => '56',
            'name' => 'Автомобильные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '12',
        ),
    Array
        (
            'id' => '57',
            'name' => 'Фонарики и лампы',
            'intro' => '',
            'content' => '',
            'parent_id' => '12',
        ),
    Array
        (
            'id' => '58',
            'name' => 'Мерки и рулетки',
            'intro' => '',
            'content' => '',
            'parent_id' => '12',
        ),
    Array
        (
            'id' => '59',
            'name' => 'Ножи и карманные ножи',
            'intro' => '',
            'content' => '',
            'parent_id' => '12',
        ),
    Array
        (
            'id' => '61',
            'name' => 'Зажигалки',
            'intro' => '',
            'content' => '',
            'parent_id' => '12',
        ),
    Array
        (
            'id' => '139',
            'name' => 'Вечные пера и ручки-роллеры',
            'intro' => '',
            'content' => '',
            'parent_id' => '2',
        ),
    Array
        (
            'id' => '64',
            'name' => 'Автоматические зонты',
            'intro' => '',
            'content' => '',
            'parent_id' => '6',
        ),
    Array
        (
            'id' => '65',
            'name' => 'Мануальные зонты',
            'intro' => '',
            'content' => '',
            'parent_id' => '6',
        ),
    Array
        (
            'id' => '67',
            'name' => 'Дождевики',
            'intro' => '',
            'content' => '',
            'parent_id' => '6',
        ),
    Array
        (
            'id' => '68',
            'name' => 'Офисные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '4',
        ),
    Array
        (
            'id' => '69',
            'name' => 'Футляры для ручек и визитных карточек',
            'intro' => '',
            'content' => '',
            'parent_id' => '4',
        ),
    Array
        (
            'id' => '70',
            'name' => 'Блокноты',
            'intro' => '',
            'content' => '',
            'parent_id' => '4',
        ),
    Array
        (
            'id' => '71',
            'name' => 'Настольные контейнеры',
            'intro' => '',
            'content' => '',
            'parent_id' => '4',
        ),
    Array
        (
            'id' => '116',
            'name' => 'Пеналы',
            'intro' => '',
            'content' => '',
            'parent_id' => '4',
        ),
    Array
        (
            'id' => '73',
            'name' => 'Рюкзаки',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '74',
            'name' => 'Термосы и бидоны',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '75',
            'name' => 'Сумки для покупок',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '76',
            'name' => 'Сумки-холодильники',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '77',
            'name' => 'Чемоданы',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '78',
            'name' => 'Аксессуары для мобильного телефона',
            'intro' => '',
            'content' => '',
            'parent_id' => '5',
        ),
    Array
        (
            'id' => '79',
            'name' => 'Компьютерные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '5',
        ),
    Array
        (
            'id' => '80',
            'name' => 'Калькуляторы',
            'intro' => '',
            'content' => '',
            'parent_id' => '5',
        ),
    Array
        (
            'id' => '183',
            'name' => 'Smile Hand',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '82',
            'name' => 'Метеорологические станции',
            'intro' => '',
            'content' => '',
            'parent_id' => '5',
        ),
    Array
        (
            'id' => '83',
            'name' => 'Часы и смартвотчи',
            'intro' => '',
            'content' => '',
            'parent_id' => '5',
        ),
    Array
        (
            'id' => '84',
            'name' => 'Настольные часы',
            'intro' => '',
            'content' => '',
            'parent_id' => '5',
        ),
    Array
        (
            'id' => '85',
            'name' => 'Настенные часы',
            'intro' => '',
            'content' => '',
            'parent_id' => '5',
        ),
    Array
        (
            'id' => '86',
            'name' => 'Косметические аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '9',
        ),
    Array
        (
            'id' => '87',
            'name' => 'Косметички',
            'intro' => '',
            'content' => '',
            'parent_id' => '9',
        ),
    Array
        (
            'id' => '88',
            'name' => 'Галстуки',
            'intro' => '',
            'content' => '',
            'parent_id' => '9',
        ),
    Array
        (
            'id' => '182',
            'name' => 'Толстовки, флисовая одежда, softshell',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '89',
            'name' => 'Маникюрные наборы',
            'intro' => '',
            'content' => '',
            'parent_id' => '9',
        ),
    Array
        (
            'id' => '90',
            'name' => 'Наборы косметики для женщин',
            'intro' => '',
            'content' => '',
            'parent_id' => '9',
        ),
    Array
        (
            'id' => '91',
            'name' => 'Наборы косметики для мужчин',
            'intro' => '',
            'content' => '',
            'parent_id' => '9',
        ),
    Array
        (
            'id' => '93',
            'name' => 'Сумки и рюкзаки',
            'intro' => '',
            'content' => '',
            'parent_id' => '13',
        ),
    Array
        (
            'id' => '94',
            'name' => 'Портфели',
            'intro' => '',
            'content' => '',
            'parent_id' => '4',
        ),
    Array
        (
            'id' => '95',
            'name' => 'Бумажники',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '96',
            'name' => 'Сумки для путешествий',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '97',
            'name' => 'Сумки для ноутбука',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '102',
            'name' => 'USB флешки',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '147',
            'name' => 'Панель ниже 100 см',
            'intro' => '',
            'content' => '',
            'parent_id' => '6',
        ),
    Array
        (
            'id' => '104',
            'name' => '1GB',
            'intro' => '',
            'content' => '',
            'parent_id' => '102',
        ),
    Array
        (
            'id' => '105',
            'name' => '2GB',
            'intro' => '',
            'content' => '',
            'parent_id' => '102',
        ),
    Array
        (
            'id' => '106',
            'name' => '4GB',
            'intro' => '',
            'content' => '',
            'parent_id' => '102',
        ),
    Array
        (
            'id' => '107',
            'name' => '8GB',
            'intro' => '',
            'content' => '',
            'parent_id' => '102',
        ),
    Array
        (
            'id' => '108',
            'name' => '16GB',
            'intro' => '',
            'content' => '',
            'parent_id' => '102',
        ),
    Array
        (
            'id' => '109',
            'name' => 'часы',
            'intro' => '',
            'content' => '',
            'parent_id' => '13',
        ),
    Array
        (
            'id' => '110',
            'name' => 'Аксессуары для упражнений',
            'intro' => '',
            'content' => '',
            'parent_id' => '8',
        ),
    Array
        (
            'id' => '113',
            'name' => 'Спортивные сумки и мешки',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '117',
            'name' => 'Корзины для пикника и гриль',
            'intro' => '',
            'content' => '',
            'parent_id' => '8',
        ),
    Array
        (
            'id' => '118',
            'name' => 'Инструменты',
            'intro' => '',
            'content' => '',
            'parent_id' => '12',
        ),
    Array
        (
            'id' => '126',
            'name' => 'Размер S',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '127',
            'name' => 'Размер M',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '128',
            'name' => 'Размер L',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '129',
            'name' => 'Размер XL',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '130',
            'name' => 'Размер XXL',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '135',
            'name' => 'Футболка поло',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '144',
            'name' => 'Фарфоровые кружки',
            'intro' => '',
            'content' => '',
            'parent_id' => '140',
        ),
    Array
        (
            'id' => '143',
            'name' => 'Пластиковые кружки',
            'intro' => '',
            'content' => '',
            'parent_id' => '140',
        ),
    Array
        (
            'id' => '145',
            'name' => 'Термокружки',
            'intro' => '',
            'content' => '',
            'parent_id' => '140',
        ),
    Array
        (
            'id' => '146',
            'name' => 'Коробки для кружек',
            'intro' => '',
            'content' => '',
            'parent_id' => '140',
        ),
    Array
        (
            'id' => '148',
            'name' => 'Панель от 100 до 110 см',
            'intro' => '',
            'content' => '',
            'parent_id' => '6',
        ),
    Array
        (
            'id' => '149',
            'name' => 'Панель выше 110 см',
            'intro' => '',
            'content' => '',
            'parent_id' => '6',
        ),
    Array
        (
            'id' => '159',
            'name' => 'Power bank',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '161',
            'name' => 'Доступно 24 часа',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '162',
            'name' => 'Доступно 24-72 часа',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '164',
            'name' => 'Сумки JASSZ',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '165',
            'name' => 'Хлопчатобумажные сумки и мешки',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '166',
            'name' => 'Herlitz',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '167',
            'name' => 'Pelikan',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '168',
            'name' => 'Diplomat',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '169',
            'name' => '32GB',
            'intro' => '',
            'content' => '',
            'parent_id' => '102',
        ),
    Array
        (
            'id' => '170',
            'name' => '64GB',
            'intro' => '',
            'content' => '',
            'parent_id' => '102',
        ),
    Array
        (
            'id' => '171',
            'name' => 'Письменные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '167',
        ),
    Array
        (
            'id' => '172',
            'name' => 'Цветные карандаши и маркеры',
            'intro' => '',
            'content' => '',
            'parent_id' => '167',
        ),
    Array
        (
            'id' => '173',
            'name' => 'Вечные пера и ручки-роллеры',
            'intro' => '',
            'content' => '',
            'parent_id' => '167',
        ),
    Array
        (
            'id' => '174',
            'name' => 'Чернила',
            'intro' => '',
            'content' => '',
            'parent_id' => '167',
        ),
    Array
        (
            'id' => '175',
            'name' => 'Продукты для школы и офиса',
            'intro' => '',
            'content' => '',
            'parent_id' => '166',
        ),
    Array
        (
            'id' => '176',
            'name' => 'Письменные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '166',
        ),
    Array
        (
            'id' => '177',
            'name' => 'Блокноты',
            'intro' => '',
            'content' => '',
            'parent_id' => '166',
        ),
    Array
        (
            'id' => '178',
            'name' => 'Чашки и блюдце',
            'intro' => '',
            'content' => '',
            'parent_id' => '140',
        ),
    Array
        (
            'id' => '179',
            'name' => 'Стеклянные кружки',
            'intro' => '',
            'content' => '',
            'parent_id' => '140',
        ),
    Array
        (
            'id' => '181',
            'name' => 'Светоотражатели',
            'intro' => '',
            'content' => '',
            'parent_id' => '7',
        ),
    Array
        (
            'id' => '184',
            'name' => '',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '185',
            'name' => 'Кепки',
            'intro' => '',
            'content' => '',
            'parent_id' => '184',
        ),
    Array
        (
            'id' => '186',
            'name' => 'Зимние шапки',
            'intro' => '',
            'content' => '',
            'parent_id' => '184',
        ),
    Array
        (
            'id' => '187',
            'name' => 'XS',
            'intro' => '',
            'content' => '',
            'parent_id' => '119',
        ),
    Array
        (
            'id' => '188',
            'name' => 'Ungaro',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '189',
            'name' => 'Шарфы и платки',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '190',
            'name' => 'Флешки USB',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '191',
            'name' => 'Бумажники и кошельки',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '193',
            'name' => 'Сувенирные наборы',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '194',
            'name' => 'Письменные аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '195',
            'name' => 'Сумки',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '196',
            'name' => 'Аксессуары',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '197',
            'name' => 'Блокноты',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '198',
            'name' => 'Брелки',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '199',
            'name' => 'Упаковки',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '200',
            'name' => 'Мужские наручные часы',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '201',
            'name' => 'Женские наручные часы',
            'intro' => '',
            'content' => '',
            'parent_id' => '188',
        ),
    Array
        (
            'id' => '204',
            'name' => 'емкость 2600 mAh',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '205',
            'name' => 'емкость 4000 mAh',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '206',
            'name' => 'емкость 5000 mAh',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '207',
            'name' => 'емкость 6000 mAh',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '208',
            'name' => 'емкость 6600 mAh',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '209',
            'name' => 'емкость 10000 mAh',
            'intro' => '',
            'content' => '',
            'parent_id' => '159',
        ),
    Array
        (
            'id' => '211',
            'name' => 'Ручки с гравировкой за 3 евроцента!',
            'intro' => '',
            'content' => '',
            'parent_id' => '1',
        ),
    Array
        (
            'id' => '212',
            'name' => 'стекло',
            'intro' => '',
            'content' => '',
            'parent_id' => '13',
        ),
);


function makeTree($parent, $array)
{
  if (!is_array($array) OR empty($array)) return FALSE;

  $output = '<ul>';

  foreach($array as $key => $value):
    if ($value['parent_id'] == $parent):
        $output .= '<li>';

        if ($value['parent_id'] == 1):
            $output .= $value['name'];

            $matches = array();

            foreach($array as $subkey => $subvalue):
                if ($subvalue['parent_id'] == $value['id']):
                    $matches[$subkey] = $subvalue;
                endif;
            endforeach;

            $output .= makeTree($value['id'], $matches);
        else:
            $output .= $value['name'];
            $output .= '</li>';
        endif;
    endif;

  endforeach;

  $output .= '</ul>';

  return $output;
}

echo makeTree(1, $arCategories);

// p($arCategories, 0, 1);