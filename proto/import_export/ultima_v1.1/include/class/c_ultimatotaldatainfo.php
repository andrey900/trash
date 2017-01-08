<?php

/**
 * Класс для получения конструктивных данных из ультимы
 * Позволяет получать не отрывки данных а целостную картину
 * (не отдельно, остатки, размеры, цены - а все вместе)
 * - примечание: работает через скоростной класс FastSoapClientFromUltima
 * 
 * @author andrey
 *
 */
class UltimaTotalDataInfo extends FastSoapClientFromUltima {

	// Необходимо указать узел WSDL
    public $wsdl = "http://89.249.18.174:50080/bitrix/OtherWebService.asmx?WSDL";
    public $arGoodsId    = array(); // Массив ид товаров каждый раз форм методом VariationResultArray
    private $newArResult = array(); // Выходной массив
    private $arSGoodsId   = array();// вспомогательный массив

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

    /**
     * Получение комплексных данных по всем товарам из ультимы
     * поскольку у них расное количество данных по разным методам, использую с меньшим числом товаров
     * FGetAllGoodsRemains
     */
    public function GetAllGoods()
    {
        parent::FGetAllGoodsRemains(); 			 // получаем все данные
        $this->VariationResultArray(); 			 // преобразовываем массив
        $this->newArResult = $this->arResult; 	 // создаем начальный новый массив
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

    /**
     * Получение комплексных данных по остаткам из ультимы(от посл выполнения скрипта)
     */
    public function GetLastModRemains()
    {
    	parent::FGetLastGoodsRemains($this->config->scriptUpdateTime);
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

    /**
    * получение данных по последним изменениям(от посл выполнения скрипта)
    * всех типов(остатки цены размеры)
    * и соответственно образование полных данных о полученых товарах
    **/
    public function GetAllLastMod()
    {
        $this->arSGoodsId = array();

        parent::FGetLastGoodsRemains($this->config->scriptUpdateTime);
        $this->_treatmentArResultCrArNResult();

        parent::FGetLastGoodsPrices($this->config->scriptUpdateTime);
        $this->_treatmentArResultCrArNResult();

        parent::FGetLastGoodsAndSizes($this->config->scriptUpdateTime);
        $this->_treatmentArResultCrArNResult();

        $r = $this->arSGoodsId;
        for( $i=0; $i<ceil(count($r)/$this->config->countRequest); $i++ ){
            $arIdPack = array_slice($r, $i*$this->config->countRequest, $this->config->countRequest);
            parent::FGetGoodsRemainsFromId($arIdPack);
            $this->VariationResultArray()->createNewArResult();
            parent::FGetGoodsPricesFromId($arIdPack);
            $this->VariationResultArray()->createNewArResult();
            parent::FGetGoodsAndSizesFromId($arIdPack);
            $this->VariationResultArray()->createNewArResult();
        }

        return $this->newArResult;
    }

    public function GetById($id)
    {
        $arIdPack = array((int)$id);
        parent::FGetGoodsPricesFromId($arIdPack);
        $this->_treatmentArResultCrArNResult();
        parent::FGetGoodsRemainsFromId($arIdPack);
        $this->VariationResultArray()->createNewArResult();
        parent::FGetGoodsPricesFromId($arIdPack);
        $this->VariationResultArray()->createNewArResult();
        parent::FGetGoodsAndSizesFromId($arIdPack);
        $this->VariationResultArray()->createNewArResult();
        return $this->newArResult;
    }

    /**
    * Используется для обработки данных в методе GetAllLastMod
    *  - создает массив ID всех товаров arSGoodsId
    *  - создает минимально необходимый массив newArResult
    **/
    private function _treatmentArResultCrArNResult()
    {
        $this->VariationResultArray();
        foreach ($this->arResult as $key => $value) {
            $this->arSGoodsId[$key] = $key;
            $this->newArResult[$key] = array($this->config->nameGoodsId => $key);
        }
    }

    /**
     * Получение комплексных данных по ценам из ультимы(от посл выполнения скрипта)
     */
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

    /**
     * Получение комплексных данных по размерам из ультимы(от посл выполнения скрипта)
     */
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

    /**
     * Переформировывает полученный массив из ультимы в именованный массив
     * и удаляет не нужный вспомогательный массив
     * работает с $this->arResult и видоизменяет тоже его
     * - примечание так же в качестве ключа начинает выступать код элемента ультимы
     * @return UltimaTotalDataInfo
     */
    public function VariationResultArray()
    {
        if( !empty($this->arResult[1]) && is_array($this->arResult[1])){
            $arRes = $this->arResult; 
            $this->arResult = array();
            $this->arGoodsId = array();
            foreach ($arRes[1] as $k => $arItem) {
                foreach ($arItem as $key => $Item) {
                    if( $arRes[0][$key]==$this->config->nameGoodsId && !empty($Item) )
                        $this->arGoodsId[$k] = $Item;
                    $this->arResult[$k][$arRes[0][$key]] = $Item; // создаем элементы массива(заменяю неименованые ключи на имена)
                }
            }
            $arRes = $this->arResult; $this->arResult = array();
            foreach ($this->arGoodsId as $key => $value) { // подставляем ИД в качестве ключей
                $this->arResult[$value] = $arRes[$key];
            }
        } else {
            $this->arResult = array();
        }
        return $this;
    }

    /**
     * Создает новый массив из преобразованого $this->arResult
     * добавляет в него новые значения после выполнения методов получения данных
     * обязательное условие должен существовать массив 
     * $this->newArResult
     * изначально можно создать просто $this->newArResult=$this->arResult
     * @return UltimaTotalDataInfo
     */
    protected function createNewArResult()
    {
    	foreach ($this->arResult as $key => $value) {
            if( is_array($value) && !empty($value) && isset($this->newArResult[$key]) )
               $this->newArResult[$key] = array_merge($this->newArResult[$key], $value);
        }
        return $this;
    }
}