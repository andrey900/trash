<?php 
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

$user = new CUser;

$email = $_POST["email"];
$pass = $_POST["pass"];
$repeatPass = $_POST["repeatPass"];
$repairCode = $_POST["repairCode"];


$userInfo = isUsserIsset($email);

$mess = CUser::ChangePassword($userInfo["LOGIN"], $repairCode, $pass, $repeatPass, $SITE_ID=false);
if($mess["TYPE"] == "ERROR"){
	die(json_encode(array("error"=>$mess["MESSAGE"])));
}else{
	die(json_encode(array("ok"=>$mess["MESSAGE"])));
}
?>