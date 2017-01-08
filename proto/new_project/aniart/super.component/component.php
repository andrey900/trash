<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// Глобальный объект $vi для доступа к функциям модуля vidi.library
global $vi;

if (isset($arParams["COMPONENT_ENABLE"]) && $arParams["COMPONENT_ENABLE"] === false)
	return;

// Режим разработки под админом
$bDesignMode = $GLOBALS["APPLICATION"]->GetShowIncludeAreas() && is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAdmin();

// Показ только шаблона компонента, без шаблона сайта
$arParams["PURE_TEMPLATE"] = ($arParams["PURE_TEMPLATE"] == "Y")? "Y" : "N";

// RSS
if (!$bDesignMode && $arParams["IS_RSS"] == "Y" || $arParams["PURE_TEMPLATE"] == "Y")
{
	$APPLICATION->RestartBuffer();

	if ($arParams["IS_RSS"] == "Y")
	{
		header("Content-Type: text/xml; charset=".LANG_CHARSET);
		header("Pragma: no-cache");
	}
}

$arNavParams = CDBResult::GetNavParams();

// убираем, так как глючит постраничная навигация
$ADDITIONAL_CACHE_ID[] = $arNavParams["PAGEN"];
$ADDITIONAL_CACHE_ID[] = $arNavParams["SIZEN"];
$ADDITIONAL_CACHE_ID[] = $arNavParams["SHOW_ALL"];

// $ADDITIONAL_CACHE_ID[] = $arNavParams;

if (isset($this->__parent->__name))
{
	// для комплексного компонента
	$component_path = $this->__parent->__relativePath.'/'.$this->__parent->__templateName.'/';
}
else
{
	// для обычного компонента
	$component_path = $this->__relativePath.'/';
}

$CACHE_PATH = "/".SITE_ID."/".LANGUAGE_ID.$component_path.$this->__templateName;


// Учитывать ли группу посетителя
if ($arParams["CACHE_GROUPS"] == "Y")
{
	$ADDITIONAL_CACHE_ID[] = $USER->GetGroups();
}

// Подключается файл result-modifier.php
if($this->StartResultCache($arParams["CACHE_TIME"], $ADDITIONAL_CACHE_ID, $CACHE_PATH))
{
	if($arParams["IS_RSS"] == "Y" && $bDesignMode)
	{
		ob_start();
		$this->IncludeComponentTemplate();
		$contents = ob_get_contents();
		ob_end_clean();

		if ($arParams["PURE_TEMPLATE"] == "Y")
			echo $contents;
		else
			echo "<pre>",htmlspecialchars($contents),"</pre>";
	}
	else
	{
		// Выводим шаблон на экран
		$this->IncludeComponentTemplate();

		// в файле result_modifier.php необходимо разместить такой код:
		// $arResult["__TEMPLATE_FOLDER"] = $this->__folder; // saving template name to cache array
		// $this->__component->arResult = $arResult; // writing new $arResult to cache file
	}
}

// RSS
if (!$bDesignMode && $arParams["IS_RSS"] == "Y" || $arParams["PURE_TEMPLATE"] == "Y")
{
	$r = $APPLICATION->EndBufferContentMan();
	echo $r;
	if(defined("HTML_PAGES_FILE") && !defined("ERROR_404")) CHTMLPagesCache::writeFile(HTML_PAGES_FILE, $r);
	die();
}

// Подключаем резалт_модифаер без кеширования
$modifier_path = $_SERVER["DOCUMENT_ROOT"].$arResult["__TEMPLATE_FOLDER"]."/result_modifier_nc.php";
if (file_exists($modifier_path))
	require_once($modifier_path);


// Подключаем шаблон без кеширования
$nocahe_template_path = $_SERVER["DOCUMENT_ROOT"].$arResult["__TEMPLATE_FOLDER"]."/template_nc.php";
if (file_exists($nocahe_template_path))
	require_once($nocahe_template_path);


// Подключение дополнительных стилей
// для случая компонента, общего для нескольких сайтов
// файлы стилей называются style-SITE_ID.css
// или если передан в параметрах $arParams["CSS"] - то style-$arParams["CSS"]

$style_postfix = ($arParams["CSS"])? trim($arParams["CSS"]) : SITE_ID.".css";
$APPLICATION->SetAdditionalCSS($arResult["__TEMPLATE_FOLDER"]."/"."style-".$style_postfix);

// Подключение дополнительных шаблонов
/*
$template_postfix = ($arParams["TEMPLATE"])? trim($arParams["TEMPLATE"]) : SITE_ID;
$additional_template_path = $_SERVER["DOCUMENT_ROOT"].$arResult["__TEMPLATE_FOLDER"]."/template-".$template_postfix.".php";
if (file_exists($additional_template_path))
	require_once($additional_template_path);
*/

// Возвращаемое значение
if (!empty($arResult["__RETURN_VALUE"]))
	return $arResult["__RETURN_VALUE"];
?>