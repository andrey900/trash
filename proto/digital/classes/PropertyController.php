<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.03.14
 * Time: 17:55
 */

// класс для точечной обработки индивидуальным набором классов, свойств товара.
// поведение находится в конечных классах атрибутов
class CPropertyController {

    var $newdoc;
    var $doc;
    var $ar_data;
    var $ar_section_data;

    function  __construct(&$ar_structure, &$p_newdoc, &$p_doc) {

        $this->newdoc = $p_newdoc;
        $this->doc = $p_doc;
        $this->ar_data = $ar_structure;

        // читаем все секции из инфоблока "значения свойств"
        $this->ar_section_data = array();
        $res = CIBlockSection::GetList(Array("left_margin"=>"asc"), Array("IBLOCK_ID"=>DICTIONARY_IBLOCK));
        while($ar = $res->Fetch() ) {
            $this->ar_section_data[$ar["XML_ID"]] = $ar["ID"];
        }
        
        /*
         * ANIART
         */
        // читаем все секции из инфоблока "бренды"
        $this->ar_section_data_brands = array();
        $res = CIBlockSection::GetList(Array("left_margin"=>"asc"), Array("IBLOCK_ID"=>BRAND_IBLOCK));
        while($ar = $res->Fetch() ) {
        	$this->ar_section_data_brands[$ar["XML_ID"]] = $ar["ID"];
        }
        /*
         * END ANIART
         */

        foreach($ar_structure as $section_key => $ar_propertis_data) {

            foreach($ar_propertis_data as $property_key => $ar_property_data) {

                //по каким ключам ищем
                $ar_key_ProductFeatures = is_array($ar_property_data[2]) ?  $ar_property_data[2] : Array ($ar_property_data[0]);


                // что ищем, значения или регулярные
                $ar_possiblevalues  = is_array($ar_property_data[6]) ?  $ar_property_data[6] : 0;

                if (preg_match('#^BRAND#',$property_key)) {

                   /*
                	* ANIART 
                	*/
                    //$obj = new CBrandAttribute($this->newdoc, $this->doc, 'BRAND', BRAND_IBLOCK);
                	$obj = new CBrandAttribute($this->newdoc, $this->doc, 'BRAND', BRAND_IBLOCK, array(), $this->ar_section_data_brands);
                	$obj->InitDictionarySection($ar_structure, $section_key, $property_key, $this->ar_section_data_brands);
                	/*
                	 * 
                	 * END ANIART
                	 */

                } elseif(preg_match('#^CATALOG_[\d]+#',$property_key)) {

                    $class_name = $ar_property_data[5] ? $ar_property_data[5] : 'CSimpleDictionaryAttribute';
                    if (class_exists($class_name)) {
                        $obj = new $class_name($this->newdoc, $property_key, DICTIONARY_IBLOCK,$ar_key_ProductFeatures, $ar_property_data  );
                        $obj->InitDictionarySection($ar_structure,$section_key,$property_key,$this->ar_section_data);

                        // иннициализация стратегий
                        if (  $ar = $this->InitStrategy( $ar_property_data[7] ) ) {
                            $obj->SetStrategy_array( $ar );
                        }

                    } else {
                        throw new Exception($class_name.'  Класс не объявлен');
                    }


                } elseif(preg_match('#^DECIMAL_[\d]+#',$property_key)) {

                    // тут могут быть варианты
                    if ( strlen($ar_property_data[1]) ) {
                        // часть значения
                        $obj = new CDimensionsAttribute($this->newdoc, $property_key, $ar_property_data );
                    } else {
                        $obj = new CSimpleDecimalAttribute($this->newdoc, $property_key, $ar_key_ProductFeatures );
                    }

                } elseif( preg_match('#^YN_CHECKBOX_[\d]+#',$property_key) ) {

                    $obj = new CCheckboxYNExistAttribute($this->newdoc, $property_key, $ar_key_ProductFeatures,$ar_possiblevalues );

                }   elseif( preg_match('#^DICTIONARY_SINGLE_[\d]{1}$#',$property_key) ) {

                    $p_IBLOCK_ID = $ar_property_data[4];
                    $class_name = $ar_property_data[5] ? $ar_property_data[5] : 'CDictionaryAttribute';
                    if (class_exists($class_name)) {
                        $obj = new $class_name($this->newdoc, $property_key, $p_IBLOCK_ID, $ar_key_ProductFeatures );
                    } else {
                        throw new Exception($class_name.'  Класс не объявлен');
                    }

                }   elseif( preg_match('#^DICTIONARY_MUL_[\d]+#',$property_key) ) {

                    $class_name = $ar_property_data[5] ? $ar_property_data[5] : 'CDictionaryMultiAttribute';
                    if (class_exists($class_name)) {
                    	if('CDictionaryMultyAttributeMinMax' == $class_name){
	                    	
	                    	$obj = new $class_name($this->newdoc, $property_key, DICTIONARY_IBLOCK, $ar_key_ProductFeatures, $ar_property_data );
	                    	$obj->InitDictionarySection($ar_structure,$section_key,$property_key,$this->ar_section_data);

                    	}else{
                        	$obj = new $class_name($this->newdoc, $property_key, DICTIONARY_IBLOCK, $ar_key_ProductFeatures );
                        	$obj->InitDictionarySection($ar_structure,$section_key,$property_key,$this->ar_section_data);
                    	}
                    } else {
                        throw new Exception($class_name.'  Класс не объявлен');
                    }

                } else {
                    $obj = nil;
                }

                //
                $this->ar_data[$section_key][$property_key]['OBJECT'] = $obj;

                //
            }
        }
    }


    function InitStrategy( $arr ) {
        // иннициализация стратегий
        $ar = array();
        if (count($arr)) {
            foreach($arr as $k=>$v) {

                if (preg_match('#PARAM#i',$k)) {
                    $ar[$k] = $v;
                } else {
                    $ar[$k] = new $v();
                }
            }
            return $ar;
        }
        return false;
    }


    // переносим все данные для товара их Характеристик в Свойства
    function ProcessTovarAttributes($uid_group, &$node_old_tovar, &$node_list_property) {
    	
        $node_ProductFeatures = CXMLNodeWork::GetNodeByName($node_old_tovar, 'ХарактеристикиТовара');

        foreach($this->ar_data[$uid_group] as $property_key => $ar_property_data) {
            $obj = $ar_property_data['OBJECT'];
            if ($obj!=nil) {
                $obj_name = get_class( $obj );
                if( in_array($obj_name,Array('CBrandAttribute','CParseNameDictionaryAttribute') ) ){
                        $obj->ConvertXMLAttribute($node_old_tovar,$node_list_property);
                } elseif( !$obj_name ) {
                    // нет объекта
                } else {
                        if ( $node_ProductFeatures ) {
                            $obj->ConvertXMLAttribute($node_ProductFeatures,$node_list_property);
                        }
                }
            }
        }
    }

}



class StorageSingleton {
    protected static $_instance;

    protected static $node_new_tovar;
    protected static $node_old_tovar;
    protected static $node_new_property;
    protected static $node_old_property;

    private function __construct() {
    }

    public static function getOldTovar() {
        return self::$node_old_tovar;
    }

    public static function setOldTovar($node) {
        self::$node_old_tovar = $node;
    }


    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __clone() {
    }

    private function __wakeup() {
    }
}