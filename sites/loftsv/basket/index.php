<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина пользователя");
?>
<?
use Bitrix\Main\Application;
use Studio8\Main\Product;
use Studio8\Main\Order;

$basket = Application::getInstance()->basket->getBasket();

$o = new Order();

$step3 = false;
if( $_REQUEST['success'] ){
    $step3 = true;
    $o->findByToken($_REQUEST['success']);
    if( !$o->getOrderField("ID") )
        $step3 = false;
}
?>
<!-- Start page content -->
        <section id="page-content" class="page-wrapper">

            <!-- SHOP SECTION START -->
            <div class="shop-section mb-80">
            <?if( $basket->quantity <= 0 && !$step3 ):?>
				<div class="container empty-basket">
					<div class="row">
                        <div class="col-md-2 col-sm-12"></div>
                        <div class="col-md-10 col-sm-12">
                        	<div class="order-complete-content box-shadow">
                                <div class="thank-you p-30 text-center">
                                    <h6 class="text-black-5 mb-0">Ваша корзина пуста, добавьте товары с <a href="/catalog/"><b>каталога</b></a></h6>
                                </div>
                            </div>
                        </div>
                    </div>
			<?else:?>
                <div class="container">
                    <div class="row">
                        <div class="col-md-2 col-sm-12">
                            <ul class="cart-tab">
                                <li>
                                    <a class="active" href="#shopping-cart" data-toggle="tab">
                                        <span>01</span>
                                        Корзина
                                    </a>
                                </li>
                                <li>
                                    <a <?=($step3)?'class="active"':'';?> href="#checkout" data-toggle="tab">
                                        <span>02</span>
                                        Оформление
                                    </a>
                                </li>
                                <li>
                                    <a <?=($step3)?'class="active"':'';?> href="#order-complete" data-toggle="tab">
                                        <span>03</span>
                                        Ваш заказ
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-10 col-sm-12">
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <?if(!$step3):?>
<?include 'step1.php';?>
                                <?endif;?>
                                <?if(!$step3):?>
<?include 'step2.php';?>
                                <?endif;?>
                                <?if($step3):?>
<?include 'step3.php';?>
                                <?endif;?>
                            </div>
                        </div>
                    </div>
                </div><!-- END CONTENT -->
                <?endif;?>
            </div>
            <!-- SHOP SECTION END -->             

        </section>
        <!-- End page content -->
<?Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/basket.js');?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>