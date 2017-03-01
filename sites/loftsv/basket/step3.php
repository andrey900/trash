<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<!-- order-complete start -->
<div class="tab-pane <?=($step3)?'active':'';?>" id="order-complete">
	<div class="order-complete-content box-shadow">
		<div class="thank-you p-30 text-center">
			<h6 class="text-black-5 mb-0">Спасибо. Ваш заказ оформлен. Ожидайте, звонка для уточнения деталей.</h6>
		</div>
		<div class="order-info p-30 mb-10">
			<ul class="order-info-list">
				<li>
					<h6>Ваш номер заказа: <b><?=$o->getOrderField('ID');?></b></h6>
				</li>
			</ul>
		</div>
		<div class="row">
			<!-- our order -->
			<div class="col-md-6">
				<div class="payment-details p-30">
					<h6 class="widget-title border-left mb-20">Информация о заказе</h6>
					<table>
						<?foreach($o->getOrderField('PROPERTY_3') as $product):?>
						<?$p = explode("\r\n", $product);?>
						<tr>
							<td class="td-title-1"><?=explode(": ", $p[1])[1];?> x <?=explode(": ", $p[3])[1];?></td>
							<td class="td-title-2"><?=explode(": ", $p[4])[1];?> руб.</td>
						</tr>
						<?endforeach;?>
						<tr>
							<td class="td-title-1">Общая сумма</td>
							<td class="td-title-2"><?=number_format($o->getOrderField('PROPERTY_1'), 2)?> руб.</td>
						</tr>
						<!--<tr>
							<td class="td-title-1">Стоимость доставки</td>
							<td class="td-title-2"><span class="delivery-price">5.00</span> руб.</td>
						</tr>-->
						<tr>
							<td class="order-total">Общая сумма к оплате</td>
							<td class="order-total-price"><?=number_format($o->getOrderField('PROPERTY_1'), 2)?> руб.</td>
						</tr>
					</table>
				</div>         
			</div>
			<div class="col-md-6">
				<div class="bill-details p-30">
					<h6 class="widget-title border-left mb-20">Параметры заказа</h6>
					<ul class="bill-address">
						<?foreach($o->propsForOrderForm() as $prop):?>
						<?if($prop['CODE'] == "USER_COMMENTS") continue;?>
						<li>
							<span><?=$prop['NAME'];?></span>
							<?
							$value = $o->getOrderField('PROPERTY_'.$prop['ID']);
							if($prop['CODE'] == "USER_DELIVERY_TYPE"){
								switch ($value) {
									case 1:
									$value = 'Да нужна доставка';
									break;
									case 2:
									$value = 'Вывезу сам';
									break;
								}
							}
							echo $value;
							?>
						</li>
						<?endforeach;?>
						<li>
							<span>Оплата: </span>
							Наличными
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12 text-center">
		<a href="/catalog/" class="submit-btn-1 black-bg btn-to-catalog p-20">Перейти в каталог</a>
	</div>
</div>
<!-- order-complete end -->