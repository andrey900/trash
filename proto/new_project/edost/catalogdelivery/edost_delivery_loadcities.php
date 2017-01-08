<?
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');


$start = (isset($_POST['mode']) && $_POST['mode'] == 'start' ? true : false);
$location_id = (isset($_POST['id']) ? intval($_POST['id']) : 0);
$location_id_default = (isset($_POST['default']) ? intval($_POST['default']) : 0);
$country_id = (isset($_POST['country']) ? intval($_POST['country']) : 0);
if (isset($_POST['edost_to_country'])) $country_id = intval($_POST['edost_to_country']); // поддержка старого шаблона (до версии 1.1.0)


if (isset($_POST['edostlocations']) && $_POST['edostlocations'] == 'Y') {
	// выбор местоположений через модуль edost.locations

	$set_location = 'N';
	if ($location_id == 0 && CModule::IncludeModule('edost.locations') && class_exists(CLocationsEDOST) && method_exists(CLocationsEDOST, 'GetUserLocation')) {
		$location = CLocationsEDOST::GetUserLocation(array('default' => $location_id_default));
		if (isset($location['ID']) && $location['ID'] > 0) {
			$location_id = $location['ID'];
			$set_location = 'Y';
		}
	}
?>
	<input name="edost_country_sel" id="edost_country_sel" value="" type="hidden">
	<input name="edost_city_sel" id="edost_city_sel" value="" type="hidden">
<?
	$GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array(
		'EDOST_CATALOGDELIVERY' => 'Y', 'CODE' => 'LOCATION', 'FIELD_NAME' => 'FIELD_LOCATION',
		'NAME' => '', 'REQUIED_FORMATED' => 'NO_CHANGE', 'NAME_TAG_END' => '', 'FIELD_TAG_END' => '', 'IN_TABLE' => 'N',
		'SET_LOCATION' => $set_location, 'CITY_NOW_ID' => $location_id,
		'CACHE_TYPE' => 'A', 'CACHE_TIME' => '180', 'CACHE_GROUPS' => 'Y'
	), null, array('HIDE_ICONS' => 'Y'));
?>
<script type="text/javascript">
	edost_SetLocation(-2<?=($set_location == 'Y' ? ', true' : '')?>);
</script>
<?
}
else {
	// стандартный выбор местоположений битрикс

	if (!CModule::IncludeModule('sale')) return;

	// подключение языкового файла компоненты
	$APPLICATION->IncludeComponent('edost:catalogdelivery', '', array('SHOW' => false), null, array('HIDE_ICONS' => 'Y'));

	$set_location = false;
	if ($location_id == 0 && $country_id == 0 && $location_id_default > 0) {
		$ar = CSaleLocation::GetByID($location_id_default);
		if (is_array($ar) && isset($ar['COUNTRY_ID'])) {
			$location_id = $location_id_default;
			$country_id = $ar['COUNTRY_ID'];
			$set_location = true;
		}
	}

	// список стран
	if ($start) {
		$country = array();
		$rsCountryList = CSaleLocation::GetCountryList(array('SORT' => 'ASC', 'NAME_LANG' => 'ASC'));
		while ($v = $rsCountryList->GetNext()) $country[] = array('ID' => $v['ID'], 'NAME_LANG' => $v['NAME_LANG']);

		$n = count($country);
?>
		<? if ($n > 1) { ?>
		<select name="edost_country_sel" id="edost_country_sel" onchange="edost_LoadCities()">
			<option value="-1" style="color: #F00;"><?echo GetMessage('SAL_CHOOSE_COUNTRY')?></option>
			<? foreach ($country as $v) { ?>
			<option value="<?=$v['ID']?>"<?=($v['ID'] == $country_id ? ' selected="selected"' : '')?>><?=$v['NAME_LANG']?></option>
			<? } ?>
		</select>
		<? } else if ($n == 1) { $country_id = $country[0]['ID']; ?>
		<input name="edost_country_sel" id="edost_country_sel" value="<?=$country[0]['ID']?>" type="hidden">
		<? } else { ?>
		<input name="edost_country_sel" id="edost_country_sel" value="-1" type="hidden">
		<? } ?>
<?
	}

	// список городов и регионов, принадлежащих выбранной стране
	$id = -1;
	$city = array();
	$city_id = array();
	if ($country_id > 0) {
		$rsLocationsList = CSaleLocation::GetList(
			array('SORT' => 'ASC', 'COUNTRY_NAME_LANG' => 'ASC', 'CITY_NAME_LANG' => 'ASC', 'REGION_NAME_LANG' => 'ASC'),
			array('COUNTRY_ID' => $country_id, 'LID' => LANGUAGE_ID), false, false,
			array('ID', 'CITY_NAME', 'REGION_NAME')
		);

		$msc = $GLOBALS['APPLICATION']->ConvertCharset('Москва', 'windows-1251', SITE_CHARSET);
		$spb = $GLOBALS['APPLICATION']->ConvertCharset('Санкт-Петербург', 'windows-1251', SITE_CHARSET);

		while ($v = $rsLocationsList->GetNext()) {
			$region = (isset($v['REGION_NAME']) ? $v['REGION_NAME'] : '');

			if ($v['CITY_NAME'] == '' && $region == '') {
				$id = $v['ID'];
				continue;
			}

			if ($v['CITY_NAME'] == '') $s = $region;
			else {
				$s = $v['CITY_NAME'];
				if ($s != $msc && $s != $spb && $region != '')
					if (strpos($s, $region) === false) $s .= ' ('.$region.')';
			}

			$city[] = $s;
			$city_id[] = $v['ID'];
		}

		array_multisort($city, SORT_STRING, $city_id);
	}

	if ($start) echo '<span id="edost_cities" name="edost_cities">';

	if (count($city) > 0) { ?>
	<select name="edost_city_sel" id="edost_city_sel" onchange="edost_Calc()">
		<option value="-1" style="color: #F00;"><?=GetMessage('SAL_CHOOSE_CITY')?></option>
		<? if ($id > 0) { ?>
		<option value="<?=$id?>" style="color: #888;"><?=GetMessage('SAL_CHOOSE_CITY_OTHER')?></option>
		<? } ?>
		<? for ($i = 0; $i < count($city); $i++) { ?>
		<option value="<?=$city_id[$i]?>"<?=($set_location && $city_id[$i] == $location_id ? ' selected="selected"' : '')?>><?=$city[$i]?></option>
		<? } ?>
	</select>
	<? } else { ?>
	<input name="edost_city_sel" id="edost_city_sel" value="<?=$id?>" type="hidden">
	<? }

	if ($start) echo '</span>';
}


require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
?>