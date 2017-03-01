<?php

global $APPLICATION;

$APPLICATION->SetPageProperty("og:title", $arResult['NAME']);
$APPLICATION->SetPageProperty("og:type", "website");
$APPLICATION->SetPageProperty("og:url", 'http://'.$_SERVER['SERVER_NAME'].$arResult['DETAIL_PAGE_URL']);
$APPLICATION->SetPageProperty("og:image", 'http://'.$_SERVER['SERVER_NAME'].$arResult['DETAIL_PICTURE']['SRC']);
$APPLICATION->SetPageProperty("og:site_name", "Loft market");
$APPLICATION->SetPageProperty("og:description", $arResult['PREVIEW_TEXT']);


// \Bitrix\Main\Page\Asset::addString('<link rel="-shortcut icon" type="image/x-icon" href="img/icon/favicon.png">');
// $APPLICATION->AddHeadString('<link rel="-shortcut icon" type="image/x-icon" href="img/icon/favicon.png">');