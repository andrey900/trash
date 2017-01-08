<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $DB;

if(!isset($arParams['CACHE_TIME'])){
	$arParams['CACHE_TIME'] = 3600;
}

if(!isset($arParams['IBLOCK_ID'])){
	$arParams['IBLOCK_ID'] = SHARE_CATALOG_IBLOCK_ID;
}

if(!isset($arParams['COUNT_ELEM'])){
	$arParams['COUNT_ELEM'] = 3;
}

if(!isset($arParams['PERIOD'])){
	$arParams['PERIOD'] = 7;
}

$arOrder = array(
	'SORT' => 'ASC'
);
	
$arFilter = array(
	'IBLOCK_ID'	=> 1
);

$arSelect = array(
	'ID', 'IBLOCK_ID', 'DETAIL_PICTURE',
	'PROPERTY_PARENT_ENTITY',
	'PROPERTY_PARENT_ENTITY.IBLOCK_ID', 'PROPERTY_PARENT_ENTITY.NAME', 'PROPERTY_PARENT_ENTITY.PREVIEW_TEXT', 'PROPERTY_PARENT_ENTITY.DETAIL_PAGE_URL',
);

$arResult = array(
	'ELEMENTS' => array()
);

if($this->StartResultCache(false, array($USER->GetGroups()))){
/*
$sql = 'SELECT *, sum(DAY_COUNT) FROM a_stat WHERE DAY_CALS>20140701 AND DAY_CALS<20140701+7 GROUP BY ELEMENT_ID LIMIT 300;';
$sql1 = 'select T.ELEMENT_ID, max(T.sumviews) from 
(SELECT *, sum(DAY_COUNT) as sumviews FROM a_stat WHERE DAY_CALS>20140701 AND DAY_CALS<20140701+7 GROUP BY ELEMENT_ID) T;';
*/
$sql = "SELECT ELEMENT_ID, sum(DAY_COUNT) as sumviews FROM 
				a_stat WHERE 
				DAY_CALS>20140701 AND 
				DAY_CALS<20140701+{$arParams['PERIOD']} GROUP BY 
				ELEMENT_ID ORDER BY 
				sumviews DESC LIMIT {$arParams['COUNT_ELEM']};";
//DATE_SUB(NOW(),INTERVAL {$arParams['COUNT_ELEM']} DAY)
/*
SELECT * FROM
		a_stat WHERE
		DAY_CALS>20140709 ORDER BY
		DAY_COUNT DESC LIMIT 3;

select * from b_iblock_element WHERE IBLOCK_SECTION_ID=1002

SELECT ELEMENT_ID, sum(DAY_COUNT) as sumviews FROM
		a_stat INNER JOIN 
		b_iblock_element ON 
		a_stat.ELEMENT_ID=b_iblock_element.ID WHERE
		b_iblock_element.IBLOCK_SECTION_ID = 1002 AND
		DAY_CALS>DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 22 DAY), '%Y%m%d') GROUP BY
		a_stat.ELEMENT_ID ORDER BY
		sumviews DESC LIMIT 3;		
		*/
$memcache = new Memcache;
$memcache->connect('localhost', 11211);

$arRes = $memcache->get('arRes');
if( empty($arRes) || $_GET['clear_cache']=='Y' ){
	$res = $DB->Query($sql);
	
	while($result = $res->GetNext()){
		$arRes[$result['ELEMENT_ID']] = array('ID'	 =>$result['ELEMENT_ID'],
							 				  'VIEWS'=>$result['sumviews'] );
		$arElementsId[] = $result['ELEMENT_ID'];
	}
	$memcache->set('arRes', $arRes, false, $arParams['CACHE_TIME']);
	$memcache->set('arElementsId', $arElementsId, false, $arParams['CACHE_TIME']);
} 

$arResult['ELEMENTS'] = $arRes;
unset($arRes);unset($result);unset($res);

$arElementsId = $memcache->get('arElementsId');

if( empty($arElementsId) ){
	foreach ($arResult['ELEMENTS'] as $k => $v) {
		$arElementsId[] = $k;
	}
}

$arSelect = Array("ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION_ID", "DETAIL_PICTURE","DETAIL_TEXT", 'PROPERTY_MAXIMUM_PRICE', /*"PROPERTY_*"*/);
$arFilter = Array("IBLOCK_ID"=>IntVal($arParams['IBLOCK_ID']), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'ID'=>$arElementsId);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($arFields = $res->GetNext()){ 
	 $arResult['ELEMENTS'][$arFields['ID']] = array_merge($arFields, $arResult['ELEMENTS'][$arFields['ID']]);
}

$this->SetResultCacheKeys(array(
		"ELEMENTS"
	));

	$this->IncludeComponentTemplate();
}
?>