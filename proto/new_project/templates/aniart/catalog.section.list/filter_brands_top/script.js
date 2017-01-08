$(document).ready(function(){

	function clearAllCheck(){
		$('.f_brands input').prop('checked', false).parent().removeClass('chek');
	}
	function createUrlPath(code){
		_jqLoc = $(location);
		regEx  = /brand-([a-zA-Z0-9_-]+)/ig;

    	var ret = _jqLoc.attr('pathname').replace( 
            regEx, 
            'brand-'+code
            );
		if( ret.match(regEx) === null ){
			window.location.href = 'http://'+_jqLoc.attr('hostname')+_jqLoc.attr('pathname')+'brand-'+code+'/';
		} else {
			window.location.href = 'http://'+_jqLoc.attr('hostname')+ret;
		}
	}
	function clearUrlPath(){
		_jqLoc = $(location);

		var ret = _jqLoc.attr('pathname').replace( 
            /\/brand-([a-zA-Z0-9_-]+)/ig, 
            ''
            );
    	
		window.location.href = 'http://'+_jqLoc.attr('hostname')+ret;
	}

	$(document).on('click', '.show-all-brands', function(){
		jqThis = $(this);
		jqChTs = jqThis.children('a');
		jqThis.toggleClass('show-all');
		if( jqThis.hasClass('show-all') ){
			jqChTs.text( jqChTs.attr('data-active') );
		} else {
			jqChTs.text( jqChTs.attr('data-noactive') );
		}

		$('.filter-brands').slideToggle().css({'display':'inline-block'});

		return false;
	});

	$(document).on('click', '.clear-all-brands', function(){
		clearAllCheck();
		clearUrlPath();
		//$(this).parents('form').submit();
	});

	$('.f_brands').on('click', 'p', function(){
		clearAllCheck();
		_jqObj = $(this).parents('.f_brands').find('input');
		_jqObj.prop('checked', true).parent().addClass('chek');
		createUrlPath( _jqObj.attr('code-name') );
		//$(this).parents('form').submit();
		return false;
	});
});