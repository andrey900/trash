<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/WholesalerProduct.php");
CModule::IncludeModule('highloadblock');
$APPLICATION->SetTitle("wholesalers");

if( isset($_POST['currency']) ){
	if( in_array($_POST['currency'], array("USD", "RUB", "BYN", "EUR")) )
		$_SESSION['BASKET']['currency'] = $_POST['currency'];
	else
		$_SESSION['BASKET']['currency'] = "RUB";

	$arItems = $_SESSION['BASKET']['items'];
	$currency = $_SESSION['BASKET']['currency'];
	if($arItems){
		$summ = 0;
		foreach ($arItems as $item) {
			$product = new WholesalerProduct($item);
			$q = $_SESSION['BASKET']['ITEMS_INFO'][$item]->quantity;
			$product->productInBasket($q, $currency);
			$p = $product->getProductForBasket();
			$_SESSION['BASKET']['ITEMS_INFO'][$item] = $p;
			$summ += $p->byCurrency->fullPrice;
		}

		$_SESSION['BASKET']['total_price'] = $summ;
	}
}

$arNews = CElectrodomTools::GetInfoElements(false, ["ID", "NAME", "PREVIEW_TEXT", "DATE_ACTIVE_FROM"], ["IBLOCK_ID" => 68, "ACTIVE_DATE" => true, "ACTIVE" => "Y"]);

if( count($arNews) > 0 ):
?>
	<div style="position:relative;"><div id="news-line-block-show">Показать новости</div></div>
	<div class="row" id="news-line-block" style="margin-bottom:15px;">
		<div class="col-md-2 hidden-xs hidden-sm news-title"><span class="hide-news-line">Скрыть новости</span></div>
		<div class="col-sm-12 col-md-10 hidden-xs" id="news-line">
			<?foreach($arNews as $news):?><div class="animated">[<?=substr($news['DATE_ACTIVE_FROM'], 0, 5);?>] <i><b><?=$news["NAME"];?>:</b></i> <?=$news["PREVIEW_TEXT"];?></div><?endforeach;?>
		</div>
		<!-- <div class="col-sm-12 col-md-10 hidden-xs" id="news-line">
		<marquee loop="infinite" scrollamount="3" behavior="altemate" direction="left" height="20"><?foreach($arNews as $news):?>[<?=substr($news['DATE_ACTIVE_FROM'], 0, 5);?>] <i><b><?=$news["NAME"];?>:</b></i> <?=$news["PREVIEW_TEXT"];?> <?endforeach;?></marquee></div> -->
	</div>
<!-- 	<script type="text/javascript">
	function animatedNews(selector){
		var newsLine = $(selector+' > .animated');
		if( newsLine.length < 1 )
			return;
		
		newsLine.eq(0).addClass('active fadeInDown');
	}
	new animatedNews('#news-line');
</script> -->
<?endif;?>

<div class="base-block">
<div class="price-download">
<?$APPLICATION->IncludeComponent("electrodom:make.prices", 
	'', 
	array(
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"IBLOCK_ID" => "66",
		"PROPERTY_CODE" => "BRAND"
	)
);
?>

<div class="container banners" style="margin-top:50px;">
	<div class="row">
		<div class="col-md-4 col-xs-6 banner-item"><a href="prices/backlight.xls" onclick="yaCounter39575035.reachGoal('banner_footer_1'); return true;"><img src="banners/1g.jpg" onmouseout="this.src='banners/1g.jpg'" onmouseover="this.src='banners/1.jpg'"></a></div>
		<div class="col-md-4 col-xs-6 banner-item"><a href="prices/kinklight.xls" onclick="yaCounter39575035.reachGoal('banner_footer_2'); return true;"><img src="banners/4g.jpg" onmouseout="this.src='banners/4g.jpg'" onmouseover="this.src='banners/4.jpg'"></a></div>
		<!-- <div class="col-md-4 col-xs-6 banner-item"><a href=""><img src="banners/3g.jpg" onmouseout="this.src='banners/3g.jpg'" onmouseover="this.src='banners/3.jpg'"></a></div> -->
	</div>
</div>


</div>

<div class="search-result" style="display:none;">
<h4>Результаты по запросу: <b><span class="query-search">3245</span></b></h4>
<p class="empty-result" style="display:none;">К сожалению по вашему запросу ничего не найдено</p>
<table class="table table-hover table-result">
	<thead>
		<tr><th>Артикул</th><th>Наименование</th><th width="95">Бренд</th><th>Кол-во</th><th width="105">МРЦ*</th><th width="105">Опт*</th><th width="150">Купить</th></tr>
	</thead>
	<tbody></tbody>
	<tfoot>
		<tr><td colspan="7">
		<small class="text-muted">*МРЦ - минимальная розничная цена на товар</small>
		<br>
		<small class="text-muted">*Опт - ваша личная цена на товар с учетом скидки, для преобретения</small>
		</td></tr>
	</tfoot>
</table>
</div>
</div>
<?
$quantity = 0;
$price = 0;
$currency = "RYB";

if( isset($_SESSION['BASKET']) ){
	$quantity = $_SESSION['BASKET']['quantity'];
	$price = number_format($_SESSION['BASKET']['total_price'], 2, '.', '');
	$currency = ($_SESSION['BASKET']['currency'])?$_SESSION['BASKET']['currency']:"RUB";
}
?>
<div class="swim-basket">
	<p><i class="glyphicon glyphicon-shopping-cart"></i> <span class="total-quantity"><?=$quantity?></span> <span class="change-currency currency"><?=$currency?></span></p>
	<p><i class="glyphicon glyphicon-usd"></i> <span class="total-price"><?=$price?></span></p>
</div>

<div class="currency-changer-block swim-currency hide">
	<form method="POST" action="/wholesalers/">
		<label><input type="radio" name="currency" value="RUB"> Российский рубль</label>
		<label><input type="radio" name="currency" value="BYN"> Беллоруский рубль</label>
		<label><input type="radio" name="currency" value="USD"> Доллар</label>
		<label><input type="radio" name="currency" value="EUR"> Евро</label>
	</form>
	<p class="hide-block-currency-change text-right"><a href="#">Скрыть &gt;</a></p>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>