<!DOCTYPE html>
<?php

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc; 

Loc::loadMessages(__FILE__); 
?>
<html class="no-js" lang="en" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>
        <?$APPLICATION->ShowTitle()?>
        <?=$APPLICATION->GetPageProperty("title");?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?$APPLICATION->ShowMeta("og:title");
    $APPLICATION->ShowMeta("og:type");
    $APPLICATION->ShowMeta("og:url");
    $APPLICATION->ShowMeta("og:image");
    $APPLICATION->ShowMeta("og:site_name");
    $APPLICATION->ShowMeta("og:description");?>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="img/icon/favicon.png">

    <?foreach (Application::getInstance()->options->usesCssFiles as $cssPath):
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.$cssPath);
    endforeach;?>

    <script src="<?=SITE_TEMPLATE_PATH?>/js/vendor/jquery-3.1.1.min.js"></script>

    <?foreach (Application::getInstance()->options->usesJsFiles as $jsPath):
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.$jsPath);
    endforeach;?>

    <?$APPLICATION->ShowHead();?>
<meta name="yandex-verification" content="828022141770f07b" />
<meta name="google-site-verification" content="-eMCaLTZdWdYXw197YyLC-4NlGbBxTNdBCyuZXc20rA" />
</head>

<body>
<!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->  
<?$APPLICATION->ShowPanel();?>
<!-- Body main wrapper start -->
<div class="wrapper">
<!-- START HEADER AREA -->
<header class="header-area header-wrapper">
    <?if(SHOW_HEADER_TOPBAR):?>
    <!-- header-top-bar -->
    <div class="header-top-bar plr-185">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <div class="call-us">
                        <p class="mb-0 roboto">
                            <i class="zmdi zmdi-phone"></i>
                            <?$APPLICATION->IncludeFile(
                                SITE_DIR."include/header/phone.php",
                                Array(),
                                Array("MODE"=>"html")
                            );?>
                        </p>
                    </div>
                </div>
                <div class="col-sm-6 hidden-xs">
                    <div class="top-link clearfix">
                        <ul class="link f-right">
                            <li>
                                <a href="my-account.html">
                                    <i class="zmdi zmdi-account"></i>
                                    My Account
                                </a>
                            </li>
                            <li>
                                <a href="wishlist.html">
                                    <i class="zmdi zmdi-favorite"></i>
                                    Wish List (0)
                                </a>
                            </li>
                            <li>
                                <a href="login.html">
                                    <i class="zmdi zmdi-lock"></i>
                                    Login
                                </a>
                            </li>
                            <li>
                                <a href="login.html">
                                    Login
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?endif;?>
    <!-- header-middle-area -->
    <div id="sticky-header" class="header-middle-area plr-185">
        <div class="container-fluid">
            <div class="full-width-mega-dropdown">
                <div class="row">
                    <!-- logo -->
                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="logo">
                            <a href="/">
                                <?$APPLICATION->IncludeFile(
                                    SITE_DIR."include/header/logo.php",
                                    Array(),
                                    Array("MODE"=>"html")
                                );?>
                            </a>
                        </div>
                    </div>
                    <!-- primary-menu -->
                    <div class="col-md-5 hidden-sm hidden-xs">
                        <?$APPLICATION->IncludeComponent(
                            "bitrix:menu", 
                            "header", 
                            array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "2",
                                "MENU_CACHE_GET_VARS" => array(
                                ),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "A",
                                "MENU_CACHE_USE_GROUPS" => "N",
                                "ROOT_MENU_TYPE" => "top",
                                "USE_EXT" => "Y",
                                "COMPONENT_TEMPLATE" => "header"
                            ),
                            false
                        );?>
                    </div>
                    <div class="col-md-3 hidden-sm hidden-xs">
                        <div class="header-phone text-center">
                        <i class="zmdi zmdi-phone"></i>
                        <?$APPLICATION->IncludeFile(
                                SITE_DIR."include/header/phone.php",
                                Array(),
                                Array("MODE"=>"html")
                            );?>
                        </div>
                    </div>
                    <!-- header-search & total-cart -->
                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="search-top-cart  f-right">
                            <!-- header-search -->
<?$APPLICATION->IncludeComponent(
	"bitrix:search.title", 
	"header", 
	array(
		"COMPONENT_TEMPLATE" => "header",
		"NUM_CATEGORIES" => "1",
		"TOP_COUNT" => "60",
		"ORDER" => "date",
		"USE_LANGUAGE_GUESS" => "Y",
		"CHECK_DATES" => "N",
		"SHOW_OTHERS" => "N",
		"PAGE" => "#SITE_DIR#search/index.php",
		"SHOW_INPUT" => "Y",
		"INPUT_ID" => "title-search-input",
		"CONTAINER_ID" => "title-search",
		"CATEGORY_0_TITLE" => "",
		"CATEGORY_0" => array(
			0 => "iblock_catalog",
		),
		"CATEGORY_0_iblock_catalog" => array(
			0 => "4",
		)
	),
	false
);?>
                            <!-- total-cart -->
                            <div class="total-cart f-left" id="small-basket">
                                <div class="total-cart-in">
                                    <div class="cart-toggler">
                                        <a href="javascript:void(0);" class="show-cart">
                                            <span class="cart-quantity"><?=Application::getInstance()->basket->getCountGoods();?></span><br>
                                            <span class="cart-icon">
                                                <i class="zmdi zmdi-shopping-cart-plus"></i>
                                            </span>
                                        </a>                            
                                    </div>
                                    <ul>
                                        <li class="loader-in-basket">
                                            <img src="/local/templates/main/img/others/loader.gif">
                                        </li>
                                        <li>
                                            <div class="top-cart-inner your-cart">
                                                <h5 class="text-capitalize">Корзина</h5>
<a class="close-cart" href="javascript:void(0);">
<i class="zmdi zmdi-close"></i>
</a>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="total-cart-pro"></div>
                                        </li>
                                        <li>
                                            <div class="top-cart-inner subtotal">
                                                <h4 class="text-uppercase g-font-2">
                                                    Общая сумма:
                                                    <span></span>
                                                </h4>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="top-cart-inner view-cart">
                                                <h4 class="text-uppercase">
                                                    <a href="/basket/">Перейти в корзину</a>
                                                </h4>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="top-cart-inner check-out">
                                                <h4 class="text-uppercase">
                                                    <a href="/basket/#checkout">Оформить заказ</a>
                                                </h4>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END HEADER AREA -->

<!-- START MOBILE MENU AREA -->
        <div class="mobile-menu-area hidden-lg hidden-md">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="mobile-menu">
                            <?$APPLICATION->IncludeComponent(
                                "bitrix:menu", 
                                "header-mobile", 
                                array(
                                    "ALLOW_MULTI_SELECT" => "N",
                                    "CHILD_MENU_TYPE" => "",
                                    "DELAY" => "N",
                                    "MAX_LEVEL" => "2",
                                    "MENU_CACHE_GET_VARS" => array(
                                    ),
                                    "MENU_CACHE_TIME" => "3600",
                                    "MENU_CACHE_TYPE" => "A",
                                    "MENU_CACHE_USE_GROUPS" => "N",
                                    "ROOT_MENU_TYPE" => "top",
                                    "USE_EXT" => "Y",
                                    "COMPONENT_TEMPLATE" => "header"
                                ),
                                false
                            );?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MOBILE MENU AREA -->

<?if(Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory() != ''):?>
<?$APPLICATION->IncludeComponent(
    "bitrix:breadcrumb", 
    "main", 
    Array(
        "PATH" => "",
        "SITE_ID" => SITE_ID,
        "START_FROM" => "0",
    ),
    false
);?>
<?endif;?>