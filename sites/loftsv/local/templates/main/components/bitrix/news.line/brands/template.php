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

if( !$arResult['ITEMS'] ) return;
?>
<!-- BY BRAND SECTION START-->
<div class="by-brand-section mb-80">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title text-left mb-40">
                    <h2 class="uppercase"><?$APPLICATION->IncludeFile(
    SITE_DIR."include/home/brands_title.php",
    Array(),
    Array("MODE"=>"html", "NAME" => "заголовок")
);?></h2>
                    <p class="h6"><?$APPLICATION->IncludeFile(
    SITE_DIR."include/home/brands_description.php",
    Array(),
    Array("MODE"=>"html", "NAME"=>"описание")
);?></p>
                </div>
            </div>
        </div>
        <div class="by-brand-product">
            <div class="row <?if($arParams['USE_SLIDER'] != "N"):?>active-by-brand<?endif;?> slick-arrow-2">
                <?foreach($arResult["ITEMS"] as $arItem):
                if( $arParams['SHOW_ALL'] != "Y" && !$arItem['PREVIEW_PICTURE'] ) continue;
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
				<!-- single-brand-product start -->
                <div class="col-xs-12 col-sm-4 col-md-3" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                    <div class="single-brand-product">
                        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                            <?if($arItem['PREVIEW_PICTURE']):?>
                            <img src="<?=$arItem['PREVIEW_PICTURE']['SRC'];?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT'];?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE'];?>">
                            <?else:?>
                            <img src="/img/brand/nologo.gif" alt="<?=$arItem['PREVIEW_PICTURE']['ALT'];?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE'];?>">
                            <?endif;?>
                        </a>
                        <?if( $arParams['SHOW_NAME'] != "N" || !$arItem['PREVIEW_PICTURE'] ):?>
                        <h3 class="brand-title text-gray">
                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
                        </h3>
                        <?endif;?>
                    </div>
                </div>
                <!-- single-brand-product end -->
                <?endforeach;?>
            </div>
        </div>
    </div>
</div>
<!-- END SLIDER AREA -->