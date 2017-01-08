<?php
class CAniartTools
{
	public static function Translit($String)
	{
		$arParams = array("replace_space" => "-", "replace_other" => "-");
		return CUtil::Translit($String, "ru", $arParams);
	}

	public static function full_trim($str){
	    return trim(preg_replace('/\s{2,}/', ' ', $str));
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

	public static function base64_encode_image($filename, $filetype){
	    if ($filename) {
	        $imgbinary = fread(fopen($filename, "r"), filesize($filename));
	        return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
	    }
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