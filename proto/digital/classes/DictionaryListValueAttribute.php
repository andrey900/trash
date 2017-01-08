<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 01.04.14
 * Time: 16:37
 */

class CDictionaryListValueAttribute extends CDictionaryColorAttribute{

    function CanElementBeAdd($name) {
        return strlen($name) >=6;
    }
} 