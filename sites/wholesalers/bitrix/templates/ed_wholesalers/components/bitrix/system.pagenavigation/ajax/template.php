<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<nav aria-label="Page navigation">
  <span class="nav-name"><?=$arResult['NavTitle']?>: </span>
  <ul class="pagination">
  	<?for($i=1; $i <= $arResult['NavPageCount']; $i++){?>
  		<li class="<?=($i == $arResult['NavPageNomer'])?'active':'';?>"><a href="<?=$arResult['sUrlPathParams'].'p='.$i;?>"><?=$i;?></a></li>
  	<?}?>
  </ul>
</nav>