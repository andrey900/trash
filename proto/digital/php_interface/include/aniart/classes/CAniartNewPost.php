<?php

/**
 * 
 * static какашка класс(переписать!)
 * предназначен для работы с API перевозчика - Новая почта
 * 
 */
class CAniartNewPost {
    
    /**
     * Подключение к API Новой почты
     * Парсинг входящего XML
     * Подключение кэша для хранения массива данных
     * Вывод массива городов, где есть отделения НП
     * 
     * @return boolean
     */
    public static function getParseCity() {
        
        $obCache = new CPHPCache;
        $lifeTime = 86400 * 7;
        $cacheID = "new_post_city";
        $cache_path = '/new_post_city/';
        
        if($obCache->InitCache($lifeTime, $cacheID, $cache_path)) {
            $tmp = $obCache->GetVars();
            $arResult = $tmp[$cacheID];
            return $tmp;
        } else {
            $obCache->StartDataCache();
            
            $keyNp = '82b7e8c0ee63d0e606a2d6f330a7895a';
            
            $xmlWarenhouse = '
                <?xml version="1.0" encoding="utf-8"?>
                <file>
                    <auth>'.$keyNp.'</auth>
                    <citywarehouses/>
                </file>';
        
            require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/xml.php');
            
            $objXML = new CDataXML();
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://orders.novaposhta.ua/xml.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlWarenhouse);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            
            if($objXML->LoadString($response)) {
                $arData = $objXML->GetArray();
                $arResult = array();
                foreach ($arData['response']['#']['result'][0]['#']["cities"][0]['#']["city"] as $arValue){
                    $ar = array();
                    foreach ($arValue['#'] as $sKey => $sVal) {
                        $ar[$sKey] = $sVal[0]['#'];
                    }
                    $arResult[] = $ar;
                }
            } else {
                return false;
            }
            $obCache->EndDataCache(array($cacheID => $arResult));
        }
        return $arResult;
    }
    
    /**
     * Подключение к API Новой почты
     * Парсинг входящего XML
     * Подключение кэша для хранения массива данных
     * Выборка по входящему параметру
     * 
     * @return string|boolean
     */
    public static function getParseWarehouse(){
        
        //подключаем кеш
        $obCache = new CPHPCache;
        $lifeTime = 86400 * 7;
        $cacheID = "new_post_warehouse";
        $cache_path = '/new_post_warehouse/';
        
        if($obCache->InitCache($lifeTime, $cacheID, $cache_path)){
            $tmp = $obCache->GetVars();
            $arResult = $tmp[$cacheID];
        }
        else{
            $keyNp = '59893ee0611c03b79652f2c08d328330';
            
            $xmlWarenhouse = '
                <?xml version="1.0" encoding="utf-8"?>
                <file>
                    <auth>'.$keyNp.'</auth>
                    <warenhouse/>
                </file>';
        
            require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/xml.php');
            
            $objXML = new CDataXML();
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://orders.novaposhta.ua/xml.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlWarenhouse);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            
            if($objXML->LoadString($response)){
                
                $arData = $objXML->GetArray();
                
                $arResult = array();

                foreach ($arData['response']['#']['result'][0]['#']["whs"][0]['#']["warenhouse"] as $arValue){
                    $ar = array();
                        
                    foreach ($arValue['#'] as $sKey => $sVal){
                        $ar[$sKey] = $sVal[0]['#'];
                    }
                    
                    $arResult[] = $ar;
                }
            }
            else{
                return false;
            }
            $obCache->StartDataCache();
            $obCache->EndDataCache(array($cacheID => $arResult));
        }
        
        return $arResult;
    }
    
    /**
     * Получает список складов по id города
     * 
     * @param int $city - название города
     * @return type
     */
    public static function getWarehouseId($city) {
        $arResult = array();
        if(!empty($city)) {
            foreach (self::getParseWarehouse() as $item) {
                if($item['city_id'] == $city) {
                    $arResult[] = $item;
                }
            }
        }
        return $arResult;
    }
    
    /**
     * Получает список складов по названию города
     * 
     * @param string $city - название города
     * @return type
     */
    public static function getWarehouseName($city) {
        $arResult = array();
        if(!empty($city)) {
            foreach (self::getParseWarehouse() as $item) {
                if($item['cityRu'] == $city) {
                    $arResult[] = $item;
                }
            }
        }
        return $arResult;
    }
}

