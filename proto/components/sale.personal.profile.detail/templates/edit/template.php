<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
function printRemove($arPropVal){
	if( !$arPropVal['main']){
		return sprintf('<span class="remove_item" onclick="AniartUserPersonal.removeItem(%s);">X</span>', $arPropVal["PROFILE"]);
	}
	return false;
}
?>
<div class="iform">
	<form action="/ajax/profile.php" method="POST">
		<input type="hidden" name="ajax" value="Y">
		<?foreach ($arResult as $prop_id => $arValues):?>
			<?if(in_array($prop_id, $arParams['ID_PROPERTY_NOT_EDITABLE'])) continue;?>
			<div class="item">
				<div class="title"><?=$arValues["NAME"]?><?=($arValues["PROP_REQUIED"]=="Y")?'<span class="req">*</span>':'';?></div>
				<div class="field cell">
					<?foreach ($arValues['VALUE'] as $p_id => $value):?>
					<?$name = "ORDER_PROP_".$prop_id.'_'.$value['ID'];?>
						<input type="text" size="<?echo (IntVal($vval["SIZE1"])>0)?$vval["SIZE1"]:30; ?>" maxlength="250" value="<?echo $value['VALUE'];?>" prop="<?=$prop_id?>" name="<?=$name?>" >
						<?=printRemove($value);?><br>
					<?endforeach;?>
				</div>
				<?if( count($arValues['VALUE']) <=4 ):?>
					<div class="add-more"><a href="#">Добавить еще</a></div>
				<?endif;?>
			</div>
		<?endforeach;?>
		<input class="sendForm" type="submit" name="save" value="save">
	</form>
</div>
<script src="/bitrix/templates/plitka/js/jquery.inputmask.js" type="text/javascript" charset="utf-8"></script>
<script>
AniartUserPersonal = {
	save: function(){},				// Отправка данных на изменение добавление
	addMore: function(){},			// Добавление полей
	removeItem: function(){},		// Удаление данных
	_dataHandling: function(){},	// внутр метод - обрботка после AJAX query
	initMaskPlugin: function(){},	// Подключение плагина полей "номер телефона"
};

AniartUserPersonal._dataHandling = function(json){
	data = jQuery.parseJSON( json );
	$('.iform').html( $(data.html).html() );
	this.initMaskPlugin();
	if( data.error.length > 0 )
		alert(data.error);
	else
		alert(data.success);
}

AniartUserPersonal.addMore = function(){
	$(document).on('click', '.item > .add-more', function(){
		_jqInput = $(this).parent().find('.field.cell input:first-child');
		_jqInput.parent().append( _jqInput.clone().attr('value', '').attr('name', 'ORDER_PROP_'+_jqInput.attr('prop')+'_0[]') ).append('<br>');
		console.log( $("<input type='"+_jqInput.attr('type')+"' name='ORDER_PROP_"+_jqInput.attr('prop')+"_0[]'>") );
		AniartUserPersonal.initMaskPlugin();
		if( _jqInput.parent().find('input').length >= 5 ){
			$(this).hide();
		}
		return false;
	});
}

AniartUserPersonal.removeItem = function(id){
	var data = { method: "deleteProfile", ajax: "Y", profile:id };
	$.ajax({
		url: '/ajax/profile.php',
		type: 'POST',
		data: data,
	}).done(function(jsondata) {
		AniartUserPersonal._dataHandling(jsondata);
	});
}

AniartUserPersonal.save = function(){
	$(document).on('click', '.sendForm', function(event){
		event.preventDefault();

		_jqForm = $(this).parents('form');

		var data = _jqForm.serialize()+'&method='+$(this).attr('name');
		$.ajax({
			url: _jqForm.attr('action'),
			type: _jqForm.attr('method'),
			data: data,
		}).done(function(jsondata) {
			AniartUserPersonal._dataHandling(jsondata);
		});
	});
}

AniartUserPersonal.initMaskPlugin = function(){
	var selector = 'input[name^="ORDER_PROP_4"]';
	$(selector).inputmask("(999) 999-99-99"); //static mask
}

$(document).ready(function(){
	AniartUserPersonal.initMaskPlugin();
	AniartUserPersonal.addMore();
	AniartUserPersonal.save();
});
</script>