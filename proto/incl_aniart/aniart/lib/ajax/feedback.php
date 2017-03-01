<?php

namespace Studio8\Main\Ajax;

/**
* 
*/
class Feedback extends AAjaxHeandler
{
	public function ajaxStart(){
		$reqData = $this->request;

		if( check_bitrix_sessid() ){
			$this->responseError('Ошибка обработки запроса');
			return;
		}

		if($reqData->name && ($reqData->email || $reqData->phone) && $reqData->subject && $reqData->message ){
			$arFields = Array(
				"TEXT" => $reqData->message,
				"PHONE" => $reqData->phone,
				"AUTHOR" => $reqData->name,
				"SUBJECT" => $reqData->subject,
				"EMAIL_TO" => 'ba_ndrey@ukr.net',
				"AUTHOR_EMAIL" => $reqData->email,
			);
			\CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields);
			$this->responseSuccess('Thanks - you message send!');
		} else {
			$this->responseError('Ошибка ввода данных');
		}
	}
}