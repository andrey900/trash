<?
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Studio8\Main\Helpers;

$arElements = Helpers::_GetInfoElements(false, ["ID", 'DETAIL_PICTURE'], ['IBLOCK_ID' => IBLOCK_CATALOG_ID, 'ACTIVE' => "Y", "!DETAIL_PICTURE" => false]);

foreach ($arElements as $item) {
	$picture = CFile::GetFileArray($item['~DETAIL_PICTURE']);
	if( $picture['WIDTH'] < 630 || $picture['HEIGHT'] < 700 ){
		$file = $product->detailPicture['ID'];
		$arFile = \CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$picture['SRC']);
		\CAllFile::ResizeImage(
	      $arFile, // путь к изображению, сюда же будет записан уменьшенный файл
	      array(
	       "width" => 630,  // новая ширина
	       "height" => 700 // новая высота
	      ),
	      BX_RESIZE_IMAGE_EXACT // метод масштабирования. обрезать прямоугольник без учета пропорций
	    );

	   $el = new \CIBlockElement;
	   $el->Update($item['ID'], ["DETAIL_PICTURE"=>$arFile]);
	}
}