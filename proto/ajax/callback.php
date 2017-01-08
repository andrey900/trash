<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arEventFields = array(
    "NAME"        => $_REQUEST['name'],
    "PHONE"       => $_REQUEST['phone'],
    "EMAIL"       => (!empty($_REQUEST['email'])) ? $_REQUEST['email'] : 'email не вводился'
);

$res = CEvent::SendImmediate("CALLBACK", SITE_ID, $arEventFields, "N");
echo $res;