<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.03.14
 * Time: 20:18
 */

class CDecimalDimansialRangeDictionaryAttribute extends CDecimalRangeDictionaryAttribute{

    var $info;
    var $delimiter;
    function  __construct(&$doc,$p_CODE, $p_IBLOCK_ID, $ar_p_values, $info) {
        $this->info = $info;
        $this->delimiter = 'x';
        parent::__construct($doc,$p_CODE, $p_IBLOCK_ID, $ar_p_values);
    }


    function ConvertXMLAttribute($node_ProductFeatures,$node_list_property) {

        $value = CXMLProductFeatures::FindValueInProductFeatures($node_ProductFeatures, $this->ar_values);
        while ( count($value) ) {
            $ar = explode($this->delimiter,array_shift($value));
            $int = $ar[$this->info[4]];
            $this->CURRENT_VALUE = $this->GetRangeCode($int);
            if ( $this->CURRENT_VALUE  ) {
                $node_list_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE, $this->CURRENT_VALUE));
            }
        }
    }

} 