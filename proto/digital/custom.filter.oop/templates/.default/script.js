function CustomFilterSubmitForm(request_name, request_value, obj, multi){
	
	if(typeof(obj) === "string"){
		var form = $(obj);
	}
	else{
		var form = $($(obj).parents('form')[0]);
	}
	
	var filter_name = form.attr("id").replace("form_","");
	if(form.length>0)
	{
		if(request_name != false && request_value != false)
		{
			var input_name = filter_name+"["+request_name+"][]";
			var input = $("input[name='"+input_name+"']");
			
			if(input.length>0 && !multi)
			{
				input.val(request_value);
			}
			else
			{
				form.append("<input type='hidden' name='"+input_name+"' value='"+request_value+"' />");
			}
		}
		form.submit();
	}
}

function ClearFilter(request_name, request_value, obj)
{
	var form = $(obj).parents('form')[0];
	$(form).find("[name^='"+request_name+"']").each(function(){
		if(request_value == $(this).val())
		{
			$(this).attr("disabled", "disabled");
			CustomFilterSubmitForm(false,false,obj);
		}
	});
}

function ClearAllFilters(obj)
{
	var form = $(obj).parents('form')[0];
	var filter_name = $(form).attr("id").replace("form_","");
	$(form).find("[name^='"+filter_name+"']").each(function(){
		$(this).attr("disabled", "disabled");
	});
	CustomFilterSubmitForm(false,false,obj);
}

function CheckFields(form)
{
	$(form).find("input, select, textarea").each(function(){
		var val = $(this).val();
		if(val===0 || val==="0" || val===""){
			$(this).attr("disabled", "disabled");
		}
		//для слайдеров - дополнительная обработка, не отсылаем параметры, если установленные пераметры = параметрам по умолчанию
		if($(this).attr('id') !== undefined && $(this).attr('id').indexOf('slider_property_') !== -1){
			if($(this).attr('value') == $(this).attr('default')){
				$(this).attr('disabled', 'disabled');
			}
		}
	});
}

function SwitchValues(from, to){
	var from_val	= parseInt(from.val());
	var to_val		= parseInt(to.val());
	
	if(from_val && to_val && from_val > to_val){
		var tmp = from_val;
		from.val(to_val).change();
		to.val(tmp).change();
	}
}

function InitSlider(SliderID, SliderParams)
{
	if(typeof SliderParams != 'object'){
		SliderParams = {};
	}
	
	//создаем слайдер
	if(window.jQuery && jQuery.ui.version)
	{
		var SliderID	= "#"+SliderID;
		var SliderMinID	= SliderID+"_min";
		var SliderMaxID	= SliderID+"_max";

		var DefaultMinValue = $(SliderMinID).attr('value') - 0;
		var DefaultMaxValue = $(SliderMaxID).attr('value') - 0;

		var SliderDefaultParams = {
			range:	true,
			values: [$(SliderMinID).val(), $(SliderMaxID).val()],
			
			slide:	function( event, ui){
				$(SliderMinID).attr('value',ui.values[0]);
				$(SliderMaxID).attr('value',ui.values[1]);
			}
		}
		
		function СheckValues(object, SliderValueType)
		{
			var CurrentValue = $(object).attr('value') - 0;
			
			if(CurrentValue < DefaultMinValue){
				CurrentValue = DefaultMinValue;
			}
			
			if(CurrentValue > DefaultMaxValue){
				CurrentValue = DefaultMaxValue;
			}
			
			$(object).attr('value', CurrentValue);
			$(SliderID).slider('values', SliderValueType, CurrentValue);
		};
		
		//объединяем параметры
		$.extend(true, SliderParams, SliderDefaultParams);
		//рисуем слайдер
		$(SliderID).slider(SliderParams);
		
		$(SliderMinID).bind('blur', function(){
			СheckValues(this,0);
		});
		$(SliderMaxID).bind('blur', function(){
			СheckValues(this,1);
		});

	}
	else{
		document.getElementById(SliderID).style.color = 'red';
		document.getElementById(SliderID).innerHTML = 'Для отображения слайдера необходимо, чтобы были подключены jQuery и jQuery UI';
	}
}