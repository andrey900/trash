<?
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetPageProperty("description", "Люстры в Минске от интернет-магазина ELECTRODOM. Мы предлагаем Вам купить люстру по самой выгодной цене! Звоните и заказывайте люстры и другие приборы освещения.");
$APPLICATION->SetPageProperty("keywords", "купить люстру в минске, купить люстры, люстры в минске, люстры в минске интернет магазин");
$APPLICATION->SetPageProperty("title", "Купить люстру по выгодной цене в интернет-магазине ELECTRODOM, потолочные люстры в Минске");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Electrodom.by");
$arElements = [];
if( !$GLOBALS['USER']->IsAdmin() ){
	CHTTP::SetStatus("404 Not Found");
	@define("ERROR_404","Y");
	//LocalRedirect ("/404.php", "404 Not Found");
	include($_SERVER["DOCUMENT_ROOT"]."/404.php");die;
}
?>
<?
if( $_SERVER['REQUEST_METHOD'] != 'POST' ):
$arRes = Studio8\Main\Helpers::_GetInfoElements(false, ['ID', 'NAME'], ['IBLOCK_ID'=>IBLOCK_BRANDS_ID]);

$dbRes = CIBlock::GetProperties(IBLOCK_CATALOG_ID, [], ['ACTIVE'=>'Y', "CHECK_PERMISSIONS" => "N"]);
?>
<link rel="stylesheet" type="text/css" href="style.css">
<form method="POST">
	<div class="form-body">
	<fieldset class="base-options">
    	<legend>Base options</legend>
	<label> Select brand: </label>
	<select name="BRAND">
		<option value="">Выберите бренд</option>
		<?foreach($arRes as $brand):?>
			<option value="<?=$brand['ID']?>"><?=$brand['NAME']?></option>
		<?endforeach;?>
	</select>
<br>
	<label> Select Properties: </label>
	<select multiple="" name="PROP[]">
		<?while( $arProp = $dbRes->Fetch() ){?>
			<option value="<?=$arProp['ID']?>"><?=$arProp['NAME']?></option>
		<?}?>
	</select>
	</fieldset>
	<br>
	<fieldset class="additional-options">
    	<legend>Additional options</legend>
    	<div class="group-option">
    		<input id="use-active" type="checkbox" name="nouse_active" value="1">
    		<label for="use-active">Не учитывать активность</label>
    	</div>
    	<div class="group-option">
    		<input id="use-code" type="checkbox" name="use_code" value="1">
    		<label for="use-code">Выгружать символьный код элемента</label>
    	</div>
    	<div class="group-option">
    		<input id="use-detail-text" type="checkbox" name="use_detail_text" value="1">
    		<label for="use-detail-text">Выгружать детальное описание</label>
    	</div>
    	<div class="group-option">
    		<input id="use-picture" type="checkbox" name="use_picture" value="1">
    		<label for="use-picture">Выгружать детальные картинки</label>
    	</div>
    	<div class="group-option">
    		<input id="use-price" type="checkbox" name="use_price" value="1">
    		<label for="use-price">Выгружать цену с валютой</label>
    	</div>
    </fieldset>
    <br>
	<input type="submit" class="input-submit">
	</div>
</form>
<?
die;
endif;

$brand = (int)$_REQUEST['BRAND'];
$selectProp = array();
$csvProp = '';
$csvHeadProp = '';
if( is_array($_REQUEST['PROP']) && !empty($_REQUEST['PROP']) ){
	foreach ($_REQUEST['PROP'] as $prop) {
		$selectProp[] = "PROPERTY_".$prop;
		$csvProp .= ";PROPERTY_".$prop."_VALUE";
		$csvHeadProp .= ";IP_PROP".$prop;
	}
}

$arAdditionalSelect = array('CODE', 'ACTIVE', "DETAIL_TEXT", "CATALOG_GROUP_1", "DETAIL_PICTURE");

$arCSVFields = ["XML_ID" => "IE_XML_ID", "NAME" => "IE_NAME"];
$arSelect = ['ID', 'NAME', 'XML_ID'];
$arFilter = ['IBLOCK_ID'=>IBLOCK_CATALOG_ID, "ACTIVE"=>"Y"];

if( $brand > 0 ){
	$arFilter["PROPERTY_6_VALUE"] = $brand;
}
if( $_POST['nouse_active'] ){
	$arFilter['ACTIVE'] = ["Y", "N"];
	$arCSVFields['ACTIVE'] = "IE_ACTIVE";
}
if( $_POST['use_code'] ){
	$arSelect[] = "CODE";
	$arCSVFields['CODE'] = "IE_CODE";
}
if( $_POST['use_picture'] ){
	$arSelect[] = "DETAIL_PICTURE";
	$arCSVFields['DETAIL_PICTURE'] = "IE_DETAIL_PICTURE";
}
if( $_POST['use_detail_text'] ){
	$arSelect[] = "DETAIL_TEXT";
	$arCSVFields['IE_DETAIL_TEXT'] = "DETAIL_TEXT";
}
if( !empty($selectProp) ){
	$arSelect = array_merge($arSelect, $selectProp);
}
if( $_POST['use_price'] ){
	$arSelect[] = "CATALOG_GROUP_1";
	$arCSVFields['CATALOG_PRICE_1'] = "PRICE";
	$arCSVFields['CATALOG_CURRENCY_1'] = "CURRENCY";
}

$arRes = Studio8\Main\Helpers::_GetInfoElements(false, $arSelect, $arFilter);

/*
if( $brand == 9999999 )
	$arRes = CElectrodomTools::_GetInfoElements(false, $arSelect, ['IBLOCK_ID'=>12, "ACTIVE"=>"Y"]);
else
	$arRes = CElectrodomTools::_GetInfoElements(false, array_merge($arSelect, $arAdditionalSelect), ["PROPERTY_68_VALUE"=>$brand, 'IBLOCK_ID'=>12, "ACTIVE"=>["Y", "N"]]);
*/
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=export_".date("d_m_H_i").".csv");
header("Pragma: no-cache");
header("Expires: 0");

//$str  = 'IE_ID;IE_XML_ID;IE_NAME;IE_ACTIVE;IE_CODE;DETAIL_PICTURE;PRICE;CURRENCY';

$str = implode(";", $arCSVFields).$csvHeadProp;
//if( !empty($csvHeadProp) )
//	$str .= $csvHeadProp;
$str .= "\n";

foreach($arRes as $arItem){
	$_str = "";
	foreach ($arCSVFields as $k => $v) {
		$_str .= str_replace(";", "", $arItem[$k]).';';
	}
	if( !empty($selectProp) ){
	 	foreach ($selectProp as $prop) {
	 		$_str .= str_replace(";", "", $arItem[$prop."_VALUE"]).";";
	 	}
	}

	$str .= substr($_str, 0, -1);

	/*$str .= $arItem['ID'].';'
		 .$arItem['XML_ID'].';'
		 .$arItem['NAME'].';'
		 .$arItem['ACTIVE'].';'
		 .$arItem['CODE'].';'
		 .CFile::GetPath($arItem['DETAIL_PICTURE']).';'
		 .$arItem['CATALOG_PRICE_1'].';'
		 .$arItem['CATALOG_CURRENCY_1'];

		 if( !empty($selectProp) ){
		 	foreach ($selectProp as $prop) {
		 		$str .= ";".$arItem[$prop."_VALUE"];
		 	}
		 }
	*/
	$str .= "\n";
}
echo $str;
die;
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
