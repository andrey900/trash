Studio8.Widget('blogSlider', {
	documentReady: true,
	init: function(){
		$('.active-blog-2').slick({
	        speed: 700,
	        arrows: false,
	        dots: false,
	        slidesToShow: 2,
	        slidesToScroll: 1,
	        responsive: [
	            {  breakpoint: 991,   settings: { slidesToShow: 2,  }  },
	            {  breakpoint: 767,   settings: { slidesToShow: 1, }   },
	            {  breakpoint: 479,   settings: { slidesToShow: 1, }   },
	        ]
	    });
	}
});