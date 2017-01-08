<?
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

//AJAX includes
if(!empty($_REQUEST['include'])){
	$_SERVER['REQUEST_URI'] = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
	$APPLICATION->SetCurPage(false);	
	switch($_REQUEST['include']){
		case 'category':
			include '../catalog/.category.allproducts.php';
		break;
		case 'allproducts':
			include '../catalog/.index.allproducts.php';
		break;
	}
	die;
}

//AJAX functions	
$data = array("status"=>"error","message"=>"Undefined Message");
switch($_REQUEST['func']){
	
} 
?>