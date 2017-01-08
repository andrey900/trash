$(function(){
	$('.price-select-all').click(function(){
		if($(this).data('selected'))
		{
			$(this).text('Выделить все').data('selected', 0);
			$('.price-sections [name="sections[]"]').attr('checked', false);		
		}
		else
		{
			$(this).text('Снять все').data('selected', 1);
			$('.price-sections [name="sections[]"]').attr('checked', true);
		}
	});
	$('.price-toggle-all').click(function(){
		if($(this).data('toggle'))
		{
			$(this).text('Развернуть все').data('toggle', 0);
			$('.child-level').hide();		
		}
		else
		{
			$(this).text('Свернуть все').data('toggle', 1);
			$('.child-level').show();
		}
	});
	
	$('.show-subsections').click(function(){
		var id = $(this).data('id');
		$('#section-'+id).toggle();
		
		if($(this).data('visible'))
		{
			$(this).data('visible', 0).html('<span class="price-plus-minus">+</span> <span class="price-toggle">(развернуть раздел)</span>');
		}
		else
		{
			$(this).data('visible', 1).html('<span class="price-plus-minus">-</span> <span class="price-toggle">(свернуть раздел)</span>');
		}
	});
	
	if($('.form-ownership .errortext').length)
	{
		$('.form-ownership .errortext').closest('.form-ownership').show();
	}

	$(document).on('click', '.get-price', function(){
		var _sid   = '';
		var _sname = '';
		$('.selected_sections input[type="checkbox"]:checked').each(function(){
			//ga('send', 'event', 'load_price', 'get_a_price_'+$(this).val(), $(this).parent().text() );
			ga('send', 'event', 'load_section', 'get_a_price', $(this).parent().text() );
			_sid   += "_"+$(this).val();
			_sname += '_'+$(this).parent().text();
		});
		ga('send', 'event', 'load_price', 'get_a_price'+_sid, _sname.substr(1) );
	});
});