<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

CModule::IncludeModule('nsandrey.textchecker');

CJSCore::Init(array('jquery'));

if (!CNASTextChecker::isApiAccessible())
{
	ShowError('API '.GetMessage("NSANDREY_TEXTCHECKER_SAYTA_NEDOST"));
	return;
}

$textChecker = new CNASTextChecker($arParams['TEXT_TO_CHECK']);
$textChecker->addTextForCheck();

$arResult['TEXT_HASH'] = $textChecker->getHash();

$this->IncludeComponentTemplate();