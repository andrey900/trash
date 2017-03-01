<?php

namespace Studio8\Main;

use \Studio8\Main\Helpers;

class Product
{

	private $product;
	private $realItem;
	private $arPropIdByName = array(
		6 => "brand",
		7 => 'article',
		8 => 'color_palfon',
		9 => 'power',
		10 => 'collection',
		11 => 'price',
		12 => 'quantity',
		19 => 'width',
		20 => 'height',
		21 => 'diametr',
		22 => 'lamp_quantity',
		23 => 'lamp_power',
		24 => 'lamp_patron',
		25 => 'ip_proptect',
		26 => 'color_armatur',
	);

	public function __construct(){
		$a = func_get_args();
        $i = func_num_args();
        $constrSufix = 'd';

        if( $i==1 && is_array($a[0]) ){
    		$constrSufix = 'agl';
    		if( isset($a[0]['PROPERTIES']) )
    			$constrSufix = 'ap';
        } elseif( $i==1 && is_int($a[0]) )
    		$constrSufix = 'i';

        if (method_exists($this, $f='__construct_'.$constrSufix)) {
            call_user_func_array(array($this,$f),$a);
        }
	}

	public function __construct_i($a1)
    {
        $this->realItem = Helpers::GetInfoElements(false, ["*", "PROPERTY_*"], ["ID" => $a1, "IBLOCK_ID" => IBLOCK_CATALOG_ID])[$a1];
        foreach ($this->realItem as $key => $val) {
    		if( $name = $this->makeName($key) )
    			$this->product->$name = $val;
    	}
    	foreach ($this->arPropIdByName as $id => $code) {
    		$p = 'property'.$id;
    		if(isset($this->product->$p)){
    			$this->product->$code = $this->product->$p;
    			unset($this->product->$p);
    		}
    	}

    	if( $this->product->detailPictureId ){
    		$this->product->detailPicture = \CFile::GetByID($this->product->detailPictureId)->Fetch();
    	}

    	$this->afterInit();
    }

    public function __construct_ap($arProduct)
    {
    	$this->realItem = $arProduct;

    	foreach ($arProduct as $key => $val) {
    		if( $name = $this->makeName($key) )
    			$this->product->$name = $val;

    		$this->loadProperties($arProduct['DISPLAY_PROPERTIES']);
    	}

    	$this->afterInit();
    }

/*	public function getById($id){
		$arRes = Helpers::GetInfoElements($id)[$id];
	}*/

	protected function makeName($name){
		if( strpos($name, "~") !== false ) return false;

		$name = strtolower($name);
		return $this->rPSRConverter($name);
	}

	protected function rPSRConverter($name){
		if( !($p = strpos($name, '_')) ) return $name;

		$items = explode('_', $name);
		$name = array_shift($items);
		array_walk($items, function(&$n){ $n = ucfirst($n); });
		return $name.implode("", $items);
	}

	protected function loadProperties($arProp){
		foreach ($arProp as $item) {
			if( ($p = strpos($item['CODE'], "CATALOG_")) !== false ){
				$name = $this->makeName(substr($item['CODE'], 8));
			} else {
				$name = $this->makeName($item['CODE']);
			}

			$this->product->$name = $item['VALUE'];
			if( $item['PROPERTY_TYPE'] == 'E' )
				$this->product->$name = $item['LINK_ELEMENT_VALUE'][$item['VALUE']]['NAME'];
		}
	}

	public function __get($name){
		return $this->product->$name;
	}

	public function getProduct(){
		return $this->product;
	}

	protected function afterInit(){
		$this->makeDetailUrl();
		$this->makeMiniImage();
		$this->product->price = number_format($this->product->price, 2, ",", "");
	}

	protected function makeMiniImage(){
		$product = $this->getProduct();
		if( !is_array($product->detailPicture) && !$product->detailPicture ){
			$product->images = new \stdClass();
			$product->images->full = SITE_TEMPLATE_PATH."/img/product/not-image.jpg";
			$product->images->mini = SITE_TEMPLATE_PATH."/img/product/not-image.jpg";
			$product->images->detail = SITE_TEMPLATE_PATH."/img/product/not-image.jpg";
			$this->product = $product;
			return;
		}

		$id = 0;
		if( isset($product->detailPicture['SRC']) ){
			$id = $product->detailPicture['ID'];
		}elseif( $this->realItem['~DETAIL_PICTURE'] ){
			$id = $this->realItem['~DETAIL_PICTURE'];
		}
		
		$product->images = $this->photoFiltering($id);

/*		$arFilters = Array(
		    array(
		    	"name" => "watermark", 
		    	"position" => "bottomright", 
		    	"type" => "text", 
		    	"text" => "LoftSvet.by", 
		    	"font" => $_SERVER["DOCUMENT_ROOT"]."/arial.ttf",
		    	"color" => "777777"
		    )
		);

		$id = 0;
		if( isset($product->detailPicture['SRC']) ){
			$id = $product->detailPicture['ID'];
		}elseif( $this->realItem['~DETAIL_PICTURE'] ){
			$id = $this->realItem['~DETAIL_PICTURE'];
		}

		if( $id ){
			$mini = \CFile::ResizeImageGet($id, array('width'=>270, 'height'=>300), BX_RESIZE_IMAGE_PROPORTIONAL, true, $arFilters, false, 50);
			$detail = \CFile::ResizeImageGet($id, array('width'=>630, 'height'=>700), BX_RESIZE_IMAGE_PROPORTIONAL, true, $arFilters);
			if( is_array($product->detailPicture) )
			$full = \CFile::ResizeImageGet($id, array('width'=>$product->detailPicture['WIDTH'], 'height'=>$product->detailPicture['HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true, $arFilters);

			$product->images = new \stdClass();
			// $product->images->full = (!is_array($product->detailPicture))?$product->detailPicture:$product->detailPicture['SRC'];
			$product->images->full = $full['src'];
			$product->images->mini = $mini['src'];
			$product->images->detail = $detail['src'];
		}*/
		// $this->product = $product;
	}

	protected function makeDetailUrl(){
		if( !$this->product->detailPageUrl )
			return;

		$brand = $this->realItem['DISPLAY_PROPERTIES']['BRAND'];

		if( !$brand ){
			$brand = ['LINK_ELEMENT_VALUE' => [$this->brand => []], 'VALUE' => $this->brand];
			$brand['LINK_ELEMENT_VALUE'][$this->brand] = Helpers::GetInfoElements($this->brand, ["*"])[$this->brand];
			$this->product->brand = $brand['LINK_ELEMENT_VALUE'][$this->brand]['NAME'];
		}

		$inUrl = array(
			"#ELEMENT_BRAND#",
			"#ELEMENT_ARTICLE#",
			"#ELEMENT_CODE#"
		);

		$toUrl = array(
			$brand['LINK_ELEMENT_VALUE'][$brand['VALUE']]['CODE'],
			$this->product->article,
			$this->product->code,
		);

		$this->product->detailPageUrl = str_replace($inUrl, $toUrl, $this->product->detailPageUrl);
	}

	public function getImageSeo(){
		if( !$this->realItem['IPROPERTY_VALUES'] )
			return;
		return ' title="'.$this->realItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'].'" alt="'.$this->realItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'].'"';
	}

	public function getHaracteristics(){
		$arProps = [];
		foreach($this->realItem['DISPLAY_PROPERTIES'] as $prop){
			if( strpos($prop['CODE'], "CATALOG_") !== false ) continue;
			$arProps[$this->makeName($prop['CODE'])] = $prop['NAME'];
		}
		return $arProps;
	}

	public function getMorePhoto(){
		if( empty($this->realItem['MORE_PHOTO']) )
			return [];

		$morePhoto = [];

		foreach ($this->realItem['MORE_PHOTO'] as $picture) {
			$morePhoto[$picture['ID']] = $this->photoFiltering($picture['ID'], $picture['WIDTH'], $picture['HEIGHT']);
		}
		
		return $morePhoto;
	}

	protected function photoFiltering($id, $width=0, $height=0){
		$images = new \stdClass();
		
		$id = (int)$id;

		if( !$id )
			return $images;

		$arFilters = Array(
		    array(
		    	"name" => "watermark", 
		    	"position" => "bottomright", 
		    	"type" => "text", 
		    	"text" => "LoftSvet.by", 
		    	"font" => $_SERVER["DOCUMENT_ROOT"]."/arial.ttf",
		    	"color" => "777777"
		    )
		);

		$mini = \CFile::ResizeImageGet($id, array('width'=>270, 'height'=>300), BX_RESIZE_IMAGE_PROPORTIONAL, true, $arFilters, false, 50);
		$detail = \CFile::ResizeImageGet($id, array('width'=>630, 'height'=>700), BX_RESIZE_IMAGE_PROPORTIONAL, true, $arFilters);
		if( $width && $height ){
			$full = \CFile::ResizeImageGet($id, array('width'=>$width, 'height'=>$height), BX_RESIZE_IMAGE_PROPORTIONAL, true, $arFilters);
			$images->full = $full['src'];
		}

		$images->mini = $mini['src'];
		$images->detail = $detail['src'];

		return $images;
	}
}