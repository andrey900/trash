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
?>

<?if($arResult["SECTIONS_COUNT"] > 0):

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
$this->AddEditAction($arResult['SECTION']['ID'], $arResult['SECTION']['EDIT_LINK'], $strSectionEdit);
$this->AddDeleteAction($arResult['SECTION']['ID'], $arResult['SECTION']['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

foreach($arResult['SECTIONS'] as $arSection):
$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
?>
<!-- blog-item start -->
<div class="col-md-3 col-xs-6 col-sm-4 mt-30" id="<? echo $this->GetEditAreaId($arSection['ID']); ?>">
    <div class="blog-item">
        <?if($arSection['DETAIL_PICTURE']):?>
        	<img src="<?=CFile::GetPath($arSection['DETAIL_PICTURE']);?>" alt="<?=$arSection['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT']?>" title="<?=$arSection['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_TITLE']?>">
        <?else:?>
        	<img src="/img/blog/1.jpg" alt="">
        <?endif;?>
        <div class="blog-desc" href="<?=$arSection['SECTION_PAGE_URL']?>">
            <h5 class="blog-title"><a href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a></h5>
            <p><?=$arSection['DESCRIPTION']?></p>
            <div class="read-more">
                <a href="<?=$arSection['SECTION_PAGE_URL']?>">Перейти в раздел - <?=$arSection['NAME']?></a>
            </div>
        </div>
    </div>
</div>
<!-- blog-item end -->
<?endforeach;?>
<?endif;?>