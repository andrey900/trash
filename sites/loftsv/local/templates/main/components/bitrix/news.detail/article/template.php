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
// p($arResult);
?>
        <!-- Start page content -->
        <section id="page-content" class="page-wrapper">

            <!-- BLOG SECTION START -->
            <div class="blog-section" id="<?echo $this->GetEditAreaId($arResult['ID'])?>">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-xs-12">
                            <div class="blog-details-area">
                                <?if($arParams["DISPLAY_PICTURE"]!="N"):?>
								<?if (is_array($arResult["DETAIL_PICTURE"])):?>
                                <!-- blog-details-photo -->
                                <div class="blog-details-photo bg-img-1 p-20 mb-30">
                                    <img
									src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
									width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
									height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
									alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
									title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
									/>
                                    <div class="today-date bg-img-1">
                                        <span class="meta-date"><?=FormatDate("d", MakeTimeStamp($arResult['DATE_CREATE']))?></span>
                                        <span class="meta-month"><?=FormatDate("M", MakeTimeStamp($arResult['DATE_CREATE']))?></span>
                                    </div>
                                </div>
                                <?endif;?>
                                <?endif;?>

                                <!-- blog-details-title -->
                                <h3 class="blog-details-title mb-30"><?=$arResult['NAME']?></h3>
                                <!-- blog-description -->
                                <div class="blog-description mb-60">
									<?=$arResult['DETAIL_TEXT'];?>
                                </div>

                                <!-- blog-share-tags -->
                                <div class="blog-share-tags box-shadow clearfix mb-60">
<?if ($arParams["USE_SHARE"] == "Y"):?>
	<noindex>
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.share", 
		$arParams["SHARE_TEMPLATE"], 
		array(
			"HANDLERS" => $arParams["SHARE_HANDLERS"],
			"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
			"PAGE_TITLE" => $arResult["~NAME"],
			"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
			"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
			"HIDE" => $arParams["SHARE_HIDE"],
		),
		$component,
		array("HIDE_ICONS" => "Y")
	);
	?>
	</noindex>
<?endif;?>
                                    <div class="blog-tags f-right">
                                        <p class="share-tags-title f-left">Tags</p>
                                        <ul class="blog-tags-list f-left">
                                            <li><a href="#">Mobile</a></li>
                                            <li><a href="#">IOS</a></li>
                                            <li><a href="#">Windows</a></li>
                                            <li><a href="#">Tab</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- author-post -->
                                <div class="media author-post box-shadow mb-60">
                                    <div class="media-left pr-20">
                                        <a href="#">
                                            <img class="media-object" src="/images/avatar_43e3abbcc99b_128.png" height="80" alt="#">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="media-heading">
                                            <a href="#">Subash Chandra Das</a>
                                        </h6>
                                        <p class="mb-0">No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursu pleasure rationally encounter conseques ences that are extremely painful.</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <!-- widget-product -->
                            <aside class="widget widget-product box-shadow mb-30">
                                <h6 class="widget-title border-left mb-20">Предлагаемые товары</h6>
                                <!-- product-item start -->
                                <div class="product-item">
                                    <div class="product-img">
                                        <a href="single-product.html">
                                            <img src="/html/img/product/4.jpg" alt=""/>
                                        </a>
                                    </div>
                                    <div class="product-info">
                                        <h6 class="product-title">
                                            <a href="single-product.html">Product Name</a>
                                        </h6>
                                        <h3 class="pro-price">$ 869.00</h3>
                                    </div>
                                </div>
                                <!-- product-item end -->
                                <!-- product-item start -->
                                <div class="product-item">
                                    <div class="product-img">
                                        <a href="single-product.html">
                                            <img src="/html/img/product/8.jpg" alt=""/>
                                        </a>
                                    </div>
                                    <div class="product-info">
                                        <h6 class="product-title">
                                            <a href="single-product.html">Product Name</a>
                                        </h6>
                                        <h3 class="pro-price">$ 869.00</h3>
                                    </div>
                                </div>
                                <!-- product-item end -->
                                <!-- product-item start -->
                                <div class="product-item">
                                    <div class="product-img">
                                        <a href="single-product.html">
                                            <img src="/html/img/product/12.jpg" alt=""/>
                                        </a>
                                    </div>
                                    <div class="product-info">
                                        <h6 class="product-title">
                                            <a href="single-product.html">Product Name</a>
                                        </h6>
                                        <h3 class="pro-price">$ 869.00</h3>
                                    </div>
                                </div>
                                <!-- product-item end -->                               
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
            <!-- BLOG SECTION END -->             

        </section>
        <!-- End page content -->