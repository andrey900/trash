<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.03.14
 * Time: 22:41
 */

class CParseNameDictionaryAttribute extends CSimpleDictionaryAttribute{

    var $reg_expres;

    function  __construct(&$doc, $p_CODE, $p_IBLOCK_ID, $ar_p_values, $info) {
        parent::__construct($doc,$p_CODE, $p_IBLOCK_ID, $ar_p_values);
        $this->reg_expres = $info[4];
    }

    function ConvertXMLAttribute($node_old_tovar,$node_list_property) {

        $str = CXMLNodeWork::GetNodeValueByName($node_old_tovar,'Наименование');
        
        if ( preg_match($this->reg_expres,$str,$value) ) {
            $val = $value[0];
            if ( $int = $this->ar_data[ $val] ) {
            } else {
                $int = $this->AddDictionaryElement( $val, $val, $this->attribute_uid );
                $this->ar_data[ $val ] = $int;
            }
            $node_list_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE, $int));
        }

    }


} 