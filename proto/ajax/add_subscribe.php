<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("subscribe");
if($_REQUEST["email"]){
	$useremail = $_REQUEST["email"];
	if($_REQUEST["user_id"] != 0){
		$user=$_REQUEST["user_id"];
	}else{
		$user = false;

        global $USER;
        $arResult = $USER->Register($_REQUEST["email"], $_REQUEST["name"], "", $_REQUEST["email"], $_REQUEST["email"], $_REQUEST["email"]);
        $user = $USER->GetID(); // ID нового пользователя

	}
	$rubid = array();
	$rub = CRubric::GetList(array("NAME"=>"DESC"), array("ACTIVE"=>"Y"));
	while($rubres=$rub->GetNext()){$rubid[] = $rubres["ID"];}


    /*$filter = Array
    (
        "EMAIL" => $useremail
    );
    $rsUsers = CUser::GetList(($by="personal_country"), ($order="desc"), $filter); // выбираем пользователей
    $arUser = $dbUsers->Fetch();

    if ($arUser) {

    }*/






$subscr = new CSubscription;
$arFields = Array(
		"USER_ID" => $user,
		"FORMAT" => "html/text",
		"EMAIL" => $useremail,
		"CONFIRMED"=> "Y",
		"ACTIVE" => "Y",
		"RUB_ID" => $rubid,
		"SEND_CONFIRM" => "N"
	);
	$arSend=array(
	"EMAIL"=>$useremail,
	);
	$idsubrscr = $subscr->Add($arFields);
        $arSend["ID"]=$idsubrscr;
        if($idsubrscr>0){
        $subscription = CSubscription::GetByID($idsubrscr);
        $arSubscribe = $subscription->Fetch();
        $arSend["CONFIRM_CODE"]=$arSubscribe["CONFIRM_CODE"];
        }
	if($idsubrscr){
		echo "YES";
		//CSubscription::Authorize($idsubrscr);
		//CSubscription::ConfirmEvent($idsubrscr);
		CEvent::Send('SUBSCRIBE_INFO',SITE_ID,$arSend);
		
	}else{
		//echo $subscr->LAST_ERROR;
	}
}
?>