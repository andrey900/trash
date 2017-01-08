<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.03.14
 * Time: 19:11
 */

class CDecimalRangeDictionaryAttribute extends CSimpleDictionaryAttribute {



    function ConvertXMLAttribute($node_ProductFeatures,$node_list_property) {

        $value = CXMLProductFeatures::FindValueInProductFeatures($node_ProductFeatures, $this->ar_values);
        while ( count($value) ) {
        	
           
            
           
            //if(count($value) == 1){
            //	$val = current($value);
            //}else{
            	$val = array_shift($value);
            //}
            
           
            
            if( isset($this->ar_strategy['VALIDATE']) ) {
                $val = $this->ar_strategy['VALIDATE']->Validate( $val );
            }

            
            
            if ( $int = $this->GetRangeCode( $val ) ) {
            	
                $node_list_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE, $int));
            }
        }
    }


    // читаем из подсекции соответствующей этому атрибуту MIN и MAX
    function ReadDictionary() {

        $res_10 = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>$this->IBLOCK_ID,"ACTIVE"=>"", "SECTION_ID"=>$this->attribute_uid),false, false, Array("ID","NAME","IBLOCK_SECTION_ID","XML_ID","CODE","PROPERTY_MIN","PROPERTY_MAX") );
        for(;$ar_10 = $res_10->GetNext();) {
            $ar[ $ar_10["ID"] ] = $ar_10;
        }
        return $ar;
    }


    function GetRangeCode($value) {
    	
    	/*if("Диафрагма" == $this->ar_values['0']){
    		
    		$ex = explode("-", $value);
    		p($ex);
    		p(preg_replace('/[^0-9\.]/', '', $ex[0]));
    		p(preg_replace('/[^0-9\.]/', '', $ex[1]));
    		
    		p($this->ar_data);
    		//p($this->IBLOCK_ID); 
    		//p($this->attribute_uid);
    		die();
    	}*/
    	
    	
        if( is_array($this->ar_data ) ) {
            foreach($this->ar_data as $k=>$v) {
                if($value >= $v["PROPERTY_MIN_VALUE"]   &&  $value <= $v["PROPERTY_MAX_VALUE"]) {
                    return $k;
                }
            }
        }
        return false;
    }



} 