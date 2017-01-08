<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

<?
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog"); 
//error_reporting(E_ALL);
$file = $_SERVER['DOCUMENT_ROOT']."/upload/csv/users.csv";
//$file = "upload/export/categories.xml";

//$xml = simplexml_load_file($file);

$arResult = parseCSVFile($file);
$arResult = parseName($arResult);
/*
foreach ($arResult['ITEMS'] as $arFields) {
    //createUser($arFields);
}
*/
p($arResult);



function parseCSVFile($path){
    $row = 1;
    $arHeaders = $arResult = array();
    
    if (($handle = fopen($path, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
            $num = count($data);
            if( empty($data[0]) )
                continue;
            if( $row == 1 ){
                foreach ($data as $k=>$value) {
                    $arHeaders[$k] = strtoupper($value);
                }
                $arResult['INFO']['HANDBOOKS'] = createHandbooksFields($arHeaders);
                $intCntFields = count($arHeaders);
                $row++;
                continue;
            }
            foreach ($data as $k=>$value) {
                $arResult['ITEMS'][$row][$arHeaders[$k]] = $value;
            }

            $row++;
        }
        fclose($handle);
        $arResult['INFO']['COUNT_FIELDS'] = $intCntFields;
        $arResult = clearCSVData( $arResult );
        return $arResult;
    } else 
     return array();
}

function clearCSVData($arRes){
    $intCntFields = $arRes['INFO']['COUNT_FIELDS'];
    foreach ( $arRes['ITEMS'] as $k => $arItem ) {
        if(count($arItem) != $intCntFields)
            unset($arRes['ITEMS'][$k]);
    }
    $arRes['ITEMS'] = array_values($arRes['ITEMS']);
    return $arRes;
}

function createHandbooksFields($arFields){
    $arHB = array();
    foreach ($arFields as $key => $value) {
        if( defined('IBLOCK_'.$value.'_ID') )
            $arHB[$value] = array('FIELD_NAME'=>$value, 'IBLOCK_ID'=>constant('IBLOCK_'.$value.'_ID') );
    }
    return $arHB;
}

function createUser($arFields){
    $user = new CUser;
    unset($arFields['FIO']);
    $ID = $user->Add($arFields);
    if (intval($ID) > 0)
        echo "Пользователь - $ID: ({$arFields['EMAIL']}) - успешно добавлен.<br>";
    else
        echo $user->LAST_ERROR;
}

function parseName($arResult){
    foreach ($arResult['ITEMS'] as $key => $value) {
        $arFio = explode(' ', $value['FIO']);
        if( count($arFio) == 1){
            $arResult['ITEMS'][$key]['NAME'] = $arFio[0];
            $arResult['ITEMS'][$key]['LAST_NAME'] = '';
            $arResult['ITEMS'][$key]['SECOND_NAME'] = '';
        } elseif( count($arFio) == 2 ) {
            $arResult['ITEMS'][$key]['NAME'] = $arFio[1];
            $arResult['ITEMS'][$key]['LAST_NAME'] = $arFio[0];
            $arResult['ITEMS'][$key]['SECOND_NAME'] = '';
        } elseif( count($arFio) == 3 ) {
            $arResult['ITEMS'][$key]['NAME'] = $arFio[1];
            $arResult['ITEMS'][$key]['LAST_NAME'] = $arFio[0];
            $arResult['ITEMS'][$key]['SECOND_NAME'] = $arFio[2];
        } else {
            $arResult['ITEMS'][$key]['NAME'] = '';
            $arResult['ITEMS'][$key]['LAST_NAME'] = '';
            $arResult['ITEMS'][$key]['SECOND_NAME'] = '';
        }
        $arResult['ITEMS'][$key]['ACTIVE'] = 'Y';
        $arResult['ITEMS'][$key]['LID'] = SITE_ID;
        $arResult['ITEMS'][$key]["GROUP_ID"] = array(3,4);
        $arResult['ITEMS'][$key]["PASSWORD"] = 'f_'.$arResult['ITEMS'][$key]['EMAIL'];
        $arResult['ITEMS'][$key]["CONFIRM_PASSWORD"] = 'f_'.$arResult['ITEMS'][$key]['EMAIL'];
        $arResult['ITEMS'][$key]["LOGIN"] = $arResult['ITEMS'][$key]['EMAIL'];
    }
    return $arResult;
}

?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>





