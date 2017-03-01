<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Каталог продукции освещения стиля лофт");
?>
<!-- Start page content -->
<section id="page-content" class="page-wrapper">
	<div class="container">

<div class="row">
<?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section.list", 
    "catalog", 
    array(
    	"IBLOCK_ID" => IBLOCK_CATALOG_ID,
    	"IBLOCK_TYPE" => IBLOCK_CATALOG_TYPE,
    	"SECTION_URL" => "/#SECTION_CODE#/",
    	"COUNT_ELEMENTS" => "N",
    	"ADD_SECTIONS_CHAIN" => "N"
    )
);?>
</div>

	</div>
</section>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>