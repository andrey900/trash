<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (isset($arParams['SHOW']) && $arParams['SHOW'] == false) return; // вызов компоненты только для подключения языкового файла
if (!CModule::IncludeModule('edost.catalogdelivery')) return;


$mode = ($arParams['MODE'] == 'window' || $arParams['MODE'] == 'inside' || $arParams['MODE'] == 'double' ? $arParams['MODE'] : '');
$param = (isset($arParams['PARAM']) && is_array($arParams['PARAM']) ? $arParams['PARAM'] : array());

// упаковка параметров в строку для расчета через ajax
$param_string = '';
$param_key = array('sort', 'minimize', 'max', 'economize', 'price_value', 'show_error', 'show_ico', 'show_day', 'location_id_default', 'currency_result', 'currency');
foreach ($param as $key => $v) if (in_array($key, $param_key)) $param_string .= $key.'('.$v.')';

// сохранение и загрузка параметра 'rename' из сессии для расчета через ajax (в отличие от остальных параметров не упаковывается в строку)
if (isset($param['rename'])) $_SESSION['EDOST']['catalogdelivery_rename'] = $param['rename'];
else if (isset($_SESSION['EDOST']['catalogdelivery_rename'])) $param['rename'] = $_SESSION['EDOST']['catalogdelivery_rename'];

//echo '<br><b>catalogdelivery component param:</b><pre>'.print_r($param, true).'</pre><br>';


if ($mode != '') {
	// расчет доставки и вывод через JS функцию  ИЛИ  только данные в JSON

	if ($mode != 'window') $mode = 'double';
	$json = (isset($arParams['JSON']) && $arParams['JSON'] == 'Y' ? true : false);
	if (!isset($param['add_cart'])) $param['add_cart'] = (isset($_COOKIE['edostcdaddcart']) && $_COOKIE['edostcdaddcart'] == 1 ? 'Y' : 'N'); // для первого расчета inside - загружается старый выбор из cookie

	$id = (isset($param['location_id']) ? intval($param['location_id']) : 0);
	$id_default = (isset($param['location_id_default']) ? intval($param['location_id_default']) : 0);

	if (CModule::IncludeModule('edost.locations') && class_exists(CLocationsEDOST) && method_exists(CLocationsEDOST, 'GetUserLocation')) $location = CLocationsEDOST::GetUserLocation(array('ID' => $id, 'default' => $id_default));
	else {
		if ($id == 0 && isset($_COOKIE['edostcdcity'])) {
			$id = explode('|', substr($_COOKIE['edostcdcity'], 0, 10));
			$id = intval($id[0]);
		}
		if ($id == 0 && isset($_COOKIE['YS_GEO_IP_LOC_ID'])) $id = intval($_COOKIE['YS_GEO_IP_LOC_ID']);
		if ($id == 0) $id = $id_default;

		$name = '';
		$country_id = 0;
		if ($id > 0) {
			$ar = CSaleLocation::GetByID($id);
			if (is_array($ar) && isset($ar['COUNTRY_NAME'])) {
				$name = (isset($ar['CITY_NAME']) ? $ar['CITY_NAME'] : '');
				if ($name == '') $name = (isset($ar['REGION_NAME']) ? $ar['REGION_NAME'] : '');
				if ($name == '') $name = (isset($ar['COUNTRY_NAME']) ? $ar['COUNTRY_NAME'] : '');
				if (isset($ar['COUNTRY_ID'])) $country_id = $ar['COUNTRY_ID'];
			}
			else $id = 0;
		}

		$location = array('ID' => $id, 'country_id' => $country_id, 'name' => $name, 'short_name' => $name);
	}

	$param['location_id'] = $location['ID'];


	// расчет доставки
	if ($param['location_id'] > 0) $r = CCatalogDeliveryEDOST::GetData($param, $mode);
    else {
    	$r = array();
    	if ($mode == 'window' || $mode == 'double') $r['window'] = array('data' => '', 'minimize' => '', 'count' => 0);
    	if ($mode == 'inside' || $mode == 'double') $r['inside'] = array('data' => '', 'minimize' => '', 'count' => 0);
    }
//	echo '<br><br><b>CatalogDelivery GetData:</b><pre>'.print_r($r, true).'</pre><br>';


	// упаковка данных в JSON
	$data = array();
	foreach ($r as $key => $v) $data[] = '"'.$key.'": {"data": "'.$v['data'].'", "minimize": "'.$v['minimize'].'"}';

	$p = array(
		'location_id' => $location['ID'],
		'country_id' => $location['country_id'],
		'location_name' => str_replace('"', '&quot;', $location['name']),
		'location_short_name' => str_replace('"', '&quot;', $location['short_name']),
		'add_cart' => $param['add_cart'],
		'param_string' => $param_string,
		'detailed' => ((isset($r['inside']['minimize']) && $r['inside']['minimize'] != '') || (isset($r['inside']['count']) && isset($r['window']['count']) && $r['inside']['count'] < $r['window']['count']) ? 'Y' : 'N'),
    );
	$ar = array();
	foreach ($p as $key => $v) $ar[] = '"'.$key.'": "'.$v.'"';
	$data[] = '"param": {'.implode(', ', $ar).'}';

	$data = '{'.implode(', ', $data).'}';


	echo ($json ? $data : '<script type="text/javascript"> edost_SetCatalogDeliveryData('.$data.'); </script>');
}
else {
	// вывод html и JS кода калькулятора (инициализация, доставка НЕ рассчитывается)

	CUtil::InitJSCore(array('ajax'));

	if ($this->StartResultCache()) {
		$arResult['COMPONENT_PATH'] = $componentPath;
		$arResult['PARAM'] = $param_string;
		if (isset($arParams['RADIUS'])) $arResult['RADIUS'] = intval($arParams['RADIUS']);
		if (isset($arParams['INFO']) && $arParams['INFO'] != '') $arResult['INFO'] = $arParams['INFO'];
		if (isset($arParams['~NO_DELIVERY_MESSAGE']) && $arParams['~NO_DELIVERY_MESSAGE'] != '') $arResult['NO_DELIVERY_MESSAGE'] = $arParams['~NO_DELIVERY_MESSAGE'];

		$arResult['FRAME_X'] = (!isset($arParams['FRAME_X']) || intval($arParams['FRAME_X']) < 400 ? 650 : intval($arParams['FRAME_X']));
		$arResult['FRAME_Y'] = (!isset($arParams['FRAME_Y']) || intval($arParams['FRAME_Y']) < 80 ? 80 : intval($arParams['FRAME_Y']));
		$arResult['FRAME_AUTO'] = (!isset($arParams['FRAME_AUTO']) || $arParams['FRAME_AUTO'] == 'Y' ? true : false);

		$arResult['EDOST_LOCATIONS'] = (isset($arParams['EDOST_LOCATIONS']) && $arParams['EDOST_LOCATIONS'] == 'N' ? 'N' : 'Y');
		if ($arResult['EDOST_LOCATIONS'] == 'Y' && !(CModule::IncludeModule('edost.locations') && class_exists(CLocationsEDOST))) $arResult['EDOST_LOCATIONS'] = 'N';

		$arResult['SHOW_QTY'] = (isset($arParams['SHOW_QTY']) && $arParams['SHOW_QTY'] == 'Y' ? 'Y' : 'N');
		$arResult['SHOW_ADD_CART'] = (isset($arParams['SHOW_ADD_CART']) && $arParams['SHOW_ADD_CART'] == 'Y' ? 'Y' : 'N');
		$arResult['SHOW_BUTTON'] = (isset($arParams['SHOW_BUTTON']) && $arParams['SHOW_BUTTON'] == 'Y' ? 'Y' : 'N');

		$v = (isset($arParams['COLOR']) ? $arParams['COLOR'] : '');
		$data = array(
			'red' => array(255, 0, 0),
			'blue' => array(160, 208, 240),
			'blue_light' => array(100, 200, 240),
			'green' => array(80, 200, 80),
			'orange' => array(255, 185, 0),
			'white' => array(230, 230, 230),
			'black' => array(80, 80, 80),
			'gray' => array(200, 200, 200),
			'clear_white' => array(255, 255, 255),
		);
        if ($v != '' && isset($data[$v])) $color = $data[$v];
		else {
			$n = strlen($v);
			if ($n == 3) $color = array(hexdec($v{0}.$v{0}), hexdec($v{1}.$v{1}), hexdec($v{2}.$v{2}));
			else if ($n == 6) $color = array(hexdec($v{0}.$v{1}), hexdec($v{2}.$v{3}), hexdec($v{4}.$v{5}));
			else $v = '';
		}
		if ($v == '') $color = $data['gray'];

		$light = ceil(0.3*$color[0] + 0.59*$color[1] + 0.11*$color[2]);

		$arResult['COLOR'] = '#'.CCatalogDeliveryEDOST::RGBtoHEX($color);
		$arResult['COLOR_SHADOW'] = '#'.CCatalogDeliveryEDOST::RGBlight($color, ($light < 90 ? 100 : -80), true);
		$arResult['COLOR_FONT'] = ($light < 160 ? '#FFF' : '#'.CCatalogDeliveryEDOST::RGBlight($color, -140, true));
		$arResult['COLOR_FONT_UP'] = '#'.CCatalogDeliveryEDOST::RGBlight($color, ($light < 150 ? 120 : -20), true);
		$arResult['COLOR_UP'] = '#'.CCatalogDeliveryEDOST::RGBlight($color, 25, true);
		$arResult['CLEAR_WHITE'] = ($light == 255 ? true : false);


		// данные для старого шаблона (до версии 1.1.0)
		if (!isset($arParams['PARAM'])) {
			if ($arResult['FRAME_Y'] < 450) $arResult['FRAME_Y'] = 450;
			$arResult['PATH_DELIVERY_IMG'] = $componentPath.'/images';
			$arResult['PATH_DELIVERY_GOODSCALC'] = $componentPath.'/edost_delivery_goodscalc.php';
			$arResult['PATH_DELIVERY_LOADCITIES'] = $componentPath.'/edost_delivery_loadcities.php';
			$arResult['PATH_DELIVERY_LOCATION'] = $componentPath.'/edost_delivery_location.php';

			// генерация списка стран
			$arResult['COUNTRY_LIST'] = array();
			$rsCountryList = CSaleLocation::GetCountryList(array('SORT' => 'ASC', 'NAME_LANG' => 'ASC'));
			while ($arCountry = $rsCountryList->GetNext()) $arResult['COUNTRY_LIST'][] = array('ID' => $arCountry['ID'], 'NAME_LANG' => $arCountry['NAME_LANG']);

			// генерация списка городов, если в местоположениях есть только одна страна
			$arResult['CITY_LIST'] = array();
			if (count($arResult['COUNTRY_LIST']) == 1) {
				$rsLocationsList = CSaleLocation::GetList(
					array('SORT' => 'ASC', 'COUNTRY_NAME_LANG' => 'ASC', 'CITY_NAME_LANG' => 'ASC', 'REGION_NAME_LANG' => 'ASC'),
					array('COUNTRY_ID' => $arResult['COUNTRY_LIST'][0]['ID'], 'LID' => LANGUAGE_ID), false, false, array('ID', 'CITY_NAME', 'REGION_NAME')
				);

				$country_ID = -1;
				while ($arCity = $rsLocationsList->GetNext()) {
					if ($arCity['CITY_NAME'] == '' && (!isset($arCity['REGION_NAME']) || $arCity['REGION_NAME'] == '')) {
						$country_ID = $arCity['ID'];
						continue;
					}

					if ($arCity['CITY_NAME'] == '') continue;
					else {
						$s = $arCity['CITY_NAME'];
						if (isset($arCity['REGION_NAME']) && $arCity['REGION_NAME'] != '')
							if (strpos($s, $arCity['REGION_NAME']) === false) $s .= ' ('.$arCity['REGION_NAME'].')';
					}

					$arResult['CITY_LIST'][] = array('ID' => $arCity['ID'], 'CITY_NAME' => $s);
				}
				$arResult['COUNTRY_ID'] = $country_ID;
			}
		}


		$this->IncludeComponentTemplate();
	}

	return $componentPath.'/images/'.(isset($arParams['IMAGE']) && $arParams['IMAGE'] != '' ? $arParams['IMAGE'] : 'delivery.png');
}
?>
