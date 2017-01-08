<?php

/* Простите меня я был не прав когда писал данный класс
Класс предназначен для автоматического разбора текстовых свойств и заполнения свойств у которых
стоит привязка к элементам инфоблока(цвет и размер)
Так же для автоматического заполнения соответствий размеров из справочников размеров
*/
class CAniartHandlers
{

	// start template from mail mess
	public function HookOnBeforeEventAddHandler(&$event, &$lid, &$arFields)
    {
        $arFields["EVENT"] = $event;
    }
    
    public function HookOnBeforeEventSend(&$arFields, &$arTemplate)
    {
        //p2f(array('fields'=>$arFields, 'template'=>$arTemplate));
        $mess = $arTemplate["MESSAGE"];
        foreach($arFields as $keyField => $arField)
            $mess = str_replace('#'.$keyField.'#', $arField, $mess);

        if ( in_array($arTemplate['ID'], self::$arMailTemplate) || $arTemplate['FIELD2_VALUE']=='YES' ) {
            ob_start();
            include($_SERVER["DOCUMENT_ROOT"]."/include/styleMail.tpl");
            $pattern = ob_get_contents();
            ob_end_clean();
            $mess = GetMessageBody($pattern,array("#MESSAGE#"=>$mess));
            $arTemplate["MESSAGE"] = $mess;
        }
    }
    // end template from mail mess

    // нерабочие ф-и для сбрасывания цены если она в нуле
	public function OBPriceUpdate($ID, &$arFields)
    {
        if( (real)$arFields['PRICE']==0 )
    		CPrice::DeleteByProduct($arFields['PRODUCT_ID']);
    	return true;
    }

    public function OPriceAdd($ID, $arFields)
    {
        if( (real)$arFields['PRICE']==0 )
            CPrice::DeleteByProduct($arFields['PRODUCT_ID']);
        return true;
    }
    // конец нераб ф-ий

    /**
    ** начало
    ** взято с кристалов тяжелые обработки
    **/
	public static $arProperty = array(PROPERTY_COLOR_ID=>PROPERTY_COLORS_M_ID, PROPERTY_SIZE_ID=>PROPERTY_SIZES_M_ID);
	protected static $arPropSizes = array('SS'=>PROPERTY_SIZES_METRICS_SS, 'PP'=>PROPERTY_SIZES_METRICS_PP, 'MM'=>PROPERTY_SIZES_METRICS_MM);
	protected static $arFilter = array();
	private static $arResult = array();

    // создаем обработчик события "OnBeforeIBlockElementAdd", "OnBeforeIBlockElementUpdate"
    public function OBIBlockEAUAddProperty(&$arFields)
    {
    	// Проверка на инфоблок каталога
    	if( $arFields['IBLOCK_ID'] != CATALOG_IBLOCK_ID )
    		return true;

    	foreach ($arFields['PROPERTY_VALUES'] as $key => $value) {

    		if( array_key_exists($key, self::$arProperty) ){
    			$methodName = 'getValue_'.self::$arProperty[$key];
    			$arElem = self::$methodName( $value );

    			if( !empty($arElem) ){
	    			foreach( $arFields['PROPERTY_VALUES'][self::$arProperty[$key]] as $k=>$v){
	    				$arEl = array_shift($arElem);
	    				$arFields['PROPERTY_VALUES'][self::$arProperty[$key]][$k]['VALUE'] = key($arEl);
	    			}
	    		}
    		}
    	}
//p(self::$arResult, false, true);
    	return true;
    }

    public function OBIBlockEAUExpancionSize(&$arFields){
    	if( $arFields['IBLOCK_ID'] != CATALOG_IBLOCK_ID )
    		return true;

    	self::MegaMethod($arFields["ID"]);
    	return true;
    }

    /**
    *
    *
    **/
    protected static function getValue_295($arInValue){//colors
    	$name = current($arInValue);
    	
    	self::$arResult = array();
    	self::$arFilter['IBLOCK_ID'] = IBLOCK_ID_COLORS;

    	if( !strripos($name['VALUE'], ';') ){
    		self::$arFilter['NAME'] = CAniartTools::full_trim($name['VALUE']);
    		self::$arResult[] = self::getElemInfo();
    	} else {
    		$arSubColors = explode(";", $name['VALUE']);
			foreach ($arSubColors as $val) {
				self::$arFilter['NAME'] = CAniartTools::full_trim($val);
    			self::$arResult[] = self::getElemInfo();
			}
    	}
    	
    	return self::$arResult;
    }

    /**
    *
    *
    **/
    protected static function getValue_296($arInValue){//sizes
    	$name    = current($arInValue);
    	
    	self::$arResult = array();
    	self::$arFilter['IBLOCK_ID'] = IBLOCK_ID_SIZES;

    	if( !strripos($name['VALUE'], ';') ){
    		self::sizeType($name['VALUE']);
    	} else {
    		$arSubColors = explode(";", $name['VALUE']);
			foreach ($arSubColors as $val) {
				self::sizeType($val);
			}
    	}
		
    	return self::$arResult;
    }

    /**
    * Trim property size string (40X32)
    * @input - string
    **/
    protected function sizeType($name){
    	
    	if( !strripos($name, 'X') ){
    		self::$arFilter['NAME'] = CAniartTools::full_trim($name);
    		self::$arResult[] = self::getElemInfo();
    	} else {
    		$arSubSizes = explode("X", $name);
    		$Pristavka = substr($arSubSizes[0], 0, 2);
			foreach ($arSubSizes as $val) {

				if( strripos($val, $Pristavka)===false ){
					$razm = $Pristavka.' '.CAniartTools::full_trim($val);
					self::$arFilter['NAME'] = CAniartTools::full_trim($razm);
				} else {
					self::$arFilter['NAME'] = CAniartTools::full_trim($val);
				}

				self::$arResult[] = self::getElemInfo();
			}
    	}
    }

    /**
    * Find element by name
    * @self::arFilter - array
    * return - array
    **/
    private static function getElemInfo(){
		$arRes = CAniartTools::_GetInfoElements(false, array(), self::$arFilter);
		$arRes = self::noExistElement($arRes);
    	return $arRes;
    }

    /**
    * Control return value, if empty add new element
    * @input - array
    * return int
    **/
    private static function noExistElement($arResGetElementinfo){
    	if( !empty($arResGetElementinfo) )
    		return $arResGetElementinfo;
    	
    	return self::addNewElems();
    }

    /**
    * Add new element if search not result
    * @self::arFilter - array
    * return int
    **/
    private static function addNewElems(){
    	$el = new CIBlockElement;
    	global $USER;
		$arLoadProductArray = Array(
		  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
		  "IBLOCK_SECTION_ID" => false,       // элемент лежит в корне раздела
		  "IBLOCK_ID"      => self::$arFilter['IBLOCK_ID'],
		  "ACTIVE"         => "Y",            // активен
		  );
		$arLoadProductArray['NAME'] = self::$arFilter['NAME'];
		
		$PRODUCT_ID = $el->Add($arLoadProductArray);

		if( $PRODUCT_ID )
			$res[$PRODUCT_ID] = array('ID'=>$PRODUCT_ID, 'NAME'=>$arLoadProductArray['NAME']);
			return $res;
    }

    /**
    * Служит для автоматического заполнения размеров из справочников(переход от одной системы к другой)
    * Тут творяться страшные дела!!! Примерное описание: выбираем все названия из трех типов справочников
    * делаем сортировку данных названий по возрастанию
    * Делаем доп выборку для определения текущего свойства элемента(размер)
    * Определяем тип текущего размера(ММ, SS, PP)
    * дальше ходим по данному масиву выбираю границу попадания размера в диапазон(диапазон от пред знач(122=>0.1) к след(123=>0.2),  все значения которые промижуточные попадат в 122)
    * Заполняю соответствующие свойства
    */
    protected static function MegaMethod($elementId){

    	$arSelect = array('ID', 'NAME', 'PROPERTY_CONFIRM_SS', 'PROPERTY_CONFIRM_PP');
		$arFilter = array('IBLOCK_ID'=>IBLOCK_ID_SIZE_MM);
		$arResult = CAniartTools::GetInfoElements(false, $arSelect, $arFilter);

		foreach ($arResult as $value) {
			$arRes_MMs[$value['ID']] = $value['NAME'];
		}
		$arRes_MMs = CAniartTools::sortableSize($arRes_MMs);// Отсортированный по возростанию
		$arRes_MM  = $arResult; // реальные размеры без сортировки

		$arSelect = array('ID', 'NAME', 'PROPERTY_CONFIRM_MM', 'PROPERTY_CONFIRM_PP');
		$arFilter = array('IBLOCK_ID'=>IBLOCK_ID_SIZE_SS);
		$arResult = CAniartTools::GetInfoElements(false, $arSelect, $arFilter);

		foreach ($arResult as $value) {
			$arRes_SSs[$value['ID']] = $value['NAME'];
		}
		$arRes_SSs = CAniartTools::sortableSize($arRes_SSs);
		$arRes_SS  = $arResult;

		$arSelect = array('ID', 'NAME', 'PROPERTY_CONFIRM_SS', 'PROPERTY_CONFIRM_MM');
		$arFilter = array('IBLOCK_ID'=>IBLOCK_ID_SIZE_PP);
		$arResult = CAniartTools::GetInfoElements(false, $arSelect, $arFilter);

		foreach ($arResult as $value) {
			$arRes_PPs[$value['ID']] = $value['NAME'];
		}
		$arRes_PPs = CAniartTools::sortableSize($arRes_PPs);
		$arRes_PP  = $arResult;

		$arSelect = array('ID', 'NAME');
		$arFilter = array('IBLOCK_ID'=>IBLOCK_ID_SIZES, "ACTIVE"=>'Y');
		$arResult = CAniartTools::_GetInfoElements(false, $arSelect, $arFilter);// весь раздел справочника общих размеров
		foreach ($arResult as $value) {
			$arHabdbookSizes[$value['ID']] = $value['NAME'];
		}

		// ф-я для получения свойства справочника тек элемента
		function GetPropertyInfo($iblock, $id, $arPropName, $arHabdbookSizes){
			$tt = CIBlockElement::GetProperty((int)$iblock, (int)$id);
			while ($ob = $tt->GetNext())
			    {
			        if( $arPropName == $ob['CODE'] )
			        	$arResult[$arPropName][$ob['VALUE']] = $arHabdbookSizes[$ob['VALUE']];
			    }
			    return $arResult;
			}

		$arResult = array();
		//Get property element info
		$hbi = GetPropertyInfo(CATALOG_IBLOCK_ID, $elementId, 'SIZES_HDBK', $arHabdbookSizes);
		// формирую массив тек элем
		$arResult[$elementId]['ID'] = $elementId;
		$arResult[$elementId]['SIZES_HDBK'] = $hbi['SIZES_HDBK'];

		foreach($arResult as $kel=>$arElement){
			foreach($arElement['SIZES_HDBK'] as $k=>$name){// хожу по всем свойствам тек элемента
				
				// MM_recreateble
				if( substr($name, 0, 2)=='MM' ){
					$preSize = '';
					foreach( $arRes_MMs as $idSize => $size ){//хожу по сорт массиву для опред границ попадания
						
						if( (real)$size > (real)substr(str_replace(',', '.', $name), 3) ){// определение верхн границы
							if( !empty($preSize) )
								$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $preSize;
							
							if( !empty($arRes_MM[$preSize]['PROPERTY_CONFIRM_SS_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $arRes_MM[$preSize]['PROPERTY_CONFIRM_SS_VALUE'];
							
							if( !empty($arRes_MM[$preSize]['PROPERTY_CONFIRM_PP_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $arRes_MM[$preSize]['PROPERTY_CONFIRM_PP_VALUE'];
							break;
						}
						elseif( (real)$size == (real)substr(str_replace(',', '.', $name), 3) ){// точное совпадение
							if( !empty($idSize) )
								$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $idSize;
							
							if( !empty($arRes_MM[$idSize]['PROPERTY_CONFIRM_SS_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $arRes_MM[$idSize]['PROPERTY_CONFIRM_SS_VALUE'];
							
							if( !empty($arRes_MM[$idSize]['PROPERTY_CONFIRM_PP_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $arRes_MM[$idSize]['PROPERTY_CONFIRM_PP_VALUE'];
							break;
						}
						$preSize = $idSize;
					}
					
				}

				// SS_recreateble
				if( substr($name, 0, 2)=='SS' ){
					$preSize = '';
					foreach( $arRes_SSs as $idSize => $size ){

						if( (real)$size > (real)substr(str_replace(',', '.', $name), 3) ){
							if( !empty($preSize) )
								$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $preSize;
							
							if( !empty($arRes_SS[$preSize]['PROPERTY_CONFIRM_MM_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $arRes_SS[$preSize]['PROPERTY_CONFIRM_MM_VALUE'];
							
							if( !empty($arRes_SS[$preSize]['PROPERTY_CONFIRM_PP_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $arRes_SS[$preSize]['PROPERTY_CONFIRM_PP_VALUE'];
							break;
						}
						elseif( (real)$size == (real)substr(str_replace(',', '.', $name), 3) ){
							if( !empty($idSize) )
								$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $idSize;
							
							if( !empty($arRes_SS[$idSize]['PROPERTY_CONFIRM_MM_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $arRes_SS[$idSize]['PROPERTY_CONFIRM_MM_VALUE'];
							
							if( !empty($arRes_SS[$idSize]['PROPERTY_CONFIRM_PP_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $arRes_SS[$idSize]['PROPERTY_CONFIRM_PP_VALUE'];
							break;
						}
						$preSize = $idSize;
					}
				}

				// PP_recreateble
				if( substr($name, 0, 2)=='PP' ){
					$preSize = '';
					foreach( $arRes_PPs as $idSize => $size ){
						if( (real)$size > (real)substr(str_replace(',', '.', $name), 3) ){
							if( !empty($preSize) )
								$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $preSize;
							
							if( !empty($arRes_PP[$preSize]['PROPERTY_CONFIRM_MM_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $arRes_PP[$preSize]['PROPERTY_CONFIRM_MM_VALUE'];
							
							if( !empty($arRes_PP[$preSize]['PROPERTY_CONFIRM_SS_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $arRes_PP[$preSize]['PROPERTY_CONFIRM_SS_VALUE'];
							break;
						}
						elseif( (real)$size == (real)substr(str_replace(',', '.', $name), 3) ){
							if( !empty($idSize) )
								$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $idSize;
							
							if( !empty($arRes_PP[$idSize]['PROPERTY_CONFIRM_MM_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $arRes_PP[$idSize]['PROPERTY_CONFIRM_MM_VALUE'];
							
							if( !empty($arRes_PP[$idSize]['PROPERTY_CONFIRM_SS_VALUE']) )
							$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $arRes_PP[$idSize]['PROPERTY_CONFIRM_SS_VALUE'];
							break;
						}
						$preSize = $idSize;
					}
				}

			// Заполняю свойства элемента
			if( isset($arResult[$kel]['SIZES_METRICS_MM']) ){
				CIBlockElement::SetPropertyValues($kel, CATALOG_IBLOCK_ID, $arResult[$kel]['SIZES_METRICS_MM'], 'SIZES_METRICS_MM');
			}
			if( isset($arResult[$kel]['SIZES_METRICS_SS']) ){
				CIBlockElement::SetPropertyValues($kel, CATALOG_IBLOCK_ID, $arResult[$kel]['SIZES_METRICS_SS'], 'SIZES_METRICS_SS');
			}
			if( isset($arResult[$kel]['SIZES_METRICS_PP']) ){
				CIBlockElement::SetPropertyValues($kel, CATALOG_IBLOCK_ID, $arResult[$kel]['SIZES_METRICS_PP'], 'SIZES_METRICS_PP');
			}

			}//end foreach

		}
    }//end megamethod
    /**
    ** КОНЕЦ
    ** взято с кристалов тяжелые обработки
    **/

}