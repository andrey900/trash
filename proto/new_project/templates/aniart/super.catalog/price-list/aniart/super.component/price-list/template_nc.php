<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
header('Content-transfer-encoding: binary');	
header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename="'.$arResult['FILE_NAME'].'"');
?>
<?if(!empty($arResult["PRICE_SECTIONS"])):?>
	<table cellpadding="1" cellspacing="0" border="0" id="commercial">
		<tr>
			<td></td>
			<td style="height: 100px;">
				&nbsp;<br>
				&nbsp;&nbsp;&nbsp;<img src="http://ssr.bi3x.org/_tpln/img/top_logo_text.png" border="0" width='177' height='78'>			
			</td>
			<td colspan="<?if($arParams['SHOW_IMAGES'] == 'Y'):?>3<?else:?>2<?endif;?>" style="text-align: right;">
				<br>
				<b>г. Москва, ул. Новгородская дом 1, стр.2<br>
				Телефон: +7 (495) 363-40-48<br>
				Режим работы: пн-пт, с 9:00 до 18:00<br>
				Отправить заявку: <a href='mailto:price@ssr-russia.ru'>price@ssr-russia.ru</a> <b>
			</td>
		</tr>

		<tr>
			<td colspan="<?if($arParams['SHOW_IMAGES'] == 'Y'):?>5<?else:?>4<?endif;?>">
				&nbsp;
			</td>
		</tr>	
		
		<?if($arParams['SHOW_IMAGES_PATH'] == 'Y'):?>
		<tr>
			<td colspan="<?if($arParams['SHOW_IMAGES'] == 'Y'):?>6<?else:?>5<?endif;?>">
				<b style="color:#dd0000">Файловый архив будет доступен c <?=$arResult['ZIP_ARCHIVE']['DATE_ACCESS']?> <a href="<?=$arResult['ZIP_ARCHIVE']['LINK_TO_ZIP']?>">по ссылке </a></b>
			</td>		
		</tr>
		<?endif?>
		
		<tr>
			<td width="50"><b>Код</b></td>
			<td width="600" colspan="2"><b>Название</b></td>
			<?if($arParams['SHOW_IMAGES'] == 'Y'):?>
				<td width="65"><b>Рисунок</b></td>
			<?endif;?>
			<td><b>Цена в руб.</b></td>
			<?if($arParams['SHOW_IMAGES_PATH'] == 'Y'):?>
				<td><b>Изображение в файловом архиве</b></td>
			<?endif;?>
		</tr>
			
		<?foreach($arResult["PRICE_SECTIONS"] as $arSection):?>
			<tr>
				<td colspan="<?if($arParams['SHOW_IMAGES'] == 'Y'):?>4<?else:?>3<?endif;?>">
					<b><?=$arSection['NAME']?></b>
				</td>
			</tr>
			<?if(count($arResult['ITEMS'][$arSection['ID']])):?>
				<?foreach($arResult["ITEMS"][$arSection['ID']] as $arItem):?>
					<tr>
						<td><?=$arItem['XML_ID']?></td>
						<td colspan="2"><?if($arParams['SHOW_LINKS'] == 'Y'):?><a href="http://<?=SITE_SERVER_NAME?><?=$arItem['DETAIL_PAGE_URL']?>"><?endif;?><?=$arItem['NAME']?><?if($arParams['SHOW_LINKS'] == 'Y'):?></a><?endif;?></td>
						<?if($arParams['SHOW_IMAGES'] == 'Y'):?>
							<td style="<?if($arItem['DETAIL_PICTURE']['height']):?>height: <?=$arItem['DETAIL_PICTURE']['height']?>px;<?endif;?>">
								<?if($arItem['DETAIL_PICTURE']):?>
									<img src="http://<?=SITE_SERVER_NAME?><?=$arItem['DETAIL_PICTURE']['src']?>" width="<?=$arItem['DETAIL_PICTURE']['width']?>" height="<?=$arItem['DETAIL_PICTURE']['height']?>" >
								<?endif;?>
							</td>
						<?endif;?>
						<td><?=$arItem['PRICE']?></td>
						<?if($arParams['SHOW_IMAGES_PATH'] == 'Y'):?>
							<td>
								<?if($arItem['DETAIL_PICTURE_PATH']):?>
									<?=$arItem['DETAIL_PICTURE_PATH']?>
								<?endif;?>
							</td>
						<?endif;?>
					</tr>
				<?endforeach;?>
			<?endif;?>
		<?endforeach;?>
	</table>
<?endif;?>