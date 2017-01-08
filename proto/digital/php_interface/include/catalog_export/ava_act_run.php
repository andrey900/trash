<?php
//<title>ava.ua (action)</title>

set_time_limit(0);

if(strlen($SETUP_FILE_NAME) <= 0) {
    $arRunErrors[] = 'Укажите файл для сохранения результата.';
} elseif(preg_match(BX_CATALOG_FILENAME_REG, $SETUP_FILE_NAME)) {
    $arRunErrors[] = 'Имя файла экспорта содержит запрещенные символы';
} else {
    $SETUP_FILE_NAME = Rel2Abs("/", $SETUP_FILE_NAME);
}

if(empty($arRunErrors)) {
    CheckDirPath($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME);

    if(!$fp = @fopen($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, "wb")) {
        $arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, 'Невозможно открыть файл #FILE# для записи');
    } else {
        if(!@fwrite($fp, '<?if (!isset($_GET["referer1"]) || strlen($_GET["referer1"])<=0) $_GET["referer1"] = "yandext";?>')) {
            $arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, 'Запись в файл #FILE# невозможна');
            @fclose($fp);
        } else {
            fwrite($fp, '<? $strReferer1 = htmlspecialchars($_GET["referer1"]); ?>');
            fwrite($fp, '<?if (!isset($_GET["referer2"]) || strlen($_GET["referer2"]) <= 0) $_GET["referer2"] = "";?>');
            fwrite($fp, '<? $strReferer2 = htmlspecialchars($_GET["referer2"]); ?>');
        }
    }
}

if(empty($arRunErrors)) {

    @fwrite($fp, '<? header("Content-Type: text/xml; charset=utf-8");?>');
    @fwrite($fp, '<? echo "<"."?xml version=\"1.0\" encoding=\"utf-8\"?".">"?>');
    @fwrite($fp, "<sales>\n");
    
    $arResult = array();
    $arSelect = Array(
        'ID',
        'NAME',
        'PREVIEW_TEXT',
        'DETAIL_PAGE_URL',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'DATE_ACTIVE_FROM',
        'DATE_ACTIVE_TO',
        'PROPERTY_GOODS'
    );
    $arFilter = Array(
        'IBLOCK_ID' => 8, 
        'ACTIVE_DATE' => 'Y', 
        'ACTIVE' => 'Y'
    );
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    
    while($itemRes = $res->GetNext()) {
        
        if(!empty($itemRes['DETAIL_PICTURE'])) {
            $fileData = CFile::GetFileArray($itemRes['DETAIL_PICTURE']);
        } elseif(!empty($itemRes['PREVIEW_PICTURE'])) {
            $fileData = CFile::GetFileArray($itemRes['PREVIEW_PICTURE']);
        }
        
        $resultData = '';
        $resultData .= "<name>".$itemRes['NAME']."</name>\n";
        $resultData .= "<link>http://".$_SERVER['HTTP_HOST'].$ar_iblock['SERVER_NAME'].htmlspecialcharsbx($itemRes["~DETAIL_PAGE_URL"])."</link>\n";
        $resultData .= "<preview>".$itemRes['PREVIEW_TEXT']."</preview>\n";
        $resultData .= "<description>".$itemRes['DETAIL_TEXT']."</description>\n";
        $resultData .= "<date_start>".$DB->FormatDate($itemRes['DATE_ACTIVE_FROM'], 'DD.MM.YYYY HH:MI:SS', 'YYYY-MM-DD')."</date_start>\n";
        $resultData .= "<date_end>".$DB->FormatDate($itemRes['DATE_ACTIVE_TO'], 'DD.MM.YYYY HH:MI:SS', 'YYYY-MM-DD')."</date_end>\n";
        $resultData .= "<picture>http://".$_SERVER['HTTP_HOST'].$fileData["SRC"]."</picture>\n";

        $resultData .= "<products>\n";
        
        $arGoodsSection = array();
        
        $arFilterGoods = array(
            'IBLOCK_ID' => SHARE_CATALOG_IBLOCK_ID,
            'ID' => $itemRes['PROPERTY_GOODS_VALUE'],
            'ACTIVE' => 'Y'
        );
        $arSelectGoods = array('ID', 'DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID');
        $resGoods = CIBlockElement::GetList(Array(), $arFilterGoods, false, false, $arSelectGoods);
        while($itemResGoods = $resGoods->GetNext()) {
            $arGoodsSection[] = $itemResGoods['IBLOCK_SECTION_ID'];
            $resultData .= "<product link=\"http://".$_SERVER['HTTP_HOST'].$itemResGoods['DETAIL_PAGE_URL']."\" name=\"".$itemResGoods['ID']."\"/>\n";
        }
        $resultData .= "</products>\n";
        
        $arFilterSection = array(
            'IBLOCK_ID' => SHARE_CATALOG_IBLOCK_ID,
            'ID' => $arGoodsSection,
            'ACTIVE' => 'Y'
        );
        
        $resultData .= "<categories>\n";
        $resSection = CIBlockSection::GetList(Array(), $arFilterSection, true);
        while($itemResSection = $resSection->GetNext()) {
            $resultData .= "<category name=\"".$itemResSection['NAME']."\"/>\n";
        }
        $resultData .= "</categories>\n";
        
            
        @fwrite($fp, "<sale id=\"".$itemRes['ID']."\">\n");
        
        @fwrite($fp, $resultData);
        
        @fwrite($fp, "</sale>\n");
    }

    @fwrite($fp, "</sales>\n");
    @fclose($fp);

}

?>