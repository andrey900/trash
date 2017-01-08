<?
class CImageEx
{
	const FONT_PATH = "/local/fonts/ubuntu-l.ttf";
	const FONT_SIZE = 16;
	const IMAGE_QUALITY = 95;
	const NO_PHOTO_TEXT = "NO PHOTO";
	const CACHE_DIR = "/bitrix/cache/resize_images";

	/**
	 * Метод проверяет установлен ли элемент DOCUMENT_ROOT в глобальном массиве $_SERVER.
	 * Если нет, то возвращает абсолютный путь следующего вида /tmp.$fileName иначе
	 * $_SERVER["DOCUMENT_ROOT"].$fileName
	 *
	 * @param string $fileName -- имя файла + относительный путь
	 * @return string
	 */
	private function GetAbsPath($fileName)
	{
		if (!isset($_SERVER["DOCUMENT_ROOT"]))
			return "/tmp".$fileName;
		else
			return $_SERVER["DOCUMENT_ROOT"].$fileName;
	}

	private function TranslitFileName($fileName)
	{
		if (function_exists("TranslitStr"))
			return TranslitStr(basename($fileName, ".jpg")).".jpg";
		else
			return $fileName;
	}

	private function GetFontPath() { return self::GetAbsPath(self::FONT_PATH); }

	/**
	 * Метод масштабирует изображение, центрируя его в прямоугольной области, указаного цвета. Полученное
	 * изображение сохраняется в кеше, контрольная сумма для которого рассчитывается исходя из пути к файлу,
	 * его размеру, дате модификации и (опционально) контрольной суммы.
	 *
	 * $arParams["SOURCE"] -- исходный файл
	 * $arParams["WIDTH"] -- ширина
	 * $arParams["HEIGHT"] -- высота
	 * $arParams["BG_COLOR"] -- цвет фона
	 *
	 * @param array $arParams -- массив параметров
	 */
	function Resize($arParams = array())
	{
		// @todo поставить анализатор входящих параметров
		
		$source = self::GetAbsPath($arParams["SOURCE"]);
		$width = $arParams["WIDTH"];
		$height = $arParams["HEIGHT"];
		$bg_color = (!isset($arParams["BG_COLOR"])?"FFFFFF":$arParams["BG_COLOR"]);
		$bg_rgb = array(hexdec(substr($bg_color,0,2)),hexdec(substr($bg_color,2,2)),hexdec(substr($bg_color,4,2)));

		// @todo переделать для работы с png
		$fileInfo = pathinfo($source);
		if (!in_array($fileInfo["extension"], array("jpeg", "jpg"))) return $arParams["SOURCE"]; 

		if (!file_exists(self::getAbsPath($arParams["SOURCE"])))
		{
			$img_dst = ImageCreateTrueColor($width, $height);
			$color_bg = ImageColorAllocate($img_dst, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
			ImageFill($img_dst, 0, 0, $color_bg);

			//! если файл отсуствует, то выводим сообщение инвертированным цветом
			$color_font = ImageColorAllocate($img_dst, 255 - $bg_rgb[0], 255 - $bg_rgb[1], 255 - $bg_rgb[2]);

			// рассчитываем размер шрифта в зависимости от размера изображения
			$font_size = min(round($width/8,0), self::FONT_SIZE);

			//! рассчитываем какую площадь занимает текст и центрируем его
			$t_box = ImageTTFBBox($font_size, 0, self::GetAbsPath(self::FONT_PATH), self::NO_PHOTO_TEXT);
			$x = $t_box[0] + ($width / 2) - ($t_box[4] / 2);
			$y = $t_box[1] + ($height / 2) - ($t_box[5] / 2);

			ImageTTFText($img_dst, $font_size, 0, $x, $y, $color_font, self::GetAbsPath(self::FONT_PATH), self::NO_PHOTO_TEXT);
				
			$cacheDir = self::CACHE_DIR."/".$width."x".$height;
			$fileCache = $cacheDir."/no-photo.jpg";
		}
		else
		{

			$arFile = CFile::MakeFileArray($arParams["SOURCE"]);
				
			if ($arParams["FULL_HASH"] == "Y")
				$arFile["MD5_HASH"] = md5_file($arFile["tmp_name"]);
				
			$arFile["EXT_HASH"] = sha1(serialize(array_merge($arParams, $arFile)));
				
			$cacheSubDir = substr($arFile["EXT_HASH"],0,3)."/".substr($arFile["EXT_HASH"],3,3);
				
			$cacheDir = self::CACHE_DIR."/".$width."x".$height."/".$cacheSubDir;
			$fileCache = $cacheDir."/".(($arParams["REAL_NAME"] == "N")?$cacheHash:self::TranslitFileName($arFile["name"]));

			if (!file_exists(self::GetAbsPath($fileCache)) || true)
			{
				// в кеше файл не обнаружен, формируем заново

				//! готовим фоновый слой
				$img_dst = ImageCreateTrueColor($width, $height);
				$color_bg = ImageColorAllocate($img_dst, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
				ImageFill($img_dst, 0, 0, $color_bg);
				
				//! обрабатываем изображение
				list($owidth,$oheight) = GetImageSize($source);

				$img_src = imagecreatefromjpeg($source);
				$off_x = 0;
				$off_y = 0;

				//! если размер оригинала больше указанного, то подгоняем его и позиционируем
				if (($owidth > $width) || ($oheight > $height)) {
					$ratio = min($width / $owidth, $height / $oheight);
				} else {
					$ratio = 1;
				}
				
				if ($ratio != 1 ) {
					$off_x = $width/2 - ($owidth * $ratio / 2);
					$width = $owidth * $ratio;
					$off_y = $height/2 - ($oheight * $ratio / 2);
					$height = $oheight * $ratio;
				} else {
					$off_y = $height/2 - ($oheight * $ratio / 2);
					$height = $oheight * $ratio;
					$off_x = $width/2 - ($owidth * $ratio / 2);
					$width = $owidth * $ratio;
				}
					
				// наложение на выходное изображение, исходного
				ImageCopyResampled($img_dst, $img_src, $off_x, $off_y, 0, 0, $width, $height, $owidth, $oheight);
				
					
				/**
				 * @todo
				 * Загружаем файл водяного знака и масштабируем его согласно DEFAULT_WM_SIZE
				 * и накладываем на полученное изображение согласно DEFAULT_POSITION, если
				 * не указаны дополнительные координаты
				 */
					
			}
		}

		// сохраняем в кеше
		if (!file_exists(self::GetAbsPath($cacheDir))) mkdir(self::GetAbsPath($cacheDir), BX_DIR_PERMISSIONS, true);
			
		imagejpeg($img_dst,self::GetAbsPath($fileCache),self::IMAGE_QUALITY);

		return $fileCache;
	}
}
