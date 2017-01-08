<?php

/**
 * Основной класс с методами для работы с ультимой(аналог 1С-предприятия)
 * SSR
 **/
class SoapClientFromUltima {
    
    public $arResult = array();		// Выходные данные будут сваливаться сюда
	public $deltaTime = 3600;		// Время разницы для получения посл данных 3600=1ч
    
    function __construct($wsdl, $options=array())
    {
        $this->client  = new SoapClientPort($wsdl, $options);	// Создание подключения
        $this->config  = new stdClass();						// Конфигуратор, тут будут храниться настройки
        $this->query   = new stdClass();						// Внутреннее свойство для формирования запросов к ультиме
        $this->config->warehouseIds = array(1);					// Явное указание номера склада - необходимо переопределить на нужное значение 
        $this->config->priceCatIds  = array(195);				// Явное указание номера типа цен - необходимо переопределить на нужное значение
        $this->_clearQueryParam();
        $this->config->returnParams  = false;					// Для методов класса SoapClientFromUltima возвращать только параметры, и не выполнять функцию
    }

    /**
     * Получение информации о остатках товаров из ультимы по всем товарам
     * @return = array
     */
    public function GetAllGoodsRemains()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';//date('Y-m-d H:i:s')
        if( $this->config->returnParams )
        	return $this;
        
        $this->_GetGoodsRemains();
        return $this->arResult;
    }

    /**
     * Получение информации о остатках товаров из ультимы по дате изменения
     * @var = int | date
     * @return = array
     */
    public function GetLastGoodsRemains($date='')
    {
    	$date = $this->checkDate($date);
        $this->query->arGoodIds = array();
        $this->query->date = ($date)?$date:$this->checkDate($this->deltaTime);
        if( $this->config->returnParams )
        	return $this;
        
        $this->_GetGoodsRemains();
        return $this->arResult;
    }

    /**
     * Получение информации о остатках товаров из ультимы по ID
     * @var = int | array
     * @return = array
     */
    public function GetGoodsRemainsFromId($id=array(0))
    {
    	$this->query->arGoodIds = (is_array($id))?$id:array(0);
    	$this->query->date = '';
    	if( $this->config->returnParams )
    		return $this;
    	
    	$this->_GetGoodsRemains();
    	return $this->arResult;
    }
    
    /**
     * Получение информации о ценах товаров из ультимы по всем товарам
     * @return = array
     */
    public function GetAllGoodsPrices()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';
        if( $this->config->returnParams )
        	return $this;
        
        $this->_GetGoodsPrices();
        return $this->arResult;
    }

    /**
     * Получение информации о ценах товаров из ультимы по дате изменения
     * @var = int | date
     * @return = array
     */
    public function GetLastGoodsPrices($date='')
    {
    	$date = $this->checkDate($date);
        $this->query->arGoodIds = array();
        $this->query->date = ($date)?$date:$this->checkDate($this->deltaTime);
        if( $this->config->returnParams )
        	return $this;
        
        $this->_GetGoodsPrices();
        return $this->arResult;
    }

    /**
     * Получение информации о ценах товаров из ультимы по ID
     * @var = int | array
     * @return = array
     */
    public function GetGoodsPricesFromId($id=array(0))
    {
        $this->query->arGoodIds = (is_array($id))?$id:array(0);
        $this->query->date = '';
        if( $this->config->returnParams )
        	return $this;
        
        $this->_GetGoodsPrices();
        return $this->arResult;
    }

    /**
     * Получение информации о размерах товаров из ультимы по всем товарам
     * @return = array
     */
    public function GetAllGoodsAndSizes()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';
        if( $this->config->returnParams )
        	return $this;
        
        $this->_GetGoodsAndSizes();
        return $this->arResult;
    }

    /**
     * Получение информации о размерах товаров из ультимы по дате изменения
     * @var = int | date
     * @return = array
     */
    public function GetLastGoodsAndSizes($date='')
    {
    	$date = $this->checkDate($date);
        $this->query->arGoodIds = array();
        $this->query->date = ($date)?$date:$this->checkDate($this->deltaTime);
        if( $this->config->returnParams )
        	return $this;
        
        $this->_GetGoodsAndSizes();
        return $this->arResult;
    }
    
    /**
     * Получение информации о размерах товаров из ультимы по ID
     * @var = int | array
     * @return = array
     */
    public function GetGoodsAndSizesFromId($id=array(0))
    {
        $this->query->arGoodIds = (is_array($id))?$id:array(0);
        $this->query->date = '';
        if( $this->config->returnParams )
        	return $this;
        
        $this->_GetGoodsAndSizes();
        return $this->arResult;
    }

    /**
     * _ Основной метод получения информации о остатках
     * GetGoodsRemains - внутр метод ультимы, и параметры для него
     * @return = array
     */
    private function _GetGoodsRemains()
    {
        $this->arResult = $this->client->GetGoodsRemains($this->query->arGoodIds, 
                                                         $this->query->arWareHouseIds, 
                                                         $this->query->date);
        return $this->_clearQueryParam();
    }

    /**
     * _ Основной метод получения информации о ценах
     * GetGoodsPrices - внутр метод ультимы, и параметры для него 
     * @return = array
     */
    private function _GetGoodsPrices(){
        $this->arResult = $this->client->GetGoodsPrices($this->query->arGoodIds, 
                                                         $this->query->priceCatIds, 
                                                         $this->query->date);
        return $this->_clearQueryParam();
    }

    /**
     * _ Основной метод получения информации о размерах
	 * GetGoodsAndSizes - внутр метод ультимы, и параметры для него
     * @return = array
     */
    private function _GetGoodsAndSizes(){
        $this->arResult = $this->client->GetGoodsAndSizes($this->query->arGoodIds, 
                                                         $this->query->date);
        return $this->_clearQueryParam();
    }

    /**
     * Форматирование даты согласно формату который необходим для правильной работы ультимы
	 * @var = int | date
     * @return = array
     */
    private function checkDate($date=false)
    {
    	$date = trim($date);
    	$df   = 'Y-m-d H:i:s';
    	if( !$date )
    		return false;
    	elseif( preg_match("/^[\d\+]+$/", $date) ){
    		return date($df, time()-$date);
    	}else
    		return date($df, strtotime($date));
    	
    }
    
    // Формирование минимально необходимых параметров для ультимы, или сброс по умолчанию(выполняеться после каждого получения данных)
    protected function _clearQueryParam()
    {
        $this->query->arGoodIds      = array(0);
        $this->query->arWareHouseIds = $this->config->warehouseIds;
        $this->query->priceCatIds    = $this->config->priceCatIds;
        $this->query->date           = $this->checkDate($this->deltaTime);
        $this->config->returnParams  = false;
        return $this;
    }
}