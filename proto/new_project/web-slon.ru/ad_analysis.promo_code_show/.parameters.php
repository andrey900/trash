<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$arComponentParameters = array(
	"GROUPS" => array(
		"FORM_SETTINGS" => array(
			"NAME" => GetMessage("SETTINGS_PHR"),
			"SORT" => 110,
		),
	),
    'PARAMETERS' => array(
//        'CACHE_TIME'  =>  array('DEFAULT'=>3600),
    	"CAPTION"  =>  array(
    		"PARENT" => "FORM_SETTINGS",
    		"NAME" => GetMessage("CAPTION_NAME"),
    		"TYPE" => "TEXT",
    		"DEFAULT"=>GetMessage("CAPTION_DEFAULT")
    	),
    ),
);
?>
