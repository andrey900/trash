<?php

namespace Studio8\Main;

class Handlers
{
	public static function removeBitrixCode(&$content){
		if( isset($GLOBALS['USER']) && $GLOBALS['USER']->IsAdmin())
			return;

		$content = preg_replace('#^(.*)<!DOCTYPE html>#siu', "<!DOCTYPE html>", $content);
		$content = preg_replace('#<link href="/bitrix/js/main/core/css/core.css\?(\d+)" type="text/css"  rel="stylesheet" />#siu', "", $content);
		$content = preg_replace('#<link href="/bitrix/js/main/core/css/core_popup.css\?(\d+)" type="text/css"  rel="stylesheet" />#siu', "", $content);
		$content = preg_replace('#<script type="text/javascript" src="/bitrix/js/main/core/([\w_]+).js\?(\d+)"></script>#siu', "", $content);
	}

	public static function removeTimeStampMarker(&$content){
		$content = preg_replace("#\.css(\?\d+)#sui", ".css", $content);
		$content = preg_replace("#\.js(\?\d+)#sui", ".js", $content);
		
		$content = str_replace('<link href="/bitrix/js/main/core/css/core.min.css" type="text/css"  rel="stylesheet" />', "", $content);
		$content = str_replace('<link href="/bitrix/js/main/core/css/core_popup.min.css" type="text/css"  rel="stylesheet" />', "", $content);
		$content = str_replace('<link href="/bitrix/panel/main/popup.css" type="text/css"  rel="stylesheet" />', "", $content);
	}
}