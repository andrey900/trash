<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.03.14
 * Time: 21:37
 */

class CDimensionsAttribute extends CSimpleAttribute{

    var $delimiter;
    var $info;
    function  __construct(&$doc,$p_CODE, $info) {
        $ar_key_ProductFeatures = is_array($info[2]) ?  $info[2] : Array ($info[0]);
        $this->info = $info;
        $this->delimiter = strlen($info[1]) ? $info[1] : 'x';
        parent::__construct($doc,$p_CODE, $ar_key_ProductFeatures);


    }


    function ConvertXMLAttribute($node_ProductFeatures, $node_new_property) {

        $value = CXMLProductFeatures::FindValueInProductFeatures($node_ProductFeatures, $this->ar_values);
        if ( count($value) ) {
            $ar = explode($this->delimiter,array_shift($value));
            $this->CURRENT_VALUE = $ar[$this->info[3]];
            $node_new_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE,  $this->CURRENT_VALUE ));
        }
    }


} 