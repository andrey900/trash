<?php

class CAniartULBX extends CAniartOrdersUltima
{
    public $ordersId = array();
    public $usersId  = array();
    
    public static $siteId = 43;
    public static $article = 'XML_ID';
    
    public function __construct()
    {
        CModule::IncludeModule("sale");
    }
    
    public static function GetOrdersNoUltima()
    {
        $res = CAniartOrdersUltima::GetList(array('ORDER_ID'=>'ASC', 'ID'=>'DESC'), array('ULTIMA_ID'=>false), array(),  false, array());
        $arResult = array();
        while ($arRes = $res->Fetch()) {
            $arResult[$arRes['ORDER_ID']] = $arRes;
            //$this->ordersId[$arRes['ORDER_ID']] = $arRes['ORDER_ID'];
        }
        return $arResult;
    }
    
    public static function GetOrderInfo($arOrdersId = array())
    {
        if( empty($arOrdersId) || !is_array($arOrdersId))
            return false;
        
        $arFilter = array('ID'=>$arOrdersId);
        $db_sales = CSaleOrder::GetList(array("ID" => "ASC"), $arFilter);
        $arResult = array();
        while ($ar_sales = $db_sales->Fetch())
        {
           $arResult[$ar_sales['ID']] = $ar_sales;
           $rsOrderProps = CSaleOrderPropsValue::GetOrderProps($ar_sales['ID']);
           while ($arOrderProps = $rsOrderProps->Fetch())
           {
                //p($arOrderProps);
                if( $arOrderProps['TYPE']=='LOCATION' )
                   $arOrderProps['VALUE'] = CSaleLocation::GetByID($arOrderProps['VALUE'], LANGUAGE_ID);

                $arResult[$ar_sales['ID']]['ORDER_PROP'][strtoupper($arOrderProps['CODE'])] = $arOrderProps['VALUE'];
           }
           
           $arResult[$ar_sales['ID']]['BASKET_INFO'] = self::GetBasketFromOrder($ar_sales['ID']);
           
           $arResult[$ar_sales['ID']]['USER_INFO'] = CUser::GetById($ar_sales['USER_ID'])->Fetch();
           //$arResult[$ar_sales['ID']]['ID'] = $ar_sales['ID'];
           //$arResult[$ar_sales['ID']]['USER_ID'] = $ar_sales['USER_ID'];
        }
        
        return $arResult;
    }
    
    public static function GetBasketFromOrder($order_id)
    {
        $rsBasketItems = CSaleBasket::GetList(
            array(),
            array( 
               "LID" => SITE_ID,
               "ORDER_ID" => $order_id
            )
         );
        
        $arItems = array();
        while($arBasket = $rsBasketItems->Fetch() ){
            $arRes = self::GetInfoElements($arBasket['PRODUCT_ID'], array('ID', self::$article));
            if( !empty($arRes[$arBasket['PRODUCT_ID']][self::$article]) ){
                $arItems[$arBasket['ID']]['ID'] = $arBasket['PRODUCT_ID'];
                $arItems[$arBasket['ID']]['ARTICLE'] = $arRes[$arBasket['PRODUCT_ID']][self::$article];
                $arItems[$arBasket['ID']]['QUANTITY'] = $arBasket['QUANTITY'];
            }
        }
        return $arItems;
    }
    
    public static function CheckUser($arResult=array(), $soapLink)
    {
        if( empty($arResult) || !is_array($arResult) )
            return false;
        
        foreach($arResult as $arOrder){
            $isFind = false;
            if( !isset($arOrder['USER_INFO']) || $arOrder['USER_INFO']['UF_AGENT'] > 0 )
                continue;
            
            if( $isFind===false && !empty($arOrder['USER_INFO']['EMAIL']) ){
                $arRes = $soapLink->FindAgent($arOrder['USER_INFO']['EMAIL']);

                if( $arRes > 0 ) $isFind = true;
            }
            if( $isFind===false && !empty($arOrder['USER_INFO']['PERSONAL_PHONE']) ){
                $arRes = $soapLink->FindAgent('', $arOrder['USER_INFO']['PERSONAL_PHONE']);
                
                if( $arRes > 0 ) $isFind = true;
            }
            
            if( !$isFind ){
                $arUser = $arOrder['USER_INFO'];

                $arUser['UF_INN'] = self::numericMatch($arUser['UF_INN'], '10, 12', '');
                
                $arUser['UF_KPP'] = self::numericMatch($arUser['UF_KPP'], '9', '');
                
                $arRes = $soapLink->CreateAgent($arUser['EMAIL'], $arUser['PERSONAL_PHONE'], $arUser['NAME'], '', $arUser['UF_INN'], $arUser['UF_KPP'], $arUser['UF_OKPO'], '', $arUser['UF_ADDR'], $arUser['UF_BANKNAME'], $arUser['UF_BIK'], $arUser['UF_RASCHETSCHET'], $arUser['UF_CORRACOUNT']);
                unset($arUser);
                $isFind = true;
            }
            
            if( $isFind && $arRes > 0 ){
                $user = new CUser;
                $user->Update($arOrder['USER_INFO']['ID'], Array( "UF_AGENT" => $arRes));
                $arResult[$arOrder['ID']]['USER_INFO'] = CUser::GetById($arOrder['USER_ID'])->Fetch();
            }
        }
        
        return $arResult;
    }
    
    public static function CheckAddress($arResult=array(), $soapLink)
    {
        if( empty($arResult) || !is_array($arResult) )
            return false;
        
        foreach($arResult as $arOrder){
            $index = self::numericMatch($arOrder['ORDER_PROP']['INDEX'], '6', '999999');

            $arRes = $soapLink->FindAddress($arOrder['USER_INFO']['UF_AGENT'], $index, $arOrder['ORDER_PROP']['CITY']['REGION_NAME_LANG'].', г. '.$arOrder['ORDER_PROP']['CITY']['CITY_NAME_LANG'].', ул. '.$arOrder['ORDER_PROP']['CITY']['STREET'].', офис/кв. '.$arOrder['ORDER_PROP']['OFFICE'], $arOrder['ORDER_PROP']['LICO'], $arOrder['ORDER_PROP']['TEL']);
            if( $arRes <= 0 ){
                $arRes = $soapLink->CreateAddress($arOrder['USER_INFO']['UF_AGENT'], $index, $arOrder['ORDER_PROP']['CITY']['REGION_NAME_LANG'].', г. '.$arOrder['ORDER_PROP']['CITY']['CITY_NAME_LANG'].', ул. '.$arOrder['ORDER_PROP']['CITY']['STREET'].', офис/кв. '.$arOrder['ORDER_PROP']['OFFICE'], $arOrder['ORDER_PROP']['LICO'], $arOrder['ORDER_PROP']['TEL']);
            }
            
            $arResult[$arOrder['ID']]['ULTIMA_ADDR_ID'] = (int)$arRes;
        }
        
        return $arResult;
    }
    
    public static function CreateReserve($arResult=array(), $soapLink)
    {
        if( empty($arResult) || !is_array($arResult) )
            return false;
        
        foreach( $arResult as $arOrder ){
            if( empty($arOrder['BASKET_INFO']) )
                continue;
            
            $arItems = $arQuantity = array();
            foreach($arOrder['BASKET_INFO'] as $arItem){
                $arItems[] = (int)$arItem['ARTICLE'];
                $arQuantity[] = (int)$arItem['ARTICLE']['QUANTITY'];
            }
            
            $arOrder['DELIVERY_ID'] = (bool)$arOrder['DELIVERY_ID'];
            //$arOrder['COMMENTS'] = 'test';
            //var_dump(array($arOrder['USER_INFO']['UF_AGENT'], $arItems, $arQuantity, $arOrder['DELIVERY_ID'], strtotime($arOrder['DATE_INSERT']), $arOrder['ULTIMA_ADDR_ID'], '', '', '', '', $arOrder['COMMENTS'], self::$siteId));
            //die();
            $reserveId = $soapLink->CreateReserve($arOrder['USER_INFO']['UF_AGENT'], $arItems, $arQuantity, $arOrder['DELIVERY_ID'], strtotime($arOrder['DATE_INSERT']), $arOrder['ULTIMA_ADDR_ID'], '', '', '', '', $arOrder['COMMENTS'], self::$siteId);
            if( is_array($reserveId) && $reserveId[0] > 0 ){
                $arFields = $arOrder['ULTIMA_INFO'];
                $ID = $arFields['ID'];
                
                $delete_keys = array('ID', 'LID', 'DATE_INSERT', 'DATE_UPDATE', 'ORDER_STATUS', 'ULTIMA_UPDATE', 'ULTIMA_STATUS');
                $arFields = array_diff_key($arFields, array_flip($delete_keys)); // Only one line!
                
                if( strtotime($arFields['ULTIMA_INSERT']) > 0 )
                    unset($arFields['ULTIMA_INSERT']);
                else
                    $arFields['ULTIMA_INSERT'] = GetTime(time(),"FULL");
                
                $arFields['ULTIMA_ID'] = $reserveId[0];
                $arFields['ULTIMA_UPDATE'] = GetTime(time(),"FULL");
                $arFields['ULTIMA_STATUS']  = 'Y';
                if( self::_Update($ID, $arFields) )
                    echo "OK";
                else
                    echo "FALSE: ".$ID.' - '.serialize($arFields);
            }
        }
        
        return $arResult;
    }
    
    public static function MergingOrders($arOrders, $arOrdersUltima)
    {
        foreach($arOrders as &$arOrder){
            $arOrder['ULTIMA_INFO'] = $arOrdersUltima[$arOrder['ID']];
        }
        unset($arOrder);
        
        return $arOrders;
    }
    
    public static function onlyNumeric($num)
    {
        return preg_replace("/\D/","",$arOrder['ORDER_PROP']['INDEX']);
    }
    
    public static function numericMatch($num, $count, $nomatch)
    {
        $num = self::onlyNumeric($num);
        if( preg_match('/[0-9]{'.$count.'}/', $num, $match) )
            return $match[0];
        else
            return $nomatch;
    }
}
