 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$REQ_FIELDS=array(
		"email" =>"email",
	);

    if(!empty($_POST['ID']))
	{
    		   CModule::IncludeModule("iblock");
   				$arSelect = Array('DETAIL_PAGE_URL','NAME','ID');
    	        $arFilter = Array(
    	        'ID'=>$_POST['ID'],
    	        "ACTIVE"=>"Y");
    	        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    	        if($ob = $res->GetNext())
    	        {
                      $arResult =  $ob;
    	        }
	}
	$MAIL_FIELDS = array_merge(
	array_values($REQ_FIELDS), array(
	array('ID',$arResult['ID']),
	array('item',$arResult['NAME']),
	array('URL',((!empty($arResult['DETAIL_PAGE_URL']))?'http://'.$_SERVER["SERVER_NAME"].$arResult['DETAIL_PAGE_URL']:'')))
	);

	$ajaxFormPath = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));
	$APPLICATION->IncludeComponent(
		"intelsib:form.uni",
		"getprice_n",
		Array(
			"AJAX_FORM_PATH" => $ajaxFormPath,
			"USE_CAPTCHA" => "N",
			"USE_JQUERY_FORMS" => "Y",
			"EVENT_NAME" => "getprice",
			"OK_TEXT" => "\\t\\t\\tЗаявка успешно отправлена! \\nВ ближайшее время Вам перезвонит менеджер компании.",
			"formname" => "getprice",
			"REQUIRED_FIELDS" => $REQ_FIELDS,
			"MAIL_FIELDS" => $MAIL_FIELDS,
			"EVENT_MESSAGE_ID" => array(0=>"80",),
			"SAVE_TO_IBLOCK" => "N",
			"IBLOCK_TYPE" => "content",
			"IBLOCK_ID" => "3",
			"IBLOCK_FIELDS_ASSOCIATHION" => array("NAME"=>"names","PREVIEW_TEXT"=>"phone","ACTIVE"=>"ACTIVE"),
			"IBLOCK_PROPERTYES_ASSOCIATION" => array(29=>'usermark', 30=>'usermail', 31=>'user_showmail', 33=>'ELEMENT')
		)
	);
?>
   

