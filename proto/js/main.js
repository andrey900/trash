$(document).ready(function() {
	/*$('.minus').click(function() {
		var $input = $(this).parent().find('input');
		var count = parseInt($input.val()) - 1;
		count = count < 1 ? 1 : count;
		$input.val(count);
		$input.change();
		return false;
	});
	
	$('.plu').click(function() {
		var $input = $(this).parent().find('input');
		$input.val(parseInt($input.val()) + 1);
		$input.change();
		return false;
	});
	*/
	if ('devicePixelRatio' in window && window.devicePixelRatio == 2) {
		var img_to_replace = jQuery('img.replace-2x').get();
		for (var i = 0, l = img_to_replace.length; i < l; i++) {
			var src = img_to_replace[i].src;
			src = src.replace(/\.(png|jpg|gif)+$/i, '-retina.$1');
			img_to_replace[i].src = src;
		}
	}
	
	/*
	 * Добавил not() так как ZONAKOMFORTA-7
	 * **/
	$('li.dropdown').not('.my-photo').hover(function() {
		$(this).addClass('open');
	}, function() {
		if( $(this).hasClass('basc')){
			$('li.dropdown.basc .dropdown-menu').mouseleave(function(){
				$('li.dropdown.basc').removeClass('open');
			});
		}
	});
	
	$('li .btn-group').hover(function() {
		$(this).addClass('open');
	}, function() {
		$(this).removeClass('open');
	});
		
	$('.basc .dropdown-menu input, .basc .dropdown-menu label, .basc .dropdown-menu .tov').on('click', function(e){
		e.stopPropagation();
	});

	$('.navbar').affix({
		offset: {
		  top: $('.top-cont').height()
		}
	});
	
	var _bxSlider = $('.bxslider');
	if(_bxSlider.length == 1){
		_bxSlider.bxSlider({
			nextText: " ",
			prevText: " "
		});
	}
		
	var bxSlider1 = $('.bxslider-1');
	if(bxSlider1.length == 1){
		bxSlider1.bxSlider({
			nextText: " ",
			prevText: " "
		});
	}
		  
	var bxSliderCarus = $('.bxslider-carus');
	if(bxSliderCarus.length == 1){
		bxSliderCarus.bxSlider({
			nextText: " ",
			prevText: " ",
			slideWidth: 245,
			minSlides: 1,
			maxSlides: 4,
			slideMargin: 0
		});
	}
});


(function() {
	// require menu height + margin, otherwise convert to drop-up
	var dropUpMarginBottom = 100;
	function dropUp(){
		var windowHeight = $(window).height();
		$(".btn-group-dropup").each(function() {
			var dropDownMenuHeight, rect = this.getBoundingClientRect();
			// only toggle menu's that are visible on the
			// current page
			if (rect.top > windowHeight) {
				return;
			}
			// if you know height of menu - set on parent, eg.
			// `data-menu="100"`
			dropDownMenuHeight = $(this).data('menu');
			if (dropDownMenuHeight == null) {
				dropDownMenuHeight = $(this).children('.dropdown-menu').height();
			}
			$(this).toggleClass("dropup", ((windowHeight - rect.bottom) < (dropDownMenuHeight + dropUpMarginBottom)) && (rect.top > dropDownMenuHeight));
		});
	};
	// bind to load & scroll - but debounce scroll with `underscorejs`
	$(window).bind({
		"resize scroll touchstart touchmove mousewheel" : _.debounce(dropUp, 100),
		"load" : dropUp
	});
}).call(this);
