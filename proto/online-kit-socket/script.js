DecorComplects = {
	seoTemplate: {},

	sliders: [],

	itemInfo: {
		brand: {},
		collection: {},
		colorFrame: {},
		colorPush: {}
	},

	init: function(){},
	events: function(){},
	loaderShow: function(item){},
	loaderHide: function(){},
	setMetaData: function(type){},
	_setMetaData: function(link, title, description, descriptionText){},
	makeLink: function(type){},
	strReplace: function(){},
	setInfo: function(type, jsonInfo){},
	sliderInit: function(){}
}

DecorComplects.init = function(jsonSeoTemplate){
	if( $(".v-slider img").length > 0 )
		$('#complect-info').show();
	else
		$('#complect-info').hide();

	this.seoTemplate = JSON.parse(jsonSeoTemplate);
	this.events();
	this.sliderInit();
}

DecorComplects.events = function(){
	var self = this;
		self.ajaxSend = false;

	$(document).on('click', '.frames img', function(){
		$('.frames img').removeClass('active');
		var $this = $(this);
			$this.addClass('active');

		$('.frames-selected').css('background-image', 'url(' + $this.attr('src') + ')');

		self.setMetaData('colorPush');

		$('#complect-description').find('.frame-color').text($this.attr('data-name'));
		$('#goods-items').html("");
	});

	$(document).on('click', ".mini-wp", function(){
		document.getElementById('complect-info').className = $(this).attr('class');
	});

	$(document).on('click', '.pushs img', function(){
		$('.pushs img').removeClass('active');
		var $this = $(this);
			$this.addClass('active');

		$('.pushs-selected').css('background-image', 'url(' + $this.attr('src') + ')');

		self.setMetaData('colorPush');
		$('#complect-description').find('.push-color').text($this.attr('data-name'));
		$('#goods-items').html("");
	});

	$(document).on('click', '#brans-list li', function(){
		if( self.ajaxSend )
			return;

		var $this = $(this);
		$this.parent().find('.active').removeClass('active');
		$this.addClass('active');
		self.loaderShow($('#collection-list').parent());

		$.ajax({
		  type: "POST",
		  url: "/online-kit-socket/ajax.php",
		  dataType: "json",
		  data: { sectionId: $this.attr('data-id'), action: "getCollections" }
		}).done(function(data){
			if( data.status == "error" ){
				alert(data.error);
				return;
			}

			if( data.status == "success" && typeof data.data.items == 'object' ){
				$('#collection-list li').remove();
				$.each(data.data.items, function(i, e){
					var item = $('<li>'+e.name+'</li>');
						item.attr('data-id', e.id);
						item.attr('data-name', e.name);
						item.attr('data-code', e.code);
					$('#collection-list').append(item);
				});

				self.itemInfo.brand = data.data.sectionInfo;
				self.setMetaData('brand');
				$('#goods-items').html("");
			}
		}).always(function(){
			self.loaderHide();
		});
	});

	$(document).on('click', '#collection-list li', function(){
		if( self.ajaxSend )
			return;

		var $this = $(this);
		$this.parent().find('.active').removeClass('active');
		$this.addClass('active');
		self.loaderShow($('.block-images').parent());

		$.ajax({
		  type: "POST",
		  url: "/online-kit-socket/ajax.php",
		  dataType: "json",
		  data: { 
		  	collectionId: $this.attr('data-id'),
		  	brandId: $('#brans-list li.active').attr('data-id'), 
		  	action: "getFramesAndPushs"
		  }
		}).done(function(data){
			if( data.status == "error" ){
				alert(data.error);
				return;
			}

			if( data.status == "success" && typeof data.data.frames == 'object' ){
				self.sliderDestroy();
				$('.frames .v-slider img').remove();
				$.each(data.data.frames, function(i, e){
					var item = $('<img></img>');
						item.attr('src', e.preview_picture);
						item.attr('data-id', e.id);
						item.attr('data-name', e.name);
						//item.attr('data-code', e.code);
					$('.frames .v-slider').append(item);
				});
			}
			if( data.status == "success" && typeof data.data.pushs == 'object' ){
				self.sliderDestroy();
				$('.pushs .v-slider img').remove();
				$.each(data.data.pushs, function(i, e){
					var item = $('<img></img>');
						item.attr('src', e.preview_picture);
						item.attr('data-id', e.id);
						item.attr('data-name', e.name);
						//item.attr('data-code', e.code);
					$('.pushs .v-slider').append(item);
				});
			}

			self.afterAjaxHandler();
			self.itemInfo.collection = data.data.sectionInfo;
			self.setMetaData('colorPush');
			$('#goods-items').html("");
		
		}).always(function(){
			self.loaderHide();
		});
	});

	$(document).on('click', '#get-goods', function(){
		if( self.ajaxSend )
			return;

		self.loaderShow($('.block-images').parent());

		$.ajax({
		  type: "POST",
		  url: "/online-kit-socket/ajax.php",
		  dataType: "json",
		  data: { 
		  	collectionId: $('#collection-list li.active').attr('data-id'),
		  	brandId: $('#brans-list li.active').attr('data-id'),
		  	frameId: $('.frames img.active').attr('data-id'),
		  	pushId: $('.pushs img.active').attr('data-id'),
		  	action: "getGoods"
		  }
		}).done(function(data){
			if( data.status == "error" ){
				alert(data.error);
				return;
			}

			$('#goods-items').html(data.html);
		}).always(function(){
			self.loaderHide();
		});
	});
}

DecorComplects.loaderShow = function(item){
	var loader = $("<div></div>");
		loader.addClass('ajax-loader-wait');
		loader.append("<img src=\"\\online-kit-socket\\loader.gif\">");

	item.css('position', 'relative').append(loader);
}

DecorComplects.loaderHide = function(){
	$(document).find('.ajax-loader-wait').remove();
}

DecorComplects.setMetaData = function(type){
	var link, title, desc, pdesc;

	if( type == 'brand' ){
		link  = this.makeLink('brand');
		title = this.strReplace(this.seoTemplate.brand.title); 
		desc  = this.strReplace(this.seoTemplate.brand.description); 
		pdesc = this.itemInfo.brand.description;
	} else if (type == 'collection') {
		link  = this.makeLink('collection');
		title = this.strReplace(this.seoTemplate.collection.title); 
		desc  = this.strReplace(this.seoTemplate.collection.description); 
		pdesc = this.itemInfo.collection.description;
	} else if (type == 'colorFrame') {
		link  = this.makeLink('colorFrame');
		title = this.strReplace(this.seoTemplate.colorFrame.title);
		desc, pdesc = "";
	} else if (type == 'colorPush') {
		link  = this.makeLink('colorPush');
		title = this.strReplace(this.seoTemplate.colorPush.title);
		desc, pdesc = "";
	}

	this._setMetaData(link, title, desc, pdesc);
}

DecorComplects.makeLink = function(type){
	var link;

	if( type == 'brand' ){
		link = "/online-kit-socket/brand-" + this.itemInfo.brand.code + "/";
	} else if (type == 'collection') {
		link = this.makeLink('brand') + "collection-" + this.itemInfo.collection.code + "/";
	} else if (type == 'colorFrame') {
		link = this.makeLink('collection') + "colorFrame-" + $('.frames img.active').attr('data-id') + "/";
	} else if (type == 'colorPush') {
		link = this.makeLink('colorFrame') + "colorPush-" + $('.pushs img.active').attr('data-id') + "/";
	}

	return link;
}

DecorComplects._setMetaData = function(link, title, desc, pdesc){
	$('.description-information').html(pdesc);
	window.history.replaceState({}, title, link);
	document.title = title;
	$(document).find('h1').text(title);
}

DecorComplects.strReplace = function(str){
	str = str.replace("#brandName#", this.itemInfo.brand.name);
	str = str.replace("#collectionName#", this.itemInfo.collection.name);
	str = str.replace("#colorFrameName#", $('.frames img.active').attr('data-name'));
	str = str.replace("#colorPushName#", $('.pushs img.active').attr('data-name'));
	return str;
}

DecorComplects.setInfo = function(type, jsonInfo){
	this.itemInfo[type] = JSON.parse(jsonInfo);
}

DecorComplects.afterAjaxHandler = function(){
	if( $('.frames img').length > 0 && $('.pushs img').length > 0 ){
		$('.frames img').eq(0).click();
		$('.pushs img').eq(0).click();
		$('#complect-info').show();
		$("#complect-description").show();
		this.sliderInit();
	} else {
		$('#complect-info').hide();
		$("#complect-description").hide();
	}
}

DecorComplects.sliderInit = function(){
	var options = {
	    mode: 'vertical',
	    slideWidth: 150,
	    minSlides: 4,
	    maxSlides: 4,
	    // moveSlides: 1,
	    slideMargin: 10,
	    pager: false
	};

	var e = $('.pushs .v-slider');
	if( e.find('img').length > 0 ){
		var _e = e.bxSlider(options);
			_e.reloadSlider();
		this.sliders.push(_e);
	}
	
		e = $('.frames .v-slider')
	if( e.find('img').length > 0 ){
		this.sliders.push(e.bxSlider(options));
	}

	// setTimeout(function() { $.each(DecorComplects.sliders, function(i, e){e.redrawSlider();}) }, 400);
}

DecorComplects.sliderDestroy = function(){
	if( this.sliders.length > 0 ){
		$.each(this.sliders, function(i, e){
			e.destroySlider();
		});
		this.sliders = [];
	}
}
