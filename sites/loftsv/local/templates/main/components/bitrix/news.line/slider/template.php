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
<div class="slider-area  plr-185  mb-80">
    <div class="container-fluid">
        <div class="slider-content">
            <div class="row">
                <div class="active-slider-1 slick-arrow-1 slick-dots-1">
                    <?foreach($arResult["ITEMS"] as $arItem):
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
                    <!-- layer-1 Start -->
                    <div class="col-md-12" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                        <div class="layer-1">
                            <div class="slider-img">
                                <img src="<?=$arItem['PREVIEW_PICTURE']['SRC'];?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT'];?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE'];?>">
                            </div>
                            <div class="slider-info gray-bg">
                                <div class="slider-info-inner">
                                    <h1 class="slider-title-1 text-uppercase text-black-1"><?=$arItem["NAME"]?></h1>
                                    <div class="slider-brief text-black-2">
                                        <p><?=$arItem["PREVIEW_TEXT"]?></p>
                                    </div>
                                    <?if($arItem["CODE"] && $arItem['PROPERTIES']['BUTTON_NAME']['VALUE']):?>
                                    <a href="<?=$arItem["CODE"];?>" class="button extra-small button-black">
                                        <span class="text-uppercase"><?=$arItem['PROPERTIES']['BUTTON_NAME']['VALUE']?></span>
                                    </a>
                                    <?endif;?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- layer-1 end -->
                    <?endforeach;?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SLIDER AREA -->