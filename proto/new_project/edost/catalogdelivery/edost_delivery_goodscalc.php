<?
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');


$param = array();

$mode = (isset($_POST['mode']) ? preg_replace("/[^a-z|]/i", "", substr($_POST['mode'], 0, 10)) : '');
$location_id = (isset($_POST['id']) ? intval($_POST['id']) : 0);
$product_id = (isset($_POST['product']) ? intval($_POST['product']) : 0);
$add_cart = (isset($_POST['addcart']) && $_POST['addcart'] == '1' ? 'Y' : '');
$quantity = (isset($_POST['quantity']) && intval($_POST['quantity']) > 0 ? intval($_POST['quantity']) : 1);

// поддержка старого шаблона (до версии 1.1.0)
if (isset($_POST['edost_to_city'])) $location_id = intval($_POST['edost_to_city']);
if (isset($_POST['edost_goods_id'])) $product_id = intval($_POST['edost_goods_id']);
if (isset($_POST['edost_add_cart'])) $add_cart = ($_POST['edost_add_cart'] == '1' ? 'Y' : '');
if (isset($_POST['edost_qty'])) $quantity = (intval($_POST['edost_qty']) > 0 ? intval($_POST['edost_qty']) : 1);

// распаковка параметров
$data = (isset($_POST['param']) ? preg_replace("/[^a-z0-9()_|:]/i", "", substr($_POST['param'], 0, 255)) : '');
if ($data != '') {
	$param_key = array('sort', 'minimize', 'max', 'economize', 'price_value', 'show_error', 'show_ico', 'show_day', 'location_id_default', 'currency_result', 'currency');
	$data = explode(')', $data);
	foreach ($data as $v) if ($v != '') {
		$ar = explode('(', $v);
		if ($ar[0] != '' && isset($ar[1]) && in_array($ar[0], $param_key)) $param[$ar[0]] = $ar[1];
	}
}

$param['location_id'] = $location_id;
$param['product_id'] = $product_id;
$param['add_cart'] = $add_cart;
$param['quantity'] = $quantity;


if ($mode != '') {
	// расчет доставки и вывод в JSON
	$APPLICATION->IncludeComponent('edost:catalogdelivery', '', array('MODE' => $mode, 'JSON' => 'Y', 'PARAM' => $param), null, array('HIDE_ICONS' => 'Y'));
}
else {
	// расчет доставки в вывод для старого шаблона (до версии 1.1.0)
	if (!CModule::IncludeModule('edost.catalogdelivery')) return;

	$r = CCatalogDeliveryEDOST::GetData($param);

	if (!isset($r['tariff'])) $arResult = array();
	else {
		// подключение языкового файла компоненты
		$APPLICATION->IncludeComponent('edost:catalogdelivery', '', array('SHOW' => false), null, array('HIDE_ICONS' => 'Y'));

		$arResult['ORDER_WEIGHT'] = $r['weight'];
		$arResult['ORDER_PRICE'] = $r['price'];
		$arResult['USER_VALS']['DELIVERY_LOCATION'] = $location_id;
		$arResult['USER_VALS']['DELIVERY_LOCATION_ZIP'] = '';
		$arResult['BASE_LANG_CURRENCY'] = CSaleLang::GetLangCurrency(SITE_ID);

		$ar = array();
		foreach ($r['tariff'] as $v) {
			if (isset($v['profile'])) {
				if (!isset($ar[$v['ID']]))
					$ar[$v['ID']] = array(
						'SID' => $v['ID'],
						'TITLE' => ($v['ID'] != 'edost' ? $v['company'] : ''),
						'DESCRIPTION' => '',
					);

				$profile = array(
					'SID' => $v['profile'],
					'TITLE' => $v['name'],
					'DESCRIPTION' => $v['description'],
					$profile['day'] = '',
				);

				if ($v['ID'] == 'edost') {
					$profile['TITLE'] = $v['company'].($v['name'] != '' ? ' ('.$v['name'].')' : '');
					$profile['price'] = ($v['price_formatted'] == '0' ? SaleFormatCurrency(0, $arResult['BASE_LANG_CURRENCY']) : $v['price_formatted']);
					$profile['day'] = $v['day'];
				}

				$ar[$v['ID']]['PROFILES'][$v['profile']] = $profile;
			}
			else {
				$ar[$v['ID']] = array(
					'ID' => $v['ID'],
					'NAME' => $v['company'],
					'DESCRIPTION' => $v['description'],
					'PRICE' => $v['price'],
					'PRICE_FORMATED' => $v['price_formatted'],
					'PERIOD_TEXT' => $v['day'],
				);
			}
	    }
		$arResult['DELIVERY'] = $ar;
	}

	include($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/edost/catalogdelivery/delivery.php');
}


require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
?>