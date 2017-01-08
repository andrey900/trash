<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$REQ_FIELDS=array(
"Телефон" =>"tel",
);

$MAIL_FIELDS = array_merge($REQ_FIELDS, array(
'h1', 'm1', 'h2', 'm2' ));

?> <?$APPLICATION->IncludeComponent( "intelsib:form.uni","zvonok", Array(
		"USE_CAPTCHA" => "N",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"formname" => "zvonok",
		"REQUIRED_FIELDS" => $REQ_FIELDS,
		"MAIL_FIELDS" => $MAIL_FIELDS ,
		"EVENT_MESSAGE_ID" => array(0=>"41",),
		"SAVE_TO_IBLOCK" => "N",
		"IBLOCK_TYPE" => "rus",
		"IBLOCK_ID" => "",
        "IBLOCK_FIELDS_ASSOCIATHION"=>array(
            "NAME"=>"RECIVER_NAME",
            "PREVIEW_TEXT"=>"MESSAGE"),
        "IBLOCK_PROPERTYES_ASSOCIATION"=> array(
            "75"=>"EMAIL_TO",
            "76"=>"AUTHOR")
	)
);?>
