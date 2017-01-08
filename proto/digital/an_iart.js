var Aniart = {
    //vars
    url: window.location.href,
            
    //functions
    init: function(){},
    post: function(){},
    get: function(){},
        
    Header: {
        init: function(){},
        referer: {}, // будет определен ниже, T:36
    },
            
    User: {

    },
            
    Catalog: {
        init: function(){}
    },
    
    Basket: {
        init: function(){}
    },
    
    Order: {
        init: function(){}
    },

    GTM: {
        init: function(){
            try {
		$(document).ready(function(){
		    Aniart.GTM.createInfoPageItems.create();
		    $.each(Aniart.GTM.eventClickElement, function(func){
			this();
		    });
		});
		return this;
	    } catch(e) {
		console.log(e);
	    }
        },

        addItems: function(obj){ // Добавление данных для посл обработки
            if( typeof obj != 'object' )
                return this;
            
            $.extend(true, this.objGTM, obj);

            return this;
        },

        dataPush: function(data){ // Отправка данных в google tag manager
            if( typeof data == 'object' )
                dataLayer.push(data);
        },

		objIntersect: function(obj, obj1){
			var newObj = new Object();
			$.each(obj, function(i, e){
					if ( typeof(obj1[e])!='undefined' ){
						newObj[e] = obj1[e];
					}
			});
			if ( typeof(newObj.id)=='undefined' || typeof(newObj.name)=='undefined' ) {
				return new Object();
			}
			return newObj;
		},
		
        objGTM: {}, // Солянка данных страницы

        createInfoPageItems: { // для создания массива из элементов на странице
            _items: {}, // внутр обьект для работы
            itemTypes: ["impressions", "detail", "promoView"], // допустимые типы
            create: function(){ // создание особого масива для отправки
                // проходимся по всем типам получ данных
                $.each(Aniart.GTM.objGTM, function(i, e){
                    // отсеиваем не правильные типы и созд предв пуст данные если это первый тип
                    if( typeof Aniart.GTM.createInfoPageItems._items[i] == 'undefined' && 
                        Aniart.GTM.createInfoPageItems.itemTypes.indexOf(i) >= 0 ){

                        if( i != 'impressions' )
                            Aniart.GTM.createInfoPageItems._items[i] = new Object();
                        else 
                            Aniart.GTM.createInfoPageItems._items[i] = new Array();
                    }

                    var _tempItems = new Array();

                    // проходимся по элементам и форм массив данных для отправки
                    if( typeof e == 'object' &&
                        Aniart.GTM.createInfoPageItems.itemTypes.indexOf(i) >= 0 ){
                        $.each(e, function(idx, el){
							var _el = {};
							$.each(el, function(i, e){
								_el[i] = e.toString();
							});
							_tempItems.push(_el);
                        });
                    }

                    // укладываю данные согласно типу
                    switch (i) {
                      case 'detail':
                        if( typeof Aniart.GTM.createInfoPageItems._items[i].products != 'array' ){
                            Aniart.GTM.createInfoPageItems._items[i].products = new Array();
                        }
                        $.extend(Aniart.GTM.createInfoPageItems._items[i].products, _tempItems);
                        break
                      case 'promoView':
                        if( typeof Aniart.GTM.createInfoPageItems._items[i].promotions != 'array' )
                            Aniart.GTM.createInfoPageItems._items[i].promotions = new Array();
                        $.extend(Aniart.GTM.createInfoPageItems._items[i].promotions, _tempItems);
                        break
                      default:
                        $.extend(Aniart.GTM.createInfoPageItems._items[i], _tempItems);
                    }

                });

				if (this._items.detail.products.length > 0)
					this._items.detail.actionField = 'catalogDetail';

                this.pushItems();

                return this._items;
            },
            pushItems: function(){ // отправлка данных на сервер google
                _obj = new Object();
                _obj.ecommerce = new Object();
                _obj.ecommerce.currencyCode = 'UAH';
                $.extend(_obj.ecommerce, this._items);
                Aniart.GTM.dataPush(_obj);
            },
        },
        eventClickElement: { // Обьект для работы с кликами - необходимо инициализировать
            productClick: function(){ // клик по ссылке перейти на детальную из каталога
		$(document).on('click', 
                                '.catalog_block .item-title a, .catalog_block a.thumb, .desc_name a, table.list_item a.thumb, .item-name-cell a, .catalog_item a:not(.remove, .one_click, .to-cart), .item-title a, .image a.thumb, .front_slider a.read_more', 
                                function()
                {
                    var href = $(this).attr('href').split('/');
                    href.splice(-1, 1); // предполагаем что посл эл пустой, а предпосл явл ИД
                    var elemId = href[href.length-1];
                    var trig = false;
                    $.each(Aniart.GTM.objGTM, function(i, e){
                    	$.each(e, function(ix, el){
                    		if( el.id==elemId ){
                                Aniart.GTM.eventsFunction.productClick(el);
                                trig = true;
                                return false;
                            }
                    	});
                    	if( trig )
                    		return false;
                    	/*if( typeof this[elemId] != 'undefined'){
                            Aniart.GTM.eventsFunction.productClick(this[elemId]);
                            return false;
                        }*/
                    });

                    return true;
                });
            },
            promoClick: function(){
		$(document).on('click', 
                                '.viewed_products_column a.image, .viewed_products_column .item-title a, .advt_banner a',
                               function()
                {
                    var href = $(this).attr('href').split('/');
                    href.splice(-1, 1); // предполагаем что посл эл пустой, а предпосл явл ИД
                    var elemId = href[href.length-1];
                    if( typeof Aniart.GTM.objGTM.promoView[elemId] != 'undefined' )
                        Aniart.GTM.eventsFunction.promoClick(Aniart.GTM.objGTM.promoView[elemId]);

                    return true;
                });
            },
            addToBasket: function(){
                $(document).on('click', 'a.basket_button.to-cart, .counter_block.basket .plus', function(){
                    var elemId = $(this).attr('data-item');
		    if ( $(this).hasClass('plus') ) {
			var elemId = $(this).parent().attr('data-item');
		    }
                    var data = $.grep(Aniart.GTM.objGTM.impressions, function(e){ return e.id == elemId; });
		    if ( typeof data == 'undefined') {
			data = $.grep(Aniart.GTM.objGTM.detail, function(e){ return e.id == elemId; });
		    }
		    /*if( typeof Aniart.GTM.objGTM.impressions[elemId] != 'undefined' )
                        var data = Aniart.GTM.objGTM.impressions[elemId];
                    else if( Aniart.GTM.objGTM.detail[elemId] != 'undefined' )
                        var data = Aniart.GTM.objGTM.detail[elemId];*/
                    
		    if (typeof data[0] != 'undefined'){
			data[0].quantity = parseInt($('.counter_block[data-item="'+elemId+'"] input').val());
			
			if ( $(this).hasClass('plus') )
			    data[0].quantity = 1;
			
			Aniart.GTM.eventsFunction.addToBasket(data[0]);
		    }
		    
                    return true;
                });
            },
            removeBasket: function(){
                $(document).on('click', '.catalog_item a.remove, .remove-cell .remove, .counter_block.basket .minus', function(){
                    var elemId = $(this).attr('data-item');
                    
		    if ( $(this).hasClass('minus') )
			var elemId = $(this).parent().attr('data-item');
		    
		    var data = $.grep(Aniart.GTM.objGTM.impressions, function(e){ return e.id == elemId; });
		    if ( typeof data == 'undefined')
			data = $.grep(Aniart.GTM.objGTM.detail, function(e){ return e.id == elemId; });
		    /*if( typeof Aniart.GTM.objGTM.impressions[elemId] != 'undefined' )
                        var data = Aniart.GTM.objGTM.impressions[elemId];
                    else if( Aniart.GTM.objGTM.detail[elemId] != 'undefined' )
                        var data = Aniart.GTM.objGTM.detail[elemId];*/

		    if (typeof data[0] != 'undefined'){
			
			if ( $(this).hasClass('minus') )
			    data[0].quantity = 1;
			    
		        Aniart.GTM.eventsFunction.removeBasket(data[0]);
		    }

                    return true;
                });
            },
            checkoutOrder: function(){
                $('body').on('click', '#second_step_order', function(){
                    data = Aniart.GTM.objGTM.impressions;
                    Aniart.GTM.eventsFunction.checkout(data, 1);

                    return true;
                });

                $(document).on('click', '#place_order button.button30', function(){
                    data = Aniart.GTM.objGTM.impressions;
                    Aniart.GTM.eventsFunction.checkout(data, 2);

                    return true;
                });

                $(document).on('click', '.order-form-content-left label', function(){
                    Aniart.GTM.eventsFunction.checkoutOption($(this).text());
                });
            },
        },
        eventsFunction: {
            productClick: function(obj)
            {  // формируем и отправляем данные для клику по ссылке перехода на детальную страницу товара
                if( typeof obj != 'object' )
                    return this;

                _obj = {
                    'event': 'productClick',
                    'ecommerce': {
                        'click': {
                            'actionField': {
                                'list': obj.list,
                                'action': 'click',
							},
							'products': new Array(),
                        },
                    },
                };

				obj = Aniart.GTM.objIntersect(["id", "name", 'price', 'brand', 'category', 'position'], obj);

                _obj["ecommerce"]['click']['products'].push(obj);

                Aniart.GTM.dataPush(_obj);

                return this;
            },

            promoClick: function(obj)
            {
                _obj = {
                    "event": "promotionClick",
                    "ecommerce": {
                        "promoClick": {
                            "promotions": new Array(),
                        }
                    }
                }
				
				obj = Aniart.GTM.objIntersect(["id", "name", 'creative', 'position'], obj);
                
				_obj["ecommerce"]['promoClick']['promotions'].push(obj);
				
                Aniart.GTM.dataPush(_obj);

                return this;
            },

            addToBasket: function(obj)
            {
                _obj = {
                    "event": "addToCart",
                    "ecommerce": {
                        "currencyCode": "UAH",
                        "add": {
                          "products": new Array(),
                        }
                    }
                }

				obj = Aniart.GTM.objIntersect(["id", "name", 'price', 'brand', 'category', 'position', 'quantity'], obj);
				
                _obj["ecommerce"]['add']['products'].push(obj);

                Aniart.GTM.dataPush(_obj);

                return this;
            },

            removeBasket: function(obj)
            {
                _obj = {
                    "event": "removeFromCart",
                    "ecommerce": {
                        "currencyCode": "UAH",
                        "remove": {
                          "products": new Array(),
                        }
                    }
                }

				obj = Aniart.GTM.objIntersect(["id", "name", 'price', 'brand', 'category', 'position', 'quantity'], obj);
				
                _obj["ecommerce"]['remove']['products'].push(obj);

                Aniart.GTM.dataPush(_obj);

                return this;
            },

            checkout: function(obj, step)
            {
                _obj = {
                    "event": "checkout",
                    "ecommerce": {
                      "checkout": {
                        "actionField": {
                          "step": step
                        },
                      "products": new Array(),
                      /*[{
                        "id": "57b9d",
                        "name": "Kiosk T-Shirt",
                        "price": "55.00",
                        "brand": "Kiosk",
                        "category": "T-Shirts",
                        "variant": "red",
                        "dimension1": "M",
                        "position": 0,
                        "quantity": 2
                      }]*/
                    },
                  }
                }

				obj = Aniart.GTM.objIntersect(["id", "name", 'price', 'brand', 'category', 'position', 'quantity'], obj);
				
                _obj["ecommerce"]['checkout']['products'].push(obj);

                Aniart.GTM.dataPush(_obj);

                return this;
            },

            checkoutOption: function(option)
            {
                _obj = {
                  "event": "checkoutOption",
                  "ecommerce": {
                    "checkout_option": {
                      "actionField": {
                        "step": 2,
                        "option": option
                      }
                    }
                  }
                }

                Aniart.GTM.dataPush(_obj);

                return this;
            },
	    
	    orderSend: function(obj, option)
            {
                _obj = {
                  "event": "transaction",
                  "ecommerce": {
                    "purchase": {
                      "actionField": {
                        id:option.id,//order id
                        revenue:option.revenue,//order price
                        action:"purchase",
                        affiliation:"DigitalVideo",//shop name
                        shipping:option.shipping,//delivery
                        tax:option.tax//nalog
                      },
                    "products": obj,
                    }
                  }
                }

                Aniart.GTM.dataPush(_obj);

                return this;
            },
        },
    }
};

Aniart.init = function(){
    
    //console.log('Y');
    
    Aniart.Catalog.init();
    
    Aniart.Basket.init();
    
    Aniart.Order.init();

    //Aniart.Header.init();

    this.GTM.init(); // Google Tag Manager

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

/**
 * AJAX запрос GET
 * 
 * @param {string} url - путь к серверному скрипту.
 * @param {mixed} vals - входящие данные.
 * @param {string} type - формат выходящих данных.
 * @param {mixed} callBack - вывод ответа.
 */
Aniart.get = function(url, vals, page, type, callBack){
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            params: vals,
            PAGEN_1: page
        },
        dataType: type,
        cache: false,
        success: callBack
    });
},
        
Aniart.Catalog.init = function() {
    
},
        
Aniart.Basket.init = function() {

},

Aniart.Order.init = function() {
    
    $('body').on('click', '#comment_order_pre', function(){
        $('#comment_order_pre_text').toggle();
    });
    
    $('body').on('change', '#deliveri_storage', function() {
        var ware_id = $('#deliveri_storage option:selected').val();
        var ware_name = $('#deliveri_storage option:selected').text();
        var data_dop = $('#deliveri_storage option:selected').attr('data-dop').split(/[#]/);
        var data_phone = '<span>т. ' + data_dop[0] + '</span><br/>';
        var data_weight = '';
        if(data_dop[1] > 0) {
            data_weight = '<span>до . ' + data_dop[1] + ' кг</span><br/>';
        }
        var data_work = '';
        if(data_dop[2] > 0) {
            data_work = '<span>График работы: ' + data_dop[2] + '</span><br/>';
        }
        if(ware_id > 0) {
            $('#ORDER_PROP_8').val(ware_name);
            $('#dop_info_stor_pre').html(data_phone + data_weight + data_work);
            $.cookie('delivery_np', ware_id);
        }
    });

    
    $('body').on('click', '#second_step_order', function(){
        var name = $('#ORDER_PROP_1').val();
        var city = $('#ORDER_PROP_6_val').val();
        var phone = $('#ORDER_PROP_3').val();
        var email = $('#ORDER_PROP_2').val();
        var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;

        if(name.length <= 0) {
            $('#ORDER_PROP_1').css({'background-color' : '#ffdab9', 'border' : '1px solid #fa8072'});
        } else {
            $('#ORDER_PROP_1').css({'background-color' : '#fff', 'border' : '1px solid #cbcbcb'});
        }
        if(!pattern.test(email)) {
            $('#ORDER_PROP_2').css({'background-color' : '#ffdab9', 'border' : '1px solid #fa8072'});
        } else {
            $('#ORDER_PROP_2').css({'background-color' : '#fff', 'border' : '1px solid #cbcbcb'});
        }
        if(city.length <= 0) {
            $('#ORDER_PROP_6_val').css({'background-color' : '#ffdab9', 'border' : '1px solid #fa8072'});
        } else {
            $('#ORDER_PROP_6_val').css({'background-color' : '#fff', 'border' : '1px solid #cbcbcb'});
        }
        
        if(phone.length <= 0) {
            $('#ORDER_PROP_3').css({'background-color' : '#ffdab9', 'border' : '1px solid #fa8072'});
        } else {
            $('#ORDER_PROP_3').css({'background-color' : '#fff', 'border' : '1px solid #cbcbcb'});
        }
        
        if(name.length > 0 && phone.length > 0 && city.length > 0 && pattern.test(email)) {
            $('#order_form_first_block').hide();
            $('#order_form_second_block').show();
            $('#place_order').show();
            $.cookie('order_step', '2');
        } else {
            $.cookie('order_step', '1');
        }

        return true;
    });
    
    $('body').on('click', '#first_step_order', function(){
        $('#order_form_second_block').hide();
        $('#place_order').hide();
        $('#order_form_first_block').show();
        $.cookie('order_step', '1');

        return true;
    });
},

$(document).ready(function() {
    Aniart.init();
});


function ShowGiftActions(actionID, productID)
{	
	name = 'show_gifts_actions';
		
	$('body').find('.'+name+'_frame').remove();
	$('body').append('<div class="'+name+'_frame popup"></div>');
	$('.'+name+'_frame').jqm({
		trigger: '.'+name+'_frame.popup', 
		onHide: function(hash) { 
			//window.location.href = window.location.href; 
			$('body').find('.'+name+'_frame').remove();
			$('body').find('.jqmOverlay').remove();		
		}, 
		toTop: false, 
		onLoad: function( hash ){ 
			onLoadjqm(name, hash ); 
		}, 
		ajax: '/catalog/ajax/show_gift_action.php?id='+actionID+'&product_id='+productID
	});
	$('.'+name+'_frame.popup').click();	
}

// For T:36
Aniart.Header.referer = {
    refererInfo: {}, // createInHeaderTemplate
    refererUrl:  document.referrer,
    params:      window.location.search,
    setAttr:     false,
    config: {
        forClass:    [],    // для каких эл-ов с классом произв. замену
        forId:       '',    // для каких эл-ов с ид произв. замену
        propElemId:  [],    // имя свойства эл где можно найти ИД
        cookieName:  '',    // имя куки файла
        useAfterAjax: false // не дописаный ф-л
    },
    // инициализация работы замены ид артикулов
    init: function(config){
        if( typeof(config)=='object' )
            $.extend(this.config, config);

        // получаем только УРЛ реферера без параметров
        this.refererUrl = this.refererUrl.substring(0, this.refererUrl.indexOf('?'));

        // для изменения приоритета необходимо поменять местами методы findUtmPoint(), findReferer
        this.findUtmPoint().findReferer().findCookie().initArticle();
    },
    //установка куков
    setCookie: function(value, exp){
        $.cookie(this.config.cookieName, value, { expires: exp, path: '/' });

        return this;
    },
    // получение данных куков
    getCookie: function(){
        var cookie = $.cookie(this.config.cookieName);
        if( typeof(cookie)=='undefined' )
            return false;
        
        return JSON.parse(cookie);
    },
    // удаление куков
    removeCookie: function(){
        $.removeCookie(this.config.cookieName, { path: '/' });

        return this;
    },
    // подготовка данных для установки куков
    createDataCookie: function(elem){
        var obj = {};
            obj.dateInst = parseInt(new Date().getTime() / 1000);
            obj.dateMod  = elem.dateMod;
            obj.id       = elem.id;
            obj.index    = elem.prefix;
            obj.name     = elem.name;
            obj.cookieLife = elem.cookieLife;

        return JSON.stringify(obj);
    },
    // инициализация механизма замены артикулов
    // для отработки после аякса и вставки данных на страницу, 
    // необходимо вызвать его, и он ИД будут изменены если необходимо
    initArticle: function(){
        if( !this.setAttr )
            return this;

        var _this = this;

        // проходимся по всем класам в которых необходимо произвести замену
        $.each(_this.config.forClass, function(i, e){
            _this.setArticleId('.', e);
        });
        // проходимся по всем ид в которых необходимо произвести замену
        $.each(_this.config.forId, function(i, e){
            _this.setArticleId('#', e);
        });
        return this;
    },
    // установка артикулов
    setArticleId:function(pref, atrname){
        var _this = this;
        // проходимся по всем найденным наборам елементов
        $('body '+pref+atrname).each(function(i, e){
            var _jqe = $(e);
            // проходимся по всем возм свойствам для определения Ид товара
            $.each(_this.config.propElemId, function(inx, el){
                attr = _jqe.attr(el);

                if( typeof(attr) === 'undefined' )
                    return true;
                _jqe.text(_this.setAttr+'-'+attr);
            });
        });
    },
    // поиск по рефереру и установка куков
    findReferer: function(){
        if( typeof(this.refererUrl)==='undefined' //||
            //this.refererUrl.match('/'+window.location.hostname+'/')
            )
            return this;

        var _this = this;

        // ищем совпадение реферера
        $.each(this.refererInfo, function(i,e){
            var ref = e;
            $.each(ref.preg, function(idx, el){
                var regEx = new RegExp(el, "i");
                if( _this.refererUrl.match(regEx) ){
                    var date = new Date();
                    var seconds = parseInt(ref.cookieLife);
                        date.setTime(date.getTime() + (seconds * 1000));

                    _this.setCookie(_this.createDataCookie(ref), date);
                }
            });
        });

        return this;
    },
    // поиск по utm-point и установка куков
    findUtmPoint: function(){
        match = '/'+window.location.hostname+'/';
        if( typeof(this.params)==='undefined' )
            return this;

        var _this = this;

        // ищем совпадение utm
        $.each(this.refererInfo, function(i,e){
            var ref = e;
            $.each(ref.utm, function(idx, el){
                var regEx = new RegExp(el, "i");
                if( _this.params.match(regEx) ){
                    var date = new Date();
                    var seconds = parseInt(ref.cookieLife);
                        date.setTime(date.getTime() + (seconds * 1000));

                    _this.setCookie(_this.createDataCookie(ref), date);
                }
            });
        });

        return this;
    },
    // поиск по кукам, так же происходит вычисление разницы данных и переустановка куков
    findCookie: function(){
        var cookie = this.getCookie();
        if( typeof(cookie)!=='object' )
            return this;

        this.setAttr = cookie.index;

        
        var currRef = this.refererInfo[cookie.id];
        // если не нашли инф о данном источнике удаляем куку
        if( typeof(currRef)=='undefined' ){
            this.removeCookie().setAttr = false;
            return this;
        }

        // сверяем дату посл модиф данных
        if( currRef.dateMod == cookie.dateMod )
            return this;

        // сверяем дату жизни кук от посл изменений
        if( currRef.cookieLife == cookie.cookieLife )
            return this;

        // если разница обнаружена пересчитываем и устанавливаем новые от пересчитаного время установки
        var date = new Date(cookie.dateInst);
            date.setTime(cookie.dateInst * 1000 + parseInt(currRef.cookieLife) * 1000);
        this.setCookie(this.createDataCookie(e), date);

        return this;
        /*if( currRef.dateMod != cookie.dateMod ){
            if( currRef.cookieLife != cookie.cookieLife ){
                var date = new Date(cookie.dateInst);
                    date.setTime(cookie.dateInst * 1000 + parseInt(currRef.cookieLife) * 1000);
                this.setCookie(this.createDataCookie(e), date);
            }
        }
        //console.log( new Date(currRef.dateMod*1000) );
        */
    },
}

