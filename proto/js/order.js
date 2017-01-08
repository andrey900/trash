var substringMatcher = function(strs) {
	  return function findMatches(q, cb) {
		var matches, substringRegex;
	 
		// an array that will be populated with substring matches
		matches = [];
	 
		// regex used to determine if a string contains the substring `q`
		substrRegex = new RegExp(q, 'i');
	 
		// iterate through the pool of strings and for any string that
		// contains the substring `q`, add it to the `matches` array
		$.each(strs, function(i, str) {
		  if (substrRegex.test(str)) {
			// the typeahead jQuery plugin expects suggestions to a
			// JavaScript object, refer to typeahead docs for more info
			matches.push({ value: str });
		  }
		});
	 
		cb(matches);
	  };
	};


var Order = {//global scope for our code
		//vars
		zidx: 	200,
		region: {},
		city: 	{},
		metroNew:{},
		constants:{'region'		:'#ORDER_PROP_7',
				   'city'		:'#ORDER_PROP_8',
				   'metro'		:'#ORDER_PROP_10',
				   'timecall'	:'#ORDER_PROP_4',
				   'pickpoint'	:'ID_DELIVERY_ID_2',
				   'hidecity'	:'select[name="ORDER_PROP_5"]',
				   'hideregion'	:'select[name="REGIONORDER_PROP_5"]',
				   'hidepickpoint':'#ORDER_PROP_13',
				   'telephonecall':'ORDER_PROP_1_telephone_cell',
					},
		
		//functions
		init:			function(){},
		ajaxinit:		function(){},
		
		//entities
		Methods: {
			_getDataOption:	function(){},			// return obj{id:option}
			_array_search:	function(name, obj){}, // find name in obj return id
			_isMoskow:		function(){},			// special method: show-hide input metro
			_getMetroData:	function(){},			// special method: get data metro
			_instZindex:	function(){},			// special method: inst z-index to DOM
			_isTelephoneCall:function(){},			// special method: show-hide input timecall
			ClearData:		function(){},			// Method clear data input delivery form 
			PickPpoint:		function(){},			// Method to init pickpoint service
			ChangePickPoint:function(){},			// Method callback pickpoint service
			DestroyTypeahead:function(idelem){},	// Destroy typeahead plugin
			InitTypeahead:	function(){},			// Init typeahead plugin
		}
	};

/*   Post ajax ReInit   */
Order.ajaxinit = function(){
	
	this._instZindex()._getDataOption();
	this._isMoskow()._isTelephoneCall();
	Order.Methods.ClearData();
	
	this.region = this._getDataOption('select[name="REGIONORDER_PROP_5"] option');
	this.city = this._getDataOption('select[name="ORDER_PROP_5"] option');
	/*this.metroNew = */ this._getMetroData();

	$('.'+this.constants['pickpoint']).attr('onclick', 'PickPoint.open(Order.Methods.ChangePickPoint, {fromcity:"Москва"});/*return false;*/');
	
	this.Methods.DestroyTypeahead(this.constants['city']).
		 DestroyTypeahead(this.constants['region']).
		 DestroyTypeahead(this.constants['metro']).
		 InitTypeahead(this.constants['city'], this.city, 'select[name="ORDER_PROP_5"]').
		 InitTypeahead(this.constants['region'],this.region, 'select[name="REGIONORDER_PROP_5"]').
		 InitTypeahead(this.constants['metro'],this.metroNew);
	
	//console.log(this.region);
	//console.log(this.city);
	
	
	var suggestionsArray = [" ", "9:00 – 11:00", "11:00 – 13:00", "13:00 – 16:00", "17:00 – 18:00", "18:00 – 19:00", "19:00 – 20:00"];
	var quicksearchInput = $(this.constants['timecall']);
	
	function obtainer(query, cb) {
		var filteredList = $.grep(suggestionsArray, function (item, index) {
			return item.match(query);
		});
	
		mapped = $.map(filteredList, function (item) { return { value: item } });
		cb(mapped);
	}
	
	quicksearchInput.typeahead({
		hint: true,
		highlight: true,
		minLength: 0
	}, {
		name: "states",
		displayKey: "value",
		source: obtainer
	});
	
	quicksearchInput.on("click", function () {
		ev = $.Event("keydown")
		ev.keyCode = ev.which = 40
		$(this).trigger(ev)
		return true
	});
	
}

/*   Init to open page   */
Order.init = function(){
	this.ajaxinit();
	
	/* Мне есть 18 лет */
	$('body').on('click', '.have-18', function() {
		$('.by').toggleDisabled();
	});

	/* Когда вам позвонить? */
	$('body').on('click', '.driv_ label.btn', function() {
		$( '.when' ).removeClass( "act" );
	});
	$('body').on('click', 'label[for="ORDER_PROP_1_telephone_cell"].btn', function() {
		$( '.when' ).addClass( "act" );
	});

	/*  checked form */
	$('body').on('click', 'a[data-parent="#accordion"]', function(){
		var statObj = $(this).find('.stat');
		var element = $(this).attr('href');
		var err = [];
		
		$('input[name="open_tab"]').val($(this).attr('href'));
		
		$(element+' input[type="text"]').each(function(){
			if( !$(this).val() && $(this).prop('required')) {
				err.push('err');;
			} 
		});

		if( err.length > 0 ){
			$(statObj).removeClass('act, error').addClass('error');
			$('input[name="'+element+'"]').val('error');
		} else {
			$(statObj).removeClass('act, error').addClass('act');
			$('input[name="'+element+'"]').val('act');
		}

	});

}

Order._getDataOption = function(select){
	var result = {};
	$(select).each(function(){
		result[$(this).val()] = $(this).text();
	});
	return result;
}

Order._instZindex = function(){
	$('.form-group').each( function(inx, elem) {
		$(elem).css('z-index', Order.zidx);
		Order.zidx--;
	});
	return this;
}

Order._getMetroData = function(){
	string = '{' + $('.metroNew').text() + '}'; 
	eval('this.metroNew='+string);
	//console.log(this.metroNew);
	//return string;
}

Order._isMoskow = function(idsrc, idhs){
	if($('#ORDER_PROP_8').val().toLowerCase() == 'москва'){
		$(this.constants['metro']).parents('.form-group').show();
	} else {
		$(this.constants['metro']).parents('.form-group').hide();
	}
	return this;
}

Order._isTelephoneCall = function(){
	if( $('#'+this.constants['telephonecall']).prop('checked') )
		$( '.when' ).addClass( "act" );
	else
		$( '.when' ).removeClass( "act" );
	return this;
}

Order._array_search = function(query, data, chek_type){
	var chek_type = !!chek_type;
    for(var key in data)
    {
        if( (chek_type && data[key] === query) || (!chek_type && data[key] == query) ) return key;
    }
    return false;
}

Order.Methods.PickPpoint = function(){
	PickPoint.open(Order.Methods.ChangePickPoint, {fromcity:'Москва'});
	return false;
}

Order.Methods.ChangePickPoint = function(res){	
	$(Order.constants['hidepickpoint']).val(res['id']);
	BX(Order.constants['pickpoint']).checked=true;submitForm();
}

Order.Methods.DestroyTypeahead = function(idelem){
	$(idelem).typeahead('destroy');
	return this;
}

Order.Methods.InitTypeahead = function(idelem, obj, idelemonchange){
	if(idelemonchange == 'undefined'){
		$(idelem).typeahead({
			  hint: true,
			  highlight: true,
			  minLength: 1
			},
			{
			  name: 'states',
			  displayKey: 'value',
			  source: substringMatcher(obj),
			}).on('typeahead:selected', function() {
				$(idelem).val(this.value);
			});
	} else {
		$(idelem).typeahead({
			  hint: true,
			  highlight: true,
			  minLength: 1
			},
			{
			  name: 'states',
			  displayKey: 'value',
			  source: substringMatcher(obj),
			}).on('typeahead:selected', function() {
				$(idelemonchange).val(Order._array_search(this.value, obj)).change();
			});
	}

	return this;
}

Order.Methods.ClearData = function(){
	/*  AJAX edit profile */
	$('body').on('click', '.personal_data', function(){
		
		var id = 'collapseOne_2'; 
		/*
		var data = 'PERSONAL_CITY='+$('#ORDER_PROP_8').val()+'&'+
				   'PERSONAL_NOTES='+$('#ORDER_PROP_10').val()+'&'+
				   'PERSONAL_STATE='+$('#ORDER_PROP_7').val()+'&'+
				   'PERSONAL_STREET='+$('#ORDER_PROP_9').val()+'&'+
				   'PERSONAL_PHONE='+$('#ORDER_PROP_12').val()+'&'+
				   'PERSONAL_ZIP='+$('#ORDER_PROP_6').val();

		$.post( $('#'+id).attr('action'), data + "&ajax=" + "true", function( data ) {
			$('#answerModal .answerModaldata').html(data);
			$('#answerModal').modal('show');
			//$('#'+id+' button[type="submit"]').attr('disabled', '');
		});*/
		$('#'+id+' .form-group input').each(function(){
			$(this).val('');
		});
		return false;
	});
}
