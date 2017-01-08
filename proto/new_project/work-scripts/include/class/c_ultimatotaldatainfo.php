<?php

class UltimaTotalDataInfo extends FastSoapClientFromUltima {

	// Необходимо указать узел WSDL
    public $wsdl = "http://89.249.18.174:50080/bitrix/OtherWebService.asmx?WSDL";
    public $arGoodsId = array();
    private $newArResult = array();

    public function __construct()
    {
        parent::__construct($this->wsdl);
        // Настройки данного класса
        $this->config->warehouseIds = array(1);    // номер склада
        $this->config->priceCatIds  = array(195);  // номер ценовой категории
        $this->config->nameGoodsId  = 'GoodId';	   // название поля для определения ИД товара
        $this->config->countRequest = 900;		   // количество элементов в пакете(<1000)
        $this->config->scriptUpdateTime = 3*60*60; // интервал с которым вызывается скрипт
    }

    public function GetAllGoods()
    {
        parent::FGetAllGoodsPrices(); 			 // получаем все данные
        $this->VariationResultArray(); 			 // преобразовываем массив
        $this->newArResult = $this->arResult; // создаем начальный новый массив
        $arIdGoods   = $this->arGoodsId; 		 // сохраняем все полученые ИД
        // формируем пакеты запросов по "countRequest" штук
        for( $i=0; $i<ceil(count($arIdGoods)/$this->config->countRequest); $i++ ){
            $arIdPack = array_slice($arIdGoods, $i*$this->config->countRequest, $this->config->countRequest);
            parent::FGetGoodsPricesFromId($arIdPack); 			// получаем цены для товаров данного пакета
            $this->VariationResultArray()->createNewArResult(); // добавляем в выходной массив
            parent::FGetGoodsAndSizesFromId($arIdPack); 		// получаем размеры для товаров данного пакета
            $this->VariationResultArray()->createNewArResult(); // добавляем в выходной массив
        }
        return $this->newArResult;
    }

    public function GetLastModRemains()
    {
    	parent::FGetLastGoodsRemains(date('Y-m-d H:i:s', time()-$this->config->scriptUpdateTime));
        $this->VariationResultArray();
        $this->newArResult = $this->arResult;
        $arIdGoods   = $this->arGoodsId;
        for( $i=0; $i<ceil(count($arIdGoods)/$this->config->countRequest); $i++ ){
            $arIdPack = array_slice($arIdGoods, $i*$this->config->countRequest, $this->config->countRequest);
            parent::FGetGoodsPricesFromId($arIdPack);
            $this->VariationResultArray()->createNewArResult();
            parent::FGetGoodsAndSizesFromId($arIdPack);
            $this->VariationResultArray()->createNewArResult();
        }
        return $this->newArResult;
    }

    public function GetLastModPrice()
    {
    	parent::FGetLastGoodsPrices(date('Y-m-d H:i:s', time()-$this->config->scriptUpdateTime));
        $this->VariationResultArray();
        $this->newArResult = $this->arResult;
        $arIdGoods   = $this->arGoodsId;
        for( $i=0; $i<ceil(count($arIdGoods)/$this->config->countRequest); $i++ ){
            $arIdPack = array_slice($arIdGoods, $i*$this->config->countRequest, $this->config->countRequest);
            parent::FGetGoodsRemainsFromId($arIdPack);
            $this->VariationResultArray()->createNewArResult();
            parent::FGetGoodsAndSizesFromId($arIdPack);
            $this->VariationResultArray()->createNewArResult();
        }
        return $this->newArResult;
    }

    public function GetLastGoodsAndSizes()
    {
        parent::FGetLastGoodsAndSizes(date('Y-m-d H:i:s', time()-$this->config->scriptUpdateTime));
        $this->VariationResultArray();
        $this->newArResult = $this->arResult;
        $arIdGoods   = $this->arGoodsId;
        for( $i=0; $i<ceil(count($arIdGoods)/$this->config->countRequest); $i++ ){
            $arIdPack = array_slice($arIdGoods, $i*$this->config->countRequest, $this->config->countRequest);
            parent::FGetGoodsRemainsFromId($arIdPack);
            $this->VariationResultArray()->createNewArResult();
            parent::FGetGoodsPricesFromId($arIdPack);
            $this->VariationResultArray()->createNewArResult();
        }
        return $this->newArResult;
    }

    public function GetLastModStatus()
    {
        parent::FGetLastGoodsActiveStatus(date('Y-m-d H:i:s', time()-$this->config->scriptUpdateTime));
        $this->VariationResultArray();
        $this->newArResult = $this->arResult;
        
        return $this->newArResult;
    }

    public function GetAllGoodsSizesFromId($arExtId = array()){
        $arNLastMod = array();
        $arIdGoods = $arExtId;
        for( $i=0; $i<ceil(count($arIdGoods)/900); $i++ ){
            $arIdPack = array_slice($arIdGoods, $i*900, 900);
            parent::FGetGoodsAndSizesFromId($arIdPack);
            $this->VariationResultArray();
            $arNLastMod = $arNLastMod + $this->arResult;
        }       
        return $arNLastMod;
    }
    
    public function GetAllGoodsPricesFromId($arExtId = array()){
    	$arNLastMod = array();
    	$arIdGoods = $arExtId;
    	for( $i=0; $i<ceil(count($arIdGoods)/900); $i++ ){
    		$arIdPack = array_slice($arIdGoods, $i*900, 900);
    		parent::FGetGoodsPricesFromId($arIdPack);
    		$this->VariationResultArray();
    		$arNLastMod = $arNLastMod + $this->arResult;
    	}
    	return $arNLastMod;
    }

    protected function VariationResultArray()
    {
        if( !empty($this->arResult[1]) && is_array($this->arResult[1])){
            $arRes = $this->arResult; 
            $this->arResult = array();
            $this->arGoodsId = array();
            foreach ($arRes[1] as $k => $arItem) {
                foreach ($arItem as $key => $Item) {
                    if( $arRes[0][$key]==$this->config->nameGoodsId && !empty($Item) )
                        $this->arGoodsId[$k] = $Item;
                    $this->arResult[$k][$arRes[0][$key]] = $Item;
                }
            }
            $arRes = $this->arResult; $this->arResult = array();
            foreach ($this->arGoodsId as $key => $value) {
                $this->arResult[$value] = $arRes[$key];
            }
        } else {
            $this->arResult = array();
        }
        return $this;
    }

    static public function EVariationResultArray($arResult=array(), $nameGoodsId)
    {
        if( empty($arResult) )
            $arRes = $this->arResult[1];
        else
            $arRes = $arResult;

        if( !empty($arResult[1]) && is_array($arResult[1])){
            $arResult = array();
            $arGoodsId = array();
            foreach ($arRes[1] as $k => $arItem) {
                foreach ($arItem as $key => $Item) {
                    if( $arRes[0][$key]==$nameGoodsId && !empty($Item) )
                        $arGoodsId[$k] = $Item;
                    $arResult[$k][$arRes[0][$key]] = $Item;
                }
            }
            $arRes = $arResult; $arResult = array();
            foreach ($arGoodsId as $key => $value) {
                $arResult[$value] = $arRes[$key];
            }
        }
        else
            return array();

        return $arResult;
    }

    protected function createNewArResult($triger = false)
    {
    	foreach ($this->arResult as $key => $value) {
            if( is_array($value) && !empty($value) && is_array($this->newArResult[$key]) )
               $this->newArResult[$key] = array_merge($this->newArResult[$key], $value);
        }
        return $this;
    }
}