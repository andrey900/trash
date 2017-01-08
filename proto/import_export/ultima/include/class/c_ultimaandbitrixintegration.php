<?php

class UltimaAndBitrixIntegration{

	public $arResult = array();
	protected $arElement = array();
	public $config = '';

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
		if( $cntExt != $cntEl ){
			//echo count($this->findNotByElements());
			$arExtId = $this->findNotByElements();
			$this->addNewElement($arExtId);
		}

		foreach ($this->arElement as $key => $value) {
			echo "\r\n update element - ".$key."\r\n";
			print_r($this->arResultUltima[$value['XML_ID']][$this->config->price]);
			/*if( $key == 14644 ){
				echo "\r\n update element - ".$key."\r\n";
				print_r($this->arResultUltima[$value['XML_ID']][$this->config->price]);
				$this->updatePrice($key, $this->arResultUltima[$value['XML_ID']][$this->config->price]);
				$this->updateProductInfo($key, $value['XML_ID']);
			}*/
			$this->updatePrice($key, $this->arResultUltima[$value['XML_ID']][$this->config->price]);
			$this->updateProductInfo($key, $value['XML_ID']);
		}
	}

	protected function findNotByElements()
	{
		$arExtId = $this->arExtId;
		foreach ($this->arElement as $key => $value) {
			unset($arExtId[$value['XML_ID']]);
		}
		return $arExtId;
	}

	protected function getElementsId()
	{
		if( empty($this->arExtId) )
			return false;
		
		$this->arElement = CAniartTools::_GetInfoElements(false, array('ID', 'XML_ID'), array('IBLOCK_ID'=>$this->config->iblock_id, 'EXTERNAL_ID'=>$this->arExtId, 'INCLUDE_SUBSECTIONS'=>'Y'));
		return true;
	}

	protected function updateProductInfo($PRODUCT_ID, $ext_id)
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

	protected function updatePrice($id, $price)
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

	protected function addNewElement($arExt_id)
	{
		foreach( $arExt_id as $ext_id ){
			$el = new CIBlockElement;

			$arLoadProductArray = Array(
			  "MODIFIED_BY"    => 1, 				// элемент изменен текущим пользователем
			  "IBLOCK_SECTION_ID" => $this->config->section_id,         // элемент лежит в корне раздела
			  "IBLOCK_ID"      => $this->config->iblock_id,
			  "NAME"           => "Артикул: $ext_id",
			  "CODE"		   => "article_$ext_id",
			  'XML_ID'		   => $ext_id,
			  "ACTIVE"         => "N",            // не активен
			  );

			if($PRODUCT_ID = $el->Add($arLoadProductArray))
			  $this->arElement[$PRODUCT_ID] = array('ID'	=>$PRODUCT_ID,
			  										'XML_ID'=>$ext_id);

		}
	}
}