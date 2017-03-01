<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (empty($arResult)) return;

if( $arResult['SPECIAL'] ){
	$subMenu = $arResult['SPECIAL'];
	unset($arResult['SPECIAL']);
}
?>

<nav id="dropdown">
    <ul>
    <?foreach($arResult as $arItem):
    if( $arItem["IS_PARENT"] ):?>
    <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>	
        <ul>
            <?foreach($subMenu[$arItem['PARAMS']['FROM_IBLOCK']] as $subItems):?>
            <li>
                <a href="<?=$subItems['LINK']?>"><?=$subItems['TEXT']?></a>
            </li>
            <?endforeach;?>
        </ul>
    </li>
    <?else:?>
    <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
    <?endif;?>
    <?endforeach?>
    </ul>
</nav>