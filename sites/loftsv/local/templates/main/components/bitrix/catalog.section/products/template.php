<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if($arResult['ITEMS']):
foreach($arResult['ITEMS'] as $product):
	$this->AddEditAction($product->id, $product->editLink, CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($product->id, $product->deleteLink, CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
	$product->editArea = $this->GetEditAreaId($product->id);
	$APPLICATION->IncludeFile(
	"include/product.php", 
	Array(
	    "product" => $product,
	    "column" => $arParams['LINE_ELEMENT_COUNT']
    ),
    array(
    	"SHOW_BORDER" => false,

    ));
endforeach;
endif;

if($arParams['DISPLAY_BOTTOM_PAGER']):
	echo $arResult["NAV_STRING"];
endif;