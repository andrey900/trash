<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$strNavQueryString : "?");

$strNavQueryString = preg_replace("/brand=([\w\d]+)&/", "", $strNavQueryString);

$sFu = $arResult["sUrlPath"] . $strNavQueryStringFull;
// p([$arResult["sUrlPath"], $strNavQueryString, $strNavQueryStringFull]);
?>

<div class="hidden">
    <div class="start-item"><?=$arResult["NavFirstRecordShow"]?></div>
    <div class="stop-item"><?=$arResult["NavLastRecordShow"]?></div>
    <div class="all-items"><?=$arResult["NavRecordCount"]?></div>
</div>
<?/*
<!-- shop-pagination start -->
<ul class="shop-pagination text-center ptblr-10-30 hidden">
    <?if( $arResult['NavPageCount'] > 1 ):?>
    	<li><a href="<?=$sFu;?>"><i class="zmdi zmdi-chevron-left"></i></a></li>
    	<?for($i=1;$i<=$arResult['NavPageCount'];$i++){
    		$queryStr = $sFu.'PAGEN_'.$arResult["NavNum"].'='.$i;
    		if( $i ==1 )
    			$queryStr = $sFu;
    		$actClass = ($arResult['NavPageNomer'] == $i)?"class=\"active\"":"";
    		if( $actClass )
    			$queryStr = "javascript:void(0)";
    	?>
    		<li <?=$actClass?>><a href="<?=$queryStr?>"><?=($i<10)?"0".$i:$i;?></a></li>
    	<?}?>
    	<li><a href="<?=$sFu.'PAGEN_'.$arResult["NavNum"].'='.$arResult['NavLastRecordShow'];?>"><i class="zmdi zmdi-chevron-right"></i></a></li>
    <?endif;?>
</ul>
<!-- shop-pagination end -->
*/?>

<ul class="shop-pagination text-center ptblr-10-30 hidden">
<?
$bFirst = true;

    if ($arResult["NavPageNomer"] > 1):
        if($arResult["bSavePage"]):
?>
            <li><a class="modern-page-previous" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><i class="zmdi zmdi-chevron-left"></i></a></li>
<?
        else:
            if ($arResult["NavPageNomer"] > 2):
?>
            <li><a class="modern-page-previous" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><i class="zmdi zmdi-chevron-left"></i></a></li>
<?
            else:
?>
            <li><a class="modern-page-previous" href="<?=$sFu?>"><i class="zmdi zmdi-chevron-left"></i></a></li>
<?
            endif;
        
        endif;
        
        if ($arResult["nStartPage"] > 1):
            $bFirst = false;
            if($arResult["bSavePage"]):
?>
            <li><a class="modern-page-first" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1">1</a></li>
<?
            else:
?>
            <li><a class="modern-page-first" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
<?
            endif;
            if ($arResult["nStartPage"] > 2):
/*?>
            <span class="modern-page-dots">...</span>
<?*/
?>
            <li><a class="modern-page-dots" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nStartPage"] / 2)?>">...</a></li>
<?
            endif;
        endif;
    endif;

    do
    {
        if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
?>
        <li class="active"><a href="javascript:void(0);"><?=$arResult["nStartPage"]?></a></li>
<?
        elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
?>
        <li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="<?=($bFirst ? "modern-page-first" : "")?>"><?=$arResult["nStartPage"]?></a></li>
<?
        else:
?>
        <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"<?
            ?> class="<?=($bFirst ? "modern-page-first" : "")?>"><?=$arResult["nStartPage"]?></a></li>
<?
        endif;
        $arResult["nStartPage"]++;
        $bFirst = false;
    } while($arResult["nStartPage"] <= $arResult["nEndPage"]);
    
    if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
        if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
            if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
/*?>
        <span class="modern-page-dots">...</span>
<?*/
?>
        <li><a class="modern-page-dots" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] + ($arResult["NavPageCount"] - $arResult["nEndPage"]) / 2)?>">...</a></li>
<?
            endif;
?>
        <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><?=$arResult["NavPageCount"]?></a></li>
<?
        endif;
?>
        <li><a class="modern-page-next" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><i class="zmdi zmdi-chevron-right"></i></a></li>
<?
    endif;?>
</ul>