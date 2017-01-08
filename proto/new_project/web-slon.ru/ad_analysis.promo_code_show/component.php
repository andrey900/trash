<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
// echo '<pre>'; print_r($arParams); echo '</pre>';
CModule::IncludeModule('iblock');
//if ($this->StartResultCache(3600))
//{
	if(isset($_SESSION['WEBSLON_PROMO_CODE'])){
		$arResult['PROMO_CODE'] = intval($_SESSION['WEBSLON_PROMO_CODE']);
	}else{
		$arResult['PROMO_CODE'] = intval($APPLICATION->get_cookie('WEBSLON_PROMO_CODE'));
	}
	$arResult['CAPTION'] = $arParams["CAPTION"];
    // echo '<pre>'; print_r($arResult); echo '</pre>';
    $this->IncludeComponentTemplate();
//}
?>
