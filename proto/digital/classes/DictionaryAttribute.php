<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.03.14
 * Time: 16:37
 */

// Single Value
class CDictionaryAttribute extends  CSimpleDictionaryAttribute{

    function  __construct(&$doc,$p_CODE, $p_IBLOCK_ID, $ar_p_values ) {
        parent::__construct($doc,$p_CODE, $p_IBLOCK_ID, $ar_p_values );
        $this->ar_data = $this->ReadDictionary();
    }

} 