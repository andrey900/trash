<?
/*
* Plugin fancybox-2.1.5 works correctly with jquery-1.10.1 only.
* For compatibility with other applications or plagins which use different
* versions jquery, is envisaged the no-Conflict mode with alias "jq110".
*
* If you need to use standard mode jquery, and version 1.10.1 is compatible
* for further development, comment the block "jQuery no-Conflict mode with
* alias jq110" and uncomment the block "jQuery standard mode with alias $".
* For all other cases leave as is.
*
*/

CAjax::Init();

/* jQuery standard mode with alias "$" */
/*
$APPLICATION->AddHeadScript($arResult["CUR_TEMPLATE_PATH"] . "/js/lib/jquery-1.10.1.min.js");
$APPLICATION->AddHeadScript($arResult["CUR_TEMPLATE_PATH"] . "/js/fancybox/jquery.fancybox.js?v=2.1.5");
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="' . $arResult["CUR_TEMPLATE_PATH"] . '/js/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" >',true);
$APPLICATION->AddHeadScript($arResult["CUR_TEMPLATE_PATH"] . "/js/wsfunc.js");
*/


/* jQuery no-Conflict mode with alias "jq110" */

$head = '<script type="text/javascript">';
$head_src = '<script type="text/javascript" src="' . $arResult["CUR_TEMPLATE_PATH"];

$APPLICATION->AddHeadString($head_src . '/js/lib/jquery-1.10.1.min.js"></script>',true);
$APPLICATION->AddHeadString($head . 'jq110 = jQuery.noConflict(true);</script>',true);
$APPLICATION->AddHeadString($head_src . '/js/fancybox/jquery.fancybox_nc.js?v=2.1.5"></script>',true);
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="' . $arResult["CUR_TEMPLATE_PATH"] . '/js/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" >',true);
$APPLICATION->AddHeadString($head_src . '/js/wsfunc_nc.js"></script>',true);

//echo "<pre>"; print_r($arResult); echo "</pre>";

?>