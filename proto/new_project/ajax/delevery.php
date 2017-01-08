<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$locID = $_POST['ORDER_PROP_3'];
CModule::IncludeModule("sale");

$isMoscow = false;
if($arLocs = CSaleLocation::GetByID($locID, LANGUAGE_ID))
{
	  $isMoscow = (stripos($arLocs['CITY_NAME'],'москва')!==false);
}

if($isMoscow)
{

	$arDeliv = CSaleDelivery::GetByID(2);
	if ($arDeliv)
	{
     //   echo "Доставка по Москве стоит ".CurrencyFormat($arDeliv["PRICE"], $arDeliv["CURRENCY"]);

	?>
	<?/*<div id="deliveryRun">     */?>
		<b>Служба доставки</b>
	<table class="sale_order_full_table">
						<tbody><tr>
					<td colspan="2">
					<table cellspacing="0" cellpadding="3" border="0">
													<tbody><tr>
								<td width="0%" valign="top">
									<input type="hidden" checked="checked" value="2" name="DELIVERY_ID_HIDE" id="ID_DELIVERY">
								</td>
								<td width="50%" valign="top">
									<label for="ID_DELIVERY">
										<small><b>Курьерская доставка по Москве</b></small>
									</label>
								</td>
								<td align="right" width="50%" valign="top">
                                   <?=CurrencyFormat($arDeliv["PRICE"], $arDeliv["CURRENCY"])?>
								</td>
							</tr>
													</tbody>
					</table>
					</td>
				</tr>
					</tbody></table>
	<br><br>
<?/*	</div> */?>
	<?
	}
}
else
{
       $_REQUEST['confirmorder'] 	  = $_POST['confirmorder'] 	 	= 'Y';
       $_REQUEST['sessid'] 			  = $_POST['sessid'] 			= bitrix_sessid();
	   $_SERVER["REQUEST_METHOD"] 	  ='POST';

	   $APPLICATION->IncludeComponent("bitrix:sale.order.ajax", "onlydelivery", array(
	   "PAY_FROM_ACCOUNT" => "Y",
	   "COUNT_DELIVERY_TAX" => "Y",
	   "COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
	   "ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
	   "ALLOW_AUTO_REGISTER" => "Y",
	   "SEND_NEW_USER_NOTIFY" => "Y",
	   "DELIVERY_NO_AJAX" => "Y",
	   "DELIVERY_NO_SESSION" => "N",
	   "TEMPLATE_LOCATION" => "popup",
	   "DELIVERY_TO_PAYSYSTEM" => "p2d",
	   "USE_PREPAYMENT" => "N",
	   "PROP_1" => array(
	   ),
	   "PATH_TO_BASKET" => "basket.php",
	   "PATH_TO_PERSONAL" => "index.php",
	   "PATH_TO_PAYMENT" => "payment.php",
	   "PATH_TO_AUTH" => "/auth/",
	   "SET_TITLE" => "Y"
	   ),
	   false
	   );
}
?><?
 /*  CModule::IncludeModule("sale");
   CModule::IncludeModule("catalog");

$arOrder = array(
  "WEIGHT" => "10", // вес заказа в граммах
  "PRICE" => "1000", // стоимость заказа в базовой валюте магазина
  "LOCATION_FROM" => COption::GetOptionInt('sale', 'location'), // местоположение магазина
  "LOCATION_TO" => 823, // местоположение доставки
);
   echo '<pre>'.print_r( COption::GetOptionInt('sale', 'location'),1).'</pre>'.__FILE__.' # '.__LINE__;
$currency = CSaleLang::GetLangCurrency(SITE_ID);
$dbHandler = CSaleDeliveryHandler::GetBySID('ems');

if ($arHandler = $dbHandler->Fetch())
{


  $arProfiles = CSaleDeliveryHandler::GetHandlerCompability($arOrder, $arHandler);

  if (is_array($arProfiles) && count($arProfiles) > 0)
  {

    $arProfiles = array_keys($arProfiles);
    $arReturn = CSaleDeliveryHandler::CalculateFull(
      'ems', // идентификатор службы доставки
      $arProfiles[0], // идентификатор профиля доставки
      $arOrder, // заказ
      $currency // валюта, в которой требуется вернуть стоимость
    );


    if ($arReturn["RESULT"] == "OK")
    {
      ShowNote('Стоимость доставки успешно рассчитана!');
      echo 'Стоимость доставки: '.CurrencyFormat($arReturn["VALUE"], $currency).'<br />';
      if (is_set($arReturn['TRANSIT']) && $arReturn['TRANSIT'] > 0)
      {
        echo 'Длительность доставки: '.$arReturn['TRANSIT'].' дней.<br />';
      }
    }
    else
    {
      ShowError('Не удалось рассчитать стоимость доставки! '.$arResult['ERROR']);
    }
  }
  else
  {
    ShowError('Невозможно доставить заказ!');
  }
}
else
{
  ShowError('Обработчик не найден!');
}

?>

								<?  /*
									$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
										"NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
										"DELIVERY" => $delivery_id,
										"PROFILE" => $profile_id,
										"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
										"ORDER_PRICE" => $arResult["ORDER_PRICE"],
										"LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
										"LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
										"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
									), null, array('HIDE_ICONS' => 'Y'));
							   */	?>