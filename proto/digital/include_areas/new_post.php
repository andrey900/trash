<?
extract('arResult');//p($arResult,false);
/*************************************/
include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/aniart/novaposhta/NovaPoshtaApi2.php");
include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/aniart/novaposhta/NovaPoshtaApi2Areas.php");
use LisDev\Delivery\NovaPoshtaApi2;

global $LANG, $TRANSLATE;

if( !defined('DOP_NEW_POST_KEY') )
    define("DOP_NEW_POST_KEY", '82b7e8c0ee63d0e606a2d6f330a7895a'); // ключ новой почты
//define("ORDER_NP_WARENHOUSE", 10);

if( isset($arResult['CITY_ID']) && (int)$arResult['CITY_ID'] > 0 )
    $arLocationInfo = CSaleLocation::GetByID( $arResult['CITY_ID'] );
elseif( isset($arResult['CITY_NAME']) && !empty($arResult['CITY_NAME']) )
    $arLocationInfo['CITY_NAME'] = trim($arResult['CITY_NAME']);
else
    return true;

$arReturn = array();
    
$arCityRes = CAniartNewPost::getWarehouseName($arLocationInfo['CITY_NAME']);
    
$np = new NovaPoshtaApi2(DOP_NEW_POST_KEY);
$cityNP = $np->getCity($arLocationInfo['CITY_NAME']);
$warenhouse = array();
if( empty($cityNP['errors']) )
    $warenhouse = $np->getWarehouses($cityNP['data'][0]['Ref']);
    //p($warenhouse['data']);
    //p($arCityRes);
?>
<div class="np-warenhouse">
    <select id="np_warenhouse" name="ORDER_PROP_<?=$arResult['PROP_NP_ID']?>" onchange="submitForm();" style="width:450px;">
        <option value="0"> Выберите отделение Новой Почты </option>
    <?foreach($warenhouse['data'] as $k => $waren):?>
        <?if(!empty($waren['DescriptionRu'])):?>
        <?
        $selectedNPW = '';
        if($waren['Number'] == $arResult['PROP_NP_VALUE']) {
            $selectedNPW = 'selected';
            $selKey = $k;
        }
        ?>
        
         <option value="<?=$waren['Number']?>" <?=$selectedNPW?>><?=$waren['DescriptionRu']?></option>
        <?endif;?>
    <?endforeach;?>

    </select>
    
    <?$arReturn['DATA'] = $warenhouse['data'][$selKey];?>
    
    <input 
        type="hidden" 
        value="<?=$warenhouse['data'][$selKey]['CityRef']?>" 
        name="ORDER_PROP_<?=$arResult['PROP_NP_CITYREF']?>" 
        id="ORDER_PROP_<?=$arResult['PROP_NP_CITYREF']?>" 
    >
    <input 
        type="hidden" 
        value="<?=$warenhouse['data'][$selKey]['Ref']?>" 
        name="ORDER_PROP_<?=$arResult['PROP_NP_REF']?>" 
        id="ORDER_PROP_<?=$arResult['PROP_NP_REF']?>" 
    >
    <?//p($warenhouse['data'][$selKey]);?>
<!--    <input 
        type="hidden" 
        value="<?=$arResult['ORDER_PROP']['USER_PROPS_N'][$arResult['PROP_NP_ID']]['VALUE']?>" 
        name="ORDER_PROP_<?=$arResult['PROP_NP_ID']?>" 
        id="ORDER_PROP_<?=$arResult['PROP_NP_ID']?>" 
    >-->
<?if( isset($arResult['PROP_NP_VALUE']) && (int)$arResult['PROP_NP_VALUE']>0 && !empty($arCityRes[$arResult['PROP_NP_VALUE']]) ):?>
    <div id="dop_info_stor_pre" class="dop-info-stor-pre">
        <span>т. <?=$arCityRes[$arResult['PROP_NP_VALUE']]['phone']?></span><br/>
        <?if($arCityRes[$arResult['PROP_NP_VALUE']]['max_weight_allowed'] > 0):?>
        <span>до <?=$arCityRes[$arResult['PROP_NP_VALUE']]['max_weight_allowed']?> кг</span><br/>
        <?endif;?>
        <?if($arCityRes[$arResult['PROP_NP_VALUE']]['weekday_work_hours'] > 0):?>
        <span>График работы: <?=$arCityRes[$arResult['PROP_NP_VALUE']]['weekday_work_hours']?></span><br/>
        <?endif;?>
    </div>
    <input 
        type="hidden" 
        value="<?=$warenhouse['data'][$selKey]['DescriptionRu']?>" 
        name="ORDER_PROP_<?=$arResult['PROP_NP_ADDRESS_ID']?>" 
        id="ORDER_PROP_<?=$arResult['PROP_NP_ADDRESS_ID']?>" 
    >
<?endif;?>
</div>
<?
// Получение кода города по названию города и области
$sender_city = $np->getCity('Киев', 'Киевская');
$sender_city_ref = $sender_city['data'][0]['Ref'];
// Получение кода города по названию города и области
//$recipient_city = $np->getCity('Киев', 'Киевская');
//$recipient_city_ref = $recipient_city['data'][0]['Ref'];
$recipient_city_ref = $cityNP['data'][0]['Ref'];
// Вес товара
$weight = 3;
// Цена в грн
$price = $arResult['ORDER_PRICE'];
// Получение стоимости доставки груза с указанным весом и стоимостью между складами в разных городах 
$result = $np->getDocumentPrice($sender_city_ref, $recipient_city_ref, 'WarehouseWarehouse', $weight, $price);
?>
<?if( $result['success'] ):?>
<?/*    <span>Ориентировочная стоимость доставки будет составлять:</span> 
    <span><?=$result['data'][0]['Cost']?>грн</span><br/>*/?>
    <?$arReturn['DELIVERY_PRICE'] = $result['data'][0]['Cost'];?>
<?endif;?>
<?return $arReturn;?>