// ASPRO.KSHOP JavaScript API v.1.0.0

$.expr[':'].findContent = function(obj, index, meta) 
{
	var matchParams = meta[3].split(',');
	regexFlags = 'ig';
	regex = new RegExp('^' + $.trim(matchParams) + '$', regexFlags);
	return regex.test($(obj).text());
};

$.fn.equalizeHeights = function() {
	var maxHeight = this.map(function(i, e) {
		return $(e).height();
	}).get();

	return this.height(Math.max.apply(this, maxHeight));
};

(function($) {
    $.fn.animateNumbers = function(stop, duration, formatPrice, start, ease, callback) 
	{	
        return this.each(function() {
            var $this = $(this);
            var start = (start === undefined) ? parseInt(delSpaces($this.text()).replace(/,/g, "")) : start;			
			formatPrice = (formatPrice === undefined) ? false : formatPrice;
            $({value: start}).animate({value: stop}, {
            	duration: duration == undefined ? 1000 : duration,
            	easing: ease == undefined ? "swing" : ease,
            	step: function() { if (formatPrice) {$this.text(jsPriceFormat(Math.floor(this.value)));} else{$this.text(Math.floor(this.value));}
            	},
            	complete: function() 
				{ 
					if (parseInt(delSpaces($this.text())) !== stop) { if (formatPrice) {$this.text(jsPriceFormat(stop));} else {$this.text(stop);} }
					if (typeof callback == "function") { callback(); }
            	}
            });
        });
    };
})(jQuery);

var  isFunction = function(func) 
{ 
	if(typeof func == 'function') return true;
}

if (!isFunction("fRand"))
{
	var fRand = function() {return Math.floor(arguments.length > 1 ? (999999 - 0 + 1) * Math.random() + 0 : (0 + 1) * Math.random());};
}

if (!isFunction("delSpaces")) 
{ 
	var delSpaces = function delSpaces(str){ str = str.replace(/\s/g, ''); return str; } 
}

if (!isFunction("waitForFinalEvent"))
{
	var waitForFinalEvent = (function () 
	{
	  var timers = {};
	  return function (callback, ms, uniqueId) 
	  {
		if (!uniqueId) {
		  uniqueId = fRand();
		}
		if (timers[uniqueId]) {
		  clearTimeout (timers[uniqueId]);
		}
		timers[uniqueId] = setTimeout(callback, ms);
	  };
	})();
}

if (!isFunction("onLoadjqm"))
{
	var onLoadjqm = function (name, hash, requestData, selector)
	{
		
		if(typeof(requestData) == "undefined"){
			requestData = '';
		}
		if(typeof(selector) == "undefined"){
			selector = false;
		}
		var width = $('.'+name+'_frame').width();		
		$('.'+name+'_frame').css('margin-left', '-'+width/2+'px');

		if(name=="order-popup-call")
		{
		}
		else if(name=="order-button")
		{
			$(".order-button_frame").find("div[product_name]").find("input").val(hash.t.title).attr("readonly", "readonly").css({"overflow": "hidden", "text-overflow": "ellipsis"});
		}
		else if(name == "to-order" && selector)
		{
			$(".to-order_frame").find('[data-sid="PRODUCT_NAME"]').val($(selector).attr('alt')).attr("readonly", "readonly").css({"overflow": "hidden", "text-overflow": "ellipsis"});
			$(".to-order_frame").find('[data-sid="PRODUCT_ID"]').val($(selector).attr('data-item'));
		}
		else if ( name == 'one_click_buy')
		{	
			$('#one_click_buy_form_button').on("click", function() { $("#one_click_buy_form").submit(); });
			$('#one_click_buy_form').submit( function() 
			{
				if ($('.'+name+'_frame form input.error').length || $('.'+name+'_frame form textarea.error').length) { return false }
				else
				{
					$.ajax({
						url: $(this).attr('action'),
						data: $(this).serialize(),
						type: 'POST',
						dataType: 'json',
						error: function(data) { alert('Error connecting server'); },
						success: function(data) 
						{
							
							if(data.result=='Y') { $('.one_click_buy_result').show(); $('.one_click_buy_result_success').show(); } 
							else { $('.one_click_buy_result').show(); $('.one_click_buy_result_fail').show(); $('.one_click_buy_result_text').text(data.message);}
							$('.one_click_buy_modules_button', self).removeClass('disabled');
							$('#one_click_buy_form').hide();
							$('#one_click_buy_form_result').show();
							$.ajax({
								url: '/ajax/send_data_to_ga.php',
								data: { ORDER_ID: data.message},
								type: 'POST',
								dataType: 'json',
								//error: function(data) { alert('Error connecting server'); },
								success: function(data) 
								{
									var order = data['ORDER'];
									var item = data['BASKET'][0];
									var cartProducts = [];
									
									cartProducts.push({
									      'id': order['ID'], 
									      'sku': item['PRODUCT_ID'],        
									      'name': item['NAME'],
									      'category': item['CATEGORY'], 
									      'price': item['PRICE'],       
									      'quantity': item['QUANTITY']         
									    });
									
									dataLayer.push({
									      'transactionId' : order['ID'],
									      'transactionAffiliation': 'fullhouse',
									      'transactionTotal': order['PRICE'],            
									      'transactionTax': '',               
									      'transactionShipping': order['PRICE_DELIVERY'],          
									      'transactionProducts': cartProducts,
									      'event': 'trackTrans'});
								}
							});
						}
					});

				}
				return false;
			});
		}
		else if ( name == 'one_click_buy_basket')
		{	
			$('#one_click_buy_form_button').on("click", function() { $("#one_click_buy_form").submit(); }); //otherwise don't works
			$('#one_click_buy_form').live("submit", function() 
			{
				$.ajax({
					url: $(this).attr('action'),
					data: $(this).serialize(),
					type: 'POST',
					dataType: 'json',
					error: function(data) { window.console&&console.log(data); },
					success: function(data) 
					{
						if(data.result=='Y') { $('.one_click_buy_result').show(); $('.one_click_buy_result_success').show(); } 
						else { $('.one_click_buy_result').show(); $('.one_click_buy_result_fail').show(); $('.one_click_buy_result_text').text(data.message);}
						$('.one_click_buy_modules_button', self).removeClass('disabled');
						$('#one_click_buy_form').hide();
						$('#one_click_buy_form_result').show();
						
						/*if ($("#basket_line .basket_fly").length) { preAnimateBasketFly($("#basket_line .basket_fly"), 0, 0, false); }
						animateBasketLine(200);
						
						jsAjaxUtil.InsertDataToNode(arKShopOptions["KSHOP_SITE_DIR"]+'ajax/show_order.php?ORDER_ID='+data.message, 'content', true);
						jsAjaxUtil.InsertDataToNode(arKShopOptions["KSHOP_SITE_DIR"]+'ajax/show_basket_line.php', 'basket_line', false);
						
						jsAjaxUtil.ShowLocalWaitWindow( 'id', 'personal_block', true );
						$.ajax( { url: $("#auth_params").attr("action"), data: $("#auth_params").serialize() }).done(function( text ) 
						{
							$('#personal_block').html(text);
							jsAjaxUtil.CloseLocalWaitWindow( 'id', 'personal_block' );
						});*/
					}
				});
				return false;
			});
		}
		
		$('.'+name+'_frame').show();
	}
}

if (!isFunction("oneClickBuy"))
{
	var oneClickBuy = function (elementID, iblockID, that)
	{	
		name = 'one_click_buy';
		if(typeof(that) !== 'undefined'){
			elementQuantity = $(that).attr('data-quantity');
		}
		$('body').find('.'+name+'_frame').remove();
		$('body').append('<div class="'+name+'_frame popup"></div>');
		$('.'+name+'_frame').jqm({trigger: '.'+name+'_frame.popup', onHide: function(hash) { window.location.href = window.location.href; }, toTop: false, onLoad: function( hash ){ onLoadjqm(name, hash ); }, ajax: arKShopOptions["KSHOP_SITE_DIR"]+'ajax/one_click_buy.php?ELEMENT_ID='+elementID+'&IBLOCK_ID='+iblockID+'&ELEMENT_QUANTITY='+elementQuantity});
		$('.'+name+'_frame.popup').click();	
	}
}

if (!isFunction("oneClickBuyBasket"))
{
	var oneClickBuyBasket = function ()
	{	
		name = 'one_click_buy_basket'
		$('body').find('.'+name+'_frame').remove();
		$('body').append('<div class="'+name+'_frame popup"></div>');
		$('.'+name+'_frame').jqm({trigger: '.'+name+'_frame.popup', onHide: function(hash) { window.location.href = window.location.href; }, onLoad: function( hash ){ onLoadjqm( name, hash ); }, ajax: arKShopOptions["KSHOP_SITE_DIR"]+'ajax/one_click_buy_basket.php'});
		$('.'+name+'_frame.popup').click();	
	}
}


if (!isFunction("jqmEd"))
{
	var jqmEd = function (name, form_id, open_trigger, requestData, selector)
	{
		if(typeof(requestData) == "undefined"){
			requestData = '';
		}
		if(typeof(selector) == "undefined"){
			selector = false;
		}
		if (form_id != "auth"){
			if(form_id){
				form_id = parseInt(form_id);
			}
		}
		$('body').find('.'+name+'_frame').remove();
		$('body').append('<div class="'+name+'_frame popup"></div>');
		if (typeof open_trigger == "undefined" )
		{
			$('.'+name+'_frame').jqm({trigger: '.'+name+'_frame.popup', onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector); }, ajax: arKShopOptions["KSHOP_SITE_DIR"]+'ajax/form.php?form_id='+form_id+(requestData.length ? '&' + requestData : '')});
		}
		else
		{
			$('.'+name+'_frame').jqm({trigger: open_trigger,  onLoad: function( hash ){ onLoadjqm( name , hash , requestData, selector); }, ajax: arKShopOptions["KSHOP_SITE_DIR"]+'ajax/form.php?form_id='+form_id+(requestData.length ? '&' + requestData : '')});
			$(open_trigger).dblclick(function(){return false;})
		}	
		return true;		
	}
}
	

if (!isFunction("animateBasketLine"))
{	
	var animateBasketLine = function(speed, total_summ, total_count, wish_count)
	{
		if (typeof speed == "undefined") { speed = 200; } else {speed = parseInt(speed);}
		
		if ($("#basket_line .cart").length)
		{
			if (typeof total_count == "undefined" || typeof total_summ == "undefined" || typeof wish_count == "undefined")
			{
				$.getJSON( arKShopOptions['KSHOP_SITE_DIR']+"ajax/get_basket_count.php", function(data)
				{	
                                    console.log(jsPriceFormat(Math.floor(data.TOTAL_SUMM)));
                                    console.log(Math.floor(data.TOTAL_SUMM));
					if ($("#basket_line .cart").length)
					{
						var basketTotalSumm = parseFloat($('.basket_popup_wrapp input[name=total_price]').attr("value"));
						var basketTotalCount = parseInt($('.basket_popup_wrapp input[name=total_count]').attr("value"));
						var wishTotalCount = parseInt($('.basket_popup_wrapp input[name=delay_count]').attr("value"));
				
						if (data.WISH_COUNT>0 && !$("#basket_line .cart_wrapp.with_delay").is(":visible"))
						{
							setTimeout(function(){$("#basket_line .cart_wrapp:not(.with_delay)").fadeOut(333, 
								function() {
									$('#basket_line .total_summ').html(jsPriceFormat(Math.floor(data.TOTAL_SUMM)));
                                                                        
								});}, 200);
							$("#basket_line .cart_wrapp.with_delay").fadeIn(333, "easeOutSine");
						}
						else if (data.WISH_COUNT==0 && $("#basket_line .cart_wrapp.with_delay").is(":visible"))
						{
							setTimeout(function(){$("#basket_line .cart_wrapp.with_delay").fadeOut(333);}, 200)
							$("#basket_line .cart_wrapp:not(.with_delay)").fadeIn(333, "easeOutSine");
							$('#basket_line .total_summ').animateNumbers(data.TOTAL_SUMM, speed, true, basketTotalSumm);
						} 
						else { $('#basket_line .total_summ').animateNumbers(data.TOTAL_SUMM, speed, true, basketTotalSumm); }
						if (parseInt(data.TOTAL_COUNT)==0) {
							$("#basket_line .cart").addClass("empty_cart").find(".cart_wrapp a").removeClass("cart-call").attr("href", $("#basket_line input[name=path_to_basket]").attr("value"));
							$("#basket_line .cart_wrapp a.cart-call").unbind();
						}
						$('#basket_line .total_count').each(function(){$(this).animateNumbers(data.TOTAL_COUNT, speed, false, basketTotalCount);});
						$('#basket_line .delay_count').each(function(){$(this).animateNumbers(data.WISH_COUNT, speed, false, wishTotalCount);});
					}
					if ($("#basket_line .basket_fly").length)
					{
						var basketTotalCount = parseInt($('.basket_popup_wrapp input[name=total_count]').attr("value"));
						var wishTotalCount = parseInt($('.basket_popup_wrapp input[name=delay_count]').attr("value"));
						if (data.TOTAL_COUNT>0 && $('#basket_line .basket_count').is(".empty")) { $('#basket_line .basket_count').removeClass("empty");  }
						else if (data.TOTAL_COUNT==0 && !$('#basket_line .basket_count').is(".empty")) { $('#basket_line .basket_count').addClass("empty"); }
						if (data.WISH_COUNT>0 && $('#basket_line .wish_count').is(".empty")) { $('#basket_line .wish_count').removeClass("empty"); }
						else if (data.WISH_COUNT==0 && !$('#basket_line .wish_count').is(".empty")) { $('#basket_line .wish_count').addClass("empty"); }
						$('#basket_line .basket_count .count').animateNumbers(data.TOTAL_COUNT, speed, false, basketTotalCount);
						$('#basket_line .wish_count .count').animateNumbers(data.WISH_COUNT, speed, false, wishTotalCount);	
					}
				});
			} 
			else 
			{	
				if ($("#basket_line .cart").length)
				{
					var basketTotalSumm = parseFloat($('.basket_popup_wrapp input[name=total_price]').attr("value"));
					var basketTotalCount = parseInt($('.basket_popup_wrapp input[name=total_count]').attr("value"));
					var wishTotalCount = parseInt($('.basket_popup_wrapp input[name=delay_count]').attr("value"));
					if (wish_count>0 && !$("#basket_line .cart_wrapp.with_delay").is(":visible"))
					{
						setTimeout(function(){$("#basket_line .cart_wrapp:not(.with_delay)").fadeOut(333, 
								function() {
									$('#basket_line .total_summ').html(jsPriceFormat(Math.floor(total_summ)));
								});}, 200);
						$("#basket_line .cart_wrapp.with_delay").fadeIn(333, "easeOutSine");
					}
					else if (wish_count==0 && $("#basket_line .cart_wrapp.with_delay").is(":visible"))
					{
						setTimeout(function(){$("#basket_line .cart_wrapp.with_delay").fadeOut(333);}, 200)
						$("#basket_line .cart_wrapp:not(.with_delay)").fadeIn(333, "easeOutSine");
					}
					if (parseInt(total_count)==0)
					{
						$('#basket_line .total_summ').animateNumbers(0, speed, true, basketTotalSumm);
						$("#basket_line .cart").addClass("empty_cart").find(".cart_wrapp a.basket_link").removeClass("cart-call").attr("href", $("#basket_line input[name=path_to_basket]").attr("value"));
						$("#basket_line .cart_wrapp a.cart-call").unbind();
					} 
					else { $('#basket_line .total_summ').animateNumbers(total_summ, speed, true, basketTotalSumm); }
					$('#basket_line .total_count').animateNumbers(total_count, speed, false, basketTotalCount);
					$('#basket_line .delay_count').animateNumbers(wish_count, speed, false, wishTotalCount);
				}
				if ($("#basket_line .basket_fly").length)
				{
					var basketTotalCount = parseInt($('.basket_popup_wrapp input[name=total_count]').attr("value"));
					var wishTotalCount = parseInt($('.basket_popup_wrapp input[name=delay_count]').attr("value"));
						
					if (total_count>0 && $('#basket_line .basket_count').is(".empty")) { $('#basket_line .basket_count').removeClass("empty");  }
					else if (total_count==0 && !$('#basket_line .basket_count').is(".empty")) { $('#basket_line .basket_count').addClass("empty"); }
					if (wish_count>0 && $('#basket_line .wish_count').is(".empty")) { $('#basket_line .wish_count').removeClass("empty"); }
					else if (wish_count==0 && !$('#basket_line .wish_count').is(".empty")) { $('#basket_line .wish_count').addClass("empty"); }			
									
					$('#basket_line .basket_count .count').animateNumbers(total_count, speed, false, basketTotalCount);
					$('#basket_line .wish_count .count').animateNumbers(wish_count, speed, false, wishTotalCount);		
				}				
			}
		}
		return true;
	}
}	


if (!isFunction("replaceBasketPopup"))
{
	function replaceBasketPopup (hash)
	{
		if (typeof hash != "undefined")
		{
			hash.w.hide();
			hash.o.hide();
			//var basketTable = $(hash.w).find(".cart_shell");	
			//$(basketTable).html($("#new_basket").html());
		}
	}
}

if (!isFunction("deleteFromBasketPopup"))
{
	function deleteFromBasketPopup (basketWindow, delay, speed, item)
	{
		if (typeof basketWindow != "undefined" && typeof item != "undefined")
		{		
			var row = $(item).parents("tr.catalog_item");
			var total_count = parseInt($(basketWindow).find("input[name=total_count]").attr("value"))-1;
			
			if (total_count<3) { $(basketWindow).find(".cart_shell").css("height", ""); }
			else
			{
				if ($(basketWindow).find(".cart_shell").attr("style"))
				{
					if (!$(basketWindow).find(".cart_shell").attr("style").match(/height/)) { 
						$(basketWindow).find(".cart_shell").height($(basketWindow).find(".cart_shell").height()); 
					}
				}	
			}

			if (total_count==0) 
			{			
				setTimeout(function(){$(basketWindow).find(".popup-intro:not(.grey)").fadeOut(333);}, 200)
				$(basketWindow).find(".popup-intro.grey").fadeIn(333, "easeOutSine");
				$(basketWindow).find(".total_wrapp .total").slideUp(speed);
				$(basketWindow).find(".total_wrapp hr").slideUp(speed);
				$(basketWindow).find(".basket_empty").slideDown(speed);
				$(basketWindow).find(".total_wrapp .but_row").addClass("no_border").animate({"marginTop": 0});				
				if (parseInt($(basketWindow).find("input[name=delay_count]").attr("value"))>0)
				{
					setTimeout(function(){$(basketWindow).find(".but_row .to_basket").fadeOut(333);}, 200)
					$(basketWindow).find(".but_row .to_delay").fadeIn(333, "easeOutSine");
				} else { $(basketWindow).find(".but_row .to_basket").fadeOut(333); }
				
				setTimeout(function(){$(basketWindow).find(".but_row .checkout").fadeOut(333);}, 200)
				$(basketWindow).find(".but_row .close_btn").fadeIn(333, "easeOutSine");
				
				var newPrice = 0;
			}
			else 
			{ 
				var itemPrice = $(row).find("input[name=item_price_"+$(row).attr('product-id')+"]").attr("value");
				var currentPrice = $(basketWindow).find("input[name=total_price]").attr("value");
				var newPrice = currentPrice - itemPrice;
			}
			
			//preanimate while waiting ajax response
			$(row).find(".cost-cell .price:not(.discount)").animateNumbers(0, (speed*2), true);
			$(basketWindow).find(".total_wrapp .total .price:not(.discount)").animateNumbers(newPrice, (speed*3), true);
			
			$(row).find("td").wrapInner('<div class="slide_out"></div>');
			$(row).fadeTo(speed, 0);
			$(row).find(".slide_out").slideUp(speed, function() { $(row).remove(); });						
			$('.basket_button.in-cart[data-item='+$(row).attr("catalog-product-id")+']').hide();	
			$('.basket_button.to-cart[data-item='+$(row).attr("catalog-product-id")+']').show();					
			
			if ($("#basket_line").find(".basket_hidden tr.catalog_item").length)
			{
				var addedRow = $("#basket_line").find(".basket_hidden tr.catalog_item").first();
				$(addedRow).attr("animated", "false").find("td").wrapInner('<div class="slide"></div>');
				$(basketWindow).find(".cart_shell tbody").append($(addedRow));
				
				$(basketWindow).find(".catalog_item[animated=false]").each(function(index, element)
				{
					$(element).fadeTo((speed*2), 1, function(){$(element).removeAttr("animated")});
					$(element).find(".slide").slideDown(speed);
				});							
			}			
			
			//correct data
			$.get( item.attr("href"), function()
			{ 	$.get( arKShopOptions['KSHOP_SITE_DIR']+"ajax/show_basket_popup.php", $.proxy
				(
					function(data)
					{
						var newBasket  = $.parseHTML(data);
												
						var newSummPrice = parseFloat($(newBasket).find("input[name=total_price]").attr("value"));
						animateBasketLine(200, newSummPrice, parseInt($(newBasket).find("input[name=total_count]").attr("value")), parseInt($(newBasket).find("input[name=delay_count]").attr("value")));
						
						$(basketWindow).find("input[name=total_count]").attr("value", $(newBasket).find("input[name=total_count]").attr("value"));
						$(basketWindow).find("input[name=delay_count]").attr("value", $(newBasket).find("input[name=delay_count]").attr("value"));
						$(basketWindow).find("input[name=total_price]").attr("value", $(newBasket).find("input[name=total_price]").attr("value"));
						
						$(basketWindow).find(".total_wrapp .total .price").animateNumbers(newSummPrice, (speed*3), true); 	
						
						if  ($(newBasket).find(".total_wrapp .more_row").length)
						{
							if  ($(basketWindow).find(".total_wrapp .more_row").length) {
								$(basketWindow).find(".total_wrapp .more_row .count_message").html($(newBasket).find(".total_wrapp .more_row .count_message").html());
								$(basketWindow).find(".total_wrapp .more_row .count").animateNumbers(parseInt(delSpaces($(newBasket).find(".total_wrapp .more_row .count").text()).replace(/,/g, "")), speed, false);
							}	
						} 
						else
						{
							var target = $(basketWindow).find(".total_wrapp .more_row");
							$(target).fadeTo(speed, 0, function(){$(target).remove();});
						}
						
						//correct all prices
						$(newBasket).find(".catalog_item").each(function(index, element)
						{
							var itemPrice = $(element).find("input[name^=item_price]").attr("value");									
							if ($(basketWindow).find(".catalog_item input[name=item_price_"+$(element).attr('product-id')+"]").length &&
								$(basketWindow).find(".catalog_item input[name=item_price_"+$(element).attr('product-id')+"]").attr("value") != itemPrice)
							{
								$(basketWindow).find(".catalog_item[product-id="+$(element).attr('product-id')+"] .price:not(.discount)").animateNumbers(itemPrice, speed, true);
							}
						});	
						
						//save some hidden elements for animate deleting
						$("#basket_line").find(".basket_hidden").html($(newBasket).find(".basket_hidden").html());
					}
				));
			});			
		}
	}
}

if (!isFunction("preAnimateBasketFly"))
{
	function preAnimateBasketFly (basketWindow, delay, speed, shift)
	{	
		if (typeof basketWindow != "undefined")
		{
			if (typeof delay == "undefined") {delay = 100;} else {delay = parseInt(delay);}
			if (typeof speed == "undefined") {speed = 200;} else {speed = parseInt(speed);}

			if ($(basketWindow).is(".basket_empty")) { $(basketWindow).removeClass("basket_empty"); }
			
			$.post( arKShopOptions['KSHOP_SITE_DIR']+"ajax/show_basket_fly.php", "PARAMS="+$(basketWindow).find("input#fly_basket_params").val(), $.proxy
			(
				function(data) 
				{			
					var newBasket  = $.parseHTML(data);
					$(basketWindow).find(".tabs_content.basket li").each(function(i, element)
					{
						if ($(element).attr("item-section")!="AnDelCanBuy")
						{
							if ($(newBasket).find(".tabs_content.basket li[item-section="+$(element).attr("item-section")+"]").length)
							{
								$(element).html($(newBasket).find(".tabs_content.basket li[item-section="+$(element).attr("item-section")+"]").html()); 
							}
							else
							{
								$(element).remove();
							}
						}
						else if($(element).find(".cart_empty").length)
						{
							$(element).find(".cart_empty").remove();
							$(element).html($(newBasket).find(".tabs_content.basket li[item-section=AnDelCanBuy]").html());
							$(element).find(".module-cart tbody").html("");							
							$(element).find(".row_values div[data-type=price_discount] .price:not(.discount)").html("0");
							$(element).find(".row_values div[data-type=price_discount] .price.discount strike").html("0");
							$(element).find(".row_values div[data-type=price_normal] .price").html("0");
						}
					});
										
					$(basketWindow).find(".basket_sort").html($(newBasket).find(".basket_sort").html());
					$(basketWindow).find(".tabs_content li").first().addClass("cur").siblings().removeClass("cur");
					$(basketWindow).find(".tabs li").first().addClass("cur").siblings().removeClass("cur");

					
					$(newBasket).find(".tabs_content.basket li").each(function(i, element)
					{
						if (!$(basketWindow).find(".tabs_content.basket li[item-section="+$(element).attr("item-section")+"]").length)
						{
							$(basketWindow).find(".tabs_content.basket").append($(element));
						}
					});
					
					if (typeof shift == "undefined" || shift!=false)
					{
						if (parseInt($(basketWindow).css("right"))<0) 
						{ 
							if(arKShopOptions['SHOW_BASKET_ONADDTOCART'] !== 'N'){
								$(basketWindow).stop().animate({"right": "0"}, 333, function()
								{
									postAnimateBasketFly ($(basketWindow).find(".tabs_content.basket li[item-section=AnDelCanBuy]"), $(newBasket).find(".tabs_content.basket li[item-section=AnDelCanBuy]"), delay, speed);
								}); 
							}
							else{
								postAnimateBasketFly ($(basketWindow).find(".tabs_content.basket li[item-section=AnDelCanBuy]"), $(newBasket).find(".tabs_content.basket li[item-section=AnDelCanBuy]"), delay, speed);
							}
						}
						else
						{
							postAnimateBasketFly (basketWindow.find(".tabs_content.basket li[item-section=AnDelCanBuy]"), $(newBasket).find(".tabs_content.basket li[item-section=AnDelCanBuy]"), delay, speed);
						}
					}					
				}				
			));
		}
	}
}


if (!isFunction("postAnimateBasketFly"))
{
	function postAnimateBasketFly (oldBasket, newBasket, delay, speed)
	{	
		setTimeout(function() //animation could be delayed
		{		
			if (typeof oldBasket != "undefined" && typeof newBasket != "undefined")
			{
				var rows = $(newBasket).find(".module-cart tbody tr[data-id]:not(.hidden)");	
				$(rows).each(function(i, element)
				{
					if (!$(oldBasket).find("tr[data-id="+$(element).attr("data-id")+"]").length)
					{									
						var itemRow = $(element).clone();
						var itemPrice = $(itemRow).find("input[name=item_price_"+$(itemRow).attr('product-id')+"]").attr("value");
						var itemDiscountPrice = $(itemRow).find("input[name=item_price_"+$(itemRow).attr('product-id')+"]").attr("value");
						$(itemRow).attr("animated", "false").find("td").wrapInner('<div class="slide"></div>');
						$(oldBasket).find(".module-cart tbody").prepend($(itemRow));
					} else { if ($(element).attr("animated")=="false") { $(element).removeAttr("animated"); } }								
				});
	
				
				$(oldBasket).find("tbody tr[animated=false]").each(function(index, element)
				{
					$(element).find(".thumb-cell img").css({"maxHeight": "inherit", "maxWidth": "inherit"}).fadeTo((speed), 1, function() { $(element).removeAttr("animated") });
					$(element).find(".slide").slideDown(speed);
					
					$(element).find(".cost-cell .price:not(.discount)").html("0");	
					$(element).find(".cost-cell .price:not(.discount)").animateNumbers($(newBasket).find("input[name=item_price_"+$(element).attr("data-id")+"]").attr("value"), (speed*2), true, 0, "", function () { $(element).removeAttr("animated"); });
					if($(element).find(".cost-cell .price.discount"))
					{
						$(element).find(".cost-cell .price.discount strike").html("0");
						$(element).find(".cost-cell .price.discount strike").animateNumbers($(newBasket).find("input[name=item_price_discount_"+$(element).attr("data-id")+"]").attr("value"), (speed*2), true, 0, "", function () { $(element).removeAttr("animated"); });
					} 					
					$(element).find(".summ-cell").html("0");	 
					$(element).find(".summ-cell").animateNumbers($(newBasket).find("input[name=item_summ_"+$(element).attr("data-id")+"]").attr("value"), (speed*2), true, 0, "", function () { $(element).removeAttr("animated"); });
					
				});	

				var results = $(newBasket).find("tr[data-id=total_row]");	
				$(results).find(".row_values div[data-type]").each(function(e, element)
				{
					if ($(oldBasket).find("tr[data-id=total_row] .row_values div[data-type="+$(element).attr("data-type")+"]").length)
					{	
						var newPrice = parseInt(delSpaces($(element).find("span.price").text()).replace(/,/g, ""));
						var newDiscountPrice = parseInt(delSpaces($(element).find("div.price.discount strike").text()).replace(/,/g, ""));	
						var dataBlock = $(oldBasket).find("tr[data-id=total_row] .row_values div[data-type="+$(element).attr("data-type")+"]");
						if ($(element).attr("data-type")=="price_discount")
							{
								$(dataBlock).find("span.price:not(.discount)").stop().animateNumbers(newPrice, speed, true);
								$(dataBlock).find("div.price.discount strike").stop().animateNumbers(newDiscountPrice, speed, true);							
							}	
						else if ($(element).attr("data-type")=="price_normal")
							{ $(dataBlock).find("span.price").stop().animateNumbers(newPrice, speed, true); }
						else 
							{ $(dataBlock).find("span.price").stop().animateNumbers(newPrice, speed, false); }							
					}
					else
					{
						if ($(element).attr("data-type")!= "price_discount" && $(element).attr("data-type") != "price_normal")
						{
							$(oldBasket).find("tr[data-id=total_row] .row_values").append($(element));
							$(oldBasket).find("tr[data-id=total_row] .row_values div[data-type="+$(element).attr("data-type")+"]").hide().fadeOut(0);
							$(oldBasket).find("tr[data-id=total_row] .row_values div[data-type="+$(element).attr("data-type")+"]").hide().slideDown(200).fadeIn(200);
						}
					}
				});
				$(oldBasket).find(".row_values div[data-type]").each(function(e, element)
				{
					if (!$(results).find(".row_values div[data-type="+$(this).attr("data-type")+"]").length)
					{
						if ($(this).attr("data-type")=="price_discount" && $(results).find(".row_values div[data-type=price_normal]").length)
						{
							$(this).attr("data-type", "price_normal");
							$(this).find(".price.discount").fadeOut(200).slideUp(333, function(){$(this).find(".price.discount").remove();});	
						}
						else if ($(this).attr("data-type")=="price_normal" && $(results).find(".row_values div[data-type=price_discount]").length)
						{
							$(this).attr("data-type", "price_discount");
							$(this).append("<div class='price discount'><strike>"+$(results).find(".row_values div[data-type=price_discount] strike").html()+"</strike></div>").hide().fadeOut(0);
							$(this).find(".price.discount").slideDown(333).fadeIn(200);
						}
						else
						{
							$(element).fadeOut(200).slideUp(333, function(){$(element).remove();});
						}
					}
				});
				
				checkRowValuesFly(oldBasket);
			}
		}, delay);
	}
}

if (!isFunction("checkRowValuesFly"))
{
	function checkRowValuesFly(basketFly) {
		var h = $(basketFly).find('.goods table').height();
		if(h > 200){
			$(basketFly).find('.itog .row_values').addClass('mt3');
		}
		else if(h > 0){
			$(basketFly).find('.itog .row_values').removeClass('mt3');
		}
	}
}

if (!isFunction("preAnimateBasketPopup"))
{
	function preAnimateBasketPopup (hash, basketWindow, delay, speed)
	{		
		if (typeof basketWindow != "undefined")
		{
			if ($(basketWindow).find(".popup-intro.grey").css("display")=="block") 
			{ 
				$(basketWindow).find(".popup-intro.grey").hide();
				$(basketWindow).find(".basket_empty").hide();
				$(basketWindow).find(".popup-intro:not(.grey)").show();
				$(basketWindow).find(".total_wrapp .total").show();
				$(basketWindow).find(".total_wrapp hr").show();
				$(basketWindow).find(".but_row .close_btn").hide();
				$(basketWindow).find(".but_row .checkout").show();
				$(basketWindow).find(".but_row .to_delay").hide();
				$(basketWindow).find(".but_row .to_basket").show();
				$(basketWindow).find(".total_wrapp .but_row").removeClass("no_border").css({"marginTop": "", "paddingTop": ""});
			}
			
			if (typeof delay == "undefined") {delay = 100;} else {delay = parseInt(delay);}
			if (typeof speed == "undefined") {speed = 200;} else {speed = parseInt(speed);}
			
			var popupWidth = $(basketWindow).width();
			$(basketWindow).css({'margin-left': '-'+popupWidth/2+'px', 'display': 'block'});			
			
			if ($(basketWindow).is("[animate]"))
			{
				$(basketWindow).removeAttr("animate");
				$.get( arKShopOptions['KSHOP_SITE_DIR']+"ajax/show_basket_popup.php", $.proxy
				(
					function(data) 
					{ 
						var newBasket  = $.parseHTML(data);
						
						$(basketWindow).find("input[name=total_count]").attr("value", $(newBasket).find("input[name=total_count]").attr("value"));
						$(basketWindow).find("input[name=delay_count]").attr("value", $(newBasket).find("input[name=delay_count]").attr("value"));
						$(basketWindow).find("input[name=total_price]").attr("value", $(newBasket).find("input[name=total_price]").attr("value"));
													
						//save some hidden elements for animate deleting
						$("#basket_line").find(".basket_hidden").html($(newBasket).find(".basket_hidden").html());
						
						var newSummPrice = parseFloat($(newBasket).find("input[name=total_price]").attr("value"));
						var rows = $(newBasket).find(".cart_shell .catalog_item[product-id]");
						
						$(rows).each(function()
						{
							if (!$(basketWindow).find(".catalog_item[product-id="+$(this).attr("product-id")+"]").length)
							{									
								var itemRow = $(this).clone();
								var itemPrice = $(itemRow).find("input[name=item_price_"+$(itemRow).attr('product-id')+"]").attr("value");
								$(itemRow).attr("animated", "false").find("td").wrapInner('<div class="slide"></div>');
								$(basketWindow).find(".cart_shell tbody").prepend($(itemRow));
							} else { if ($(this).attr("animated")=="false") { $(this).removeAttr("animated"); } }								
						});	
						
						setTimeout(function() //animation could be delayed
						{
							if ($(newBasket).find("input[name=total_count]").attr("value")>=3)
							{ 
								if ($(basketWindow).find(".cart_shell").attr("style"))
								{
									if (!$(basketWindow).find(".cart_shell").attr("style").match(/height/)) { 
										$(basketWindow).find(".cart_shell").height($(basketWindow).find(".cart_shell").height()); 
									}
								} else { $(basketWindow).find(".cart_shell").height($(basketWindow).find(".cart_shell").height()); }
							} else { $(basketWindow).find(".cart_shell").css("height", ""); }
							
							$(basketWindow).find("tr.catalog_item").each(function(index, element)
							{
								if (index<3) return true; 
								else {
									$(element).find("td").wrapInner('<div class="slide_out"></div>');
									$(element).find(".slide_out").slideUp(speed, function() { $(element).remove(); });
								}
							});		
							
							$(basketWindow).find(".catalog_item[animated=false]").each(function(index, element)
							{
								$(element).fadeTo((speed*2), 1, function(){$(element).removeAttr("animated")});
								$(element).find(".slide").slideDown(speed);
							});	

							$(basketWindow).find(".catalog_item[animated=false]").each(function(index, element)
							{
								$(element).find(".cost-cell .price").html("0");			
								$(element).find(".cost-cell .price").animateNumbers($(newBasket).find("input[name=item_price_"+$(element).attr("product-id")+"]").attr("value"), (speed*2), true, 0, "", function () { $(element).removeAttr("animated"); });
							});

							if  ($(newBasket).find(".total_wrapp .more_row").length)
							{
								if  ($(basketWindow).find(".total_wrapp .more_row").length)
								{
									$(basketWindow).find(".total_wrapp .more_row .count_message").html($(newBasket).find(".total_wrapp .more_row .count_message").html());
									$(basketWindow).find(".total_wrapp .more_row .count").animateNumbers(parseInt(delSpaces($(newBasket).find(".total_wrapp .more_row .count").text()).replace(/,/g, "")), speed, false);
								}
								else
								{
									$(basketWindow).find(".total_wrapp").prepend($(newBasket).find(".total_wrapp .more_row").fadeTo(0,0));
									$(basketWindow).find(".total_wrapp .more_row").fadeTo(speed, 1);
								}
							}
								
							//correct all prices
							$(newBasket).find(".catalog_item").each(function(index, element)
							{
								var itemPrice = $(element).find("input[name^=item_price]").attr("value");									
								if ($(basketWindow).find(".catalog_item input[name=item_price_"+$(element).attr('product-id')+"]").length &&
									$(basketWindow).find(".catalog_item input[name=item_price_"+$(element).attr('product-id')+"]").attr("value") != itemPrice)
								{
									$(basketWindow).find(".catalog_item[product-id="+$(element).attr('product-id')+"] .price:not(.discount)").animateNumbers(itemPrice, speed, true);
								}
							});	
			
							$(basketWindow).find(".total_wrapp .total .price").animateNumbers(newSummPrice, (speed*3), true); 
						}, delay);
					}
				));
			}	
		}	
	}
}
$(document).ready(function()
{
	//some adaptive hacks
	$(window).resize(function () 
	{
		waitForFinalEvent(function() 
		{
			if ($(window).outerWidth()>600 && $(window).outerWidth()<768 && $(".catalog_detail .buy_buttons_wrapp a").length>1) 
			{ 
				var adapt = false;
				var prev;
				$(".catalog_detail .buy_buttons_wrapp a").each(function(i, element)
				{
					prev = $(".catalog_detail .buy_buttons_wrapp a:eq("+(i-1)+")");
					if ($(this).offset().top!=$(prev).offset().top && i>0) { $(".catalog_detail .buttons_block").addClass("adaptive"); }
				});
			} else { $(".catalog_detail .buttons_block").removeClass("adaptive"); }			
			
			if ($(window).outerWidth()>600)
			{		
				$("#header ul.menu").removeClass("opened").css("display", "");
				
				if ($(".authorization-cols").length)
				{
					$('.authorization-cols').equalize({children: '.col .auth-title', reset: true}); 
					$('.authorization-cols').equalize({children: '.col .form-block', reset: true}); 
				}
			}
			else
			{
				$('.authorization-cols .auth-title').css("height", "");
				$('.authorization-cols .form-block').css("height", "");
			}
			
			
			if ($(window).width()>=400)
			{
				var textWrapper = $(".catalog_block .catalog_item .item-title").height();
				var textContent = $(".catalog_block .catalog_item .item-title a");
				$(textContent).each(function()
				{
					if ($(this).outerHeight()>textWrapper) 
					{
						$(this).text(function (index, text) { return text.replace(/\W*\s(\S)*$/, '...'); });
					}
				});	
				$('.catalog_block').equalize({children: '.catalog_item .cost', reset: true}); 
				$('.catalog_block').equalize({children: '.catalog_item .item-title', reset: true}); 
				$('.catalog_block').equalize({children: '.catalog_item', reset: true}); 
			}
			else
			{
				$(".catalog_block .catalog_item").removeAttr("style");
				$(".catalog_block .catalog_item .item-title").removeAttr("style");
				$(".catalog_block .catalog_item .cost").removeAttr("style");
			}
			
			if ($("#basket_form").length && $(window).outerWidth()<=600)
			{
				$("#basket_form .tabs_content.basket li.cur td").each(function() { $(this).css("width","");});
			}
			
			
			if ($("#header .catalog_menu").length && $("#header .catalog_menu").css("display")!="none")
			{
				if ($(window).outerWidth()>600)
				{
					reCalculateMenu();
				}
			}
			
			if ($(".front_slider_wrapp").length)
			{
				$(".extended_pagination li i").each(function() 
				{ 
					$(this).css({"borderBottomWidth": ($(this).parent("li").outerHeight()/2), "borderTopWidth": ($(this).parent("li").outerHeight()/2)}); 
				});
			}
			
		}, 
		50, fRand());
	});	

	
	jqmEd('enter', 'auth', '.avtorization-call.enter');
	jqmEd('feedback', arKShopOptions['FEEDBACK_FORM_ID']);
	jqmEd('ask', arKShopOptions['ASK_FORM_ID'], '.ask_btn');
	jqmEd('resume', arKShopOptions['RESUME_FORM_ID'], '.resume_send');

	$('.to-order').live( 'click', function(e){
		e.preventDefault();
		$("body").append("<span class='evb-toorder' style='display:none;'></span>");
		jqmEd('to-order', arKShopOptions['TOORDER_FORM_ID'], '.evb-toorder', '', this);
		$("body .evb-toorder").click();		
		$("body .evb-toorder").remove();
	});
	
	$(".counter_block:not(.basket) .plus").live("click", function()
	{
		var input = $(this).parents(".counter_block").find("input[type=text]");
		input.val(parseInt(input.val())+1);
		input.change();
	});
	
	$(".counter_block:not(.basket) .minus").live("click", function()
	{
		var input = $(this).parents(".counter_block").find("input[type=text]");
		var currentValue = parseInt(input.val());
		if (currentValue>1) 
		{ 
			input.val(parseInt(input.val())-1);
			$(this).parents(".counter_block").find("input[type=text]").change();
		}
	});

	$('.counter_block input[type=text]').numeric();
	
	$('.counter_block input[type=text]').live('change', function(e)
	{
		var val = $(this).val();
		$(this).parents('.counter_block').parent().parent().find('.to-cart').attr('data-quantity', val);		
		$(this).parents('.counter_block').parent().parent().find('.one_click').attr('data-quantity', val);		
	});

	$('.to-cart').live( 'click', function(e)
	{
		e.preventDefault();
		$(this).hide();
		var th=$(this);
		var val = $(this).attr('data-quantity');
		if (!val) $val = 1;
		var item = $(this).attr('data-item');
		$(this).parent().find('.in-cart').show();		
		$.get( arKShopOptions['KSHOP_SITE_DIR']+"ajax/item.php?item="+item+"&quantity="+val+"&add_item=Y", 
			$.proxy
			(
				function() 
				{ 	
					$('.wish_item[data-item='+item+']').removeClass("added");
					$('.wish_item[data-item='+item+']').find(".value").show();
					$('.wish_item[data-item='+item+']').find(".value.added").hide();
					if ($("#basket_line .basket_fly").length && $(window).outerWidth()>768)
					{
						preAnimateBasketFly($("#basket_line .basket_fly"), 200, 333);
					}
					else if ($("#basket_line .cart").length)
					{
						if ($("#basket_line .cart").is(".empty_cart"))
						{
							$("#basket_line .cart").removeClass("empty_cart").find(".cart_wrapp a.basket_link").removeAttr("href").addClass("cart-call");
							$(".cart-call:not(.small)").click(function(){$('.card_popup_frame').jqmShow();}) //dirty hack, jqmAddTrigger doesn't work here, fix it
						}
						if ($(window).outerWidth()>520){
							if(arKShopOptions['SHOW_BASKET_ONADDTOCART'] !== 'N'){
								$('.card_popup_frame').attr("animate", "true").jqmShow();
							}
						};
					}
					animateBasketLine(200);
				}
			)
		);
	})
	
	$('.to-subscribe').live( 'click', function(e)
	{
		e.preventDefault();
		
		if ($(this).is(".auth"))
		{
			$(".avtorization-call.enter").click();
		}
		else
		{
			var item = $(this).attr('data-item');
			$(this).hide();
			$(this).parent().find('.in-subscribe').show();	
			
			$.get( arKShopOptions['KSHOP_SITE_DIR']+"ajax/item.php?item="+item+"&subscribe_item=Y",
				$.proxy
				(
					function(data) 
					{
						if ($("#basket_line .basket_fly").length) { preAnimateBasketFly($("#basket_line .basket_fly"), 0, 0, false); }
						$('.wish_item[data-item='+item+']').removeClass("added");	
						$.getJSON( arKShopOptions['KSHOP_SITE_DIR']+"ajax/get_basket_count.php", function(data) { animateBasketLine(200); });
						
						
					/*	if ($("#basket_line .basket_fly").length && $(window).outerWidth()>768)
						{
							preAnimateBasketFly($("#basket_line .basket_fly"), 0, 0, false);
						}
						
						*/
					}
				)
			);
		}
	})
	
	$('.in-subscribe').live( 'click', function(e)
	{
		e.preventDefault();
		var item = $(this).attr('data-item');
		$(this).hide();
		$(this).parent().find('.to-subscribe').show();	
		
		$.get( arKShopOptions['KSHOP_SITE_DIR']+"ajax/item.php?item="+item+"&subscribe_item=Y",
			$.proxy
			(
				function(data) 
				{
					if ($("#basket_line .basket_fly").length) { preAnimateBasketFly($("#basket_line .basket_fly"), 0, 0, false); }
					$.getJSON( arKShopOptions['KSHOP_SITE_DIR']+"ajax/get_basket_count.php", function(data) { animateBasketLine(200); });
				}
			)
		);
	})
	
	$('.wish_item').live( 'click', function(e)
	{
		e.preventDefault();
		var item = $(this).attr('data-item');
		$(this).toggleClass("added");
		if ($(this).find(".value.added").length) 
		{ 
			if ($(this).find(".value.added").css("display")=="none") { $(this).find(".value").hide(); $(this).find(".value.added").show(); }
			else { $(this).find(".value").show(); $(this).find(".value.added").hide(); }
		}
		$('.basket_button.in-cart[data-item='+item+']').hide();	
		$('.basket_button.to-cart[data-item='+item+']').show();
		$('.wish_item[data-item='+item+']').find(".value").hide();
		$('.wish_item[data-item='+item+']').find(".value.added").show();
		
		
		$.get( arKShopOptions['KSHOP_SITE_DIR']+"ajax/item.php?item="+item+"&wish_item=Y", 
			$.proxy
			(
				
				function(data) 
				{
					if ($("#basket_line .basket_fly").length) { preAnimateBasketFly($("#basket_line .basket_fly"), 0, 0, false); } 
					animateBasketLine(200);
				}
			)
		);
	})
	
	$('.compare_item').live( 'click', function(e)
	{
		e.preventDefault();
		var item = $(this).attr('data-item');
		var iblockID = $(this).attr('data-iblock');
		$(this).toggleClass("added");
		if ($(this).find(".value.added").length) 
		{ 
			if ($(this).find(".value.added").css("display")=="none") { $(this).find(".value").hide(); $(this).find(".value.added").show(); }
			else { $(this).find(".value").show(); $(this).find(".value.added").hide(); }
		}
		
		$.get( arKShopOptions['KSHOP_SITE_DIR']+"ajax/item.php?item="+item+"&compare_item=Y&iblock_id="+iblockID, 
			$.proxy
			(
				function(data) { jsAjaxUtil.InsertDataToNode(arKShopOptions['KSHOP_SITE_DIR']+"ajax/show_compare_preview.php", 'compare_small', false); }
			)
		);
	})

	$(".compare_frame").remove();
	$('body').append('<span class="compare_frame popup"></span>');
	$('.compare_frame').jqm({trigger: '.compare_link', onLoad: function(hash){ onLoadjqm('compare', hash); }, ajax: arKShopOptions['KSHOP_SITE_DIR']+'ajax/show_compare_list.php'});
	$('.fancy').fancybox();
	
});