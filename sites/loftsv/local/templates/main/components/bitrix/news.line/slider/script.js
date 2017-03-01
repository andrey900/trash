Studio8.Widget('homeSlider', {
	documentReady: true,
	init: function(){
		$('.active-slider-1').slick({
	        autoplay: true,
	        autoplaySpeed: 8000,
	        speed: 1000,
	        dots: true,
	        slidesToShow: 1,
	        slidesToScroll: 1,
	        prevArrow: '<button type="button" class="arrow-prev"><i class="zmdi zmdi-long-arrow-left"></i></button>',
	        nextArrow: '<button type="button" class="arrow-next"><i class="zmdi zmdi-long-arrow-right"></i></button>',
	    });
	}
});