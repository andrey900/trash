<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

class CCrmSoapClient {
	
	function __construct($wsdl, $options, $systemID) 
	{	
		$this->client = new SoapClient($wsdl, $options);
		$this->systemID = $systemID;
	}
	
	private function ExecuteMethod($method, $params) { $this->arResult = $this->client->$method($params);	}
	 
	private function GetMarketingInfo($getSimpleBonusRules, $getSimpleGiftRules) 
	{ 
		$method = "GetMarketingInfo";
		$params = new stdClass;
		
		$params->SystemID = $this->systemID;
		$params->GetSimpleBonusRules = $getSimpleBonusRules;
		$params->GetSimpleGiftRules = $getSimpleGiftRules;
		
		$this->ExecuteMethod($method, $params);
	}

	public function GetListAll()
	{
		$this->GetMarketingInfo(true, true);
		return $this->arResult;
	}
	
	public function GetListBonus() 
	{ 
		$this->GetMarketingInfo(true, false);
		return $this->arResult;
	}
	 
	public function GetListGift() 
	{ 
		$this->GetMarketingInfo(false, true);
		return $this->arResult;
	}
	
	public function GetLastResult() { return $this->arResult; }
}

ini_set("soap.wsdl_cache_enabled", 0);

define("CRM_CLIENT_ID", 2); // Идентификатор внешней системы. Используется для получения данных от CRM
$wsdl = "http://172.16.1.38:2270/SAN_CRM/ws/TD_LoyaltyExchange?wsdl";
$options = array();
$soapClient = new CCrmSoapClient($wsdl, $options, CRM_CLIENT_ID);

$result = $soapClient->GetListAll();
p($result->result);
//p(array("ALL" => $soapClient->GetListAll()), false);

