<?php

/**
 * Ускоренный класс расширяющий основной класс с методами
 * для работы с ультимой(аналог 1С-предприятия)
 * SSR
 **/
class FastSoapClientFromUltima extends SoapClientFromUltima {

    public $arResult = array();
    
    function __construct($wsdl, $options=array())
    {
        parent::__construct($wsdl, $options=array());
        parent::_clearQueryParam();
    }

    /**
     * Описывать нет смысла, все тоже самое что и в классе SoapClientFromUltima
     * только используя другие внутренние методы ультимы 
     */
    public function FGetAllGoodsRemains()
    {
        $this->config->returnParams  = true;
        parent::GetAllGoodsRemains()->_GetGoodsRemainsCSV();
        return $this->arResult;
    }

    public function FGetLastGoodsRemains($date='')
    {
        $this->config->returnParams  = true;
        parent::GetLastGoodsRemains($date)->_GetGoodsRemainsCSV();
        return $this->arResult;
    }

    public function FGetGoodsRemainsFromId($id=array(0))
    {
        $this->config->returnParams  = true;
        parent::GetGoodsRemainsFromId($id);
        $this->_GetGoodsRemainsCSV();
        return $this->arResult;
    }

    public function FGetAllGoodsPrices()
    {
        $this->config->returnParams  = true;
        parent::GetAllGoodsPrices()->_GetGoodsPricesCSV();
        return $this->arResult;
    }

    public function FGetLastGoodsPrices($date='')
    {
        $this->config->returnParams  = true;
        parent::GetLastGoodsPrices($date)->_GetGoodsPricesCSV();
        return $this->arResult;
    }

    public function FGetGoodsPricesFromId($id=array(0))
    {
        $this->config->returnParams  = true;
        parent::GetGoodsPricesFromId($id)->_GetGoodsPricesCSV();
        return $this->arResult;
    }

    public function FGetAllGoodsAndSizes()
    {
        $this->config->returnParams  = true;
        parent::GetAllGoodsAndSizes()->_GetGoodsAndSizesCSV();
        return $this->arResult;
    }

    public function FGetLastGoodsAndSizes($date='')
    {
        $this->config->returnParams  = true;
        parent::GetLastGoodsAndSizes($date)->_GetGoodsAndSizesCSV();
        return $this->arResult;
    }

    public function FGetGoodsAndSizesFromId($id=array(0))
    {
    	$this->config->returnParams  = true;
    	parent::GetGoodsAndSizesFromId($id)->_GetGoodsAndSizesCSV();
        return $this->arResult;
    }

    /**
     * _ Основной метод получения информации о остатках
     * GetGoodsRemainsCSV - внутр метод ультимы, и параметры для него
     * @return = array
     */
    private function _GetGoodsRemainsCSV()
    {
        $this->arResult = $this->client->GetGoodsRemainsCSV($this->query->arGoodIds, 
                                                            $this->query->arWareHouseIds, 
                                                            $this->query->date);
        return $this->_FUncover()->_clearQueryParam();
    }

    /**
     * _ Основной метод получения информации о ценах
     * GetGoodsPricesCSV - внутр метод ультимы, и параметры для него
     * @return = array
     */
    private function _GetGoodsPricesCSV(){
        $this->arResult = $this->client->GetGoodsPricesCSV($this->query->arGoodIds, 
                                                         $this->query->priceCatIds, 
                                                         $this->query->date);
        return $this->_FUncover()->_clearQueryParam();
    }

    /**
     * _ Основной метод получения информации о размерах
     * GetGoodsAndSizesCSV - внутр метод ультимы, и параметры для него
     * @return = array
     */
    private function _GetGoodsAndSizesCSV(){
        $this->arResult = $this->client->GetGoodsAndSizesCSV($this->query->arGoodIds, 
                                                         $this->query->date);
        return $this->_FUncover()->_clearQueryParam();
    }

    /**
     * Метод раскрывающий полученые строки в данные массива разделенные ||
     * Необходим для правильной работы данного класса
     * @return FastSoapClientFromUltima
     */
    protected function _FUncover()
    {
        if( is_array($this->arResult) && isset($this->arResult[1]) ){
            foreach( $this->arResult[1] as &$item ){
                $item = explode('||', $item);
            }
            unset($item);
        }
        return $this;
    }
}