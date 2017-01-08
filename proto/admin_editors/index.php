<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");?>
<?$APPLICATION->IncludeComponent(
	"aniart:iblock.element.add.list",
	"edit_ajax",
	Array(
		"SEF_MODE" => "N",
		"IBLOCK_TYPE" => "catalogs",
		"SECTION_ID" => ((int)$_GET['SECTION_ID']>0)?(int)$_GET['SECTION_ID']:7,
		"IBLOCK_ID" => "1",
		"GROUPS" => array(1, 12),
		"STATUS" => array(),
		"EDIT_URL" => "",
		"ELEMENT_ASSOC" => "N",
		"ALLOW_EDIT" => "N",
		"ALLOW_DELETE" => "N",
		"NAV_ON_PAGE" => "100",
		"MAX_USER_ENTRIES" => "100000"
	),
false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>