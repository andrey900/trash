<?
class CAniartTools
{
	public static function Translit($String)
	{
		$arParams = array("replace_space" => "-", "replace_other" => "-");
		return CUtil::Translit($String, "ru", $arParams);
	}
	
	public static function IsRussian($text){
    	return preg_match('/[А-Яа-яЁё]/u', $text);
	}
	
	//строит дерево
	public static function BuildTree($arItems, $ParentID = 'PARENT_ID', $ChildID = 'ID')
	{
		$Childs= array();
		if(!is_array($arItems) || empty($arItems)){
			return array();
		}
		foreach($arItems as &$Item){
			if(!$Item[$ParentID]){
				$Item[$ParentID] = 0;
			}
			$Childs[$Item[$ParentID]][] = &$Item;
		}
		unset($Item);
		foreach($arItems as &$Item){
			if (isset($Childs[$Item[$ChildID]])){
				$Item['CHILDS'] = $Childs[$Item[$ChildID]];
			}
		}
		return $Childs[0];
	}
	
	//Обрезание текста по границе слова
	public static function SmartTruncate($string, $length = 80, $etc = '...', $charset='UTF-8', $break_words = false, $middle = false) 
	{
		if ($length == 0) return '';
		if (strlen($string) > $length) {
			$length -= min($length, strlen($etc));
			if (!$break_words && !$middle) {
				$string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1, $charset));
			}
			if(!$middle) {
				return mb_substr($string, 0, $length, $charset) . $etc;
			}
			else {
				return mb_substr($string, 0, $length/2, $charset) .
						$etc .
						mb_substr($string, -$length/2, $charset);
	        }
		}
		else {
        	return $string;
    	}
	}
	
	//Вставка значения внутрь массива
	public static function InsertIntoArray($InputArray, $position, $value)
	{
		$OutputArray = array();
		if(is_array($InputArray)){
			if(!is_array($value)){
				$value = array($value);
			}
			if(is_string($position)){
				$position = array_search($position, array_keys($InputArray)) + 1;
				if($position === false){
					$position = count($InputArray) - 1;
				}
			}
			$OutputArray = array_merge(array_slice($InputArray, 0, $position, true), $value, array_slice($InputArray, $position, null, true));
		}
		return $OutputArray;
	}
	
	/*
	* Функция склонения числительных в рус. языке
	*
	* @param int    $number Число которое нужно просклонять
	* @param array  $titles Массив слов для склонения
	* @return string
	*/
	public static function DeclOfNum($number, $titles)
	{
		$cases = array (2, 0, 1, 1, 1, 2);
		return $number." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
	}
	
	//функция для добавления в куки последнего просмотренного предмета
	public static function SetViewedItem($CategoryName, $itemID)
	{
		global $APPLICATION;
		if((int)$itemID == 0) return false;
		if(empty($CategoryName)) $CategoryName = "VIEWED_ITEMS";
	
		$arVI = array($itemID);
	
		$ViewedItems = $APPLICATION->get_cookie($CategoryName);
		if(!empty($ViewedItems))
		{
			$arVI	= explode(",", $ViewedItems);
			$key	= array_search($itemID, $arVI);
				
			if($key!==false) unset($arVI[$key]);
			array_unshift($arVI,$itemID);
		}
	
		$APPLICATION->set_cookie($CategoryName, implode(",", $arVI), time()+3600*2);
		return true;
	}
	
	//функция для получения идентификаторов ранее просмотренных товаров
	public static function GetViewedItems($CategoryName)
	{
		global $APPLICATION;
		$return = $APPLICATION->get_cookie($CategoryName);
		if(!empty($return))
			$return = explode(",",$return);
		return $return;
	}
	
	//создает og метатеги на основании входного массива
	public static function CreateOGMetaTagsBlock($arParams)
	{
		$Str = '';
		if(!is_array($arParams) || empty($arParams)){
			return '';
		}
		foreach($arParams as $Property => $Content){
			if(is_array($Content) && !empty($Content)){
				foreach($Content as $Value){
					$Str.='<meta property="og:'.$Property.'" content="'.$Value.'" />';
				}
			}
			else{
				$Str.='<meta property="og:'.$Property.'" content="'.$Content.'" />'."\r\n";
			}
		}
	
		return $Str;
	}
	
	//создает специальные блоки для Google Analytics для ведения заказов
	public static function CreateGAOrderBlock($OrderData, $OrderItemsData)
	{
		$Result = '';
		if(
			(!is_array($OrderData) || empty($OrderData)) ||
			(!is_array($OrderItemsData) || empty($OrderItemsData))
		){
			return false;
		}
		
		$Result.="_gaq.push(['_addTrans',
			'".$OrderData['ID']."',
			'',
			'".$OrderData['SUMM']."',
			'',
			'',
			'',
			'',
			'',
		]);\n";
		
		foreach($OrderItemsData as $ItemData){
			$Result.="_gaq.push(['_addItem',
				'".$OrderData['ID']."',
				'".$ItemData['ID']."',
				'".$ItemData['NAME']."',
				'".$ItemData['CATEGORY']."',
				'".$ItemData['PRICE']."',
				'".$ItemData['QUANTITY']."'
			]);\n";
		}
		
		$Result.="_gaq.push(['_trackTrans']);";
		
		return $Result;
	}

	public static function _GetInfoElements($arElements, $arSelect=array(), $arFilter=array()){
		if(!CModule::IncludeModule('iblock'))
			return false;

		if( !is_array($arElements) )
			$arElements = array((int)$arElements);
		else
			$arElements = array_filter ( $arElements );

		if( empty($arElements) && empty($arFilter) )
			return false;

		if( empty($arSelect) )
			$arSelect = Array("ID", "NAME", "CODE", 
							  "PREVIEW_PICTURE", "PREVIEW_TEXT", 
							  "DETAIL_PICTURE", "DETAIL_TEXT",
						);

		if( empty($arFilter) )
			$arFilter = Array("ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'ID'=>$arElements);

		$res = CIBlockElement::GetList(Array('SORT'=>'ASC', 'NAME'=>'ASC'), $arFilter, false, false, $arSelect);
		while($arTRes = $res->GetNext())
		{
			$arRes[$arTRes['ID']] = $arTRes;
		}

		return $arRes;
	}

	public static function _GetPriceElements($arElements){
		if( !is_array($arElements) )
			$arElements = array((int)$arElements);
		
		$arPriceElements = array();

		foreach ($arElements as $element) {
			$e = (int)$element;
			if( $e > 0)
				$arPriceElements[$e] = CPrice::GetBasePrice($e);
		}

		return $arPriceElements;
	}

	public static function GetBrandsInSection($sectionId){
		$arSelect = array('ID', 'PROPERTY_VENDORS');
		$arFilter = array('IBLOCK_ID'=>IBLOCK_CATALOG, "SECTION_ACTIVE"=>"Y", "SECTION_ID"=>(int)$sectionId, 'INCLUDE_SUBSECTIONS'=>'Y', 'ACTIVE'=>'Y', "!PROPERTY_VENDORS"=>false);
		$arRes = self::GetInfoElements(false, $arSelect, $arFilter);
		
		$arElems = array();

		foreach ($arRes as $value) {
			$arElems[$value['PROPERTY_VENDORS_VALUE']] = $value['PROPERTY_VENDORS_VALUE'];
		}
		
		return self::GetInfoElements($arElems);
	}

	public static function _GetFirstSection($id){
		$arRes = CIBlockSection::GetById($id)->GetNext();
		if( !empty($arRes['IBLOCK_SECTION_ID']) )
			return self::_GetFirstSection($arRes['IBLOCK_SECTION_ID']);
		else
			return $arRes['ID'];
	}

	public static function GetPriceElements($arElements){
		$arData = array($arElements);
		return CAniartTools::cache('_GetPriceElements', $arData);
	}

	public static function GetInfoElements($arElements, $arSelect=array(), $arFilter=array()){
		$arData = array($arElements, $arSelect, $arFilter);
		return CAniartTools::cache('_GetInfoElements', $arData);
	}

	public static function full_trim($str){
	    return trim(preg_replace('/\s{2,}/', ' ', $str));
	}

	//выборка свойств и их группировка
    public static function _groupInfoProp($propName, $arFilter=array()){
        if(!CModule::IncludeModule('iblock'))
            return false;

        if( empty($propName) || !is_array($arFilter))
            return false;

        $_arFilter = Array("ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y");
        $_arFilter = array_merge($_arFilter, $arFilter);

        $res = CIBlockElement::GetList(Array($propName=>'ASC'), $_arFilter, array($propName));
        while($arTRes = $res->GetNext())
        {
            $arRes[$arTRes[$propName.'_VALUE']] = $arTRes;
            $arRes[$arTRes[$propName.'_VALUE']]['ID'] = $arTRes[$propName.'_VALUE'];
        }

        return $arRes;
    }

	// кешируемые результаты выбора свойств элемента и выбор названий этих свойст
    public static function GetInfoProp($propName, $arFilter=array())
    {
        $arData = array($propName, $arFilter);
        $arRes = CAniartTools::cache('_groupInfoProp', $arData);
        $arIds = array();
        foreach($arRes as $ID=>$v){
            $arIds[$ID] = $ID;
        }
        return CAniartTools::GetInfoElements($arIds);
    }

	public static function base64_encode_image($filename, $filetype){
	    if ($filename) {
	        $imgbinary = fread(fopen($filename, "r"), filesize($filename));
	        return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
	    }
	    //echo CAniartTools::base64_encode_image($_SERVER['DOCUMENT_ROOT'].'/logo.png','png')
	}

	/*!
	* @brief функция производит поиск в многомерном массиве по паре ключ=>значение
	* и возвращает результат в виде массива.
	* 
	* @param array $array -- входящий массив
	* @param any $key -- ключ
	* @param any $value -- значение
	* @return array -- массив значений, удовлетворяющих условиям поиска
	* @author sunelbe at gmail dot com
	*/
	public static function search($array, $key, $value)
	{
		$results = array();

		if (is_array($array))
		{
			if (isset($array[$key]) && $array[$key] == $value)
				$results[] = $array;

			foreach ($array as $subarray)
				$results = array_merge($results, search($subarray, $key, $value));
		}

		return $results;
	}


	/**
	 * Функция транслитерирует строку
	 *
	 * @param string $string
	 * @return string
	 */
	public static function ConvTranslit($string, $alternative = false) {
		$replace = array(
				"'"=>"",
				"`"=>"",
				"а"=>"a","А"=>"A",
				"б"=>"b","Б"=>"B",
				"в"=>"v","В"=>"V",
				"г"=>"g","Г"=>"G",
				"д"=>"d","Д"=>"D",
				"е"=>"e","Е"=>"E",
				"ж"=>"zh","Ж"=>"Zh",
				"з"=>"z","З"=>"Z",
				"и"=>"i","И"=>"I",
				"й"=>"y","Й"=>"Y",
				"к"=>"k","К"=>"K",
				"л"=>"l","Л"=>"L",
				"м"=>"m","М"=>"M",
				"н"=>"n","Н"=>"N",
				"о"=>"o","О"=>"O",
				"п"=>"p","П"=>"P",
				"р"=>"r","Р"=>"R",
				"с"=>"s","С"=>"S",
				"т"=>"t","Т"=>"T",
				"у"=>"u","У"=>"U",
				"ф"=>"f","Ф"=>"f",
				"х"=>"h","Х"=>"H",
				"ц"=>"c","Ц"=>"C",
				"ч"=>"ch","Ч"=>"Ch",
				"ш"=>"sh","Ш"=>"Sh",
				"щ"=>"sch","Щ"=>"Sch",
				"ъ"=>"","Ъ"=>"",
				"ы"=>"y","Ы"=>"Y",
				"ь"=>"","Ь"=>"",
				"э"=>"e","Э"=>"E",
				"ю"=>"yu","Ю"=>"Yu",
				"я"=>"ya","Я"=>"Ya",
				"і"=>"i","І"=>"I",
				"ї"=>"yi","Ї"=>"Yi",
				"є"=>"e","Є"=>"E",
				"ё"=>"e","Ё"=>"E"
		);
		
		// лёгкий изврат для того, чтобы рещить частично пробелму с дубликатами символьных кодов
		if ($alternative) {
			$replace = array_merge($replace, array("й"=>"j", "ы"=>"j", "я"=>"ja", "ю"=>"ju", "ы"=>"i", "о"=>"0"));
		}
		
		return $str = strtr($string, $replace);
	}

	/**
	 * Функция траслитерирует строку, заменяя пробелы на символы подчёркивания
	 * и преобразуя символы к нижнему регистру
	 *
	 * @param string $str
	 * @return string
	 */
	public static function translitStr($str) {
		$params = Array(
				"max_len" => "100", 								// обрезает символьный код до 100 символов
				"change_case" => "L", 							// буквы преобразуются к нижнему регистру
				"replace_space" => "_", 						// меняем пробелы на нижнее подчеркивание
				"replace_other" => "_", 						// меняем левые символы на нижнее подчеркивание
				"delete_repeat_replace" => "true", 	// удаляем повторяющиеся нижние подчеркивания
		);

		//return CUtil::translit($str, "ru", $params);
	}

	public static function cache($method, $arDataFromMethod){

		$arRes = array();

		$obCache = new CPHPCache();
		$cache_time = 3600;
		$cache_id = md5($method).md5(serialize($arDataFromMethod));

		if( $obCache->InitCache($cache_time, $cache_id, '/CAniartTools/') )// Если кэш валиден
		{
		   $vars = $obCache->GetVars();// Извлечение переменных из кэша
	   	   if (is_array($vars["result"]) && (count($vars["result"]) > 0))
	      		$arRes = $vars["result"];
		}
		elseif( $obCache->StartDataCache() || empty($arRes) )// Если кэш невалиден
		{
			if(method_exists($method, 'CAniartTools')){
				return "Method not exist";
			}

		   /*Тяжелые вычисления*/
		   $arRes = CAniartTools::$method($arDataFromMethod[0], $arDataFromMethod[1], $arDataFromMethod[2]);

		   $obCache->EndDataCache(array("result"=>$arRes));// Сохраняем переменные в кэш.
		}

		return $arRes;
	}
	
} 
?>