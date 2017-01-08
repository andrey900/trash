<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.03.14
 * Time: 17:49
 */

define('BRAND_IBLOCK', 4);
define('DICTIONARY_IBLOCK', 18);
define('COLOR_IBLOCK', 16);
define('COUNTRY_IBLOCK', 17);


// список свойств, которые встречаются во всех разделах каталога
global $general_property_filter;

$general_property_filter = array(
	"HIT" => array("SORT" => 0, "TITLE" => "Акции", "TEMPLATE" => "sales", "LINK_TO_PRODUCT" => NULL), /** SORT was 1000, sma set 0 **/
);

// корневые секции, которые мы будем показывать/импортировать
$const_ar_uid_section_import = array(
		
        //'8c2eeaa7-2f0a-11e3-9858-00199999144e', //Телевизоры, фото, видео, аудио
		'df66956a-716c-11e4-bdee-0025906944e4',
		
        //'e53a7d3b-6d47-11e3-b15e-00199999144e', //Кухонная техника
);

if ( !function_exists('IsProcessImportGroup') ) {

    // для DEBUG - проверяем какую подгруппу импортить
    function IsProcessImportGroup($uid_group) {
        //return ANIART_DEBUG ? $uid_group =='8c2eeb48-2f0a-11e3-9858-00199999144e': true;  // Сотовые
        return true;
    }
}



//'DECIMAL_3'=>array('Макс. Потр. мощн-ть, W','2d286610-2f0a-11e3-9858-00199999144e-1',Array('Макс. Потр. Мощность, W','Макс. Потр. мощн-ть, W')),
// 0 = То как будет называться свойство
// 1 = XML_ID секции в инфоблоке "значения свойств"  для хранения значений справочника этого свойства
// 2 = массив ключей по которым будут искаться значения в свойстве "Характеристики товара", если отсутствует, то берется [0]
// 3 = имя шаблона для визуализации
// 4 = COLOR_IBLOCK идентификатор внешнего справочника если справочник? для CDecimalDimansialRangeDictionaryAttribute  = порядковый номер фрагмента который нужно распарсить
// 5 = уникальный класс CDecimalRangeDictionaryAttribute
// 6 = что ищем, ключи или регулярные, для каждого класса своё
// 7 = массив стратегий поведения:
        // 'VALIDATE'=>'CDecimalValue', преобразование перед сохранением
        // CAN_BE_ADD - проверка, может ли быть значение свойства добавлено

//require(dirname(__FILE__).'/catalog_structure/BT.php');
require(dirname(__FILE__).'/catalog_structure/TVaudioVideo.php');



$ar_structure_data = array();
$ar_structure_data = array_merge($ar_structure_data,$ar_structure_data_TV);
