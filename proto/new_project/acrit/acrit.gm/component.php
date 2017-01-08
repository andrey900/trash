<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	if(!CModule::IncludeModule("acrit.googlemerchant"))
		ShowError(GetMessage("ACRIT_GOOGLEMERCHANT_NOT_MODULE"));
	if(!isset($arParams['PROFILE']))
		ShowError(GetMessage("ACRIT_GOOGLEMERCHANT_NOT_PROFILE"));
$arDefaultUrlTemplates404 = array(
	"element" => "#ELEMENT_ID#/",
);
$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();
$arComponentVariables = array(
	"ID",
	"CODE",
);

if($arParams["SEF_MODE"] == "Y")
{
	$arVariables = array();
	$engine = new CComponentEngine($this);
	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);
	$componentPage = $engine->guessComponentPath($arParams["SEF_FOLDER"],$arUrlTemplates,$arVariables);
	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
	$arResult = array(
		"FOLDER" => $arParams["SEF_FOLDER"],
		"URL_TEMPLATES" => $arUrlTemplates,
		"VARIABLES" => $arVariables,
		"ALIASES" => $arVariableAliases
	);
}
else
{
	$arVariables = array();
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);
	if(isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0)
		$componentPage = "element";
	elseif(isset($arVariables["ELEMENT_CODE"]) && strlen($arVariables["ELEMENT_CODE"]) > 0)
		$componentPage = "element";
	
	$arResult = array(
		"FOLDER" => "",
		"URL_TEMPLATES" => Array(
			"element" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#"."&".$arVariableAliases["ELEMENT_ID"]."=#ELEMENT_ID#",
		),
		"VARIABLES" => $arVariables,
		"ALIASES" => $arVariableAliases
	);
}

if($arParams['SEF_MODE']=='Y')
{
	$profId=$arResult['VARIABLES']['ID'];
}
else
{
	$profId=$arResult['VARIABLES']['ID'];
}
	if($profId>0)
	{
	$APPLICATION->RestartBuffer();
	echo CGM::ReturnXMLData($profId);
	$r = $APPLICATION->EndBufferContentMan();
	echo $r;die();
	}
		
?>