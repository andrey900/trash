<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?$APPLICATION->IncludeComponent(
	"aniart:super.component",
	"filter",
	Array(
		"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"		=>	$arParams["IBLOCK_ID"],
		"SECTION_ID" => $arParams['SECTION_ID'],
		"SHOW_PARAMS" => $arParams['SHOW_PARAMS'],
		"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
		"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
	),
	$component
);
?>

<?
if(!empty($_POST['get-price']))
{
	global $APPLICATION;
	$APPLICATION->RestartBuffer();
	
	$APPLICATION->IncludeComponent(
		"aniart:super.component",
		"price-list",
		Array(
			"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
			"IBLOCK_ID"		=>	$arParams["IBLOCK_ID"],
			"SECTIONS" => $_POST['sections'],
			"SHOW_IMAGES" => $_POST['SHOW_IMAGES'],
			"SHOW_LINKS" => $_POST['SHOW_LINKS'],
			"SHOW_IMAGES_PATH" => $_POST['SHOW_IMAGES_PATH'],
			"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
			"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
		),
		$component
	);
	
	exit();	
}

?>