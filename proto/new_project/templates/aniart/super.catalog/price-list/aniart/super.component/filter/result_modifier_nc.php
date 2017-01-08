<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;
$APPLICATION->SetAdditionalCSS($APPLICATION->GetTemplatePath().'components/bitrix/form.result.new/.default/style.css');
$APPLICATION->AddHeadScript($APPLICATION->GetTemplatePath().'components/bitrix/form.result.new/.default/script.js');