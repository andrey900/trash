<?php 
	define("NO_KEEP_STATISTIC", true); // отключим статистику
	require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

	/*
	$emailList[] = getUserEmailList();
	die(json_encode($emailList));
	*/
	
	$email =  trim($_REQUEST["params"]);
	$user = isUsserIsset($email);
	//  с таким мылом юзер существует
	$mess = Array();
	if($user){
		$mess = CUser::SendUserInfo(
				$user["ID"], 
				"s1",
				"", 
				$bImmediate=true, 
				$eventName="USER_PASS_REQUEST"
		);
		$mess["mess"] =  "На email был отправлен код подтверждения";
	}else{
		$mess["error"] =  "Пользователя с таким email не существует";
	}
	die(json_encode($mess));


?>