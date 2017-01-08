<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); ?><?

		$mail=trim($_POST['subscrib']);
        $p = '/^[a-z0-9]+([a-z0-9-_]+)*';
        $p.= '@([a-z0-9]+\.)+([a-z]{2,3}';
        $p.= '|info|arpa|aero|coop|name|museum|mobi)$/ix';

       if(!preg_match($p,$mail))
       {
        $mail='';
         echo 'Некорректный адрес электронной почты';
		 return;
       }

	   if(CModule::IncludeModule("iblock"))
	   {
                     $arSelect = Array('NAME');
                     $arFilter = Array('NAME'=>$mail);
                     $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                     if($ob = $res->GetNext())
                     {
                             echo 'Вы уже подписаны!';
							 return;
                     }

					 $el = new CIBlockElement;
					 $arLoadProductArray = Array(
					 	"IBLOCK_SECTION_ID" => false,
					 	"IBLOCK_ID"      => 5,
					 	"NAME"           => $mail,
					 	"ACTIVE"         => "Y",
					 );

					 if($ID = $el->Add($arLoadProductArray)){
					 	     echo 'Вы успешно подписаны!';
							 return;
					 }else{
					 		echo 'Ошибка! Обратитесь к Администратору! ';
         					return;
					 }
	   }
	   else
	   {
       		echo 'Ошибка! Обратитесь к Администратору!';
         	return;
	   }
	   ?>
?>