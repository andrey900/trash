<?php

if( $_REQUEST['logout'] ){
	$GLOBALS['USER']->Logout();
	LocalRedirect('/wholesalers/');
	die;
}