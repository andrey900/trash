<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$exReviewsDir = explode($_SERVER["DOCUMENT_ROOT"], __DIR__);
?>
<script>
	var arParams = <? echo CUtil::PhpToJSObject($arParams, false, true);?>;
	var CurComponentTemplateDir = "<?=end($exReviewsDir)?>";
</script>

<?
function BuildTreePreview($Items, $arParams, $parent){	
	if(count($Items)>0){
		foreach($Items as $i => $arItem){
			?>
			<li style="margin-left: <?=$arItem['LEFT_MARGIN']?>px;">
				<div class="review_body <? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_P'){?>review_body_moderation<? }?>">
					<div class="reviews_list_header">
						<table cellpadding="0" cellspacing="0" width="100%" border="0">
							<tr valign="middle">
								<td>
									<div><b><?=$arItem['NAME']?></b> <span><?=$arItem['DISPLAY_ACTIVE_FROM']?></span></div>
								</td>
								<? /*if($arParams['USER_ID']>0){?>
									<td width="100px" align="right">
										<div>
											<span id="preview_rating-vote-<?=htmlspecialcharsbx($arItem['ID'])?>" class="rating-vote">
												<span class="rating-vote-result-plus"><?=$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']:0?></span>
												<a href="javascript:void(0)" class="rating-vote-plus <?if(in_array($arParams['USER_ID'], $arItem['PROPERTIES']['LIKE_USERS']['VALUE'])){?> rating-vote-plus-active<?}?>" onclick="PlusClickPreviewReview(this, '<?=$arItem['ID']?>')"></a>
												&nbsp;
												<a href="javascript:void(0)" class="rating-vote-minus <?if(in_array($arParams['USER_ID'], $arItem['PROPERTIES']['DISLIKE_USERS']['VALUE'])){?> rating-vote-minus-active<?}?>" onclick="MinusClickPreviewReview(this, '<?=$arItem['ID']?>')"></a>
												<span class="rating-vote-result-minus"><?=$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']:0?></span>
											</span>
										</div>
									</td>
								<? }*/?>
								<? if(!$arParams['USER_ID']){?>
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
					<div class="reviews_list_links" style="text-align: right">
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
				BuildTreePreview($arItem['SUB_ITEMS'], $arParams, $parent);
			}
		}
	}
}
?>
<? if(count($arResult["ITEMS"])>0){?>
	<div class="small_block">
		<div class="small_block_head">Отзывы</div>
		<div class="description">
			<div id="preview_response_body">
			<ul class="reviews_list">
				<? foreach($arResult["ITEMS"] as $arItem){?>
					<li style="margin-left: <?=$arItem['LEFT_MARGIN']?>px;">
						<div class="review_body <? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_P'){?>review_body_moderation<? }?>">
							<div class="reviews_list_header">
								<table cellpadding="0" cellspacing="0" width="100%" border="0">
									<tr valign="middle">
										<td>
											<div><b><?=$arItem['NAME']?></b> <span><?=$arItem['DISPLAY_ACTIVE_FROM']?></span></div>
										</td>
										<? if(!$arParams['USER_ID']){?>
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
										<? /*if($arParams['USER_ID']>0){?>
											<td width="100px" align="right">
												<div>
													<span id="preview_rating-vote-<?=htmlspecialcharsbx($arItem['ID'])?>" class="rating-vote">
														<span class="rating-vote-result-plus"><?=$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['LIKE_COUNTER']['VALUE']:0?></span>
														<a href="javascript:void(0)" class="rating-vote-plus <?if(in_array($arParams['USER_ID'], $arItem['PROPERTIES']['LIKE_USERS']['VALUE'])){?> rating-vote-plus-active<?}?>" onclick="PlusClickPreviewReview(this, '<?=$arItem['ID']?>')"></a>
														&nbsp;
														<a href="javascript:void(0)" class="rating-vote-minus <?if(in_array($arParams['USER_ID'], $arItem['PROPERTIES']['DISLIKE_USERS']['VALUE'])){?> rating-vote-minus-active<?}?>" onclick="MinusClickPreviewReview(this, '<?=$arItem['ID']?>')"></a>
														<span class="rating-vote-result-minus"><?=$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']>0?$arItem['PROPERTIES']['DISLIKE_COUNTER']['VALUE']:0?></span>
													</span>
												</div>
											</td>
										<? }*/?>
									</tr>
								</table>
							</div>
							<div><?=$arItem['DETAIL_TEXT']?></div>
							<div class="reviews_list_links" style="text-align: right">
								<? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_P'){?>
									<span class="review_moderation">Отзыв находится на модерации</span>
								<? }?>
								<? if($arItem['PROPERTIES']['STATUS']['VALUE_XML_ID'] == 'XML_REVIEW_STATUS_S'){?>
									<span class="review_hide">Отзыв скрыт</span>
								<? }?>
							</div>
							<div class="reviews_list_links">
								<? if(count($arItem['SUB_ITEMS'])>0){?>
									<a id="preview_reviews_answers_block_link_<?=$arItem['ID']?>" class="reviews_answers_block_link" href="javascript:void(0)" onclick="ReviewsShowHide(this, 'preview_reviews_sub_items_<?=$arItem['ID']?>')"><span>Все ответы</span> (<?=$arItem["SUB_ITEMS_COUNT"]?>)</a>
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
							<div id="preview_reviews_sub_items_<?=$arItem['ID']?>" class="hide">
								<ul class="reviews_list">
									<?=BuildTreePreview($arItem['SUB_ITEMS'], $arParams, $arItem['ID'])?>
								</ul>
							</div>
						<? }?>
						
					</li>
				<?}?>
			</ul>
			
			</div>
			<br />
			<a href="#reviews_tab">Подробнее</a>
		</div>
	</div>
<? }?>
