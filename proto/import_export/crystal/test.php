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

$arSelect = array('ID', 'NAME');
$arFilter = array('IBLOCK_ID'=>44);
$arResult = _GetInfoElements(false, $arSelect, $arFilter);

foreach ($arResult as $value) {
	$arRes[$value['ID']] = $value['NAME'];
}

//p($arRes, false, false);

$arFilter = array('IBLOCK_ID'=>16, "!TSVET"=>false, "ACTIVE"=>'Y', "SECTION_ID"=>'3224', 'INCLUDE_SUBSECTIONS'=>'Y');
//$arFilter = array('IBLOCK_ID'=>16, "ACTIVE"=>'Y', "SECTION_ID"=>'3224', 'INCLUDE_SUBSECTIONS'=>'Y', 'PROPERTY_SIZES_HDBK'=>178956);
$arSelect = array("ID", 'NAME', 'PROPERTY_SIZES_HDBK.NAME', 'PROPERTY_TSVET', 'PROPERTY_RAZMER', 'IBLOCK_SECTION_ID');

$arResult = _GetInfoElements(false, $arSelect, $arFilter);
p($arResult, false);
/*
$arFilter = array('IBLOCK_ID'=>16, "!TSVET"=>false, "ACTIVE"=>'Y', "SECTION_ID"=>'3224', 'INCLUDE_SUBSECTIONS'=>'Y');
$arSelect = array("ID", 'NAME', 'PROPERTY_SIZES_HDBK.NAME', 'PROPERTY_TSVET', 'PROPERTY_RAZMER', 'IBLOCK_SECTION_ID');

$arResult = _GetInfoElements(false, $arSelect, $arFilter);
//$arResult = _GetInfoElements(122334, array('PROPERTY_COLORS_HDBK'));
echo "Count elements: ".count($arResult).'<br>';
$arColors = array();
$arSelect = array('ID', 'NAME');
$arFilter = array('IBLOCK_ID'=>44);
$arColorsIDs = _GetInfoElements(false, $arSelect, $arFilter);

foreach ($arColorsIDs as $value) {
	$arColorsID[$value['NAME']] = $value['ID'];
}
//p($arResult);
/*
$nn = str_replace(" ", " ", full_trim('SUNFLOWER  F.jpg'));
var_dump(file_exists($_SERVER["DOCUMENT_ROOT"].COLOR_PATH.'SUNFLOWER  F.jpg'));
var_dump(rename( $_SERVER["DOCUMENT_ROOT"].COLOR_PATH.'SUNFLOWER  F.jpg', $_SERVER["DOCUMENT_ROOT"].COLOR_PATH.'SUNFLOWER F.jpg' ));

$i = 0;
foreach ($arResult as $arRes) {
/*
	var_dump(file_exists($_SERVER["DOCUMENT_ROOT"].COLOR_PATH.$arRes["PROPERTY_TSVET_VALUE"].".jpg"));
	$e = rename($_SERVER["DOCUMENT_ROOT"].COLOR_PATH.$arRes["PROPERTY_TSVET_VALUE"].".jpg", 
		   $_SERVER["DOCUMENT_ROOT"].COLOR_PATH.full_trim($arRes["PROPERTY_TSVET_VALUE"]).".jpg");
	var_dump($e);
	
	if($i>3)
		p($_SERVER["DOCUMENT_ROOT"].COLOR_PATH.full_trim($arRes["PROPERTY_TSVET_VALUE"]).".jpg");

	$i++;
}
*/
/*
$arLoadProductArray = Array(
  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
  "IBLOCK_SECTION_ID" => false,       // элемент лежит в корне раздела
  "IBLOCK_ID"      => 44,
  "ACTIVE"         => "Y",            // активен
  );

foreach ($arResult as $value) {
	//$el = new CIBlockElement;

	if( !strripos($value['PROPERTY_RAZMER_VALUE'], ';') ){
		$arManyColors = array();
		$PRODUCT_ID = $arColorsID[full_trim($value['PROPERTY_RAZMER_VALUE'])];
		if( !empty($PRODUCT_ID) )
			$arManyColors[] = array('VALUE'=>$PRODUCT_ID);
		else{
			if( strripos($value['PROPERTY_RAZMER_VALUE'], 'X') ){
				$arSubColors = explode("X", $value['PROPERTY_RAZMER_VALUE']);
				$Pristavka = substr($arSubColors[0], 0, 2);
				foreach ($arSubColors as $v) {
					if( strripos($v, $Pristavka)===false ){
						$razm = $Pristavka.' '.full_trim($v);
						$arManyColors[] = array('VALUE'=>$arColorsID[$razm]);
					} else {
						$arManyColors[] = array('VALUE'=>$arColorsID[$v]);
					}
				}
			}
		}
		//CIBlockElement::SetPropertyValues($value['ID'], 16, $arManyColors, 'SIZES_HDBK');
		//p(array('id'=>$arManyColors,'name'=>full_trim($value['PROPERTY_RAZMER_VALUE'])) , false, false);
	} else {
		$arSubColors = explode(";", $value['PROPERTY_RAZMER_VALUE']);
		$arManyColors = array();

		foreach ($arSubColors as $val) {
			$PRODUCT_ID = $arColorsID[full_trim($val)];
			if( !empty($PRODUCT_ID) )
				$arManyColors[] = array('VALUE'=>$PRODUCT_ID);
			else{
				if( strripos($val, 'X') ){
					$arSubColors = explode("X", $val);
					$Pristavka = 'MM';//substr($arSubColors[0], 0, 2);
					foreach ($arSubColors as $v) {
						if( strripos($v, $Pristavka)===false ){
							$razm = $Pristavka.' '.full_trim($v);
							$arManyColors[] = array('VALUE'=>$arColorsID[$razm]);
						} else {
							$arManyColors[] = array('VALUE'=>$arColorsID[$v]);
						}
					}
				}

			}
			/*
			if( !in_array(full_trim($v), $arColorsID) ){
				$arLoadProductArray['NAME'] = full_trim($v);
				//$PRODUCT_ID = $el->Add($arLoadProductArray);
				$arColors[full_trim($v)] = full_trim($v);
				$arManyColors[] = array('VALUE' => $PRODUCT_ID);
			} else {
				$PRODUCT_ID = $arColorsID[full_trim($v)];
				$arManyColors[] = array('VALUE' => $PRODUCT_ID);
			}
			*//*
		}
		//CIBlockElement::SetPropertyValues($value['ID'], 16, $arManyColors, 'SIZES_HDBK');
	}
}

$arSize = array();

foreach ($arColors as $key => $value) {
	if( strripos($value, 'X') ){
		$arSubColors = explode("X", $value);
		$Pristavka = substr($arSubColors[0], 0, 2);
		$arManyColors = array();
		foreach ($arSubColors as $v) {
			if( strripos($v, $Pristavka)===false ){
				$razm = $Pristavka.' '.full_trim($v);
				if( !in_array($razm, $arSize) ){
					$arSize[$razm] = $razm;
				}
			} else {
				if( !in_array(full_trim($v), $arSize) ){
					$arSize[full_trim($v)] = full_trim($v);
				}
			}
		}
	} else {
		if( !empty($value) )
			$arSize[$value] = $value;
	}
}
/*
foreach ($arSize as $value) {
	$el = new CIBlockElement;
	$arLoadProductArray['NAME'] = $value;
	$el->Add($arLoadProductArray);
}
*/
/*
foreach ($arResult as $value) {
	$el = new CIBlockElement;

	if( !strripos($value['PROPERTY_RAZMER_VALUE'], ';') ){
		$arManyColors = array();
		if( strripos($value['PROPERTY_RAZMER_VALUE'], 'X') ){

			$arSubColors = explode("X", $value['PROPERTY_RAZMER_VALUE']);
			$Pristavka = substr($arSubColors[0], 0, 2);
			$arManyColors = array();
			foreach ($arSubColors as $v) {
				$el = new CIBlockElement;

				if( strripos($v, $Pristavka)===false ){
					$razm = $Pristavka.' '.full_trim($v);
					if( !in_array($razm, $arColors) ){
						$arLoadProductArray['NAME'] = $razm;
						$PRODUCT_ID = $el->Add($arLoadProductArray);
						$arColors[$razm] = $razm;
						$arColorsID[$razm] = $PRODUCT_ID;
						$arManyColors[] = array('VALUE' => $PRODUCT_ID);
					} else {
						$PRODUCT_ID = $arColorsID[$razm];
						$arManyColors[] = array('VALUE' => $PRODUCT_ID);
					}
				} else {
					if( !in_array(full_trim($v), $arColors) ){
						$arLoadProductArray['NAME'] = full_trim($v);
						$PRODUCT_ID = $el->Add($arLoadProductArray);
						$arColors[full_trim($v)] = full_trim($v);
						$arColorsID[full_trim($v)] = $PRODUCT_ID;
						$arManyColors[] = array('VALUE' => $PRODUCT_ID);
					} else {
						$PRODUCT_ID = $arColorsID[full_trim($value['PROPERTY_RAZMER_VALUE'])];
						$arManyColors[] = array('VALUE' => $PRODUCT_ID);
					}
				}
			}
			//p($arManyColors, false, false);
			CIBlockElement::SetPropertyValues($value['ID'], 16, $arManyColors, 'SIZES_HDBK');
		} else {

		if( !in_array(full_trim($value['PROPERTY_RAZMER_VALUE']), $arColors) ){
			$arLoadProductArray['NAME'] = full_trim($value['PROPERTY_RAZMER_VALUE']);
			$PRODUCT_ID = $el->Add($arLoadProductArray);
			$arColors[full_trim($value['PROPERTY_RAZMER_VALUE'])] = full_trim($value['PROPERTY_RAZMER_VALUE']);
			$arColorsID[full_trim($value['PROPERTY_RAZMER_VALUE'])] = $PRODUCT_ID;
			$arManyColors[] = array('VALUE' => $PRODUCT_ID);
		} else {
			$PRODUCT_ID = $arColorsID[full_trim($value['PROPERTY_RAZMER_VALUE'])];
			$arManyColors[] = array('VALUE' => $PRODUCT_ID);
		}

		CIBlockElement::SetPropertyValues($value['ID'], 16, $arManyColors, 'SIZES_HDBK');

	   }//end else

	} else {
		$arSubColors = explode(";", $value['PROPERTY_RAZMER_VALUE']);
		$arManyColors = array();
		foreach ($arSubColors as $v) {

			if( !in_array(full_trim($v), $arColors) ){
				$arLoadProductArray['NAME'] = full_trim($v);
				$PRODUCT_ID = $el->Add($arLoadProductArray);
				$arColors[full_trim($v)] = full_trim($v);
				$arColorsID[full_trim($v)] = $PRODUCT_ID;
				$arManyColors[] = array('VALUE' => $PRODUCT_ID);
			} else {
				$PRODUCT_ID = $arColorsID[full_trim($v)];
				$arManyColors[] = array('VALUE' => $PRODUCT_ID);
			}
		}
//p($arManyColors, false, false);
		CIBlockElement::SetPropertyValues($value['ID'], 16, $arManyColors, 'SIZES_HDBK');
	}
}
*/
echo "Count colors: ".count($arSize);
echo "<h3 color='red'>Export DB successfull!</h3>";
p($arColorsID, false, false);
//p($arSize, false, false);
//CIBlockElement::SetPropertyValues(125949, 16, array(array('VALUE'=>1), array('VALUE'=>3)), 'COLORS_HDBK');
?>

<?php require_once 'bitrix/footer.php';?>