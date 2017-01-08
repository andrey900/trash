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

    public function FGetAllGoodsRemains()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';
        $this->_GetGoodsRemainsCSV();
        return $this->arResult;
    }

    public function FGetLastGoodsRemains($date='')
    {
        $this->query->arGoodIds = array();
        $this->query->date = ($date)?$date:date('Y-m-d H:i:s');
        $this->_GetGoodsRemainsCSV();
        return $this->arResult;
    }

    public function FGetGoodsRemainsFromId($id=array(0))
    {
        $this->query->arGoodIds = (is_array($id))?$id:array(0);
        $this->query->date = '';
        $this->_GetGoodsRemainsCSV();//print_r($this->arResult);
        return $this->arResult;
    }

    public function FGetAllGoodsPrices()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';
        $this->_GetGoodsPricesCSV();
        return $this->arResult;
    }

    public function FGetLastGoodsPrices($date='')
    {
        $this->query->arGoodIds = array();
        $this->query->date = ($date)?$date:date('Y-m-d H:i:s');
        $this->_GetGoodsPricesCSV();
        return $this->arResult;
    }

    public function FGetGoodsPricesFromId($id=array(0))
    {
        $this->query->arGoodIds = (is_array($id))?$id:array(0);
        $this->query->date = '';
        $this->_GetGoodsPricesCSV();
        return $this->arResult;
    }

    public function FGetAllGoodsAndSizes()
    {
        $this->query->arGoodIds = array();
        $this->query->date = '';
        $this->_GetGoodsAndSizesCSV();
        return $this->arResult;
    }

    public function FGetLastGoodsAndSizes()
    {
        $this->query->arGoodIds = array();
        $this->query->date = date('Y-m-d H:i:s');
        $this->_GetGoodsAndSizesCSV();
        return $this->arResult;
    }

    public function FGetGoodsAndSizesFromId($id=array(0))
    {
        $this->query->arGoodIds = (is_array($id))?$id:array(0);
        $this->query->date = '';
        $this->_GetGoodsAndSizesCSV();
        return $this->arResult;
    }

    private function _GetGoodsRemainsCSV()
    {
        $this->arResult = $this->client->GetGoodsRemainsCSV($this->query->arGoodIds, 
                                                            $this->query->arWareHouseIds, 
                                                            $this->query->date);
        return $this->_FUncover()->_clearQueryParam();
    }

    private function _GetGoodsPricesCSV(){
        $this->arResult = $this->client->GetGoodsPricesCSV($this->query->arGoodIds, 
                                                         $this->query->priceCatIds, 
                                                         $this->query->date);
        return $this->_FUncover()->_clearQueryParam();
    }

    private function _GetGoodsAndSizesCSV(){
        $this->arResult = $this->client->GetGoodsAndSizesCSV($this->query->arGoodIds, 
                                                         $this->query->date);
        return $this->_FUncover()->_clearQueryParam();
    }

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