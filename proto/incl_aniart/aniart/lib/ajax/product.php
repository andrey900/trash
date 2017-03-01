<?php

namespace Studio8\Main\Ajax;

/**
* 
*/
class Product extends AAjaxHeandler
{
	public function ajaxStart(){
		if( $this->request->id )
			$p = new \Studio8\Main\Product((int)$this->request->id);
		else
			$this->responseError('Id not found');
		if($p->id > 0){
			$this->response->product = $p->getProduct();
			$this->responseSuccess('Product is find');
		}
	}
}