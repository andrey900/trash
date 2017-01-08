$(document).ready( function() {
/*	
	$( ".day" ).focus(function() {
		 $(".cal").toggleClass("act");
	});

	$('.inf').hide();
	
	$('.form-control').focusin(function(){
		$(this).parent().parent().find('.inf').stop().fadeIn( 200 );
	}).focusout(function() {
   	$(this).parent().parent().find('.inf').stop().fadeOut( 1000 );
	});
/*
	$('#change-password input').on('click', function(){
		$('#change-password .no-empty').each(function(){
		inputval = $(this).val();
		if( inputval == '' || inputval == null )
			$('#change-password button[type="submit"]').removeAttr('disabled');
		});
	})
*/	/*
	$('#change-password button[type="submit"]').click( function(){
		$.post( $('#change-password').attr('action'), $( '#change-password' ).serialize() + "&ajax=" + "true", function( data ) {
			alert( "Data Loaded: " + data );
		});
		return false;
	});

	$('#change-user-info button[type="submit"]').click( function(){
		$.post( $('#change-user-info').attr('action'), $( '#change-user-info' ).serialize() + "&ajax=" + "true", function( data ) {
			alert( "Data Loaded: " + data );
		});
		return false;
	});

	$('#change-user-delivery button[type="submit"]').click( function(){
		$.post( $('#change-user-delivery').attr('action'), $( '#change-user-delivery' ).serialize() + "&ajax=" + "true", function( data ) {
			alert( "Data Loaded: " + data );
		});
		return false;
	});
*//*
	function buttonActivate(id){
		$( "#"+id ).change(function() {
		 	$('#'+id+' button[type="submit"]').removeAttr('disabled');
		});
	}
	
	function changeData(id, ajax){
		ajax = 'true';
		
		$('#'+id+' button[type="submit"]').click( function(){
			$.post( $('#'+id).attr('action'), $( '#'+id ).serialize() + "&ajax=" + "true", function( data ) {
				alert( "Data Loaded: " + data );
			});
			return false;
		});
	}
	
	changeData('change-user-delivery');
	changeData('change-user-info');
	changeData('change-user-password');
	
	buttonActivate('change-user-delivery');
	buttonActivate('change-user-info');
	buttonActivate('change-user-password');
	*/
});