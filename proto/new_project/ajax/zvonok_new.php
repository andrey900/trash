 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$REQ_FIELDS=array(
		"Ваше имя" =>"names",
		"Телефон" =>"phone",
		"Страница сайта" => "url"
	);
	
	$MAIL_FIELDS = array_merge(array_values($REQ_FIELDS), array(array('ACTIVE','N')));

	$ajaxFormPath = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));

	$APPLICATION->IncludeComponent(
		"intelsib:form.uni",
		"zvonok_new",
		Array(
			"AJAX_FORM_PATH" => $ajaxFormPath,
			"USE_CAPTCHA" => "N",
			"USE_JQUERY_FORMS" => "Y",
			"EVENT_NAME" => "ZVONOK_NEW",
			"OK_TEXT" => "\\t\\t\\tЗаявка успешно отправлена! \\nВ ближайшее время Вам перезвонит менеджер компании.",
			"formname" => "zvonok",
			"REQUIRED_FIELDS" => $REQ_FIELDS,
			"MAIL_FIELDS" => $MAIL_FIELDS,
			"EVENT_MESSAGE_ID" => array(0=>"77",),
			"SAVE_TO_IBLOCK" => "N",
			"IBLOCK_TYPE" => "content",
			"IBLOCK_ID" => "3",
			"IBLOCK_FIELDS_ASSOCIATHION" => array("NAME"=>"names","PREVIEW_TEXT"=>"phone","ACTIVE"=>"ACTIVE"),
			"IBLOCK_PROPERTYES_ASSOCIATION" => array(29=>'usermark', 30=>'usermail', 31=>'user_showmail', 33=>'ELEMENT')
		)
	);
?>
   

