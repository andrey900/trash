<?php

class UltimaAndBitrixIntegration{

	public  $arResult = array();
	protected $arElement = array();
	public  $config = '';
	private $fieldNameXML_ID = 'XML_ID';		// используется для выборки элементов (PROPERTY_#CODE#)
	private $xml = 'XML_ID';					// используется в результ массиве(PROPERTY_#CODE#_VALUE)

	public function __construct($arResultUltima=array())
	{
		$this->arResultUltima = $arResultUltima;
		$this->createArExtId();
		$this->config = new stdClass();
		$this->config->iblock_id = 1;
		$this->config->section_id = false;
		$this->config->pricecataloggroupid = 1;
		$this->config->currency = 'RUB';
		$this->config->price = 'Price';
		$this->config->quantity = 'Free';
		$this->config->quantityres = 'Reserve';
		$this->config->weight = 'Weight';
		$this->config->height = 'Height';
		$this->config->length = 'Length';
		$this->config->width = 'Width';
		$this->config->useExtId = true;
		$this->config->createNewItem = true;
		$this->config->propertyExtId = '';
	}

	private function createArExtId()
	{
		if( !empty($this->arResultUltima) ){
			$this->arExtId = array();
			foreach ($this->arResultUltima as $key => $value) {
				if( $key != 0 )
					$this->arExtId[$key] = $key;
			}
			return true;
		} else 
			return false;
	}

	public function execute()
	{
		$this->getElementsId();
		$cntEl  = count($this->arElement);
		$cntExt = count($this->arExtId);
		if( $cntExt > $cntEl ){
			$arExtId = $this->findNotByElements();
			$this->addNewElement($arExtId);
		}

		if( !$this->fieldNameXML_ID )
			return false;

		foreach ($this->arElement as $key => $value) {
			$this->updatePrice($key, $this->arResultUltima[$value[$this->xml]][$this->config->price]);
			$this->updateProductInfo($key, $value[$this->xml]);
			$this->updateProductProperty($key, $value[$this->xml]);
		}
	}

	public function execUpdateSize()
	{
		$this->getElementsId();
		$cntEl  = count($this->arElement);
		$cntExt = count($this->arExtId);
		if( $cntExt != $cntEl ){
			//echo count($this->findNotByElements());
			$arExtId = $this->findNotByElements();
			$this->addNewElement($arExtId);
		}

		foreach ($this->arElement as $key => $value) {
			if( !$this->fieldNameXML_ID )
				break;
			$this->updateProductInfo($key, $value[$this->xml]);
			$this->updateProductProperty($key, $value[$this->xml]);
		}
	}

	protected function findNotByElements()
	{
		$arExtId = $this->arExtId;
		foreach ($this->arElement as $key => $value) {
			if( !$this->fieldNameXML_ID )
				break;
			unset($arExtId[$value[$this->xml]]);
		}
		return $arExtId;
	}

	public function getElementsId()
	{
		if( empty($this->arExtId) )
			return false;
		
		if( !$this->createFieldXML_ID() )
			return false;

		$this->arElement = CAniartTools::_GetInfoElements(false, array('ID', $this->fieldNameXML_ID), array('IBLOCK_ID'=>$this->config->iblock_id, $this->fieldNameXML_ID=>$this->arExtId, 'INCLUDE_SUBSECTIONS'=>'Y'));
		return $this->arElement;
	}

	public function updateProductInfo($PRODUCT_ID, $ext_id)
	{
		if( (int)$PRODUCT_ID <= 0 || (int)$ext_id <= 0 )
			return false;

		$arFields = array('QUANTITY' => $this->arResultUltima[$ext_id][$this->config->quantity],
						  'QUANTITY_RESERVED' => $this->arResultUltima[$ext_id][$this->config->quantityres],
						  'WIDTH'  => $this->arResultUltima[$ext_id][$this->config->width],
						  'LENGTH' => $this->arResultUltima[$ext_id][$this->config->length],
						  'HEIGHT' => $this->arResultUltima[$ext_id][$this->config->height],
						  'WEIGHT' => $this->arResultUltima[$ext_id][$this->config->weight]);
		CCatalogProduct::Update($PRODUCT_ID, $arFields);
	}

	public function updateProductProperty($PRODUCT_ID, $ext_id)
	{
		if( (int)$PRODUCT_ID <= 0 || (int)$ext_id <= 0 )
			return false;

		foreach ($this->config->property as $code_name => $ult_code_name) {
			CIBlockElement::SetPropertyValueCode($PRODUCT_ID, $code_name, $this->arResultUltima[$ext_id][$ult_code_name]);
		}
	}

	public function updatePrice($id, $price)
	{
		if( (int)$id <= 0 && (real)$price <= 0 )
			return false;

		$arPrice = CAniartTools::_GetPriceElements($id);
		if( (int)$arPrice[$id]['ID'] > 0 && (real)$price > 0){
			$arFields = Array(
			    "PRICE" 	=> $price,
			);
			CPrice::Update($arPrice[$id]['ID'], $arFields);
			return true;
		}

		if( (int)$arPrice[$id]['ID'] > 0 && (real)$price == 0 ){
			CPrice::Delete($arPrice[$id]['ID']);
			return true;
		}

		if( (int)$arPrice[$id]['ID'] == 0 && (real)$price > 0 ){
			$arFields = Array(
			    "PRODUCT_ID" => $id,
			    "CATALOG_GROUP_ID" => $this->config->pricecataloggroupid,
			    "PRICE" => $price,
			    "CURRENCY" => $this->config->currency,
			);
			CPrice::Add($arFields);
			return true;
		}
	}

	public function addNewElement($arExt_id)
	{
		if( !$this->config->createNewItem )
			return false;

		foreach( $arExt_id as $ext_id ){
			$el = new CIBlockElement;

			$arLoadProductArray = Array(
			  "MODIFIED_BY"    => 1, 				// элемент изменен текущим пользователем
			  "IBLOCK_SECTION_ID" => $this->config->section_id,         // элемент лежит в корне раздела
			  "IBLOCK_ID"      => $this->config->iblock_id,
			  "NAME"           => "Артикул: $ext_id",
			  "CODE"		   => "article_$ext_id",
			  //'XML_ID'		   => $ext_id,
			  "ACTIVE"         => "N",            // не активен
			  );

			if( $this->config->useExtId )
				$arLoadProductArray['XML_ID'] = $ext_id;

			if($PRODUCT_ID = $el->Add($arLoadProductArray)){
			  $this->arElement[$PRODUCT_ID] = array('ID'	  =>$PRODUCT_ID,
			  										$this->xml=>$ext_id);
			  $arFields = array(
                  "ID" => $PRODUCT_ID, 
                  "QUANTITY" => 1,
                  );
			  CCatalogProduct::Add($arFields);
			}

		}
	}
	
	private function createFieldXML_ID()
	{
		if( $this->config->useExtId ){
			$this->fieldNameXML_ID = 'XML_ID';
			$this->xml = $this->fieldNameXML_ID;
		}elseif( !empty($this->config->propertyExtId) ){
			$this->fieldNameXML_ID = 'PROPERTY_'.$this->config->propertyExtId;
			$this->xml = $this->fieldNameXML_ID.'_VALUE';
		}else {
			$this->fieldNameXML_ID = false;
			$this->xml = false;
			$this->config->createNewItem = false;
		}

		return $this->fieldNameXML_ID;
	}
}