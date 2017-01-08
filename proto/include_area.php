<?$APPLICATION->IncludeComponent(
				"bitrix:main.include",
				"",
				Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "/include/buy_oneclick.php",
				"EDIT_TEMPLATE" => ""
				),
				false
				);?> 
<?$APPLICATION->IncludeFile("/include/redline.php");?>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "page",
	"AREA_FILE_SUFFIX" => "inc",
	"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>