<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION;
global $USER;
CJSCore::Init(array("popup"));

if($arParams['ALLOW_SOCSERV_USER_AUTHORIZATION'] != 'N' && $arParams['ALLOW_SOCSERV_USER_AUTHORIZATION']){

	/*soc*/
	$arParamsToDelete = array(
			"login",
			"logout",
			"register",
			"forgot_password",
			"change_password",
			"confirm_registration",
			"confirm_code",
			"confirm_user_id",
			"logout_butt",
	);
	
	if(defined("AUTH_404"))
		$arResult["AUTH_URL"] = htmlspecialcharsback(POST_FORM_ACTION_URI);
	else
		$arResult["AUTH_URL"] = $APPLICATION->GetCurPageParam("login=yes", $arParamsToDelete);
	
	$arResult["POST"] = array();
	foreach($_POST as $vname=>$vvalue)
	{
		if(!array_key_exists($vname, $arVarExcl))
		{
			if(!is_array($vvalue))
			{
				$arResult["POST"][htmlspecialcharsbx($vname)] = htmlspecialcharsbx($vvalue);
			}
			else
			{
				foreach($vvalue as $k1 => $v1)
				{
					if(is_array($v1))
					{
						foreach($v1 as $k2 => $v2)
						{
							if(!is_array($v2))
								$arResult["POST"][htmlspecialcharsbx($vname)."[".htmlspecialcharsbx($k1)."][".htmlspecialcharsbx($k2)."]"] = htmlspecialcharsbx($v2);
						}
					}
					else
					{
						$arResult["POST"][htmlspecialcharsbx($vname)."[".htmlspecialcharsbx($k1)."]"] = htmlspecialcharsbx($v1);
					}
				}
			}
		}
	}
	
	$arResult["BACKURL"] = $APPLICATION->GetCurPageParam("", $arParamsToDelete);
	$arResult["AUTH_SERVICES"] = false;
	$arResult["CURRENT_SERVICE"] = false;
	$arResult["FOR_INTRANET"] = false;
	$arResult["ALLOW_SOCSERV_AUTHORIZATION"] = (COption::GetOptionString("main", "allow_socserv_authorization", "Y") != "N" ? "Y" : "N");
	
	if(!$USER->IsAuthorized() && CModule::IncludeModule("socialservices") && ($arResult["ALLOW_SOCSERV_AUTHORIZATION"] == 'Y'))
	{
		$oAuthManager = new CSocServAuthManager();
		$arServices = $oAuthManager->GetActiveAuthServices(array(
				'BACKURL' => $arResult['BACKURL'],
				'FOR_INTRANET' => $arResult['FOR_INTRANET'],
		));
	
		if(!empty($arServices))
		{
			$arResult["AUTH_SERVICES"] = $arServices;
			if(isset($_REQUEST["auth_service_id"]) && $_REQUEST["auth_service_id"] <> '' && isset($arResult["AUTH_SERVICES"][$_REQUEST["auth_service_id"]]))
			{
				$arResult["CURRENT_SERVICE"] = $_REQUEST["auth_service_id"];
				if(isset($_REQUEST["auth_service_error"]) && $_REQUEST["auth_service_error"] <> '')
				{
					$arResult['ERROR_MESSAGE'] = $oAuthManager->GetError($arResult["CURRENT_SERVICE"], $_REQUEST["auth_service_error"]);
				}
				elseif(!$oAuthManager->Authorize($_REQUEST["auth_service_id"]))
				{
					$ex = $APPLICATION->GetException();
					if ($ex)
						$arResult['ERROR_MESSAGE'] = $ex->GetString();
				}
			}
		}
		?>
			<div class="reviews_soc_services">
				Для того чтобы оставить отзыв, пожалуйста, <a href="javascript:void(0)" onclick="$('#loginPopup').click()">авторизуйтесь</a>! <br />
				<?
				$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
						array(
								"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
								"CURRENT_SERVICE" => $arResult["CURRENT_SERVICE"],
								"AUTH_URL" => $arResult["AUTH_URL"],
								"POST" => $arResult["POST"],
								"SHOW_TITLES" => 'N',//$arResult["FOR_INTRANET"]?'N':'Y',
								"FOR_SPLIT" => 'Y',//$arResult["FOR_INTRANET"]?'Y':'N',
								"AUTH_LINE" => 'N'// $arResult["FOR_INTRANET"]?'N':'Y',
						),
						false,
						array("HIDE_ICONS"=>"Y")
				);
				?>
				* все отзывы проходят модерацию.
			</div>
		<?
	}
	/*end soc*/
}