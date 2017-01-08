<? define("NO_KEEP_STATISTIC", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$return = Array();

//new dBug($_REQUEST,'',true);

/*print_r($_REQUEST);

die();*/

if(!empty($_REQUEST['ID']) && !empty($_REQUEST['REVIEW']) && !empty($_REQUEST['AJAX_ID']) ){
	$return["bxajaxid"] = $_REQUEST['AJAX_ID'];
	$return["page"] = $_REQUEST['PAGE'];
	global $USER;
    if ($USER->IsAuthorized()) {
        $rsUser = CUser::GetByID($USER->GetID());
        $arUser = $rsUser->Fetch();
    } else {
        $arUser['ID'] = 0;
        $arUser['NAME'] = 'Гость';
    }

	
	CModule::IncludeModule("iblock");
	$el = new CIBlockElement;
	$PROP = array();
	$PROP['REVIEWS_GOOD'] = $_REQUEST['ID'];
	$PROP['REVIEWS_USER'] = $arUser['ID'];
	
	$arLoadProductArray = Array(
			'IBLOCK_SECTION_ID' => false,
			'IBLOCK_ID' => GOODS_REVIEWS_IBLOCK_ID,
			'PROPERTY_VALUES' => $PROP,
			'NAME' => $arUser['NAME'],
			'ACTIVE' => 'Y',
			'DATE_ACTIVE_FROM'=>ConvertTimeStamp(time()+CTimeZone::GetOffset(), "FULL"),
			'PREVIEW_TEXT' => $_REQUEST['REVIEW']
	);
	if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
		$return["res"] = "<div class='green'>Спасибо. Ваш отзыв успешно добавлен!</div>";
	} else {
		$return["error"] = "<div class='error'>Произошла ошибка. Попробуйте еще раз</div>"; 
	}
	
}else{
	$return["error"] = "<div class='error'>Произошла ошибка. Попробуйте еще раз</div>"; 
}
die(json_encode($return));
?>