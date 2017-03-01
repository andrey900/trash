<?php

namespace Studio8\Main;

/**
* 
*/
class BasketProduct implements IBasketProduct
{
	protected $product;

	public function __construct(Product $product)
	{
		$this->product = $product;

		$this->id = $product->id;
		$this->quantity = 1;
		$this->name = $product->name;
		$this->detailPicture = $product->images->full;
		$this->image = $product->images->mini;
		$this->brand = $product->brand;
		$this->article = $product->article;
		$this->price = round($product->price, 2);
		$this->detailPageUrl = $product->detailPageUrl;
	}

	public function getTotalPrice($format = true){
		if(!$format)
			return $this->quantity * $this->price;
		
		return number_format($this->quantity * $this->price, 2);
	}

	public function getProduct(){
		return $this->product;
	}

	public function changeQuantity($quantity){
		$quantity = (int)$quantity;
		
		if($quantity > 0)
			$this->quantity = $quantity;
	}

	public function __toString(){
		return json_encode($this);
	}

	public function clearProductData(){
		$this->product = null;
	}
}