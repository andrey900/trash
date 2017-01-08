<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?    
	$Filter		= $arResult["FILTER"];
	$is_disks	= $Filter->GetParam("MAIN_IBLOCK") == 13;
?>
<form rel="1" onsubmit="CheckFields(this)" id="form_<?=$arParams["FILTER_NAME"]?>" method="get" action="<?=$arParams["REQUEST_PAGE_URL"]?>">

<?if($Filter->GetSelectedValues()->SelectedValuesCount() > 0):?>
<div class="your_choise">
	<h3>Ваш выбор</h3>
	<?foreach($Filter->GetSelectedValues()->Get() as $PropertID => $PropertyData):?>
    <?
        if(empty($PropertyData['VALUES']))
            continue;
    ?>
	<h4><?=$PropertyData["NAME"];?></h4>
		<?foreach($PropertyData["VALUES"] as $ValueID => $ValueData):?>
			<?$SelectedValues = $Filter->GetSelectedValues()->Get();?>
			<?if(!empty($ValueData["NAME"])):?>
			<p>
				<?if($ValueData["INDEX"] === "f"):?>
				от
				<?elseif($ValueData["INDEX"] === "t"):?>
				до
				<?endif;?>
				<a onclick="ClearFilter('<?=$Filter->GetName();?>[<?=$PropertID?>]','<?=$ValueID?>', this);" href="javascript:void(0);"><?=str_replace("_", "/", $ValueData["NAME"]);?></a>
			</p>
			<?endif;?>
			<?if($Filter->GetSelectedValues()->NeedHiddenFieldFor($PropertID, $ValueID)):?>
				<?if($PropertyData["MULTIPLE"] == "Y"):?>
				<input type="hidden" name="<?=$Filter->GetName()?>[<?=$PropertID?>][]" value="<?=$ValueID?>">
				<?else:?>
				<input type="hidden" name="<?=$Filter->GetName()?>[<?=$PropertID?>]<?=isset($ValueData["INDEX"])?"[".$ValueData["INDEX"]."]":"";?>" value="<?=$ValueID?>">
				<?endif;?>
			<?endif;?>
		<?endforeach;?>
	<?endforeach;?>
	<a href="javascript:void(0);" onclick="ClearAllFilters(this)" class="default">Сбросить фильтры</a>
	<?foreach($_REQUEST as $key=>$value):?>
		<?if($key != $arParams["FILTER_NAME"] && substr($key,0,5)!=="PAGEN"):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?endif;?>
	<?endforeach;?>
</div>
<?endif;?>
<?if($Filter->PropertiesCount()>0):?>
	<?foreach($Filter->GetProperties() as $obProperty):?>
	<?=$obProperty->GetHtml();?>
	<?endforeach;?>
<?endif;?>
</form>