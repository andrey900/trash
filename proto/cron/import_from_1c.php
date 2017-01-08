<?
/**
 * Скрипт импортирующий данные из 1с. Проект LAPPUKRAINE. Особенность импорта заключается в том,
 * что передаются данные для трёх инфоблоков:
 *  import1.xml
 *  import2.xml
 *  import.xml
 *  offers1.xml
 *  offers2.xml
 *  offers.xml
 * Перед обработкой каждого из них необходимо определить это частичная выгрузка или полная и
 * установить соотвествующие параметры
 */

@set_time_limit(0);

/////////////////////////////////////////////////////////////////////////////////////
// Константы
/////////////////////////////////////////////////////////////////////////////////////
define("NOT_CHECK_PERMISSIONS", true);
define("NO_KEEP_STATISTIC", true);
define("ARCHIVE_DATE", date("Ymd"));

// ID пользователя от имени, которого работает скрипт в cron'е
define("ID_USER_CRON", 1);
// режим вывода отладочной информации в php
define("PHP_DEBUG_MODE", E_ERROR | E_WARNING | E_PARSE );
// режим отладки
define("DEBUG_MODE", false);

/////////////////////////////////////////////////////////////////////////////////////////////////////
// Чтобы не приходилось на разных хост-площадках прописывать руками $_SERVER["DOCUMENT_ROOT"]
// расчитываем пути к корню сайта. По умолчанию считаем, что скрипт выполняется в каталоге 1-го уровня
/////////////////////////////////////////////////////////////////////////////////////////////////////
$needAuth = false;

$fileInfo = pathinfo(__FILE__);

define("MAX_EXECUTION_TIME", 3600);
define("MAX_SIZE_FILE_LOG", 1*1024*1024);

define("SCRIPT_NAME", $fileInfo["basename"]);
define("SCRIPT_SHORT_NAME", $fileInfo["filename"]);
define("FILE_LOG_SHORT",$fileInfo["filename"].".log"); // краткое имя файла логов
define("FILE_PID", dirname(__FILE__)."/".SCRIPT_SHORT_NAME.".pid"); // Файл, содержащий PID скрипта
define("FILE_LOCK", dirname(__FILE__)."/".SCRIPT_SHORT_NAME.".lock"); // Файл-блокиратор
define("FILE_LOG", dirname(__FILE__)."/".SCRIPT_SHORT_NAME.".log");	// Файл логов
define("LOG_FILENAME", dirname(__FILE__)."/".SCRIPT_SHORT_NAME."_bitrix.log");	// Файл логов Битрикс

if (!$_SERVER["DOCUMENT_ROOT"]) {
	$arPartsPath = explode("/",$fileInfo["dirname"]);
	$pathRoot = "";

	for ($i = 1; $i < count($arPartsPath) - 1; $i++) {
		$pathRoot .= "/".$arPartsPath[$i];
	}

	$_SERVER["DOCUMENT_ROOT"] = $pathRoot;
} else {
	$needAuth = true;
}

define("EXCHANGE_DIR", $_SERVER["DOCUMENT_ROOT"]."/upload/1c_catalog");	// каталог обмена
define("ARCHIVE_DIR", EXCHANGE_DIR."/archive/".ARCHIVE_DATE);	// каталог для архивов

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
// если скрипт запущен через веб-интерфейс проверяем относится и пользователь к группе администраторов
if (($needAuth) && (!$USER->IsAdmin())) {
	die("Недостаточно прав для выполнения скрипта");
}

// авторизуемся под учётной записью с правами администратора, которую используют задачи cron
$USER->Authorize(ID_USER_CRON);

require_once("include/_".SCRIPT_NAME);

if (DEBUG_MODE) error_reporting(PHP_DEBUG_MODE);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arXmlFiles = array(
	$_SERVER["DOCUMENT_ROOT"].IMPORT_XML,
	$_SERVER["DOCUMENT_ROOT"].OFFERS_XML,
);

$arImportFiles = $arOffersFiles = array();

foreach (glob(EXCHANGE_DIR."/*.xml") as $filename) {
	if (strpos($filename, "import") > 0) $arImportFiles[] = $filename; 
	if (strpos($filename, "offers") > 0) $arOffersFiles[] = $filename;
	if (strpos($filename, "region") > 0) $arRegionsFiles[] = $filename;
}

$filesNotFound = (count($arImportFiles) + count($arOffersFiles)) == 0;

if ($filesNotFound) {
	Add2Log("[php] Нет xml-файлов для выгрузки в ".EXCHANGE_DIR);
	Add2Log("[php] Удаляем pid-файл.");
	unlink(FILE_PID);
	die();
}

Add2Log("[php] Импорт запущен (".date("Y-m-d H:i:s").")"); 

//////////////////////////////////////////////////////////////////////////////////
// Читаем файлы import.xml, offers.xml и обрабатываем их
//////////////////////////////////////////////////////////////////////////////////
foreach ($arImportFiles as $importFile) CCustomImport1C::Execute($importFile, false);
foreach ($arOffersFiles as $offersFile) CCustomImport1C::Execute($offersFile, true);
foreach ($arRegionsFiles as $regionsFile) CCustomImport1C::ExecuteRegion($regionsFile);

Add2Log("[php] Импорт завершен (".date("Y-m-d H:i:s").")");

// удаляем PID-файл
Add2Log("[php] Удаляем pid-файл.");
unlink(FILE_PID);

// если файл логов превышает размер в 1Мб, то его переносят в файл архивов для текущей
// операции
if (!DEBUG_MODE && file_exists(FILE_LOG) && filesize(FILE_LOG) > MAX_SIZE_FILE_LOG) {
	Add2Log("[php] Файл журнала превысил ".MAX_SIZE_FILE_LOG." байт. Переносим его в архив.");
	rename(FILE_LOG,ARCHIVE_DIR."/".FILE_LOG_SHORT);
	unlink(FILE_LOG);
}

?>
