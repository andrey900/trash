<?extract('product');?>
<!-- product-item start -->
<div class="product-card col-xs-12 col-sm-<?=round(12 / $column);?> col-md-<?=round(12 / $column);?>" id="<?=$product->editArea;?>" data-product-id="<?=$product->id;?>">
    <div class="product-item product-container">
        <div class="product-img">
            <a href="<?=$product->detailPageUrl;?>">
                <?if( $product->images->mini ):?>
                    <img src="<?=$product->images->mini;?>" alt="<?=$product->detailPicture['ALT'];?>" title="<?=$product->detailPicture['TITLE'];?>" />
                <?else:?>
                    <img src="<?=SITE_TEMPLATE_PATH;?>/img/product/not-image.jpg" alt=""/>
                <?endif;?>
            </a>
        </div>
        <div class="product-info">
            <p class="h6 product-title">
                <a href="<?=$product->detailPageUrl;?>"><?=$product->name?></a>
            </p>
            <!-- <div class="pro-rating">
                <a href="#"><i class="zmdi zmdi-star"></i></a>
                <a href="#"><i class="zmdi zmdi-star"></i></a>
                <a href="#"><i class="zmdi zmdi-star"></i></a>
                <a href="#"><i class="zmdi zmdi-star-half"></i></a>
                <a href="#"><i class="zmdi zmdi-star-outline"></i></a>
            </div> -->
            <p class="h3 pro-price"><?=$product->price;?> бел. руб. </p>
            <ul class="action-button">
                <li>
                    <a href="javascript:void(0);" id="<?=$product->id;?>" class="quickview" title="Quickview"><i class="zmdi zmdi-zoom-in"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="add-to-cart" title="Add to cart"><i class="zmdi zmdi-shopping-cart-plus"></i></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- product-item end -->