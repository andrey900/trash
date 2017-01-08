if(!Aniart){
	Aniart = {};
}

Aniart.Popup = function(id, outerParams)
{
	"use strict";
	
	Object.defineProperty(this, 'prefix', {
		value: 'aniart_popup_'
	});
	
	Object.defineProperty(this, 'container', {
		enumerable:	true,
		get:		getContainer,
		set:		setContainer
	});

	//constants
	Object.defineProperties(this, {
		'STATUS_NEW': {
			enumerable: true,
			value: 0
		},
		'STATUS_OPENED': {
			enumerable: true,
			value: 1
		},
		'STATUS_CLOSED': {
			enumerable: true,
			value: 2
		}
	});
	
	this.id			= this.prefix + id;
	this.container	= outerParams['container'] || undefined;
	this.content	= outerParams['content'] || undefined;
	this.layout		= outerParams['layout'] || undefined;
	this.useLayout	= outerParams['useLayout'] || true;
	this.status		= this.STATUS_NEW;
	
	//local variables
	var _container;
	
	function getContainer(){
		return _container;
	}
	
	function setContainer(jqObject){
		if(jqObject instanceof jQuery){
			_container = jqObject;
			_container.attr('id', this.id);
			_container.css('display', 'none');
			_container.data('AniartPopup', this);
		}
		else{
			throw new Error('Container must be a jQuery object!'); 
		}
	}
}

Aniart.Popup.get = function(popupID){
	if(popupID){
		var popupContainer = $('#' + this.prefix + this.id);
		if(popupContainer.length == 1){
			return popupContainer.data('AniartPopup');
		}
	}
	return false;
};

Aniart.Popup.prototype.open = function(){
	if(this.useLayout){
		if(this.status == this.STATUS_NEW){
			$('body').append(this.layout);
		}
		this.layout.show();
	}
	if(this.status == this.STATUS_NEW){
		$('body').append(this.container);
	}
	this.container.show();
	this.status = this.STATUS_OPENED;
};

Aniart.Popup.prototype.close = function(){
	if(this.useLayout){
		this.layout.hide();
	}
	this.container.hide();
	this.status = this.STATUS_NEW;
};

Aniart.Popup.prototype.remove = function(){
	if(this.useLayout){
		this.layout.remove();
	}
	this.container.remove();
	this.status = this.STATUS_REMOVED;
};

Aniart.Popup.prototype.changeContent = function(content){
	this.container.empty().append(content);
};


Aniart.DefaultPopup = function(id, outerParams)
{
	this.id			= this.prefix + id;
	this.container	= this.container; //need to setter work
	this.useLayout	= (outerParams['useLayout'] === false) ? false : this.useLayout;

	this.showClose	= outerParams['showClose']	|| true;
	this.showLogo	= outerParams['showLogo']	|| true;
};

Aniart.DefaultPopup.prototype = new Aniart.Popup('', {
	container: $(Aniart.hereDoc(function(){
		/*
		<div class="modal fade mod mod-1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="all-im-mod">
						<div class="modal-header"></div>
					</div>
				</div>
			</div>
		</div>
		*/		
	})),
	layout: $('<div>', {
		'class'			: 'modal-backdrop fade in'
	})
});
