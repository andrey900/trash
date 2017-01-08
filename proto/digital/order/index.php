<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
?>
<?if(isset($_REQUEST['ORDER_ID']) && $_REQUEST['ORDER_ID']>0)
{
/*?>
<script type="text/javascript">
        			var _gaq = _gaq || [];
        			_gaq.push(['_setAccount', 'UA-39695500-1']);
        			_gaq.push(['_trackPageview']);
        			_gaq.push(['_addTrans',
        			'<?=$_SESSION['BASKET_ANALYTIC']['INFO']['ORDER_ID']?>', // order ID - required
        			'<?=$_SESSION['BASKET_ANALYTIC']['INFO']['SITE_NAME']?>', // affiliation or store name
        			'<?=$_SESSION['BASKET_ANALYTIC']['INFO']['ORDER_PRICE']?>', // total - required
        			'<?=$_SESSION['BASKET_ANALYTIC']['INFO']['ORDER_TAX']?>', // tax
        			'<?=$_SESSION['BASKET_ANALYTIC']['INFO']['ORDER_DELIVERY_PRICE']?>', // shipping
        			'<?=$_SESSION['BASKET_ANALYTIC']['INFO']['CITY']?>', // city
        			'', // state or province
        			'<?=$_SESSION['BASKET_ANALYTIC']['INFO']['COUNTRY']?>' // country
        			]);
        			// add item might be called for every item in the shopping cart
        			// where your ecommerce engine loops through each item in the cart and
        			// prints out _addItem for each

        			<?foreach ($_SESSION['BASKET_ANALYTIC']['ITEMS'] as $item): ?>
        			_gaq.push(['_addItem',
        			'<?=$_SESSION['BASKET_ANALYTIC']['INFO']['ORDER_ID']?>', // order ID - required
        			'<?=$item['PRODUCT_ID']?>', // SKU/code - required
        			'<?=$item['NAME']?>', // product name
        			'<?=$item['CATEGORY']?>', // category or variation
        		/*	'<?=$item['PRICE']?>', // unit price - required
        			'<?=$item['QUANTITY']?>' // quantity - required
        			]);

        			<? endforeach;?>

        			_gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
        			(function() {
        			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        			})();

        		</script>
<?*/ }?>
<?$APPLICATION->IncludeComponent("bitrix:sale.order.ajax", "main", array(
	"PAY_FROM_ACCOUNT" => "N",
	"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
	"COUNT_DELIVERY_TAX" => "Y",
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
	"ALLOW_AUTO_REGISTER" => "Y",
	"SEND_NEW_USER_NOTIFY" => "N",
	"DELIVERY_NO_AJAX" => "Y",
	"DELIVERY_NO_SESSION" => "N",
	"TEMPLATE_LOCATION" => "popup",
	"DELIVERY_TO_PAYSYSTEM" => "d2p",
	"USE_PREPAYMENT" => "N",
	"PROP_1" => array(
	),
	"PROP_3" => array(
	),
	"PROP_2" => array(
	),
	"PROP_4" => array(
	),
	"SHOW_STORES_IMAGES" => "Y",
	"PATH_TO_BASKET" => SITE_DIR."basket/",
	"PATH_TO_PERSONAL" => SITE_DIR."personal/",
	"PATH_TO_PAYMENT" => SITE_DIR."order/payment/",
	"PATH_TO_AUTH" => SITE_DIR."auth/",
	"SET_TITLE" => "Y",
	"PRODUCT_COLUMNS" => array(
	),
	"DISABLE_BASKET_REDIRECT" => "N",
	"DISPLAY_IMG_WIDTH" => "90",
	"DISPLAY_IMG_HEIGHT" => "90"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>