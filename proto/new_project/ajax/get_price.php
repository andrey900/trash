<?php 
define("NO_KEEP_STATISTIC", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$REQ_FIELDS=array(
		"email" =>"email",
		'id'	=>'id',
	);

$error = '<h2 class="answer-err">';
$e = false;
foreach ($REQ_FIELDS as $key => $value) {
	if( !isset($_POST[$key]) || empty($_POST[$key]) ){
		$error .= 'Не заполнено поле '.$key;
		$e = true;
	} else {
		if( $key == 'email' && !check_email($_POST[$key]) ){
			$error .= 'Не верно указан Email '.$key;
			$e = true;
		}
		$REQ_FIELDS[$key] = $_POST[$key];
	}
}

$arRes['order']['text'] = $error.'</h2>';

if( $e )
	die( json_encode($arRes) );

if( (int)$REQ_FIELDS['id'] > 0 )
{
	CModule::IncludeModule("iblock");
	$arSelect = Array('NAME','ID', 'DETAIL_PAGE_URL', 'PROPERTY_PICTURE', 'IBLOCK_ID');
	$arFilter = Array(
		'ID'=>(int)$_POST['id'],
		"ACTIVE"=>"Y");
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	if($ob = $res->GetNext())
	{
		$arResult =  $ob;
	}
} else {
	$arRes['order']['text'] = 'He верно указан ID';
	die( json_encode($arRes) );
}

$MAIL_FIELDS = array_merge(
	$REQ_FIELDS,
	array(
		'id'=>$arResult['ID'],
		'name'=>$arResult['NAME'],
		'picture'=>( !isset($arResult['PROPERTY_PICTURE_VALUE'][0]) )? 'images/no_photo.png' : CFile::GetPath($arResult['PROPERTY_PICTURE_VALUE'][0]) ,
		'url'=>$arResult['DETAIL_PAGE_URL'],
		'edit_admin'=>'/bitrix/admin/iblock_element_admin.php?PAGEN_1=1&SIZEN_1=20&type=catalog&IBLOCK_ID='.$arResult['IBLOCK_ID'].'&set_filter=Y&find_el_id_start='.$arResult['ID'].'&find_el_id_end='.$arResult['ID'].'&find_section_section=-1&find_el_subsections=Y&',
	));

$omail = CEvent::SendImmediate(
	'CHECK_PRICE',
	SITE_ID,
	$MAIL_FIELDS,
	'N',
	'38'
);
//$omail = 1;
if($omail){
	$arRes['order']['text'] = '<h2 class="answer-ok">Запрос успешно сформирован.</h2><p>Ожидайте, наш менеджер свяжеться с Вами в ближайшее время.</p>';
	die( json_encode($arRes) );
} else {
	$arRes['order']['text'] = '<h2 class="answer-err">Во время формирования запроса произошла ошибка</h2>
								<p>Приносим наши извинения за технические проблемы.</p>
								<p>Попробуйте повторить запрос.</p>';
	die( json_encode($arRes) );
}
?>