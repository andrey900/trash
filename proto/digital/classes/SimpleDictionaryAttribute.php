<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.03.14
 * Time: 17:45

0a5d0f597b197f92ad702ae8f161298d790a761d7
Date:   Thu Mar 13 18:27:51 2014 +0200

42de2aa1e2bbac8581efa4829a84e0cb26b47283
Date:   Fri Mar 7 17:56:35 2014 +0200
 */


class CSimpleDictionaryAttribute extends CSimpleAttribute{
    var $ar_data;               // справочник значений в формате XML_ID = NAME или NAME = ID, прочитанный из $IBLOCK_ID
    var $IBLOCK_ID;             // код инфоблока, если внешний справочник
    var $ar_structure;          // вся структура конвертации
    var $category3_uid;
    var $attribute_code;
    var $attribute_uid;
    var $ar_section_data;       // структура справочника = все секции из инфоблока DICTIONARY_IBLOCK

    var $ar_strategy; // массив со стратегиями поведения


    function  __construct(&$doc,$p_CODE, $p_IBLOCK_ID, $ar_p_values = Array(), $p_fake =':)') {

        parent::__construct($doc,$p_CODE, $ar_p_values);
        if (!IntVal($p_IBLOCK_ID)) {
            throw new Exception($p_CODE.' '.__CLASS__.' = IBLOCK_ID должен быть установлен');
        }
        $this->IBLOCK_ID = $p_IBLOCK_ID;

    }

    function InitDictionarySection(&$ar_structure, $category3_uid, $attribute_code, &$ar_section_data) {
        $this->ar_structure = $ar_structure;
        $this->category3_uid = $category3_uid;
        $this->attribute_code  = $attribute_code;
        $this->ar_section_data = &$ar_section_data;

        // TODO надо искать баг

        // проверяем чтобы нужные секции были, если их нет, то добавляем
        // категория
        if ( empty($this->ar_section_data[$this->category3_uid]) ) {
            if ( $ID = $this->AddDictionarySection( $this->ar_structure[$category3_uid][0],$category3_uid ) ) {
                $this->ar_section_data[$category3_uid] = $ID;
            }
        }

        $attribute_uid = $this->ar_structure[$category3_uid][$attribute_code][1];

        // для значений свойства
        $attribute_uid = $this->ar_structure[$category3_uid][$attribute_code][1];
        if ( empty($this->ar_section_data[$attribute_uid]) ) {
            if ( $ID = $this->AddDictionarySection( $this->ar_structure[$category3_uid][$attribute_code][0],$attribute_uid, $this->ar_section_data[$this->category3_uid] ) ) {
                $this->ar_section_data[$attribute_uid] = $ID;
            }
        }
        $this->attribute_uid = $this->ar_section_data[$attribute_uid];

        //echo '==>>'.count($this->ar_section_data);

        // теперь читаем справочник
        $this->ar_data = $this->ReadDictionary();
        
        //if($this->attribute_uid == 2268)
        	//p($this->ar_data);
    }


    function SetStrategy_array($ar) {
        $this->ar_strategy = $ar;
    }




    function ConvertXMLAttribute(&$node_ProductFeatures,&$node_list_property) {

        $value = CXMLProductFeatures::FindValueInProductFeatures($node_ProductFeatures, $this->ar_values);

	// если данные вообще не были найдены, то обнуляем предыдущее значение
	if (!count($value)) {
		$node_list_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE, ''));
	} else {

        // хоть тут и цикл, но записывается только последнее значение TODO
        while ( count($value) ) {
            $val = array_shift($value);
            if( isset($this->ar_strategy['VALIDATE']) ) {
                $val = $this->ar_strategy['VALIDATE']->Validate( $val );
            }
            $this->CURRENT_VALUE = $val;

            if ( $int = $this->ar_data[$val] ) {
                $node_list_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE, $int));
            } else {
                if ( $this->CanElementBeAdd($val) ) {
                    $int = $this->AddDictionaryElement( $val, $val, $this->attribute_uid );
                    $this->ar_data[ $val ] = $int;
                    $node_list_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE, $int));
                }  else {
                    global $ar_structure_data;
                    $g_UID = CXMLNodeWork::GetNodeValueByName( CXMLNodeWork::GetNodeByName(StorageSingleton::getOldTovar(),'Группы'), 'Ид' );
                    $str_code = sprintf("ERROR_2 Неверное значения свойства\t%s\t%s\t%s\t%s",$ar_structure_data[$g_UID][$this->CODE][0], $val,CXMLNodeWork::GetNodeValueByName(StorageSingleton::getOldTovar(),'Наименование'), CXMLNodeWork::GetNodeValueByName(StorageSingleton::getOldTovar(),'Ид'));
                    //AddMessage2Log($str_code);
                }
            }
        }//while
	}// else

	//


    }


    // читаем только из подсекции соответствующей этому атрибуту
    function ReadDictionary() {

        $res_10 = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>$this->IBLOCK_ID,"ACTIVE"=>"", "SECTION_ID"=>$this->attribute_uid),false, false, Array("ID","NAME","XML_ID") );
        for(;$ar_10 = $res_10->GetNext();) {
            $ar[ $ar_10["XML_ID"] ] = $ar_10["ID"];
        }
        return $ar;
    }


    function AddDictionaryElement($p_name, $p_XML_ID, $p_SECTION_ID = false) {
        $be = new CIBlockElement;
        
        /*ANIART*/
        /*$arFields = Array(
        		"ACTIVE"=>'Y',
        		"IBLOCK_ID" => $this->IBLOCK_ID,
        		"NAME" => $p_name,
        		"XML_ID" => $p_XML_ID,
        		"IBLOCK_SECTION_ID" => $p_SECTION_ID,
        		"SORT" => 500
        );*/
        $arFields = Array(
        		"ACTIVE"=>'Y',
        		"IBLOCK_ID" => $this->IBLOCK_ID,
        		"NAME" => htmlspecialchars_decode($p_name),
        		"XML_ID" => $p_XML_ID,
        		"SORT" => 500
        );
        if($arFields['IBLOCK_ID'] == BRANDS_IBLOCK_ID){
        	 
        	$curEl = array();
        	$arSelectEl = Array("ID");
        	$arFilterEl = Array("IBLOCK_ID"=>BRANDS_IBLOCK_ID, "XML_ID" => $p_XML_ID);
        	$resEl = CIBlockElement::GetList(Array(), $arFilterEl, false, false, $arSelectEl);
        	while($obEl = $resEl->GetNextElement())
        	{
        		$arFieldsEl = $obEl->GetFields();
        		$curEl = $arFieldsEl;
        	}
        	 
        	if(!empty($curEl)){
        		$db_old_groups = CIBlockElement::GetElementGroups($curEl['ID'], true);
        		$ar_new_groups = Array($p_SECTION_ID);
        		while($ar_group = $db_old_groups->Fetch())
        			$ar_new_groups[] = $ar_group["ID"];
        		CIBlockElement::SetElementSection($curEl['ID'], $ar_new_groups);
        	}else{
        		$arFields["IBLOCK_SECTION_ID"] = $p_SECTION_ID;
        	}
        	 
        	$params = array(
        			"max_len" => "100",
        			"change_case" => "L",
        			"replace_space" => "_",
        			"replace_other" => "_",
        			"delete_repeat_replace" => "true",
        			"use_google" => "false",
        	);
        	 
        	if (strlen($arFields["NAME"])>0 && strlen($arFields["CODE"])<=0) {
        		$arFields['CODE'] = CUtil::translit($arFields["NAME"], "ru", $params);
        	}
        }else{
        	$arFields["IBLOCK_SECTION_ID"] = $p_SECTION_ID;
        }
        /*END ANIART*/
        
        $ID = $be->Add($arFields);
        if(!$ID)  {
            AddMessage2Log($be->LAST_ERROR);
            return false;
        }
        unset($be);
        return $ID;
    }


    function AddDictionarySection($p_name, $p_XML_ID, $p_SECTION_ID = false) {
        $be = new CIBlockSection;
        global $DB;
        $arFields = Array(
            "ACTIVE"=>'Y',
            "IBLOCK_ID" => $this->IBLOCK_ID,
            "NAME" => $p_name,
            "XML_ID" => $p_XML_ID,
            "CODE" => $p_XML_ID,
            "IBLOCK_SECTION_ID" => $p_SECTION_ID,
            "SORT" => 500
        );
        $ID = $be->Add($arFields);
        if(!$ID)  {
            //AddMessage2Log( sprintf("ERROR_1 Ошибка создания секции\t%s\t%s\t%s", $p_name,$p_XML_ID,$be->LAST_ERROR) );
            return false;
        }
        unset($be);
        return $ID;
    }

    function CanElementBeAdd($name) {
        if( isset($this->ar_strategy['CAN_BE_ADD']) ) {

            return $this->ar_strategy['CAN_BE_ADD']->Validate($name, $this->ar_strategy['CAN_BE_ADD_PARAMS'] );

        } else {
            // делаем простейшую проверку по просьбе Заказчика на <>
            return $name=='<>' ? false : strlen($name) >=1;
        }
    }
}