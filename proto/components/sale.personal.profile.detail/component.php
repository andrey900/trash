<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}
if (!$USER->IsAuthorized())
{
	$APPLICATION->AuthForm(GetMessage("SALE_ACCESS_DENIED"));
}

global $USER;

$ID = IntVal($arParams["ID"]);
$errorMessage = "";
$bInitVars = false;

$arParams["PATH_TO_LIST"] = Trim($arParams["PATH_TO_LIST"]);
if (strlen($arParams["PATH_TO_LIST"]) <= 0)
	$arParams["PATH_TO_LIST"] = htmlspecialcharsbx($APPLICATION->GetCurPage());
$arParams["PATH_TO_DETAIL"] = Trim($arParams["PATH_TO_DETAIL"]);
if (strlen($arParams["PATH_TO_DETAIL"]) <= 0)
	$arParams["PATH_TO_DETAIL"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?ID=#ID#");

	
$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if($arParams["SET_TITLE"] == 'Y')
	$APPLICATION->SetTitle(GetMessage("SPPD_TITLE").$ID);

function getPrimaryProfile($userId=0){

	$db_sales = CSaleOrderUserProps::GetList(
	        array("ID" => "ASC"),
	        array("USER_ID" => $userId),
	        false,
	        array('nTopCount' =>1 )
	    );

	if($ar_sales = $db_sales->Fetch()){
		return $ar_sales['ID'];
	} else {
		echo 'Нет ни одного профиля пользователя для оформления заказов';
		//header('Location: /');
	}
}

function getAllProfiles($userId=0){

	$db_sales = CSaleOrderUserProps::GetList(
        array("ID" => "ASC"),
        array("USER_ID" => $userId)
    );

    $arUserProf = array();
	
	while ($ar_sales = $db_sales->Fetch()) {
	   $arUserProf[] = $ar_sales["ID"];
	}

	return $arUserProf;
}

function createResult($ID){
	if( (int)$ID <= 0 ){
		global $USER;
		if( !$USER->IsAuthorized() ){
			return false;
		}
		
		$ID = $USER->GetID();
	}

	$arUserProf= getAllProfiles($ID);
	$primaryId = getPrimaryProfile($ID);

	$db_propVals = CSaleOrderUserPropsValue::GetList(array("ID" => "ASC"), Array("USER_PROPS_ID"=>$arUserProf));

	while ($arPropVals = $db_propVals->Fetch())
	{
		if( isset($arRes[$arPropVals['ORDER_PROPS_ID']]) ){
				$arRes[$arPropVals['ORDER_PROPS_ID']]['VALUE'][$arPropVals['USER_PROPS_ID']] = array('ID'=>$arPropVals['ID'],'VALUE'=>$arPropVals['VALUE'], 'PROFILE'=>$arPropVals['USER_PROPS_ID'], 'main'=>($primaryId==$arPropVals['USER_PROPS_ID'])?true:false);
		} else {
			$arRes[$arPropVals['ORDER_PROPS_ID']] = $arPropVals;
			$val = $arRes[$arPropVals['ORDER_PROPS_ID']]['VALUE'];
			$id  = $arRes[$arPropVals['ORDER_PROPS_ID']]['USER_PROPS_ID'];
			$arRes[$arPropVals['ORDER_PROPS_ID']]['VALUE'] = array();
			$arRes[$arPropVals['ORDER_PROPS_ID']]['VALUE'][$id] = array('ID'=>$arPropVals['ID'],
																		'VALUE'=>$val,
																		'PROFILE'=>$arPropVals['USER_PROPS_ID'],
																		'main'=>($primaryId==$arPropVals['USER_PROPS_ID'])?true:false);
		}
	}
	return $arRes;
}

$arResult = createResult($ID);

$this->IncludeComponentTemplate();
?>
