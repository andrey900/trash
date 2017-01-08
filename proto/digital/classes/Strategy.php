<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.04.14
 * Time: 19:39
 */


interface IValidateStrategy
{
    function Validate( $record );
}


interface ICanBeAddStrategy
{
    function Validate( $record, $param );
}


// для результата - целого числа
class CDecimalValue implements  IValidateStrategy{

    function Validate( $record ) {
        return IntVal($record);
    }
}


// для Мб записанных в виде Кб
class CMBValue implements  IValidateStrategy{

    function Validate( $record ) {
        $int = IntVal($record);
        return $int > 512 ? round ( $int / 1024) : $int;
    }
}

// ==================================================


// может ли элемент быть добавлен в словарь
class CCheckValueByLen implements  ICanBeAddStrategy{

    function Validate( $val, $param ) {
        return (strlen($val) >= $param);
    }
}

// проверка через регулярное
class CCheckValueByReg implements  ICanBeAddStrategy{

    function Validate( $value, $reg ) {
        return (bool)preg_match($reg,$value);
    }

}



