<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

//CModule::IncludeModule('subscribe');
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

Class ImportCSV
{
	private $pathfile = '';
	public  $arResult = array();
	public  $handbooks= new stdClass();

	public function __construct($file)
	{
		$this->pathfile = $this->isFile($file);
	}

	/**
	 * Парсинг файла - раскрывает в ассоц массив
	 **/
	public function parseCSVFile()
	{
    	$row = 1;
	    $arHeaders = array();
	    
	    if (($handle = fopen($this->pathfile, "r")) !== FALSE) {
	        while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
	            $num = count($data);
	            if( empty($data[0]) )   // Пропускаем пустые строки
	                continue;
	            if( $row == 1 ){		// Создаем заголовочный массив
	                foreach ($data as $k=>$value) {
	                    $arHeaders[$k] = strtoupper($value);
	                }
	                //$arResult['INFO']['HANDBOOKS'] = createHandbooksFields($arHeaders);
	                $intCntFields = count($arHeaders);
	                $row++;
	                continue;
	            }
	            foreach ($data as $k=>$value) {		// Создаем массив данных
	                $this->arResult['ITEMS'][$row][$arHeaders[$k]] = $value;
	            }

	            $row++;
	        }
	        fclose($handle);
	        $this->arResult['INFO']['COUNT_FIELDS'] = $intCntFields;
	        $this->arResult = $this->clearCSVData();
	    }
	    return $this->getResult();
	}

	/**
	 * Удаляет неправильные данные(если не правильно распарсилась строчка)
	 **/
	public function clearCSVData($arRes=array(), $trigger = false)
	{
		if( empty($arRes) ){
			$arRes   = $this->arResult;
			$trigger = true;
		}

	    $intCntFields = $arRes['INFO']['COUNT_FIELDS'];
	    foreach ( $arRes['ITEMS'] as $k => $arItem ) {
	        if(count($arItem) != $intCntFields)
	            unset($arRes['ITEMS'][$k]);
	    }
	    $arRes['ITEMS'] = array_values($arRes['ITEMS']);
	    
	    if( $trigger )
	    	$this->arResult = $arRes;

	    return $arRes;
	}

	/**
	 * Создаем доп массив справочников
	 **/
	public function createHandbooksFields($arFields=array())
	{
	    $arHB = array();
	    foreach ($arFields as $key => $value) {
	        if( defined('IBLOCK_'.$value.'_ID') )
	            $arHB[$value] = array('FIELD_NAME'=>$value, 'IBLOCK_ID'=>constant('IBLOCK_'.$value.'_ID') );
	    }
	    return $arHB;
	}

	/**
	 * Возвращает внутренний массив
	 **/
	public function getResult()
	{
		return $this->arResult;
	}

	/**
	 * Проверка на наличие файла
	 **/
	public function isFile($file)
	{
		if( is_file($_SERVER['DOCUMENT_ROOT'].$file) )
			return $_SERVER['DOCUMENT_ROOT'].$file;
		else
			throw new Exception("No find file", 1);
	}
}