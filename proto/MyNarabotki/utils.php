<?php
/**
 * Функция транслитерирует строку
 *
 * @param string $string
 * @return string
 */
function ConvTranslit($string, $alternative = false) {
	$replace = array(
			"'"=>"",
			"`"=>"",
			"а"=>"a","А"=>"A",
			"б"=>"b","Б"=>"B",
			"в"=>"v","В"=>"V",
			"г"=>"g","Г"=>"G",
			"д"=>"d","Д"=>"D",
			"е"=>"e","Е"=>"E",
			"ж"=>"zh","Ж"=>"Zh",
			"з"=>"z","З"=>"Z",
			"и"=>"i","И"=>"I",
			"й"=>"y","Й"=>"Y",
			"к"=>"k","К"=>"K",
			"л"=>"l","Л"=>"L",
			"м"=>"m","М"=>"M",
			"н"=>"n","Н"=>"N",
			"о"=>"o","О"=>"O",
			"п"=>"p","П"=>"P",
			"р"=>"r","Р"=>"R",
			"с"=>"s","С"=>"S",
			"т"=>"t","Т"=>"T",
			"у"=>"u","У"=>"U",
			"ф"=>"f","Ф"=>"f",
			"х"=>"h","Х"=>"H",
			"ц"=>"c","Ц"=>"C",
			"ч"=>"ch","Ч"=>"Ch",
			"ш"=>"sh","Ш"=>"Sh",
			"щ"=>"sch","Щ"=>"Sch",
			"ъ"=>"","Ъ"=>"",
			"ы"=>"y","Ы"=>"Y",
			"ь"=>"","Ь"=>"",
			"э"=>"e","Э"=>"E",
			"ю"=>"yu","Ю"=>"Yu",
			"я"=>"ya","Я"=>"Ya",
			"і"=>"i","І"=>"I",
			"ї"=>"yi","Ї"=>"Yi",
			"є"=>"e","Є"=>"E",
			"ё"=>"e","Ё"=>"E"
	);
	
	// лёгкий изврат для того, чтобы рещить частично пробелму с дубликатами символьных кодов
	if ($alternative) {
		$replace = array_merge($replace, array("й"=>"j", "ы"=>"j", "я"=>"ja", "ю"=>"ju", "ы"=>"i", "о"=>"0"));
	}
	
	return $str = strtr($string, $replace);
}

/**
 * Функция траслитерирует строку, заменяя пробелы на символы подчёркивания
 * и преобразуя символы к нижнему регистру
 *
 * @param string $str
 * @return string
 */
function translitStr($str) {
	$params = Array(
			"max_len" => "100", 								// обрезает символьный код до 100 символов
			"change_case" => "L", 							// буквы преобразуются к нижнему регистру
			"replace_space" => "_", 						// меняем пробелы на нижнее подчеркивание
			"replace_other" => "_", 						// меняем левые символы на нижнее подчеркивание
			"delete_repeat_replace" => "true", 	// удаляем повторяющиеся нижние подчеркивания
	);

	return CUtil::translit($str, "ru", $params);
}

