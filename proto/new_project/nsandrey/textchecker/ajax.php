<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->RestartBuffer();

$action = $_REQUEST['action'];

CModule::IncludeModule('nsandrey.textchecker');

switch ($action)
{
	case 'GET_RESULT':
		if (!empty($_REQUEST['key']))
		{
			echo CNASTextChecker::textCheckCallback(
				$_REQUEST['key'],
				$_REQUEST['uid'],
				$_REQUEST['text_unique'],
				$_REQUEST['spell_check'],
				$_REQUEST['json_result']
			);
		}
		break;

	case 'CHECK_RESULT':
		if (!empty($_REQUEST['textHash']))
		{
			$result = CNASTextChecker::getResultByHash($_REQUEST['textHash']);

			echo json_encode($result);

			exit;
		}

		echo json_encode(array('ready' => false));

		break;

	default:
		break;
}

exit;