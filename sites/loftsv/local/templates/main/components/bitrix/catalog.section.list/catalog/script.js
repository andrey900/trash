Studio8.Widget('homeSlider', {
	documentReady: true,
	init: function(){
		$('.blog-desc').on('click', function(){
			window.location.href = $(this).attr('href');
	    });
	}
});