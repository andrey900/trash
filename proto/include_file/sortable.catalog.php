<?php extract('arResult'); extract('arMESS'); //p($arResult,false);

if($_REQUEST["sort"]){
  $sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arResult) ? $arResult[ToLower($_REQUEST["sort"])][0] : "name";
  $sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arResult[$sort][1];
}else{
  $sort = "CATALOG_PRICE_1";
  $sort_order = "asc";
}

foreach( $arMESS as $key=>$val){
  $MESS['SECT_SORT_'.ToLower($key)] = $val;
}

?>
<div class="sort">
   Сортировать по:
    <? foreach ($arResult as $key => $val):
      $className_sub = "";
      $className = ($sort == $val[0]) ? ' asc' : '';
      if ($className)
      $className_sub = ($sort_order == 'asc') ? 'asc' : 'desc';
      $newSort = ($sort == $val[0]) ? ($sort_order == 'desc' ? 'asc' : 'desc') : $arResult[$key][1];
      ?>
      <a href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort,  array('sort', 'order'))?>"  rel="nofollow" class="<?=$className_sub?>"><?=GetMessage('SECT_SORT_'.$key)?></a>
    <? endforeach;?>       
</div>
<?/*
	  <?$APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include/sortable.php",
            Array(
                "arResult" => array("name" => Array("name", "asc"),
                                    "price" => Array('CATALOG_PRICE_1', "asc"),
                               ),
                "arMESS" => array("name" => 'По имени',
                                  "price" => 'По цене',
                               ),
            ),
            Array("MODE"=>"php")
);?>
*/?>