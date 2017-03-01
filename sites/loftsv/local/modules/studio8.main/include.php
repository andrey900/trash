<?php

use Bitrix\Main\Application;
use Studio8\Main\Basket;

\Bitrix\Main\Loader::IncludeModule('highloadblock');
\Bitrix\Main\Loader::includeModule('iblock');

$modulePath = dirname(__FILE__);

session_start();

include $modulePath.'/vars.php';
include $modulePath.'/utils.php';

$options = new \stdClass();
$options->usesCssFiles = [
	"/css/bootstrap.min.css",
	"/css/core.css",
	"/lib/css/nivo-slider.css",
	"/css/shortcode/shortcodes.css",
	"/css/responsive.css",
	"/css/custom.css",
	"/css/style.css",
	"/css/color/color-core.css",
	// "/css/all_styles.min.css",
	"/css/fix.css",
];
$options->usesJsFiles = [
	"/js/vendor/modernizr-2.8.3.min.js",
	"/js/bootstrap.min.js",
	"/lib/js/jquery.nivo.slider.js",
	"/js/plugins.js",
	"/js/main.js"
];

Application::getInstance()->options = $options;
Application::getInstance()->basket = new Basket();

/*if(!strpos(Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory(), 'bitrix') )
	AddEventHandler("main", "OnEndBufferContent", ["Studio8\Main\Handlers", "removeBitrixCode"]);*/

AddEventHandler("main", "OnEndBufferContent", ["Studio8\Main\Handlers", "removeTimeStampMarker"]);