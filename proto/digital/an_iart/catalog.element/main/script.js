var detail_page = (function() {

	return {
		//инициализация событий
		init: function() {
			
			// Remove the # from the hash, as different browsers may or may not include it
		    var hash = location.hash.replace('#','');		    
		    if(hash != ''){
		    	 var target = '#'+hash;
				 var $target = $(target);

				 if($target.length)
					 $(target).click();
				 
				 $('html, body').stop().animate({
				     'scrollTop': $target.offset().top
				 }, 900, 'swing', function () {
				     window.location.hash = target;
				 });
		    }
			
			//вешаем событие прокрутки к нужному месту
			//на все ссылки якорь которых начинается на #
			
			$('a[href^="#"]').live('click',function (e) {
			    e.preventDefault();

			    var target = this.hash;
			    var $target = $(target);

			    if($target.length)
					 $(target).click();
			    
			    $('html, body').stop().animate({
			        'scrollTop': $target.offset().top
			    }, 900, 'swing', function () {
			        window.location.hash = target;
			    });
			});
			
		},
	};
})();

$(document).ready(function() {
	detail_page.init();
});