<?php

namespace Studio8\Main\Ajax;

interface IAjaxHeandler
{
	public function ajaxStart();
	public function responseSuccess($mess);
	public function responseError($mess);
	public function getResponse();
}