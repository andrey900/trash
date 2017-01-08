<?php
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');
//error_reporting(E_ALL);

//print_r($_REQUEST);//die();

/**
 * Function checked password curr user
 * @pass 	 - no crypt pass
 * @hashPass - hash pass DB ($USER->GetParam("PASSWORD_HASH"))
 **/
function checkPassword($pass='', $hashPass=''){
	
	if( empty($pass) || empty($hashPass))
		return false;

	$salt = substr($hashPass,0,8);
 	
 	if( $salt.md5($salt.$pass) ==  $hashPass)
 		return true;
 	else 
 		return false;
}

/**
 * return json object
 *------------------------------------------------------
 * return.error - json object if yes error
 * return.html - json object html from update DOM area
 * return.success - json object messages if return require OK
 **/

class Personal{

	private $arRequest = '';
	private $arMethodVars = array();
	public $objreturn = false;
	
	public function __construct() {
		unset($_REQUEST['ajax']);
		$this->arRequest = $_REQUEST;
		unset($this->arRequest['method']);
		$this->objreturn = new stdClass();
		$this->objreturn->error   = '';
		$this->objreturn->success = '';
		$this->objreturn->location= '';
		$this->objreturn->html 	  = '';
	}

	/**
	 * Method from update and add new fields from user data
	 **/
	public function save(){	
		$this->getPropValue()->getPropInfo()->setProp()->addProp()->getComponent();
		$this->objreturn->success = 'Данные успешно обновлены';
	}

	/**
	 * Method from delete fields and profile user data
	 **/
	public function deleteProfile(){
		$id = (int)$this->arRequest['profile'];

		if( $id < 0 )
			throw new Exception("Неверный ID профиля");

		CSaleOrderUserProps::Delete($id);
		$this->getComponent();
		$this->objreturn->success = 'Данные успешно удалены';
	}

	/**
	 * Изменение пароля
	 **/
	public function changePassword(){
		$this->setParam( array('new_pass','r_new_pass','old_pass') )->checkPassword('old_pass')->samePassword('new_pass', 'r_new_pass')->updatePassword();
	}

	/**
	 * Получаем из имени входных параметров нужные данные
	 * @param arRequest = input array
	 * @param arMethodVars['PROP_ID'] = create array
	 * @return this
	 **/
	private function getPropValue(){
		$this->arMethodVars['PROP_ID'] = array();
		$this->arMethodVars['NEWPROP'] = array();
		foreach ($this->arRequest as $key => $value) {
			preg_match("/ORDER_PROP_([0-9]+)_([0-9]+)/", $key, $matches);

			if( $matches[2] > 0 ) // если свойство создано
				$this->arMethodVars['PROP_ID'][$matches[0]] = $matches[2];
			elseif( $matches[2] == 0 ){ // добавление нового свойства+профиль
				$this->arMethodVars['NEWPROP'][$matches[1]] = $value;
			}
		}
		
		return $this;
	}

	/**
	 * Получаем необходимые поля и информацию по входным параметрам для изменения
	 * @param arMethodVars['PROP_ID'] = input array
	 * @param arMethodVars['PROP_INFO'] = create array
	 * @return this
	 **/
	private function getPropInfo(){
		$this->arMethodVars['PROP_INFO'] = array();
		foreach ($this->arMethodVars['PROP_ID'] as $key => $value) {
			$this->arMethodVars['PROP_INFO'][$key] = CSaleOrderUserPropsValue::GetByID($value);
		}
		return $this;
	}

	/**
	 * Обновление информации
	 * @param arMethodVars['PROP_ID'] = input array
	 * @param arMethodVars['PROP_INFO'] = create array
	 * @return this
	 **/
	private function setProp(){
		foreach ($this->arMethodVars['PROP_INFO'] as $key => $value) {
			$arFields = $value;
			if( !empty($this->arRequest[$key]) ){
				$arFields['VALUE'] = $this->arRequest[$key];
				CSaleOrderUserPropsValue::Update($value['ID'], $arFields);
			} else {
				throw new Exception('непопустимо пустое значение');
			}
		}
		return $this;
	}

	/**
	 * Чистка массива новых значений если они пустые
	 * @param arMethodVars['NEWPROP'] = input array
	 * @param arMethodVars['NEWPROP'] = cleared array
	 * @return this
	 **/
	private function trimNewProp(){
		if( empty($this->arMethodVars['NEWPROP']) )
			return $this;

		foreach ($this->arMethodVars['NEWPROP'] as $id => $val) {
			foreach ($val as $k => $v) {
				$nv = full_trim($v);
				if( empty($nv) )
					unset($this->arMethodVars['NEWPROP'][$id][$k]);
			}
		}
		foreach ($this->arMethodVars['NEWPROP'] as $id => $val) {
			if( empty($val) )
				unset($this->arMethodVars['NEWPROP'][$id]);
		}

		return $this;
	}

	/**
	 * Создание профилей и добавление к 1 профилю 1 значения
	 * @param arMethodVars['NEWPROP'] = input array
	 * @param arMethodVars['NEWPROP'] = cleared array
	 * @return this
	 **/
	private function addProp(){
		$this->trimNewProp();

		if( empty($this->arMethodVars['NEWPROP']) )
			return $this;

		$this->arMethodVars['GROUPINFO'] = array();
		foreach ($this->arMethodVars['NEWPROP'] as $id => $val) {
			$this->arMethodVars['GROUPINFO'][$id] = CSaleOrderProps::GetByID($id);
		}

		if( !empty($this->arMethodVars['NEWPROP']) ){
			
			$this->addNewProp();
		
		}
		
		return $this;
	}

	/**
	 * Создание профилей
	 * @return ID create profile
	 **/
	private function addProfile(){
		global $USER;
		$arFields = array(
		   "NAME" => "new".time(),
		   "USER_ID" => $USER->GetID(),
		   "PERSON_TYPE_ID" => 1
		);
		return CSaleOrderUserProps::Add($arFields);
	}

	/**
	 * Создание профилей+заполнение их свойством
	 * @return this
	 **/
	private function addNewProp(){
		
		foreach ($this->arMethodVars['NEWPROP'] as $id => $arValue) {
			foreach ($arValue as $value) {
				
				$profId = $this->addProfile();
				
				$arFields = array(
			   		"USER_PROPS_ID" => $profId,
			   		"ORDER_PROPS_ID" => $id,
			   		"NAME" => $this->arMethodVars['GROUPINFO'][$id]['NAME'],
			   		"VALUE" => $value
				);
				CSaleOrderUserPropsValue::Add($arFields);
			}
		}
	}

	/**
	 * Получение компонента на возвращение HTML результата
	 * @param template = name template
	 * @param $this->objreturn->html - html template
	 * @return this
	 **/
	public function getComponent($template='edit'){
		global $APPLICATION;
		global $USER;

		ob_start();
		$APPLICATION->IncludeComponent("aniart:sale.personal.profile.detail", $template, Array(
			"PATH_TO_DETAIL" => "/personal/",	// Страница редактирования профиля
			"ID" => $USER->GetId(),	// Идентификатор профиля
			"ID_PROPERTY_NOT_EDITABLE" => array(1),
			),
			false
		  );
		$this->objreturn->html = ob_get_contents();
		ob_end_clean();
		return $this;
	}

	/**
	 * проверка на входные параметры(пустоту и существование)
	 **/
	private function setParam($array){
		if( !is_array($array) ){
			throw new Exception('Error input parametrs', 3);
		}

		foreach ($array as $name) {
			if( isset($this->arRequest[$name]) && !empty($this->arRequest[$name]) ){
				$this->arMethodVars[$name] = $this->arRequest[$name];
			} else {
				throw new Exception('обязательное поле оказалось пустым!', 3);
			}
		}

		return $this;
	}

	/**
	 * Проверка существующего пароля с введеным пользователем
	 **/
	private function checkPassword($pole=''){
		if( empty($pole) )
			$pole = 'old_pass';

		global $USER;
		if( checkPassword($this->arMethodVars[$pole],$USER->GetParam("PASSWORD_HASH")) )
			return $this;
		else
			throw new Exception('текущий пароль неверен!', 3);
	}

	/**
	 * Проверка на совпадение паролей
	 */
	private function samePassword($pass1='', $pass2=''){
		if(empty($pass1))
			$pass1 = 'new_pass';
		if(empty($pass2))
			$pass2 = 'r_new_pass';

		if( $this->arMethodVars[$pass1] == $this->arMethodVars[$pass2] )
			return $this;
		else
			throw new Exception('пароли не совпадают!', 3);
	}

	/**
	 * Обновление пароля
	 */
	private function updatePassword($pass1='', $pass2=''){
		//global $USER;
		if(empty($pass1))
			$pass1 = 'new_pass';
		if(empty($pass2))
			$pass2 = 'r_new_pass';

		$fields = Array( "PASSWORD" => $this->arMethodVars[$pass1], "CONFIRM_PASSWORD" => $this->arMethodVars[$pass2],
		  );
		
		$this->BX_update($fields);
	}

	/**
	 * Общий метод для обновления полей пользователя
	 * @param array $fields - массив полей и значений для обновления
	 */
	private function BX_update($fields){
		global $USER;
		$curuser = new CUser;

		$curuser->Update($USER->GetID(), $fields);
		
		if( empty($curuser->LAST_ERROR) ){
			$USER->Authorize($USER->GetID()); // авторизуем
			$this->objreturn->success = "Персональные данные пользователя успешно изменены!";
			$this->objreturn->location = $_SERVER['HTTP_REFERER'];
		} else
			$this->objreturn->success = $curuser->LAST_ERROR;
	}
}

if( !isset($_REQUEST['method']) || !method_exists('Personal', $_REQUEST['method']) ){
	die('Error!');
}

if( !isset($_REQUEST['ajax']) ){
	if( !empty($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']);
	else
		header("Location: /");
}

$personal = new Personal();

try {
	$personal->$_REQUEST['method']();
	echo json_encode($personal->objreturn);
} catch (Exception $e) {
	$personal->objreturn->error[] = 'Возникла ошибка: '.$e->getMessage()."\n";
	if( $e->getCode() != 3 )
		$personal->getComponent();
	echo json_encode($personal->objreturn);

	//echo 'Возникла ошибка: '.  $e->getMessage(). "\n";
}