<?php
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');
//error_reporting(E_ALL);

//print_r($_REQUEST);//die();
class AjaxHandler{

	private $arRequest = '';
	private $arMethodVars = array();
	private $userInfoFields = array("NAME", "LAST_NAME", "PERSONAL_BIRTHDAY", "UF_NUMBER_CARD", "UF_EMAIL_SUBSCRIBE", "UF_SMS_SUBSCRIBE");
	public $result = '';

	public function __construct() {
		$this->arRequest = $_REQUEST;
		$this->result = new \stdClass();
		$this->result->html = '';
		$this->result->data = '';
		$this->result->error = '';
		$this->result->success = '';

		\CModule::IncludeModule('subscribe');
	}

	/**
	 * проверка на входные параметры(пустоту и существование)
	 **/
	protected function setParam($array){
		if( !is_array($array) ){
			throw new Exception('Error input parametrs');
		}

		foreach ($array as $name) {
			if( isset($this->arRequest[$name]) && !empty($this->arRequest[$name]) ){
				$this->arMethodVars[$name] = $this->arRequest[$name];
			} else {
				throw new Exception('обязательное поле оказалось пустым');
			}
		}

		return $this;
	}

	protected function checkEmail($emailFieldName)
	{
		$this->setParam( [$emailFieldName] );

		if ( !filter_var($this->arRequest[$emailFieldName], FILTER_VALIDATE_EMAIL) )
		    throw new Exception('email указан не верно');

		return $this;
	}

	public function registerUser($emailFieldName)
	{
		$email = strtolower( $this->arRequest[$emailFieldName] );

		$this->findUser($email);

		$login = $email;

		$pass = self::str_random(16);

		if(!empty($login) && !empty($email) && !empty($pass)) {
			$this->result->data = ['email'=>$email, 'login'=>$login];

			// добавляем юзера
			$registration = $USER->Register(
		        $login, 
		        '',
		        '', 
		        $pass,
		        $pass, 
		        $email
		    );
			if($registration['TYPE'] == 'ERROR')
	    		throw new Exception( $registration["MESSAGE"] );

	    	$mess["ok"] = $registration["MESSAGE"];
		}
	}

	public function findUser($email)
	{
		$arUsers = CUser::GetList(($by="id"), ($order="desc"), ["EMAIL" => $email])->Fetch();

		if( !empty($arUsers) )
			throw new Exception('указанный email зарегистрирован, укажите другой или авторизуйтесь под ним');
	}

	/**
	 * Изменение пароля
	 **/
	public function register(){
		try {
		$this->checkEmail('email')->registerUser('email');
		} catch (Exception $e) {
    		$this->result->error = 'Возникла ошибка: '.  $e->getMessage(). "!\n";
		}
		
	}

	/**
	 * Generate a "random" alpha-numeric string.
	 *
	 * Should not be considered sufficient for cryptography, etc.
	 *
	 * @param  int  $length
	 * @return string
	 */
	public static function str_random($length = 16)
	{
	    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	    return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
	}

}

if( !isset($_REQUEST['method']) || !method_exists('AjaxHandler', $_REQUEST['method']) ){
	die( json_encode(['error'=>'Error!']));
}

if( !isset($_REQUEST['ajax']) ){
	if( !empty($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']);
	else
		header("Location: /");
}

$ajaxHandler = new AjaxHandler();

try {
	$ajaxHandler->$_REQUEST['method']();
} catch (Exception $e) {
	$ajaxHandler->result->error = 'Возникла ошибка: '.  $e->getMessage(). "\n";
}

echo json_encode($ajaxHandler->result);