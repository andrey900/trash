<?php
/**
 * Функция выводит отладочную информацию (замена pre+print_r+pre) на экран
 *
 * @param any $obj -- объект, значение которого выводт
 * @param boolean $admOnly -- функци¤ доступна только администартору
 * @param boolean $die -- остановить выполнение скрипта
 * @return boolean
 */
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

/**
 * Выводит на странице var_dump обрамленный в тег <pre>
 *  - если последний передаваемый параметр === true, то вызывается die;
 */
function pre_dump()
{
	$arguments	= func_get_args();
	$die		= array_pop($arguments);
	if(!is_bool($die)){
		$arguments[] = $die;		
	}
	echo "<br clear='all' />";
	echo "<pre>";
	call_user_func_array('var_dump', $arguments);
	echo "</pre>";
	if($die === true){
		die;
	}
}

/**
 *  Выводит на странице var_dump обрамленный в тег <pre>, удаляет весь предшествующий вывод (для битрикса)
 *   - если последним параметром не указано false, то вызывается die;
 */

function pre_dump_clr()
{
	static $notToDiscard;
	global $APPLICATION;
	if(is_object($APPLICATION) && !$notToDiscard){
		$APPLICATION->RestartBuffer();
		$notToDiscard = true;
	}
	$arguments	= func_get_args();
	$arg_count	= count($arguments);
	if(!is_bool($arguments[$arg_count-1])){
		$arguments[] = true;
	}
	call_user_func_array('pre_dump', $arguments);
}

/**
 *  Выводит на странице var_dump в popup-окне(для битрикса) или alert
 */

function pre_dump_win()
{
	$number = rand(1, 1000).'_' .rand(1, 1000);
	$arguments = func_get_args();
	ob_start();
		call_user_func_array('var_dump', $arguments);
		$dump = ob_get_contents();
		$dump = addslashes($dump);
		$dump_alert = str_replace("\n", "\\n", $dump);
		$dump_popup = "<pre style=\"font-size: 11px; max-height:480px; max-width: 640px; overflow-y:auto; overflow-x: hidden;\">".$dump_alert."</pre>";
	ob_end_clean();
	$script = <<<SCRIPT
		<script type="text/javascript">
			(function(){
				var BX = false;
				if(window.BX){
					BX = window.BX;
				}
				else if(window.parent.BX){
					BX = window.parent.BX;
				}
				if(BX){
					var Popup{$number} = new BX.PopupWindow('pre_dump_win{$number}', null, {
						content: '{$dump_popup}',
						titleBar: {content: BX.create("b", {html: 'Dump #{$number}'})},
						lightShadow : true,
						closeIcon: {right: "20px", top: "10px"},
						closeByEsc : true,
						overlay: false,
						draggable: {restrict: true},
					});
					Popup{$number}.show();
				}
				else{
					alert('{$dump_alert}');
				}
			})();
		</script>
SCRIPT;
	echo $script;
	$number++;
}

/**
 *  Выводит var_dump в окне, если функция была выполнена в ajax-обработчике и предполагается, что ответ будет вставлен на страницу
 */

function pre_dump_ajax()
{
	global $APPLICATION;
	if(is_object($APPLICATION)){
		$APPLICATION->RestartBuffer();
	}
	$arguments = func_get_args();
	ob_start();
		call_user_func_array('pre_dump_win', $arguments);
		$script = ob_get_contents();
	ob_end_clean();
	echo $script;
	die;
}
?>