<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22.04.14
 * Time: 17:09
 */

class CParseRegDictionaryAttribute extends CParseNameDictionaryAttribute {

    function ConvertXMLAttribute($node_old_tovar,$node_list_property) {

        $value = CXMLProductFeatures::FindValueInProductFeatures($node_old_tovar, $this->ar_values);

        if (count($value)) {

            $str = array_shift($value);
//echo "$str \n";
            if( isset($this->ar_strategy['VALIDATE']) ) {
                $str = $this->ar_strategy['VALIDATE']->Validate( $str );
            }

            $this->CURRENT_VALUE = $str;

            // должен быть массив регулярных
            if (!is_array($this->reg_expres))
                throw new Exception($p_CODE.' '.__CLASS__.' должен быть массив регулярных выражений    REG=>NAME ');

            foreach ($this->reg_expres as $reg=>$name) {
  //              echo "$reg \n";
                if ( preg_match($reg,$str,$value) ) {
                    $val = $value[0];
    //                print_r($value);
                    if ( $int = $this->ar_data[ $name ] ) {
                    } else {
                        $int = $this->AddDictionaryElement( $name, $name, $this->attribute_uid );
                        $this->ar_data[ $name ] = $int;
                    }
                    $node_list_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE, $int));
                    return $val;
                }
            }
        }
        return false;
    }
    //
}
