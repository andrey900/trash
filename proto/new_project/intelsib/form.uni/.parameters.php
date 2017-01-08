<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arFilter = Array("TYPE_ID" => "FEEDBACK_FORM", "ACTIVE" => "Y");
if($site !== false)
	$arFilter["LID"] = $site;

$arEvent = Array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arComponentParameters = array(
	"PARAMETERS" => array(
		"USE_CAPTCHA" => Array(
			"NAME" => GetMessage("MFP_CAPTCHA"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "BASE",
		),
		"OK_TEXT" => Array(
			"NAME" => GetMessage("MFP_OK_MESSAGE"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("MFP_OK_TEXT"), 
			"PARENT" => "BASE",
		),
        "formname" => Array(
            "NAME" => 'имя формы',
            "TYPE" => "STRING",
            "DEFAULT" => '',
            "PARENT" => "BASE",
        ),

		"REQUIRED_FIELDS" => Array(
			"NAME" => GetMessage("MFP_REQUIRED_FIELDS"), 
			"TYPE"=>"LIST", 
			"MULTIPLE"=>"Y", 
			"VALUES" => Array("NONE" => GetMessage("MFP_ALL_REQ"), "NAME" => GetMessage("MFP_NAME"), "EMAIL" => "E-mail", "MESSAGE" => GetMessage("MFP_MESSAGE")),
			"DEFAULT"=>"", 
			"COLS"=>25, 
			"PARENT" => "BASE",
		),
        "MAIL_FIELDS" => Array(
			"NAME" => 'Поля формы',
			"TYPE"=>"STRING",
			"MULTIPLE"=>"Y", 
			"DEFAULT"=>"", 
			"PARENT" => "BASE",
		),

		"EVENT_MESSAGE_ID" => Array(
			"NAME" => GetMessage("MFP_EMAIL_TEMPLATES"), 
			"TYPE"=>"LIST", 
			"VALUES" => $arEvent,
			"DEFAULT"=>"", 
			"MULTIPLE"=>"Y", 
			"COLS"=>25, 
			"PARENT" => "BASE",
		),
               "SAVE_TO_IBLOCK" => array(
			"PARENT" => "IBLOCK",
			"NAME" => 'Сохранять данные в инфоблок',
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
            "IBLOCK_TYPE" => array(
			"PARENT" => "IBLOCK",
			"NAME" => "Тип инфоблока",
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "IBLOCK",
			"NAME" => "Инфоблок",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
            "IBLOCK_FIELDS_ASSOCIATHION" => array(
			"PARENT" => "IBLOCK",
			"NAME" => 'Поля инфоблока',
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => array(),
			"ADDITIONAL_VALUES" => "Y",
		),
            "IBLOCK_PROPERTYES_ASSOCIATION" => array(
			"PARENT" => "IBLOCK",
			"NAME" => 'Свойства инфоблока',
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => array(),
			"ADDITIONAL_VALUES" => "Y",
		),
		"USE_JQUERY_FORMS" => Array(
			"NAME" => GetMessage("USE_JQUERY_FORMS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "BASE",
		),
        "AJAX_FORM_PATH" => Array(
			"NAME" => GetMessage("AJAX_FORM_PATH"),
			"TYPE" => "STRING",
			"DEFAULT" => "/_ajax/form.php",
			"PARENT" => "BASE",
		),

	)
);


?>