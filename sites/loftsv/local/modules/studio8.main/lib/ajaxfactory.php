<?php

namespace Studio8\Main;

use Studio8\Main\Ajax\IAjaxHeandler;

class AjaxFactory
{
	protected $ajaxHeandler;
	protected $ajaxMethod = 'ajaxStart';
	protected $error;

	public function makeAjaxHeandler(){
		if( !isset($_REQUEST['className']) || !$_REQUEST['className'] ){
			$this->error = 'Error request';
		} else {
			$clName = ucfirst($_REQUEST['className']);
		}

		if( isset($_REQUEST['method']) && $_REQUEST['method'] ){
			$this->ajaxMethod = $_REQUEST['method'];
		}

		$cn = '\\Studio8\\Main\\Ajax\\'.$clName;

		if( class_exists($cn) && method_exists($cn, $this->ajaxMethod) ){
			try {
				$this->setAjaxHendler(new $cn());
			} catch (\Exception $e ) {
				$this->error = 'class signature error';
			}
		} else {
			$this->error = 'class not found';
		}

		return $this;
	}

	public function start(){
		if( !$this->ajaxHeandler ){
			echo json_encode($this->sendError($this->error));
			return false;
		}

		$m = $this->ajaxMethod;
		$this->ajaxHeandler->$m();

		$GLOBALS['APPLICATION']->RestartBuffer();
		echo json_encode($this->ajaxHeandler->getResponse());
	}

	protected function setAjaxHendler(IAjaxHeandler $cl){
		$this->ajaxHeandler = $cl;
	}

	protected function sendError($text){
		$obj = new \stdClass();
		$obj->status = 'error';
		$obj->msg = $text;

		return $obj;
	}

	public function getAjaxHeandler(){
		return $this->ajaxHeandler;
	}
}