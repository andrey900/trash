<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $KShopSectionID;
$KShopSectionID = $arResult["IBLOCK_SECTION_ID"];
if(($arParams["SHOW_MEASURE"] == "Y") && ($arResult["CATALOG_MEASURE"])){
	$arMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arResult["CATALOG_MEASURE"]), false, false, array())->GetNext();
}
/*$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);*/
$totalCount = CKShop::GetTotalCount($arResult);
$arQuantityData = CKShop::GetQuantityArray($totalCount);
$arAddToBasketData = CKShop::GetAddToBasketArray($arResult, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], true);
$useStores = $arParams["USE_STORE"] == "Y" && $arResult["STORES_COUNT"] && $arQuantityData["RIGHTS"]["SHOW_QUANTITY"];
?>
<!--<h1><?=$arResult["NAME"]?></h1>-->
<div id='product-gift-to'>
<?
	$arFilter = array("ID" => $arResult['ID']);
	$arSelect = array("CATALOG_GROUP_".PRICE_BASE_ID);
	$arSort = array("sort" => "asc", "name" => "asc");
	$listElements = CIBlockExt::GetListElements($arFilter, $arSelect, $arSort);
	$productInfo = array_shift($listElements);

	$actions = new CCustomGiftAction(SHARE_CATALOG_IBLOCK_ID, GIFTS_IBLOCK_ID, $arResult["PROPERTIES"]["GIFTS"]["VALUE"]);
	$actionsInfo = $actions->GetInfo();
?>
</div>
<div class="item_main_info" itemscope itemtype="http://schema.org/Product">
	<span itemprop="name" style="display:none"><?=$arResult["NAME"]?></span>
	<span itemprop="sku" style="display:none"><?=$arResult["ID"]?></span>
	<span itemprop="description" style="display:none"><?=( empty($arResult["PREVIEW_TEXT"]) )?$arResult["NAME"]:$arResult["PREVIEW_TEXT"];?></span>
	
	<?php 
	include '_pictures_slider.php';
	?>	
	
	<div class="right_info">
		<?
		if (!empty($actionsInfo["GIFT_ACTIONS"]))
		{
			include '_action_gift_info.php'; // общая информация об акции
			include '_action_gift_popup.php'; // общая информация об акции
		}
		?>
		
		<?php 
		include '_good_main_data_block.php';
		?>	
		
	</div>
	<div class="clearleft"></div>	
	
	
	<?if (($arParams["SHOW_KIT_PARTS"]=="Y") && !empty($arResult["SET_ITEMS"])):?>
		<?php 
		include '_sets.php';
		?>
	<?endif;?>	
</div>	

	<?php 
	include '_sets_constructor.php';
	?>	
	
<div class="tabs_section">
	<ul class="tabs main_tabs">
		<?$show_tabs=false;?>
		<li class="<?if (!$show_tabs): $show_tabs=true;?> cur<?endif;?>" id="all_tab">
			<a href="#all_tab"><span>Все</span></a>
		</li>
		<li id="characteristics_tab">
			<a href="#characteristics_tab"><span>Характеристики</span></a>
		</li>
		<?if( is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) ):?>
			<li class="prices_tab<?if (!$show_tabs): $show_tabs=true;?> cur<?endif;?>" id="offers_tab">
				<a href="#offers_tab"><span><?=GetMessage("OFFER_PRICES")?></span></a>
			</li>
		<?endif;?>
		<?
			$showProps = false;
			foreach( $arResult["DISPLAY_PROPERTIES"] as $arProp )
			{ if (($arProp["CODE"]!="SERVICES")&&($arProp["CODE"]!="BRAND")&&($arProp["CODE"]!="HIT")&&($arProp["CODE"]!="RECOMMEND")&&($arProp["CODE"]!="NEW")&&($arProp["CODE"]!="STOCK")&&($arProp["CODE"]!="VIDEO")&&trim($arProp["VALUE"])){$showProps=true;}}
		?>
		<?if ($arResult["DETAIL_TEXT"] || count($arResult["STOCK"]) || ($showProps && $arParams["PROPERTIES_DISPLAY_LOCATION"]!="TAB")):?>
			<li <?if (!$show_tabs): $show_tabs=true;?>class="cur"<?endif;?> id="description_tab">
				<a href="#description_tab"><span><?=GetMessage("DESCRIPTION_TAB")?></span></a>
			</li>
		<?endif;?>
		<?if ($arParams["PROPERTIES_DISPLAY_LOCATION"]=="TAB" && $showProps):?>
			<li <?if (!$show_tabs): $show_tabs=true;?>class="cur"<?endif;?> id="location_tab">
				<a href="#location_tab"><span><?=GetMessage("PROPERTIES_TAB")?></span></a>
			</li>
		<?endif;?>
		<?if ($arParams["USE_REVIEW"]=="Y"):?>
			<li <?if (!$show_tabs): $show_tabs=true;?>class="cur"<?endif;?> id="reviews_tab">
				<a href="#reviews_tab"><span><?=GetMessage("REVIEW_TAB")?></span><span class="count empty"></span></a>
			</li>
		<?endif;?>
		<?if (($arParams["SHOW_ASK_BLOCK"]=="Y")&&(intVal($arParams["ASK_FORM_ID"]))):?>
			<li <?if (!$show_tabs): $show_tabs=true;?>class="cur"<?endif;?> id="ask_tab">
				<a href="#ask_tab"><span><?=GetMessage('ASK_TAB')?></span></a>
			</li>
		<?endif;?>
		<?if($useStores && (!is_array($arResult["OFFERS"]) || empty($arResult["OFFERS"]))):  ?>
			<li class="stores_tab" id="stores_tab">
				<a href="#stores_tab"><span><?=GetMessage("STORES_TAB");?></span></a>
			</li>
		<?endif;?>
		<?if ($arResult["DISPLAY_PROPERTIES"]["VIDEO"]["VALUE"]||$arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"]||$arResult["SECTION_FULL"]["UF_VIDEO"]||$arResult["SECTION_FULL"]["UF_VIDEO_YOUTUBE"]):?>
			<li <?if (!$show_tabs): $show_tabs=true;?>class="cur"<?endif;?> id="video_tab">
				<a href="#video_tab"><span><?=GetMessage("VIDEO_TAB")?></span></a>
			</li>
		<?endif;?>
		<?if($arParams["SHOW_ADDITIONAL_TAB"] == "Y"):?>
			<li id="add_tab">
				<a href="#add_tab"><span><?=GetMessage("ADDITIONAL_TAB");?></span></a>
			</li>
		<?endif;?>
		<? /*ANIART*/?>
		<? if($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"]){?>
			<li id="expandables_tab">
				<a href="#expandables_tab"><span><?=GetMessage("EXPANDABLES_TITLE")?></span></a>
			</li>
		<? }?>
	</ul>
		
	<ul class="tabs_content">
		<?$show_tabs = false;?>
		<? /*ANIART*/?>
		<li class="<?if (!$show_tabs): $show_tabs=true;?> cur<?endif;?>">
			<div class="clear"></div>
			
			<?
			$filename = $_SERVER['DOCUMENT_ROOT'].SITE_DIR."include/additional_products_description.php";
			$handle = fopen($filename, "r");
			$contents_add_info = fread($handle, filesize($filename));
			fclose($handle);
			?>
			<?
			$arPieceTable = array('CHARACTERISTICS'=>'');
			$ar_res = $arResult['RES_ELEM_COMP'];
			$arPropName = $arResult['PRIMARY_PROP_NAME'];
			$arIdElements = array($arResult['ID']);
			?>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr valign="top">
					<? if($arResult["DETAIL_TEXT"] || strlen($contents_add_info)>0){?>
						<td>
							<? if($arResult["DETAIL_TEXT"]){?>
								<div class="small_block">
									<div class="small_block_head"><?=GetMessage("DESCRIPTION_TAB")?></div>
									<div class="description">
										<div class="small_block_description_text">
											<? echo strip_tags(substr($arResult["~DETAIL_TEXT"], 0, 160)); echo strlen($arResult["DETAIL_TEXT"])>160?"...":"";?>
										</div>
										<a href="#description_tab">Подробнее</a>
									</div>
								</div>
								<br />
							<? }?>
							<? if(strlen($contents_add_info)>0){?>
								<div class="small_block">
									<div class="small_block_head"><?=GetMessage("ADDITIONAL_TAB");?></div>
									<div class="description">
										<div class="small_block_description_text">
											<? echo strip_tags(substr($contents_add_info, 0, 160)); echo strlen($contents_add_info)>160?"...":"";?>
										</div>	
										<br />
										<a href="#add_tab">Подробнее</a>
									</div>
								</div>
								<br />
							<? }?>
							<div class="clear"></div>
							<div id="preview_reviews_content_block"></div>
						</td>
						<td width="20px;">&nbsp;</td>
					<? }?>
					<td width="40%">
						<div class="small_block">
							<div class="small_block_head">Характеристики</div>
							<div class="description">
								<?	
								unset($arPropName['MINIMUM_PRICE']);
										
								foreach ($arPropName as $code => $name)
								{
									if($code != '0')
										$arPieceTable[$code] = '<li><b>'.$name.'</b>: ';
								}
										
								foreach ($arIdElements as $arValue)
								{
									foreach ($arPropName as $code => $name)
									{
										$null_val_count = 0;
										foreach ($arIdElements as $ElementID)
										{
											if(empty($ar_res[$ElementID]['PROPERTY_'.$code.'_VALUE']) || in_array($ar_res[$ElementID]['PROPERTY_'.$code.'_VALUE'], array('Нет', 'нет')))
											{
												$null_val_count++;
											}			
										}
										
										if($null_val_count == count($arIdElements))
										{
											unset($arPropName[$code]);
											continue;
										}
												
										if( stristr($code, 'YN_CHECKBOX'))
										{
											$strPropName = $ar_res[$arValue]['PROPERTY_'.$code.'_VALUE'];
										}
										elseif ( stristr($code, 'DICTIONARY_MUL') )
										{
											$strPropName = '';
											foreach( $ar_res[$arValue]['PROPERTY_'.$code.'_VALUE'] as $propelem )
											{
												$strPropName .= $ar_res[$arValue]['PROPERTY_'.$code.'_INFO'][$propelem]['NAME'].'<br/>';
											}
										}
										else
										{
											$strPropName = $ar_res[$arValue]['PROPERTY_'.$code.'_NAME'];
										}
										$arDiffComp[$code][$strPropName] = $strPropName;
										$arPieceTable[$code] .= $strPropName;
									}
								}
								?>
								<div class="small_block_description_text">
									<ul class="small_characteristics">
									<?
									unset($arPropName[0]);
									foreach ($arPropName as $code => $name) 
									{
										echo $arPieceTable[$code].'</li>';
									}
									?>
									</ul>
								</div>
								<a href="#characteristics_tab">Подробнее</a>
							</div>		
						</div>
						<? if($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"]){?>
							<br />
							<div class="expandables_small_block" id="expandables_small_block"></div>
						<? }?>
					</td>
				</tr>
			</table>
		</li>
		<li>
			<?include_once('_table_compare.php');?>
		</li>	
			
		<?$showSkUImages = in_array('PREVIEW_PICTURE', $arParams['OFFERS_FIELD_CODE']) || in_array('DETAIL_PICTURE', $arParams['OFFERS_FIELD_CODE']);?>
		<?if( is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) ){?>
			<li class="prices_tab<?if (!$show_tabs): $show_tabs=true;?> cur<?endif;?>">
				<table class="colored offers_table" cellspacing="0" cellpadding="0" width="100%" border="0">
					<thead>
						<tr>
							<?if($useStores):?>
								<td></td>
							<?endif;?>
							<?if($showSkUImages):?>
								<td class="property" width="50"></td>
							<?endif;?>
							<?foreach ($arResult["SKU_PROPERTIES"] as $key => $arProp){?>
								<?if(!$arProp["IS_EMPTY"]):?>
									<td class="property">
										<span><?if($arProp["HINT"] && $arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><div class="tooltip"><a class="tooltip_close">&times;</a><?=$arProp["HINT"]?></div></div><?endif;?><?=$arProp["NAME"]?></span>
									</td>
								<?endif;?>
							<?}?>
							<td class="price_th"><?=GetMessage("CATALOG_PRICE")?></td>
							<?if($arQuantityData["RIGHTS"]["SHOW_QUANTITY"]):?>
								<td class="count_th"><?=GetMessage("AVAILABLE")?></td>
							<?endif;?>
							<td colspan="3"></td>
						</tr>
					</thead>
					<tbody>
						<?$numProps = count($arResult["SKU_PROPERTIES"]);?>
						<?foreach ($arResult["SKU_ELEMENTS"] as $key => $arSKU){?>
							<?
							if($arResult["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"]){
								$sMeasure = $arResult["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"].".";
							}
							else{
								$sMeasure = GetMessage("MEASURE_DEFAULT").".";
							}
							$skutotalCount = CKShop::CheckTypeCount($arSKU["CATALOG_QUANTITY"]);
							$arskuQuantityData = CKShop::GetQuantityArray($skutotalCount, array('quantity-wrapp', 'quantity-indicators'));
							$arskuAddToBasketData = CKShop::GetAddToBasketArray($arSKU, $skutotalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false);
							?>
							<?
							if(($arParams["SHOW_MEASURE"]=="Y")&&($arSKU["CATALOG_MEASURE"])){ 
								$symb = substr($arSKU["PRICE"], strrpos($arSKU["PRICE"], ' '));
								$arSCUMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arSKU["CATALOG_MEASURE"]), false, false, array())->GetNext(); 
							}
							?>
							<?$collspan = 1;?>
							<tr>
								<?if($useStores):?>
									<td class="opener">
										<?$collspan++;?>
										<span class="opener_icon"><i></i></span>
									</td>
								<?endif;?>
								<?if($showSkUImages):?>
									<?$collspan++;?>
									<td class="property">
										<?if($imgID = ($arResult['OFFERS'][$key]['PREVIEW_PICTURE'] ? $arResult['OFFERS'][$key]['PREVIEW_PICTURE'] : ($arResult['OFFERS'][$key]['DETAIL_PICTURE'] ? $arResult['OFFERS'][$key]['DETAIL_PICTURE'] : false))):?>
											<?$arImg = CFile::ResizeImageGet($imgID, array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
											<img src="<?=$arImg['src']?>" alt="" />
										<?endif;?>
									</td>
								<?endif;?>
								<?for( $i = 0; $i < $numProps; $i++ ){?>
									<?if(!$arResult["SKU_PROPERTIES"][$i]["IS_EMPTY"]):?>
										<?$collspan++;?>
										<td class="property">
											<?=!empty($arSKU[$i]) ? $arSKU[$i] : GetMessage('NOT_PROP')?>
										</td>
									<?endif;?>
								<?}?>
								<td class="price">
									<?$collspan++;?>
									<?if( intval($arSKU["DISCOUNT_PRICE"]) > 0 && $arSKU["PRICE"] > 0){?>
										<span class="price"><?=$arSKU["DISCOUNT_PRICE"]?><?if (($arParams["SHOW_MEASURE"]=="Y")&&$arSCUMeasure["SYMBOL_RUS"]):?><small>/<?=$arSCUMeasure["SYMBOL_RUS"]?></small><?endif;?></span><br />
										<span class="price discount"><strike><?=$arSKU["PRICE"]?></strike></span>
									<?}else{?>
										<span class="price">
											<?=$arSKU["PRICE"]?><?if (($arParams["SHOW_MEASURE"]=="Y")&&$arSCUMeasure["SYMBOL_RUS"]):?><small>/<?=$arSCUMeasure["SYMBOL_RUS"]?></small><?endif;?>
										</span>
									<?}?>
								</td>
								<?if(strlen($arskuQuantityData["TEXT"])):?>
									<?$collspan++;?>
									<td class="count">
										<?=$arskuQuantityData["HTML"]?>
									</td>
								<?endif;?>
								<!--noindex-->
									<?if($arskuAddToBasketData["ACTION"] == "ADD"):?>
										<td class="counter_block" data-item="<?=$arSKU["ID"];?>">
											<?$collspan++;?>
											<?if($arskuAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] && !count($arSKU["OFFERS"]) && $arskuAddToBasketData["ACTION"] == "ADD"):?>
												<span class="minus">-</span>
												<input type="text" class="text" name="count_items" value="<?=($arParams["DEFAULT_COUNT"] ? $arParams["DEFAULT_COUNT"] : "1");?>" />
												<span class="plus">+</span>
											<?endif;?>
										</td>
									<?endif;?>
									<td class="buy" <?=($arskuAddToBasketData["ACTION"] !== "ADD" ? 'colspan="3"' : "")?>>
										<?if($arskuAddToBasketData["ACTION"] !== "ADD"):?>
											<?$collspan += 3;?>
										<?else:?>
											<?$collspan++;?>
										<?endif;?>
										<?=$arskuAddToBasketData["HTML"]?>
									</td>
									<?if($arskuAddToBasketData["ACTION"] == "ADD"):?>
										<td class="one_click_buy">
											<?$collspan++;?>
											<a class="basket_button one_click" data-item="<?=$arSKU["ID"]?>" data-quantity="<?=($skutotalCount >= $arParams["DEFAULT_COUNT"] ? $arParams["DEFAULT_COUNT"] : $skutotalCount)?>" onclick="oneClickBuy('<?=$arSKU["ID"]?>', '<?=$arParams["IBLOCK_ID"]?>', this)">
												<span><?=GetMessage('ONE_CLICK_BUY')?></span>
											</a>
										</td>
									<?endif;?>	
								<!--/noindex-->
							</tr>
							
							<?if($useStores):?>
								<?$collspan--;?>
								<tr class="offer_stores"><td colspan="<?=$collspan?>">
									<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "product_stores_amount", array(
											"PER_PAGE" => "10",
											"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
											"SCHEDULE" => $arParams["SCHEDULE"],
											"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
											"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
											"ELEMENT_ID" => $arSKU["ID"],
											"STORE_PATH"  =>  $arParams["STORE_PATH"],
											"MAIN_TITLE"  =>  $arParams["MAIN_TITLE"],
											"MAX_AMOUNT"=>$arParams["MAX_AMOUNT"],
											"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
										),
										$component
									);?>
								</tr>
							<?endif;?>
						<?}?>
					</tbody>
				</table>
			</li>
		<?}?>
		<?if ($arResult["DETAIL_TEXT"] /*|| count($arResult["STOCK"])*/ || ($showProps && $arParams["PROPERTIES_DISPLAY_LOCATION"]!="TAB")):?>
			<li <?if (!$show_tabs): $show_tabs=true;?>class="cur"<?endif;?>>
				<?/*if (is_array($arResult["STOCK"]) && !empty($arResult["STOCK"])):?>
					<?foreach($arResult["STOCK"] as $key => $arStockItem):?>
						<div class="stock_board">
							<div class="title"><?=GetMessage("CATALOG_STOCK_TITLE")?></div>
							<div class="txt"><?=$arStockItem["PREVIEW_TEXT"]?></div>	
							<a class="read_more" href="<?=$arStockItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("CATALOG_STOCK_VIEW")?></a>							
						</div>
					<?endforeach;?>
				<?endif;*/?>
				<?if ($arResult["DETAIL_TEXT"]):?>
					<div class="detail_text"><?=$arResult["DETAIL_TEXT"]?></div>
				<?endif;?>
				<?if ($showProps && $arParams["PROPERTIES_DISPLAY_LOCATION"]!="TAB"):?>
					<?if ($arParams["PROPERTIES_DISPLAY_TYPE"]!="TABLE"):?>
						<div class="props_block">		
							<?foreach( $arResult["PROPERTIES"] as $propCode => $arProp ){?>
								<?if(isset($arResult["DISPLAY_PROPERTIES"][$propCode])):?>
									<?$arProp = $arResult["DISPLAY_PROPERTIES"][$propCode];?>
									<?if (($arProp["CODE"]!="SERVICES")&&($arProp["CODE"]!="BRAND")&&($arProp["CODE"]!="HIT")&&($arProp["CODE"]!="RECOMMEND")&&($arProp["CODE"]!="NEW")&&($arProp["CODE"]!="STOCK")&&($arProp["CODE"]!="VIDEO")&&($arProp["CODE"]!="VIDEO_YOUTUBE")):?>				
										<?if( !empty( $arProp["VALUE"] ) ){?>
											<div class="char">
												<div class="char_name"><span><?if($arProp["HINT"]&&$arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><b class="triangle"></b><div class="tooltip"><a class="tooltip_close">&times;</a><?=$arProp["HINT"]?></div></div><?endif;?><?=$arProp["NAME"]?></span></div>
												<div class="char_value">
													<? 
													if(count($arProp["DISPLAY_VALUE"])>1) { foreach($arProp["DISPLAY_VALUE"] as $key => $value) { if ($arProp["DISPLAY_VALUE"][$key+1]) {echo $value.", ";} else {echo $value;} }} 
													else { echo $arProp["DISPLAY_VALUE"]; } 
													?>
												</div>
											</div>
										<?}?>
									<?endif;?>
								<?endif;?>
							<?}?>		
						</div>	
					<?else:?>
						<table class="props_table">
							<?foreach( $arResult["PROPERTIES"] as $propCode => $arProp ){?>
								<?if(isset($arResult["DISPLAY_PROPERTIES"][$propCode])):?>
									<?$arProp = $arResult["DISPLAY_PROPERTIES"][$propCode];?>
									<?if (($arProp["CODE"]!="CML2_ARTICLE")&&($arProp["CODE"]!="SERVICES")&&($arProp["CODE"]!="BRAND")&&($arProp["CODE"]!="HIT")&&($arProp["CODE"]!="RECOMMEND")&&($arProp["CODE"]!="NEW")&&($arProp["CODE"]!="STOCK")&&($arProp["CODE"]!="VIDEO")&&($arProp["CODE"]!="VIDEO_YOUTUBE")):?>				
										<?if( !empty( $arProp["VALUE"] ) ){?>
											<tr>
												<td class="char_name"><span><?if($arProp["HINT"]&&$arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><b class="triangle"></b><div class="tooltip"><a class="tooltip_close">&times;</a><?=$arProp["HINT"]?></div></div><?endif;?><?=$arProp["NAME"]?></span></td>
												<td class="char_value">
													<span>
													<? 
														if(count($arProp["DISPLAY_VALUE"])>1) 
														{ 
															foreach($arProp["DISPLAY_VALUE"] as $key => $value) 
															{ 
																if ($arProp["DISPLAY_VALUE"][$key+1]) {echo $value.", ";} else {echo $value;}}
															}
														else { echo $arProp["DISPLAY_VALUE"]; }
													?>
													</span>
												</td>
											</tr>
										<?}?>
									<?endif;?>
								<?endif;?>
							<?}?>
						</table>
					<?endif;?>
				<?endif;?>
				<?if( $arResult["SERVICES"] ):?>
					<div class="services_block">
						<img class="shadow" src="<?=SITE_TEMPLATE_PATH?>/images/shadow_bottom.png" />
						<h4><?=GetMessage("SERVICES_TITLE")?></h4>
						<?foreach ($arResult["SERVICES"] as $arService):?>
							<span class="item">
								<a href="<?=$arService["DETAIL_PAGE_URL"]?>">
									<i class="arrow"><b></b></i>
									<span class="link"><?=$arService["NAME"]?></span>
								</a>
							</span>
						<?endforeach;?>
					</div>				
				<?endif;?>	
				
				<?
				$arFiles = array();
				if ($arResult["PROPERTIES"]["INSTRUCTIONS"]["VALUE"]) { $arFiles = $arResult["PROPERTIES"]["INSTRUCTIONS"]["VALUE"]; }
					else { $arFiles = $arResult["SECTION_FULL"]["UF_FILES"]; }
				if (is_array($arFiles))
				{
					foreach ($arFiles as $key => $value){if (!intval($value)){unset($arFiles[$key]);}}
				}
				?>
				<?if ($arFiles){?>
					<img class="shadow" src="<?=SITE_TEMPLATE_PATH?>/images/shadow_bottom.png" />
					<div class="files_block">
						<h4><?=GetMessage("DOCUMENTS_TITLE")?></h4>
						<?foreach( $arFiles as $arItem ){?>
							<?$arItem = CFile::GetFileArray($arItem);?>
							<div class="file_type clearfix<? if( $arItem["CONTENT_TYPE"] == 'application/pdf' ){ echo " pdf"; } elseif( $arItem["CONTENT_TYPE"] == 'application/octet-stream' ){ echo " word"; } 
							elseif( $arItem["CONTENT_TYPE"] == 'application/xls' ){ echo " excel"; } elseif( $arItem["CONTENT_TYPE"] == 'image/jpeg' ){ echo " jpg"; } elseif( $arItem["CONTENT_TYPE"] == 'image/tiff' ){ echo " tiff"; }?>">
								<i class="icon"></i>
								<div class="description">
									<?$fileName = substr($arItem["ORIGINAL_NAME"], 0, strrpos($arItem["ORIGINAL_NAME"], '.'));?>
									<a target="_blank" href="<?=$arItem["SRC"]?>"><?if ($arItem["DESCRIPTION"]):?><?=$arItem["DESCRIPTION"]?><?elseif($fileName):?><?=$fileName?><?endif;?></a>
									<span class="size"><?=GetMessage('CT_NAME_SIZE')?>:
									<? $filesize = $arItem["FILE_SIZE"];
										if($filesize > 1024) 
										{ $filesize = ($filesize/1024);
											if($filesize > 1024) 
											{ $filesize = ($filesize/1024);
												if($filesize > 1024) { $filesize = ($filesize/1024); $filesize = round($filesize, 1); echo $filesize.GetMessage('CT_NAME_GB');} 
													else { $filesize = round($filesize, 1); echo $filesize.GetMessage('CT_NAME_MB'); }
											} else { $filesize = round($filesize, 1); echo $filesize.GetMessage('CT_NAME_KB'); }
										} else { $filesize = round($filesize, 1); echo $filesize.GetMessage('CT_NAME_b'); }
									?></span>
								</div>
							</div>
						<?}?>
					</div>
				<?}?>
			</li>
		<?endif;?>
		
		<?if ($showProps && $arParams["PROPERTIES_DISPLAY_LOCATION"]=="TAB"):?>
			<li>
				<?if ($arParams["PROPERTIES_DISPLAY_TYPE"]!="TABLE"):?>
					<div class="props_block">		
						<?foreach( $arResult["DISPLAY_PROPERTIES"] as $arProp ){?>
							<?if (($arProp["CODE"]!="SERVICES")&&($arProp["CODE"]!="BRAND")&&($arProp["CODE"]!="HIT")&&($arProp["CODE"]!="RECOMMEND")&&($arProp["CODE"]!="NEW")&&($arProp["CODE"]!="STOCK")&&($arProp["CODE"]!="VIDEO")&&($arProp["CODE"]!="VIDEO_YOUTUBE")):?>				
								<?if( !empty( $arProp["VALUE"] ) ){?>
									<div class="char">
										<div class="char_name"><span><?if($arProp["HINT"]&&$arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><b class="triangle"></b><div class="tooltip"><a class="tooltip_close">&times;</a><?=$arProp["HINT"]?></div></div><?endif;?><?=$arProp["NAME"]?></span></div>
										<div class="char_value">
											<? 
											if(count($arProp["DISPLAY_VALUE"])>1) { foreach($arProp["DISPLAY_VALUE"] as $key => $value) { if ($arProp["DISPLAY_VALUE"][$key+1]) {echo $value.", ";} else {echo $value;} }} 
											else { echo $arProp["DISPLAY_VALUE"]; } 
											?>
										</div>
									</div>
								<?}?>
							<?endif;?>
						<?}?>		
					</div>	
				<?else:?>
					<table class="props_table">
						<?foreach( $arResult["DISPLAY_PROPERTIES"] as $arProp ){?>
							<?if (($arProp["CODE"]!="SERVICES")&&($arProp["CODE"]!="BRAND")&&($arProp["CODE"]!="HIT")&&($arProp["CODE"]!="RECOMMEND")&&($arProp["CODE"]!="NEW")&&($arProp["CODE"]!="STOCK")&&($arProp["CODE"]!="VIDEO")&&($arProp["CODE"]!="VIDEO_YOUTUBE")):?>					
								<?if( !empty( $arProp["VALUE"] ) ){?>
									<tr>
										<td class="char_name"><span><?if($arProp["HINT"]&&$arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><b class="triangle"></b><div class="tooltip"><a class="tooltip_close">&times;</a><?=$arProp["HINT"]?></div></div><?endif;?><?=$arProp["NAME"]?></span></td>
										<td class="char_value">
											<span>
												<?
												if(count($arProp["DISPLAY_VALUE"])>1) { foreach($arProp["DISPLAY_VALUE"] as $key => $value) { if ($arProp["DISPLAY_VALUE"][$key+1]) {echo $value.", ";} else {echo $value;} }} 
												else { echo $arProp["DISPLAY_VALUE"]; }
												?>
											</span>
										</td>
									</tr>
								<?}?>
							<?endif;?>
						<?}?>
					</table>
				<?endif;?>
			</li>
		<?endif;?>		
		<?if ($arParams["USE_REVIEW"]=="Y"):?><li></li><?endif;?>
		<?if (($arParams["SHOW_ASK_BLOCK"]=="Y")&&(intVal($arParams["ASK_FORM_ID"]))):?>
			<li>
				 <?$APPLICATION->IncludeFile(SITE_DIR."include/ask_tab_detail_description.php", Array(), Array( "MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_ASK_DESCRIPTION')) );?>
				 <div id="ask_block"></div>
			</li>
		<?endif;?>
		<?if($useStores && (!is_array($arResult["OFFERS"]) || empty($arResult["OFFERS"]))):?>
			<li class="stores_tab">
				<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "product_stores_amount", array(
						"PER_PAGE" => "10",
						"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
						"SCHEDULE" => $arParams["SCHEDULE"],
						"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
						"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
						"ELEMENT_ID" => $arResult["ID"],
						"STORE_PATH"  =>  $arParams["STORE_PATH"],
						"MAIN_TITLE"  =>  $arParams["MAIN_TITLE"],
						"MAX_AMOUNT"=>$arParams["MAX_AMOUNT"],
						"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
					),
					$component
				);?>
			</li>
		<?endif;?>
		<?if ($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"]||$arResult["SECTION_FULL"]["UF_VIDEO_YOUTUBE"]):?>
			<li class="video<?if (!$show_tabs): $show_tabs=true;?> cur<?endif;?>">
				<?if (!empty($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])):?>
					<?=$arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["~VALUE"];?>
					<div class="description">
						<img class="shadow" src="<?=SITE_TEMPLATE_PATH?>/images/shadow_bottom.png" />
						<p><?$APPLICATION->IncludeFile(SITE_DIR."include/video_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_VIDEO_DESCRIPTION')));?></p>
					</div>
				<?elseif (!empty($arResult["SECTION_FULL"]['UF_VIDEO_YOUTUBE'])):?>
					<?=$arResult["SECTION_FULL"]['~UF_VIDEO_YOUTUBE'];?>
					<div class="description">
						<img class="shadow" src="<?=SITE_TEMPLATE_PATH?>/images/shadow_bottom.png" />
						<p><?$APPLICATION->IncludeFile(SITE_DIR."include/video_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_VIDEO_DESCRIPTION')));?></p>
					</div>
				<?endif;?>
			</li>
		<?endif;?>
		<?if($arParams["SHOW_ADDITIONAL_TAB"] == "Y"):?>
			<li>
				<?$APPLICATION->IncludeFile(SITE_DIR."include/additional_products_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_ADDITIONAL_DESCRIPTION')));?>
			</li>
		<?endif;?>
		<? /*ANIART*/?>
		<? if($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"]){?>
			<li>
				<div id="expandables_block"></div>
			</li>
		<?php }?>
	</ul>
</div>

<script>
$(".open_stores .availability-row .value").click( function()
{	
	if ($(".stores_tab").length) { $(".stores_tab").addClass("cur").siblings().removeClass("cur"); }
	else 
	{ 
		$(".prices_tab").addClass("cur").siblings().removeClass("cur"); 
		if ($(".prices_tab .property.opener").length && !$(".prices_tab .property.opener .opened").length)
		{
			var item = $(".prices_tab .property.opener").first();
			item.find(".opener_icon").addClass("opened");
			item.parents("tr").next(".offer_stores").find(".stores_block_wrap").slideDown(200);
		}
	}
});

$(".opener").click(function()
{
	$(this).find(".opener_icon").toggleClass("opened");
	var showBlock = $(this).parents("tr").toggleClass("nb").next(".offer_stores").find(".stores_block_wrap");
	showBlock.slideToggle(200); 
});


$(".tabs_section .tabs li").live("click", function()
{
	if (!$(this).is(".cur"))
	{
		$(".tabs_section .tabs li").removeClass("cur");
		$(this).addClass("cur");
		$(".tabs_section ul.tabs_content li").removeClass("cur");
		if ($(this).attr("id")=="reviews_tab") { 
			$(".shadow.common").hide(); 
			$("#reviews_content").show();
			$(".tabs_section ul.tabs_content > li").hide();  
		} 
		else { 
			$(".shadow.common").show(); 
			$("#reviews_content").hide(); 
			$(".tabs_section ul.tabs_content > li").hide(); 
			$(".tabs_section ul.tabs_content > li:eq("+$(this).index()+")").addClass("cur"); 
			$(".tabs_section ul.tabs_content > li:eq("+$(this).index()+")").show(); 
		}
	}
});

$(".hint .icon").click(function(e)
{
	var tooltipWrapp = $(this).parents(".hint");
	tooltipWrapp.click(function(e){e.stopPropagation();})
	if (tooltipWrapp.is(".active"))
	{
		tooltipWrapp.removeClass("active").find(".tooltip").slideUp(200); 
	}
	else
	{
		tooltipWrapp.addClass("active").find(".tooltip").slideDown(200);
		tooltipWrapp.find(".tooltip_close").click(function(e) { e.stopPropagation(); tooltipWrapp.removeClass("active").find(".tooltip").slideUp(100);});	
		$(document).click(function() { tooltipWrapp.removeClass("active").find(".tooltip").slideUp(100);});				
	}
});	
</script>

<?/*if( !empty($arResult["PROPERTIES"]["ASSOCIATED"]["VALUE"])||( !empty($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"]) )){?>	
	<div class="specials_tabs_section specials_slider_wrapp">
		<img class="shadow " src="<?=SITE_TEMPLATE_PATH?>/images/shadow_bottom.png" />
		<ul class="tabs">
			<?$show_tabs = false;?>
			<?if ( !empty($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"])):?>
				<li<?if (!$show_tabs): $show_tabs=true;?> class="cur"<?endif;?>><span><?=GetMessage("EXPANDABLES_TITLE")?></span><i class="triangle"></i></li>
			<?endif;?>
			<?if ( !empty($arResult["PROPERTIES"]["ASSOCIATED"]["VALUE"])):?>
				<li<?if (!$show_tabs): $show_tabs=true;?> class="cur"<?endif;?>><span><?=GetMessage("ASSOCIATED_TITLE")?></span><i class="triangle"></i></li>
			<?endif;?>
		</ul>
		<ul class="slider_navigation">
			<?$show_tabs = false;?>	
			<?if ( !empty($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"])):?>
				<?if(count($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"])>4):?>
					<li class="specials_slider_navigation expandables_nav<?if (!$show_tabs): $show_tabs=true;?> cur<?endif;?>"></li>
				<?endif;?>
			<?endif;?>
			<?if ( !empty($arResult["PROPERTIES"]["ASSOCIATED"]["VALUE"])):?>
				<?if(count($arResult["PROPERTIES"]["ASSOCIATED"]["VALUE"])>4):?>
					<li class="specials_slider_navigation associated_nav<?if (!$show_tabs): $show_tabs=true;?> cur<?endif;?>"></li>
				<?endif;?>
			<?endif;?>			
		</ul>
		<ul class="tabs_content">
			<?$show_tabs = false;?>
			<?if ( !empty($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"])):?>
				<li class="tab<?if(!$show_tabs):$show_tabs=true;?> cur<?endif;?>">
					<?$GLOBALS['arrFilterExpandables'] = array( "ID" => $arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"] );?>
					<?$APPLICATION->IncludeComponent("bitrix:catalog.top", "products_slider", array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"ELEMENT_SORT_FIELD" => "SORT",
						"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
						"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
						"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
						"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
						"ELEMENT_COUNT" => "20",
						"LINE_ELEMENT_COUNT" => "4",
						"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
						"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
						"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
						"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
						"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
						"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
						"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
						"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
						"SECTION_URL" => $arParams["SECTION_URL"],
						"DETAIL_URL" => $arParams["DETAIL_URL"],
						"BASKET_URL" => $arParams["BASKET_URL"],
						"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
						"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
						"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
						"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
						"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
						"PRICE_CODE" => $arParams["PRICE_CODE"],
						"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
						"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
						"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
						"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
						"USE_PRODUCT_QUANTITY" =>$arParams["USE_PRODUCT_QUANTITY"],
						"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
						"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
						"FILTER_NAME" => "arrFilterExpandables",
						"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
						),
						false
					);?> 
				</li>
			<?endif;?>
			<?if ( !empty($arResult["PROPERTIES"]["ASSOCIATED"]["VALUE"])):?>
				<li class="tab<?if(!$show_tabs):$show_tabs=true;?> cur<?endif;?>">
					<?$GLOBALS['arrFilterAssociated'] = array( "ID" => $arResult["PROPERTIES"]["ASSOCIATED"]["VALUE"] );?>
					<?$APPLICATION->IncludeComponent("bitrix:catalog.top", "products_slider", array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"ELEMENT_SORT_FIELD" => "SORT",
						"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
						"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
						"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
						"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
						"ELEMENT_COUNT" => "20",
						"LINE_ELEMENT_COUNT" => "4",
						"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
						"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
						"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
						"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
						"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
						"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
						"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
						"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
						"SECTION_URL" => $arParams["SECTION_URL"],
						"DETAIL_URL" => $arParams["DETAIL_URL"],
						"BASKET_URL" => $arParams["BASKET_URL"],
						"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
						"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
						"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
						"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
						"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
						"PRICE_CODE" => $arParams["PRICE_CODE"],
						"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
						"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
						"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
						"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
						"USE_PRODUCT_QUANTITY" =>$arParams["USE_PRODUCT_QUANTITY"],
						"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
						"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
						"FILTER_NAME" => "arrFilterAssociated",
						"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
						),
						false
					);?> 
				</li>
			<?endif;?>
		</ul>
		<script>
		

	
		$(".specials_slider_wrapp:first-child").first().flexslider({
			animation: "slide",
			selector: ".specials_slider > li",
			slideshow: false,
			animationSpeed: 600,
			directionNav: true,
			controlNav: false,
			pauseOnHover: true,
			itemWidth: 198, 
			animationLoop: false, 
			controlsContainer: ".specials_slider_navigation.cur",
		});
		if ($(".specials_slider_navigation.cur .flex-direction-nav .flex-disabled").length>1) { $(".specials_slider_navigation.cur").remove(); }
		
		if ($(".thumbs_navigation .flex-direction-nav .flex-disabled").length>1) { $(".thumbs_navigation").remove(); }
		
		$(".specials_tabs_section .tabs > li").live("click", function()
		{
			if (!$(this).is(".cur"))
			{
				$(".specials_tabs_section .tabs > li").removeClass("cur");
				$(this).addClass("cur");
				$(".specials_tabs_section .tabs_content li.tab").removeClass("active");
				$(".specials_tabs_section .tabs_content li.tab:eq("+$(this).index()+")").addClass("active");
				setTimeout(function(){$(".specials_tabs_section .tabs_content li.tab:not(.active)").fadeOut(333);}, 200)
				$(".specials_tabs_section .tabs_content li.tab:eq("+$(this).index()+")").fadeIn(333);
				$(".slider_navigation > li").removeClass("cur");
				$(".slider_navigation > li:eq("+$(this).index()+")").addClass("cur").show();
			}
			if (!$(".tab.active .flex-viewport").length)
			{
				$(".tab.active").flexslider({
					animation: "slide",
					selector: ".specials_slider > li",
					slideshow: false,
					animationSpeed: 600,
					directionNav: true,
					controlNav: false,
					pauseOnHover: true,
					itemWidth: 199, 
					animationLoop: false, 
					controlsContainer: ".specials_slider_navigation.cur",
				});
				$('.specials_slider_wrapp .tab.active').equalize({children: '.item-title'}); 
				$('.specials_slider_wrapp .tab.active').equalize({children: '.item_info'});
				
				if ($(".specials_slider_navigation.cur .flex-direction-nav .flex-disabled").length>1) { $(".specials_slider_navigation.cur").hide(); }
			}
			
		});
		
		$(".specials_slider_wrapp").ready(function()
		{
			$('.specials_slider_wrapp .tab.cur').equalize({children: '.item-title'}); 
			$('.specials_slider_wrapp .tab.cur').equalize({children: '.item_info'}); 
			$('.specials_tabs_section .tabs_content').equalize({children: 'li.tab'});
			$('.specials_tabs_section .tabs_content .tab.cur').equalize({children: 'li.catalog_item'});
			//$('.specials_tabs_section .tabs_content').height($('.specials_tabs_section .tabs_content li:first-child').outerHeight());
			
		});
		
		if ($(window).outerWidth()>600 && $(window).outerWidth()<768 && $(".catalog_detail .buy_buttons_wrapp a").length>1) 
		{ 
			var adapt = false;
			var prev;
			$(".catalog_detail .buy_buttons_wrapp a").each(function(i, element)
			{
				prev = $(".catalog_detail .buy_buttons_wrapp a:eq("+(i-1)+")");
				if ($(this).offset().top!=$(prev).offset().top && i>0) { $(".catalog_detail .buttons_block").addClass("adaptive"); }
			});
		} else { $(".catalog_detail .buttons_block").removeClass("adaptive"); }	
		</script>
	</div>
<?}*/?>

