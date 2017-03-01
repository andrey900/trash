<?php

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>

        <!-- START FOOTER AREA -->
        <footer id="footer" class="footer-area">
            <div class="footer-top">
                <div class="container-fluid">
                    <div class="plr-185">
                        <div class="footer-top-inner gray-bg">
                            <div class="row">
                                <div class="col-lg-5 col-md-3 col-sm-4">
                                    <div class="single-footer footer-about">
                                        <div class="footer-logo">
                                            <?$APPLICATION->IncludeFile(
                                                SITE_DIR."include/header/logo.php",
                                                Array(),
                                                Array("MODE"=>"html")
                                            );?>
                                        </div>
                                        <div class="footer-brief">
                                            <?$APPLICATION->IncludeFile(
                                                SITE_DIR."include/footer/info.php",
                                                Array(),
                                                Array("MODE"=>"html")
                                            );?>
                                        </div>
                                        <ul class="footer-social">
                                            <?$APPLICATION->IncludeFile(
                                                SITE_DIR."include/footer/social.php",
                                                Array(),
                                                Array("MODE"=>"html")
                                            );?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 hidden-sm hidden-xs">
                                    <div class="single-footer">
                                        <?$APPLICATION->IncludeComponent(
                                        	"bitrix:menu", 
                                        	"footer", 
                                        	array(
                                        		"ALLOW_MULTI_SELECT" => "N",
                                        		"CHILD_MENU_TYPE" => "footer_1",
                                        		"DELAY" => "N",
                                        		"MAX_LEVEL" => "1",
                                        		"MENU_CACHE_GET_VARS" => array(
                                        		),
                                        		"MENU_CACHE_TIME" => "3600",
                                        		"MENU_CACHE_TYPE" => "A",
                                        		"MENU_CACHE_USE_GROUPS" => "N",
                                        		"ROOT_MENU_TYPE" => "footer_1",
                                        		"USE_EXT" => "N",
                                        		"COMPONENT_TEMPLATE" => "footer"
                                        	),
                                        	false
                                        );?>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                    <div class="single-footer">
                                        <?$APPLICATION->IncludeComponent(
                                            "bitrix:menu", 
                                            "footer", 
                                            array(
                                                "ALLOW_MULTI_SELECT" => "N",
                                                "CHILD_MENU_TYPE" => "footer_2",
                                                "DELAY" => "N",
                                                "MAX_LEVEL" => "1",
                                                "MENU_CACHE_GET_VARS" => array(
                                                ),
                                                "MENU_CACHE_TIME" => "3600",
                                                "MENU_CACHE_TYPE" => "A",
                                                "MENU_CACHE_USE_GROUPS" => "N",
                                                "ROOT_MENU_TYPE" => "footer_2",
                                                "USE_EXT" => "N",
                                                "COMPONENT_TEMPLATE" => "footer"
                                            ),
                                            false
                                        );?>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
                                    <div class="single-footer">
                                        <?$APPLICATION->IncludeComponent(
                                            "bitrix:menu", 
                                            "footer", 
                                            array(
                                                "ALLOW_MULTI_SELECT" => "N",
                                                "CHILD_MENU_TYPE" => "footer_3",
                                                "DELAY" => "N",
                                                "MAX_LEVEL" => "1",
                                                "MENU_CACHE_GET_VARS" => array(
                                                ),
                                                "MENU_CACHE_TIME" => "3600",
                                                "MENU_CACHE_TYPE" => "A",
                                                "MENU_CACHE_USE_GROUPS" => "N",
                                                "ROOT_MENU_TYPE" => "footer_3",
                                                "USE_EXT" => "N",
                                                "COMPONENT_TEMPLATE" => "footer"
                                            ),
                                            false
                                        );?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom black-bg">
                <div class="container-fluid">
                    <div class="plr-185">
                        <div class="copyright">
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="copyright-text">
                                        <p><?$APPLICATION->IncludeFile(
                                            SITE_DIR."include/footer/copyright.php",
                                            Array(),
                                            Array("MODE"=>"html")
                                        );?></p>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <ul class="footer-payment text-right">
                                        <?$APPLICATION->IncludeFile(
                                            SITE_DIR."include/footer/paysystem.php",
                                            Array(),
                                            Array("MODE"=>"html")
                                        );?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- END FOOTER AREA -->

        <?$APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include/quickShowProduct.php",
            Array(),
            Array("MODE"=>"html")
        );?>
    </div>
    <!-- Body main wrapper end -->

    <?$APPLICATION->IncludeFile(
        SITE_DIR."include/footer/metric.php",
        Array(),
        Array("MODE"=>"html")
    );?>

</body>

</html>