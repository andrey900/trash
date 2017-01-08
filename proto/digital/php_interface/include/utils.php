<?
/**
 * Функция выводит отладочную информацию (замена pre+print_r+pre) на экран
 * 
 * @param any $obj -- объект, значение которого выводят
 * @param boolean $admOnly -- функция доступна только администартору
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
 * Функция выводит отладочную информацию (замена pre+print_r+pre) в файл
 * 
 * @param any $obj -- объект, значение которого выводят
 * @param boolean $admOnly -- функция доступна только администартору
 * @param boolean $die -- остановить выполнение скрипта
 * @return boolean
 */
function p2f($obj, $admOnly=true, $die = false) {
	global $USER;
	if($admOnly===false || $USER->IsAdmin()) {
		$dump="<pre style='font-size: 11px; font-family: tahoma;'>".print_r($obj, true)."</pre>";
		$files = $_SERVER["DOCUMENT_ROOT"]."/_dump.html";
		$fp = fopen( $files, "a+" );
		fwrite( $fp, $dump);
		fclose( $fp );
		if ($die) die(); 
	}
}

/**
 * Функция возвращает кол-во подразделов у текущего раздела
 *
 * @param integer $iblockID
 * @param integer $sectionParentID
 * @return integer
 */
function GetCountSectionChild($iblockID, $sectionParentID) {
	$countSectionChild = 0;

	if (empty($iblockID)) return $countSectionChild;

	if (CModule::IncludeModule("iblock")) {
		$arFilter = array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"GLOBAL_ACTIVE" => "Y",
				"SECTION_ID" => $sectionParentID
		);

		$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID","IBLOCK_ID"));
		if ($arSection = $dbRes->GetNext()) {
			$countSectionChild++;
			while ($arSection = $dbRes->GetNext()) $countSectionChild++;
		}
	}
	return $countSectionChild;
}
?>