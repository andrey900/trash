<? 
$arIds = array();
$arActionIds = array();
$arSaleIds = array();
foreach($arResult['ITEMS'] as $arItem)
{
	$arIds[] = $arItem['ID'];
}
$arSelect = Array("ID", "NAME", "IBLOCK_ID", "PROPERTY_ACTION_OR_SALE");
$arFilter = Array("IBLOCK_ID"=>$arParams['IBLOCK_ID'], "ID"=>$arIds);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
$arGTM = array();
while($ob = $res->Fetch())
{
	if($ob['PROPERTY_ACTION_OR_SALE_ENUM_ID'] == '21')
	{
		$arSaleIds[] = $ob['ID'];
		$arGTM['SliderIndexSale'][] = array('NAME'=>$ob['NAME'], 'ID'=>$ob['ID']);
	}
	elseif($ob['PROPERTY_ACTION_OR_SALE_ENUM_ID'] == '20')
	{
		$arActionIds[] = $ob['ID'];
		$arGTM['SliderIndexAction'][] = array('NAME'=>$ob['NAME'], 'ID'=>$ob['ID']);
	}
}
$arResult['ACTION'] = $arActionIds;
$arResult['SALE'] = $arSaleIds;

//GTM
foreach( $arGTM as $key=>$arItem ){
	GTMDataCollector('promoView', $arItem, $key);
}
//end GTM
?>