<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( !function_exists('find_children') ){
	function find_children($type, array &$menu){
		$arRes = [];
		foreach ($menu as $k=>$item) {
			if( $item['DEPTH_LEVEL'] == 1 ) continue;
			if( $item['PARAMS']['FROM_IBLOCK'] == $type ){
				$arRes[] = $item;
				unset($menu[$k]);
			}
		}
		return $arRes;
	}
}

$tmp = [];
// resort menu
foreach ($arResult as $key => $value) {
	if( $value['IS_PARENT'] )
		$tmp[$value['PARAMS']['FROM_IBLOCK']] = find_children($value['PARAMS']['FROM_IBLOCK'], $arResult);
}

$arResult['SPECIAL'] = $tmp;

