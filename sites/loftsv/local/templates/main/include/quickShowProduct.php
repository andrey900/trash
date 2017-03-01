<!-- START QUICKVIEW PRODUCT -->
<div id="quickview-wrapper">
    <!-- Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="modal-product product-card clearfix" data-product-id="">
                        <div class="product-images">
                            <div class="main-image images">
                                <img alt="" src="<?=SITE_TEMPLATE_PATH;?>/img/product/not-image.jpg">
                            </div>
                        </div><!-- .product-images -->
                        
                        <div class="product-info">
                            <h1></h1>
                            <p class="h6 brand-name-2"><span class="brand-name"></span>: <span class="article"></span></p>
                            <div class="price-box-3">
                                <div class="s-price-box">
                                    Цена: <span class="new-price"></span>
                                    <a href="" class="see-all">Просмотреть на странице</a>
                                </div>
                            </div>
                            <br>
                            <ul class="action-button">
                                <li>
                                    <a href="javascript:void(0);" class="act-btn quick-buy" tabindex="0">Купить сейчас</a>
                                </li>
                                <li><a href="javascript:void(0);" class="act-btn add-to-cart" tabindex="0"><i class="zmdi zmdi-shopping-cart-plus"></i> Добавить в корзину</a></li>
                            </ul>
                            <hr>
                            <div class="quick-desc"></div>
                            <div role="tabpanel" class="tab-pane active" id="information">
                                <table>
                                </table>
                            </div>
                        </div><!-- .product-info -->
                    </div><!-- .modal-product -->
                    <div class="loader-product">
                        <img src="/local/templates/main/img/others/loader.gif">
                    </div>
                </div><!-- .modal-body -->
            </div><!-- .modal-content -->
        </div><!-- .modal-dialog -->
    </div>
    <!-- END Modal -->
</div>
<!-- END QUICKVIEW PRODUCT -->