<?php

namespace Studio8\Main\Integration;
use Studio8\Main\Helpers as Main;


class Helper
{
    public static function getNameCollection(array $arItems, $strField = "NAME"){
        return array_map(function($e) use ($strField){return $e[$strField];}, $arItems);
    }

    public static function findNameInCollection(array $arItems, $find, $strField = "NAME"){
        return array_filter($arItems, function($e) use ($strField, $find){ return $find == $e[$strField]; });
    }

    public static function updateQuantity($xmlId, $quantity){
        $arItem = Main::_GetInfoElements(false, [], ['IBLOCK_ID' => 14, 'XML_ID' => $xmlId]);

        if( !$arItem )
            return ;

        $PRODUCT_ID = key($arItem);
        $arFields = array('QUANTITY' => $quantity);// зарезервированное количество
        \CCatalogProduct::Update($PRODUCT_ID, $arFields);
    }
}
