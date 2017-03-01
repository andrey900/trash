<?php

namespace Studio8\Main\Ajax;

abstract class AAjaxHeandler implements IAjaxHeandler
{
	protected $response;
	protected $request;

	public function __construct(){
		$this->makeDefaultResponse();
		$this->makeDefaultRequest();
	}

	protected function makeDefaultResponse(){
		$this->response = new \stdClass();
		$this->response->data = null;
		$this->response->msg = "";
		$this->response->status = "";
	}

	protected function makeDefaultRequest(){
		$this->request = new \stdClass();
		foreach ($_REQUEST as $key => $value) {
			$m = strtolower($key);
			$this->request->$m = trim($value);
		}
	}

	public function responseSuccess($mess){
		$this->response->msg = $mess;
		$this->response->status = "success";
	}

	public function responseError($mess){
		$this->response->msg = $mess;
		$this->response->status = "error";
	}

	public function getResponse(){
		return $this->response;
	}
}