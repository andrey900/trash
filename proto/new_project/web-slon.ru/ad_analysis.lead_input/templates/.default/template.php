<form class="promocode-lead-input" action="." method="post">
	<div class="success"><?=$arResult["success"]?></div>
	<div class="error"><?=$arResult["error"]?></div>
	<table class="data-table">
		<tr>
			<td><?=GetMessage("WEBSLON_AD_ANALYSIS_TIP_LIDA")?></td>
			<td>
				<?foreach($arResult["dataTypes"] as $key=>$val):?>
					<input id="DATA_TYPE_<?=$key?>" name="DATA_TYPE" type="radio" <?if($val["CHECKED"]) echo 'checked="checked"'?> value="<?=$key?>"></input><label for="DATA_TYPE_<?=$key?>"><?=$val["NAME"]?></label><br/>
				<?endforeach;?>
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("WEBSLON_AD_ANALYSIS_VID_LIDA")?></td>
			<td>
				<?foreach($arResult["leadTypes"] as $key=>$val):?>
					<input id="LEAD_TYPE_<?=$key?>" name="LEAD_TYPE" type="radio" <?if($val["CHECKED"]) echo 'checked="checked"'?> value="<?=$key?>"></input><label for="LEAD_TYPE_<?=$key?>"><?=$val["NAME"]?></label><br/>
				<?endforeach;?>
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("WEBSLON_AD_ANALYSIS_FIO")?></td>
			<td><input class="fio" name="FIO" type="text" value="<?=$arResult["FIO"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("WEBSLON_AD_ANALYSIS_TELEFON")?></td>
			<td><input class="phone" name="PHONE" type="text" value="<?=$arResult["PHONE"]?>" /></td>
		</tr>
		<tr>
			<td>Email:</td>
			<td><input class="email" name="EMAIL" type="text" value="<?=$arResult["EMAIL"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("WEBSLON_AD_ANALYSIS_PROMOKOD")?></td>
			<td><input class="promocode" name="PROMO_CODE_ID" type="text" value="<?=$arResult["PROMO_CODE_ID"]?>" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="submit" value="<?=$arResult["BUTTON_CAPTION"]?>" /></td>
		</tr>
	</table>
</form>
<?
//echo '<pre>'; print_r($arResult); echo '</pre>';
?>