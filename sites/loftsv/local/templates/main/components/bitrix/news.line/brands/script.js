Studio8.Widget('homeBrandSlider', {
	documentReady: true,
	init:function(){
		$('.active-by-brand').slick({
	        speed: 700,
	        arrows: true,
	        dots: false,
	        slidesToShow: 4,
	        slidesToScroll: 1,
	        prevArrow: '<button type="button" class="arrow-prev"><i class="zmdi zmdi-long-arrow-left"></i></button>',
	        nextArrow: '<button type="button" class="arrow-next"><i class="zmdi zmdi-long-arrow-right"></i></button>',
	        responsive: [
	            { breakpoint: 991, settings: { slidesToShow: 3 }  },
	            { breakpoint: 767, settings: { slidesToShow: 1 }  },
	            { breakpoint: 479, settings: { slidesToShow: 1 }  }
	        ]
	    });
	}
});