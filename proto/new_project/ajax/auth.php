<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
session_start();
?>

<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "popup", array(
	"REGISTER_URL" => "/registration/physical-person/",
	"FORGOT_PASSWORD_URL" => "/forgot_password/",
	"PROFILE_URL" => "/personal/",
	"SHOW_ERRORS" => "N"
	),
	false
);?>

<?$APPLICATION->IncludeComponent(
	"ulogin:auth",
	"",
	Array(
		"PROVIDERS" => "facebook,vkontakte,yandex,mailru,google",
		"HIDDEN" => "other",
		"TYPE" => "panel",
		"REDIRECT_PAGE" => "",
		"UNIQUE_EMAIL" => "N",
		"SEND_MAIL" => "N",
		"GROUP_ID" => "5"
	),
false
);?>