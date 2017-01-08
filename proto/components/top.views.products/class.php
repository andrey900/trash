<?php
use \Bitrix\Main;
use Bitrix\Main\Config;
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\SystemException as SystemException;
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


class TopViewsProductsComponent extends CBitrixComponent
{
	
	private $arElementsId = array();
	private $obCache;
	private $cache_dir;
	private $useCache = true;
	private $initObCache = false;
	
	public function __construct($component = null)
	{
		parent::__construct($component);
		Loc::loadMessages(__FILE__);
	}

	public function onPrepareComponentParams($arParams)
	{
		global $APPLICATION;

		$this->tryParseInt($arParams["CACHE_TIME"], 3600);
		$this->tryParseInt($arParams['IBLOCK_ID'], SHARE_CATALOG_IBLOCK_ID);
		$this->tryParseInt($arParams['COUNT_ELEM'], 3);
		$this->tryParseInt($arParams['PERIOD'], 7);
		$this->tryParseInt($arParams["IBLOCK_SECTION_ID"], 0);
		$this->tryParseInt($arParams["ELEMENT_ID"], 0);

		$arParams['CACHE_GROUPS'] = trim($arParams['CACHE_GROUPS']);
		if ('N' != $arParams['CACHE_GROUPS'])
			$arParams['CACHE_GROUPS'] = 'Y';
		
		$this->tryParseString($arParams["TOTAL_STATISTICS"], 'N');

		return $arParams;
	}

	/**
	 * Function reduces input value to integer type, and, if gets null, passes the default value
	 * @param mixed $fld Field value
	 * @param int $default Default value
	 * @return int Parsed value
	 */
	public static function tryParseInt(&$fld, $default)
	{
		$fld = intval($fld);
		if(!$fld && isset($default))
			$fld = $default;
			
		return $fld;
	}

	/**
	 * Function processes string value and, if gets null, passes the default value to it
	 * @param mixed $fld Field value
	 * @param string $default Default value
	 * @return string Parsed value
	 */
	public static function tryParseString(&$fld, $default)
	{
		$fld = trim((string)$fld);
		if(!strlen($fld) && isset($default))
			$fld = htmlspecialcharsbx($default);

		return $fld;
	}
	
	/**
	 * Create SQL query string
	 * @return string
	 */
	private function _crSQL(){
		
		$period = "=0";
		$element = '';

		if( $this->arParams['TOTAL_STATISTICS']!='Y' )
			$period = ">DATE_FORMAT(DATE_SUB(NOW(), INTERVAL {$this->arParams['PERIOD']} DAY), '%Y%m%d')";			

		if( $this->arParams['ELEMENT_ID'] > 0)
			$element = " AND a_stat.ELEMENT_ID!={$this->arParams['ELEMENT_ID']} ";

		if( $this->arParams["IBLOCK_SECTION_ID"]>0 ){
			
			$sectionId = "b_iblock_element.IBLOCK_SECTION_ID in ( {$this->arParams['IBLOCK_SECTION_ID']}, ";
			
			$res = CIBlockSection::GetByID($this->arParams["IBLOCK_SECTION_ID"]);
			
			if($ar_res = $res->GetNext()){
	  			
	  			if($ar_res['LEFT_MARGIN']+1 != $ar_res['RIGHT_MARGIN']){
				   $arFilter = array('IBLOCK_ID' => $this->arParams['IBLOCK_ID'],'>=LEFT_MARGIN' => $ar_res['LEFT_MARGIN'],'<RIGHT_MARGIN' => $ar_res['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $ar_res['DEPTH_LEVEL']); // выберет потомков без учета активности
				   $res = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter, false, array('ID'));
				   while ($arSect = $res->GetNext())
				   {
				       $sectionId .= $arSect['ID'].", ";
				   }
	  			}
			}

			$sectionId = substr($sectionId,0,-2).') AND ';
			$section = "INNER JOIN b_iblock_element ON a_stat.ELEMENT_ID=b_iblock_element.ID WHERE {$sectionId}";
			//$section = "INNER JOIN b_iblock_element ON a_stat.ELEMENT_ID=b_iblock_element.ID WHERE b_iblock_element.IBLOCK_SECTION_ID = {$this->arParams['IBLOCK_SECTION_ID']} AND";
		}else
			$section = 'WHERE';

		$sql = "SELECT ELEMENT_ID, sum(DAY_COUNT) as sumviews FROM
		a_stat ". $section ." DAY_CALS".$period.$element." GROUP BY
		ELEMENT_ID ORDER BY
		sumviews DESC LIMIT {$this->arParams['COUNT_ELEM']};";

		return $sql;
	}

	/**
	 * Show statistics - max viewed products
	 * @return this
	 */
	protected function getViewedProducts()
	{
		global $DB;
	/*	
		if( $this->arParams['PERIOD']<=0 || $this->arParams['COUNT_ELEM']<=0 )
			$this->checkInputData();
	*/	

		$memcache = CacheEngineMemcache::getInstance();
		//$memcache = new Memcache;
		//$memcache->connect('localhost', 11211);
		
		$cache_id = md5(serialize($this->arParams));

		//$arRes = $memcache->get($cache_id.'arRes');
		$arRes = $memcache->read($cache_id.'arRes');
		if( empty($arRes) || $_GET['clear_cache']=='Y' || !$this->useCache || $this->arParams['CACHE_TYPE']=='N'){
			$arRes = array();

			$sql = $this->_crSQL();

			$res = $DB->Query($sql);
		
			while($result = $res->GetNext()){
				$arRes[$result['ELEMENT_ID']] = array('ID'	 =>$result['ELEMENT_ID'],
													  'VIEWS'=>$result['sumviews'] );
				$this->arElementsId[] = $result['ELEMENT_ID'];
			}
			
			$memcache->write($cache_id.'arRes', $arRes);
			$memcache->write($cache_id.'arElementsId', $this->arElementsId);
			//$memcache->set($cache_id.'arRes', $arRes, false, $this->arParams['CACHE_TIME']);
			//$memcache->set($cache_id.'arElementsId', $this->arElementsId, false, $this->arParams['CACHE_TIME']);
		}
		
		$this->arResult['ELEMENTS'] = $arRes;
		
		if( empty($this->arElementsId) )
			$this->arElementsId = $memcache->read($cache_id.'arElementsId');

		unset($arRes);unset($result);unset($res);
		return $this;
	}
	
	/**
	 * Create special array elements ID
	 * @return this
	 */
	private function createArIdProducts()
	{
		if( empty($arElementsId) ){
			foreach ($this->arResult['ELEMENTS'] as $k => $v) {
				$this->arElementsId[] = $k;
			}
		}
		return $this;
	}
	
	/**
	 * Get info from element
	 * @return this
	 */
	protected function getElementsInfo(){
		
		if( empty($this->arElementsId) )
			$this->createArIdProducts();

		if( empty($this->arElementsId) )
			return $this;

		$data = $this->getCacheData($this->arElementsId);

		if( !empty($data) ){
			$this->arResult['ELEMENTS'] = $data['ELEMENTS'];
			return $this;
		}

		$arSelect = Array("ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION_ID", "DETAIL_PICTURE","DETAIL_TEXT", 'DETAIL_PAGE_URL','PROPERTY_MAXIMUM_PRICE', /*"PROPERTY_*"*/);
		$arFilter = Array("IBLOCK_ID"=>IntVal($this->arParams['IBLOCK_ID']), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'ID'=>$this->arElementsId);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		while($arFields = $res->GetNext()){
			$this->arResult['ELEMENTS'][$arFields['ID']] = array_merge($arFields, $this->arResult['ELEMENTS'][$arFields['ID']]);
		}

		$this->setCacheData($this->arElementsId, $this->arResult['ELEMENTS'], 'ELEMENTS');

		return $this;
	}
	
	/**
	 * Load language file.
	 */
	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
		//throw new SystemException(Loc::getMessage("CVP_ACTION_PRODUCT_ID_REQUIRED"));
	}
	
	/**
	 * Cache function
	 * Cache mechanism initialization
	 * @return this
	 */
	protected function initObCache(){
		$this->obCache = new CPHPCache;
		if( $_GET['clear_cache']=='Y' || $this->arParams['CACHE_TYPE'] == 'N')
			$this->useCache = false;
		
		$this->cache_dir = "/cache/aniart/";
		$this->initObCache = true;

		return $this;
	}
	/**
	 * Obtaining data from the cache
	 * @param array
	 * @return array
	 */
	protected function getCacheData($arPar){
		if( !$this->initObCache )
			$this->initObCache();

		if( !$this->useCache )
			return array();
		
		$cache_id = md5(serialize($this->arParams).serialize($arPar));

		if( $this->obCache->InitCache($this->arParams['CACHE_TIME'], $cache_id, $this->cache_dir) )
			return $this->obCache->GetVars();// Извлечение переменных из кэша
		else
			return array();
	}
	/**
	 * Push data from the cache
	 * @param array(init cache id)
	 * @param array(data)
	 * @param array(name)
	 * @return array
	 */
	protected function setCacheData($arPar, $saveData, $name){
		if( !$this->initObCache )
			$this->initObCache();

		$cache_id = md5(serialize($this->arParams).serialize($arPar));

		$this->obCache->InitCache($this->arParams['CACHE_TIME'], $cache_id, $this->cache_dir);

		if( $this->obCache->StartDataCache()  )
		{
			$this->obCache->EndDataCache(array($name => $saveData));
		}
	}
	/**
	 * Clear data from the cache
	 * @param array(init cache id)
	 * @return this
	 */
	protected function cleanCacheData($arPar){
		$cache_id = md5(serialize($this->arParams).serialize($arPar));
		CPHPCache::Clean($cache_id, $this->cache_dir);
		return $this;
	}

	/**
	 * Extract data from cache. No action by default.
	 * @return bool
	 */
	protected function extractDataFromCache()
	{
		return false;
	}
	
	protected function putDataToCache()
	{
	}
	
	protected function abortDataCache()
	{
	}
	
	/**
	 * Start Component
	 */
	public function executeComponent()
	{
		global $APPLICATION;
		try
		{	
			//$this->putDataToCache();
			//$this->initObCache();
			$this->getViewedProducts()->getElementsInfo();
			$this->includeComponentTemplate();
		}
		catch (SystemException $e)
		{
			$this->abortDataCache();
	
			ShowError($e->getMessage());
		}
	}
}