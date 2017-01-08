<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

 /*
global $USER;
if(is_object($USER))
{
	$rsUser = CUser::GetList($by, $order,
		array(
			"ID" => $USER->GetID(),
		),
		array(
			"SELECT" => array(
				"UF_*",
			),
		)
	)->Fetch();
}
    if(empty($rsUser['ID'])) return;  */

	$REQ_FIELDS=array(
	"Магазин" =>"magaz",
	"Область предложения" =>"TYPE",
	"Описание ситуации" =>"comment",
	"Имя" =>"name",
	"Email" =>"email",
	);


	$MAIL_FIELDS = array_merge(array_values($REQ_FIELDS), array(
   /*	array('USER',$rsUser['ID']),
	array('NAME',$rsUser['LOGIN']),
	array('DATE_ACTIVE_FROM',CIBlockFormatProperties::DateFormat('d.m.Y H:i:s', time())),
	array('ACTIVE','Y'), */
	'TYPE_NAME'
	));

	$ajaxFormPath = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));

  $APPLICATION->IncludeComponent(
		"intelsib:form.uni",
		"feedback_publ",
		Array(
			"AJAX_FORM_PATH" => $ajaxFormPath,
			"USE_CAPTCHA" => "N",
			"USE_JQUERY_FORMS" => "Y",
			"EVENT_NAME" => "OTZYVY_PREDLOGENIYA",
			"OK_TEXT" => "Спасибо, Ваше обращение отправлено!",
			"formname" => "comment",
			"REQUIRED_FIELDS" => $REQ_FIELDS,
			"MAIL_FIELDS" => $MAIL_FIELDS,
			"EVENT_MESSAGE_ID" => array(0=>"74",),
			"SAVE_TO_IBLOCK" => "N",
			"IBLOCK_TYPE" => "content",
			"IBLOCK_ID" => "12",
			"IBLOCK_FIELDS_ASSOCIATHION" => array("NAME"=>"NAME","PREVIEW_TEXT"=>"comment", "ACTIVE"=>"ACTIVE",'DATE_ACTIVE_FROM'=>'DATE_ACTIVE_FROM'),
			"IBLOCK_PROPERTYES_ASSOCIATION" => array("TYPE"=>"TYPE",'USER'=>'USER')
		)
	);
?>
