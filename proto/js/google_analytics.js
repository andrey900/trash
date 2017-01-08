
  ga('create', 'UA-59890948-1', 'auto');
  ga('send', 'pageview');
  ga('require', 'ecommerce');
  ga('require', 'ec');

  // данные заказа
  ga('ecommerce:addTransaction', {
	  'id': '1234',                     // Transaction ID. Required.
	  'affiliation': 'Acme Clothing',   // Affiliation or store name.
	  'revenue': '11.99',               // Grand Total.
	  'shipping': '5',                  // Shipping.
	  'tax': '1.29',                     // Tax.
	  //'currency': 'UAH'  // local currency code.
	});

  // данные элем в корзине
  ga('ecommerce:addItem', {
	  'id': '1234',                     // Transaction ID. Required.
	  'name': 'Fluffy Pink Bunnies',    // Product name. Required.
	  'sku': 'DD23444',                 // SKU/code.
	  'category': 'Party Toys',         // Category or variation.
	  'price': '11.99',                 // Unit price.
	  'quantity': '1',                   // Quantity.
	  //'currency': 'UAH'  // local currency code.
	});

  // отправка данных
  ga('ecommerce:send');

  // очистка данных
  //ga('ecommerce:clear');

  ga('ec:addImpression', {
  'id': 'P12345',                   // Product details are provided in an impressionFieldObject.
  'name': 'Android Warhol T-Shirt',
  'category': 'Apparel/T-Shirts',
  'brand': 'Google',
  'variant': 'black',
  'list': 'Search Results',
  'position': 1                     // 'position' indicates the product position in the list.
});

ga('ec:addImpression', {
  'id': 'P67890',
  'name': 'YouTube Organic T-Shirt',
  'type': 'view',
  'category': 'Apparel/T-Shirts',
  'brand': 'YouTube',
  'variant': 'gray',
  'list': 'Search Results',
  'position': 2
});

ga('send', 'pageview');              // Send product impressions with initial pageview. 
