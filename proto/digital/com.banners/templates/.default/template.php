<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><div class="baners-row">
	<?foreach($arResult["ITEMS"] as $arItem){
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		$year = explode('.', $arItem["DISPLAY_ACTIVE_FROM"]);
		$arItem["DETAIL_PAGE_URL"] = str_replace('#YEAR#', $year[2], $arItem["DETAIL_PAGE_URL"]);
		?>
		<div class="banners" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<?if( $arItem["DETAIL_PICTURE"] != "" ):?>
						<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 240, "height" => 100 ), BX_RESIZE_IMAGE_EXACT,true );?>
						<a href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>" target="<?=strstr($arItem["PROPERTIES"]["TARGETS"]["VALUE"], ' ', true)?>" class="thumb"><span class="arr"></span><img src="<?=$img["src"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a>

			<?endif;?>
		</div>
	<?}?>
</div>