<?php

namespace Studio8\Main;

class Handlers
{
    public static function addElement(&$arFields)
    {
        $arParams = array(
            "max_len" => "80", // обрезаем символьный код до 60 символов
            "change_case" => "L", // приводим к нижнему регистру
            "replace_space" => "-", // меняем пробелы на тире
            "replace_other" => "-", // меняем плохие символы на тире
            "delete_repeat_replace" => "true", // удаляем повторяющиеся тире
        );

        if( !$arFields["CODE"] )
            $arFields["CODE"] = \Cutil::translit($arFields["NAME"], "ru", $arParams);
    }

}