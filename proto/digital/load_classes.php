<?
/**
 * Функция дописывает к имени файла префикс wm_ 
 * 
 * @param string $src_file
 * @return string
 */
function GetWMFileName($src_file) {
	$f_info = pathinfo($src_file);
	p($f_info,false);
	return $f_info["dirname"]."/wm_".$f_info["basename"];
}
class CXMLNodeWork {


    // простое копирование подветки
    function CopyXMLSubTree(&$newdoc,&$node_old_tovar, &$node_new_tovar, $name_for_copy) {
        if ( $node = CXMLNodeWork::GetNodeByName($node_old_tovar,$name_for_copy) ) {
            $node = $newdoc->importNode($node, true);
            $node_new_tovar->appendChild($node);
        }
    }



	// возвращает значение ноды с заданным ИД из списка свойств
	function GetNodeValueByNameFromPropertyList(&$DOMNode, $prop_ID) {
		foreach($DOMNode->childNodes as $node) {
			$code = CXMLNodeWork::GetNodeValueByName($node,'Ид');
			if($code == $prop_ID) {
				$val = CXMLNodeWork::GetNodeValueByName($node,'Значение');
				return $val;
			}
		}
		return false;
	}



    // возвращает значение ноды с заданным ИД из списка свойств
    function GetNodeMulValueByNameFromPropertyList(&$DOMNode, $prop_ID) {
        $ar_result = array();
        foreach($DOMNode->childNodes as $node) {
            $code = CXMLNodeWork::GetNodeValueByName($node,'Ид');
            if($code == $prop_ID) {
                foreach($node->childNodes as $node_v2) {
                    if ($node_v2->nodeName == 'ЗначениеСвойства' ) {
                        $ar_result[] = CXMLNodeWork::GetNodeValueByName($node_v2,'Значение');
                    }
                }
                return $ar_result;
            }
        }
        return false;
    }


	// возвращает ноду с заданным ИД из списка свойств
	function GetNodeByNameFromPropertyList(&$DOMNode, $prop_ID) {
		foreach($DOMNode->childNodes as $node) {
			$code = CXMLNodeWork::GetNodeValueByName($node,'Ид');
			if($code == $prop_ID) {
				return $node;
			}
		}
		return false;
	}


    // копирует ноду с заданным ИД из списка свойств
    function CopyNodeByNameFromPropertyList(&$newdoc, &$node_old_tovar, $prop_ID) {

        if ( $node_property = CXMLNodeWork::GetNodeByName($node_old_tovar,'ЗначенияСвойств') ) {
            $node_list = CXMLNodeWork::GetNodeByNameFromPropertyList($node_property,$prop_ID);
            $i = 0;
            foreach($node_list->childNodes as $node) {
                // для множественных свойств
                if ($node->nodeName =='ЗначениеСвойства' ) {
                    if ( $node->firstChild->nodeValue ) {
                        $ar[][0] = $node->firstChild->nodeValue;
                    }
                }

                // для единичных свойств
                if ($node->nodeName =='Значение' ) {
                        $ar[][0] = $node->nodeValue;
                }

            }
            // добавляем мультисвойство
            if (count($ar) ) {
                $nodes = CXMLNodeWork::AddMulPropertyNode($newdoc,$prop_ID,$ar);
                return $nodes;
            }
        }
        return false;
    }



	// копирует значение ноды
    static public
	function CopyNodeValueByName(&$newdoc, &$DOMNode_new, &$DOMNode_old, $name, $new_name=false) {
		$val = CXMLNodeWork::GetNodeValueByName($DOMNode_old,$name);
		if($val) {
			$DOMNode_new->appendChild( $newdoc->createElement($new_name ? $new_name: $name, htmlspecialchars($val) ) ); //todo следить за корректностью
		}
	}
	//


    // копирует значение ноды @Группы@ для товара
    function CopyGroupsNode(&$newdoc, &$DOMNode_new, &$DOMNode_old) {
        $nodes = CXMLNodeWork::GetNodeByName($DOMNode_old,'Группы');
        $i=0;
        $uid_group = '';
        foreach($nodes->childNodes as $node) {
            if(!$i++) {
                $node_groups = $newdoc->createElement('Группы', '');
            }
            // TODO будет добавлена логика проверки на группы
            if($node->nodeName == 'Ид') {
                $node_groups->appendChild( $newdoc->createElement('Ид', $node->nodeValue) );
                $uid_group = $node->nodeValue;
            }
        }
        if($i) {
            $DOMNode_new->appendChild($node_groups);
        }
        return $uid_group;

    }
    //


    static public    
	function GetNodeByName($DOMNode, $name) {
		foreach($DOMNode->childNodes as $node) {
			if($node->nodeName == $name) {
				return $node;
			}
		}
		return  false;
	}
	//
    static public	
	function GetNodeValueByName($DOMNode, $name) {
		$result = false;
		$node = CXMLNodeWork::GetNodeByName($DOMNode, $name);
		if ($node) {
			$result = $node->nodeValue; 
		}
		return $result;
	}
	//

    static public
	function AddPropertyNode(&$doc,$code,$value) {
		$node = $doc->createElement('ЗначенияСвойства', '');
            	$node->appendChild( $doc->createElement('Ид',$code));
            	$node->appendChild( $doc->createElement('Значение',$value));
		return $node;
	}

    static public	
    function AddMulPropertyNode(&$doc,$code,$ar) {
        $node = $doc->createElement('ЗначенияСвойства', '');
        $node->appendChild( $doc->createElement('Ид',$code));
        if( count($ar) ) {
            //print_r($ar);die();
            foreach($ar as $k=>$v) {
                $node->appendChild( $doc->createElement('Значение',$v[0]));
                $node_detail = $doc->createElement('ЗначениеСвойства','');
                $node_detail->appendChild( $doc->createElement('Значение',$v[0]));
                $node_detail->appendChild( $doc->createElement('Описание',$v[1]));
                $node->appendChild($node_detail);
            }
        } else {
            return false;
        }

        return $node;
    }


} //




class CStrucrureWork {

	function AddColor($p_name) {
		return $this->AddDictionaryElement( $p_name, COLOR_IBLOCK_ID );
	}

	function AddSurface($p_name) {
		return $this->AddDictionaryElement( $p_name, SURFACE_IBLOCK_ID );
	}

	function AddErasability($p_name) {
		return $this->AddDictionaryElement( $p_name, ERASABILITY_IBLOCK_ID );
	}



	function AddCountry($p_name) {
		return $this->AddDictionaryElement( $p_name, COUNTRY_IBLOCK );
	}


	function AddBrand($p_name) {
		return $this->AddDictionaryElement( $p_name, BRAND_IBLOCK_NEW );
	}
	
	public
	function AddDictionaryElement($p_name, $p_IBLOCK_ID, $p_SECTION_ID = false) {
		$be = new CIBlockElement;
		global $DB;		
		$arFields = Array(
			"ACTIVE"=>'Y',
			"IBLOCK_ID" => $p_IBLOCK_ID,  
			"NAME" => $p_name,
			"XML_ID" => $p_name,			
			"IBLOCK_SECTION_ID" => $p_SECTION_ID,
			"SORT" => 500,
			"PROPERTY_VALUES"=> Array('MODERATION'=>1)
			);
		$ID = $be->Add($arFields);
		if(!$ID)  {
			AddMessage2Log($be->LAST_ERROR);
			return false;			
		}
		return $ID;
	}
}


function ReadDictionary(&$ar,$IBLOCK_ID) {
	$res_10 = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_ID,"ACTIVE"=>""),false, false, Array("ID","NAME","XML_ID") );
	for(;$ar_10 = $res_10->GetNext();) {
		$ar[ $ar_10["NAME"] ] = $ar_10["ID"];  
	}
	return $ar;
}


//!
function ReadSKUDictionaryWithSectionXML(&$ar,$IBLOCK_ID) {

      	// читаем секции
      	// в XML_ID находится код, по которому сопоставляютя  группы для товаровов в них входящих
	$ar_section_xml = Array();
      	$res_10 = CIBlockSection::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_ID,"ACTIVE"=>""));
      	for(;$ar_10 = $res_10->GetNext();) {
      		$ar_section_xml[ $ar_10["ID"] ] = $ar_10["XML_ID"];
      	}

	$res_10 = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>$IBLOCK_ID,"ACTIVE"=>""),false, false, Array("ID","NAME","XML_ID","IBLOCK_SECTION_ID") );
	for(;$ar_10 = $res_10->GetNext();) {
		$ar_10["SECTION_XML"] = $ar_section_xml[$ar_10["IBLOCK_SECTION_ID"]];
		$ar[ $ar_10["XML_ID"] ] = $ar_10;  
	}
	return $ar;
}




// класс-обертка для проведения итеративных действий
// = иннициализация по структурированным данным
// = конвертация атрибутов
// основной функционал см. в CDictionaryAttribute
class CDictionaryAttributes {
	var $attributes;
	var $attributes_map;

	function  __construct($ar) {
		$this->attributes = $ar;
		foreach($this->attributes as $k=>$v) {
			$this->attributes_map[$k] = new CDictionaryAttribute($v,$k);
		}
	}

	function ConvertXMLAttributes($node_property,$node_old_property,$newdoc) {
		foreach($this->attributes_map as $k=>$obj) {
			$obj->AddXMLAttribute($node_property,$node_old_property,$newdoc);
		}
	}


    function ConvertArrayAttributes($node_property,$node_old_property,$newdoc) {
    }


   	function __destruct() {
   	}
} //





// обработка атрибутов
// приходят данные вида
//	"1621df22-2a1c-11e1-91f7-00241dd010f2"=>Array("COLOR",COLOR_IBLOCK_ID),  // Цвет
/*
class CDictionaryAttribute {
	var $ar_data;
	var $ar_base;
	var $uid;

	function  __construct($ar,$uid) {
		$this->ar_base = $ar;
		$this->uid = $uid;
        // читаем данные из справочника function ReadDictionary(&$ar,$IBLOCK_ID)
		$this->ar_data = ReadDictionary($this->ar_data,$this->ar_base[1]);
	}

	function AddXMLAttribute($node_property,$node_old_property,$newdoc) {
		if( $str = CXMLNodeWork::GetNodeValueByNameFromPropertyList($node_old_property,$this->uid) ) {  
      			$int = isset($this->ar_data[$str]) ? $this->ar_data[$str] : 0;
      			if(!$int) {
				$int = CStrucrureWork::AddDictionaryElement( $str, $this->ar_base[1] );
       				$this->ar_data[ $str ] = $int;
       			}
			$node_property->appendChild(CXMLNodeWork::AddPropertyNode($newdoc,$this->ar_base[0],$int));
		}
	} // function


}
*/

class CDictionaryPRICEAttribute {
	var $ar_data;
	var $ar_sections;

	function  __construct() {
		$this->ar_data = Array();
		$this->ar_data_ID = Array();
		$this->ar_sections = Array();
		self::ReadDictionary();
	}

        function ReadDictionary() {
		// читаем секции
		// в XML_ID находится код, по которому считываются группы цен для товаров
		$res_10 = CIBlockSection::GetList(Array(), Array("IBLOCK_ID"=>PRICEFILTER_IBLOCK,"ACTIVE"=>""));
        	for(;$ar_10 = $res_10->GetNext();) {
			$this->ar_sections[ $ar_10["ID"] ] = $ar_10["XML_ID"];
        	}

		// читаем элементы
        	$res_10 = CIblockElement::GetList(Array(), Array("IBLOCK_ID"=>PRICEFILTER_IBLOCK,"ACTIVE"=>"", "INCLUDE_SUBSECTIONS"=>'Y'),false, false, Array("ID","NAME","IBLOCK_SECTION_ID","XML_ID","CODE","PROPERTY_MIN","PROPERTY_MAX") );
        	for(;$ar_10 = $res_10->GetNext();) {
			$this->ar_data[ $this->ar_sections[ $ar_10["IBLOCK_SECTION_ID"] ] ][ $ar_10["ID"] ] = $ar_10;
			$this->ar_data_ID[ $ar_10["IBLOCK_SECTION_ID"] ][ $ar_10["ID"] ] = $ar_10;
        	}
        }

        function GetPriceCode($code, $price) {
		$price = intval($price);
		if( is_array($ar = $this->ar_data[ $code ] ) ) {
			foreach($ar as $k=>$v) {
				if($price >= $v["PROPERTY_MIN_VALUE"]   &&  $price <= $v["PROPERTY_MAX_VALUE"]) {
					return $k;
				}
			}
		}
		return false;
	} //

}



	function RunXMLLoader() {
		// запуск загрузки
		global $APPLICATION, $USER, $str_LOAD_MODE;

//AddMessage2Log($str_LOAD_MODE);
//return 0;
		
		unset($_SESSION);
		$USER->Logout();
		$USER->Authorize(1);
		unset($_SESSION["BX_CML2_IMPORT"]);		
		
		$APPLICATION->IncludeComponent("aniart:catalog.import.1c", "", Array(
			"IBLOCK_TYPE" => COption::GetOptionString("catalog", "1C_IBLOCK_TYPE", "-"),
			"SITE_LIST" => array(COption::GetOptionString("catalog", "1C_SITE_LIST", "-")),
			"INTERVAL" => 0,
			"GROUP_PERMISSIONS" => explode(",", COption::GetOptionString("catalog", "1C_GROUP_PERMISSIONS", "")),
			"GENERATE_PREVIEW" => COption::GetOptionString("catalog", "1C_GENERATE_PREVIEW", "Y"),
			"PREVIEW_WIDTH" => COption::GetOptionString("catalog", "1C_PREVIEW_WIDTH", "100"),
			"PREVIEW_HEIGHT" => COption::GetOptionString("catalog", "1C_PREVIEW_HEIGHT", "100"),
			"DETAIL_RESIZE" => COption::GetOptionString("catalog", "1C_DETAIL_RESIZE", "Y"),
			"DETAIL_WIDTH" => COption::GetOptionString("catalog", "1C_DETAIL_WIDTH", "300"),
			"DETAIL_HEIGHT" => COption::GetOptionString("catalog", "1C_DETAIL_HEIGHT", "300"),
			"ELEMENT_ACTION" => COption::GetOptionString("catalog", "1C_ELEMENT_ACTION", "D"),
			"SECTION_ACTION" => COption::GetOptionString("catalog", "1C_SECTION_ACTION", "D"),
			"FILE_SIZE_LIMIT" => COption::GetOptionString("catalog", "1C_FILE_SIZE_LIMIT", 200*1024),
			"USE_CRC" => COption::GetOptionString("catalog", "1C_USE_CRC", "Y"),
			"USE_ZIP" => COption::GetOptionString("catalog", "1C_USE_ZIP", "Y"),
			"USE_OFFERS" => COption::GetOptionString("catalog", "1C_USE_OFFERS", "N"),
			"USE_IBLOCK_TYPE_ID" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_TYPE_ID", "N"),
			"USE_IBLOCK_PICTURE_SETTINGS" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_PICTURE_SETTINGS", "Y"),
			"TRANSLIT_ON_ADD" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_ADD", "N"),
			"TRANSLIT_ON_UPDATE" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_UPDATE", "N"),
			)
		);
	}
	//


function GetCatalogStructure($par_IBLOCK) {
    $res = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$par_IBLOCK), Array('ID','XML_ID'));
    for($result = array();$ar = $res->Fetch();) {
        $result[ $ar['XML_ID'] ] = $ar['ID'];
    }
    return $result;
}


function GetCatalogXMl2ID($par_IBLOCK) {
    $res = CIBlockElement::GetList(Array(),Array("IBLOCK_ID"=>$par_IBLOCK), false, false, Array('ID','XML_ID'));
    for($result = array();$ar = $res->Fetch();) {
        $result[ $ar['XML_ID'] ] = $ar['ID'];
    }
    return $result;
}



?>
