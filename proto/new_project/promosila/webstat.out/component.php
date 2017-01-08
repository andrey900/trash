<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//echo "<pre>"; print_r($arParams); echo "</pre>";

if(!isset($arParams["CACHE_TIME"])) $arParams["CACHE_TIME"] = 180;

if(!CModule::IncludeModule("fileman"))
{
	$this->AbortResultCache();
	return;
}

if(CModule::IncludeModule("promosila.pswebstat"))
{
	$webstat = new Ps\WebStat;
	$res = $webstat->GetAllData();
	if(!$res)
	{
		ShowError(GetMessage("WEBSTAT_NO_PROJECT"));
		$this->AbortResultCache();
		return;	
	}
	if(!array_key_exists("balance", $res["balance"]))
	{
		ShowError(GetMessage("WEBSTAT_SERVICE_ERROR"));
		echo "<br>", GetMessage("WEBSTAT_SERVICE_ERROR_MESS"), "<br>";
		foreach($res["balance"] as $key => $val) echo "<br>", $key, ": ", $val;
		$this->AbortResultCache();
		return;	
	}
	$arResult = $res;
	//$this->SetResultCacheKeys(array());	// $arResult cach off (for debugging)
	$this->IncludeComponentTemplate();
}
else
{
	ShowError(GetMessage("WEBSTAT_NO_MODULE"));
	$this->AbortResultCache();
	return;
}


?>
