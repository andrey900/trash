<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?CJSCore::Init(array('fx', 'popup', 'window', 'ajax'));?>
<script>
	$(document).ready(function()
	{ 	
		var emailInpuit = $("#order_form_content input.email").attr("name");
		if (emailInpuit)
		{
			$("form#ORDER_FORM").validate(
			{
				rules:{
				  emailInpuit: 
				  {
					required: true,
					email: true
				  }
				}		
			});
		}		
		$('input.phoneMask').mask('<?=trim(COption::GetOptionString("aspro.kshop", "PHONE_MASK", "+38(999) 999-9999", SITE_ID));?>'); 
	});
</script>

<a name="order_form"></a>

<div id="order_form_div" class="order-checkout">
<NOSCRIPT>
	<div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
</NOSCRIPT>

<?
//p($arResult);
if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
{
	if(!empty($arResult["ERROR"])) { foreach($arResult["ERROR"] as $v) echo ShowError($v); }
	elseif(!empty($arResult["OK_MESSAGE"])) { foreach($arResult["OK_MESSAGE"] as $v) echo ShowNote($v); }
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
}
else
{
	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
	{
		if(strlen($arResult["REDIRECT_URL"]) > 0)
		{
			?>
			<script type="text/javascript">
			window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
			</script>
			<?
			die();
		}
		else { include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php"); }
	}
	else
	{
		?>
                <script type="text/javascript">
            $(document).on('click', '.city-pre input', function(){
				$('#ORDER_PROP_6').val( $(this).val() );
				$('#ORDER_PROP_6_val').val( $('label[for=ID_CITY_'+$(this).val()+'] span:first').text() + ', Украина');
				submitForm();
				//$(this).parents('form').submit();
			});
                var BXFormPosting = false;
                function submitForm(val)
                {
                        if (BXFormPosting === true)
                                return true;

                        BXFormPosting = true;
                        if(val != 'Y')
                                BX('confirmorder').value = 'N';

                        var orderForm = BX('ORDER_FORM');
                        BX.showWait();

                        BX.ajax.submit(orderForm, ajaxResult);

                        return true;
                }

                function ajaxResult(res)
                {
                        try
                        {
                                var json = JSON.parse(res);
                                BX.closeWait();

                                if (json.error)
                                {
                                        BXFormPosting = false;
                                        return;
                                }
                                else if (json.redirect)
                                {
                                        window.top.location.href = json.redirect;
                                }
                        }
                        catch (e)
                        {
                                BXFormPosting = false;
                                BX('order_form_content').innerHTML = res;
                        }
                        
			/*$(document).on('click', '.city-pre input', function(){
				$('#ORDER_PROP_6').val( $(this).val() );
				$('#ORDER_PROP_6_val').val( $('label[for=ID_CITY_'+$(this).val()+'] span:first').text() + ', Украина');
				submitForm();
				//$(this).parents('form').submit();
			});*/
			
                        if($('#deliveri_storage option:selected')) {
                            $('#ORDER_PROP_8').val($('#deliveri_storage option:selected').text());
                            $('#ORDER_PROP_9').val($('#deliveri_storage option:selected').val());
                        }
                        
                        var step_order = $.cookie('order_step');
                        if(step_order == '1'){
                            $('#order_form_first_block').show();
                            $('#order_form_second_block').hide();
                        } else if(step_order == '2') {
                            var name_ord = $('#ORDER_PROP_1').val();
                            var city_ord = $('#ORDER_PROP_6_val').val();
                            var phone_ord = $('#ORDER_PROP_3').val();
                            var email_ord = $('#ORDER_PROP_2').val();
                            var pattern_ord = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
                            if(name_ord.length > 0 && phone_ord.length > 0 && city_ord.length > 0 && pattern_ord.test(email_ord)) {
                                $('#order_form_first_block').hide();
                                $('#order_form_second_block').show();
                                $('#place_order').show();
                            }

                        }
                        
                        $('input.phoneMask').mask('<?=trim(COption::GetOptionString("aspro.kshop", "PHONE_MASK", "+38(999) 999-9999", SITE_ID));?>'); 

                        BX.closeWait();
                }

                function SetContact(profileId)
                {
                        BX("profile_change").value = "Y";
                        submitForm();
                }
                </script>
		<?if($_POST["is_ajax_post"] != "Y")
		{
			?><form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" id="ORDER_FORM">
			<?=bitrix_sessid_post()?>
			<div class="order-form-content" id="order_form_content">
			<?
		}
		else
		{
			$APPLICATION->RestartBuffer();
		}
		if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
		{
			foreach($arResult["ERROR"] as $v)
				echo ShowError($v);

			?>
			<script type="text/javascript">
				top.BX.scrollToNode(top.BX('ORDER_FORM'));
			</script>
			<?
		}
                ?>
                        <div class="order-form-content-left">
                            <!-- step 1 -->
                            <div id="order_form_first_block" class="order-form-first-block">
                <?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/person_type.php");
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");
                ?>
                                <div class="step-next-block">
                                    <button id="second_step_order" class="button30" type="button" value="Далее">
                                        <span>Далее</span>
                                    </button>
                                </div>
                                <div class="title indent-proto">
                                    <div class="title-first-block-de">2</div>
                                    <div class="title-first-text"><?=GetMessage("ANI_DELIVERY_PAYMENT")?></div>
                                </div>
                            </div>
                            <!-- / step 1 -->
                            
                            <!-- step 2 -->
                            <div id="order_form_second_block" class="order-form-second-block">
                                <div class="title">
                                    <div class="title-first-block">1</div>
                                    <div class="title-first-text">
                                        <?=GetMessage("SOA_TEMPL_PROP_INFO")?>
                                        <span id="first_step_order">редактировать</span>
                                    </div> 
                                </div>
                                <div class="title indent-proto">
                                    <div class="title-first-block">2</div>
                                    <div class="title-first-text"><?=GetMessage("ANI_DELIVERY_PAYMENT")?></div>
                                </div>
                                <div class="block-spet-two">
                <?
		if ($arParams["DELIVERY_TO_PAYSYSTEM"] == "p2d")
		{
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery_new.php");
		}
		else
		{
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery_new.php");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");
		}
                ?>
                                </div>
                            </div>
                            <!-- / step 2 -->
                        </div>
                        <div class="order-form-content-right">
                <?
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");
                ?>
                        </div>          
                <?
		if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0)
			echo $arResult["PREPAY_ADIT_FIELDS"];
		?>
		<?if($_POST["is_ajax_post"] != "Y")
		{
			?>
				</div>
				<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
				<input type="hidden" name="profile_change" id="profile_change" value="N">
				<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
				
				<br /><br /><br />
			</form>

			<?if($arParams["DELIVERY_NO_AJAX"] == "N"):?>
				<script type="text/javascript" src="/bitrix/js/main/cphttprequest.js"></script>
				<script type="text/javascript" src="/bitrix/components/bitrix/sale.ajax.delivery.calculator/templates/.default/proceed.js"></script>
			<?endif;?>
			<?
		}
		else
		{
			?>
			<script type="text/javascript">
				top.BX('confirmorder').value = 'Y';
				top.BX('profile_change').value = 'N';
			</script>
			<?
			die();
		}

	}
}
?>
</div>