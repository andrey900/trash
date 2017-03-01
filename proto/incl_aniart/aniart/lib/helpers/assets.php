<?php

namespace Studio8\Main\Helpers;

use Bitrix\Main\Page\Asset;

/**
* 
*/
class Assets
{
	protected $includeJs = [
		"/js/bootstrap.min.js",
		"/js/main.js"
	];

	protected $includeCss = [
		"/css/bootstrap.min.css",
		"/css/fix.css",
		"/css/font-awesome.min.css",
		"/css/icofont.css",
		"/css/owl.carousel.css",
		"/css/slidr.css",
		"/css/main.css",
		"/css/presets/preset1.css",
		"/css/responsive.css"
	];

	public function registerCss(){
		foreach ($this->includeCss as $cssPath){
        	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.$cssPath);
		}
	}

	public function registerJs(){
		foreach ($this->includeJs as $jsPath){
        	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.$jsPath);
		}
	}

	public function init(){
		$this->registerCss();
		$this->registerJs();
	}

	public function getCss(){
		return $this->includeCss;
	}

	public function getJs(){
		return $this->includeJs;
	}
}