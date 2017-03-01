<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!-- checkout start -->
<div class="tab-pane" id="checkout">
    <div class="checkout-content box-shadow p-30">
        <form action="#" class="form-order ajax">
            <div class="row">
                <!-- billing details -->
                <div class="col-md-6">
                    <div class="billing-details pr-10">
                        <h6 class="widget-title border-left mb-20">Информация для оформления заказа</h6>
                        <?=$o->baseTemplateFields($o->propsForOrderForm());?>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- our order -->
                    <div class="payment-details pl-10 mb-50">
                        <h6 class="widget-title border-left mb-20">Ваш заказ</h6>
                        <table>
						<?foreach($basket->items as $product):?>
						    <tr>
						        <td class="td-title-1"><?=$product->name?> x <?=$product->quantity?></td>
						        <td class="td-title-2"><?=$product->getTotalPrice()?> руб.</td>
						    </tr>
						<?endforeach;?>
                            <tr>
                                <td class="td-title-1">Общая сумма</td>
                                <td class="td-title-2"><?=$basket->totalPrice?> руб.</td>
                            </tr>
                            <tr>
                                <!-- <td class="td-title-1">Стоимость доставки</td>
                                <td class="td-title-2"><span class="delivery-price">0.00</span> руб.</td> -->
                                <td class="td-title-1" colspan="2">Стоимость доставки
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="/delivery/" target="blank" class="text-right">Читать условия доставки</a></td>
                            </tr>
                            <tr>
                                <td class="order-total">Общая сумма к оплате</td>
                                <td class="order-total-price">
                                <?=$basket->totalPrice;?> руб.</td>
                            </tr>
                        </table>
                    </div> 
                    <!-- payment-method -->
                    <div class="payment-method">
                        <h6 class="widget-title border-left mb-20">Способ оплаты</h6>
						<div class="payment-content">
                        <p>Наличными курьеру</p>
                        </div>
                    </div>
                    <!-- payment-method end -->
                    <div class="text-right">
                    	<button class="submit-btn-1 mt-30 btn-hover-1 send-order" type="submit">Оформить заказ</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- checkout end -->