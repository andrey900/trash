<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$exReviewsDir = explode($_SERVER["DOCUMENT_ROOT"], __DIR__);
?>
<script>
	var arParams = <? echo CUtil::PhpToJSObject($arParams, false, true);?>;
	var CurComponentTemplateDir = "<?=end($exReviewsDir)?>";
</script>

<?
function BuildTree($Items, $arParams, $parent){	
	if(count($Items)>0){
		foreach($Items as $i => $arItem){
			?>
			<li style="margin-left: <?=$arItem['LEFT_MARGIN']?>px;">
				<div class="review_body <? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_P'){?>review_body_moderation<? }?> 
				<? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_S'){?>review_body_hide<? }?>">
					<div class="reviews_list_header">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr valign="middle">
								<td>
									<div><b><?=$arItem['NAME']?></b> <span><?=$arItem['DISPLAY_ACTIVE_FROM']?></span></div>
								</td>
								<? if($arParams['USER_ID']>0){?>
								<td width="100px" align="right">
									<div>
										<span id="rating-vote-<?=htmlspecialcharsbx($arItem['ID'])?>" class="rating-vote">
											<span class="rating-vote-result-plus"><?=$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']:0?></span>
											<a href="javascript:void(0)" class="rating-vote-plus <?if(in_array($arParams['USER_ID'], $arItem['PROPERTIES']['LIKE_USERS']['VALUE'])){?> rating-vote-plus-active<?}?>" onclick="PlusClickReview(this, '<?=$arItem['ID']?>')"></a>
											&nbsp;
											<a href="javascript:void(0)" class="rating-vote-minus <?if(in_array($arParams['USER_ID'], $arItem['PROPERTIES']['DISLIKE_USERS']['VALUE'])){?> rating-vote-minus-active<?}?>" onclick="MinusClickReview(this, '<?=$arItem['ID']?>')"></a>
											<span class="rating-vote-result-minus"><?=$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']:0?></span>
										</span>
									</div>
								</td>
								<? }else{?>
									<td width="120px" align="right">
										<div>
											<span id="preview_rating-vote-<?=htmlspecialcharsbx($arItem['ID'])?>" class="rating-vote">
												Отзыв полезен: 
												<span class="rating-vote-result-plus"><?=$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']:0?></span>
												/
												<span class="rating-vote-result-minus"><?=$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']:0?></span>
											</span>
										</div>
									</td>
								<? }?>
							</tr>
						</table>
					</div>
					<div><?=$arItem['DETAIL_TEXT']?></div>
					<div class="reviews_list_links">
						<? if($arParams['USER_ID']>0){?>
							<a parent="<?=$parent?>" good="<?=$arParams['OBJECT_ID']?>" review_id="<?=$arItem['ID']?>" href="javascript:void(0)" class="leave_review" onclick="showReviewForm(this);">Ответить</a> 
							<? if($arParams["CAN_USER_DEL_REVIEW"] == "Y"){?>
								<? if($arItem['PROPERTIES']['USER_ID']['VALUE'] == $arParams['USER_ID']){?>
									<a parent="<?=$parent?>" href="javascript:void(0)" onclick="DeleteReview(this, '<?=$arItem['ID']?>')">Удалить</a>
								<? }?>
							<? }?>
						<? }?>
						<? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_P'){?>
							<span class="review_moderation">Отзыв находится на модерации</span>
						<? }?>
						<? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_S'){?>
							<span class="review_hide">Отзыв скрыт</span>
						<? }?>
					</div>
					
				</div>
				<? if($arItem['PROPERTIES']['ANSWER']['VALUE']['TEXT']){?>
						<div class="review_answer">
							<b class="review_answer_head"><?=$arItem['PROPERTIES']['ANSWER']['NAME']?></b>
							<div class="review_answer_text"><?=$arItem['PROPERTIES']['ANSWER']['~VALUE']['TEXT']?></div>
						</div>
					<? }?>
			</li>
			<?
			if(count($arItem['SUB_ITEMS'])>0){
				BuildTree($arItem['SUB_ITEMS'], $arParams, $parent);
			}
		}
	}
}
?>


	<div id="response_body">
		<h2>Отзывы</h2>
		<br />
		<? if($arParams['USER_ID']>0){?>
			
			<div class="first_leave_review" style="display:none">
				<a parent="1" good="<?=$arParams['OBJECT_ID']?>" href="javascript:void(0)" class="leave_review" onclick="showReviewForm(this);">Оставить отзыв</a>
			</div>
			<? include(__DIR__.'/form.php');?>
				
		<? }?>
		
		<div id="response_reviews"></div>
		
		<? if(count($arResult["ITEMS"])>0){?>
			<?//p($arResult["ITEMS"])?>
			<ul class="reviews_list">
				<? foreach($arResult["ITEMS"] as $arItem){?>
					<li style="margin-left: <?=$arItem['LEFT_MARGIN']?>px;">
						<div class="review_body <? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_P'){?>review_body_moderation<? }?>
						<? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_S'){?>review_body_hide<? }?>">
							<div class="reviews_list_header">
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
									<tr valign="middle">
										<td>
											<div><b><?=$arItem['NAME']?></b> <span><?=$arItem['DISPLAY_ACTIVE_FROM']?></span></div>
										</td>
										<? if($arParams['USER_ID']>0){?>
										<td width="100px" align="right">
											<div>
												<span id="rating-vote-<?=htmlspecialcharsbx($arItem['ID'])?>" class="rating-vote">
													<span class="rating-vote-result-plus"><?=$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']:0?></span>
													<a href="javascript:void(0)" class="rating-vote-plus <?if(in_array($arParams['USER_ID'], $arItem['PROPERTIES']['LIKE_USERS']['VALUE'])){?> rating-vote-plus-active<?}?>" onclick="PlusClickReview(this, '<?=$arItem['ID']?>')"></a>
													&nbsp;
													<a href="javascript:void(0)" class="rating-vote-minus <?if(in_array($arParams['USER_ID'], $arItem['PROPERTIES']['DISLIKE_USERS']['VALUE'])){?> rating-vote-minus-active<?}?>" onclick="MinusClickReview(this, '<?=$arItem['ID']?>')"></a>
													<span class="rating-vote-result-minus"><?=$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']:0?></span>
												</span>
											</div>
										</td>
										<? }else{?>
											<td width="120px" align="right">
												<div>
													<span id="preview_rating-vote-<?=htmlspecialcharsbx($arItem['ID'])?>" class="rating-vote">
														Отзыв полезен: 
														<span class="rating-vote-result-plus"><?=$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']:0?></span>
														/
														<span class="rating-vote-result-minus"><?=$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']:0?></span>
													</span>
												</div>
											</td>
										<? }?>
									</tr>
								</table>
							</div>
							<div><?=$arItem['DETAIL_TEXT']?></div>
							<div class="reviews_list_links">
								<? if($arParams['USER_ID']>0){?>
									<a parent="<?=$arItem['ID']?>" good="<?=$arParams['OBJECT_ID']?>" review_id="<?=$arItem['ID']?>" href="javascript:void(0)" class="leave_review" onclick="showReviewForm(this);">Ответить</a> 
									<? if($arParams["CAN_USER_DEL_REVIEW"] == "Y"){?>
										<? if($arItem['PROPERTIES']['USER_ID']['VALUE'] == $arParams['USER_ID']){?>
											<a href="javascript:void(0)" onclick="DeleteReview(this, '<?=$arItem['ID']?>')">Удалить</a>
										<? }?>
									<? }?>
								<? }?>
								<? if(count($arItem['SUB_ITEMS'])>0){?>
									<a id="reviews_answers_block_link_<?=$arItem['ID']?>" class="reviews_answers_block_link" href="javascript:void(0)" onclick="ReviewsShowHide(this, 'reviews_sub_items_<?=$arItem['ID']?>')"><span>Все ответы</span> (<?=$arItem["SUB_ITEMS_COUNT"]?>)</a>
								<? }?>
								<? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_P'){?>
									<span class="review_moderation">Отзыв находится на модерации</span>
								<? }?>
								<? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_S'){?>
									<span class="review_hide">Отзыв скрыт</span>
								<? }?>
							</div>
							
						</div>
						<? if($arItem['PROPERTIES']['ANSWER']['VALUE']['TEXT']){?>
								<div class="review_answer">
									<b class="review_answer_head"><?=$arItem['PROPERTIES']['ANSWER']['NAME']?></b>
									<div class="review_answer_text"><?=$arItem['PROPERTIES']['ANSWER']['~VALUE']['TEXT']?></div>
								</div>
							<? }?>
						
						<? if(count($arItem['SUB_ITEMS'])>0){?>
							<div id="reviews_sub_items_<?=$arItem['ID']?>" class="hide">
								<ul>
									<?=BuildTree($arItem['SUB_ITEMS'], $arParams, $arItem['ID'])?>
								</ul>
							</div>
						<? }?>
						
					</li>
				<?}?>
			</ul>
			
			<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
				<br /><?=$arResult["NAV_STRING"]?>
			<?endif;?>
			<?if($_REQUEST['parent']){?>
			<script>
				if($('#reviews_answers_block_link_'+<?=$_REQUEST['parent']?>).length){
					$('#reviews_answers_block_link_'+<?=$_REQUEST['parent']?>).click();
				}
			</script>
		<? }?>
		
	<? }?>
</div>
