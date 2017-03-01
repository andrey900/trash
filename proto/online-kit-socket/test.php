<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

define("DELIMITER", ";");
$file = 'test-vikluchateli.csv';

if (($handle = fopen($file, "r")) !== FALSE) {
	$fileData = array();

	if(($data = fgetcsv($handle, 0, DELIMITER)) !== false ){
		$fieldCnt = count($data);

    	for ($c=0; $c < $fieldCnt; $c++) {
            $fileFields[$c] = $data[$c];
        }
	}

    while (($data = fgetcsv($handle, 0, DELIMITER)) !== FALSE ) {
        $fileData[] = array_combine($fileFields, $data);
    }

    fclose($handle);
}

foreach ($fileData as $arItem) {
	if(!$arItem['ARTICLES'])
		continue;

	$arItem['ARTICLES'] = preg_replace("/(^\||\|$)/s", "", $arItem['ARTICLES']);
	$arArticles = explode("|", $arItem['ARTICLES']);
	// p($arArticles);
	p([$arArticles, $arItem['SECTION_ID']]);
	//$arRes = CElectrodomTools::_GetInfoElements(false, ["ID"], ['IBLOCK_ID'=>39, 'PROPERTY_ARTICLE'=>$arArticles, "SECTION_ID"=>$arItem['SECTION_ID'], "INCLUDE_SUBSECTIONS"=>"Y" ]);
	$arRes = CElectrodomTools::_GetInfoElements(false, ["ID"], ['IBLOCK_ID'=>65, 'XML_ID'=>$arItem['IE_XML_ID']]);
	//CIBlockElement::SetPropertyValuesEx((int)$id, 65, ['1066' => 1]);
	
	if($arRes && $arArticles){
		$id = key($arRes);
		//p([$id, 65, ['1074' => $arArticles]]);
		//CIBlockElement::SetPropertyValuesEx((int)$id, 65, ['1074' => $arArticles]);
	}
}
