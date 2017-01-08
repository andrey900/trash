<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$Property = $arParams["PROPERTY"];?>
<?$cnt = $Property->ValuesCount();
if($cnt > 0):?>
<div class="vendors_form">
        <h2><?=$Property->GetParam("TITLE");?></h2>
        <div <?=$cnt>10?'class="block"':''?>>
		<table width="100%">
        <?foreach($Property->GetValues() as $arVal):?>
        <tr>
        <td class="filter_el">
        	<?$is_check = $Property->IsValueSelected($arVal['ID'])?'disabled="disabled" checked="checked"':"";?>
   			<input <?=$is_check?> onchange="CustomFilterSubmitForm(false,false,this,<?=$Property->GetParams("MULTIPLE")==="Y"?'true':'false'?>);" type="checkbox" name="<?=$arParams["FILTER_NAME"]?>[<?=$Property->GetID()?>][]" value="<?=$arVal['ID']?>" id="label_<?=$Property->GetID()?>_<?=$arVal['ID']?>">
            <label for="label_<?=$Property->GetID()?>_<?=$arVal['ID']?>"><?=$arVal["NAME"]?></label>
            <span>(<?=$arVal["~COUNT"]?>)</span>
        </td>
        </tr>
        <?endforeach;?>
        </table>
        </div>
        </div>
<?endif;?>
