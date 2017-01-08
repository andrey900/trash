<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if( !empty($arResult['SECTION']['IBLOCK_SECTION_ID']) ){
	if( !in_array(CAniartTools::_GetFirstSection($arResult['SECTION']['IBLOCK_SECTION_ID']), $arParams['SECTIONS_ID']) ){// Если элемент не попадает под секцию выборки
		$arResult['SECTIONS'] = array();
	} else {
		$arUnsetAllBrandsElem = array();

		for ($i=1; $i < 10; $i++) { // Формирую массив элементов
			$arElems[] = $arResult['SECTION']['UF_FILTER_BRANDS_'.$i];
		}
		$arRes = CAniartTools::GetInfoElements($arElems);// Получаю данные
		$arResult['SECTION']['UF_FILTER_BRANDS_COUNT'] = ($arRes!==false)?count($arRes):0;
		if( $arRes ){
			for ($i=1; $i < 10; $i++) { // вставляю полные данные о элементе
				$arResult['SECTION']['UF_FILTER_BRANDS_'.$i] = $arRes[$arResult['SECTION']['UF_FILTER_BRANDS_'.$i]];
				$arUnsetAllBrandsElem[] = $arResult['SECTION']['UF_FILTER_BRANDS_'.$i];
			}
		}

		$arRes = CAniartTools::GetBrandsInSection($arResult['SECTION']['ID']);
		$arResult['SECTION']['ALL_BRANDS_FILTER'] = $arRes;
		foreach ($arUnsetAllBrandsElem as $value) {
			unset($arResult['SECTION']['ALL_BRANDS_FILTER'][$value['ID']]);
		}
	}
}
