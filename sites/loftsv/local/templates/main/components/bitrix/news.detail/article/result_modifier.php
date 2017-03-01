<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/*TAGS*/
if ($arParams["SEARCH_PAGE"])
{
	if ($arResult["FIELDS"] && isset($arResult["FIELDS"]["TAGS"]))
	{
		$tags = array();
		foreach (explode(",", $arResult["FIELDS"]["TAGS"]) as $tag)
		{
			$tag = trim($tag, " \t\n\r");
			if ($tag)
			{
				$url = CHTTP::urlAddParams(
					$arParams["SEARCH_PAGE"],
					array(
						"tags" => $tag,
					),
					array(
						"encode" => true,
					)
				);
				$tags[] = '<a href="'.$url.'">'.$tag.'</a>';
			}
		}
		$arResult["FIELDS"]["TAGS"] = implode(", ", $tags);
	}
}

$cp = $this->__component; // объект компонента

if (is_object($cp))
{
    // добавим в arResult компонента all
    $cp->SetResultCacheKeys(array_keys($cp->arResult));
}