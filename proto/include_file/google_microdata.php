<?php extract('arResult');//p($arResult,false);
$arResult['PRICEVALIDUNTIL'] = (empty($arResult['PRICEVALIDUNTIL']))?time()+(7*24*60*60):$arResult['PRICEVALIDUNTIL'];
?>

<div itemscope itemtype="http://data-vocabulary.org/Product" class="micro_data">
  <meta itemprop="brand" content="<?=$arResult['BRAND'];?>" />
  <meta itemprop="name" content="<?=$arResult['NAME'];?>" />
  <img class="micro_data" itemprop="image" src="<?=$arResult['IMAGE'];?>" />
  <meta itemprop="category" content="<?=$arResult['CATEGORY'];?>" />
  <meta itemprop="identifier" content="sku:<?=$arResult['IDENTIFIER'];?>" />
  
  <span itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
    <span class='inform micro_data'>summary-price: <?=$arResult['PRICE'];?> <?=$arResult['PRICECURRENCY']?></span>
    <meta itemprop="priceCurrency" content="<?=$arResult['PRICECURRENCY']?>" />
    <time itemprop="priceValidUntil" datetime="<?=date('Y-m-d', $arResult['PRICEVALIDUNTIL']);?>"></time>
    <meta itemprop="seller" content="<?=$arResult['SELLER']?>" />
    <meta itemprop="price" content="<?=$arResult['PRICE'];?>" />
    <meta itemprop="condition" content="<?=$arResult['CONDITION'];?>" />
    <meta itemprop="availability" content="<?=$arResult['AVAILABILITY'];?>" />
  </span>
</div>
<script type="text/javascript">
function removeElementsByClass(className){
    var elements = document.getElementsByClassName(className);
    while(elements.length > 0){
        elements[0].parentNode.removeChild(elements[0]);
    }
}

try {
    removeElementsByClass('micro_data');
}
catch(err) {
    console.log("ERROR micro_data: "+err.message);
}
</script>
<?/*
<?$arSite = CSite::GetByID(SITE_ID)->Fetch();?>
<?$APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include/google_microdata.php",
            Array(
                "$arResult" => array("NAME"          => "",
                                    "BRAND"         => "",
                                    "IMAGE"         => "",
                                    "CATEGORY"      => "",
                                    "IDENTIFIER"    => "",
                                    "PRICECURRENCY" => "",
                                    "SELLER"        => $arSite['NAME'],
                                    "PRICEVALIDUNTIL"=>"",
                                    "PRICE"         => "",
                                    "CONDITION"     => "",
                                    "AVAILABILITY"  => "",
                                     ),
                ),
            Array("MODE"=>"php")
);?>
*/?>