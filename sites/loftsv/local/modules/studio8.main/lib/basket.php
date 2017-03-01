<?php

namespace Studio8\Main;

use Studio8\Main\Product;
use Studio8\Main\BasketProduct;

class Basket
{
	protected $basket = [];

	private $sessName = 'basket';

	public function __construct($load = true){
		if( $load )
			$this->loadInSession();
	}

	public function __destruct(){
		$this->saveInSession();
	}

	public function add($id){
		if( $this->has($id) ){
			$p = $this->get($id);
			$p->changeQuantity($p->quantity + 1);
		} else {
			$this->basket[$id] = $this->makeBasketItem($id);
		}
	}

	public function has($id){
		return isset($this->basket[$id]);
	}

	public function get($id){
		if($this->has($id)) 
			return $this->basket[$id];
		
		return false;
	}

	public function getAllBasket(){
		return $this->basket;
	}

	public function makeBasketItem($id){
		$arRes = new Product((int)$id);

		return new BasketProduct($arRes);
	}

	public function remove($id){
		unset($this->basket[$id]);
	}

	public function saveInSession(){
		if( $this->getCountGoods()  ){
			foreach ($this->getAllBasket() as $product) {
				$product->clearProductData();
			}
		}
		$_SESSION[$this->sessName] = serialize($this->getAllBasket());
	}

	public function loadInSession(){
		$this->basket = unserialize($_SESSION[$this->sessName]);
	}

	public function clear(){
		$this->basket = [];
	}

	public function getCountGoods(){
		if( !$this->basket )
			return "";

		$cnt = count($this->basket);
		if( $cnt == 0 )
			return "";

		if( $cnt < 10 )
			return "0".$cnt;

		return $cnt;
	}

	public function getTotalPrice($format = true){
		$sum = 0;
		foreach ($this->getAllBasket() as $item) {
			$sum += $item->getTotalPrice(false);
		}

		if( $format )
			return number_format($sum, 2, ".", "'");
		
		return $sum;
	}

	public function getBasket(){
		$basket = new \stdClass();
		$basket->quantity = $this->getCountGoods();
		$basket->totalPrice = $this->getTotalPrice();
		$basket->items = $this->getAllBasket();

		return $basket;
	}
}