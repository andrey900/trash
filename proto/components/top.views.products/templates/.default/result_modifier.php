<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach ($arResult['ELEMENTS'] as $k=>$value) {
		$arResult['ELEMENTS'][$k]['INFO_VIEWS'] = GetElementStat($k, false);
	}
?>