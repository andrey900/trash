<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $summBespDostavki;

$resUser = CUser::GetByID($USER->GetID());
$arUser = $resUser->Fetch();

/* Сформируем заказ */
$id = intval($_POST['ID']);
$kol= intval($_POST['COL']);
$IBLOCK_ID = 8;
if(empty($kol)) $kol = 1;


if($id > 0 && $kol != 0)
{
         CModule::IncludeModule("sale");
		 CModule::IncludeModule("iblock");


$arBasketItems = array();
$dbBasketItems = CSaleBasket::GetList(
        array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
        false,
        false,
        array("ID", "CALLBACK_FUNC", "MODULE",
              "PRODUCT_ID", "QUANTITY", "DELAY",
              "CAN_BUY", "PRICE", "WEIGHT")
    );

	$arSelect = Array('NAME','ID','CATALOG_GROUP_1');
   //	$arFilter = Array('IBLOCK_ID'=>$IBLOCK_ID);

	$allsum = 0;
	$newElemSumm = 0;
	$count = 0;
	while ($arItems = $dbBasketItems->Fetch())
	{
		 //if($arItems['CAN_BUY'] == 'N') continue;
        // echo '<pre>'.print_r($arItems,1).'</pre>'.__FILE__.' # '.__LINE__;
	  if($arItems['PRODUCT_ID'] == $id)
	  {
	  	  if($kol == -1)
		  {
              CSaleBasket::Delete($arItems['ID']);
			  continue;
		  }
		  else
		  {
              if(CSaleBasket::Update($arItems['ID'],array('QUANTITY'=>$kol)))
			  {
				  $arFilter['ID'] = $arItems['PRODUCT_ID'];
				  $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
				  if($ob = $res->GetNext())
				  {
		 		   		$arrReturn['elemNal'] = ($kol<=$ob['CATALOG_QUANTITY'])?'<span class="green">В наличии</span>':'<span class="red">Под заказ</span>';
	                	$arrReturn['111'] = $ob['CATALOG_QUANTITY']. ' '.$arItems["QUANTITY"];
				  }
			      $newElemSumm = $kol * $arItems['PRICE'];
				  $allsum+= $newElemSumm;
			  }
			  else
			  {
                 $allsum+= $arItems['QUANTITY'] * $arItems['PRICE'];
				 $badAdd = 1;
			  }
		  }
	  }
	  else
	  {
	  	  $allsum+= $arItems['QUANTITY'] * $arItems['PRICE'];
	  }
     $count++;
	}

	/*if($allsum >= $summBespDostavki)
		 $arrReturn['bespDostavka'] = 1;
	else
		 $arrReturn['bespDostavka'] = 0; */

	// echo '<pre>'.print_r(array($allsum,$newElemSumm),1).'</pre>'.__FILE__.' # '.__LINE__;

	$arrReturn['elemSum']  = number_format($newElemSumm, 0, '.', ' ');
	$arrReturn['allSum']   = number_format($allsum, 0, '.', ' ');
    $arrReturn['countint'] = $count;
    $arrReturn['counttxt'] = gettxtpokupki($count);
   // $arrReturn['txtpopup'] = discountPopup($allsum);

	//$arrReturn['goodorder'] = -1;

   /*	if(isUrik(0))
	{
       if( $allsum < URIK_MIN_SUM_ORDER )
	     	$arrReturn['goodorder'] = 0;
	   else
	   		$arrReturn['goodorder'] = 1;
	}  */

	header('Content-Type: application/json; charset=utf-8');
	$JAISON = json_encode($arrReturn);
	echo $JAISON;
}
?>