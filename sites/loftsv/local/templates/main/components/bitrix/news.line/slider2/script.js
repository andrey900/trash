Studio8.Widget('homeSlider', {
	documentReady: true,
	init: function(){
		$('#nivoslider-2').nivoSlider({
	        directionNav: true,
	        animSpeed: 1000,
	        effect: 'random',
	        slices: 18,
	        pauseTime: 6000,
	        pauseOnHover: true,
	        controlNav: true,
	        prevText: '<i class="zmdi zmdi-long-arrow-up"></i>',
	        nextText: '<i class="zmdi zmdi-long-arrow-down"></i>'
	    });
	}
});