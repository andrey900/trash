<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?><div class="address-section mb-80">
	<div class="container">
		<div class="row">
			<div class="col-sm-4 col-xs-12">
				<div class="contact-address box-shadow">
 <i class="zmdi zmdi-pin"></i>
					<h6>ул. Уручская 19, павильон 3Б</h6>
					<h6>г. Минск, Беларусь</h6>
				</div>
			</div>
			<div class="col-sm-4 col-xs-12">
				<div class="contact-address box-shadow">
 <i class="zmdi zmdi-phone"></i>
					<h6>+375(29)117-74-33</h6>
				</div>
			</div>
			<div class="col-sm-4 col-xs-12">
				<div class="contact-address box-shadow">
 <i class="zmdi zmdi-email"></i>
					<h6><a href="mailto:sale@loftsvet.by">sale@loftsvet.by</a></h6>
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- GOOGLE MAP SECTION START -->
<div class="google-map-section">
	<div class="container-fluid">
		<div class="google-map plr-185">
			<div id="googleMap">
			</div>
		</div>
	</div>
</div>
 <!-- GOOGLE MAP SECTION END -->
<div class="message-box-section mt--50 mb-80">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="message-box box-shadow white-bg">
					<p class="danger error-msg">
					</p>
					<form id="contact-form" method="post">
						<div class="row">
							<div class="col-md-12">
								<h4 class="blog-section-title border-left mb-30">Напишите нам</h4>
							</div>
							<div class="col-md-6">
 <input class="input-field" name="name" placeholder="Введите ваше имя" type="text">
							</div>
							<div class="col-md-6">
 <input class="input-field" name="email" placeholder="Введите ваше email" type="text">
							</div>
							<div class="col-md-6">
 <input class="input-field" name="subject" placeholder="Введите тему сообщения" type="text">
							</div>
							<div class="col-md-6">
 <input class="input-field" name="phone" placeholder="Введите ваше телефон" type="text">
							</div>
							<div class="col-md-12">
 <textarea class="custom-textarea input-field" name="message" placeholder="Сообщение"></textarea> <button class="submit-btn-1 send-feedback mt-30 btn-hover-1" type="submit">Отправить сообщение</button>
							</div>
						</div>
					</form>
					<p class="form-message hidden">
						 Спасибо ваше сообщение принято.
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBuU_0_uLMnFM-2oWod_fzC0atPZj7dHlU"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/map.js"></script>
<script type="text/javascript">
Studio8.Widget('feedbackForm', {
	documentReady: true,
	ajaxFeedbackSend: false,
	init: function(){
		this.eventsInit()
	},
	eventsInit: function(){
		var self = this;
		$(document).on('click', '.send-feedback', function(){
			var trigError = false;
			var form = $(this).closest('form');
			$('.input-field').each(function(){
				if( !$(this).val() ){
					$(this).addClass('field-error');
					trigError = true;
				}
			});
			if( !trigError && !self.ajaxFeedbackSend ){
				self.ajaxFeedbackSend = true;
				$.ajax({
					url: '/ajax/feedback/',
					method: 'post',
					dataType: 'json',
					data: form.serialize()
				}).done(function(data){
					if( data.status == 'success' ){
						form.hide();
						$('.form-message.hidden').removeClass('hidden');
					} else {
						$('.error-msg').text(data.msg);
					}
				}).always(function(){
					self.ajaxFeedbackSend = false;
				});
			}
			return false;
		});
	}
});
</script><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>