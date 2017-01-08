<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
   
 //функция по удалению из корзины всех товаров
//CModule::IncludeModule("sale");
//CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID(), false);

$resUser = CUser::GetByID($USER->GetID());
$arUser = $resUser->Fetch();

/* Сформируем заказ */
if(isset($_POST['id']))
	$id[0] = intval($_POST['id']);

$kol= 1;
//$kol[]= intval($_POST['col']);

if(isset($_POST['val']))
{
    $arrVal = explode(',',$_POST['val']);
    $arrXml = array();
	foreach($arrVal as $val)
	{
        $val = intval($val);
		if($val > 0)
          $arrXml[] = $val;
	}

    if(count($arrXml) > 0)
	{
	   CModule::IncludeModule("iblock");

       $arFilter = array(
	    array(
        "LOGIC" => "OR",
        array('XML_ID' => $arrXml),
        array('PROPERTY_CODTOVARA' => $arrXml),
    ),
	   'IBLOCK_ID'=>1,
	   "ACTIVE"=>"Y");
	   $res = CIBlockElement::GetList(Array(), $arFilter, false, false, array('ID'));

	   while($ob = $res->GetNext())
          $id[] = $ob['ID'];
	}
}

///if(count($id)==0 || $id[0]==0) return;

if(empty($kol)) $kol = 1;

if(count($id) > 0)
{       CModule::IncludeModule("iblock");
 		CModule::IncludeModule("sale");
	    $arSelect = Array('NAME','CATALOG_GROUP_1','ID','ACTIVE');
	    $arFilter = Array(
	    'ID'=>$id
	   );
	    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

		    if($ob = $res->GetNext())
	  		{
			    if($ob['CATALOG_PRICE_1'] > 0)
				{
				    $arFields = array(
				    "PRODUCT_ID" => $ob['ID'],
				    "PRICE" => $ob['CATALOG_PRICE_1'],
				    "QUANTITY" => $kol,
				    "PRODUCT_PRICE_ID" => 1,
				    "CURRENCY" => "RUB",
				    "LID" => SITE_ID,
				    "DELAY" => "N",
				    "CAN_BUY" => "Y",
				    "NAME" => $ob['~NAME'],
				    "CALLBACK_FUNC" => "CatalogBasketCallback",
				    "MODULE" => "catalog",
				    "NOTES" => "Базовая цена",
				    "ORDER_CALLBACK_FUNC" => "CatalogBasketOrderCallback",
					"CANCEL_CALLBACK_FUNC" => "CatalogBasketCancelCallback",
					"PAY_CALLBACK_FUNC" => "CatalogPayOrderCallback",
				    /*"DETAIL_PAGE_URL" => "/".LANG."/detail.php?ID=". $arElement['ELEM_ID'],*/
					"FUSER_ID"=>CSaleBasket::GetBasketUserID(),

				  	);

		    		 //echo '<pre>'.print_r($arFields,1).'</pre>'.__FILE__.' # '.__LINE__;
					 $BASKET_ID = CSaleBasket::Add($arFields);
					 if ($BASKET_ID == false)
					 {
					 	$errAdd = 1;
					 }
				 }
				 else
				 {
                    $errAdd = 1;
					$errmess =  'Товар не может быть добавлен в корзину';
				 }
	    	}

			 $arrReturn = getCountBasket();
             $arrReturn['ttt'] = $ob;
             $arrReturn['ttt1'] = $arFilter;
			 if ($errAdd == 1)
			 {
			 	if(empty($errmess))
						$arrReturn['mess'] =  'Не все товары добавлены в корзину';
				else
					$arrReturn['mess'] = $errmess;
			 }
			 else
			 	$arrReturn['mess'] =  'Товар успешно добавлен в корзину!';

            $arrReturn['echo_js'] = implode("\r\n",$arrReturn['jsButtonText']);
 }
 else
    $arrReturn = getCountBasket();

 	header('Content-Type: application/json; charset=utf-8');
	$JAISON = json_encode($arrReturn);
	echo $JAISON;
	return;
?>