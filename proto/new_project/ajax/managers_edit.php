<?php
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

class ManagersAdmin{

	private $arRequest = '';
	private $arMethodVars = array();
	public $objreturn = false;
	
	public function __construct() {
		unset($_REQUEST['ajax']);
		$this->arRequest = $_REQUEST;
		unset($this->arRequest['method']);
		
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("workflow");
		CModule::IncludeModule("bizproc");

		$this->objreturn = new stdClass();
		$this->objreturn->error   = '';
		$this->objreturn->success = '';
		$this->objreturn->location= '';
		$this->objreturn->html 	  = '';
	}

	/**
	 * Method from update and add new fields from user data
	 **/
	public function editProp(){	
		$this->checkData('name')->checkData('prop')->checkPermition($this->arRequest['name'])->BX_updateProp();//getPropValue()->getPropInfo()->setProp()->addProp()->getComponent();
	}

	private function checkData($name)
	{
		if( !isset($this->arRequest[$name]) || empty($this->arRequest[$name]) )
			throw new Exception("Параметр $name не правильно определен", 1);
		return $this;
	}

	private function checkPermition($id)
	{
		global $USER;
		$iblock_permission = CIBlock::GetPermission(1, $USER->GetId());
		if($iblock_permission<"X")
			throw new Exception('Нет прав на изменение значения', 1);
		//$canWrite = CIBlockElementRights::UserHasRightTo(1, $id, 'X');
		//if(!$canWrite)
		//	throw new Exception('Нет прав на изменение значения', 1);
		return $this;
	}

	private function BX_updateProp()
	{
		//throw new Exception('пароли не совпадают!', 1);
		if( CIBlockElement::SetPropertyValueCode($this->arRequest['name'], (int)$this->arRequest['prop'], $this->arRequest['value']) )
			$this->objreturn->success = 'Данные успешно обновлены';
		else
			throw new Exception('При попытке обновить данные возникла ошибка', 1);
	}

}

if( !isset($_REQUEST['method']) || !method_exists('ManagersAdmin', $_REQUEST['method']) ){
	die('Error!');
}

if( !isset($_REQUEST['ajax']) ){
	if( !empty($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']);
	else
		header("Location: /");
}

$object = new ManagersAdmin();

try {
	$object->$_REQUEST['method']();
	echo json_encode($object->objreturn);
} catch (Exception $e) {
	$object->objreturn->error[] = 'Возникла ошибка: '.$e->getMessage()."\n";
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($object->objreturn);

	//echo 'Возникла ошибка: '.  $e->getMessage(). "\n";
}

//CIBlockElement::SetPropertyValueCode($ELEMENT_ID, "picture", $arFile);
?>