<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>
	<span class="pclose"></span>
	<span class="pzag">Подписка на новости</span>
	<form id="formsubscribe" action="" method="post">
	<?

if(isset($_POST['getemail']))
 {
   $email = htmlspecialchars($_POST["getemail"]);
        $p = '/^[a-z0-9]+(\.[a-z0-9]+)*';
        $p.= '@([a-z0-9]+\.)+([a-z]{2,3}';
        $p.= '|info|arpa|aero|coop|name|museum|mobi)$/ix';
       if(!preg_match($p,$email))
       {
        	$mail='';
        	echo '
			<p><font class="errortext">Некорректный адрес электронной почты<br/><br/></font></p>
			';
       }
	   else
	   {


	  	  $filter = Array (  "EMAIL"  => $email);
		  $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
		  if($res = $rsUsers->GetNext())
	      {
	            echo '     <script type="text/javascript">
     alert("Вы уже подписаны на новости!");
	 $("#subs .pclose").click();
     </script>';
		  		$err = 1 ;
	      }
		  else
		  {
		 		 $user = new CUser;
				 $new_password = randString(7);
				 $new_login = 'subscriber_'.$email;
		         $arFields = Array(
		             "EMAIL" => $email,
		             "LOGIN" => $new_login,
		             "PASSWORD" => $new_password,
		             "CONFIRM_PASSWORD" => $new_password,
		             "LID" => SITE_ID,
		             "ACTIVE" => "Y",
		             "GROUP_ID" => array(6,9),
		             "UF_SUBSCRIBER" => 1
		         );

		         $ID = $user->Add($arFields);
		 		 $arFields['USER_ID'] =$ID;
		         //echo '<pre>'.print_r($arFields,1).'</pre>'.__FILE__.' # '.__LINE__;
				 global $USER;
		         if (intval($ID) > 0)
				 {
     echo '     <script type="text/javascript">
     alert(" Вы успешно подписаны на новости!");
	 $("#subs .pclose").click();
     </script>';

		             //$USER->Authorize($ID);
					 //CEvent::Send("NEW_USER", "ru", $arFields);
					 //CEvent::SendImmediate("NEW_USER", "s1", $arFields);
		                      // echo '<pre>'.print_r($USER,1).'</pre>'.__FILE__.' # '.__LINE__;
		         }
				 else
				 {
    echo '     <script type="text/javascript">
     alert("Ошибка подписке на новости, обратитесь к администратору");
	 $("#subs .pclose").click();
     </script>';
					echo $user->LAST_ERROR;
					/**global $USER; if($USER->IsAdmin()) *///echo '<pre>'.print_r($arFields,1).'</pre>'.__FILE__.' # '.__LINE__;
				 		$err = 1 ;
				 }
	         }
	   }
}?>
	<div class="formBlock">
		<div class="row">
			<input class="inp"  name="getemail" placeholder="Ваш е-mail" type="text">
			<input class="sub" name="submit" value="Отправить" type="submit"  onclick="addSubscribe(); return false;">
		</div>
	</div>
 <?


/*echo '<div>
<form id="formsubscribe" action="" method="post">
<table class="reg" style="margin-left:0;">
<tr><td class="nm"><p>
</p></td>
<td>  <input type="text" placeholder="Мой e-mail" name="getemail">    </td></tr>
 <tr><td></td><td>	<input style="height: 36px; width: 153px;" class="buy-cat" type="submit" name="submit" value="Подписаться">   </td></tr>
</table>
</form>
</div>  ';*/

?>
   </form> 					 <?//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>