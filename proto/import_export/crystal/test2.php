<?php require_once 'bitrix/header.php';?>
<?php //require_once('bitrix/php_interface/include/classes/CAniartTools.php');?>
<?php
CModule::IncludeModule("iblock");
error_reporting('E_ALL');
function full_trim($str){
    return trim(preg_replace('/\s{2,}/', ' ', $str));
}

function sortable_MM($inArray){
	//p($inArray, false, false);
	$rrr = $inArray;
	//$rrr = array('28,0', '2,50', '19,0', '8,16', '2,00', '7,27', '5,10', '22,0');
	foreach ($rrr as $key => $value) {
		$rrrr[$key] = substr(str_replace(',', '.', $value), 3);
	}
	asort($rrrr);/*
	$rrr = array();
	foreach ($rrrr as $key => $value) {
		$rrr[$key] = 'MM '.str_replace('.', ',', $value);
	}*/
	$inArray = $rrrr;
	return $inArray;
}


$arSelect = array('ID', 'NAME', 'PROPERTY_CONFIRM_SS', 'PROPERTY_CONFIRM_PP');
$arFilter = array('IBLOCK_ID'=>48);
$arResult = CAniartTools::_GetInfoElements(false, $arSelect, $arFilter);

foreach ($arResult as $value) {
	$arRes_MMs[$value['ID']] = $value['NAME'];
}
$arRes_MMs = sortable_MM($arRes_MMs);
$arRes_MM  = $arResult;

$arSelect = array('ID', 'NAME', 'PROPERTY_CONFIRM_MM', 'PROPERTY_CONFIRM_PP');
$arFilter = array('IBLOCK_ID'=>46);
$arResult = CAniartTools::_GetInfoElements(false, $arSelect, $arFilter);

foreach ($arResult as $value) {
	$arRes_SSs[$value['ID']] = $value['NAME'];
}
$arRes_SSs = sortable_MM($arRes_SSs);
$arRes_SS  = $arResult;

$arSelect = array('ID', 'NAME', 'PROPERTY_CONFIRM_SS', 'PROPERTY_CONFIRM_MM');
$arFilter = array('IBLOCK_ID'=>47);
$arResult = CAniartTools::_GetInfoElements(false, $arSelect, $arFilter);

foreach ($arResult as $value) {
	$arRes_PPs[$value['ID']] = $value['NAME'];
}
$arRes_PPs = sortable_MM($arRes_PPs);
$arRes_PP  = $arResult;


//p($arRes_MM, false, false);p($arRes_PP, false, false);p($arRes_SS, false, false);

//p($arRes, false, false);
foreach ($arRes_MM as $key => $value) {
	$arNRes[$key]=$arResult[$key];
}
// New sortable array
//p($arNRes, false, false);

//p(CIBlockSection::GetById(5295)->GetNext());
$arSelect = array('ID', 'NAME');
$arFilter = array('IBLOCK_ID'=>44, "ACTIVE"=>'Y');
$arResult = CAniartTools::_GetInfoElements(false, $arSelect, $arFilter);
foreach ($arResult as $value) {
	$arHabdbookSizes[$value['ID']] = $value['NAME'];
}
//p($arHabdbookSizes);

function GetPropertyInfo($iblock, $id, $arPropName, $arHabdbookSizes){
$tt = CIBlockElement::GetProperty((int)$iblock, (int)$id);//,array('SORT'=>'ASC'), array("CODE" => "SIZES_HDBK")
while ($ob = $tt->GetNext())
    {
        if( $arPropName == $ob['CODE'] )
        	$arResult[$arPropName][$ob['VALUE']] = $arHabdbookSizes[$ob['VALUE']];//p($ob, false, false);
    }
    return $arResult;
}

//p( GetPropertyInfo(16, 121144, 'SIZES_HDBK') );

$arSelect = array('ID', 'NAME', /*'PROPERTY_RAZMER', 'PROPERTY_SIZES_HDBK', "IBLOCK_SECTION_ID"*/);
$arFilter = array('IBLOCK_ID'=>16, "ACTIVE"=>'Y', "SECTION_ID"=>'3224', 'INCLUDE_SUBSECTIONS'=>'Y', /*"ID"=>174323 /*"ID"=>121144*/);
$arResult = CAniartTools::_GetInfoElements(false, $arSelect, $arFilter);
//Get property element info
foreach($arResult as $arElement){
	$hbi = GetPropertyInfo($arFilter['IBLOCK_ID'], $arElement['ID'], 'SIZES_HDBK', $arHabdbookSizes);
	$arResult[$arElement['ID']]['SIZES_HDBK'] = $hbi['SIZES_HDBK'];
}

foreach($arResult as $kel=>$arElement){
	foreach($arElement['SIZES_HDBK'] as $k=>$name){
		// MM_recreateble
		if( substr($name, 0, 2)=='MM' ){
			$preSize = '';
			foreach( $arRes_MMs as $idSize => $size ){
				//p( substr(str_replace(',', '.', $name), 3),false, false );
				//p( array($idSize =>$size), false, false );
				if( (real)$size > (real)substr(str_replace(',', '.', $name), 3) ){
					//p(array('pre'=>$preSize),false, false );
					if( !empty($preSize) )
						$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $preSize;
					
					if( !empty($arRes_MM[$preSize]['PROPERTY_CONFIRM_SS_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $arRes_MM[$preSize]['PROPERTY_CONFIRM_SS_VALUE'];
					
					if( !empty($arRes_MM[$preSize]['PROPERTY_CONFIRM_PP_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $arRes_MM[$preSize]['PROPERTY_CONFIRM_PP_VALUE'];
					break;
				}
				elseif( (real)$size == (real)substr(str_replace(',', '.', $name), 3) ){
					//p( array('=='=>$idSize),false, false );
					if( !empty($idSize) )
						$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $idSize;
					
					if( !empty($arRes_MM[$idSize]['PROPERTY_CONFIRM_SS_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $arRes_MM[$idSize]['PROPERTY_CONFIRM_SS_VALUE'];
					
					if( !empty($arRes_MM[$idSize]['PROPERTY_CONFIRM_PP_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $arRes_MM[$idSize]['PROPERTY_CONFIRM_PP_VALUE'];
					break;
				}
				$preSize = $idSize;
			}
		}

		// SS_recreateble
		if( substr($name, 0, 2)=='SS' ){
			$preSize = '';
			foreach( $arRes_SSs as $idSize => $size ){
				//p( substr(str_replace(',', '.', $name), 3),false, false );
				//p( array($idSize =>$size), false, false );
				if( (real)$size > (real)substr(str_replace(',', '.', $name), 3) ){
					//p(array('pre'=>$preSize),false, false );
					if( !empty($preSize) )
						$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $preSize;
					
					if( !empty($arRes_SS[$preSize]['PROPERTY_CONFIRM_MM_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $arRes_SS[$preSize]['PROPERTY_CONFIRM_MM_VALUE'];
					
					if( !empty($arRes_SS[$preSize]['PROPERTY_CONFIRM_PP_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $arRes_SS[$preSize]['PROPERTY_CONFIRM_PP_VALUE'];
					break;
				}
				elseif( (real)$size == (real)substr(str_replace(',', '.', $name), 3) ){
					//p( array('=='=>$idSize),false, false );
					if( !empty($idSize) )
						$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $idSize;
					
					if( !empty($arRes_SS[$idSize]['PROPERTY_CONFIRM_MM_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $arRes_SS[$idSize]['PROPERTY_CONFIRM_MM_VALUE'];
					
					if( !empty($arRes_SS[$idSize]['PROPERTY_CONFIRM_PP_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $arRes_SS[$idSize]['PROPERTY_CONFIRM_PP_VALUE'];
					break;
				}
				$preSize = $idSize;
			}
		}

		// PP_recreateble
		if( substr($name, 0, 2)=='PP' ){
			$preSize = '';
			foreach( $arRes_PPs as $idSize => $size ){
				//p( substr(str_replace(',', '.', $name), 3),false, false );
				//p( array($idSize =>$size), false, false );
				if( (real)$size > (real)substr(str_replace(',', '.', $name), 3) ){
					//p(array('pre'=>$preSize),false, false );
					if( !empty($preSize) )
						$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $preSize;
					
					if( !empty($arRes_PP[$preSize]['PROPERTY_CONFIRM_MM_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $arRes_PP[$preSize]['PROPERTY_CONFIRM_MM_VALUE'];
					
					if( !empty($arRes_PP[$preSize]['PROPERTY_CONFIRM_SS_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $arRes_PP[$preSize]['PROPERTY_CONFIRM_SS_VALUE'];
					break;
				}
				elseif( (real)$size == (real)substr(str_replace(',', '.', $name), 3) ){
					//p( array('=='=>$idSize),false, false );
					if( !empty($idSize) )
						$arResult[$arElement['ID']]['SIZES_METRICS_PP'][] = $idSize;
					
					if( !empty($arRes_PP[$idSize]['PROPERTY_CONFIRM_MM_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_MM'][] = $arRes_PP[$idSize]['PROPERTY_CONFIRM_MM_VALUE'];
					
					if( !empty($arRes_PP[$idSize]['PROPERTY_CONFIRM_SS_VALUE']) )
					$arResult[$arElement['ID']]['SIZES_METRICS_SS'][] = $arRes_PP[$idSize]['PROPERTY_CONFIRM_SS_VALUE'];
					break;
				}
				$preSize = $idSize;
			}
		}

	}

	
	/*
	if( isset($arResult[$kel]['SIZES_METRICS_MM']) ){
		CIBlockElement::SetPropertyValues($kel, 16, $arResult[$kel]['SIZES_METRICS_MM'], 'SIZES_METRICS_MM');
	}
	if( isset($arResult[$kel]['SIZES_METRICS_SS']) ){
		CIBlockElement::SetPropertyValues($kel, 16, $arResult[$kel]['SIZES_METRICS_SS'], 'SIZES_METRICS_SS');
	}
	if( isset($arResult[$kel]['SIZES_METRICS_PP']) ){
		CIBlockElement::SetPropertyValues($kel, 16, $arResult[$kel]['SIZES_METRICS_PP'], 'SIZES_METRICS_PP');
	}
*/
	//p($arResult[$kel]);
}
//p($arResult);
?>
<?php require_once 'bitrix/footer.php';?>