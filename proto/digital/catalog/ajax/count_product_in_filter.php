<?define("NO_KEEP_STATISTIC", true); // Отключение сбора статистики для AJAX-запросов ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
if (!CModule::IncludeModule("iblock")) return;


$arFilter = array(
	"IBLOCK_ID" => SHARE_CATALOG_IBLOCK_ID,
	"ACTIVE" => "Y",
	"SECTION_ID" => $_REQUEST["section_id"],
	"INCLUDE_SUBSECTIONS" => "Y",
);

foreach ($_REQUEST["filter"] as $propertyID => $arValues) 
{
	$arTmp = array();

	if ($propertyID == PROPERTY_MIN_PRICE_ID)
	{
		if($arValues["f"]==0){
			$arTmp[] = array("PROPERTY_".$propertyID => false);
			$arTmp[] = array("<=PROPERTY_".$propertyID => $arValues["t"]);
			$arFilter[] = array_merge(array("LOGIC"=>"OR"), $arTmp);
		}else{ 
			$arTmp[] = array(">=PROPERTY_".$propertyID => $arValues["f"]);
			$arTmp[] = array("<=PROPERTY_".$propertyID => $arValues["t"]);
			$arFilter[] = array_merge(array("LOGIC"=>"AND"), $arTmp);
		}
	}
	else
	{
		foreach ($arValues as $value) 
			$arTmp[] = array("PROPERTY_".$propertyID => $value); 
		
		$arFilter[] = array_merge(array("LOGIC"=>"OR"), $arTmp);
	}	
}
//p($arFilter);
$countElement = CIBlockElement::GetList(array(), $arFilter, array());

echo $countElement;
?>
	
