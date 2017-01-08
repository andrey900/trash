<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.03.14
 * Time: 18:17
 */

class CDictionaryMultiAttribute extends CSimpleDictionaryAttribute{

    function ConvertXMLAttribute($node_ProductFeatures,$node_list_property) {

        $value = CXMLProductFeatures::FindValueInProductFeatures($node_ProductFeatures, array_keys($this->ar_values));
        $value = array_intersect_assoc ($this->ar_values,$value);
        // запись мультисвойства
        if ( count($value) ) {
            $ar = array();
            foreach($value as $val=>$v) {
                if ( $int = $this->ar_data[$val] ) {
                } else {
                    $int = $this->AddDictionaryElement( $val, $this->attribute_uid );
                    $this->ar_data[ $val ] = $int;
                }
                $ar[] = Array($int);
            }
            //print_r($ar);
            //$node_list_property->appendChild(CXMLNodeWork::AddMulPropertyNode($this->doc,$this->CODE, $ar));
            $this->StoreData($ar,$node_list_property);
            return 1;
        }
        return 0;
    }


    function StoreData($ar,$node_list_property) {
        $node_list_property->appendChild(CXMLNodeWork::AddMulPropertyNode($this->doc,$this->CODE, $ar));
    }


    // особенность  для MULTIPLE  -   NAME=> XML  читаем только из подсекции соответствующей этому атрибуту
    function ReadDictionary() {

        $res_10 = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>$this->IBLOCK_ID,"ACTIVE"=>"", "SECTION_ID"=>$this->attribute_uid),false, false, Array("ID","NAME","XML_ID") );
        for(;$ar_10 = $res_10->GetNext();) {
            $ar[ $ar_10["NAME"] ] = $ar_10["XML_ID"];
        }
        return $ar;
    }


    function AddDictionaryElement($p_name, $p_SECTION_ID = false) {
        $be = new CIBlockElement;
        $arFields = Array(
            "ACTIVE"=>'Y',
            "IBLOCK_ID" => $this->IBLOCK_ID,
            "NAME" => $p_name,
            "IBLOCK_SECTION_ID" => $p_SECTION_ID,
            "SORT" => 500
        );
        $ID = $be->Add($arFields);
        if(!$ID)  {
            AddMessage2Log($be->LAST_ERROR);
            return false;
        }
        unset($be);
        return $ID;
    }



}


// это специальный класс для телефонов, чтобы совмещать в одном месте парсинг как свойства, так и названия
class CParseRegDictionaryLTEAttribute extends CDictionaryMultiAttribute {

    function StoreData($ar,$node_list_property) {

        //$obj = StorageSingleton::getInstance();
        //$node = $obj::getOldTovar();
        $str = CXMLNodeWork::GetNodeValueByName(StorageSingleton::getOldTovar(),'Наименование');

        if ( preg_match('/-4([^-4]+)$/',$str,$value) ) {
            $str_names = 'Поддержка LTE/4G';
            if ( $int = $this->ar_data[$str_names] ) {
            } else {
                $int = $this->AddDictionaryElement( $str_names, $this->attribute_uid );
                $this->ar_data[$str_names] = $int;
            }
            //print_r($this->ar_data);
            $ar[] = Array($int);
        }
        $node_list_property->appendChild(CXMLNodeWork::AddMulPropertyNode($this->doc,$this->CODE, $ar));
    }
}