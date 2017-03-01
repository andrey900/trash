<!DOCTYPE html>
<html>
<head>
	<title>test</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</head>
<body>

<style type="text/css">
	.animated {
		opacity: 0;
		visibility: hidden;
		-webkit-animation-fill-mode: both;
		-moz-animation-fill-mode: both;
		-ms-animation-fill-mode: both;
		-o-animation-fill-mode: both;
		animation-fill-mode: both;
		-webkit-animation-duration: 1s;
		-moz-animation-duration: 1s;
		-ms-animation-duration: 1s;
		-o-animation-duration: 1s;
		animation-duration: 1s;
	}
	.fadeInDown {
		-webkit-animation-name: fadeInDown;
		-moz-animation-name: fadeInDown;
		-o-animation-name: fadeInDown;
		animation-name: fadeInDown;
		visibility: visible;
	}

	.fadeOutDown {
		-webkit-animation-name: fadeOutDown;
		-moz-animation-name: fadeOutDown;
		-o-animation-name: fadeOutDown;
		animation-name: fadeOutDown;
		visibility: visible;
	}

	@-webkit-keyframes fadeInDown {
		0% {
			opacity: 0;
			-webkit-transform: translateY(-20px);
		}
		100% {
			opacity: 1;
			-webkit-transform: translateY(0);
		}
	}

	@-webkit-keyframes fadeOutDown {
		0% {
			opacity: 1;
			-webkit-transform: translateY(0);
		}
		100% {
			opacity: 0;
			-webkit-transform: translateY(20px);
		}
	}
	#news-line{
		border: 1px solid #888;
		height: 20px;
		position: relative;
	}
	#news-line > div{
		position: absolute;
		top: 0px;
	}
</style>

<div class="col-sm-12 col-md-10 hidden-xs" id="news-line">
	<div class="animated active fadeInDown">[02.02] <i><b>Новые бренды:</b></i> добавили новые бренды - <i>Arlight</i>, <i>Семь огней</i>.</div>
	<div class="animated">[02.02] <i><b>Новые позиции:</b></i>  производителей  - <i>Электростандарт</i>, <i>Werkel</i>(добавлены рамки).</div>
	<div class="animated">[02.02] <i><b>Обновление цен и прайсов:</b></i> для производителей: Elvan, Werkel, Электростандарт, Ledix.</div>		
</div>


<script type="text/javascript">
	function animatedNews(selector){
		var newsLine = $(selector+' > .animated');
		
		this.curr = null;

		if( newsLine.length < 1 )
			return;
		
		this.showNext = function(){
			if( this.curr.index()+1 >= newsLine.length ){
				this.animateItem(0);
			} else {
				this.animateItem(this.curr.index()+1);
			}
		}

		this.animateItem = function(idx){
			if( this.curr )
				this.curr.removeClass('active fadeInDown').addClass('fadeOutDown');
			
			this.curr = newsLine.eq(idx);
			this.curr.removeClass('fadeOutDown').addClass('active fadeInDown');
		}

		if( !newsLine.find('.active').length )
			this.animateItem(0);

		setInterval(this.showNext.bind(this), 3000);

		return this;
	}
	new animatedNews('#news-line');
</script>

</body>
</html>