<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
global $USER;
if (!$USER->IsAuthorized() && false)
{?>
   <p><span class="bold">Сервис доступен только для зарегистрированных пользователей.</span></p>
    <span class="close_form"></span>
<?
}
else
{
    //$_POST['phone'] = '+7 '.$_POST['phone1'].'-'.$_POST['phone2'];
  	$REQ_FIELDS=array(
	"Ссылка на товар с меньшей ценой" =>"link",
	"Цена конкурента" =>"price",
	"Необходимое количество" =>"quant",
	"E-mail" =>"email",
	"Телефон" =>"phone1",
	);

	$MAIL_FIELDS = array_merge(array_values($REQ_FIELDS), array(
   /*	array('IBLOCK_SECTION_ID',0),
   	array('good_id',intval($_GET['good_id'])),*/
	array('ACTIVE','Y'),
	'comment',
	'goods_name',
	'good_id',
	'goods_price',
	));

	$ajaxFormPath = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));

	$APPLICATION->IncludeComponent(
		"intelsib:form.uni",
		"getMyPrice",
		Array(
			"AJAX_FORM_PATH" => $ajaxFormPath,
			"USE_CAPTCHA" => "N",
			"USE_JQUERY_FORMS" => "Y",
			"EVENT_NAME" => "GET_MY_PRICE",
			"OK_TEXT" => "Спасибо, Ваш запрос отправлен.",
			"formname" => "getmyprice",
			"REQUIRED_FIELDS" => $REQ_FIELDS,
			"MAIL_FIELDS" => $MAIL_FIELDS,
			"EVENT_MESSAGE_ID" => array("68",'67'),
			"SAVE_TO_IBLOCK" => "Y",
			"IBLOCK_TYPE" => "content",
			"IBLOCK_ID" => "7",
			"IBLOCK_FIELDS_ASSOCIATHION" => array("NAME"=>"email","ACTIVE"=>"ACTIVE",'PREVIEW_TEXT'=>'comment'),
			"IBLOCK_PROPERTYES_ASSOCIATION" => array(
            37=>'link',
			38=> 'price',
			39=> 'quant',
			41=> 'phone'
			)
		)
	);

}?>



