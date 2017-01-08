<?php
class CAniartOrdersUltima extends CAniartTools
{
    /**
     * Название таблицы
     **/
    public static $tableName = 'u_order_ultima';

    /**
     * Метод похож на стандартный битриксовый GetList
     * @param = array('FILED'=>'SORT');
     * @param = array(); // подобен битриксовому ciblockelement::getlist
     * @param = array(); // не работает
     * @param = array(navParams); // подобен битриксовому ciblockelement::getlist
     * @param = array('FIELD1', 'FIELD2',..); 
     * @return = CDbResult;
     **/
    public static function GetList($arOrder=array(), $arFilter=array(), $arGroupBy=array(), $arNavStartParams=false, $arSelectFields = array() )
    {
        if( !empty($arSelectFields) && is_array($arSelectFields) )
            $select = implode(', ', $arSelectFields);
        else
            $select = '*';

        $where = self::MKFilter($arFilter);
        $order = self::MKOrder($arOrder);
        $strSql = "SELECT $select FROM ".self::$tableName.' '.$where.' '.$order;
        //echo $strSql;die;
        //$res = $GLOBALS['DB']->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
        $res = new CDbResult();
        $cnt = self::GetCountRecords();
        $res->NavQuery($strSql, $cnt, $arNavStartParams);
        return $res;
    }

    /**
    * Добавление элементов в таблицу
    **/
    public static function Add($arFields)
    {
        $delete_keys = array('LID', 'DATE_INSERT', 'DATE_UPDATE', 'ULTIMA_UPDATE', 'ULTIMA_INSERT', 'ULTIMA_STATUS');

        $arFields = array_diff_key($arFields, array_flip($delete_keys)); // Only one line!
        
        $arFields['LID'] = SITE_ID;
        $arFields['DATE_UPDATE'] = GetTime(time(),"FULL");
        return self::_Update(false, $arFields);
    }

    /**
    * Добавление и обновление элементов в таблице
    **/
    public static function Update($ID, $arFields)
    {
        $delete_keys = array('LID', 'DATE_INSERT', 'DATE_UPDATE', 'ULTIMA_UPDATE', 'ULTIMA_INSERT', 'ULTIMA_STATUS', 'ID');

        $arFields = array_diff_key($arFields, array_flip($delete_keys)); // Only one line!
        
        $arFields['LID'] = SITE_ID;
        $arFields['DATE_UPDATE'] = GetTime(time(),"FULL");
        return self::_Update($ID, $arFields);
    }

    /**
    * внутренний метод для для обн и добавления в таблицу
    **/
    protected static function _Update($ID, $arFields)
    {
        if( !is_array($arFields) || empty($arFields) )
            return false;

        $GLOBALS['DB']->StartTransaction();
        if ((int)$ID>0) {
            $strUpdate = $GLOBALS['DB']->PrepareUpdate(self::$tableName, $arFields);
            $strSql = "UPDATE ".self::$tableName." SET ".$strUpdate." WHERE ID=".(int)$ID;
            $res = $GLOBALS['DB']->Query($strSql, false, $err_mess.__LINE__);
        } else {
            $arFields['DATE_INSERT'] = GetTime(time(),"FULL");
            $arInsert = $GLOBALS['DB']->PrepareInsert(self::$tableName, $arFields);
            $strSql = "INSERT INTO ".self::$tableName." (".$arInsert[0].") VALUES (".$arInsert[1].")";
            $res = $GLOBALS['DB']->Query($strSql, false, $err_mess.__LINE__);
            $ID = $GLOBALS['DB']->LastID();
        }
        $cntEdit = $res->AffectedRowsCount();
        
        if( is_object($res) )
            $GLOBALS['DB']->Commit();
        else
            $GLOBALS['DB']->Rollback();
        
        return $ID;
    }

    /**
    * Добавление элементов в таблицу
    **/
    public static function Delete($ID)
    {
        if( $ID<=0 )
            return false;
        
        $GLOBALS['DB']->StartTransaction();
        
        $strSql = "DELETE FROM ".self::$tableName." WHERE ID=".(int)$ID;
        $res = $GLOBALS['DB']->Query($strSql, false, $err_mess.__LINE__);
        $cntEdit = $res->AffectedRowsCount();
        if( $cntEdit>=1 ){
            $GLOBALS['DB']->Commit();
            return true;
        } else{
            $GLOBALS['DB']->Rollback();
            return false;
        }
    }
    
    /**
    * получение инф по ИД записи
    **/
    public static function GetById($id=0)
    {
        if( (int)$id<=0 )
            return false;
        return self::GetList(array(), array('ID'=>(int)$id))->Fetch();
    }

    /**
    * получение инф по ИД заказа
    **/
    public static function GetByOrderId($id=0)
    {
        if( (int)$id<=0 )
            return false;
        return self::GetList(array(), array('ORDER_ID'=>(int)$id))->Fetch();
    }

    /**
    * получение инф по ИД из ультимы
    **/
    public static function GetByUltimaId($id=0)
    {
        if( (int)$id<=0 )
            return false;
        return self::GetList(array(), array('ULTIMA_ID'=>(int)$id))->Fetch();
    }

    /**
    * внутр метод для формирования запроса для GetList
    **/
    protected function MKFilter($arFilter = array())
    {
        if( !is_array($arFilter) || empty($arFilter))
            return ' ';
        
        $sql = '';

        foreach( $arFilter as $key=>$val ){
            $pos = strpos($key, '!');
            $sql .= 'AND ';
            if( $pos !==false && $pos<=1 ){
                $sql .= "`".strtoupper(substr($key, $pos+1))."`";
                if( !is_array($val) && !is_bool($val) )
                    $sql .= " NOT IN ('".$val."') ";
                elseif( is_bool($val) ){
                    $not = ($val===false)?'NOT':'';
                    $sql .= " IS ".$not." NULL ";
                } else
                    $sql .= " NOT IN ('".implode(', ', $val)."') ";

                continue;
            }elseif( preg_match("/[<>=]+/s", $key, $match) ){
                    $key = preg_replace("/[<>=]+/s", '', $key);
                    $sql .= "`".strtoupper($key)."` ".$match[0]." '".$val."' ";
                continue;
            }elseif( $pos===false ){
                if( is_array($val) )
                    $sql .= "`".strtoupper($key)."` IN ('".implode("', '", $val)."') ";
                elseif( is_bool($val) ){
                    $not = ($val===false)?'':'NOT';
                    $sql .= "$key IS ".$not." NULL ";
                } else
                    $sql .= "`".strtoupper($key)."` IN ('".$val."') ";

                continue;
            }
        }

        return "WHERE ".substr($sql, 4);
    }

    /**
    * Внутр метод для формирования сортировки
    **/
    protected function MKOrder($arOrder = array())
    {
        if( !is_array($arOrder) || empty($arOrder))
            return ' ';

        $ord = 'ORDER BY ';
        foreach ($arOrder as $key => $value) {
            $val = ( preg_match('/(desc)/i', $value) )?'DESC':'ASC';
            $ord .= $key.' '.$val.', ';
        }
        return substr($ord, 0, -2);
    }

    /**
    * получение количества всех записей
    **/
    public static function GetCountRecords()
    {
        $strSql = 'SELECT count(*) FROM '.self::$tableName;
        $res = $GLOBALS['DB']->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
        return (int)current($res->Fetch());
    }
}
