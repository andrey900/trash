<?php

function p($obj,$admOnly=true,$d=false)
{
    global $USER;
    
	if($USER->IsAdmin() || $admOnly===false)
	{
	    echo "<pre>";
	    print_r($obj);
	    echo "</pre>";

	    if($d===true)
		die();
	}
}

