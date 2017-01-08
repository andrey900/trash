<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$orderGTM = new stdClass();
$orderGTM->id =  $arResult['ORDER']['ID'];
$orderGTM->tax = $arResult['ORDER']['TAX_VALUE'];
$orderGTM->action = 'purchase';
$orderGTM->revenue = $arResult['ORDER']['PRICE'];
$orderGTM->shipping = $arResult['ORDER']['PRICE_DELIVERY'];
$orderGTM->affiliation = 'DigitalVideo';

$arBasketItems = array();

$dbBasketItems = CSaleBasket::GetList(
        array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => $arResult['ORDER']['ID']
            ),
        false,
        false,
        array("ID", "CALLBACK_FUNC", "MODULE", 
              "PRODUCT_ID", "QUANTITY", "DELAY", 
              "CAN_BUY", "PRICE", "WEIGHT", "NAME")
    );
$arBasket = array();
while ($arItems = $dbBasketItems->Fetch())
{
	$arProduct = CIBlockExt::GetElementInfo($arItems['PRODUCT_ID']);
	$arSection = CIBlockSection::GetById($arProduct['IBLOCK_SECTION_ID'])->GetNext();

	$arBasket[$arItems['ID']]['ID'] = $arItems['PRODUCT_ID'];
	$arBasket[$arItems['ID']]['NAME'] = $arItems['NAME'];
	$arBasket[$arItems['ID']]['PRICES']['BASE']['VALUE'] = $arItems['PRICE'];
	$arBasket[$arItems['ID']]['DISPLAY_PROPERTIES']['BRAND']['LINK_ELEMENT_VALUE'][]['NAME'] = $arProduct['PROPERTY_BRAND_NAME'];
	$arBasket[$arItems['ID']]['QUANTITY'] = $arItems['QUANTITY'];
	$arBasket[$arItems['ID']]['SECTION_NAME'] = $arSection['NAME'];
}

GTMDataCollector('impressions', $arBasket, 'order');

if (!empty($arResult["ORDER"]))
{
	?>
	<script>
		Aniart.GTM.addItems(JSON.parse('<?=json_encode($GLOBALS['GTM_DATA'])?>'));
		var orderGTM = JSON.parse('<?=json_encode($orderGTM);?>');
		var basket = new Array();
		$.each(Aniart.GTM.objGTM.impressions, function(id, el){
		  if(el.list=='order'){
		    basket.push(el);
		  }
		});
		Aniart.GTM.eventsFunction.orderSend(basket, orderGTM);
	</script>
  <?/*?>
   var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-55506456-1']);
        _gaq.push(['_trackPageview']);
        _gaq.push(['_addTrans',
        	'<?=$arResult["ORDER_ID"]?>', // order ID - required
        	'fullhouse', // affiliation or store name
        	'<?=$arResult["ORDER"]["PRICE"]?>', // total - required
        	'', // tax
        	'<?=$arResult["ORDER"]["DELIVERY_PRICE"]?>', // shipping
        ]);
        // add item might be called for every item in the shopping cart
        // where your ecommerce engine loops through each item in the cart and
        // prints out _addItem for each

        <?foreach ($arBasketItems as $item): ?>
        	_gaq.push(['_addItem',
        		'<?=$arResult["ORDER_ID"]?>', // order ID - required
        		'<?=$item['PRODUCT_ID']?>', // SKU/code - required
        		'<?=$item['NAME']?>', // product name
        		'', // category or variation
        		'<?=$item['PRICE']?>', // unit price - required
        		'<?=$item['QUANTITY']?>' // quantity - required
        	]);

        <? endforeach;?>

        _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers      		
  <?*/?>  		
 
	<table class="sale_order_full_table">
		<tr>
			<td>
				<?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]))?>
				<br /><br />
				<?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?>
			</td>
		</tr>
	</table>
	<?
	if (!empty($arResult["PAY_SYSTEM"]))
	{
		?>
		<br />

		<table class="sale_order_full_table">
			<tr>
				<td class="ps_logo">
                                    <div class="pay_name">
                                        <?=GetMessage("SOA_TEMPL_PAY")?>: 
                                        <strong><?=$arResult["PAY_SYSTEM"]["NAME"] ?></strong>
                                    </div>
				</td>
			</tr>
			<?
			if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
			{
				?>
				<p>Счет будет выслан Вам на указаный email при оформлении заказа.</p>
				<?/*?>
				<tr>
					<td>
						<?
						if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
						{
							?>
							<script language="JavaScript">
								window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
							</script>
							<?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
							<?
							if (CSalePdf::isPdfAvailable())
							{
								?><br />
								<?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$arResult["ORDER_ID"]."&pdf=1")) ?>
								<?
							}
						}
						else
						{
							if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
							{
								//include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
							}
						}
						?>
					</td>
				</tr>
				<?*/
			}
			?>
		</table>
		<?
	}
}
else
{
	?>
	<b><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></b><br /><br />

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ORDER_ID"]))?>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>
	<?
}
?>
