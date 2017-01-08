<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="banners_column">
	<div class="small_banners_block">
		<?foreach( $arResult["ITEMS"] as $arItem ){
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<?if( is_array( $arItem["PREVIEW_PICTURE"] ) ){?>
				<?$img = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array( "width" => 180, "height" => 260 ), BX_RESIZE_IMAGE_EXACT ,true );?>
				<div class="advt_banner" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<?if( !empty( $arItem["PROPERTIES"]["URL_STRING"]["VALUE"] ) ){?>
						<a href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>">
					<?}?>
						<img border="0" width="<?=$img["width"]?>" height="<?=$img["height"]?>" src="<?=$img["src"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
					<?if( !empty( $arItem["PROPERTIES"]["URL_STRING"]["VALUE"] ) ){?>
						</a>
					<?}?>
				</div>
			<?}?>
		<?}?>
	</div>
</div>