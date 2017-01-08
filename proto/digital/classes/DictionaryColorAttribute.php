<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 31.03.14
 * Time: 22:50
 */

class CDictionaryColorAttribute extends CDictionaryAttribute{

    // читаем из элементов из PREVIEW вытаскиваем цвета
    function ReadDictionary() {

        $res_10 = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>$this->IBLOCK_ID,"ACTIVE"=>""),false, false, Array("ID","NAME","XML_ID",'PREVIEW_TEXT') );
        for($ar=array();$ar_10 = $res_10->GetNext();) {

            $ar_result = explode("\n",$ar_10["~PREVIEW_TEXT"]);
            if (count($ar_result)>1 ) {
                foreach($ar_result as $v) {
                    if ( strlen(trim($v)) > 3) {
                        $ar[ trim($v) ] = $ar_10["ID"];
                    }
                }
            } else {
                $ar[ $ar_10["XML_ID"] ] = $ar_10["ID"];
            }
        }
        return $ar;
    }

    function CanElementBeAdd($name) {
        return strlen($name) >=6;
    }

} 