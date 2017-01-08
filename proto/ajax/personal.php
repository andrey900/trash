<?php
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');
//error_reporting(E_ALL);

//print_r($_REQUEST);//die();
class Personal{

	private $arRequest = '';
	private $arMethodVars = array();
	private $userInfoFields = array("NAME", "LAST_NAME", "PERSONAL_BIRTHDAY", "UF_NUMBER_CARD", "UF_EMAIL_SUBSCRIBE", "UF_SMS_SUBSCRIBE");
	private $userDeliveryFields = array("PERSONAL_ZIP", "PERSONAL_STATE", "PERSONAL_CITY", "PERSONAL_STREET", "PERSONAL_NOTES", "PERSONAL_PHONE");
	
	public function __construct() {
		$this->arRequest = $_REQUEST;
		CModule::IncludeModule('subscribe');
	}

	/**
	 * Изменение пароля
	 **/
	public function changePassword(){
		try {
		$this->setParam( array('new_pass','r_new_pass','old_pass') )->checkPassword('old_pass')->samePassword('new_pass', 'r_new_pass')->updatePassword();
		} catch (Exception $e) {
    		echo 'Возникла ошибка: '.  $e->getMessage(). "\n";
		}
		
	}
	
	/**
	 * Изменение инф о пользователе
	 **/
	public function changeUserInfo(){
		try {
		$this->updateUserInfo();
		} catch (Exception $e) {
    		echo 'Возникла ошибка: '.  $e->getMessage(). "\n";
		}
	}
	
	/**
	 * Изменение информации для доставки
	 **/
	public function changeUserDelivery(){
		try {
		$this->updateUserDelivery();
		} catch (Exception $e) {
    		echo 'Возникла ошибка: '.  $e->getMessage(). "\n";
		}
	}

	/**
	 * метод для создания, изменения, удаления подписки
	 **/
	protected function Subscription(){
		$this->checkEmailSubs();
	}
	
	/**
	 * проверка на входные параметры(пустоту и существование)
	 **/
	private function setParam($array){
		if( !is_array($array) ){
			throw new Exception('Error input parametrs');
		}

		foreach ($array as $name) {
			if( isset($this->arRequest[$name]) && !empty($this->arRequest[$name]) ){
				$this->arMethodVars[$name] = $this->arRequest[$name];
			} else {
				throw new Exception('обязательное поле оказалось пустым!');
			}
		}

		return $this;
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
			throw new Exception('пароли не совпадают!');
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
			throw new Exception('текущий пароль неверен!');
	}

	/**
	 * Проверка по подписке(пользователь менял значение), и изм если нужно
	 **/
	private function checkEmailSubs(){
		global $USER;
		$arUserInfo = CUser::GetByID($USER->GetID())->GetNext();
		if($arUserInfo['UF_EMAIL_SUBSCRIBE'] != $this->arRequest['UF_EMAIL_SUBSCRIBE']){
			if($this->arRequest['UF_EMAIL_SUBSCRIBE'] == 'on')
				$this->addSubscribe($arUserInfo['EMAIL'], $arUserInfo['ID']);
			else
				$this->unSubscribe($arUserInfo['EMAIL']);
		}
	}
	
	/**
	 * Добавление подписки(либо просто активность=Y)
	 */
	private function addSubscribe($email, $userid=false){
		
		$subscr = new CSubscription;
		
		$arFields = Array(
			"USER_ID" => $userid, //($USER->IsAuthorized()? $USER->GetID():false),
			"FORMAT" => "html",//($FORMAT <> "html"? "text":"html"),
			"EMAIL" => $email,
			"ACTIVE" => "Y",
			"CONFIRMED" => "Y",
			"RUB_ID" => array(SUBSCRIBE_ID)
		);
		
		$subscription = CSubscription::GetByEmail($email)->ExtractFields("str_");
		if( isset($subscription['ID']) )
			$subscr->Update($subscription['ID'], array("ACTIVE"=>'Y' ) );
		else {
			//can add without authorization
			$ID = $subscr->Add($arFields);
			if($ID>0)
				CSubscription::Authorize($ID);
		}
/*		else
			$strWarning .= "Error adding subscription: ".$subscr->LAST_ERROR."<br>";*/
	}
	
	/**
	 * Отписывание от подписки (установка статуса ACTIVE=N)
	 */
	private function unSubscribe($email){
		
		$subscr = new CSubscription;
		
		$subscription = CSubscription::GetByEmail($email)->ExtractFields("str_");
		if( isset($subscription['ID']) )
			$subscr->Update($subscription['ID'], array("ACTIVE"=>'N') );
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

		//$curuser = new CUser;
		
		$fields = Array( "PASSWORD" => $this->arMethodVars[$pass1], "CONFIRM_PASSWORD" => $this->arMethodVars[$pass2],
		  );
		
		$this->BX_update($fields);

		/*$curuser->Update($USER->GetID(), $fields);
		if( empty($curuser->LAST_ERROR) ){
			$USER->Authorize($USER->GetID()); // авторизуем
			echo "Password changed!";
		} else
			echo $curuser->LAST_ERROR;
			*/
	}

	/**
	 * Обновление иформации по доставке
	 */
	private function updateUserDelivery(){

		$existValue = array_intersect($this->userDeliveryFields, array_keys($this->arRequest));
		foreach ($existValue as $value) {
				$fields[$value] = $this->arRequest[$value];
		}

		$this->BX_update($fields);
	}

	/**
	 * Обновление информации о пользователе
	 */
	private function updateUserInfo(){
		
		$this->Subscription();
		
		$fields = array("UF_SMS_SUBSCRIBE" => 0, "UF_EMAIL_SUBSCRIBE" => 0);
		$existValue = array_intersect($this->userInfoFields, array_keys($this->arRequest));
		foreach ($existValue as $value) {
				if($value == 'PERSONAL_BIRTHDAY') continue;
				$fields[$value] = ($this->arRequest[$value]=="on")?1:$this->arRequest[$value];
		}

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
			echo "Персональные данные пользователя успешно изменены!";
		} else
			echo $curuser->LAST_ERROR;
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
$personal->$_REQUEST['method']();
?>