 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

   //echo "<pre>";print_r($_POST);echo "</pre>";
	$REQ_FIELDS=array(
	"Ваше имя" =>"username",
	"email" =>"usermail",
	"Комментарий" =>"comment",
	);

	$MAIL_FIELDS = array_merge(array_values($REQ_FIELDS), array(
   /*	array('IBLOCK_SECTION_ID',$SECTION_ID),*/
   	array('goods'=>$_POST['ELEMENT_ID']),
	array('ACTIVE'=>'N'),
	/*'file'*/
	'usermark',
	'user_showmail',
	));

	$ajaxFormPath = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));

	$APPLICATION->IncludeComponent(
		"intelsib:form.uni",
		"otziv_main",
		Array(
			"AJAX_FORM_PATH" => $ajaxFormPath,
			"USE_CAPTCHA" => "Y",
			"USE_JQUERY_FORMS" => "Y",
			"EVENT_NAME" => "FEEDBACK_FORM",
			"OK_TEXT" => "Спасибо, Ваш отзыв отправлен.",
			"formname" => "comment",
			"REQUIRED_FIELDS" => $REQ_FIELDS,
			"MAIL_FIELDS" => $MAIL_FIELDS,
			"EVENT_MESSAGE_ID" => array(0=>"26",),
			"SAVE_TO_IBLOCK" => "Y",
			"IBLOCK_TYPE" => "content",
			"IBLOCK_ID" => "3",
			"IBLOCK_FIELDS_ASSOCIATHION" => array("NAME"=>"username","PREVIEW_TEXT"=>"comment",/*'IBLOCK_SECTION_ID'=>'IBLOCK_SECTION_ID',*/"ACTIVE"=>"ACTIVE"),
			"IBLOCK_PROPERTYES_ASSOCIATION" => array(29=>'usermark', 30=>'usermail', 31=>'user_showmail', 33=>'ID_ELEMENT')
		)
	);
?>
