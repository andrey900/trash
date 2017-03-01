Studio8.Widget('basket', {
	documentReady: true,
	init: function(){
		var self = this;
		$('.cart-tab li a').on("click", function(){
	        $(this).addClass("active");
	        $(this).parent('li').prevAll('li').find('a').addClass("active");
	        $(this).parent('li').nextAll('li').find('a').removeClass("active");
	    });
	    $(document).on('click', '.step-to-order', function(e){
			e.preventDefault();
			$('a[href="#checkout"]').click();
			self.scrollToTopOrder();
		});
        if( window.location.hash ){
            var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
            var fi = $('.cart-tab a[href="#'+hash+'"]');
            if(fi.length > 0){
                fi.click();
            }
        }
	},
	scrollToTopOrder: function(){
		if( $('#sticky-header').hasClass('sticky') ){
			$('html, body').animate({
	            scrollTop: $('.header-area').height() + $('.breadcrumbs-section').height() - 50
			}, 300, 'linear');
		}
	}
});
Studio8.Widget('changeQuantityInBasket', {
	documentReady: true,
	init: function(){
		this.plusminusInit(),
		this.updateBasketTable(),
		this.events()
	},
	updateBasketTable: function(){
		var btnPM = $('.product-quantity .qtybutton');
		if(btnPM.length > 0){
			btnPM.each(function(){
				$(this).addClass('update-basket');
			})
		}
		var btnPM = $('#small-basket .remove-of-cart');
		if( btnPM.length > 0 ){
			btnPM.each(function(){
				$(this).addClass('update-basket');
			})
		}
	},
	plusminusInit: function(){
		var self = this;
		$(".cart-plus-minus").prepend('<div class="dec qtybutton">-</div>');
	    $(".cart-plus-minus").append('<div class="inc qtybutton">+</div>');
	    $(".qtybutton").on("click", function() {
	        var $button = $(this);
	        var oldValue = $button.parent().find("input").val();
	        if ($button.text() == "+") {
	            var newVal = parseFloat(oldValue) + 1;
	        } else {
	            // Don't allow decrementing below zero
	            if (oldValue > 1) {
	                var newVal = parseFloat(oldValue) - 1;
	            } 
	            else {
	                newVal = 1;
	            }
	        }

	        $button.parent().find("input").val(self.changeHandler(newVal, $button.closest('.product-card').attr("data-product-id")));
	    });
	},
	events: function(){
		var self = this;
		$(document).on('input', '.cart-plus-minus-box', function(){
			var $this = $(this);
			$this.val(self.changeHandler($this.val(), $this.closest('.product-card').attr("data-product-id")));
		});
		$(document).on('click', '.update-basket', function(){
			$.ajax({
				method: 'get',
				url: '/basket/'
			}).done(function(content){
				var bsT = $(content).find('.basket-table');
				if( bsT.length > 0 ){
					var tbsT = $(document).find('.basket-table')
						tbsT.hide().after(bsT);
						tbsT.remove();
					self.plusminusInit();
					self.updateBasketTable();
				}
				bsT = $(content).find('.empty-basket');
				if( bsT.length > 0 ){
					var tbsT = $(document).find('.shop-section.mb-80 > .container')
						tbsT.hide().after(bsT);
						tbsT.remove();
				}
			});
		});
        $(document).on('focus', '.field-error', function(e){
            $(this).removeClass('field-error');
        });
		$(document).on('click', '.send-order', function(e){
			e.preventDefault();

            var data = Object.create(null);

            $.each($('.form-order.ajax').serializeArray(), function(i, e){
                data[e.name] = e.value;
            });

			$.ajax({
				method: 'post',
				url: '/ajax/order/',
				dataType: 'json',
				data: data
			}).done(function(data){
                if( data.status == 'error' ){
                    if( data.data.length > 0 ){
                        for (var i = data.data.length - 1; i >= 0; i--) {
                            var sel = 'input[name="' + data.data[i]['input'] + '"]';
                            $(sel).addClass('field-error');
                        }
                        Studio8.Widget('basket').scrollToTopOrder();
                    }
                } else {
                    window.location.href = window.location.pathname + "?success=" + data.data.token;
                }
			});
		});
	},
	changeHandler: function(value, productId){
		value = parseInt(value);
		if( value <= 0 || value !== value ){
			value = 1;
		}

		Studio8.Widget('addToCart').helpers.sendAjaxBasket('quantity', {id: productId, quantity: value});

		return value;
	}
})