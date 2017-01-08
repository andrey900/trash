<?php

class CAniartHandlers
{

    public static $arMailTemplate = array(
            'NEWUSER'=>79,
        );

	public function OBPriceUpdate($ID, &$arFields)
    {
        if( (real)$arFields['PRICE']==0 )
    		CPrice::DeleteByProduct($arFields['PRODUCT_ID']);
    	return true;
    }

    public function OPriceAdd($ID, $arFields)
    {
        if( (real)$arFields['PRICE']==0 )
            CPrice::DeleteByProduct($arFields['PRODUCT_ID']);
        return true;
    }

    public function HookOnBeforeEventAddHandler(&$event, &$lid, &$arFields)
    {
        $arFields["EVENT"] = $event;
    }
    
    public function HookOnBeforeEventSend(&$arFields, &$arTemplate)
    {
        //p2f(array('fields'=>$arFields, 'template'=>$arTemplate));
        $mess = $arTemplate["MESSAGE"];
        foreach($arFields as $keyField => $arField)
            $mess = str_replace('#'.$keyField.'#', $arField, $mess);

        if ( in_array($arTemplate['ID'], self::$arMailTemplate) || $arTemplate['FIELD2_VALUE']=='YES' ) {
            ob_start();
            include($_SERVER["DOCUMENT_ROOT"]."/include/styleMail.tpl");
            $pattern = ob_get_contents();
            ob_end_clean();
            $mess = GetMessageBody($pattern,array("#MESSAGE#"=>$mess));
            $arTemplate["MESSAGE"] = $mess;
        }
    }

    public function HookOnAfterOrderComplete($ID, $arOrder)
    {
        $arFields['ORDER_ID'] = $ID;
        CAniartOrdersUltima::Add($arFields);
        return true;
    }
}