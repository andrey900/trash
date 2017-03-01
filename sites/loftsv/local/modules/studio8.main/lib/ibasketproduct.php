<?php

namespace Studio8\Main;

interface IBasketProduct
{
	public function __construct(Product $product);
	public function getTotalPrice();
	public function getProduct();
	public function changeQuantity($quantity);
}