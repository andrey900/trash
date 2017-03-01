<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
include_once("libs.php");

$filter = str_replace(array('/online-kit-socket/', "index.php"), "", $APPLICATION->GetCurPage());

$arSuperFilter = new \stdClass();

if(preg_match_all("/(brand|collection|colorPush|colorFrame)-([\w\d\-\+]+)\//", $filter, $match)){
	foreach ($match[1] as $key => $type) {
		$arSuperFilter->$type = $match[2][$key];
	}
}

$replace = array($arSuperFilter->brand, $arSuperFilter->collection, $arSuperFilter->colorFrame, $arSuperFilter->colorPush);
/*
if(property_exists($arSuperFilter, 'colorPush')){
	$type = 'colorPush';
	$arBrandInfo = GetDataFactory::getSectionByCode($arSuperFilter->brand);
	$arCollectionInfo = GetDataFactory::getCollectionsByBrand($arBrandInfo->id);
	$currCollection = current(array_filter($arCollectionInfo, function($e) use ($arSuperFilter){return ($e->code == $arSuperFilter->collection);}));
	$arPushsInfo = CElectrodomTools::GetInfoElements($arSuperFilter->colorPush);
	$arFramesInfo = CElectrodomTools::GetInfoElements($arSuperFilter->colorFrame);
	$arFrames = GetDataFactory::getFramesOrPushItems($currCollection->id, 'frames');
	$arPushs = GetDataFactory::getFramesOrPushItems($currCollection->id, 'pushs');
	$replace[0] = $arBrandInfo->name;
	$replace[1] = $arCollectionInfo->name;
	$replace[2] = $arFramesInfo[$arSuperFilter->colorFrame]['NAME'];
	$replace[3] = $arPushsInfo[$arSuperFilter->colorPush]['NAME'];
} elseif(property_exists($arSuperFilter, 'colorFrame')) {
	$type = 'colorFrame';
	$arBrandInfo = GetDataFactory::getSectionByCode($arSuperFilter->brand);
	$arCollectionInfo = GetDataFactory::getCollectionsByBrand($arBrandInfo->id);
	$arFramesInfo = CElectrodomTools::GetInfoElements($arSuperFilter->colorFrame);
	$currCollection = current(array_filter($arCollectionInfo, function($e) use ($arSuperFilter){return ($e->code == $arSuperFilter->collection);}));
	$arFrames = GetDataFactory::getFramesOrPushItems($currCollection->id, 'frames');
	$arPushs = GetDataFactory::getFramesOrPushItems($currCollection->id, 'pushs');
	$replace[0] = $arBrandInfo->name;
	$replace[1] = $arCollectionInfo->name;
	$replace[2] = $arFramesInfo[$arSuperFilter->colorFrame]['NAME'];
} elseif(property_exists($arSuperFilter, 'collection')) {
	$type = 'collection';
	$arBrandInfo = GetDataFactory::getSectionByCode($arSuperFilter->brand);
	$arCollectionInfo = GetDataFactory::getCollectionsByBrand($arBrandInfo->id);
	$currCollection = current(array_filter($arCollectionInfo, function($e) use ($arSuperFilter){return ($e->code == $arSuperFilter->collection);}));
	$arFrames = GetDataFactory::getFramesOrPushItems($currCollection->id, 'frames');
	$arPushs = GetDataFactory::getFramesOrPushItems($currCollection->id, 'pushs');
	$replace[0] = $arBrandInfo->name;
	$replace[1] = $arCollectionInfo->name;
} elseif(property_exists($arSuperFilter, 'brand')) {
	$type = 'brand';
	$arBrandInfo = GetDataFactory::getSectionByCode($arSuperFilter->brand);
	$arCollectionInfo = GetDataFactory::getCollectionsByBrand($arBrandInfo->id);
	$replace[0] = $arBrandInfo->name;
} else {
	$type = 'default';
}
*/

$type = 'default';
if(property_exists($arSuperFilter, 'brand')) {
	$type = 'brand';
	$arBrandInfo = GetDataFactory::getSectionByCode($arSuperFilter->brand);
	$arCollectionInfo = GetDataFactory::getCollectionsByBrand($arBrandInfo->id);
	$replace[0] = $arBrandInfo->name;
}

if(property_exists($arSuperFilter, 'collection')) {
	$type = 'collection';
	$currCollection = current(array_filter($arCollectionInfo, function($e) use ($arSuperFilter){return ($e->code == $arSuperFilter->collection);}));
	if($currCollection){
		$arFrames = GetDataFactory::getFramesOrPushItems($currCollection->id, 'frames');
		$arPushs = GetDataFactory::getFramesOrPushItems($currCollection->id, 'pushs');
	}
	$replace[1] = $currCollection->name;
}
if(property_exists($arSuperFilter, 'colorFrame')) {
	$type = 'colorFrame';
	//$arFramesInfo = CElectrodomTools::GetInfoElements($arSuperFilter->colorFrame);
	$arCurrentFrame = current(array_filter($arFrames, function($e) use ($arSuperFilter){return ($e->id == $arSuperFilter->colorFrame);}));

	$replace[2] = $arCurrentFrame->name;
}
if(property_exists($arSuperFilter, 'colorPush')){
	$type = 'colorPush';
	// $arPushsInfo = CElectrodomTools::GetInfoElements($arSuperFilter->colorPush);
	$arCurrentPush = current(array_filter($arPushs, function($e) use ($arSuperFilter){return ($e->id == $arSuperFilter->colorPush);}));

	$replace[3] = $arCurrentPush->name;
}



$title = SeoFactory::getTitleByType($type, $replace);//str_replace($replacment, $replace, $arTemplatesSEO[$type]['title']);
$description = SeoFactory::getDescriptionByType($type, $replace);//str_replace($replacment, $replace, $arTemplatesSEO[$type]['description']);
$APPLICATION->SetTitle($title);

$arSections = GetDataFactory::getBrands(); 

?>

<script src="/bitrix/templates/bitronic_1.16.5/static/js/jquery.bxslider.min.js"></script>
<script src="/bitrix/templates/bitronic_1.16.5/static/js/bootstrap-colorpicker.min.js"></script>
<script src="/online-kit-socket/script.js"></script>
<!-- bxSlider CSS file -->
<link href="/bitrix/templates/bitronic_1.16.5/static/css/jquery.bxslider.min.css" rel="stylesheet" />
<link href="/bitrix/templates/bitronic_1.16.5/static/css/bootstrap-colorpicker.min.css" rel="stylesheet" />
<link href="/online-kit-socket/style.css" rel="stylesheet" />


<div class="row">
	<div class="col-md-12 description-information">
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<div>
		<p>Бренды</p>
		<div>
		
		<ul id="brans-list">
			<?foreach($arSections as $arSection):?>
			<?$class = ""; if(isset($arSuperFilter->brand) && $arSection['CODE'] == $arSuperFilter->brand) $class="active";?>
			<li 
				data-id="<?=$arSection['ID']?>"
				data-code="<?=$arSection['CODE']?>"
				data-name="<?=$arSection['NAME']?>"
				class="<?=$class;?>"
			><?=$arSection['NAME']?></li>
			<?endforeach;?>
		</ul>
		</div>
		</div>

		<div>
		<p>Коллекции</p>
		<ul id="collection-list">
			<?foreach($arCollectionInfo as $arSection):?>
			<?$class = ""; if(isset($arSuperFilter->collection) && $arSection->code == $arSuperFilter->collection) $class="active";?>
			<li 
				data-id="<?=$arSection->id?>"
				data-code="<?=$arSection->code?>"
				data-name="<?=$arSection->name?>"
				class="<?=$class;?>"
			><?=$arSection->name?></li>
			<?endforeach;?>
		</ul>
		</div>
	</div>
	<div class="col-md-1 col-sm-2 col-xs-5 block-images">
		<div class="frames">
		<p>
			Рамки
		</p>
			<div class="v-slider">
			<?if($arFrames):?>
				<?foreach($arFrames as $item):?>
					<img src="<?=$item->preview_picture?>" data-name="<?=$item->name?>" data-id="<?=$item->id?>" data-code="<?=$item->code?>" <?=(isset($arCurrentFrame) && $arCurrentFrame->id==$item->id)?'class="active"':'';?>>
				<?endforeach;?>
			<?endif;?>
			</div>
		</div>
	</div>
	<div class="col-md-1 col-sm-2 col-xs-5 block-images">
		<div class="pushs">
		<p>
			Клавиши
		</p>
			<div class="v-slider">
			<?if($arPushs):?>
				<?foreach($arPushs as $item):?>
					<img src="<?=$item->preview_picture?>" data-name="<?=$item->name?>" data-id="<?=$item->id?>" data-cod="<?=$item->code?>" <?=(isset($arCurrentPush) && $arCurrentPush->id==$item->id)?'class="active"':'';?>>
				<?endforeach;?>
			<?endif;?>
			</div>
		</div>
	</div>
	<div class="col-md-5 col-sm-7 col-xs-12 block-images">
		<div id="selector-wp" class="">
			<div class="mini-wp wp-0" data-class="wp-0"></div>
			<div class="mini-wp wp-1" data-class="wp-1"></div>
			<div class="mini-wp wp-2" data-class="wp-2"></div>
			<div class="mini-wp wp-3" data-class="wp-3"></div>
			<div class="mini-wp wp-4" data-class="wp-4"></div>
			<div class="mini-wp wp-5" data-class="wp-5"></div>
		</div>
		<p style="color:#b55; font-size:12px;">*Для изменения цвета обоев нажмите на область рамки и выключателя</p>
		<div id="complect-info">
			<div class="decor-items">
				<?if($arSuperFilter->colorFrame):?>
			<div class="decor-element frames-selected active" style="background-image:url(<?=$arCurrentFrame->preview_picture?>);"></div>
				<?else:?>
			<div class="decor-element frames-selected"></div>
				<?endif;?>
				<?if($arSuperFilter->colorPush):?>
			<div class="decor-element pushs-selected active" style="background-image:url(<?=$arCurrentPush->preview_picture?>);"></div>
				<?else:?>
			<div class="decor-element pushs-selected"></div>
				<?endif;?>
			</div>
		</div>
		<div id="complect-description" <?if(!$arCurrentPush && !$arCurrentFrame):?>style="display:none;"<?endif;?>>
			<div>Рамка: <span class="frame-color"><?=($arCurrentFrame)?$arCurrentFrame->name:'';?></span></div>
			<div>Клавиша: <span class="push-color"><?=($arCurrentPush)?$arCurrentPush->name:'';?></span></div>
			<div><button id="get-goods">Показать товары</button></div>
		</div>
	</div>
</div>
<input id="ajax_iblock_id_sku" type="hidden" value="39" name="ajax_iblock_id_sku">
<input id="ajax_iblock_id" type="hidden" value="39" name="ajax_iblock_id">
<div id="goods-items">
</div>


<script type="text/javascript">
$(document).ready(function(){
	try{
		<?if($arBrandInfo):?>
		DecorComplects.setInfo('brand', '<?=json_encode($arBrandInfo);?>');
		<?endif;if($currCollection):?>
		DecorComplects.setInfo('collection', '<?=json_encode($currCollection);?>');
		<?endif;?>
		DecorComplects.init('<?=json_encode(SeoFactory::getTemplates())?>');
	} catch(e){
		console.log('error');
		console.log(e);
	}

	$('#complect-info').colorpicker({customClass: 'colorpicker-2x', sliders: { saturation: { maxLeft: 200, maxTop: 200 }, hue: { maxTop: 200 }, alpha: { maxTop: 200 } }}).on('changeColor', function(e) { $('#complect-info')[0].style.backgroundColor = e.color.toHex(); });
});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>