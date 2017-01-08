<?php
class CAniartTools
{

	public function sortableSize($inArray){
		$rrr = $inArray;
		foreach ($rrr as $key => $value) {
			$rrrr[$key] = substr(str_replace(',', '.', $value), 3);
		}
		asort($rrrr);
		$inArray = $rrrr;
		return $inArray;
	}

	public function sortableSizeName($array){
		$arTempHandbookSort = array();
		foreach ($array as $value) {
			$arTempHandbookSort[$value['ID']] = $value['NAME'];
		}
		$arTempHandbookSort = CAniartTools::sortableSize($arTempHandbookSort);
		foreach ($arTempHandbookSort as $key => $value) {
			$arTempHandbookSort[$key] = $array[$key];
		}

		return $arTempHandbookSort;
	}

}
