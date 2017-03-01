<?php

namespace Studio8\Main\Ajax;

use Bitrix\Main\Application;

/**
* 
*/
class Basket extends AAjaxHeandler
{
	protected $basket;

	public function __construct(){
		parent::__construct();
		$this->basket = Application::getInstance()->basket;
		$this->request->id = (int)$this->request->id;
	}

	public function ajaxStart(){
		$this->response->basket = $this->basket->getBasket();
		$this->responseSuccess('Show basket');
	}

	public function add(){
		if( $this->request->id > 0 )
			$this->basket->add($this->request->id);

		$this->response->basket = $this->basket->getBasket();
		$this->responseSuccess('Product is added');
	}

	public function remove(){
		if( $this->request->id > 0 )
			$this->basket->remove($this->request->id);

		$this->response->basket = $this->basket->getBasket();
		$this->responseSuccess('Product is removed');
	}

	public function quantity(){
		if( $this->request->id > 0 ){
			if(!$this->basket->has($this->request->id))
				$this->basket->add($this->request->id);

			$this->basket->get($this->request->id)->changeQuantity($this->request->quantity);
			if( $this->request->quantity <= 0 )
				$this->basket->remove($this->request->id);
		}

		$this->response->basket = $this->basket->getBasket();
		$this->responseSuccess('Product quantity is changed');
	}

	public function quickBuy(){
		if( $this->request->id > 0 ){
			$this->basket->clear();
			$this->add();
			$this->response->data->type = 'quickBuy';
			return $this->responseSuccess('Product is added quick');
		}
		$this->responseError('Product not added quick');
	}
}