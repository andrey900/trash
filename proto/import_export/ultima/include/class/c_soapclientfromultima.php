<?php

/**
 * Основной класс с методами для работы с ультимой(аналог 1С-предприятия)
 * SSR
 **/
class SoapClientFromUltima {
    
    public $arResult = array();

    function __construct($wsdl, $options=array())
    {
        $this->client  = new SoapClientPort($wsdl, $options);
        $this->config  = new stdClass();
        $this->query   = new stdClass();
        $this->_clearQueryParam();
        $this->config->warehouseIds = array(1);
        $this->config->priceCatIds  = array(195);
    }

    public function GetAllGoodsRemains()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';//date('Y-m-d H:i:s')
        $this->_GetGoodsRemains();
        return $this->arResult;
    }

    public function GetLastGoodsRemains()
    {
        $this->query->arGoodIds = array();
        $this->query->date = date('Y-m-d H:i:s');
        $this->_GetGoodsRemains();
        return $this->arResult;
    }

    public function GetAllGoodsPrices()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';
        $this->_GetGoodsPrices();
        return $this->arResult;
    }

    public function GetLastGoodsPrices()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';
        $this->_GetGoodsPrices();
        return $this->arResult;
    }

    public function GetGoodsPricesFromId($id=array(0))
    {
        $this->query->arGoodIds = (is_array($id))?$id:array(0);
        $this->query->date = '';
        $this->_GetGoodsPrices();
        return $this->arResult;
    }

    public function GetAllGoodsAndSizes()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';
        $this->_GetGoodsAndSizes();
        return $this->arResult;
    }

    public function GetLastGoodsAndSizes()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';
        $this->_GetGoodsAndSizes();
        return $this->arResult;
    }

    public function GetGoodsAndSizesFromId($id=array(0))
    {
        $this->query->arGoodIds = (is_array($id))?$id:array(0);
        $this->query->date = '';
        $this->_GetGoodsAndSizes();
        return $this->arResult;
    }

    private function _GetGoodsRemains()
    {
        $this->arResult = $this->client->GetGoodsRemains($this->query->arGoodIds, 
                                                         $this->query->arWareHouseIds, 
                                                         $this->query->date);
        return $this->_clearQueryParam();
    }

    private function _GetGoodsPrices(){
        $this->arResult = $this->client->GetGoodsPrices($this->query->arGoodIds, 
                                                         $this->query->priceCatIds, 
                                                         $this->query->date);
        return $this->_clearQueryParam();
    }

    private function _GetGoodsAndSizes(){
        $this->arResult = $this->client->GetGoodsAndSizes($this->query->arGoodIds, 
                                                         $this->query->date);
        return $this->_clearQueryParam();
    }

    protected function _clearQueryParam()
    {
        $this->query->arGoodIds      = array(0);
        $this->query->arWareHouseIds = $this->config->warehouseIds;
        $this->query->priceCatIds    = $this->config->priceCatIds;
        $this->query->date           = date('Y-m-d H:i:s');
        return $this;
    }
}