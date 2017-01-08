<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
// echo '<pre>'; print_r($arParams); echo '</pre>';
$module_id = "webslon.adanalysis";
CModule::IncludeModule($module_id);
CJSCore::Init("jquery");

if(isset($_SESSION['WEBSLON_PROMO_CODE'])){
	$promocode = intval($_SESSION['WEBSLON_PROMO_CODE']);
}else{
	$promocode = intval($APPLICATION->get_cookie('WEBSLON_PROMO_CODE'));
}

$arPromocode = C_WEBSLON_AD_ANALYSIS_Promo_code::GetArrayByID($promocode);
$campaign_id = C_WEBSLON_AD_ANALYSIS_Campaign::GetIdByOpenstatWordSource($arPromocode['OPENSTAT_CAMPAIGN'],$arPromocode['WORD_ID'],$arPromocode['SOURCE']);

/*
if($campaign_id != 0){
	$arCampaign = C_WEBSLON_AD_ANALYSIS_Campaign::GetArrayByID($campaign_id);
	if(isset($arCampaign["PHONE_NUMBER"]) && $arCampaign["PHONE_NUMBER"] != ""){
		$arParams["COUNTRY_CODE"] = $arCampaign["PHONE_COUNTRY_CODE"];
		$arParams["CITY_CODE"] = $arCampaign["PHONE_CITY_CODE"];
		$arParams["NUMBER"] = $arCampaign["PHONE_NUMBER"];
	}
}

//print_r($arParams);

if(!isset($arParams["NUMBER"])){
	$arParams["COUNTRY_CODE"] = COption::GetOptionString($module_id, "DEFAULT_PHONE_COUNTRY_CODE");
	$arParams["CITY_CODE"] = COption::GetOptionString($module_id, "DEFAULT_PHONE_CITY_CODE");
	$arParams["NUMBER"] = COption::GetOptionString($module_id, "DEFAULT_PHONE_NUMBER");
}
*/
$res = C_WEBSLON_AD_ANALYSIS_Campaign::getPhoneNumberArray($campaign_id);
$arParams["COUNTRY_CODE"] = $res["COUNTRY_CODE"];
$arParams["CITY_CODE"] = $res["CITY_CODE"];
$arParams["NUMBER"] = $res["NUMBER"];

if ($this->StartResultCache(false))
{
	//if(isset($_SESSION['WEBSLON_PROMO_CODE'])){
	//	$arResult['PROMO_CODE'] = intval($_SESSION['WEBSLON_PROMO_CODE']);
	//}else{
	//	$arResult['PROMO_CODE'] = intval($APPLICATION->get_cookie('WEBSLON_PROMO_CODE'));
	//}
	$arResult["COUNTRY_CODE"] = $arParams["COUNTRY_CODE"];
	$arResult["CITY_CODE"] = $arParams["CITY_CODE"];
	$arResult["NUMBER"] = $arParams["NUMBER"];
    // echo '<pre>'; print_r($arResult); echo '</pre>';
    $this->IncludeComponentTemplate();
    ?>
    <script type="text/javascript">    	
    	(function() {
		   	BX.ajax({
				url: "/bitrix/tools/webslon.adanalysis/updateCalltrackingPhoneUseTime.php"
			});		
		    setTimeout(arguments.callee, 20000);
		})();
    </script>
    <?
}
?>
