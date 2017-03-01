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

<!-- START SLIDER AREA -->
<div class="slider-area slider-2 mb-30">
    <div class="bend niceties preview-2">
        <div id="nivoslider-2" class="slides">
            <?foreach($arResult["ITEMS"] as $k=>$arItem):?>
            <img src="<?=$arItem['PREVIEW_PICTURE']['SRC'];?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT'];?>" title="#slider-direction-<?=$k;?>">
            <?endforeach;?>
        </div>
        <?if($arParams['SHOW_DIRECTION'] == "Y"):?>
        <?foreach($arResult["ITEMS"] as $k=>$arItem):
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
        <!-- ===== direction 1 ===== -->
        <div id="slider-direction-<?=$k;?>" class="slider-direction">
            <div class="slider-content-1">
                <div class="title-container">
                    <div class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="1s">
                        <h1 class="slider2-title-3"><?=$arItem["NAME"]?></h1>
                    </div>
                    <div class="wow fadeInUp" data-wow-duration="1s" data-wow-delay="2s">
                        <p class="slider2-title-4"><?=$arItem["PREVIEW_TEXT"]?></p>
                    </div>
                    <?if($arItem["CODE"] && $arItem['PROPERTIES']['BUTTON_NAME']['VALUE']):?>
                    <div class="slider-button wow fadeInUp" data-wow-duration="1s" data-wow-delay="2.5s">
                        <a href="<?=$arItem["CODE"];?>" class="button extra-small button-black">
                            <span class="text-uppercase"><?=$arItem['PROPERTIES']['BUTTON_NAME']['VALUE']?></span>
                        </a>
                    </div>
                    <?endif;?>
                </div>
            </div>
        </div>
        <?endforeach;?>
        <?endif;?>
    </div>
</div>
<!-- END SLIDER AREA -->