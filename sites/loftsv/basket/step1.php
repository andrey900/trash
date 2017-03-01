<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!-- shopping-cart start -->
<div class="tab-pane active" id="shopping-cart">
    <div class="shopping-cart-content">
        <form action="#">
            <div class="table-content table-responsive mb-50">
                <table class="text-center basket-table">
                    <thead>
                        <tr>
                            <th class="product-thumbnail">Товар</th>
                            <th class="product-price">Цена</th>
                            <th class="product-quantity">Количество</th>
                            <th class="product-subtotal">Общая цена</th>
                            <th class="product-remove">Удалить</th>
                        </tr>
                    </thead>
					<tbody>
					<?foreach($basket->items as $product):?>
					<tr class="product-card" data-product-id="<?=$product->id?>">
						<td class="product-thumbnail">
							<div class="pro-thumbnail-img"><img src="<?=$product->image?>" alt=""></div>
							<div class="pro-thumbnail-info text-left">
								<h6 class="product-title-2"><a href="<?=$product->detailPageUrl?>"><?=$product->name?></a></h6>
								<p>Производитель: <?=$product->brand?></p>
								<p>Ариткул: <?=$product->article?></p>
							</div>
						</td>
						<td class="product-price"><?=number_format($product->price, 2);?> руб.</td>
						<td class="product-quantity">
							<div class="cart-plus-minus f-left">
								<input type="text" value="<?=$product->quantity?>" name="qtybutton" class="cart-plus-minus-box">
							</div>
						</td>
						<td class="product-subtotal"><?=$product->getTotalPrice();?> руб.</td>
						<td class="product-remove">
							<a href="javascript:void(0);" class="remove-of-cart update-basket"><i class="zmdi zmdi-close"></i></a>
						</td>
					</tr>
					<?endforeach;?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="3" class="text-right"><b class="text-uppercase g-font-2">Общая цена:</b></td>
						<td colspan="2" class="text-tenter"><b class="text-uppercase g-font-2 order-total"><?=$basket->totalPrice;?> руб.</b></td>
					</tr>
					</tfoot>
				</table>
				<div class="col-md-12 text-right mt-20">
					<button class="submit-btn-1 black-bg btn-hover-2 step-to-order">Далее</button>
				</div>
            </div>
        </form>
    </div>
</div>
<!-- shopping-cart end -->