Studio8.Widget('smartFilter', {
	arPropCode: ['BRAND'],
	init: function(){
		var self = this;
		$(document).on('click', '.smartfilter [type="checkbox"]', function(){
			var $this = $(this);
			/*if(self.arPropCode.indexOf($this.closest('aside').attr('id')) >= 0){
				window.location.href = $this.attr('url');
				return;
			}*/

			if( !$(this).parent().hasClass('disabled') )
				$(this).closest('form').find('input[name="set_filter"]').click();
			else {
				$(this).prop('checked', false);
			}
		});
	}
});

Studio8.Widget('mobile-fileter', {
	init: function(){
		$(document).on('click', '.mobile-filter-toggle', function(){
			$(this).parent().next().toggleClass('hidden-xs');
		});
	}
})