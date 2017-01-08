<?php
header('Content-Type: text/html; charset=utf-8');

function p($params){
	echo "<pre>";
	print_r($params);
	echo "</pre>";
}

function full_trim($str){
    return trim(preg_replace('/\s{2,}/', ' ', $str));
}

$row = 1;
if (($handle = fopen("ee_import.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $i = 1;
        
        if( $row == 1 ){
        	foreach($data as $k=>$key){
        		$arSs[$k] = $key;
        	}
        } else {
        	foreach($data as $key => $value){
        		$arRes[$row][$arSs[$key]] = $value;
        	}
        	$arRes[$row]['count'] = count($arRes[$row]);
        }

        $row++;
    }
    fclose($handle);
}

$_countProp = count($arSs);

foreach ( $arRes as $key => $value ) {
	if( empty($value['Title']) || $value['count']!= $_countProp){
		unset($arRes[$key]); continue;
	}

	if( !empty($value['images']) ){
		$arImg = explode(',', $value['images']);
		$arRes[$key]['img'] = $arImg;
	}
}
$arRes = array_values($arRes);

foreach ($arRes as $key => $value) {
	if( $value['lang'] == 'en' ){
		echo "<hr>";
		echo "Add to db new \"EN\" post! ";
		$id = $key;
	} else {
		echo "Add to db ";
		$tid = $key;
		echo "Create link to $id this $tid ";
	}
}

//p( count($arSs) );
p($arRes);

?>