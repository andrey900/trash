<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

// заголовок кнопки
$arResult['BUTTON_CAPTION'] = $arParams['BUTTON_CAPTION'];

$arResult['success'] = "";
$arResult['error'] = "";
	
// обработка отправки формы
if(isset($_POST['submit'])){
	//echo '<pre>'; print_r($_POST); echo '</pre>';
	 
	// проверяем отправку формы...
	if(!isset($_POST["DATA_TYPE"]) || $_POST["DATA_TYPE"] == ""){
    	$arResult['error'] .= GetMessage("WEBSLON_AD_ANALYSIS_ERR_DATA_TYPE")."<br/>";	
	}
	if(!isset($_POST["LEAD_TYPE"]) || $_POST["LEAD_TYPE"] == ""){
    	$arResult['error'] .= GetMessage("WEBSLON_AD_ANALYSIS_ERR_LEAD_TYPE")."<br/>";	
	}
	
	if($_POST["FIO"] == ""){
    	$arResult['error'] .= GetMessage("WEBSLON_AD_ANALYSIS_ERR_FIO")."<br/>";	
	}
    if($_POST["EMAIL"] == "" && $_POST["PHONE"] == ""){
    	$arResult['error'] .= GetMessage("WEBSLON_AD_ANALYSIS_ERR_EMAIL_PHONE")."<br/>";	
    }
	//if($_POST["PROMO_CODE_ID"] == ""){
    //	$arResult['error'] .= GetMessage("WEBSLON_AD_ANALYSIS_ERR_PROMO_CODE")."<br/>";	
	//}
	
	if($arResult['error'] != ""){ // есть ошибки
		$arResult['FIO'] = $_POST["FIO"];
		$arResult['EMAIL'] = $_POST["EMAIL"];
		$arResult['PHONE'] = $_POST["PHONE"];
		$arResult['PROMO_CODE_ID'] = $_POST["PROMO_CODE_ID"];
		$arResult['PHONE_NAME'] = $_POST["PHONE_NAME"];
	}else{ // делаем запись в БД
		$arData = array();
		$arData['DATE'] = date("d-m-Y H:i:s");
		$arData['LEAD_TYPE_ID'] = intval($_POST['LEAD_TYPE']);
		if($_POST["DATA_TYPE"] == "call"){			
			$arData['DATA_TYPE'] = "call";
		}
		//$arData['DATA_ID'] = $WEB_FORM_ID;
		//$arData['DATA_ELEMENT_ID'] = $RESULT_ID;
		
		$arData['FIO'] = $_POST['FIO'];
		$arData['PHONE'] = $_POST['PHONE'];
		$arData['EMAIL'] .= $_POST['EMAIL'];
		$arData['PHONE_NAME'] .= $_POST['PHONE_NAME'];
		$arData['PROMO_CODE_ID'] = intval($_POST['PROMO_CODE_ID']);
		$arData['UPDATE_DATA_FROM_PROMOCODE'] = true;
				
		$ob = new C_WEBSLON_AD_ANALYSIS_Lead(0);
		$ob->Add($arData);
		
		$arResult['success'] = $arParams['SUCCESS'];
	}
};

// заполняем типы лидов
$arResult['dataTypes'] = array(
	"call" => array(
		"NAME" => GetMessage("WEBSLON_AD_ANALYSIS_CALL"),
		"CHECKED" => true
	)
);
    
// заполняем виды лидов
$rs = C_WEBSLON_AD_ANALYSIS_LeadType::GetList();
while($ar = $rs->Fetch()){
	$arResult['leadTypes'][$ar["ID"]] = $ar;
	if($arResult['error'] != "" && isset($_POST["LEAD_TYPE"]) && $_POST["LEAD_TYPE"] == $ar["ID"]){
		$arResult['leadTypes'][$ar["ID"]]["CHECKED"] = true;
	}
};

$this->IncludeComponentTemplate();
?>
