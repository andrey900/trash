<?php require_once 'bitrix/header.php';?>
<?php

CModule::IncludeModule("iblock");
function _GetInfoElements($arElements, $arSelect=array(), $arFilter=array()){
		if( !is_array($arElements) )
			$arElements = array((int)$arElements);

		if( empty($arSelect) )
			$arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", 
							  "PREVIEW_TEXT", "DETAIL_PICTURE", 
							  "DETAIL_TEXT",
						);

		if( empty($arFilter) )
			$arFilter = Array("ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", 'ID'=>$arElements);
		
		$res = CIBlockElement::GetList(Array('SORT'=>'ASC'), $arFilter, false, false, $arSelect);
		while($arTRes = $res->GetNext())
		{
			$arRes[$arTRes['ID']] = $arTRes;
		}

		return $arRes;
	}
function full_trim($str){
    return trim(preg_replace('/\s{2,}/', ' ', $str));
}


$arSelect = array('ID', 'NAME', 'PROPERTY_CONFIRM_SS', 'PROPERTY_CONFIRM_PP');
$arFilter = array('IBLOCK_ID'=>48);
$arResult = _GetInfoElements(false, $arSelect, $arFilter);

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
foreach ($arResult as $value) {
	$arRes[$value['ID']] = $value['NAME'];
}
$arRes = sortable_MM($arRes);
p($arRes);
foreach ($arRes as $key => $value) {
	$arNRes[$key]=$arResult[$key];
}
p($arNRes, false, false);

$arLoadProductArray = Array(
  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
  "IBLOCK_SECTION_ID" => false,       // элемент лежит в корне раздела
  "IBLOCK_ID"      => 48,
  "ACTIVE"         => "Y",            // активен
  );

foreach ($arRes as $elem) {
	//$el = new CIBlockElement;
	
	//p(substr($elem, 2, 1), false, false);
	if( substr($elem, 0, 2) == 'SS' && substr($elem, 2, 1)==' ' ){
		$arLoadProductArray["IBLOCK_ID"] = 46;
	} elseif( substr($elem, 0, 2) == 'PP' && substr($elem, 2, 1)==' ' ){
		$arLoadProductArray["IBLOCK_ID"] = 47;
	} else{
		$arLoadProductArray["IBLOCK_ID"] = 48;
	}
	$arLoadProductArray["NAME"] = $elem;
	//p($elem, false, false);
	//$el->Add($arLoadProductArray);
}

?>

<?php require_once 'bitrix/footer.php';?>