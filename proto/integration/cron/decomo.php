<?
if( !$_SERVER['DOCUMENT_ROOT'] ){
	$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));
}

if( !empty($_SERVER['REMOTE_ADDR']) ){
	die('Script not run in shell');
}

// The file to store our process file
define('PROCESS_FILE', $_SERVER['DOCUMENT_ROOT'].'/cron/process.pid');
define('PRICES_FOLDER', $_SERVER['DOCUMENT_ROOT'].'/upload/prices/new/');
define('ARCHIVE_FOLDER', $_SERVER['DOCUMENT_ROOT'].'/upload/prices/archive/');

$pid_running = false;

// Check if I'm already running and kill myself off if I am
if (file_exists(PROCESS_FILE)) {
    $data = file(PROCESS_FILE);
    foreach ($data as $pid) {
        $pid = (int)$pid;
        if ($pid > 0 && file_exists('/proc/' . $pid)) {
            $pid_running = $pid;
            break;
        }
    }
}

if ($pid_running && $pid_running != getmypid()) {
    die('Script is running');
} else {
    // Make sure file has just me in it
    file_put_contents(PROCESS_FILE, getmypid());
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

Cmodule::IncludeModule('catalog');

//$arRes = CElectrodomTools::_GetInfoElements(false, ['ID', 'XML_ID', 'NAME', "PROPERTY_49"], ["ACTIVE"=>'Y', 'DETAIL_PICTURE'=>false, 'IBLOCK_ID'=>12 ]);
/**/

//$files = array_slice(scandir(PRICES_FOLDER), 2);

$arFiles = ParserCsvPrices::scanFilesInFolder(PRICES_FOLDER);

try {
	foreach ($arFiles as $file) {
		ParserCsvPrices::parseCsvFile($file);

		$bitrix = new BitrixWorker();
		$bitrix->setVendors(ParserCsvPrices::getVendors());
		$arBrands = $bitrix->getVendorsInfo();

		foreach($arBrands as $brand){
			$cntAll = count(ParserCsvPrices::$fileData[strtolower($brand['NAME'])]);
			$pageItem = 10;

			for( $c=0; ceil($cntAll/$pageItem) > $c; $c++ ){
				$arItems = array_slice( ParserCsvPrices::$fileData[strtolower($brand['NAME'])], $c*$pageItem, $pageItem );

				$bitrix->setItems($arItems);
				$bitrix->findItems();
			}
		}
		ParserCsvPrices::moveToArchive($file);
	}
} catch (Exception $e) {
	unlink(PROCESS_FILE);
}

unlink(PROCESS_FILE);
//echo $_SERVER["DOCUMENT_ROOT"];

/**
* 
*/
class BitrixWorker
{
	
	protected $arItems;
	protected $arVendors;

	public function __construct(array $arItems = array())
	{
		$this->arItems = $arItems;
	}

	public function setItems(array $arItems)
	{
		$this->arItems = $arItems;
	}

	public function setVendors(array $arVendors)
	{
		$this->arVendors = $arVendors;
	}

	public function getVendorsInfo()
	{
		if( !$this->arVendors )
			return array();

		// тупо битрикс-стайл
		$arRes = CElectrodomTools::_GetInfoElements(false, ['ID', 'NAME'], ['IBLOCK_ID'=>8, '?NAME'=>"(".implode(' || ', $this->arVendors).")" ]);

		foreach ($arRes as $item) {
			$this->vendorsData[strtolower($item['NAME'])] = $item;
		}

		return $arRes;
	}

	public function findItems()
	{
		if( !$this->arVendors )
			$this->getVendorsInfo();

		$arArticles = [];
		foreach ($this->arItems as $item) {
			$arArticles[] = $item->article;
			$arItems[strtolower($item->article)] = $item;
		}

		$arRes = CElectrodomTools::_GetInfoElements(false, ['ID', 'NAME', "PROPERTY_49"], ['IBLOCK_ID'=>12, 'PROPERTY_49'=>$arArticles, "PROPERTY_68" => $this->vendorsData[strtolower($this->arItems[0]->vendor)]['ID'] ]);

$strLog = "--- Articles ---".PHP_EOL;
$strLog .= print_r($arArticles, 1);
$strLog .= "--- Brands ---".PHP_EOL;
$strLog .= print_r($this->vendorsData[strtolower($this->arItems[0]->vendor)]['ID'], 1);
$strLog .= "--- Result ---".PHP_EOL;
$strLog .= print_r($arRes, 1);
$strLog .= "--- END ---".PHP_EOL;
file_put_contents($_SERVER['DOCUMENT_ROOT'].'/decomo_nalichie.log', $strLog, FILE_APPEND);

		if( $arRes ){
		    foreach($arRes as $arItem){
			$this->updateQuantity($arItem['ID'], $arItems[strtolower($arItem['PROPERTY_49_VALUE'])]->quantity, $arItems[strtolower($arItem['PROPERTY_49_VALUE'])]->price);
		    }
		}
	}

	public function updateQuantity($id, $quantity=null, $price=null)
	{
		if(!$id)
			return;

		$flag = (int)($price > 0);

		if( $quantity !== null ){
			CCatalogProduct::Update((int)$id, ['QUANTITY' => $quantity]);
			CIBlockElement::SetPropertyValuesEx((int)$id, 12, ['1066' => 1]);
		}
		
		if( $price !== null ){
			CPrice::SetBasePrice((int)$id, $price, 'USD');
			CIBlockElement::SetPropertyValuesEx((int)$id, 12, ['1022' => $flag]);
		}
	}
}

/**
* 
*/
class ParserCsvPrices
{
	const EXT_FILE = 'csv';
	const DELIMITER = ";";

	public static $fileData = array();
	public static $fileFields = array();

	protected static $arVendors = array();
	protected static $lastParseLine;

	protected static $fieldCnt;

	public static function parseCsvFile($file){
		if (($handle = fopen(PRICES_FOLDER.$file, "r")) !== FALSE) {

			self::$fileData = array();
			self::$arVendors = array();

			self::parseFirstLine($handle);

		    while (($data = fgetcsv($handle, 0, self::DELIMITER)) !== FALSE ) {
		        self::parseLineCsv($data);
		        self::addVendor();
		    }

		    fclose($handle);
		}
	}

	protected static function addVendor()
	{
		if( is_object(self::$lastParseLine) )
			self::$arVendors[self::$lastParseLine->vendor] = self::$lastParseLine->vendor;
		
		return self;
	}

	protected static function parseFirstLine($handle)
	{
		if(($data = fgetcsv($handle, 0, self::DELIMITER)) !== false ){
			self::$fieldCnt = count($data);

        	for ($c=0; $c < self::$fieldCnt; $c++) {
	            self::$fileFields[$c] = strtolower($data[$c]);
	        }
		}
	}

	protected static function parseLineCsv($dataLine)
	{
		if(($num = count($dataLine)) != self::$fieldCnt){
			self::$lastParseLine = null;
			return;
		}

        $obj = new \stdClass();

        for ($c=0; $c < $num; $c++) {
            $obj->{self::$fileFields[$c]} = (string)$dataLine[$c];
        }

        self::$fileData[strtolower($obj->vendor)][] = $obj;
        self::$lastParseLine = $obj;
	}

	public static function scanFilesInFolder($folder){
		$arRes = array();
		
		$arItems = array_slice(scandir($folder), 2);
		
		if( !$arItems )
			return $arRes;

		foreach ($arItems as $filename) {
			if( substr(strrchr($filename, "."), 1) == self::EXT_FILE )
				$arRes[] = $filename;
		}
		
		return $arRes;
	}

	public static function moveToArchive($fileName){
		if( file_exists(PRICES_FOLDER.$fileName) ){
			//rename(PRICES_FOLDER.'1.csv', ARCHIVE_FOLDER.'1_'.date("d-m_H-i-s").'.csv');
			$zip = new ZipArchive();
			$filename = ARCHIVE_FOLDER.basename($fileName, '.csv')."_".date("d-m_H-i-s").".zip";

			if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
			    throw new Exception("Невозможно открыть <$filename>\n", 1);
			}

			$zip->addFile(PRICES_FOLDER.$fileName);
			$zip->close();
			unlink(PRICES_FOLDER.$fileName);
		}
	}

	public static function getVendors()
	{
		return array_values(self::$arVendors);
	}
}
