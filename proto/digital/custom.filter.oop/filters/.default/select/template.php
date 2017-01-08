<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$Property = $arParams["PROPERTY"];?>
<?$cnt = $Property->ValuesCount();
if($cnt > 0):?>
<div class="form-part">
    <span class="form-text"><?=$Property->GetParam("TITLE");?></span>
    <select onchange="CustomFilterSubmitForm(false,false,this,<?=$Property->GetParams("MULTIPLE")==="Y"?'true':'false'?>);" class="size-form" name="<?=$arParams["FILTER_NAME"]?>[<?=$Property->GetID()?>][]" size="1">
        <option value="0">-</option>
        <?foreach($Property->GetValues() as $arVal):?>
            <option value="<?=$arVal['ID']?>"><?=$arVal["NAME"]?></option>
        <?endforeach;?>
    </select>
</div>
<?endif;?>
            

