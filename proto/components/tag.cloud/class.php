<?php
use \Bitrix\Main;
use Bitrix\Main\Data;
use Bitrix\Main\Config;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class TagCloud extends CBitrixComponent
{
	/**
	* кешируемые ключи arResult
	* @var array()
	*/
	protected $cacheKeys = array();
	
	/**
	* дополнительные параметры, от которых должен зависеть кеш
	* @var array
	*/
	protected $cacheAddon = array();
	
	/**
	* парамтеры постраничной навигации
	* @var array
	*/
	protected $navParams = array();
	
	private $arFilter = array();

	/**
	* подключает языковые файлы
	*/
	public function onIncludeComponentLang()
	{
		$this -> includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}
	
	/**
	* подготавливает входные параметры
	* @param array $arParams
	* @return array
	*/
	public function onPrepareComponentParams($params)
	{
		$result = array(
			'IBLOCK_ID'  => ( !is_array($params['IBLOCK_ID']) )?intval($params['IBLOCK_ID']):$params['IBLOCK_ID'],
			'ACTIVE'	 => ( $params['ACTIVE'] != 'N' )?'Y':'N',
			'FIELD_FROM_TAG' => ( !is_array($params['FIELD_FROM_TAG']) )?array():$params['FIELD_FROM_TAG'],
			'ORDER_TAG'	 => ( !is_array($params['ORDER_TAG']) )?array():$params['ORDER_TAG'],
			'COUNT_TAG'  => ( !is_array($params['COUNT_TAG'] ) )?array():$params['COUNT_TAG'],
			'CACHE_TYPE' => ( $params['CACHE_TYPE']!= 'N') ? 'A' : 'N',
			'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600
		);
		if( strlen($params["FILTER_NAME"])>0 || preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $params["FILTER_NAME"]) ){
			$result['arrFilter'] = $GLOBALS[$params["FILTER_NAME"]];
			if(!is_array($result['arrFilter']))
				$result['arrFilter'] = array();
		}
		//p($params, false);die();
		return $result;
	}

	/**
	* определяет читать данные из кеша или нет
	* @return bool
	*/
	protected function readDataFromCache()
	{
		if ($this -> arParams['CACHE_TYPE'] == 'N')
		return false;
		return !($this -> StartResultCache(false, $this -> cacheAddon));
	}

	/**
	* кеширует ключи массива arResult
	*/
	protected function putDataToCache()
	{
		if (is_array($this -> cacheKeys) && sizeof($this -> cacheKeys) > 0)
		{
			$this -> SetResultCacheKeys($this -> cacheKeys);
		}
	}

	/**
	* прерывает кеширование
	*/
	protected function abortDataCache()
	{
		$this -> AbortResultCache();
	}

	/**
	* проверяет подключение необходиимых модулей
	* @throws LoaderException
	*/
	protected function checkModules()
	{
		if (!Main\Loader::includeModule('iblock'))
		throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));
	}

	/**
	* проверяет заполнение обязательных параметров
	* @throws SystemException
	*/
	protected function checkParams()
	{
		if ($this -> arParams['IBLOCK_ID'] <= 0)
			throw new Main\ArgumentNullException('IBLOCK_ID');

		$this->createArFilter();
	}

	/**
	* выполняет действия после выполения компонента, например установка заголовков из кеша
	*/
	protected function executeEpilog()
	{
	}

	/**
	* выполяет действия перед кешированием
	*/
	protected function executeProlog()
	{
	}

	/**
	* Получение данных и формирование выходного массива 
	**/
	protected function getResult()
	{
		if( empty( $this->arParams['FIELD_FROM_TAG'] ) )
			throw new Main\LoaderException(Loc::getMessage('STANDARD_ELEMENTS_LIST_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));

		foreach ( $this->arParams['FIELD_FROM_TAG'] as $k => $strFieldName) {
			$cnt = isset($this->arParams['COUNT_TAG'][$k])?$this->arParams['COUNT_TAG'][$k]:5;
			$ord = ( !isset($this->arParams['ORDER_TAG'][$k]) )?'DESC':$this->arParams["ORDER_TAG"][$k];
			
			$res = CIBlockElement::GetList(Array('CNT'=>$ord), $this->arFilter, array($strFieldName), Array("nPageSize"=>$cnt), Array());
			
			while($arFields = $res->GetNext())
			{
			 	if(preg_match("/section/i", $strFieldName, $match)){
			 		$t = CIBlockExt::GetSectionInfo($arFields[$strFieldName]);
			 	} else {
			 		$t = CIBlockExt::GetElementInfo($arFields[$strFieldName.'_VALUE']);
			 	}
			 	$this->arResult['ITEMS'][$strFieldName][$t['ID']] = $t;
			}
		}
	}

	private function createArFilter()
	{
		$this->arFilter = array(
		    "ACTIVE" 			=> $this->arParams['ACTIVE'],
		    "IBLOCK_ID" 		=> $this->arParams['IBLOCK_ID'],
		);
		$this->arFilter = array_merge($this->arFilter, $this->arParams['arrFilter']);
		return $this;
	}

	/**
	 * Start Component
	 */
	public function executeComponent()
	{
		global $APPLICATION;
		global $USER;

		//ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
		//$APPLICATION->AuthForm(GetMessage("SALE_ACCESS_DENIED"));
		
		try
		{	
			$this -> checkModules();
			$this -> checkParams();
			$this -> cacheAddon = array($this->arParams['IBLOCK_ID'],
										$this->arFilter, 
										$this->arParams['FIELD_FROM_TAG'],
										$this->arParams['COUNT_TAG']);
			$this -> executeProlog();
			if (!$this -> readDataFromCache())
			{
				$this -> getResult();
				$this -> putDataToCache();
				//p($this->arResult, false);die();
				$this -> includeComponentTemplate();
			}
			$this -> executeEpilog();

			//$this->putDataToCache();
			//$this->initObCache();
		}
		catch (SystemException $e)
		{
			ShowError($e->getMessage());
		}
	}
}