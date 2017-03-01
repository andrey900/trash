<?php

namespace Studio8\Main\Ajax;

use Studio8\Main\Order as OrderMain;
use Bitrix\Main\Application;

/**
* 
*/
class Order extends AAjaxHeandler
{
	protected $basket;
	protected $order;

	public function __construct(){
		parent::__construct();
		$this->basket = Application::getInstance()->basket;
		$this->order = new OrderMain();
		$this->request->id = (int)$this->request->id;
	}

	public function ajaxStart(){
		$basket = $this->basket->getBasket();
		if( empty($basket->items) || $basket->quantity <= 0 ){
			$this->responseError('Basket empty');
			return;
		}

		$props = $this->order->getOrderProps();
		$errors = [];
		$propValues = [];
		foreach ($props as $prop) {
			$code = strtolower($prop['CODE']);
			if( $prop['IS_REQUIRED'] == 'Y' && !$this->request->$code )
				$errors[] = ['input' => $prop['CODE'], 'mess' => 'Обязательное поле не заполенено'];
			$propValues[$prop['CODE']] = $this->request->$code;
		}
		if( !empty($errors) ){
			$this->response->data = $errors;
			$this->responseError('Field error');
			return;
		}

		$id = $this->order->add($propValues, $props);

		if( $id > 0 ){
			$this->response->data->id = $id;
			$this->response->data->token = $this->order->getOrderField('PROPERTY_17');
			$this->responseSuccess('Order maked');
		} else {
			$this->responseError($this->order->getError());
		}
	}
}