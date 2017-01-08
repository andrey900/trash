<?define("NO_KEEP_STATISTIC", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arFilter = array(
	"LOGIN" => $_REQUEST['email'],
);
$db_user = CUser::GetList($by, $order, $arFilter);
$arResult['COUNT'] = $db_user->SelectedRowsCount(); 

echo json_encode($arResult);