<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<div id="basket_preload">
<?$APPLICATION->IncludeComponent( "bitrix:sale.basket.basket.small", "normal", Array( "PATH_TO_BASKET" => SITE_DIR."basket/", "PATH_TO_ORDER" => SITE_DIR."order/" ) );?>
</div>