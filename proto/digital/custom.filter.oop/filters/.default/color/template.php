<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?
$dbRes = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>13, "ACTIVE"=>"Y"), false, false, array("ID", "IBLOCK_ID", "PREVIEW_PICTURE"));
while($arRes = $dbRes->GetNext())
{
    $arColors[$arRes['ID']] = $arRes;
}
?>

<?$Property = $arParams["PROPERTY"];?>
<?$cnt = $Property->ValuesCount();
if($cnt > 0):?>
    <div class="form-part">
        <span class="form-text"><?=$Property->GetParam("TITLE");?></span>
        <ul class="col">
        <?foreach($Property->GetValues() as $arVal):?>
            <?$is_check = $Property->IsValueSelected($arVal['ID'])?'disabled="disabled" checked="checked"':"";?>
            <li>
                <a onclick="getcolor('<?=$Property->GetID()?>_<?=$arVal['ID']?>', this);"><?=CFile::ShowImage($arColors[$arVal['ID']]['PREVIEW_PICTURE']);?></a>
                <input style="display: none" <?=$is_check?> onchange="CustomFilterSubmitForm(false,false,this,<?=$Property->GetParams("MULTIPLE")==="Y"?'true':'false'?>);" type="checkbox" name="<?=$arParams["FILTER_NAME"]?>[<?=$Property->GetID()?>][]" value="<?=$arVal['ID']?>" id="label_<?=$Property->GetID()?>_<?=$arVal['ID']?>">
            </li>
        <?endforeach;?>
        </ul>
    </div>
<?endif;?>