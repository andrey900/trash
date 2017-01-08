<?

if($_POST["newcapcha"] == "Y")
{
	echo htmlspecialchars($APPLICATION->CaptchaGetCode());
    return;
}

if($arParams['USE_JQUERY_FORMS'] == 'Y')  // подключени js файла обработчика форм
{
	if(!defined('addJqueryFormLoadHead'))
	{
	   define('addJqueryFormLoadHead',1);
       	$APPLICATION->AddHeadString('<script type="text/javascript" src="'.substr(dirname(__FILE__), strrpos(__FILE__, '/bitrix/')).'/jquery.form.js"></script>',true);
    	$APPLICATION->AddHeadString('<script type="text/javascript" src="'.substr(dirname(__FILE__), strrpos(__FILE__, '/bitrix/')).'/forms.function.js"></script>',true);
	}
}

if(!function_exists('get_arrayNameParams'))
{
  	function get_arrayNameParams($FIELD)
	{

	   if(substr($FIELD,-2,2) == '[]')
	   {
	   		return substr($FIELD,0,-2);
	   }
	return false;
	}
}

if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");
$arParams["EVENT_NAME"] = trim($arParams["EVENT_NAME"]);
if(strlen($arParams["EVENT_NAME"]) <= 0)
	$arParams["EVENT_NAME"] = "FEEDBACK_FORM";
$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if(strlen($arParams["OK_TEXT"]) <= 0)
	$arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $ok = ($_POST["formname"]== trim($arParams['formname']));
}
else
	$ok=true;


 session_start();
 //echo '<pre>'.print_r($_SESSION['feed_back_form'],1).'</pre>'.__FILE__.' # '.__LINE__;
 if(!empty($_REQUEST['datas']))
 {
     $datas = $_REQUEST['datas'];
     $arrSesParams = $_SESSION['feed_back_form'][$datas]['arParams'];
	 if(count($arrSesParams)>0)
	 {
     		foreach($arrSesParams as $key=>$val)
			{
				if(!is_array($arParams[$key]))
				{
                	if(empty($arParams[$key]) && !empty($val))
						$arParams[$key] = $val;
				}
				else
				{
                    if(count($arParams[$key]) ==0 && count($val) > 0)
                        $arParams[$key] = $val;
				}
			}
	 }
 }



$datas = md5(serialize($arParams));
$_SESSION['feed_back_form'][$datas]['arParams'] = $arParams;
$arResult['data'] = ' <input type="hidden" name="datas" value="'.$datas.'"> ';

if(!empty($arParams['formname']))
	$arResult['data'] .= ' <input type="hidden" name="formname" value="'.$arParams['formname'].'">';

if($_SERVER["REQUEST_METHOD"] != "POST")
{
	//если есть данные при вызове формы, помещаем их в сессию чтобы при аякс запросе их не потерять
	$arrSess = array();
	foreach($arParams['MAIL_FIELDS'] as $OF)
	{
		if(is_array($OF))// только через массивы можно задать данные, остальные поля не трогаем
		{
		   $arrSess[$OF[0]] = htmlspecialchars($OF[1]);
	    }
	}

	if(count($arrSess) > 0)
	{

		//$datas = md5(serialize($arrSess));
		$_SESSION['feed_back_form'][$datas]['value'] = $arrSess;
		$arResult['session_data'] = '<input type="hidden" name="datas" value="'.$datas.'">';
	}
}


if(($_SERVER["REQUEST_METHOD"] == "POST") &&( strlen($_POST["submit"]) > 0) && $ok)
{

	if(!empty($_REQUEST['datas'])) // если есть даные в сесии то присоединяем их к посту
	{
        session_start();
		if(is_array($_SESSION['feed_back_form'][$_REQUEST['datas']]['value']))
		{
			$_POST = array_merge($_POST,$_SESSION['feed_back_form'][$_REQUEST['datas']]['value']);
			//echo '<pre>'.print_r($_SESSION['feed_back_form'][$_REQUEST['datas']],1).'</pre>'.__FILE__.' # '.__LINE__;
		}
	}
	if(check_bitrix_sessid())
	{
		
		if(in_array('email', $arParams['REQUIRED_FIELDS']) && strlen($_POST["email"]) > 1 && !check_email($_POST["email"]))
			$arResult["ERROR_MESSAGE"][] = 'Вы указали не верный email';

        $NOT_SPEC='';
             foreach ($arParams['REQUIRED_FIELDS'] as $NAME=>$FIELD)
            {   $err = 0;
			    if($FILEDS_ARR = get_arrayNameParams($FIELD))
				{      // echo '<pre>'.print_r($FILEDS_ARR,1).'</pre>'.__FILE__.' # '.__LINE__;
                    if(count($_POST[$FILEDS_ARR]) == 0)
						$err = 1;
				}
				elseif($_POST[$FIELD]=='')
                {
                    $err = 1;
                }
				if($err) $arResult["ERROR_MESSAGE"][] = 'Укажите "'.$NAME.'"';
            }

		if($arParams["USE_CAPTCHA"] == "Y")
		{
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$captcha_code = $_POST["captcha_sid"];
			$captcha_word = $_POST["captcha_word"];
			$cpt = new CCaptcha();
			$captchaPass = COption::GetOptionString("main", "captcha_password", "");
			if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
			{
				if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
					$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTCHA_WRONG");
			}
			else
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTHCA_EMPTY");

		}


		if(count($arResult["ERROR_MESSAGE"])==0)
		{
            if(!empty($_REQUEST['datas'])) // если есть даные в сесии то присоединяем их к посту
			{
					$arResult['session_data'] = '<input type="hidden" name="datas" value="'.$_REQUEST['datas'].'">';
			}
			// Поля

			foreach($arParams['MAIL_FIELDS'] as $OF)
			{
				if(is_array($OF))
				{
				 		$arFields[$OF[0]]=htmlspecialchars($OF[1]);
				 		$OF = $OF[0];

                }
				if($FILEDS_ARR = get_arrayNameParams($OF))
				{
					if(is_array($_POST[$FILEDS_ARR]))
                     	foreach($_POST[$FILEDS_ARR] as $key => $val)
							  $arFields[$OF][$key] = htmlspecialchars($val);

                    if(count( $arFields[$OF]) > 0)
						 $arFields[$OF] = implode(', ', $arFields[$OF]);
				}
				else
				   if(empty($arFields[$OF])) $arFields[$OF] = htmlspecialchars($_POST[$OF]);
			}

                /* $tttfile=$_SERVER["DOCUMENT_ROOT"].'/txt.php';
                 file_put_contents($tttfile,  date("d.m.Y H:i:s").'#'.substr(microtime(true).' ',-5,5). '
                 <pre>'.print_r($arFields,1).'</pre>'.__FILE__.' # '.__LINE__.'<hr/> '.file_get_contents($tttfile));  */
			//----------------------------------
			if($arParams["SAVE_TO_IBLOCK"]=='Y')
            {
                            CModule::IncludeModule('iblock');
                            $OBJ=new CIBlockElement;
                            $arIBlockFields=array();
                            $arIBlockFields['IBLOCK_ID']=$arParams["IBLOCK_ID"];

                            foreach ($arParams["IBLOCK_FIELDS_ASSOCIATHION"] as $NAME=>$VAL)
                            {
                              $arIBlockFields[$NAME]=$arFields[$VAL];
                            }
                               		/*$tttfile=$_SERVER["DOCUMENT_ROOT"].'/txt.php';
											file_put_contents($tttfile,  date("d.m.Y H:i:s").'#'.substr(microtime(true).' ',-5,5). '
											<pre>'.print_r(array($_FILES),1).'</pre>'.__FILE__.' # '.__LINE__.'<hr/> '.file_get_contents($tttfile));
                               */


                            if(!empty ($arParams["IBLOCK_PROPERTYES_ASSOCIATION"]))
                            {
                                $TEMP=array();
                                foreach ($arParams["IBLOCK_PROPERTYES_ASSOCIATION"] as $NAME=>$VAL)
                                {
									if(stripos($VAL,'file')!==false) // если прикрепляем файл
									{

                                            if(is_file($_FILES[$VAL]['tmp_name']))
											{

      									 	   	$cFile = CFile::MakeFileArray($_FILES[$VAL]['tmp_name']);
											   //echo '<pre>'.print_r($arResult,1).'</pre>'.__FILE__.' # '.__LINE__;
												$TEMP[$NAME]=$cFile;
											}
									}
									else
                                    	$TEMP[$NAME]=$arFields[$VAL];
                                }
                                $arIBlockFields['PROPERTY_VALUES']=$TEMP;
                            }


                            if($ELEMENT_ID = $OBJ->Add($arIBlockFields))
                            {
                                $arFields['ELEMENT_ID'] = $ELEMENT_ID;
								session_start();
                                $_SESSION['eventFields'][$arParams['formname']] = $arFields;


                            }
                            else $arResult["ERROR_MESSAGE"][] = "Ошибка добавления элемента в инфоблок: ".$OBJ->LAST_ERROR;
            }

			if(!empty($arParams["EVENT_MESSAGE_ID"]))
			{
				foreach($arParams["EVENT_MESSAGE_ID"] as $v)
					if(IntVal($v) > 0)
						CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, "N", IntVal($v));
			}
			else
				CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields);

            if(count($arResult["ERROR_MESSAGE"])==0)
			{
				$_SESSION["MF_NAME"] = htmlspecialcharsEx($_POST["user_name"]);
				$_SESSION["MF_EMAIL"] = htmlspecialcharsEx($_POST["user_email"]);


                //echo '<script>alert("'.$arParams["OK_TEXT"].'")</script>';
                //$this->IncludeComponentTemplate();
			   	//LocalRedirect($APPLICATION->GetCurPageParam("success=Y&datas=".$_REQUEST['datas'], Array("success",'datas')));
			   	$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
            }
		}
		
		$arResult["MESSAGE"] = htmlspecialcharsEx($_POST["MESSAGE"]);
		$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_POST["user_name"]);
		$arResult["AUTHOR_EMAIL"] = htmlspecialcharsEx($_POST["user_email"]);
	}
	else
		$arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP");
} //if(($_SERVER["REQUEST_METHOD"] == "POST") &&( strlen($_POST["submit"]) > 0) && $ok)
elseif(($_REQUEST["success"] == "Y")&& $ok )
{
	$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
}

if(empty($arResult["ERROR_MESSAGE"]))
{
	if($USER->IsAuthorized())
	{
		$arResult["AUTHOR_NAME"] = $USER->GetFullName();
		$arResult["AUTHOR_EMAIL"] = $USER->GetEmail();
	}
	else
	{
		if(strlen($_SESSION["MF_NAME"]) > 0)
			$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_SESSION["MF_NAME"]);
		if(strlen($_SESSION["MF_EMAIL"]) > 0)
			$arResult["AUTHOR_EMAIL"] = htmlspecialcharsEx($_SESSION["MF_EMAIL"]);
	}
}

if($arParams["USE_CAPTCHA"] == "Y")
	$arResult["capCode"] =  htmlspecialchars($APPLICATION->CaptchaGetCode());
//Присоединяем другие поля
			foreach($arParams['MAIL_FIELDS'] as $OF)
			{
				if(is_array($OF))
				{
				 	   //	$arResult[$OF[0]]= htmlspecialchars($OF[1]);
					  $arResult[$OF[0]]=htmlspecialchars($OF[1]);
                      $OF = $OF[0];

                }
				if($FILEDS_ARR = get_arrayNameParams($OF))
				{
					if(is_array($_POST[$FILEDS_ARR]))
                     	foreach($_POST[$FILEDS_ARR] as $key => $val)
							  $arResult[$OF][$key] = htmlspecialchars($val);

                    if(count( $arResult[$OF]) > 0)
						 $arResult[$OF] = implode(', ', $arResult[$OF]);
				}
				else
				    if(empty($arResult[$OF]))	$arResult[$OF]=htmlspecialcharsEx($_POST[$OF]);
			}

//---------------------
if(!empty($_REQUEST['datas'])) // если есть даные в сесии то присоединяем их к посту
{
		$arResult['session_data'] = '<input type="hidden" name="datas" value="'.$_REQUEST['datas'].'">';
}
$this->IncludeComponentTemplate();


?>