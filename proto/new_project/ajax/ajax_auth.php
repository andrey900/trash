<?php
include_once $_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php';
?>

<?$APPLICATION->IncludeComponent(
    "bitrix:system.auth.form",
    "radio",
    array(
        "REGISTER_URL" => "/registratsiya/",
        "FORGOT_PASSWORD_URL" => "#forgot-password",
        "PROFILE_URL" => "/profile/",
        "SHOW_ERRORS" => "Y"
    ),
    false
);?>