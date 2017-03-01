<?extract('items');extract('name');?>
<tr><td colspan="5" class="text-center h3 header-table"><b><?=$name?></b></td></tr>
<tr><th class="text-center">Изображение</th><th class="text-center">Наименование</th><th class="text-center">Цена</th><th width="70">Кол-во</th><th>Купить</th></tr>
<?foreach($items as $arItem):?>
<tr>
	<td class="text-center">
		<?if($arItem['DETAIL_PICTURE']):?>
		<img src="<?=CFile::GetPath($arItem['DETAIL_PICTURE']);?>" height="50">
		<?else:?>
		<img src="/online-kit-socket/no_image.gif" height="50">
		<?endif;?>
	</td>
	<td><a href="<?=$arItem['DETAIL_PAGE_URL']?>" target="_blank"><?=$arItem['NAME']?></a></td>
	<td width="75" class="text-center"><?=number_format($arItem['CATALOG_PRICE_1'], 2, ',', ' ')?></td>
	<td>
		<input type="text" onchange="$(this).parent().next().children().attr('quantity', $(this).val())" value="1" style="max-width: 40px" max="0" min="0" maxlength="18" size="3">
	</td>
	<td width="150">
		<a rel="nofollow" href="<?=$arItem['DETAIL_PAGE_URL']?>?action=ADD2BASKET&id=<?=$arItem['ID']?>" id="b-<?=$arItem['ID']?>" onclick="yaCounter19955539.reachGoal('ORDER_SOCKET'); return true;" class="button1 ajax_add2basket"><span>В корзину</span></a>
	</td>
</tr>
<?endforeach;?>