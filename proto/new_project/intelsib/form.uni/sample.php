для отзывов

   <?/**/

     global $filtronlyotzivy;
	 $filtronlyotzivy['PROPERTY_ELEMENT'] = $arResult['ID'];

    ?> <a name="comment"></a>
    <?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"comments",
	Array(
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "6",
		"NEWS_COUNT" => "20",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "filtronlyotzivy",
		"FIELD_CODE" => array(),
		"PROPERTY_CODE" => array(),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N"
	),
false
);?>
       <div class="wBlock1 inter1">
       <?
	   	$SECTION_ID = 29;
		$ELEMENT_ID = $arResult['ID'];
		require($_SERVER["DOCUMENT_ROOT"]."/_ajax/otzivy_page.php");
	   ?>
		</div>



--------------------- /_ajax/otzivy_page.php

 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

	$REQ_FIELDS=array(
	"Ваше имя" =>"NAME",
	"email" =>"email",
	"Отзыв" =>"otzyv",
	);

	$MAIL_FIELDS = array_merge(array_values($REQ_FIELDS), array(
	array('IBLOCK_SECTION_ID',$SECTION_ID),
   	array('ID_ELEMENT',$ELEMENT_ID),
	array('ACTIVE','N'),
	'file'
	));

	$ajaxFormPath = substr(__FILE__, strlen($_SERVER["DOCUMENT_ROOT"]));

	$APPLICATION->IncludeComponent(
		"intelsib:form.uni",
		"otziv_page",
		Array(
			"AJAX_FORM_PATH" => $ajaxFormPath,
			"USE_CAPTCHA" => "N",
			"USE_JQUERY_FORMS" => "Y",
			"EVENT_NAME" => "FEEDBACK_FORM",
			"OK_TEXT" => "Спасибо, Ваш отзыв отправлен.",
			"formname" => "comment",
			"REQUIRED_FIELDS" => $REQ_FIELDS,
			"MAIL_FIELDS" => $MAIL_FIELDS,
			"EVENT_MESSAGE_ID" => array(0=>"26",),
			"SAVE_TO_IBLOCK" => "Y",
			"IBLOCK_TYPE" => "content",
			"IBLOCK_ID" => "6",
			"IBLOCK_FIELDS_ASSOCIATHION" => array("NAME"=>"NAME","PREVIEW_TEXT"=>"otzyv",'IBLOCK_SECTION_ID'=>'IBLOCK_SECTION_ID',"ACTIVE"=>"ACTIVE"),
			"IBLOCK_PROPERTYES_ASSOCIATION" => array("20"=>"email",'21'=>'ID_ELEMENT','27'=>'file')
		)
	);
 ?>

 ------------------------ шаблон!! --------------------------------

 <?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?=bitrix_sessid_post()?>
<?=$arResult['data']?>
<?
if($arResult["OK_MESSAGE"])
{ ?>
	<script type="text/javascript">
	alert('<?=$arParams['OK_TEXT']?>');
	</script>
<?}?>

<?if(!empty($arResult["ERROR_MESSAGE"]))
{
	echo  '<font color="red">';
	foreach($arResult["ERROR_MESSAGE"] as $v)
	echo $v.'<br />';
	echo  '</font><br />';
}
?>

  <table width="100%" cellspacing="0" cellpadding="0" border="0" class="itemEstimateBlock">
		<tbody><tr>
			<td>
				<input type="hidden" value="1" name="estimate_form_active">
				<input type="hidden" value="Москва" id="city_2" name="city">
				<input type="hidden" value="1" name="submit">
				
			</td>
		</tr>
		<tr>
			<td style="padding: 0px 10px 0px 10px;">

						<table cellspacing="0" cellpadding="3" border="0" class="itemEstimateForm">
					<tbody><tr>
						<td style="font-size: 14px;" class="iefLeft1" colspan="3">Оставьте свой отзыв:</td>
					</tr>
					<tr>
						<td class="iefLeft1">Имя</td>
						<td><input type="text" value="<?=$arResult['username']?>" id="username" name="username" style="width: 150px;"></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="iefLeft1">Оценка</td>
						<td colspan="2">
							<table cellspacing="0" cellpadding="0">
								<tbody><tr>
								<td valign="bottom" nowrap="">
									<input<?=(empty($arResult['usermark']))?' selected="selected" ':''?>  type="radio" style="border: 0px;" value="0" name="usermark" id="usermark_0">не&nbsp;голосую
																											</td><td valign="bottom" nowrap="" align="center" style="padding-left: 5px; font-weight: bold;">
										<span onclick="this.parentNode.getElementsByTagName('input')[0].click();">
										<input<?=($arResult['usermark']==1)?' selected="selected" ':''?> type="radio" style="border-style: none !important;" value="1" name="usermark" id="usermark_1">1
										</span>
									</td>
																											<td valign="bottom" nowrap="" align="center" style="padding-left: 5px; font-weight: bold;">
										<span onclick="this.parentNode.getElementsByTagName('input')[0].click();">
										<input<?=($arResult['usermark']==2)?' selected="selected" ':''?>  type="radio" style="border-style: none !important;" value="2" name="usermark" id="usermark_2">2
										</span>
									</td>
																											<td valign="bottom" nowrap="" align="center" style="padding-left: 5px; font-weight: bold;">
										<span onclick="this.parentNode.getElementsByTagName('input')[0].click();">
										<input<?=($arResult['usermark']==3)?' selected="selected" ':''?>  type="radio" style="border-style: none !important;" value="3" name="usermark" id="usermark_3">3
										</span>
									</td>
																											<td valign="bottom" nowrap="" align="center" style="padding-left: 5px; font-weight: bold;">
										<span onclick="this.parentNode.getElementsByTagName('input')[0].click();">
										<input<?=($arResult['usermark']==4)?' selected="selected" ':''?>  type="radio" style="border-style: none !important;" value="4" name="usermark" id="usermark_4">4
										</span>
									</td>
																											<td valign="bottom" nowrap="" align="center" style="padding-left: 5px; font-weight: bold;">
										<span onclick="this.parentNode.getElementsByTagName('input')[0].click();">
										<input<?=($arResult['usermark']==5)?' selected="selected" ':''?>  type="radio" style="border-style: none !important;" value="5" name="usermark" id="usermark_5">5
										</span>
									</td>
																	</tr>
							</tbody></table>
						</td>
					</tr>
					<tr>
						<td class="iefLeft1">E-mail</td>
						<td style="width: 160px;">
							<input type="text" value="<?=$arResult['username']?>"  id="usermail" name="usermail" style="width: 150px;">
						</td>
						<td>
							<input type="checkbox" style="border: 0px;" value="1" <?=($arResult['user_showmail']==1)?' checked="checked" ':''?>name="user_showmail"> Показать всем
						</td>
					</tr>

					<tr>
						<td valign="top" class="iefLeft1">Отзыв</td>
						<td style="padding-right: 20px;" colspan="2">
						<textarea style="width: 100%; height: 85px;" cols="50" rows="3" id="comment" name="comment"><?=$arResult['comment']?></textarea></td>
					</tr>
					<?if($arParams["USE_CAPTCHA"] == "Y" ){?>
					<tr>
						<td class="iefLeft1">Введите код</td>
						<td colspan="2">
							<input type="text" style="width: 120px;" value="" name="captcha_word">
							&nbsp;
						   	<input class="captcha_sid" type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
					       	<img class="imgcapcha" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="133" height="50" alt="CAPTCHA">
                           	&nbsp;<a class="link-dark obj_update" href="#0">Показать другое число</a>
						</td>
					</tr>
					<?}?>
					<tr>
						<td style="padding: 10px 0px 10px 94px;" colspan="3">
							<script type="text/javascript" language="javascript">image_preload('/i/design4/btn_send_estimate_hover.png');</script>
							<img style="border: 0px; cursor: pointer;" onclick="<?/*check_comment();*/?>$('#estimate_form').submit()" onmouseout="this.src='/i/design4/btn_send_estimate.png';" onmouseover="this.src='/i/design4/btn_send_estimate_hover.png';" src="/i/design4/btn_send_estimate.png" alt="">
                             <input name="ddddd" type="submit" value="ddd">
						</td>
					</tr>
				</tbody></table>
		  </td>
		</tr>
    </tbody></table>

<script type="text/javascript">
$(document).ready(function(){

	var options = {
        beforeSubmit:  check_comment,
	    url:"<?=$arParams['AJAX_FORM_PATH']?>",
        success:       showResponse1
    };

 	$("#estimate_form").ajaxForm(options);

	function showResponse1(responseText, statusText, xhr, $form)  {
	       $("#estimate_form").html(responseText);
	}

   $(".obj_update").live('click',function(){
       var form = $(this).parents("form");
	   var dataForm={newcapcha:'Y'};
	   $.post(
	   "<?=$arParams['AJAX_FORM_PATH']?>",
	   dataForm,
	   function(data)
	   {
	  	   //	console.log(data);
		   	if(data.length > 0)
			{
		       $(form).find(".captcha_sid").val(data);
			   $(form).find(".imgcapcha").attr("src",'/bitrix/tools/captcha.php?captcha_sid='+data);
			}
	   },'html'
	   );
	});

	function check_comment() {
	alert_mes="";
	if ($('#comment').value=='')
	{
		alert_mes += "Поле комментарий не заполнено!\n";
	}
	if ($('#username').value=='')
	{
		alert_mes += "Поле Имя не заполнено!\n";
	}

	if ($('#usermail').value=='')
	{
		alert_mes += "Поле E-mail не заполнено!\n";
	}

	if ($('input[name="captcha_word"]').length > 0 && $('input[name="captcha_word"]').value=='')
	{
		alert_mes += "Поле проверочного кода не заполнено!\n";
	}

	if (alert_mes!='')
	{
		alert(alert_mes); return false
	}
	else
	{
		//check_ajax();
	   	//document.getElementById('estimate_form').submit();
		return true;
	}
}

});


</script>

<?/*
<form method="post" id="otzivy_page"  <?/* onsubmit="return(sendForm($(this),'#otzivy_page','/_ajax/otzivy_page.php'))"*//*?>>
<div class="commentForm">

				<?=bitrix_sessid_post()?>
					<?=$arResult['data']?>
					<?
						if($arResult["OK_MESSAGE"])
						{ ?>
						     	<script type="text/javascript">
									alert('<?=$arParams['OK_TEXT']?>');
						    	</script>
					   <?}?>
					<?if(!empty($arResult["ERROR_MESSAGE"]))
					{   echo  '<font color="red">';
							foreach($arResult["ERROR_MESSAGE"] as $v)
								echo $v.'<br />';
						 echo  '</font><br />';
					}
					?>
					<div class="left">
							<textarea class="txtar" placeholder="Ваше отзыв" name="otzyv"><?=$arResult['otzyv']?></textarea>
					</div>
					<div class="right">
						<div class="row">
							 <input type="text"  class="inp" name="NAME" value="<?=$arResult['NAME']?>" placeholder="Ваше имя"/>
						</div>
						<div class="row">
							<input type="submit" value="Отправить отзыв" class="sub">
							<input type="hidden" name="submit" value="Оставить комментарий"/>
							<input type="hidden" name="formname" value="<?=$arParams['formname']?>"/>
						</div>
					</div>
			</div>
<script type="text/javascript">
$(document).ready(function(){
	var options = {
        /*beforeSubmit:  checkForm,*//*
	    url:"<?=$arParams['AJAX_FORM_PATH']?>",
        success:       showResponse1
    };

 	$(".otzivy_page").ajaxForm(options);

	 function showResponse1(responseText, statusText, xhr, $form)  {
	       $(".otzivy_page").html(responseText);
	}

});
</script>

</form>
   <?/*
	<div class="title">Заказать звонок</div>
	<p>Укажите ваш номер телефона<br> и удобное время для звонка.<br> Наш менеджер свяжется с вами.</p>
	<div class="formBlock">
	   <form method="post" id="zvonok" onsubmit="return(sendForm($(this),'#call','/_ajax/zvonok.php'))">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="formname" value="zvonok">
			<label>
				<input class="inp" name="tel" placeholder="Телефон" type="text">
			</label>
			<div class="row">
				<div class="left">c <input class="inp wid2" name="h1" type="text" maxlength="2"> : <input maxlength="2" class="inp wid2" name="m1" type="text"></div>
				<div class="right">до <input class="inp wid2" name="h2" type="text"maxlength="2"> : <input maxlength="2" name="m2" class="inp wid2" type="text"></div>
			</div>
			<div class="button">
				<span>Заказать</span>
				<input name="submit1" class="sub" value="Заказать" type="submit">
				<input name="submit" class="sub" value="Заказать" type="hidden">
			</div>
		</form>
	</div>



<script type="text/javascript">
function sendForm(obj,repclock,action)
{

        var dataForm=obj.serializeArray();
        $.post(
            action,
            dataForm,
            function(data)
            {
                $(repclock).html(data);
            }
        );
return false;
}
</script>

<form method="post" id="zvonok" onsubmit="return(sendForm($(this),'/_ajax/sendCarBuy.php?id=1047'))">


</form>


<label>Имя <span class="red">*</span></label> <input class="text" type="text" name="fio" value="<?=htmlspecialchars($_POST['fio'])?>" />
<label>Е-mail<span class="red">*</span></label> <input class="text" type="text" name="email" value="<?=htmlspecialchars($_POST['email'])?>" />
<label>Телефон<span class="red">*</span></label> <input class="text" type="text" name="telefon" value="<?=htmlspecialchars($_POST['telefon'])?>" />
<label>Сообщение<span class="red">*</span></label> <textarea name="mess"><?=htmlspecialchars($_POST['mess'])?></textarea>
<input class="sbm" id="sendSv" type="submit" name="send" value="Отправить" onclick="sendForm(); return false;"/>
<input type="hidden" name="submit" value="1" />

</form>*/?>


