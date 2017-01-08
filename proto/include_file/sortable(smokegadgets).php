<?php extract('arResult'); extract('arMESS'); //p($arResult,false);

if($_REQUEST["sort"]){
  $GLOBALS['sort_catalog'] = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arResult) ? $arResult[ToLower($_REQUEST["sort"])][0] : "name";
  $GLOBALS['sort_catalog_order'] = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arResult[$sort][1];
}else{
  $GLOBALS['sort_catalog'] = "CATALOG_PRICE_1";
  $GLOBALS['sort_catalog_order'] = "asc";
}

foreach( $arMESS as $key=>$val){
  $MESS['SECT_SORT_'.ToLower($key)] = $val;
}

?>

<!-- Один селект -->
  <div class="select">
    <select class="sortable_filter">
      <? foreach ($arResult as $key => $val):?>
      <option value="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$val['1'],  array('sort', 'order'))?>" <?=($GLOBALS['sort_catalog']==$val[0] && $GLOBALS['sort_catalog_order']==$val[1])?'selected':'';?>><?=GetMessage('SECT_SORT_'.$key)?></option>
      <? endforeach;?>
    </select>
  </div>
<!-- Конец Один селект -->
<script type="text/javascript">
  try{
    $(document).on('change', 'select.sortable_filter', function(){
      window.location.href = $(this).val();
    });
  } catch(e) {
    console.log('Error in sortable filter');
  }
</script>