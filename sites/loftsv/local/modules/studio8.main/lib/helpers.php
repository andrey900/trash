<?php

namespace Studio8\Main;

class Helpers
{
	public static function _GetInfoElements($arElements, $arSelect=array(), $arFilter=array(), $counter = false, $arOrder = array()){
        if(!\CModule::IncludeModule('iblock'))
            return false;

        if( !is_array($arElements) && (int)$arElements > 0 )
            $arElements = array((int)$arElements);
        elseif( is_array($arElements) )
            $arElements = array_filter ( $arElements );

        if( empty($arElements) && empty($arFilter) )
            return false;

        if( empty($arSelect) )
            $arSelect = Array("ID", "IBLOCK_ID", "NAME", "CODE",
                "PREVIEW_PICTURE", "PREVIEW_TEXT",
                "DETAIL_PICTURE", "DETAIL_TEXT",
            );

        $_arFilter = Array("ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'ID'=>$arElements);
        if( $arElements === false )
            unset($_arFilter['ID']);
        if(is_array($arFilter) && !empty($arFilter)){
        	$_arFilter = array_merge($_arFilter, $arFilter);
        }
        if(empty($arOrder)){
        	$arOrder = array('SORT' => 'ASC', 'NAME' => 'ASC');
        }

        $res = \CIBlockElement::GetList($arOrder, $_arFilter, false, $counter, $arSelect);
        while($arTRes = $res->GetNext())
        {
            if( $arTRes['PREVIEW_PICTURE'] ){
                $arTRes['PREVIEW_PICTURE_ID'] = $arTRes['PREVIEW_PICTURE'];
                $arTRes['PREVIEW_PICTURE'] = \CFile::GetPath($arTRes['PREVIEW_PICTURE']);
            }
            if( $arTRes['DETAIL_PICTURE'] ){
                $arTRes['DETAIL_PICTURE_ID'] = $arTRes['DETAIL_PICTURE'];
                $arTRes['DETAIL_PICTURE'] = \CFile::GetPath($arTRes['DETAIL_PICTURE']);
            }
            
            $arRes[$arTRes['ID']] = $arTRes;
        }

        return $arRes;
    }

    public static function GetInfoElements($arElements, $arSelect=array(), $arFilter=array(), $counter = false, $arOrder = array()){
        $arData = array($arElements, $arSelect, $arFilter, $counter, $arOrder);
        return self::cache('_GetInfoElements', $arData);
    }

    public static function _GetTopMenu(){
        $arMenuExt = array();
        $arFilter = array('IBLOCK_ID' => IBLOCK_CATALOG_ID, 'ACTIVE' => 'Y');
        $res = \CIBlockSection::GetTreeList();
        while ($arRes = $res->Fetch()) {
            $arMenuExt[] = array(
                $arRes['NAME'], 
                "/".$arRes['CODE'].'/', 
                Array(),
                Array("FROM_IBLOCK"=>"catalog", "IS_PARENT"=>"0", "DEPTH_LEVEL"=>"2", "IMAGE" => $arRes['PREVIEW_PICTURE']), 
                "" 
            );
        }

        return $arMenuExt;
    }

    public static function GetTopMenu(){
        return self::cache('_GetTopMenu', array());
    }

    public static function _countItemInSection($arFilter){
        return \CIBlockElement::GetList(array(), $arFilter, array(), false);
    }

    public static function _getInfoBySection($id){
        if( (int)$id > 0 )
            return \CIblockSection::GetById($id)->Fetch();
        else{
            $arFilter = array(
                "IBLOCK_ID" => IBLOCK_CATALOG_ID,
                "ACTIVE" => "Y",
                "GLOBAL_ACTIVE" => "Y",
                "=CODE" => $id
            );
            return \CIBlockSection::GetList(array(), $arFilter)->Fetch();
        }
    }

    public static function getInfoBySection($id){
        return self::cache('_getInfoBySection', [$id]);
    }

    public static function cache($method, $arDataFromMethod){

        $arRes = array();

        $obCache = new \CPHPCache();
        $cache_time = 3600;
        $cache_id = md5($method).md5(serialize($arDataFromMethod));


        $className = get_class();
        if( $pos = strpos("::", $method) ){
        	$className = substr($method, 0, $pos);
        	$method = substr($method, $pos+2);
        }

        if( $obCache->InitCache($cache_time, $cache_id, '/Studio8/') )// Если кэш валиден
        {
            $vars = $obCache->GetVars();// Извлечение переменных из кэша
            if (is_array($vars["result"]) && (count($vars["result"]) > 0))
                $arRes = $vars["result"];
        }
        elseif( $obCache->StartDataCache() )// Если кэш невалиден
        {
            if(method_exists($method, $className)){
                throw new Exception("Method not exist", 1);
            }
            /*Тяжелые вычисления*/
            $arRes = call_user_func_array(array($className, $method), $arDataFromMethod);
            //$arRes = CAniartTools::$method($arDataFromMethod[0], $arDataFromMethod[1], $arDataFromMethod[2]);

            $obCache->EndDataCache(array("result"=>$arRes));// Сохраняем переменные в кэш.
        }

        return $arRes;
    }
}