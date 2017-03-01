$(document).ready(function(){
	try{
		var ElectrodomObj = new Electrodom();
		ElectrodomObj.Init();
	} catch(err) {
		console.log('Init error: ');
		console.log(err);
	}
});

Electrodom = function(){
	this.xhr = null;
	this.timerForAjaxQuery = null;
};

Electrodom.prototype.Init = function(){
	this.panelIntegration();
	this.stikyBasket(true);
	this.eventInit();
	// new animatedNews('#news-line');

/*	for (var i = 0; i < 10; i++) {
		$('.main-content').append('<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam ab ea cumque sequi sapiente. Veritatis sed velit earum, error, ipsa exercitationem, debitis veniam ex itaque cumque suscipit? Hic laborum ipsum quos dolorem consectetur ut nobis asperiores, tenetur corrupti optio dolor, officia modi ad harum sit aliquid maiores minima nesciunt nihil.</p>');
	}*/

	String.prototype.replaceArray = function(find, replace) {
	  var replaceString = this;
	  for (var i = 0; i < find.length; i++) {
	    replaceString = replaceString.replace(find[i], replace[i]);
	  }
	  return replaceString;
	};
};

Electrodom.prototype.panelIntegration = function(){
	$('.navbar-fixed-top').css('top', $('.panel').height());
}

Electrodom.prototype.makeRow = function(item, type){
	var tr = document.createElement('tr');	
		tr = this.makeCol(tr, item.article);
		tr = this.makeCol(tr, item.name);
		tr = this.makeCol(tr, item.brand);
		tr = this.makeCol(tr, item.stockQuantity);
		tr = this.makeCol(tr, item.byCurrency.realPrice);
		tr = this.makeCol(tr, item.byCurrency.userPrice);
		if( type == 'edit_basket' )
			tr = this.makeBasketColEdit(tr, item);
		else
			tr = this.makeBasketCol(tr, item.id);

	return tr;
}

Electrodom.prototype.makeCol = function(row, text){
	var td = document.createElement('td');
		td.innerHTML = text;

	row.appendChild(td);

	return row;
}

Electrodom.prototype.makeBasketColEdit = function(row, item){
	var td = document.createElement('td');
		td.classList.add("add-item-to-basket");
	var qnt = document.createElement('input');
		qnt.classList.add('form-control');
		qnt.classList.add('quantity-item');
		qnt.classList.add('ajax');
		qnt.setAttribute('item-id', item.id);
		qnt.value = item.quantity;
		qnt.setAttribute("type", "text");
	var btn = document.createElement('button');
		btn.classList.add("btn");
		btn.classList.add("btn-danger");
		btn.classList.add("btn-xs");
		btn.classList.add("remove-of-cart");
		btn.setAttribute("type", "button");
		btn.innerHTML = '<i class="glyphicon glyphicon-remove"></i>';

	td.appendChild(qnt);
	td.appendChild(btn);
	row.appendChild(td);

	return row;
}

Electrodom.prototype.makeBasketCol = function(row, id){
	var td = document.createElement('td');
		td.classList.add("add-item-to-basket");
	var qnt = document.createElement('input');
		qnt.classList.add('form-control');
		qnt.classList.add('quantity-item');
		qnt.setAttribute('item-id', id);
		qnt.value = 1;
		qnt.setAttribute("type", "text");
	var btn = document.createElement('button');
		btn.classList.add("btn");
		btn.classList.add("btn-success");
		btn.classList.add("add-to-cart");
		btn.setAttribute("type", "button");
		btn.innerHTML = '<i class="glyphicon glyphicon-shopping-cart"></i>';

	td.appendChild(qnt);
	td.appendChild(btn);
	row.appendChild(td);

	return row;
}

Electrodom.prototype.stikyFooter = function(){
	if( $('body').height()+30 < $(window).height() ){
		$('.footer').css('bottom', 0);
	} else {
		$('.footer').css('bottom', 'auto');
	}
}

Electrodom.prototype.stikyBasket = function(init){
	if( typeof init != 'undefined' && init )
		this.offsetBasketTop = $('.swim-basket').css('top');

	var ww = $(window).width();
	var scT = $(window).scrollTop();

	if( ww >= 768 ){
		var t = parseInt(this.offsetBasketTop)+scT-50;
		$('.swim-basket').css({'padding': "10px", 'top': t+"px", 'right':"0px"});
	} else if( ww < 768 ){
		var tt = parseInt($('.navbar-fixed-top').css('top'));
		$('.swim-basket').css({'padding': "4px 10px", 'top': scT+tt, 'right': "70px"});
	}
}

Electrodom.prototype.eventInit = function(){
	var self = this;

	$('#bx-panel-expander, #bx-panel-hider').on('click', function(){
		self.panelIntegration();
	});

	$(window).scroll(function(){
		var scT = $(window).scrollTop();
		var navBar = $('.navbar-fixed-top');
		if( scT > $('.panel').height()){
			if( parseInt(navBar.css('top')) > 0 )
				navBar.css('top', 0);
		} else {
			navBar.css('top', $('.panel').height() - scT);
		}
		self.stikyBasket();
	});
	$(window).resize(function(){
		self.stikyFooter();
		self.stikyBasket();
	})

	$('.ajax-search').on('click', function(e){
		e.preventDefault();
		var phrase = $('#search-field').val();
			
		if( phrase.length <= 2 ){
			return;
		}
		
		$('.query-search').text(phrase);

		self.ajaxSearch("/wholesalers/search.php?q="+phrase);
	});

	$(document).on('click', '.pagination a', function(e){
		e.preventDefault();
		self.ajaxSearch($(this).attr('href'));
	});

	$(document).keydown(function (event) {
		// start timer for search if input new value
		if( $("#search-field").is(":focus") && $("#search-field").val().length > 2 && event.keyCode != 13 ){
	    	self.timerForAjaxQuery = setTimeout(function(){$('.ajax-search').click();}, 600);
	    }

	    // enter click in search
	    if ($("#search-field").is(":focus") && (event.keyCode == 13)) {
	    	clearTimeout(self.timerForAjaxQuery);
	        $('.ajax-search').click();
	        event.preventDefault();
      		return false;
	    }

	    // change quantity in basket
	    if( $('.quantity-item').is(":focus") && (event.keyCode == 13) ){
	    	var $this = $('.quantity-item:focus');
			var data = Object.create(null);
				data.quantity = $this.val();
				data.product_id = $this.attr('item-id');
			if($this.hasClass('ajax'))
				data.method = 'editQuantity';
			self.ajaxAddToBasket(data);
			self.ajaxGetBasket();
	    	event.preventDefault();
	    }
	});

	// click download prices
	$('a[href="/wholesalers/"]').on('click', function(e){
		e.preventDefault();
		
		var pdiv = $('.search-result');
	        pdiv.insertBefore(pdiv.prev());

		$('.price-download').slideDown(function(){self.stikyFooter()});
		$('.search-result').slideUp(function(){self.stikyFooter()});
		self.hideFullBasket();
	})

	// click add item in cart
	$(document).on('click', '.add-to-cart', function(e){
		e.preventDefault();
		var $this = $(this).prev();
		var data = Object.create(null);
			data.quantity = $this.val();
			data.product_id = $this.attr('item-id');
		self.ajaxAddToBasket(data);
	});

	// swim basket click - show basket page
	$('.swim-basket').click(function(e){
		if($(e.target).hasClass('currency')){
			self.currencyChanger('show', $(e.target).text());
		} else {
			self.showFullBasket();
		}
	});

	$('.hide-block-currency-change a').click(function(){
		self.currencyChanger('hide');
		return false;
	});

	$('.currency-changer-block input').change(function(){
		$(this).closest('form').submit();
	})

	// click button remove item of cart
	$(document).on('click', '.remove-of-cart', function(e){
		var $this = $(this).prev();
		var data = Object.create(null);
			data.quantity = 0;
			data.product_id = $this.attr('item-id');
			data.method = 'editQuantity';
		self.ajaxAddToBasket(data);
		self.ajaxGetBasket();
    	e.preventDefault();
	});

	$(document).on('focusout', '.quantity-item.ajax', function(e){
		var $this = $(this);
		var data = Object.create(null);
			data.quantity = $this.val();
			data.product_id = $this.attr('item-id');
			data.method = 'editQuantity';
		self.ajaxAddToBasket(data);
		self.ajaxGetBasket();
    	e.preventDefault();
	});

	$(document).on('click', '.new-order.ajax', function(){
		self.ajaxOrderAdd();
	});
}

Electrodom.prototype.ajaxOrderAdd = function(){
	var self = this;
	$.ajax({
		url: '/wholesalers/order.php',
		data: {method : "add"},
		dataType: "json"
	}).done(function(data){
		alert(data.msg);
		self.ajaxGetBasket();
		$('a[href="/wholesalers/"]').click();
		self.animatePrice($('.total-quantity'), 0, 0, 0);
		self.animatePrice($('.total-price'), 0, 0, 2);
	});
}

Electrodom.prototype.ajaxAddToBasket = function(data){
	var self = this;
	$.ajax({
		url: '/wholesalers/basket.php',
		data: data,
		dataType: "json"
	}).done(function(data){
		self.animatePrice($('.total-quantity'), parseInt($('.total-quantity').text()), data.quantity, 0);
		self.animatePrice($('.total-price'), parseInt($('.total-price').text()), data.total_price, 2);
	});
}

Electrodom.prototype.ajaxGetBasket = function(data){
	var self = this;
	$.ajax({
		url: '/wholesalers/order.php',
		data: data,
		dataType: "json"
	}).done(function(data){
		var str = $("<table class='table table-hover'></table>");
		str.append($('.table-result thead').clone());
		str.append($('<tbody></tbody>'));
		$('.fullbasket').html("");
		$.each(data.itemsInfo, function(i, e){
			str.find('tbody').append(self.makeRow(e, 'edit_basket'));
		});
		$('.fullbasket').append($('<h4></h4>').text('Ваша корзина'));
		$('.fullbasket').append(str);
		if(data.items.length > 0){
			$('.fullbasket').append($('<div class="text-center"><button class="btn btn-success btn-lg new-order ajax">Оформить</button></div>'));
		}
	});
}

Electrodom.prototype.ajaxSearch = function(url){
	var self = this;

	if (this.xhr != null){ 
	    this.xhr.abort();
	    this.xhr = null;
	}

	clearTimeout(self.timerForAjaxQuery);

	$('.search-form .search-loading').removeClass('hidden').prev().addClass('hidden');
	this.xhr = $.ajax({
		url: url,
		dataType: "json"
	}).done(function(data){
		$('.search-form .search-loading').addClass('hidden').prev().removeClass('hidden');

		var priceDownload = $('.price-download');
		if( priceDownload.is(':visible') || $('.fullbasket').is(':visible') ){
			if( priceDownload.next().length == 0 ){
	        	priceDownload.insertBefore(priceDownload.prev());
			}
			
			$('.price-download').slideUp(function(){self.stikyFooter()});
			$('.search-result').slideDown(function(){self.stikyFooter()});
			self.hideFullBasket();
		}

		$('.table-result tbody > tr').remove();
		if(data.items.length == 0){
			$('.table-result').hide(function(){self.stikyFooter()});
			$('.empty-result').show(function(){self.stikyFooter()});
		} else {
			$.each(data.items, function(i, e){
				$('.table-result tbody').append(self.makeRow(e));
			});
			$('.table-result tbody').append($('<tr></tr>').append($('<td colspan="7"></td>').html(data.pagen)));
			$('.table-result').show(function(){self.stikyFooter()});
			$('.empty-result').hide(function(){self.stikyFooter()});
		}

		$("html, body").stop().animate({scrollTop:0}, '700', 'swing');
	});
}

Electrodom.prototype.animatePrice = function($el, oldvalue, value, decimal){
	if( typeof decimal == 'undefined' )
		decimal = 0;

	$({percentage: oldvalue}).stop(true).animate({percentage: value}, {
        duration : 1000,
        // easing: "easeOutExpo",
        step: function () {
            // percentage with 1 decimal;
            var percentageVal = Math.round(this.percentage * 10) / 10;
            $el.text(percentageVal.toFixed(decimal));
        }
    }).promise().done(function () {
        // hard set the value after animation is done to be
        // sure the value is correct
        $el.text(value.toFixed(decimal));
    });
}

Electrodom.prototype.showFullBasket = function(){
	var self = this;

	self.ajaxGetBasket()

	if( $('.fullbasket').length == 0 )
		$('.swim-basket').after($('<div class="fullbasket"></div>'));
	
	$('.price-download').slideUp(function(){self.stikyFooter()});
	$('.search-result').slideUp(function(){self.stikyFooter()});
	$('.fullbasket').slideDown();
}

Electrodom.prototype.hideFullBasket = function(){
	$('.fullbasket').slideUp();
}

Electrodom.prototype.currencyChanger = function($type, curr){
	if( $type == 'show' ){
		var r = 0;
		var t = parseInt($('.swim-basket').css('top')) + 5 + parseInt($('.swim-basket').css('height'));
		$('.currency-changer-block').css('top', t);
		$('.currency-changer-block input[value='+curr+']').prop('checked', true);
		$('.currency-changer-block').removeClass('hide');
	} else {
		var r = -185;
	}
	$('.currency-changer-block').animate({
		right: r
	}, 500, function(){
		if( parseInt($('.currency-changer-block').css('right')) )
			$('.currency-changer-block').addClass('hide');
	});
}


function animatedNews(selector){
	this.newsLine = $(selector+' > .animated');
	
	this.curr = null;

	if( this.newsLine.length < 1 )
		return;

	this.hideShowNews = function(){
		$('.hide-news-line').on('click', function(){
			$('#news-line-block').slideUp();
			$("#news-line-block-show").fadeIn();
		});
		$("#news-line-block-show").on('click', function(){
			$("#news-line-block-show").fadeOut();
			$('#news-line-block').slideDown();
		})
	}

	this.showNext = function(){
		if( this.curr.index()+1 >= this.newsLine.length ){
			this.animateItem(0);
		} else {
			this.animateItem(this.curr.index()+1);
		}
	}

	this.animateItem = function(idx){
		if( this.curr )
			this.curr.removeClass('active fadeInDown').addClass('fadeOutDown');
		
		this.curr = this.newsLine.eq(idx);
		this.curr.removeClass('fadeOutDown').addClass('active fadeInDown');
	}

	if( !this.newsLine.find('.active').length )
		this.animateItem(0);

	setInterval(this.showNext.bind(this), 3000);

	return this;
}


var newsBlock = new animatedNews('#news-line');
newsBlock.hideShowNews();
