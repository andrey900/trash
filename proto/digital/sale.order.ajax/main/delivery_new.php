<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
function fShowStore(id, showImages, formWidth, siteId)
{
	var strUrl = '<?=$templateFolder?>' + '/map.php';
	var strUrlPost = 'delivery=' + id + '&showImages=' + showImages + '&siteId=' + siteId;

	var storeForm = new BX.CDialog({
				'title': '<?=GetMessage('SOA_ORDER_GIVE')?>',
				head: '',
				'content_url': strUrl,
				'content_post': strUrlPost,
				'width': formWidth,
				'height':450,
				'resizable':false,
				'draggable':false
			});

	var button = [
			{
				title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
				id: 'crmOk',
				'action': function ()
				{
					GetBuyerStore();
					BX.WindowManager.Get().Close();
				}
			},
			BX.CDialog.btnCancel
		];
	storeForm.ClearButtons();
	storeForm.SetButtons(button);
	storeForm.Show();
}

function GetBuyerStore()
{
	BX('BUYER_STORE').value = BX('POPUP_STORE_ID').value;
	//BX('ORDER_DESCRIPTION').value = '<?=GetMessage("SOA_ORDER_GIVE_TITLE")?>: '+BX('POPUP_STORE_NAME').value;
	BX('store_desc').innerHTML = BX('POPUP_STORE_NAME').value;
	BX.show(BX('select_store'));
}
</script>

<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" />
<div class="section">
<?



//print_r($arResult["DELIVERY"]);
//p($arResult["DELIVERY"]);
if(!empty($arResult["DELIVERY"]))
{
    $width = ($arParams["SHOW_STORES_IMAGES"] == "Y") ? 850 : 700;
    ?>
    <div class="title delivery-title">
        <span><?=GetMessage("SOA_TEMPL_DELIVERY")?></span>
    </div>

    <div class="sale-order-delivery">
        <div class="delivery-list">
        <?
        foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
        {
                if ($delivery_id !== 0 && intval($delivery_id) <= 0)
                {
                    foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
                    {
                        ?>
                            <div class="delivery-pre">
                                <input type="radio" id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>" name="<?=$arProfile["FIELD_NAME"]?>" value="<?=$delivery_id.":".$profile_id;?>" <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?> onclick="submitForm();" />
                                <label for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>">
                                        <?if (count($arDelivery["LOGOTIP"]) > 0):?>
                                                <?=CFile::ShowImage($arDelivery["LOGOTIP"], 95, 55, "border=0", "", false)?>
                                        <?else:?>
                                                <img src="<?=SITE_TEMPLATE_PATH?>/components/bitrix/sale.order.ajax/main/images/logo-default-d.gif" alt="" />
                                        <?endif;?>
                                        <div class="desc">
                                                <div class="name"><?=$arDelivery["TITLE"]." (".$arProfile["TITLE"].")";?></div>
                                                <div class="desc">
                                                        <?if (strlen($arProfile["DESCRIPTION"]) > 0):?>
                                                                <?=nl2br($arProfile["DESCRIPTION"])?>
                                                        <?else:?>
                                                                <?=nl2br($arDelivery["DESCRIPTION"])?>
                                                        <?endif;?>

                                                </div>
                                        </div>
                                </label>
                            </div>
                            <div>
                            <?
                                $APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
                                        "NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
                                        "DELIVERY" => $delivery_id,
                                        "PROFILE" => $profile_id,
                                        "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
                                        "ORDER_PRICE" => $arResult["ORDER_PRICE"],
                                        "LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
                                        "LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
                                        "CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
                                        "ITEMS" => $arResult["BASKET_ITEMS"]
                                ), null, array('HIDE_ICONS' => 'Y'));
                            ?>
                            </div>
                        <?
                } // endforeach
            }
            else
            {
                    if (count($arDelivery["STORE"]) > 0)
                            $clickHandler = "onClick = \"fShowStore('".$arDelivery["ID"]."','".$arParams["SHOW_STORES_IMAGES"]."','".$width."','".SITE_ID."')\";";
                    else
                            $clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery["ID"]."').checked=true;submitForm();\"";
                    ?>
                    <div class="delivery-pre">
                            <input type="radio" id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" name="<?=$arDelivery["FIELD_NAME"]?>" value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?> onclick="submitForm();">
                            <label for="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" <?=$clickHandler?> >
                                    <div class="desc">
                                            <div class="name">
                                                <span><?= $arDelivery["NAME"] ?></span>
                                            </div>
                                            <div class="desc">
                                            <?
                                            if (strlen($arDelivery["PERIOD_TEXT"])>0)
                                            {
                                                    echo $arDelivery["PERIOD_TEXT"];
                                                    ?><br /><?
                                            }
                                            ?>
                                            </div>
                                            <div class="clear"></div>
                                            <?if (count($arDelivery["STORE"]) > 0):?>
                                                    <span id="select_store"<?if(strlen($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"]) <= 0) echo " style=\"display:none;\"";?>>
                                                            <span class="select_store"><?=GetMessage('SOA_ORDER_GIVE_TITLE');?>: </span>
                                                            <span class="ora-store" id="store_desc"><?=htmlspecialcharsbx($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"])?></span>
                                                    </span>
                                            <?endif;?>
                                    </div>
                            </label>
                            <div class="clear"></div>
                    </div>
                    <?
            }
        }
        ?>
            <div class="deli-info-add-pre" id="delivery_info_add_pre">
                <?foreach ($arResult["DELIVERY"] as $arDeliveryInfo):?>
                    <?if($arDeliveryInfo['CHECKED'] == 'Y'):?>
                        <?if($arDeliveryInfo['ID'] == SELF_DELIVERY_ID):?>
						
                            <?foreach($arResult['ORDER_PROP']['USER_PROPS_Y'] as $arUserProp):?>
                                <?if($arUserProp['CODE'] == 'LOCATION'):?>
                                     <?foreach($arUserProp['VARIANTS'] as $arCity):?>
                                        <?if($arCity['SELECTED'] == 'Y'):?>
                                            <?
											$arLocationInfo = CSaleLocation::GetByID( $arCity['ID'] );
											//$arCityRes = CAniartNewPost::getWarehouseName($arLocationInfo['CITY_NAME']);
//p($arResult['ORDER_PRICE'], false);
$arDeliveryData = $APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include_areas/new_post.php",
            Array(
                "arResult" => array("CITY_NAME"     => "",
                                    "CITY_ID"       => $arCity['ID'],
                                    "PROP_NP_ID"    => "9",
                                    "PROP_NP_VALUE" => $arResult['ORDER_PROP']['USER_PROPS_N'][9]['VALUE'],
									"PROP_NP_ADDRESS_ID"    => "8",
                                    "ORDER_PRICE"   => $arResult['ORDER_PRICE'],
									"PROP_NP_CITYREF" => "12",
									"PROP_NP_REF"     => "11",
                                    //"PROP_NP_ADDRESS_VALUE" => $arResult['ORDER_PROP']['USER_PROPS_N'][8]['VALUE'],
                                     ),
                ),
            Array("MODE"=>"php")
);
if( !empty($arDeliveryData) ){
		$arResult["DELIVERY_PRICE"] = round($arDeliveryData['DELIVERY_PRICE']);
		$arResult["DELIVERY_PRICE_FORMATED"] = $arDeliveryInfo["PRICE_FORMATED"] = CurrencyFormat($arDeliveryData['DELIVERY_PRICE'], "UAH");
}
											//p($arCityRes);
											?>
											<?/*
                                            $arCityRes = CAniartNewPost::getWarehouseName($arCity['CITY_NAME']);
                                            $counterStor = 0;
                                            ?>
                                            <?if(!empty($arCityRes)):?>
                                                <div>
                                                <select id="deliveri_storage" class="deliveri-storage-pre">

                                                <?foreach($arCityRes as $cityDet):?>
                                                    <?
                                                    ++$counterStor;
                                                    $selectedStor = '';
                                                    if($cityDet['wareId'] == $_COOKIE['delivery_np']) {
                                                        $selectedStor = 'selected';
                                                    }elseif($counterStor == 1) {
                                                        $selectedStor = 'selected';
                                                    }
                                                    $dopInfoStorage = $cityDet['phone'].'#'.$cityDet['max_weight_allowed'].'#'.$cityDet['weekday_work_hours'];
                                                    ?>
                                                    <option value="<?=$cityDet['wareId']?>" data-dop="<?=$dopInfoStorage?>" <?=$selectedStor?>>
                                                        <?=preg_replace('/\(.*?\)/', '', $cityDet['addressRu']);?>
                                                    </option>

                                                <?endforeach;?>

                                                </select>
                                                </div>
                                                <div id="dop_info_stor_pre" class="dop-info-stor-pre">
                                                    <span>т. <?=$arCityRes[0]['phone']?></span><br/>
                                                    <?if($arCityRes[0]['max_weight_allowed'] > 0):?>
                                                    <span>до <?=$arCityRes[0]['max_weight_allowed']?> кг</span><br/>
                                                    <?endif;?>
                                                    <?if($arCityRes[0]['weekday_work_hours'] > 0):?>
                                                    <span>График работы: <?=$arCityRes[0]['weekday_work_hours']?></span><br/>
                                                    <?endif;?>
                                                </div>

                                            <?endif;?>*/?>
                                        <?endif;?>
                                     <?endforeach;?>
                                <?endif;?>
                            <?endforeach;?>

                        <?endif;?>

                        <?if($arDeliveryInfo['ID'] == STANDART_DELIVERY_ID ||
							 $arDeliveryInfo['ID'] == STANDART_DELIVERY_ID1 ):?>
                            <div class="address-curier">
                                <?foreach($arResult['ORDER_PROP']['USER_PROPS_N'] as $arUserProps):?>
                                    <?if($arUserProps['CODE'] == 'ADDRESS'):?>
                                <textarea 
                                    code="<?=$arUserProps['CODE']?>"
                                    name="<?=$arUserProps['FIELD_NAME']?>" 
                                    id="<?=$arUserProps['FIELD_NAME']?>" 
                                    placeholder="Укажите адрес"
                                ><?=$arUserProps['VALUE']?></textarea>
                                    <?endif;?>
                                <?endforeach;?>
                            </div>
                        <?endif;?>
                        <?=GetMessage("SALE_DELIV_PRICE");?> <?=$arDeliveryInfo["PRICE_FORMATED"]?><br />
                        <?
                        if(strlen($arDeliveryInfo["DESCRIPTION"]) > 0)
                        {
                            echo $arDeliveryInfo["DESCRIPTION"]."<br />";
                        }
                        ?>
                    <?endif;?>
                <?endforeach;?>
            </div>
        </div>
    </div>
    <?
}

?>
</div>
