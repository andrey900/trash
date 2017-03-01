<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (empty($arResult)) return;

if( $arResult['SPECIAL'] ){
	$subMenu = $arResult['SPECIAL'];
	$cntItems = count($subMenu['catalog']);
	$cntInS = $cntItems / 4;
	unset($arResult['SPECIAL']);
}
?>

<nav id="primary-menu">
	<ul class="main-menu text-center">
<?foreach($arResult as $arItem):
	if( $arItem["IS_PARENT"] ):?>
    <li class="mega-parent"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>	
    <div class="mega-menu-area clearfix">
    <div class="mega-menu-link mega-menu-link-4 f-left">
        <?for($i=1; $i <= 4; $i++){?>
        <ul class="single-mega-item">
            <?$_t = array_slice($subMenu['catalog'], ($i-1)*2, 2);foreach($_t as $subItems):?>
            <li>
                <a href="<?=$subItems['LINK']?>"><?if($subItems['PARAMS']['IMAGE']):?><img src="http://pod-potol.com/upload/medialibrary/6b1/6b1742b4491751a51d7eaba965c87d9c.jpg" width="150"><?else:?><?=$subItems['TEXT']?><?endif;?></a>
            </li>
            <?endforeach;?>
        </ul>
        <? }?>
        
    </div>
</div>
<?else:?>
    <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
<?endif;?>
<?endforeach?>
	</ul>
</li>
