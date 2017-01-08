<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.03.14
 * Time: 20:00
 */

class CCheckboxYNAttribute extends CSimpleAttribute {

    function ConvertXMLAttribute($node_ProductFeatures, $node_new_property) {

        $value = CXMLProductFeatures::FindValueInProductFeatures($node_ProductFeatures, $this->ar_values);
        if ( count($value) ) {
            $this->CURRENT_VALUE = array_shift($value);
            $node_new_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE,  $this->CURRENT_VALUE =='Да' ? 'XML_Y' : 'XML_N' ));
        } else {
        }
    }


}


class CCheckboxYNExistAttribute extends CSimpleAttribute {

    var $ar_find_values;

    function  __construct(&$doc,$p_CODE, $ar_p_values = Array(), $ar_find_values = Array()) {

        parent::__construct($doc,$p_CODE, $ar_p_values);
        if ( is_array($ar_find_values) && count($ar_find_values) ) {
            foreach($ar_find_values as $k=>$v){
                $this->ar_find_values[$k] = strtolower($v);
            }
        } else {
            $this->ar_find_values = Array('да', 'есть');
        }

    }



    function ConvertXMLAttribute($node_ProductFeatures, $node_new_property) {
        $value = CXMLProductFeatures::FindValueInProductFeatures($node_ProductFeatures, $this->ar_values);
        if ( count($value) ) {
            $this->CURRENT_VALUE = preg_match( sprintf("/%s/iu", implode('|',$this->ar_find_values) ), array_shift($value) ) ? 'XML_Y' : 'XML_N';
            $node_new_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE,$this->CURRENT_VALUE));
        } else {
            $this->CURRENT_VALUE = nil;
            $node_new_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE,  'XML_N' ));
        }
    }


}