<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.03.14
 * Time: 19:05
 */

class CSimpleAttribute {

    var $CODE;      // код свойства
    var $ar_values;       // значения, которые ищутся в Характеристиках товара
    var $doc;             // ссылка на документ
    var $CURRENT_VALUE;             // текущее значение свойства

    function  __construct(&$doc,$p_CODE, $ar_p_values = Array()) {
        $this->doc = $doc;
        $this->CODE = $p_CODE;
        $this->ar_values = $ar_p_values;
    }

    function ConvertXMLAttribute($node_ProductFeatures, $node_new_property) {
    }

} 