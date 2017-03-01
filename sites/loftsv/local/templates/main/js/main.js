(function(){
    function Studio8(){
        this.widgets = {};
        this.initDocumentReady = [];
    };
    Studio8.prototype.Widget = function(name, callback){
        if(!this.hasWidget(name) && typeof callback == 'object')
            this.widgets[name] = callback;
        else if( this.hasWidget(name) )
        	return this.widgets[name];

        if( this.widgets[name].documentReady === true && document.readyState != 'complete' ){
        	this.initDocumentReady.push(name);
        } else {
            this.loadWidget(name);
        }
    };
    Studio8.prototype.hasWidget = function(name){
        if( this.widgets[name] !== undefined )
            return true;
        
        return false;
    };
    Studio8.prototype.initReady = function(){
    	for (var i = 0; i < this.initDocumentReady.length; i++) {
    		this.loadWidget(this.initDocumentReady[i]);
    	}
    };
    Studio8.prototype.loadWidget = function(name){
    	if( this.widgets[name]['init'] && typeof this.widgets[name]['init'] == 'function' ){
            this.widgets[name]['init']();
        }
    }

    if( window.Studio8 === undefined )
        window.Studio8 = new Studio8();
})();

$(document).ready(function(){
	Studio8.initReady();
});

Studio8.Widget('main', {
	ddm: false,
	wow: false,
	documentReady: true,
	init: function(){
		this.stikyHeader().scrollUp().other();
	},
	stikyHeader: function(){
		$(window).scroll(function() {
            if ($(this).scrollTop() > 1){  
                $('#sticky-header').addClass("sticky");
            }
            else{
                $('#sticky-header').removeClass("sticky");
            }
        });
        return this;
	},
	scrollUp: function(){
		$.scrollUp({
			scrollText: '<i class="zmdi zmdi-chevron-up"></i>',
			easingType: 'linear',
			scrollSpeed: 900,
			animation: 'fade'
		});
		return this;
	},
	other: function(){
		this.ddm = $('nav#dropdown').meanmenu();
		this.wow = new WOW().init();
		$('[data-toggle="tooltip"]').tooltip();
		//$('.fancybox').fancybox();

		return this;
	}
});

Studio8.Widget('homeFeatureProductsSlider', {
	documentReady: true,
	init: function(){
		$('.active-featured-product').slick({
	        speed: 700,
	        arrows: true,
	        dots: false,
	        slidesToShow: 4,
	        slidesToScroll: 1,
	        prevArrow: '<button type="button" class="arrow-prev"><i class="zmdi zmdi-long-arrow-left"></i></button>',
	        nextArrow: '<button type="button" class="arrow-next"><i class="zmdi zmdi-long-arrow-right"></i></button>',
	        responsive: [
	            {  breakpoint: 991,   settings: { slidesToShow: 3,  }  },
	            {  breakpoint: 767,   settings: { slidesToShow: 1, }   },
	            {  breakpoint: 479,   settings: { slidesToShow: 1, }   },
	        ]
	    });
	}
})

Studio8.Widget('other', {
	documentReady: true,
	init: function(){
		$('.active-related-product').slick({
	        speed: 700,
	        arrows: true,
	        dots: false,
	        slidesToShow: 4,
	        slidesToScroll: 1,
	        prevArrow: '<button type="button" class="arrow-prev"><i class="zmdi zmdi-long-arrow-left"></i></button>',
	        nextArrow: '<button type="button" class="arrow-next"><i class="zmdi zmdi-long-arrow-right"></i></button>',
	        responsive: [
	            {  breakpoint: 991,   settings: { slidesToShow: 2,  }  },
	            {  breakpoint: 767,   settings: { slidesToShow: 1, }   },
	            {  breakpoint: 479,   settings: { slidesToShow: 1, }   },
	        ]
	    });
	    $('[data-countdown]').each(function() {
	        var $this = $(this), finalDate = $(this).data('countdown');
	        $this.countdown(finalDate, function(event) {
	            $this.html(event.strftime('<span class="cdown days"><span class="time-count">%-D</span> <p>Days</p></span> <span class="cdown hour"><span class="time-count">%-H</span> <p>Hour</p></span> <span class="cdown minutes"><span class="time-count">%M</span> <p>Mint</p></span> <span class="cdown second"> <span><span class="time-count">%S</span> <p>Sec</p></span>'));
	        });
	    });
	    this.basketFixMobile();
	},
	basketFixMobile: function(){
		$(document).on('click', '#small-basket .show-cart', function(){
			$(this).closest('#small-basket').find('ul').toggleClass('show-always');
		});
		$(document).on('click', '#small-basket .close-cart', function(){
			$('body *').unbind('mouseenter mouseleave');
			var item = $(this).closest('#small-basket').find('ul').removeClass('show-always').addClass('hidden');
			setTimeout(function(){item.removeClass('hidden')}, 300);
		})
	}
});

Studio8.Widget('orderChanger', {
	documentReady: true,
	init: function(){
		$(document).on('change', '.order-change', function(){
			if(window.location.href.search('order=') > 0){
				window.location.href = window.location.href.replace(/order=[\w\d-]+/g, 'order=' + $(this).val());
			}else if(window.location.href.search(/\?/i) > 0){
				window.location.href = window.location.href + "&order=" + $(this).val();
			} else {
				window.location.href = window.location.href + "?order=" + $(this).val();
			}
		});
	}
});

Studio8.Widget('quickShowProduct', {
	documentReady: true,
	init: function(){
		var self = this;
		$('#productModal').on('show.bs.modal', function () {
			$(this).find('.modal-product').hide()
		});
		$(document).on('click', '.quickview', function(){
			$('#productModal').modal({
				'show': true,
				'handleUpdate': true
			});
			self.loadProduct($(this).attr('id'));
		});
	},
	loadProduct: function(id){
		$.ajax({
			method: 'post',
			url: '/ajax/product/',
			dataType: 'json',
			data: {id: id}
			// data: { name: "John", location: "Boston" }
		}).done(function(data){
			if( data.status == 'success' ){
				var mp = $('#productModal .modal-product');
					mp.attr('data-product-id', data.product.id);
					mp.find('.see-all').attr('href', data.product.detailPageUrl);
					mp.find('.product-images img').attr('src', data.product.images.detail);
					mp.find('h1').text(data.product.name);
					mp.find('.brand-name-2 .brand-name').text(data.product.brand);
					mp.find('.brand-name-2 .article').text(data.product.article);
					mp.find('.new-price').text(data.product.price + ' бел. руб.');
					mp.find('.quick-desc').html(data.product.detailText);
					mp.fadeIn();
					$('#productModal .loader-product').hide();
			}
		});
	}
});

Studio8.Widget('addToCart', {
	documentReady: true,
	init: function(){
		this.helpers.event.initEvent('afterUpdateSmallBasket', true, true);
		this.events.addGoodToCart();
		this.events.removeGoodOfCart();
		this.events.quickBuy();
		this.helpers.sendAjaxBasket("", {id: 0});
	},
	helpers: {
		isSend: false,
		btnElement: null,
		event: document.createEvent('Event'),
		updateSmallBasket: function(data){
			if( data.status == 'success' ){
				var mp = $('#small-basket');
					mp.find('.cart-quantity').text(data.basket.quantity);
					mp.find('.subtotal span').text(data.basket.totalPrice + ' бел. руб.');
				
				mp.find('.total-cart-pro').html("");
				for( product in data.basket.items ){
					$item = Studio8.Widget('addToCart').helpers.templateProductForCart(data.basket.items[product]);
					mp.find('.total-cart-pro').append($item);
				}

				Studio8.Widget('addToCart').helpers.event.data = data;
				document.dispatchEvent(Studio8.Widget('addToCart').helpers.event);
			}
		},
		sendAjaxBasket: function(type, data, $elem, callback){
			var self = this;
			if( self.isSend === true )
				return;

			self.beforeSend($elem);
			$.ajax({
				method: 'post',
				url: '/ajax/basket/' + type,
				dataType: 'json',
				data: data
			}).done(self.updateSmallBasket).always(self.afterSend);
		},
		beforeSend: function($elem){
			Studio8.Widget('addToCart').helpers.isSend = true;
			$('#small-basket .loader-in-basket').show();

			if( !$elem )
				return;
			Studio8.Widget('addToCart').helpers.btnElement = $elem;
			$elem.closest('.product-card').find('.product-item').append('<div class="loader-basket-add"><img src="http://www.chapala.gob.mx/web/images/cargando.gif"></div>');
		},
		afterSend: function(){
			Studio8.Widget('addToCart').helpers.isSend = false;
			$('#small-basket .loader-in-basket').hide();

			var $elem = Studio8.Widget('addToCart').helpers.btnElement;
			if( !$elem )
				return;
			$elem.closest('.product-card').find('.loader-basket-add').remove();
		},
		templateProductForCart: function(product){
			var tpl = '<div class="single-cart product-card clearfix" data-product-id="#id#">' +
				'<div class="product-container">' +
                '<div class="cart-img f-left">' +
                    '<a href="#detailPageUrl#"><img src="#image#" alt="Cart Product"/></a>' +
                    '<div class="del-icon">' +
                        '<a href="javascript:void(0);" class="remove-of-cart"><i class="zmdi zmdi-close"></i></a>'+
                    '</div>' +
                '</div>' +
                '<div class="cart-info f-left">' +
                    '<h6 class="text-capitalize"><a href="#detailPageUrl#">#name#</a></h6>' +
                    '<p><span>Артикул <strong>:</strong></span>#article#</p>' +
                    '<p><span>Цена <strong>:</strong></span>#price#</p>' +
                    '<p><span>Количество <strong>:</strong></span>#quantity#</p>' +
                '</div>'+
                '<div>' +
            '</div>';

            for(var prop in product) {
            	tpl = tpl.replace(new RegExp("#"+prop+"#", 'g'), product[prop]);
        	}

        	return $(tpl);
		}
	},
	events: {
		addGoodToCart: function(){
			$(document).on('click', '.add-to-cart', function(){
				var $this = $(this);
				var productId = $this.closest('.product-card').attr("data-product-id");
				Studio8.Widget('addToCart').helpers.sendAjaxBasket('add', {id: productId}, $this);
			});
		},
		removeGoodOfCart: function(){
			$(document).on('click', '.remove-of-cart', function(){
				var $this = $(this);
				var productId = $this.closest('.product-card').attr("data-product-id");
				Studio8.Widget('addToCart').helpers.sendAjaxBasket('remove', {id: productId}, $this);
			});
		},
		quickBuy: function(){
			$(document).on('click', '.quick-buy', function(){
				var $this = $(this);
				var productId = $this.closest('.product-card').attr("data-product-id");
				Studio8.Widget('addToCart').helpers.sendAjaxBasket('quickbuy', {id: productId}, $this);
			});
			document.addEventListener('afterUpdateSmallBasket', function (e) {
				var ajaxAns = Studio8.Widget('addToCart').helpers.event.data;
				if( ajaxAns.data && ajaxAns.data.type == "quickBuy" ){
					window.location.href = "/basket/#checkout";
				}
			}, false);
		}
	}
});