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
$product = &$arResult['product'];
$GLOBALS['arrCollection'] = ["PROPERTY_COLLECTION" => $product->collection, "!ID" => $product->id];
?>
<!-- SHOP SECTION START -->
    <div class="shop-section mb-80">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <!-- single-product-area start -->
                    <div class="single-product-area mb-80">
                        <div class="row">
                            <!-- imgs-zoom-area start -->
                            <div class="col-md-5 col-sm-5 col-xs-12">
                                <div class="imgs-zoom-area">
                                    <img id="zoom_03" src="<?=$product->images->detail;?>" data-zoom-image="<?=$product->images->full?>" <?=$product->getImageSeo();?>>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div id="gallery_01" class="carousel-btn slick-arrow-3 mt-30">
                                                <div class="p-c">
                                                    <a href="#" data-image="<?=$product->images->detail?>" data-zoom-image="<?=$product->images->full?>">
                                                        <img class="zoom_03" src="<?=$product->images->mini?>" alt="">
                                                    </a>
                                                </div>
                                                <?foreach($product->getMorePhoto() as $photo):?>
                                                <div class="p-c">
                                                    <a href="#" data-image="<?=$photo->detail;?>" data-zoom-image="<?=$photo->full;?>">
                                                        <img class="zoom_03" src="<?=$photo->mini;?>" alt="">
                                                    </a>
                                                </div>
                                                <?endforeach;?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- imgs-zoom-area end -->
                            <!-- single-product-info start -->
                            <div class="col-md-7 col-sm-7 col-xs-12"> 
                                <div class="single-product-info product-card" data-product-id="<?=$product->id;?>">
                                    <h1 class="h3 text-black-1"><?=($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"])?$arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]:$product->name;?></h1>
                                    <p class="h6 brand-name-2"><span class="brand-name"><?=$product->brand;?></span>: <?=$product->article;?></p>
                                    <!-- hr -->
                                    <hr>
                                    <!-- single-product-tab -->
                                    <div class="single-product-tab">
                                        <ul class="reviews-tab mb-20">
                                            <li  class="active"><a href="#information" data-toggle="tab"><?=GetMessage('DPP_CHARACTERISTICS')?></a></li>
                                            <li ><a href="#description" data-toggle="tab"><?=GetMessage('DPP_DESCRIPTION')?></a></li>
                                            <li ><a href="#delivery" data-toggle="tab"><?=GetMessage('DPP_DELIVERY_PAY')?></a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane active" id="information">
                                                <table>
                                                <?foreach($product->getHaracteristics() as $char=>$name):?>
                                                <?if($product->$char):?>
                                                <tr><td width="260"><b><?=$name?></b></td><td><?=$product->$char?></td></tr>
                                                <?endif;?>
                                                <?endforeach;?>
                                                </table>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="description">
                                                <?=$product->detailText;?>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="delivery">
                                            <?$APPLICATION->IncludeFile(
                                                SITE_DIR."include/catalog/delivery.php",
                                                Array(),
                                                Array("MODE"=>"html")
                                            );?>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  hr -->
                                    <br>
                                    <div class="price-box-3">
                                        <div class="s-price-box">
                                            <span class="font-p2">Цена:</span> <span class="new-price"><?=$product->price;?> бел. руб. </span>
                                        </div>
                                    </div>
                                    <br>
                                    <!-- plus-minus-pro-action -->
                                    <div class="plus-minus-pro-action">
                                        <div class="sin-pro-action f-left">
                                            <ul class="action-button">
                                                <li>
                                                    <a href="javascript:void(0);" class="act-btn quick-buy" tabindex="0">Купить сейчас</a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" class="act-btn add-to-cart" tabindex="0"><i class="zmdi zmdi-shopping-cart-plus"></i> Добавить в корзину</a>
                                                </li>
                                                <li>
                                                	
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                            <!-- single-product-info end -->
                        </div>
                    </div>
                    <!-- single-product-area end -->
                    <div class="related-product-area">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="section-title text-left mb-40">
                                    <h2 class="uppercase">Коллекция</h2>
                                    <h6>Товары из этой же коллекции</h6>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="active-related-product slick-arrow-2">
<?
$APPLICATION->IncludeComponent(
    "bitrix:catalog.section", 
    "products", 
    array(
        "COMPONENT_TEMPLATE" => "products",
        "IBLOCK_TYPE" => IBLOCK_CATALOG_TYPE,
        "IBLOCK_ID" => IBLOCK_CATALOG_ID,
        "SECTION_ID" => "",
        "SECTION_CODE" => "",
        "SECTION_USER_FIELDS" => array(
            0 => "",
            1 => "",
        ),
        "ELEMENT_SORT_FIELD" => "name",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER2" => "desc",
        "FILTER_NAME" => "arrCollection",
        "INCLUDE_SUBSECTIONS" => "A",
        "SHOW_ALL_WO_SECTION" => "N",
        "PAGE_ELEMENT_COUNT" => "12",
        "LINE_ELEMENT_COUNT" => "4",
        "PROPERTY_CODE" => array(
            0 => "ARTICLE",
            1 => "BRAND",
            2 => "CATALOG_QUANTITY",
            3 => "COLLECTION",
            4 => "POWER",
            5 => "COLOR",
            6 => "CATALOG_PRICE",
            7 => "",
        ),
        "OFFERS_LIMIT" => "1",
        "BACKGROUND_IMAGE" => "-",
        "TEMPLATE_THEME" => "blue",
        "ADD_PICT_PROP" => "-",
        "LABEL_PROP" => "-",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "SECTION_URL" => "",
        "DETAIL_URL" => "",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SEF_MODE" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
        "CACHE_GROUPS" => "N",
        "SET_TITLE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "BROWSER_TITLE" => "-",
        "SET_META_KEYWORDS" => "N",
        "META_KEYWORDS" => "-",
        "SET_META_DESCRIPTION" => "N",
        "META_DESCRIPTION" => "-",
        "SET_LAST_MODIFIED" => "N",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "ADD_SECTIONS_CHAIN" => "N",
        "CACHE_FILTER" => "N",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRICE_CODE" => array(
        ),
        "USE_PRICE_COUNT" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "PRICE_VAT_INCLUDE" => "N",
        "BASKET_URL" => "/personal/basket.php",
        "USE_PRODUCT_QUANTITY" => "N",
        "PRODUCT_QUANTITY_VARIABLE" => "",
        "ADD_PROPERTIES_TO_BASKET" => "N",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRODUCT_PROPERTIES" => array(
        ),
        "PAGER_TEMPLATE" => ".default",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "PAGER_TITLE" => "Товары",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "SET_STATUS_404" => "N",
        "SHOW_404" => "N",
        "MESSAGE_404" => "",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N"
    ),
    false
);?>
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SHOP SECTION END -->             
