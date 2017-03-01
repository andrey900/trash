<?

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
if( !$_SERVER['DOCUMENT_ROOT'] ){
	$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$path = pathinfo(__FILE__);
$path = $path['dirname'].DIRECTORY_SEPARATOR;
$fileName = "lastId.ini";
$mailTemplate = "mail.tpl";

$fullPath = $path . $fileName;

$id = (int)file_get_contents($fullPath);

// $id = 1648;

$strSql = "SELECT * FROM b_event WHERE ID > $id order by ID asc";

if($res = $GLOBALS['DB']->Query($strSql, true)){
	$lastId = 0;
	$arSitesInfo = array();
	while ( $arRes = $res->Fetch() ) {
		if( !$arSitesInfo[$arRes['LID']] )
			$arSitesInfo[$arRes['LID']] = CSite::GetByID($arRes['LID'])->Fetch();
		$arRes['SITE_INFO'] = $arSitesInfo[$arRes['LID']];

		$arData = makeMessageData($arRes);
		if( !empty($arData) ){
			if( sendMail($arData['eventName'], $arData['message']) )
				file_put_contents($fullPath, $arRes['ID']);
		}
	}
}

function sendMail($eventName, $message){
	// несколько получателей
	// $to  = 'ba_ndrey@ukr.net' . ', ';
	$to  = '7055556@mail.ru' . ', ';
	$to .= 'electrodom_by@mail.ru' . ', '; // обратите внимание на запятую
	$to .= 'electrodom.by@gmail.com';

	// тема письма
	$subject = "Дублирующее письмо события - $eventName";

	// Для отправки HTML-письма должен быть установлен заголовок Content-type
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

	// Дополнительные заголовки
	/*$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
	$headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
	$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
	$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
*/

	// Отправляем
	return mail($to, $subject, $message, $headers);
}

function replacer($arFields){
	$arFieldName = array(
		"NAME" => "Имя",
		"PRICE" => "Цена",
		"EMAIL" => "Е-маил",
		"PHONE" => "Телефон",
		"MESSAGE" => "Сообщение пользователя",
		"ORDER_ID" => "Заказ номер",
		"ORDER_DATE" => "Дата заказа",
		"ORDER_LIST" => "Состав заказа",
		"ORDER_USER" => "Имя пользователя",
		"REAL_ORDER_ID" => "Номер заказа в системе",
		"ORDER_COMMENT" => "Комментарий к заказу",
	);

	$arRes = array();
	foreach ($arFieldName as $key => $newName) {
		if( !isset($arFields[$key]) ) continue;
		$arRes[$newName] = $arFields[$key];
	}

	return $arRes;
}

function makeContent($arFields){
	$str = "";
	foreach ($arFields as $title => $value) {
		$str .= sprintf("<p><b>%s</b>: %s</p>".PHP_EOL, $title, $value);
	}

	return $str;
}

function makeMessageData($arData){
	$arEvents = array("SALE_NEW_ORDER", "CALLBACK_PHONE");
	if( !in_array($arData["EVENT_NAME"], $arEvents) ) 
		return array();

	if( $arData["EVENT_NAME"] == "SALE_NEW_ORDER" )
		$eventName = "Нового заказа";
	elseif( $arData["EVENT_NAME"] == "CALLBACK_PHONE" )
		$eventName = "Обратного звонка";

	$arFields = unserialize($arData['C_FIELDS']);

	// текст письма
	$message = file_get_contents($GLOBALS['path'] . $GLOBALS['mailTemplate']);
	$arFind = array(
		"#SITE_URL#",
		"#SITE_NAME#",
		"#EVENT_NAME#",
		"#FIELDS#",
	);

	$arReplace = array(
		sprintf("http://%s", $arData['SITE_INFO']['SERVER_NAME']),
		$arData['SITE_INFO']['NAME'],
		$eventName,
		makeContent(replacer($arFields)),
	);
	$message = str_replace($arFind, $arReplace, $message);

	return array("eventName" => $eventName, "message" => $message);
}