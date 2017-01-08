<?
class CAniartTools
{

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

	public static function GetInfoElements($arElements, $arSelect=array(), $arFilter=array()){
		$arData = array($arElements, $arSelect, $arFilter);
		return CAniartTools::cache('_GetInfoElements', $arData);
	}

	public static function createDataFromRefferers()
	{
		$arRes = self::_GetInfoElements(false, array('ID', 'NAME', 'PROPERTY_INDEX', 'PROPERTY_UTM_POINT', 'PROPERTY_HTTP_REFERER', 'TIMESTAMP_X_UNIX', 'PROPERTY_COOKIELIFE' ), array('ACTIVE'=>'Y', 'IBLOCK_ID'=>SOURCE_TRAFIC_IBLOCK) );
		
		$std = new stdClass();
		
		foreach( $arRes as $arElem ){
			
			$std->$arElem['ID']->id 		= $arElem['ID'];
			$std->$arElem['ID']->utm 		= $arElem['PROPERTY_UTM_POINT_VALUE'];
			$std->$arElem['ID']->name 		= $arElem['NAME'];
			$std->$arElem['ID']->preg 		= $arElem['PROPERTY_HTTP_REFERER_VALUE'];
			$std->$arElem['ID']->dateMod    = $arElem['TIMESTAMP_X_UNIX'];
			$std->$arElem['ID']->cookieLife = $arElem['PROPERTY_COOKIELIFE_VALUE'];
			$std->$arElem['ID']->prefix 	= ($arElem['PROPERTY_INDEX_VALUE'])?$arElem['PROPERTY_INDEX_VALUE']:$arElem['ID'];
			
		}

		return json_encode($std);
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