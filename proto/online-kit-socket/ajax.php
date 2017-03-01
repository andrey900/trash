<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

include_once("libs.php");

if(!CModule::IncludeModule('iblock') 
	|| !CModule::IncludeModule('catalog') 
	|| !CModule::IncludeModule('sale') 
	|| !$_POST['action']
) die();

/**
* 
*/
class AjaxHandler
{
	protected $request;

	protected static $response;

	function __construct()
	{
		$this->request = $_POST;

		self::$response = new stdClass();
		self::$response->status = "";
		self::$response->error = "";
		self::$response->success = "";
		self::$response->html = "";
		self::$response->data = new \stdClass();
	}

	public function getCollections(){
		if( ($intSection = (int)$this->request['sectionId']) == 0 ){
			$this->setError("Неверный идентификатор секции");
			return $this->responseJson(); 
		}

		$arRes = GetDataFactory::getCollectionsByBrand($intSection);

		$this->setResponseObjectData("items", $arRes);
		$this->setResponseObjectData("sectionInfo", GetDataFactory::getSectionById($intSection));
		$this->setSuccess();
		return $this->responseJson();
	}

	public function getFramesAndPushs(){
		if( ($intCollection = (int)$this->request['collectionId']) == 0 || 
			($intBrand = (int)$this->request['brandId']) == 0
		){
			$this->setError("Неверный идентификатор коллекции или бренда");
			return $this->responseJson(); 
		}

		$this->setResponseObjectData("frames", GetDataFactory::getFramesOrPushItems($intCollection, 'frames'));
		$this->setResponseObjectData("pushs", GetDataFactory::getFramesOrPushItems($intCollection, 'pushs'));
		$this->setResponseObjectData("sectionInfo", GetDataFactory::getSectionById($intCollection));
		$this->setSuccess();
		return $this->responseJson();
	}

	public function getGoods(){
		//$arIds = (int)$this->request['frameId'];
		$arIds = (int)$this->request['pushId'];
		$t = GetDataFactory::getArticlesItems($arIds, (int)$this->request['brandId']);
		$this->setResponseObjectData("pushItems", $t);
		
		$this->setResponseObjectData("frameItems", GetDataFactory::getArticlesItems((int)$this->request['frameId'], (int)$this->request['brandId']));
		
		// BrutalHack Render
		ob_start();
		echo "<table>";
		$GLOBALS['APPLICATION']->IncludeFile("/online-kit-socket/template.php", Array(
			"items" => self::$response->data->frameItems,
			"name" => "Рамки"
			));
		$GLOBALS['APPLICATION']->IncludeFile("/online-kit-socket/template.php", Array(
			"items" => self::$response->data->pushItems,
			"name" => "Механизмы"
			));		
		echo "</table>";
		$strTable = ob_get_contents();
		ob_end_clean();

		$this->setResponseObjectHtml($strTable);

		$this->setSuccess();
		return $this->responseJson();
	}

	public static function getResponse(){
		return self::$response;
	}

	public static function setError($str="", $html=""){
		self::$response->status = "error";
		self::$response->error = $str;
		
		if( $html )
			self::setResponseObject($html);
	}

	public static function setSuccess($str="", $html=""){
		self::$response->status = "success";
		self::$response->success = $str;
		
		if( $html )
			self::setResponseObject($html);
	}

	public static function responseJson($data=''){
		if( $data )
			return json_encode($data);
			
		return json_encode(self::$response);
	}

	protected static function setResponseObjectHtml($html=""){
		self::$response->html = $html;
	}

	protected static function setResponseObjectData($name, $data = ""){
		self::$response->data->$name = $data;//json_encode($data);
	}
}

$method = strtolower($_POST['action']);

$ajax = new AjaxHandler();
if( method_exists($ajax, $method) )
	$res = $ajax->$method();
else{
	$res = $ajax->responseJson($ajax->setError("Method not exist"));
}

echo $res; die;