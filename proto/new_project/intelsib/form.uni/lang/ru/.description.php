<?
$MESS ['MAIN_FEEDBACK_COMPONENT_NAME'] = "**_Универсальная форма отправки сообщений с сохранением информации в инфоблок";
$MESS ['MAIN_FEEDBACK_COMPONENT_DESCR'] = "Универсальная форма отправки сообщений на E-mai:<br/>".'<pre>'.htmlspecialchars('
<?$REQ_FIELDS=array(
"Ваше имя" =>"NAME",
"email" =>"email",
"Отзыв" =>"otzyv",
);

$MAIL_FIELDS = array_merge(array_values($REQ_FIELDS), array(
array(\'IBLOCK_SECTION_ID\',12),
array(\'ID_ELEMENT\',22),
array(\'ACTIVE\',\'N\')
));
$ajaxFormPath = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));
?>

<?$APPLICATION->IncludeComponent(
	"intelsib:form.uni",
	"left_otzivi",
	Array(
		"AJAX_FORM_PATH"=$ajaxFormPath,
		"USE_JQUERY_FORMS"="Y",
		"USE_CAPTCHA" => "N",
		"EVENT_NAME" => "FEEDBACK_FORM",
		"OK_TEXT" => "Спасибо, Ваш отзыв отправлен.",
		"formname" => "comment",
		"REQUIRED_FIELDS" => $REQ_FIELDS,
		"MAIL_FIELDS" => $MAIL_FIELDS,
		"EVENT_MESSAGE_ID" => array(0=>"34",),
		"SAVE_TO_IBLOCK" => "Y",
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "6",
		"IBLOCK_FIELDS_ASSOCIATHION" => array("NAME"=>"NAME","PREVIEW_TEXT"=>"otzyv",\'IBLOCK_SECTION_ID\'=>\'IBLOCK_SECTION_ID\',"ACTIVE"=>"ACTIVE"),
		"IBLOCK_PROPERTYES_ASSOCIATION" => array("20"=>"email",\'21\'=>\'ID_ELEMENT\')
	)
);
?>

<?$APPLICATION->IncludeComponent(
	"intelsib:reload.captcha",
	"contact",
	Array(
		"USE_GLOBAL" => "Y",
		"FORM_NAME" => array(),
		"IMAGE_DIALOG" => "/bitrix/components/bitrix/reload.captcha/templates/.default/images/reload.png")
);?>').'
</pre>';
?>