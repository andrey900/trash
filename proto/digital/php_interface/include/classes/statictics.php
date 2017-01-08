<?
/**
 * Получение статистики по выбранному материалу
 */
 function GetElementStat( $int_ID, $TYPE = 1 ){
	// теперь работаем через memcache 

	$obj_memCache = CacheEngineMemcache::getInstance();

	//тут же делается и инкремент
	if( $TYPE == 1 ) {
		$obj_memCache->increment($int_ID);
		$obj_memCache->increment('STABLE_ALL|'.$int_ID);
	}

	$day = $obj_memCache->read($int_ID);
	$all = $obj_memCache->read('STABLE_ALL|'.$int_ID);

	// читаем из БД если ключ не иннициализирован - например после перезагрузки
	/*if ( empty($all) || empty($day) ) {  
		global $DB;
		$res = $DB->Query(sprintf('select DAY_COUNT AS CNT, 1 AS TYPE from a_stat where M_TYPE = 2 AND ELEMENT_ID = %d 
					UNION				
				   select DAY_COUNT AS CNT, 2 AS TYPE from a_stat where DAY_CALS = CURDATE()+0 AND ELEMENT_ID = %d 
				',$int_ID,$int_ID));

		$b_read_all = false;
		$b_read_day = false;
		while ($ar = $res->Fetch())  {

			if ( $ar["TYPE"]==1 ) {
				$all = $all<$ar['CNT'] ? $ar['CNT'] :$all;
				$b_read_all = true;
			} elseif( $ar["TYPE"]==2 ) {
				$day = $day<$ar['CNT'] ? $ar['CNT'] : $day;
				$b_read_day = true;
			}
		} //while
		$obj_memCache->write('STABLE_ALL|'.$int_ID, $b_read_all ? ($all?$all:1) : 1);
		$obj_memCache->write($int_ID, $b_read_day ? ($day?$day:1) : 1);
	}*/
	
	//p2f(array("PARAMS" => array($int_ID, $TYPE)), array("ALL" => $all, "DAY" => "day"), false);
	
	return Array('day'=>$day,'all'=>$all);
 }
