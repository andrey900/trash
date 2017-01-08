<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$arComponentParameters = array(
	"GROUPS" => array(
		"FORM_SETTINGS" => array(
			"NAME" => GetMessage("SETTINGS_PHR"),
			"SORT" => 110,
		),
	),
    "PARAMETERS" => array(
    	"BUTTON_CAPTION"  =>  array(
    		"PARENT" => "FORM_SETTINGS",
    		"NAME" => GetMessage("BUTTON_CAPTION_NAME"),
    		"TYPE" => "TEXT",
    		"DEFAULT"=>GetMessage("BUTTON_CAPTION_DEFAULT")
    	),
    	"SUCCESS"  =>  array(
    		"PARENT" => "FORM_SETTINGS",
    		"NAME" => GetMessage("SUCCESS_MESSAGE"),
    		"TYPE" => "TEXT",
    		"DEFAULT"=>GetMessage("SUCCESS_MESSAGE_DEFAULT")
    	),
    ),
);
?>
