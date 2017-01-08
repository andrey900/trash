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
