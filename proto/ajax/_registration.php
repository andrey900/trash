<?php
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');



$login = $_REQUEST['params']['login'];
$email = $_REQUEST['params']['email'];
$pass = $_REQUEST['params']['pass'];
$name = '';

if(isset($_REQUEST['params']['name']))
	$name = $_REQUEST['params']['name'];

$mess = Array();
if(!empty($login) && !empty($email) && !empty($pass)) {
		
		// добавляем юзера
			$registration = $USER->Register(
	        $login, 
	        $name,
	        '', 
	        $pass,
	        $pass, 
	        $email
	    );
		if($registration['TYPE'] == 'ERROR') {
    	$mess["error"]=$registration["MESSAGE"];
    }else{
    	$mess["ok"]=$registration["MESSAGE"];
    }
    
  	die(json_encode($mess));
}

?>