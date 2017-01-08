<?php require_once 'bitrix/header.php';?>
<script type="text/javascript">
		var y = {
			one: "Mikhail",
			two:"alex",
			tree: "yourch"
		};
		var mess = 0;
		var ar = [];
		for(var i in y){
			if(y[i] == "alex")
				 mess = 2;
			else
				 mess = 3;
			ar.push(y[i]);
		}
		console.log(ar);
</script>

<?php
//error_reporting(E_ALL);
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


$arFilter = array('IBLOCK_ID'=>16, "!PROPERTY_TSVET"=>false, "ACTIVE"=>'Y', "SECTION_ID"=>'3224', 'INCLUDE_SUBSECTIONS'=>'Y');
$arSelect = array("ID", 'NAME', 'PROPERTY_TSVET', 'IBLOCK_SECTION_ID');

$arResult = _GetInfoElements(false, $arSelect, $arFilter);
echo "Count elements: ".count($arResult).'<br>';
$arColors = array();
$arColorsID = array();

$arLoadProductArray = Array(
  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
  "IBLOCK_SECTION_ID" => false,       // элемент лежит в корне раздела
  "IBLOCK_ID"      => 45,
  "ACTIVE"         => "Y",            // активен
  );

foreach ($arResult as $value) {
	//$el = new CIBlockElement;

	if( !strripos($value['PROPERTY_TSVET_VALUE'], ';') ){
		if( !in_array(full_trim($value['PROPERTY_TSVET_VALUE']), $arColors) ){
			$arLoadProductArray['NAME'] = full_trim($value['PROPERTY_TSVET_VALUE']);
		//	$PRODUCT_ID = $el->Add($arLoadProductArray);
			$arColors[full_trim($value['PROPERTY_TSVET_VALUE'])] = full_trim($value['PROPERTY_TSVET_VALUE']);
			$arColorsID[full_trim($value['PROPERTY_TSVET_VALUE'])] = $PRODUCT_ID;
		} else {
			$PRODUCT_ID = $arColorsID[full_trim($value['PROPERTY_TSVET_VALUE'])];
		}

		//CIBlockElement::SetPropertyValues($value['ID'], 16, array(array('VALUE'=>$PRODUCT_ID)), 'COLORS_HDBK');

	} else {
		$arSubColors = explode(";", $value['PROPERTY_TSVET_VALUE']);
		$arManyColors = array();
		foreach ($arSubColors as $v) {
			if( !in_array(full_trim($v), $arColors) ){
				$arLoadProductArray['NAME'] = full_trim($v);
		//		$PRODUCT_ID = $el->Add($arLoadProductArray);
				$arColors[full_trim($v)] = full_trim($v);
				$arColorsID[full_trim($v)] = $PRODUCT_ID;
				$arManyColors[] = array('VALUE' => $PRODUCT_ID);
			} else {
				$PRODUCT_ID = $arColorsID[full_trim($v)];
				$arManyColors[] = array('VALUE' => $PRODUCT_ID);
			}
		}
//p($arManyColors, false, false);
		//CIBlockElement::SetPropertyValues($value['ID'], 16, $arManyColors, 'COLORS_HDBK');
	}
}
echo "Count colors: ".count($arColors);
echo "<h3 color='red'>Export DB successfull!</h3>";
//p($arColorsID, false, false);
//p($arColors, false, false);
//CIBlockElement::SetPropertyValues(125949, 16, array(array('VALUE'=>1), array('VALUE'=>3)), 'COLORS_HDBK');
?>

<?php require_once 'bitrix/footer.php';?>