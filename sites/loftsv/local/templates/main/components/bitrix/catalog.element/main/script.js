Studio8.Widget('detailPage', {
	documentReady: true,
	zoomSelector: null,
	init: function(){
	    var self = this;

		self.zoomSelector = $("#zoom_03");

		this.initZoom();

	    //pass the images to Fancybox
	    $("#zoom_03").on("click", function(e) {
	        var ez = self.zoomSelector.data('elevateZoom');
	        $.fancybox(ez.getGalleryList());
	        return false;
	    });

	    $(window).resize(function(){
	    	self.initZoom();
	    });

	    $('.carousel-btn').slick({
	        speed: 700,
	        arrows: true,
	        dots: false,
	        slidesToShow: 4,
	        slidesToScroll: 1,
	        prevArrow: '<button type="button" class="arrow-prev"><i class="zmdi zmdi-long-arrow-left"></i></button>',
	        nextArrow: '<button type="button" class="arrow-next"><i class="zmdi zmdi-long-arrow-right"></i></button>',
	        responsive: [
	            { breakpoint: 991, settings: { slidesToShow: 3 }  },
	            { breakpoint: 767, settings: { slidesToShow: 3 }  },
	            { breakpoint: 479, settings: { slidesToShow: 3 }  }
	        ]
	    });
	},
	destroyZoom: function(){
		$('.zoomContainer').remove();
		this.zoomSelector.removeData('elevateZoom');
		this.zoomSelector.removeData('zoomImage');
	},
	initZoom: function(){
		if( $(window).width() < 767 ){
			this.destroyZoom();
			return;
		}

	    if( $('#gallery_01 a.active').parent().index() > 0 ){
	    	this.zoomSelector.data('zoom-image', $('#gallery_01 a.active').attr('data-image'));
	    }
		this.zoomSelector.elevateZoom({
	        constrainType: "height",
	        zoomType: "lens",
	        lensSize: 300,
	        containLensZoom: true,
	        gallery: 'gallery_01',
	        cursor: 'pointer',
	        galleryActiveClass: "active",
	    });
	}
});