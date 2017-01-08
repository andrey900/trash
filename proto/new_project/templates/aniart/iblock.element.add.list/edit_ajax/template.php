<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);
//echo "<pre>";print_r($arResult);echo"</pre>";
$colspan = 4;
if ($arResult["CAN_EDIT"] == "Y") $colspan++;
if ($arResult["CAN_DELETE"] == "Y") $colspan++;
?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<div id="textareaFeedback"></div>
<select name="SECTION_ID" class="select-section">
	<?
$iblock = $arParams['IBLOCK_ID'];
$res = CIBlockSection::GetList(
Array("LEFT_MARGIN" => "ASC", "RIGHT_MARGIN"=>"DESC"),//
Array("IBLOCK_ID" => $iblock, "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "CNT_ACTIVE" => 'Y', "ELEMENT_SUBSECTIONS" => "Y"),
true,
Array("ID", "NAME", 'DEPTH_LEVEL', 'LEFT_MARGIN', 'RIGHT_MARGIN')
);
while ($arSection = $res->GetNext()) {
	$select = ($arSection["ID"]==$_GET["SECTION_ID"])?"selected":"";
	$str = '';
	if($arSection['DEPTH_LEVEL'] > 1){
		for( $i=1; $i<$arSection['DEPTH_LEVEL']; $i++ )
			$str .= '. ';
	}
	//$iCnt2 = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $iblock, "ACTIVE" => "Y", "SECTION_ID" => $arSection['ID']), array('ID'));
	//$iCnt2 = $iCnt2->GetNext();
	print_r($arSection['DEPTH_LEVEL']);
	if ($arSection['ELEMENT_CNT'] > 0 && $arSection['DEPTH_LEVEL'] > 1)
		echo '<option value="'.$arSection['ID'].'" '.$select.' >'.$str.$arSection['NAME'] . "</option>";
	elseif( $arSection['ELEMENT_CNT'] > 0)
		echo '<option value="'.$arSection['ID'].'" '.$select.' class="sbold">'.$arSection['NAME'] . "</option>";
}
?> 
</select>


<table class="data-table">
<?if($arResult["NO_USER"] == "N"):?>
	<thead>
		<tr>
			<td<?=$colspan > 1 ? " colspan=\"".$colspan."\"" : ""?>><h1><?=GetMessage("IBLOCK_ADD_LIST_TITLE")?></h1></td>
		</tr>
	</thead>
	<tbody>
	<?if (count($arResult["ELEMENTS"]) > 0):?>
		<?foreach ($arResult["ELEMENTS"] as $arElement):?>
		<tr>
			<td><span class="article"><?=$arElement["XML_ID"]?></span></td>
			<td><span class="full-name"><?=$arElement["NAME"]?></span></td>
			<td><span class="live-input short-name" name="142" elem-id="<?=$arElement["ID"];?>"><?=$arElement["PROPERTY_142"]?></span></td>
			<td><small><?=is_array($arResult["WF_STATUS"]) ? $arResult["WF_STATUS"][$arElement["WF_STATUS_ID"]] : $arResult["ACTIVE_STATUS"][$arElement["ACTIVE"]]?></small></td>
			<?if ($arResult["CAN_EDIT"] == "Y"):?>
			<td><?if ($arElement["CAN_EDIT"] == "Y"):?><a href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arElement["ID"]?>"><?=GetMessage("IBLOCK_ADD_LIST_EDIT")?><?else:?>&nbsp;<?endif?></a></td>
			<?endif?>
			<?if ($arResult["CAN_DELETE"] == "Y"):?>
			<td><?if ($arElement["CAN_DELETE"] == "Y"):?><a href="?delete=Y&amp;CODE=<?=$arElement["ID"]?>&amp;<?=bitrix_sessid_get()?>" onClick="return confirm('<?echo CUtil::JSEscape(str_replace("#ELEMENT_NAME#", $arElement["NAME"], GetMessage("IBLOCK_ADD_LIST_DELETE_CONFIRM")))?>')"><?=GetMessage("IBLOCK_ADD_LIST_DELETE")?></a><?else:?>&nbsp;<?endif?></td>
			<?endif?>
		</tr>
		<?endforeach?>
	<?else:?>
		<tr>
			<td<?=$colspan > 1 ? " colspan=\"".$colspan."\"" : ""?>><?=GetMessage("IBLOCK_ADD_LIST_EMPTY")?></td>
		</tr>
	<?endif?>
	</tbody>
<?endif?>
	<tfoot>
		<tr>
			<td<?=$colspan > 1 ? " colspan=\"".$colspan."\"" : ""?>><?if ($arParams["MAX_USER_ENTRIES"] > 0 && $arResult["ELEMENTS_COUNT"] < $arParams["MAX_USER_ENTRIES"] && false):?><a href="<?=$arParams["EDIT_URL"]?>?edit=Y"><?=GetMessage("IBLOCK_ADD_LINK_TITLE")?></a><?else:?><?=GetMessage("IBLOCK_LIST_CANT_ADD_MORE")?><?endif?></td>
		</tr>
	</tfoot>
</table>
<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>