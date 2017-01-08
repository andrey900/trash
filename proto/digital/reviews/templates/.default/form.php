<? 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
$userName = strlen($USER->GetFirstName())>0?$USER->GetFirstName():$USER->GetLogin();

?>
<div id="reviews-reply-form">
	<div class="reviews-reply-form">
		<form action="javascript:void(null);" onsubmit="SendReviewForm($(this)); return false">
		
			<div class="results"></div>
			<input type="hidden" name="GOOD" value="<?=$_REQUEST['good']?$_REQUEST['good']:$arParams['OBJECT_ID']?>"/>
			<input type="hidden" name="REVIEW_ID" value="<?=$_REQUEST['review_id']?$_REQUEST['review_id']:''?>"/>
			<input type="hidden" name="parent" value="<?=$_REQUEST['parent']?$_REQUEST['parent']:'1'?>"/>
			<input type="hidden" name="ajax_id" value="<?=$_REQUEST['ajax_id']?$_REQUEST['ajax_id']:$arParams['AJAX_ID']?>" />
			<input type="hidden" name="IBLOCK_ID" value="<?=$_REQUEST['IBLOCK_ID']?$_REQUEST['IBLOCK_ID']:$arParams['IBLOCK_ID']?>" />
			<input type="hidden" name="page" value="<?=$_REQUEST['page']?$_REQUEST['page']:$arParams['FIRST_PAGE']?>" />
			<input type="hidden" name="city" value="<?=$_REQUEST['city']?$_REQUEST['city']:$arParams['CUR_CITY']?>" />
			<input type="hidden" name="method" value="AddReview" />	
			<?php /*?><input type="hidden" name="ajax" value="Y" /><?*/?>
			
			<div class="reviews-reply-header"><span>Ваше имя</span><span class="reviews-required-field">*</span></div>
			<div class="reviews-reply-field reviews-reply-field-text">	
				<input class="review_author" type="text" name="review_author" value="<?=$userName?>"/>
			</div>
			<div class="reviews-reply-header"><span>Ваш комментарий</span><span class="reviews-required-field">*</span></div>
			<div class="reviews-reply-field reviews-reply-field-text">
				<div style="padding-right: 23px">
					<textarea name="review_text" class="review_text"></textarea>
				</div>
			</div>
			<br />	
			<div class="reviews-reply-buttons"><input type="submit" value="Оставить отзыв"></div>
		</form>
	</div>
	<div class="clear"></div>
</div>