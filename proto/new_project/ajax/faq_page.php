<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
   //echo "<pre>";print_r($_POST);echo "</pre>";
	$REQ_FIELDS=array(
		"Ваше имя" =>"username",
		"Ваш e-mail" =>"usermail",
		"Ваш вопрос" =>"comment",
	);


	$MAIL_FIELDS = array_merge(array_values($REQ_FIELDS), array(
		array('ELEMENT',$ELEMENT_ID),
		array('ACTIVE','N')
	));
//print_r($MAIL_FIELDS);
	$ajaxFormPath = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));

	$APPLICATION->IncludeComponent(
		"intelsib:form.uni",
		"faq_page",
		Array(
			"AJAX_FORM_PATH" => $ajaxFormPath,
			"USE_CAPTCHA" => "N",
			"USE_JQUERY_FORMS" => "Y",
			"EVENT_NAME" => "FAQ",
			"OK_TEXT" => "Спасибо, Ваш вопрос отправлен.",
			"formname" => "faq_page",
			"REQUIRED_FIELDS" => $REQ_FIELDS,
			"MAIL_FIELDS" => $MAIL_FIELDS,
			"EVENT_MESSAGE_ID" => array(0=>"78",),
			"SAVE_TO_IBLOCK" => "Y",
			"IBLOCK_TYPE" => "faq",
			"IBLOCK_ID" => "20",
			"IBLOCK_FIELDS_ASSOCIATHION" => array("NAME"=>"username","PREVIEW_TEXT"=>"comment","ACTIVE"=>"ACTIVE"),
			"IBLOCK_PROPERTYES_ASSOCIATION" => array(138=>'usermail')
		)
	);

?>
 

