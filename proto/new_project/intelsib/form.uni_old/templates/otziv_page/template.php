<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<form method="post" id="otzivy_page"  <?/* onsubmit="return(sendForm($(this),'#otzivy_page','/_ajax/otzivy_page.php'))"*/?>>
<div class="commentForm">
			 
				<?=bitrix_sessid_post()?>
					<?=$arResult['session_data']?>
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
        beforeSubmit:  checkForm,
	    url:"<?=$arParams['AJAX_FORM_PATH']?>",
        success:       showResponse1
    };

 	$("#otzivy_page").ajaxForm(options);

	 function showResponse1(responseText, statusText, xhr, $form)  {

	      //$form.html($(responseText).find("#"+ $form.attr("id")).html());
		  // console.log($(responseText).find("#"+ $form.attr("id")).html());
	       $("#otzivy_page").replaceWith(responseText);
		  /*if($(responseText).find("#OK_MESSAGE").length > 0 )
		  {
	      	alert('<?=$arParams['OK_TEXT']?>');
		  }*/
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

