$(document).ready(function(){

$('.char .hide').show();
/*
$('body').on('click', '.slide-block .label', function(){
	$('#'+$(this).parent().attr('data-block') ).slideToggle();
});
*/
$('body').on('click', '.slide-block .label1', function(){//.char-name
	$_elem = $('#'+$(this).parent().attr('data-block'));
	if( !$_elem.is(':visible') )
		$_elem.slideToggle();
	
	if($_elem.find('tr.difference').length == 0)
		return true;

	$(this).toggleClass('action');
	$_elem.find('tr').not(".difference, .top-tab, .choose-by").toggle();
	//$(this).parents('table').find('tr').not(".difference, .top-tab, .choose-by").toggle();
});

$('.sr-tit').tooltip();

$('body').on('click', '.delete-element', function(){
	
	/* Better index-calculation from @activa */
	var myIndex = $(this).closest("th").prevAll("th").length//index( this );//.prevAll("td").length;
	var countcol = $(this).parents('table').find('th').length;
	
	var link = $(this).children().attr('href');
	//alert(link);
	
	$.post( link, { ajax: "Y" });

	if( countcol-1 <=2  ){
		var block_id = $(this).parents(".hide").attr('id');
		$('.slide-block[data-block="'+block_id+'"]').remove();
		$('#'+block_id).remove();
	} else {
		
		var block_id = $(this).parents(".hide").attr('id');
		var _elem = $('.slide-block[data-block="'+block_id+'"]').find('.show-elem')
		_elem.text(parseInt(_elem.text())-1);

		$(this).parents("table").find("tr").each(function(){
			$(this).find("th:eq("+myIndex+")").remove();
			$(this).find("td:eq("+myIndex+")").remove();
		});
	}

	return false;
});
});