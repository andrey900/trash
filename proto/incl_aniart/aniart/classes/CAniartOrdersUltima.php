<?php
class CAniartOrdersUltima extends CAniartTools
{
    public static $tableName = 'u_order_ultima';

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

    public static function Update($id, $arFields)
    {
    	if( !is_array($arFields) || empty($arFields) )
    		return false;
    	
    	$strUpdate = $DB->PrepareUpdate(self::$tableName, $arFields);
        $DB->StartTransaction();
        if ($ID>0) 
        {
            $DB->Update(self::$tableName, $arFields, "WHERE ID='".$ID."'", $err_mess.__LINE__);
        }
        else 
        {
            $ID = $DB->Insert("b_form", $arFields, $err_mess.__LINE__);
            $new="Y";
        }
        $ID = intval($ID);
        if (strlen($strError)<=0) 
        {
            $DB->Commit();
            if (strlen($save)>0) LocalRedirect("form_list.php?lang=".LANGUAGE_ID);
            elseif ($new=="Y") LocalRedirect("form_edit.php?lang=".LANGUAGE_ID."&ID=".$ID);
        }
        else $DB->Rollback();
    }

    public static function GetById($id=0)
    {
    	if( (int)$id<=0 )
    		return false;
    	return self::GetList(array(), array('ID'=>(int)$id))->Fetch();
    }

    public static function GetByOrderId($id=0)
    {
    	if( (int)$id<=0 )
    		return false;
    	return self::GetList(array(), array('ORDER_ID'=>(int)$id))->Fetch();
    }

    public static function GetByUltimaId($id=0)
    {
    	if( (int)$id<=0 )
    		return false;
    	return self::GetList(array(), array('ULTIMA_ID'=>(int)$id))->Fetch();
    }

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
    				$not = ($val===false)?'NOT':'';
    				$sql .= " IS ".$not." NULL ";
    			} else
    				$sql .= "`".strtoupper($key)."` IN ('".$val."') ";

    			continue;
    		}
    	}

    	return "WHERE ".substr($sql, 4);
    }

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

    public static function GetCountRecords()
    {
    	$strSql = 'SELECT count(*) FROM '.self::$tableName;
    	$res = $GLOBALS['DB']->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
    	return (int)current($res->Fetch());
    }
}