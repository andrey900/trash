<?
$strBrands = include '.urlrewrite_ext.php';

$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/($strBrands)\\/([\\w\\d\\-]+)\\.html\\??(.*)#",
		"RULE" => "brand=\$1&product=\$2",
		"ID" => "bitrix:catalog.element",
		"PATH" => "/catalog/detail.php",
	),
	array(
		"CONDITION" => "#^/($strBrands)/(\\?.*){0,1000}#",
		"RULE" => "brand=$1",
		"ID" => "bitrix:catalog",
		"PATH" => "/catalog/catalog.php",
	),
	array(
		"CONDITION" => "#^/ajax/([\\w\\d\\-]+)/([a-z\\d\\-]+){0,30}/?(.*)#",
		"RULE" => "className=\$1&method=\$2",
		"ID" => "",
		"PATH" => "/ajax/heandler.php",
	),
	/*array(
		"CONDITION" => "#^/([\w\d\-]+)/(\\?.*){0,1000}#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/catalog/catalog.php",
	),*/
	array(
		"CONDITION" => "#^/articles/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/articles/index.php",
	),
);

?>