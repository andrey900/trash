$(document).ready(function(){
	$('span.live-input').each(function(idx, el){
		_jqEl = $(el);
		if( _jqEl.text().length === 0 ){
			AniartLiveInput.showInput(_jqEl);
		}
	});
	AniartLiveInput.config.focus = true;
	AniartLiveInput.init();
	$(AniartLiveInput.config.textInfo).hide();
});

AniartLiveInput = {
	config: {
		maxLength: 33,
		textInfo: '#textareaFeedback',
		focus: false,
		ajaxURL: '/_ajax/managers_edit.php',
		ajaxMethod: 'POST',
	},

	timeoutId: 0,

	liveInput: function(){

	},
	createInput: function(value, cl){
		$_input = $('<input type="text" size="'+AniartLiveInput.config.maxLength+'">');
		$_input.val(value).addClass(cl).addClass('live-input');
		return $_input;
	},
	init: function(){
		$(document).on('click', 'span.live-input', function(){
			AniartLiveInput.showInput( $(this) );
			//$('#textareaFeedback').stop().fadeIn();
		});
		$(document).on('blur', 'input.live-input', function () {
	        if( $(this).val() != '' ){
	        	AniartLiveInput.showSpan( $(this) );
	        } else{
	        	var data = {
					ajax: true,
					value: '',
					name: $(this).next().attr('elem-id'),
					prop: $(this).next().attr('name'),
					method: 'editProp',
				};
	        	AniartLiveInput.ajaxSave(data);
	        }
	    });
	    $(document).on('keyup', 'input.live-input', function () {
			//var $this = $(this);
		    AniartLiveInput.showInputInfo( $(this) );
		});
		$(document).on('focus', 'input.live-input', function () {
			//var $this = $(this);
		    AniartLiveInput.showInputInfo( $(this) );
		});
		$(document).on('change', 'select.select-section', function(){
			var url = window.location.href.slice(0,window.location.href.indexOf('\?'));
			document.location = url+'?'+$(this).attr('name')+'='+$(this).val();
		});
	    /*$(document).on('focus', 'input', function () {
	    	$(this).keyup(function () {
	        	var $this = $(this);
				$this.val($this.val().substr(0, AniartLiveInput.config.maxLength));

				    var curLength = $this.val().length;         //(2)
			        $this.val($this.val().substr(0, AniartLiveInput.config.maxLength));     //(3)
			        var remaning = AniartLiveInput.config.maxLength - curLength;
			        if (remaning < 0) remaning = 0;
			        $('#textareaFeedback').html(remaning + ' осталось символов');//(4)
			        if (remaning < 10)          //(5)
			        {
			            $('#textareaFeedback').addClass('warning')
			        }
			        else
			        {
			            $('#textareaFeedback').removeClass('warning')
			        }

	    	});
		});*/
	},
	showInput: function(_jqEl){

		var shortVal = '';
		var cl = '';
		_jqEl.hide();
		_jqFn = _jqEl.parents('tr').find('.full-name').text();
		if( _jqEl.text().length != 0 ){
			shortVal = _jqEl.text();
		} else if( _jqFn.length <= this.config.maxLength ){
			shortVal = _jqFn;
			cl = 'green';
		}
		$_ni = AniartLiveInput.createInput(shortVal, cl);
		_jqEl.before( $_ni );
		if( this.config.focus )
			_jqEl.prev().focus();
		//console.log($(el).text().length);
		_jqEl.parents('tr').find('.full-name').addClass('block-val');

		this.showInputInfo( _jqEl.prev() );

	},

	showSpan: function(_jqEl){
		var data = {
			ajax: true,
			value: _jqEl.val(),
			name: _jqEl.next().attr('elem-id'),
			prop: _jqEl.next().attr('name'),
			method: 'editProp',
		};
		this.ajaxSave(data);
		_jqEl.next().text(_jqEl.val()).show();
	    _jqEl.remove();
	    //$(this.config.textInfo).hide();
	},

	showInputInfo: function($this){
		clearTimeout(this.timeoutId);
		var curLength = $this.val().length;         //(2)
        var $_jqInfblock = $(this.config.textInfo);
        $this.val($this.val().substr(0, AniartLiveInput.config.maxLength));     //(3)
        var remaning = AniartLiveInput.config.maxLength - curLength;
        if (remaning < 0) remaning = 0;
        $_jqInfblock.html(remaning + ' осталось символов');//(4)
        if (remaning < 10)          //(5)
        {
            $_jqInfblock.addClass('warning')
        }
        else
        {
            $_jqInfblock.removeClass('warning')
        }
        $_jqInfblock.show();
	},

	ajaxSave:function(data){
		$(AniartLiveInput.config.textInfo).removeClass('warning').html('<img src="/_tpln/img/loading.gif" width="20">').show();
		$.ajax({
			type: AniartLiveInput.config.ajaxMethod,
			url:  AniartLiveInput.config.ajaxURL,
			data: data,
			dataType: "json",
		}).done(function( msg ) {
			AniartLiveInput._dataHandling( msg );
		}).fail(function(){
			$(AniartLiveInput.config.textInfo).addClass('warning').text('Error!').show();
			AniartLiveInput.timeoutId = setTimeout(function(){$(AniartLiveInput.config.textInfo).stop().fadeOut();}, 3000);
		});
	},

	_dataHandling: function(json){
		data = json;

		if( data.error.length > 0 )
			msg = data.error;
		else
			msg = data.success;

		$(AniartLiveInput.config.textInfo).removeClass('warning').text(msg).show();
		AniartLiveInput.timeoutId = setTimeout(function(){$(AniartLiveInput.config.textInfo).fadeOut();}, 3000);
	}
}