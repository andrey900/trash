<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

 $har = (trim($_REQUEST['har']));
 $prop = (trim($_REQUEST['prop']));



 $arRes = getAllCharacteristics();

    //var_dump(count($arRes));
  //var_dump(empty($har));
  foreach($arRes as $nameHar => $values)
  {     //   echo '-------'.print_r($nameHar,1);
        if($_REQUEST['view'] == 'PROP')
		{
         if(empty($har) || (!empty($har) && preg_match("!^\s*$har!iu",$nameHar)))
	         foreach($values as $key=>$val)
			 {
			 	   //$val = trim(strtolower($val));
	               if(preg_match("!^\s*$prop!iu",$val))
			 		$arItog[$val] = $val;
			 }
		}
		else
		if($_REQUEST['view'] == 'DESCRIPTION')
		{
              if(!empty($har) && preg_match("!^\s*$har!iu",$nameHar))
             	 $arItog[$nameHar] = $nameHar;
		}
  }

    if(count($arItog) == 0)
		 $arItog[] = 'совпадений не найдено';

	$send = array('data'=>$arItog);
	header('Content-Type: application/json; charset=utf-8');
	$JAISON = json_encode($send);
	echo $JAISON;

?>