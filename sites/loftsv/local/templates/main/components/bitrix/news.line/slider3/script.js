Studio8.Widget('homeSlider', {
	documentReady: true,
	init: function(){
	    var self = this;
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
	    this.inCenter('.overlay-slider-image');
	    $(window).resize(function(){
	    	self.inCenter('.overlay-slider-image');
	    });
	},

	inCenter: function(selector){
		var elem = $(selector);
		var wWidth = $(window).width();
		var eHeight = $(window).height();
		if(wWidth < 670){
			elem.css({"width": "250px", "height": "96px", "display": "none"});
		} else {
			elem.css({"width": "500px", "height": "192px", "display": "block"});
		}

		var eWidth = elem.width();
		var eHeight = elem.height();

		elem.css("left", parseInt((wWidth / 2) - (eWidth / 2)) + "px");
	}
});