<div class="iform">
<form action="/ajax/profile.php" method="POST">
	<input type="hidden" name="ajax" value="Y">
	<input class="sendForm" type="submit" name="save" value="save">
</form>
</div>

<script>
AniartUserPersonal = {
	save: function(){},				// Отправка данных на изменение добавление
	_dataHandling: function(){},	// внутр метод - обрботка после AJAX query
};

AniartUserPersonal._dataHandling = function(json){
	data = jQuery.parseJSON( json );
	$('.iform').html( $(data.html).html() );
	if( data.error.length > 0 )
		alert(data.error);
	else
		alert(data.success);
}

AniartUserPersonal.save = function(){
	$(document).on('click', '.sendForm', function(event){
		event.preventDefault();

		_jqForm = $(this).parents('form');

		var data = _jqForm.serialize()+'&method='+$(this).attr('name');
		$.ajax({
			url: _jqForm.attr('action'),
			type: _jqForm.attr('method'),
			data: data,
		}).done(function(jsondata) {
			AniartUserPersonal._dataHandling(jsondata);
		});
	});
}

$(document).ready(function(){
	AniartUserPersonal.save();
});

/*
AniartUserPersonal = {
    initialization: false,
    save: function(){},             // Отправка данных на изменение добавление
    subscribe: function(){},		// подписки
    _dataHandling: function(){},    // внутр метод - обрботка после AJAX query
};

AniartUserPersonal._dataHandling = function(json){
    data = jQuery.parseJSON( json );
    //$('.iform').html( $(data.html).html() );
    if( data.error.length > 0 )
        alert(data.error);
    else{
    	if(data.success.length > 0)
        	alert(data.success);
    }
}

AniartUserPersonal.save = function(){
    $(document).on('click', '.sendForm', function(event){
        event.preventDefault();

        _jqForm = $(this).closest('form');

        var data = _jqForm.serialize()+'&method='+$(this).attr('name');
        var ajaxSend = false;
        if( ajaxSend===false ){
            ajaxSend = true;
            $.ajax({
                url: _jqForm.attr('action'),
                type: _jqForm.attr('method'),
                data: data,
            }).done(function(jsondata) {
                ajaxSend = false;
                AniartUserPersonal._dataHandling(jsondata);
            });
        }
    });
    return this;
}

AniartUserPersonal.subscribe = function(){
    $(document).on('click', '.subscribeSave', function(event){

        _jqForm = $(this).closest('.form');

        var data = _jqForm.serialize()+'&ajax=y'+'&method='+$(this).attr('name');
        var ajaxSend = false;
        if( ajaxSend===false ){
            ajaxSend = true;
            $.ajax({
                url: _jqForm.attr('action'),
                type: _jqForm.attr('method'),
                data: data,
            }).done(function(jsondata) {
                ajaxSend = false;
                AniartUserPersonal._dataHandling(jsondata);
            });
        }
    });
    return this;
}

AniartUserPersonal.changePersonType = function(){
    $(document).on('click', '.status-select .radio', function(event){

        _jqForm = $(this).closest('.status-select');

        var data = 'ajax=y'+'&method=changePersonType&'+_jqForm.attr('name')+'='+_jqForm.find('.checked .radio').attr('value');
        var ajaxSend = false;
        if( ajaxSend===false ){
            ajaxSend = true;
            $.ajax({
                url: _jqForm.attr('action'),
                type: _jqForm.attr('method'),
                data: data,
            }).done(function(jsondata) {
                ajaxSend = false;
                AniartUserPersonal._dataHandling(jsondata);
            });
        }

        if( _jqForm.find('.checked .radio').attr('value')==2 )
        	$('.special-user-fields').slideDown();
        else
        	$('.special-user-fields').slideUp();
    });
    return this;
}
*/
</script> 
