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

<!-- BLOG SECTION START -->
<div class="blog-section-2 pt-60 pb-30">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title text-left mb-40">
<h2 class="uppercase"><?$APPLICATION->IncludeFile(
    SITE_DIR."include/home/articles_title.php",
    Array(),
    Array("MODE"=>"html", "NAME" => "заголовок")
);?></h2>
<h6><?$APPLICATION->IncludeFile(
    SITE_DIR."include/home/articles_description.php",
    Array(),
    Array("MODE"=>"html", "NAME"=>"описание")
);?></h6>
                </div>
            </div>
        </div>
        <div class="blog">
            <div class="row <?=($arParams['SLIDER_PROPERTY'] == "Y")?'active-blog-2':'';?>">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
<!-- blog-item start -->
<div class="col-xs-12 col-sm-6" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
    <div class="blog-item-2">
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="blog-image">
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                    	<?if( $arItem["PREVIEW_PICTURE"] ):?>
                    	<img src="<?=$arItem["PREVIEW_PICTURE"]['SRC'];?>" alt="<?=$arItem["PREVIEW_PICTURE"]['ALT']?>" title="<?=$arItem["PREVIEW_PICTURE"]['TITLE']?>">
                    	<?endif;?>
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="blog-desc">
                    <h5 class="blog-title-2">
						<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
                    	<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"];?></a>
						<?endif;?>
                    </h5>
                    <?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
                    	<p><?=$arItem["PREVIEW_TEXT"];?></p>
					<?endif;?>
                    <div class="read-more">
                        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее...</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- blog-item end -->
<?endforeach;?>
			</div>
		</div>
        <div class="col-xs-12">
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><?=$arResult["NAV_STRING"]?><?endif;?>
        </div>
	</div>
</div>

