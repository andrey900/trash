<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
	<?if ($arResult["ITEMS"]):?>
	<div class="top_slider_wrapp">
		<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.flexslider-min.js',true)?> 
		<div class="flexslider tab_sale_body active">
			<ul class="slides">
				
				<?foreach($arResult["ITEMS"] as $arItem){
					if(in_array($arItem['ID'], $arResult['SALE']))
					{
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						$background = is_array( $arItem["DETAIL_PICTURE"] ) ? $arItem["DETAIL_PICTURE"]["SRC"] : $this->GetFolder()."/images/background.jpg"?>
						<li class="box<?=($arItem["PROPERTIES"]["TEXTCOLOR"]["VALUE_XML_ID"] ? " ".$arItem["PROPERTIES"]["TEXTCOLOR"]["VALUE_XML_ID"] : "");?><?=($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"] ? " ".$arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"] : " left");?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>" style="background-image: url('<?=$background?>') !important;">
							<?if (!$arItem["PREVIEW_PICTURE"] && !$arItem["PREVIEW_TEXT"] && $arItem["PROPERTIES"]["URL_STRING"]["VALUE"]):?><a class="target" href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>"><?endif;?>
							<div class="wrapper_inner">	
								<? 
									$position = "center left";
									if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"])
									{
										if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="left")  $position = "center right";
										elseif ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="right")  $position = "center left";
										elseif ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="image")  $position = "center;";
									}
								?>
								<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%" <?if($arItem["PREVIEW_PICTURE"]):?>style="background: url(<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>) <?=$position;?> no-repeat"<?endif;?>>					
									<?if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]!="image"):?>
										<?ob_start();?>
											<td class="text">							
												<?if ($arItem["NAME"]):?>
													<?if($arItem["PROPERTIES"]["URL_STRING"]["VALUE"]):?><a href="<?=$arItem["PROPERTIES"]["BUTTON2LINK"]["VALUE"]?>"<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?> target="<?=$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]?>"<?endif;?>><?endif;?>
													<div class="banner_title"><span><?=$arItem["NAME"];?></span></div>
													<?if($arItem["PROPERTIES"]["URL_STRING"]["VALUE"]):?></a><?endif;?>
												<?endif;?>
												<?if ($arItem["PREVIEW_TEXT"]):?>
													<div class="banner_text"><?=$arItem["PREVIEW_TEXT"];?></div>
												<?endif;?>
												<?if((!empty($arItem["PROPERTIES"]["BUTTON2TEXT"]["VALUE"])&&!empty($arItem["PROPERTIES"]["BUTTON2LINK"]["VALUE"])) || (!empty($arItem["PROPERTIES"]["BUTTON1TEXT"]["VALUE"])&&!empty($arItem["PROPERTIES"]["BUTTON1LINK"]["VALUE"]))):?>
													<div class="banner_buttons">
														<?if(trim($arItem["PROPERTIES"]["BUTTON1TEXT"]["VALUE"]) && trim($arItem["PROPERTIES"]["BUTTON1LINK"]["VALUE"])){?>
															<a href="<?=$arItem["PROPERTIES"]["BUTTON1LINK"]["VALUE"]?>" class="<?=!empty( $arItem["PROPERTIES"]["BUTTON1CLASS"]["VALUE"] ) ? $arItem["PROPERTIES"]["BUTTON1CLASS"]["VALUE"] : "button30"?>"<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?> target="<?=$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]?>"<?endif;?>>
																<?=$arItem["PROPERTIES"]["BUTTON1TEXT"]["VALUE"]?>
															</a>
														<?}?>
														<?if( !empty( $arItem["PROPERTIES"]["BUTTON2TEXT"]["VALUE"] ) && !empty( $arItem["PROPERTIES"]["BUTTON2LINK"]["VALUE"] ) ){?>
															<a href="<?=$arItem["PROPERTIES"]["BUTTON2LINK"]["VALUE"]?>" class="<?=!empty( $arItem["PROPERTIES"]["BUTTON2CLASS"]["VALUE"] ) ? $arItem["PROPERTIES"]["BUTTON2CLASS"]["VALUE"] : "button30 grey"?>"<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?> target="<?=$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]?>"<?endif;?>>
																<?=$arItem["PROPERTIES"]["BUTTON2TEXT"]["VALUE"]?>
															</a>
														<?}?>
													</div>
												<?endif;?>							
											</td>
										<?$text = ob_get_clean();?>
									<?endif;?>
										
									<?ob_start();?>
										<td class="img" >
											<?if($arItem["PREVIEW_PICTURE"]):?>
												
												<?if( !empty( $arItem["PROPERTIES"]["URL_STRING"]["VALUE"] )){?><a href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>"<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?> target="<?=$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]?>"<?endif;?>><?}?>
												<?/*	<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />*/?>
												<?if( !empty( $arItem["PROPERTIES"]["URL_STRING"]["VALUE"] )){?></a><?}?>
											<?endif;?>									
										</td>
									<?$image = ob_get_clean();?>
									
									<? 
										if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"])
										{
											if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="left") echo $text.$image;
											elseif ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="right") echo $image.$text;
											elseif ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="image") echo $image;
										} else echo $text.$image;
									?>
								</table>
							</div>
							<?if (!$arItem["PREVIEW_PICTURE"] && !$arItem["PREVIEW_TEXT"] && $arItem["PROPERTIES"]["URL_STRING"]["VALUE"]):?></a><?endif;?>
						</li>
					<?}
				}?>
			</ul>
		</div>
		<div class="flexslider tab_action_body">
			<ul class="slides">
				<?foreach($arResult["ITEMS"] as $arItem){
					if(in_array($arItem['ID'], $arResult['ACTION']))
					{
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						$background = is_array( $arItem["DETAIL_PICTURE"] ) ? $arItem["DETAIL_PICTURE"]["SRC"] : $this->GetFolder()."/images/background.jpg"?>
						<li class="box<?=($arItem["PROPERTIES"]["TEXTCOLOR"]["VALUE_XML_ID"] ? " ".$arItem["PROPERTIES"]["TEXTCOLOR"]["VALUE_XML_ID"] : "");?><?=($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"] ? " ".$arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"] : " left");?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>" style="background-image: url('<?=$background?>') !important;">
							<?if (!$arItem["PREVIEW_PICTURE"] && !$arItem["PREVIEW_TEXT"] && $arItem["PROPERTIES"]["URL_STRING"]["VALUE"]):?><a class="target" href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>"><?endif;?>
							<div class="wrapper_inner">	
								<? 
									$position = "center left";
									if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"])
									{
										if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="left")  $position = "center right";
										elseif ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="right")  $position = "center left";
										elseif ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="image")  $position = "center;";
									}
								?>
								<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%" <?if($arItem["PREVIEW_PICTURE"]):?>style="background: url(<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>) <?=$position;?> no-repeat"<?endif;?>>					
									<?if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]!="image"):?>
										<?ob_start();?>
											<td class="text">							
												<?if ($arItem["NAME"]):?>
													<?if($arItem["PROPERTIES"]["URL_STRING"]["VALUE"]):?><a href="<?=$arItem["PROPERTIES"]["BUTTON2LINK"]["VALUE"]?>"<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?> target="<?=$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]?>"<?endif;?>><?endif;?>
													<div class="banner_title"><span><?=$arItem["NAME"];?></span></div>
													<?if($arItem["PROPERTIES"]["URL_STRING"]["VALUE"]):?></a><?endif;?>
												<?endif;?>
												<?if ($arItem["PREVIEW_TEXT"]):?>
													<div class="banner_text"><?=$arItem["PREVIEW_TEXT"];?></div>
												<?endif;?>
												<?if((!empty($arItem["PROPERTIES"]["BUTTON2TEXT"]["VALUE"])&&!empty($arItem["PROPERTIES"]["BUTTON2LINK"]["VALUE"])) || (!empty($arItem["PROPERTIES"]["BUTTON1TEXT"]["VALUE"])&&!empty($arItem["PROPERTIES"]["BUTTON1LINK"]["VALUE"]))):?>
													<div class="banner_buttons">
														<?if(trim($arItem["PROPERTIES"]["BUTTON1TEXT"]["VALUE"]) && trim($arItem["PROPERTIES"]["BUTTON1LINK"]["VALUE"])){?>
															<a href="<?=$arItem["PROPERTIES"]["BUTTON1LINK"]["VALUE"]?>" class="<?=!empty( $arItem["PROPERTIES"]["BUTTON1CLASS"]["VALUE"] ) ? $arItem["PROPERTIES"]["BUTTON1CLASS"]["VALUE"] : "button30"?>"<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?> target="<?=$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]?>"<?endif;?>>
																<?=$arItem["PROPERTIES"]["BUTTON1TEXT"]["VALUE"]?>
															</a>
														<?}?>
														<?if( !empty( $arItem["PROPERTIES"]["BUTTON2TEXT"]["VALUE"] ) && !empty( $arItem["PROPERTIES"]["BUTTON2LINK"]["VALUE"] ) ){?>
															<a href="<?=$arItem["PROPERTIES"]["BUTTON2LINK"]["VALUE"]?>" class="<?=!empty( $arItem["PROPERTIES"]["BUTTON2CLASS"]["VALUE"] ) ? $arItem["PROPERTIES"]["BUTTON2CLASS"]["VALUE"] : "button30 grey"?>"<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?> target="<?=$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]?>"<?endif;?>>
																<?=$arItem["PROPERTIES"]["BUTTON2TEXT"]["VALUE"]?>
															</a>
														<?}?>
													</div>
												<?endif;?>							
											</td>
										<?$text = ob_get_clean();?>
									<?endif;?>
										
									<?ob_start();?>
										<td class="img" >
											<?if($arItem["PREVIEW_PICTURE"]):?>
												
												<?if( !empty( $arItem["PROPERTIES"]["URL_STRING"]["VALUE"] )){?><a href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>"<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?> target="<?=$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]?>"<?endif;?>><?}?>
												<?/*	<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />*/?>
												<?if( !empty( $arItem["PROPERTIES"]["URL_STRING"]["VALUE"] )){?></a><?}?>
											<?endif;?>									
										</td>
									<?$image = ob_get_clean();?>
									
									<? 
										if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"])
										{
											if ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="left") echo $text.$image;
											elseif ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="right") echo $image.$text;
											elseif ($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"]=="image") echo $image;
										} else echo $text.$image;
									?>
								</table>
							</div>
							<?if (!$arItem["PREVIEW_PICTURE"] && !$arItem["PREVIEW_TEXT"] && $arItem["PROPERTIES"]["URL_STRING"]["VALUE"]):?></a><?endif;?>
						</li>
					<?}
				}?>
			</ul>
		</div>
		<div id="tab_navigation">
			<div id="tab_sale" class="active_tab"><?=GetMessage('SALE');?></div>
			<div id="tab_action"><?=GetMessage('ACTION');?></div>
		</div>
	</div>
	<?global $TEMPLATE_OPTIONS;?>
	<script>
	$(document).ready(function(){
		$('#tab_navigation div').click(function(){
			$('#tab_navigation div').removeClass('active_tab');
			$(this).addClass('active_tab');
			var id = $(this).attr('id')+'_body';
			$('.top_slider_wrapp .flexslider').removeClass('active');
			$('.'+id).addClass('active');
		});
	});
		$(".flexslider").flexslider({
			animation: "slide",
			slideshow: true,
			slideshowSpeed: 10000,
			animationSpeed: 600,
			<?=((count($arResult["ITEMS"])<2 || $TEMPLATE_OPTIONS["BANNER_WIDTH"]["CURRENT_VALUE"]=="NARROW") ? "directionNav: false," : "directionNav: true,")?>
			pauseOnHover: true
		});
	</script>
<?endif;?>