<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?
	if($arResult["OK_MESSAGE"])
	{echo '<pre>'.print_r($arResult,1).'</pre>'.__FILE__.' # '.__LINE__;    ?>
    	<script type="text/javascript">
			alert('Ваша заявка отправлена');
    	</script>
	<?}

?>

<?if(!empty($arResult["ERROR_MESSAGE"]))
{   echo  '<font color="red">';
		foreach($arResult["ERROR_MESSAGE"] as $v)
			echo $v.'<br />';
	 echo  '</font><br />';  echo '<pre>'.print_r($arResult,1).'</pre>'.__FILE__.' # '.__LINE__;
}
echo '<pre>'.print_r($_FILES,1).'</pre>'.__FILE__.' # '.__LINE__;
?>

	<div class="title">Заказать звонок</div>
	<p>Укажите ваш номер телефона<br> и удобное время для звонка.<br> Наш менеджер свяжется с вами.</p>
	<div class="formBlock">
	   <form method="post" id="zvonok" action="/test1.php" <?/*onsubmit="return(sendForm($(this),'#call','/_ajax/zvonok.php'))"*/?>>
			<?=bitrix_sessid_post()?>

			<label>
				<input class="inp" name="tel" placeholder="Телефон" type="text">
			</label>
			<div class="row">
				<div class="left">c <input class="inp wid2" name="h1" type="text" maxlength="2"> : <input maxlength="2" class="inp wid2" name="m1" type="text"></div>
				<div class="right">до <input class="inp wid2" name="h2" type="text"maxlength="2"> : <input maxlength="2" name="m2" class="inp wid2" type="text"></div>
			</div>
			<div class="button">
				<span>Заказать</span>
				<input name="file" type="file" value="ava_4.png">
				<input name="submit" class="sub" value="Заказать" type="submit">
				<?/*<input name="submit" class="sub" value="Заказать" type="hidden">   */?>
			</div>
		</form>
	</div>

<script type="text/javascript">
$(document).ready(function(){
var options = {
       // target:        '#zvonok',   // target element(s) to be updated with server response
        //replaceTarget:  true,
        //beforeSubmit:  showRequest,  // pre-submit callback
        success:       showResponse  // post-submit callback

        // other available options:
        //url:       url         // override for form's 'action' attribute
        //type:      type        // 'get' or 'post', override for form's 'method' attribute
        //dataType:  null        // 'xml', 'script', or 'json' (expected server response type)
        //clearForm: true        // clear all form fields after successful submit
        //resetForm: true        // reset the form after successful submit

        // $.ajax options can be used here too, for example:
        //timeout:   3000
    };

 $("#zvonok").ajaxForm(options);
	});


	function showRequest(formData, jqForm, options) {
    // formData is an array; here we use $.param to convert it to a string to display it
    // but the form plugin does this for you automatically when it submits the data
    var queryString = $.param(formData);

    // jqForm is a jQuery object encapsulating the form element.  To access the
    // DOM element for the form do this:
    // var formElement = jqForm[0];

    alert('About to submit: \n\n' + queryString);

    // here we could return false to prevent the form from being submitted;
    // returning anything other than false will allow the form submit to continue
    return true;
}

// post-submit callback
function showResponse(responseText, statusText, xhr, $form)  {
var dom = $(responseText);
      console.log(dom.find('div').html());
      console.log(responseText);
    // for normal html responses, the first argument to the success callback
    // is the XMLHttpRequest object's responseText property

    // if the ajaxForm method was passed an Options Object with the dataType
    // property set to 'xml' then the first argument to the success callback
    // is the XMLHttpRequest object's responseXML property

    // if the ajaxForm method was passed an Options Object with the dataType
    // property set to 'json' then the first argument to the success callback
    // is the json data object returned by the server

    alert('status: ' + statusText + '\n\nresponseText: \n' + responseText +
        '\n\nThe output div should have already been updated with the responseText.');
}
	</script>

<?
return;
?>
<?/*

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

