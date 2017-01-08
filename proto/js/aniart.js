/*
временный файл скриптов, пока идет верстка, выносим все наши стили сюда
потом совместим, или нет - будет видно
*/

function form_to_json (selector) {
  var ary = $(selector).serializeArray();
  var obj = {};
  for (var a = 0; a < ary.length; a++) obj[ary[a].name] = ary[a].value;
  return obj;
}

(function($) {
$.fn.serializeFormJSON = function() {

   var o = {};
   var a = this.serializeArray();
   $.each(a, function() {
       if (o[this.name]) {
           if (!o[this.name].push) {
               o[this.name] = [o[this.name]];
           }
           o[this.name].push(this.value || '');
       } else {
           o[this.name] = this.value || '';
       }
   });
   return o;
};
})(jQuery);
$('#contact').submit(function(e) {

 var $form = $(this);
 var data = $form.serializeFormJSON();
 
 $.post('process.php', data, function(output) {
 
  // do something here
 
 });

 e.preventDefault();

});
/* --- ANIART --- */
var Aniart = {//global scope for our code
	//vars
	jqAdminPanel: false,
	url: window.location.pathname,
	urlParams:decodeURIComponent(window.location.search),
	
	//functions
	init:			function(){},
	hereDoc:		function(){},
	combinePanels:	function(){},
	post:			function(){},
	hereDoc:		function(){},
	//entities
	User: {
		hasPanel:		function(){},
		ajaxRequest:	function(){},
		forgotPass: function(){},
		validEmail: function(){},
		showMess: function (){},
		checkEmail: function(){},
		updateUserPass: function(){},
		getJSON: function(){},
		//"Первый вход на сайт"
		firstEntry:function(){},
		//Проверка первого входа
		checkfirstEntry:function(){},
		//Скрытия окна авторизации
		hideAuthForm: function(){},
		//Показ окна авторизации,
		showAuthForm: function(){},
		//Форма восстановления пароля
		formRepairPass: function(){},
		// оправить запрос клиента
		specialOffers: function(){},
		// my test personal
		changeData: function(){},
		buttonActivate: function(){}
	},
	Basket:{
		init: function(){},
		ajaxBasket: function(){}
	},
	SafeMode: {
		attribute:	'worksafe',
		status: 	'off',
		expires:	365,
		
		init:		function(callback){},
		turnOn:		function(){},
		turnOff:	function(){},
		toggle: 	function(){},
		_saveStatus:function(){},
		_loadStatus:function(){}
	},
};

/* --- ANIART FUNCTIONS ---*/
Aniart.init = function(){
	this.jqAdminPanel = $('.bitrix-admin-panel');
        
        //авторизация пользователя
        $('body').on('submit', '#login_in', function() {
            var form = $(this).serializeArray();
            var login = form[0]['value'];
            var password = form[1]['value'];
            if( $(this).hasClass('anspopup') )
        		var callback = $(".answerModaldata");
            else	
            	var callback = $(".aut");
            
            var popupanswer = false;
            if( $(this).hasClass('anspopup') )
            	popupanswer = true; 
            Aniart.User.auth(login, password, callback, popupanswer);
        });
        
        //регистрация пользователя
        $('body').on('submit', '#registration_new', function() {
            var form = $(this).serializeArray();
            //console.log(form);
            var login = form[0]['value'];
            var email = form[1]['value'];
            var password = form[2]['value'];
            var callback = $(".aut");
            Aniart.User.registration(login, email, password, '', Aniart.url,callback);
        });
        
      //регистрация пользователя (оформление заказа)
        $('body').on('submit', '#registration_new_order', function() {
            var form = $(this).serializeArray();
            var name  = form[0]['value'];
            var login = form[1]['value'];
            var email = form[2]['value'];
            var password = form[3]['value'];
            var callback = $(".answerModaldata");
            Aniart.User.registration(login, email, password, name, Aniart.url, callback, true);
        });
        
        //первый вход
        Aniart.User.firstEntry();
        
       //показ окна авторизации
	   
	   $(".in-g a").click(function(){
		   Aniart.User.hideAuthForm()
	   });
	   
	   //форма восстановления пароля
	   $("#repair-pass").on("click", function(){
		   $("#myModal4").modal("show");
		   Aniart.User.formRepairPass($(this));
	   });
	   //конец форма восстановления пароля
	   
	   // отправить запрос клиента
	   $("body").on("submit", "#request-client", function(){
		   Aniart.User.specialOffers();
	   })
	   //конец отправить запрос клиента
	   
	   //*** Start Function for page "/personal/" ***//
	   $( ".day" ).focus(function() {
		 $(".cal").toggleClass("act");
		});
	
		$('.inf').hide();
		
		$('.form-control').focusin(function(){
			$(this).parent().parent().find('.inf').stop().fadeIn( 200 );
		}).focusout(function() {
	    	$(this).parent().parent().find('.inf').stop().fadeOut( 1000 );
		});
	    this.User.changeData('change-user-delivery');
	    this.User.changeData('change-user-info');
	    this.User.changeData('change-user-password', 'true', true);
		
	    this.User.buttonActivate('change-user-delivery');
	    this.User.buttonActivate('change-user-info');
	    this.User.buttonActivate('change-user-password');
		//*** End Function for page "/personal/" ***//
	    
		//*** Start INIT "/basket/" ***//
	    this.Basket.init();
	    $('.ord, .send').click(function(){
    		window.location.href = "/basket/order.php";
        });
	    //*** End INIT "/basket/" ***//
};

Aniart.combinePanels = function(){
	//var navbar	= $('.navbar');
	//var menu	= $('.top-cont');
	//navbar.css('top', this.jqAdminPanel.outerHeight());
	//menu.css('top', this.jqAdminPanel.outerHeight());
	var adminPanel = this.jqAdminPanel;
	$('body').children(':not(footer)').each(function(){
		var child = $(this);
		if(child.get(0) != adminPanel.get(0)){
			var top;
			if((top = child.data('top')) == undefined){
				top = parseInt(child.css('top'), 10) || 0;
				child.data('top', top);
			}
			top += adminPanel.outerHeight();
			child.css('top', top + 'px');
			if(child.css('position') == 'static'){
				child.css('position', 'relative');
			}
		}
	});
};

/**
 * AJAX запрос POST 
 * 
 * @param {string} url - путь к серверному скрипту.
 * @param {mixed} vals - входящие данные.
 * @param {string} type - формат выходящих данных.
 * @param {mixed} callBack - вывод ответа.
 */
Aniart.post = function(url, vals, type, callBack){
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            params: vals
        },
        dataType: type,
        cache: false,
        success: callBack
    });
};


Aniart.hereDoc = function hereDoc(f){
	return f.toString().
	replace(/^[^\/]+\/\*!?/, '').
	replace(/\*\/[^\/]+$/, '');
};

/* --- USER FUNCTIONS --- */

Aniart.User.buttonActivate = function(id){
	$( "#"+id ).change(function() {
	 	$('#'+id+' button[type="submit"]').removeAttr('disabled');
	});
}

Aniart.User.clearFields = function(id){
	$( "#"+id+' input' ).each(function() {
	 	$(this).val('');
	});
}

Aniart.User.changeData = function(id, ajax, clearfields){
	if( ajax=='undefined' )
		ajax = 'true';
	if( clearfields=='undefined' )
		clearfields = false;
	
	$('#'+id+' button[type="submit"]').click( function(){
		$.post( $('#'+id).attr('action'), $( '#'+id ).serialize() + "&ajax=" + "true", function( data ) {
			$('#answerModal .answerModaldata').html(data);
			$('#answerModal').modal('show');
			$('#'+id+' button[type="submit"]').attr('disabled', '');
			if(clearfields==true)
				Aniart.User.clearFields(id);
			//alert( "Data Loaded: " + data );
		});
		return false;
	});
}

Aniart.User.hasPanel = function(){
	return ($('#bx-panel').length > 0);
};


Aniart.User.specialOffers = function (){
	// передаю параметры формы 
	var paramsArray,
		email,
		placeMess = ".request-client-mess",
		self,
		path;
		
		self = this;
		paramsArray = $("#request-client").serialize();
		email = $("#inputAdr4").val();
		path = $("#request-client").attr("form-action");
		if(self.isEmptyFields($("#request-client input"), placeMess, true)){
			if(self.validEmail(email, placeMess)){
				$.post(
						path,
						paramsArray,
						function(data){
							var successRequest = $(".success-request");
								successRequest.fadeIn(2000);
								successRequest.fadeOut(5000);
						}
				);
			}else{
				$(placeMess).fadeOut(3000);
			}
		}
		
}
Aniart.User.isEmptyFields = function(formParams,placeMess, isError){
	var error = []; 
	formParams.each(function(idx, item){
		if($(item).val() == ""){
			error.push("Поле " + $(item).parent().prev().text() + " не заполнено </br>");
		}
	})
	if(error.length > 0){
		Aniart.User.showMess(error,placeMess,isError);
		return false;
	}else{
		return true;
	}
}

Aniart.User.formRepairPass = function(event){
	var currentModalForm = $(event).parents("#myModal3"),
		formRepairPass = $("#myModal4"),
	    email = currentModalForm.find("input").val(),
	    self = this;
	
	 // если поле email не пустое и заполнено корректно
	 if(self.validEmail(email, currentModalForm.find(".in-d"))){
		 self.checkEmail("/ajax/checkEmail.php",email);
		 self.updateUserPass();
	 }
}
Aniart.User.firstEntry = function(){
	this.checkFirstEntry() === false ? this.hideAuthForm():this.showAuthForm();
	$(".entry-quest").click(function(){
		$.cookie("firstEntry", false, {expires: 700, path: '/'});
	})
}
Aniart.User.checkFirstEntry = function(){
	if($.cookie("firstEntry") == "false"){
		return false;
	}else{
		return true;
	}
}
Aniart.User.hideAuthForm = function(){
	$("#myModal").modal('hide');
}
Aniart.User.showAuthForm = function(){
	$("#myModal").modal('show');
}


Aniart.User.updateUserPass = function(){
	var self = this;
	 $('#update-pass').click(function(){
		 var params = $("#form-repair-pass").serializeArray();
		 $.post("/ajax/updateUserPass.php", params, function(data){
			 if(data.ok){
				 self.showMess(data.ok, $("#myModal4").find(".in-d"),false);
				 setTimeout(function(){
					 window.location.href = "/"; 
				 },5000);
			 }else{
				 self.showMess(data.error, $("#myModal4").find(".in-d"),true);
			 }
		 },"json");
	 });
}

Aniart.User.checkEmail = function(url, email){
	var self = this;
	self.getJSON(url,email);
	
}
Aniart.User.getJSON = function(url, params){
	var self = this;
	$.getJSON(
			url,
			{params:params}, 
			function(data, textStatus, jqXHR){
				if(data.error != undefined){
					self.showMess(data.error, $("#myModal4").find(".in-d"),true);
				}else{
					self.showMess(data.mess, $("#myModal4").find(".in-d"),false);
				}
			}
	);
}

Aniart.User.validEmail = function (email, place){
		var self = this;
		if(email !== "" ){
			var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
			var isValidEmail = email.match(pattern);
			if(isValidEmail != null){
				return true;
			}else{
				self.showMess("Поле email заполнено некорректно!", place,true);
				return false;
			}
		}else{
			self.showMess("Заполните поле email!", place, true);
			return false;
		}
		return true;
}

Aniart.User.showMess = function(mess, place,isError){
	var tag,
		objMessPlace,
		reg;
	
	if(typeof mess == "object"){
		reg = /\,/
		mess = mess.toString().replace(reg, "");
	}
	if(isError) {
		tag = "<p class='mess-error'>"+ mess +"</p>";
	}else{
		tag = "<p class='mess'>"+ mess +"</p>";
	}
	objMessPlace = $(place);
	objMessPlace.html(tag);
	objMessPlace.is(":hidden") ? objMessPlace.show() : "";	 
}

/**
 * Авторизация пользователя.
 * 
 * @param {string} login.
 * @param {string} password.
 * @param {object} callback.
 */
Aniart.User.auth = function(login, password, callback, answerpop) {
    if(!login) {
        callback.html('<span style="color:red">Логин не определён!</span>');
        if(answerpop)
        	$('#answerModal').modal('show');
    } else if(!password) {
        callback.html('<span style="color:red">Пароль не указан!</span>');
        if(answerpop)
        	$('#answerModal').modal('show');
    } else {
        Aniart.post(
            '/ajax/authorization.php',
            {login: login, pass: password},
            'html',
            function (data) {
                if(data == 'Y') {
                    callback.html('<span>Авторизация успешно пройдена!</span>');
                    if(answerpop)
                    	$('#answerModal').modal('show');
                    setTimeout( function () { location.reload(); }, 2000);
                } else {
                    callback.html('<span style="color:red">' + data + '</span>');
                    if(answerpop)
                    	$('#answerModal').modal('show');
                }
            }
        );
    }
};

/**
 * Регистрация пользователя.
 * 
 * @param {string} login.
 * @param {string} email.
 * @param {string} password.
 * @param {object} callback.
 */
Aniart.User.registration = function(login, email, password, name, pageReg, callback, answerpop) {

	if(!login) {
        callback.html('<span style="color:red">Логин не определён!</span>');
        if(answerpop)
        	$('#answerModal').modal('show');
    } else if(login.length < 3) {
        //api bitrix
        callback.html('<span style="color:red">Логин слишком короткий! Укажите не менее 3-х символов.</span>');
        if(answerpop)
        	$('#answerModal').modal('show');
    } else if(!email) {
        callback.html('<span style="color:red">Email не определён!</span>');
        if(answerpop)
        	$('#answerModal').modal('show');
    } else if(!password) {
        callback.html('<span style="color:red">Пароль не указан!</span>');
        if(answerpop)
        	$('#answerModal').modal('show');
    } else if(password.length < 3) {
        //api bitrix
        callback.html('<span style="color:red">Пароль слишком короткий! Укажите не менее 3-х символов.</span>');
        if(answerpop)
        	$('#answerModal').modal('show');
    } else {
        Aniart.post(
            '/ajax/registration.php',
            {login: login, email: email, pass: password, name: name, pageReg:pageReg},
            'json',
            function (data) {
            	if(answerpop){
            		if(data.ok)
            			$('#answerModal .answerModaldata').html(data.ok);
            		else
            			$('#answerModal .answerModaldata').html(data.error);
            			
            		$('#answerModal').modal('show');
            	} else {
	            	if(data.ok){
	            		Aniart.User.showMess(data.ok, $("#myModal2").find(".in-d"),false);
	            	}else{
	            		Aniart.User.showMess(data.error, $("#myModal2").find(".in-d"),true);
	            	}
            	}
                
            }
        );
    }
};

/* --- BASKET FUNCTIONS --- */
Aniart.Basket.init = function($input){
	$('body').on('click', '.minus', function() {
		var $input = $(this).parent().find('input');
		var count = parseInt($input.val()) - 1;
		//count = count < 1 ? 1 : count;
		$input.val(count);
		//$input.change();
		Aniart.Basket.ajaxBasket($input);
		return false;
	});
	
	$('body').on('click', '.plu', function() {
		var $input = $(this).parent().find('input');
		var count = parseInt($input.val()) + 1;
		$input.val(count);
		//$input.change();
		Aniart.Basket.ajaxBasket($input);
		return false;
	});
	
	$('body').on('click', '.dropdown.basc .close, .it-close', function(){
		var $input = $(this).parent().find('input');
		var count = 0;
		$input.val(count);
		Aniart.Basket.ajaxBasket($input);
	});
};

Aniart.Basket.ajaxBasket = function($input){
	var params = { 'quantity':parseInt($input.val()), 'elemId':$input.attr('id-element') };
	//var params = { 'quantity':parseInt(count), 'elemId':parseInt(id) };
	$('#item-'+$input.attr('id-element')+' .ajax-overlay').fadeIn();
	Aniart.post('/ajax/basket.php?method=allUpdate&ajax=true', JSON.stringify(params), 'html', function updateInfo(jsonData){
		data = JSON.parse(jsonData);
		$('.with-item .row').empty().append(data.content);
		$('.dropdown.basc ul.dropdown-menu').empty().append(data.smallBasket);
		$('.dropdown.basc .tr-text').text(data.smallBasketCount);
		//console.log(data);
	});
};


/* --- SAFE_MODE FUNCTIONS --- */
Aniart.SafeMode.init = function(callback){
	this._loadStatus();
	(this.status == 'on') ? this.turnOn() : this.turnOff();
	if(typeof callback == 'function'){
		callback.call(this);
	}
};

Aniart.SafeMode.turnOn = function(){
	this.status = 'on';
	this._saveStatus();
	$('['+this.attribute+' = "Y"]').addClass('opac');
};

Aniart.SafeMode.turnOff = function(){
	this.status = 'off';
	this._saveStatus();
	$('['+this.attribute+' = "Y"]').removeClass('opac');
};

Aniart.SafeMode.toggle = function(){
	(this.status == 'on') ? this.turnOff() : this.turnOn();
};

Aniart.SafeMode._saveStatus = function(){
	$.cookie(this.attribute, this.status, {expires: this.expires});
};

Aniart.SafeMode._loadStatus = function(){
	var status;
	if((status = $.cookie(this.attribute)) != undefined){
		this.status = status;
	}
};

/*--------------------------*/

$(document).ready(function(){
	Aniart.init();
	//подружим между собой админ-панель Битрикса и навигационную панель сайта
	if(Aniart.User.hasPanel()){
		Aniart.combinePanels();
		Aniart.jqAdminPanel.find('#bx-panel-expander, #bx-panel-hider').on('click', function(){
			setTimeout(function(){
				Aniart.combinePanels($(this));
			}, 50);
		});
	}
	
	//инициализация и подключение Безопасного режима
	Aniart.SafeMode.init(function(){
		var safeModeSwitcher = $('.onoffswitch-label');
		if(safeModeSwitcher.length > 0){
			var safeMode = this;
			safeModeSwitcher.on('click', function(event, ignoreSafeMode){
				$(this).toggleClass("on");
				if(!ignoreSafeMode){
					safeMode.toggle();
				}
			});
			if(this.status == 'on'){
				safeModeSwitcher.trigger('click', [true]);
			}
		}
	});
});