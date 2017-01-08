<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.03.14
 * Time: 18:49
 */

class CSimpleDecimalAttribute extends CSimpleAttribute{

    function ConvertXMLAttribute($node_ProductFeatures, $node_new_property) {

        $value = CXMLProductFeatures::FindValueInProductFeatures($node_ProductFeatures, $this->ar_values);
        if ( count($value) ) {
            $this->CURRENT_VALUE = array_shift($value);
            $node_new_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE,  $this->CURRENT_VALUE ));
        }
    }


} 