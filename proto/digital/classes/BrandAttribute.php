<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.03.14
 * Time: 17:46
 */

class CBrandAttribute extends CSimpleDictionaryAttribute {

    var $ar_xml_dictionary_data;
    var $doc_old;

   /*
    * ANIART
    */
    var $category3_uid;
    var $attribute_code;
    var $attribute_uid_brand;
    var $ar_section_data_brand;// структура справочника = все секции из инфоблока BRAND_IBLOCK
    
    //function  __construct(&$doc,&$p_doc_old, $p_CODE, $p_IBLOCK_ID, $ar_p_values = Array() ) {
    function  __construct(&$doc,&$p_doc_old, $p_CODE, $p_IBLOCK_ID, $ar_p_values = Array(), &$ar_section_data_brands) {
    	$this->ar_section_data_brands = $ar_section_data_brands;
    /*
     * END ANIART
     */
        $this->doc_old = $p_doc_old;
        parent::__construct($doc,$p_CODE, $p_IBLOCK_ID, $ar_p_values);
        $this->ReadFileDictionary();

        if ( $this->IBLOCK_ID ) {
            $this->ar_data = $this->ReadDictionary();
        }

    }

    function ReadDictionary() {

        $res_10 = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>$this->IBLOCK_ID,"ACTIVE"=>""),false, false, Array("ID","NAME","XML_ID") );
        for(;$ar_10 = $res_10->GetNext();) {
            $ar[ $ar_10["XML_ID"] ] = $ar_10["ID"];
        }
        return $ar;
    }


    function ReadFileDictionary() {
        $ar_result = array();
        $xpath = new DOMXPath($this->doc_old);
        $node_list = $xpath->query('/КоммерческаяИнформация/Классификатор/Свойства/Свойство');
        foreach( $node_list as $node_property) {
            if ( CXMLNodeWork::GetNodeValueByName($node_property,'Ид') == 'ID_СправочникБренды') {
                $node_list_dic = CXMLNodeWork::GetNodeByName($node_property,'ВариантыЗначений');
                foreach ($node_list_dic->childNodes as $node_dic) {
                    $ar_result[ CXMLNodeWork::GetNodeValueByName($node_dic,'ИдЗначения') ] = CXMLNodeWork::GetNodeValueByName($node_dic,'Значение');
                }
            }
        }

        $this->ar_xml_dictionary_data = $ar_result;
    }

    /*ANIART*/
    function InitDictionarySection(&$ar_structure, $category3_uid, $attribute_code, &$ar_section_data_brands) {
    	$this->ar_structure = $ar_structure;
    	$this->category3_uid = $category3_uid;
    	$this->attribute_code  = $attribute_code;
    	$this->ar_section_data_brands = &$ar_section_data_brands;
    
    	// TODO надо искать баг
    	$this->ar_key_ProductFeatures[] = $this->category3_uid;
    	// проверяем чтобы нужные секции были, если их нет, то добавляем
    	// категория
    	if ( empty($this->ar_section_data_brands[$this->category3_uid]) ) {
    		if ( $ID = $this->AddDictionarySection( $this->ar_structure[$category3_uid][0],$category3_uid ) ) {
    			$this->ar_section_data_brands[$category3_uid] = $ID;
    		}
    	}
    	/*ANIART*/
    	//$attribute_uid = $this->ar_structure[$category3_uid][$attribute_code][1];
    	//$this->attribute_uid_brand = $this->ar_section_data_brands[$attribute_uid];
    	$this->attribute_uid_brand = $this->ar_section_data_brands[$this->category3_uid];
    	/*END ANIART*/
    	// теперь читаем справочник
    	$this->ar_data = $this->ReadDictionary();
    }
    /*END ANIART*/
    
    function ConvertXMLAttribute($node_tovar, $node_new_property) {
        $node_property = CXMLNodeWork::GetNodeByName($node_tovar, 'ЗначенияСвойств');
        $xml_brand = CXMLNodeWork::GetNodeValueByNameFromPropertyList($node_property, 'ID_СправочникБренды');
        if ( $name = $this->ar_xml_dictionary_data[$xml_brand] ) {
            if ($int = $this->ar_data[$xml_brand] ) {
            	/*ANIART*/
            	$curEl = array();
            	$arSelectEl = Array("ID");
            	$arFilterEl = Array("IBLOCK_ID"=>BRANDS_IBLOCK_ID, "ID" => $int);
            	$resEl = CIBlockElement::GetList(Array(), $arFilterEl, false, false, $arSelectEl);
            	while($obEl = $resEl->GetNextElement())
            	{
            		$arFieldsEl = $obEl->GetFields();
            		$curEl = $arFieldsEl;
            	}
            	 
            	if(!empty($curEl)){
            		$db_old_groups = CIBlockElement::GetElementGroups($curEl['ID'], true);
            		$ar_new_groups = Array($this->attribute_uid_brand);
            		while($ar_group = $db_old_groups->Fetch())
            			$ar_new_groups[] = $ar_group["ID"];
            		CIBlockElement::SetElementSection($curEl['ID'], $ar_new_groups);
            	}
            	/*END ANIART*/
            } else {
            	
                $int = $this->AddDictionaryElement( $name, $xml_brand, $this->attribute_uid_brand );
                $this->ar_data[ $xml_brand ] = $int;
            }

            $node_new_property->appendChild(CXMLNodeWork::AddPropertyNode($this->doc,$this->CODE,$int));
        } else {
            $str_code = sprintf('Товар:%s   UID:%s', CXMLNodeWork::GetNodeValueByName(StorageSingleton::getOldTovar(),'Наименование'), CXMLNodeWork::GetNodeValueByName(StorageSingleton::getOldTovar(),'Ид'));
            AddMessage2Log('В справочнике свойств import.XML не содержится бренд '.$xml_brand.' на который есть ссылка в товаре:'.$str_code);
        }

    }

}
