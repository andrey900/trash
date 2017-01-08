<?
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("CIBlockPropertyElementListMain", "GetIBlockPropertyDescription"));

class CIBlockPropertyElementListMain
{
	public function GetIBlockPropertyDescription()
	{
		return array(
			"PROPERTY_TYPE" => "N",
			"USER_TYPE" => "GiftsSets",
			"DESCRIPTION" => "Подарки, комплекты",
			"GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
			"GetPropertyFieldHtmlMulty" => array(__CLASS__,'GetPropertyFieldHtmlMulty'),
			"GetAdminListViewHTML" => array(__CLASS__,"GetAdminListViewHTML"),
			"GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
			//"GetAdminFilterHTML" => array(__CLASS__,'GetAdminFilterHTML'),
			"GetSettingsHTML" => array(__CLASS__,'GetSettingsHTML'),
			"PrepareSettings" => array(__CLASS__,'PrepareSettings'),
			"AddFilterFields" => array(__CLASS__,'AddFilterFields'),
		);
	}
	
	function PrepareSettings($arProperty)
	{
		//$arProperty['LINK_IBLOCK_ID'] = CATALOG_IBLOCK_ID;
		
		// инфоблок, с элементами которого будет выполняться связь
		$iIBlockId = intval($arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID']);
		return array(
			'IBLOCK_ID' => $iIBlockId > 0 ? $iIBlockId : 0,
			'LINK_IBLOCK_ID' => $iIBlockId > 0 ? $iIBlockId : 0
		);
		//return $arProperty;
	}

	function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		$result = '';
		
		if(!CModule::IncludeModule('iblock')) {
			return $result;
		}

		// текущие значения настроек 
		if($arPropertyFields) {
			$value = $strHTMLControlName['NAME']['IBLOCK_ID'];
		} elseif(is_array($arProperty)) {
			$value = $arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID'];
		} else {
			$value = '';
		}
		$result .= '
		<tr style="vertical-align: top;">
			<td>Информационный блок по умолчанию:</td>
			<td>
				'.GetIBlockDropDownList($value, $strHTMLControlName['NAME'].'[IBLOCK_TYPE_ID]', $strHTMLControlName['NAME'].'[IBLOCK_ID]').'
			</td>
		</tr>
		';
		return $result;
		
	}

	protected function GetLinkElement($ElementID,$iblockID)
	{
		static $cache = array();
		
		$iblockID = intval($iblockID);
		if (0 >= $iblockID)
			$iblockID = 0;
		$ElementID = intval($ElementID);
		if (0 >= $ElementID)
			return false;
		if (!isset($cache[$ElementID]))
		{
			$arFilter = array();
			if (0 < $iblockID)
				$arFilter['IBLOCK_ID'] = $iblockID;
			$arFilter['ID'] = $ElementID;
			
			$arSelect = array(
					"ID",
					"NAME",
					"IN_SECTIONS",
					"IBLOCK_SECTION_ID",
			);
			$arOrder = array(
					"NAME" => "ASC",
					"ID" => "ASC",
			);
			
			$rsItems = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
			if($arItem = $rsItems->GetNext()){
				$result = array(
						'ID' => $arItem['ID'],
						'NAME' => $arItem['NAME'],
						'~NAME' => $arItem['~NAME'],
						'IBLOCK_ID' => $arItem['IBLOCK_ID'],
				);
				$cache[$ElementID] = $result;
			}
			else
			{
				$cache[$ElementID] = false;
			}
		}
		return $cache[$ElementID];
	}
	
	protected function GetPropertyValue($arProperty,$arValue)
	{
		
		$settings = CIBlockPropertyElementListMain::PrepareSettings($arProperty);

		$mxResult = false;
	
		if (0 < intval($arValue['VALUE']))
		{
			$mxResult = self::GetLinkElement($arValue['VALUE'],$settings['LINK_IBLOCK_ID']);
			if (is_array($mxResult))
			{
				$mxResult['PROPERTY_ID'] = $arProperty['ID'];
				if (isset($arProperty['PROPERTY_VALUE_ID']))
				{
					$mxResult['PROPERTY_VALUE_ID'] = $arProperty['PROPERTY_VALUE_ID'];
				}
				else
				{
					$mxResult['PROPERTY_VALUE_ID'] = false;
				}
			}
		}
		return $mxResult;
	}
	
	//PARAMETERS:
	//$arProperty - b_iblock_property.*
	//$value - array("VALUE","DESCRIPTION") -- here comes HTML form value
	//strHTMLControlName - array("VALUE","DESCRIPTION")
	//return:
	//safe html
	public function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName)
	{
		global $APPLICATION;
		$settings = CIBlockPropertyElementListMain::PrepareSettings($arProperty);
		
		$strResult = '';
		
		$mxElement = self::GetPropertyValue($arProperty,$arValue);
		if (!is_array($mxElement))
		{
			
			$id_prop = str_replace(":", "_", $strHTMLControlName['VALUE']);
			
			$strResult = '<table><tr>';
			$strResult .= '<td>';
			$strResult .= '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="'.$id_prop.'" value="'.$arValue['VALUE'].'" size="5">'.
					'<input type="button" value="..." onClick="jsUtils.OpenWindow(\'iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.intval($settings["LINK_IBLOCK_ID"]).'&amp;n='.urlencode($id_prop).'\', 900, 600);">'.
					'&nbsp;<span id="sp_'.$id_prop.'" ></span>';
			$strResult .= '</td></tr>';
			if (($arProperty["WITH_DESCRIPTION"]=="Y") && ('' != trim($strHTMLControlName["DESCRIPTION"]))):
				$strResult .= '<tr><td>';
				$strResult .= '<span><input type="text" name="'.$strHTMLControlName["DESCRIPTION"].'" value="'.$arValue["DESCRIPTION"].'" size="50"> - Используется для установки цен</span>';
				$strResult .= '</td></tr>';
			endif;
			$strResult .= '</table>';
		}
		else
		{
				
			$id_prop = str_replace(":", "_", $strHTMLControlName['VALUE']);
			
			$strResult = '<table><tr>';
			$strResult .= '<td>';
			$strResult .= '<input type="text" name="'.$strHTMLControlName["VALUE"].'" id="'.$id_prop.'" value="'.$arValue['VALUE'].'" size="5" />'.
					//'<input type="button" value="..." onClick="jsUtils.OpenWindow(\'/ajax/uf_iblock_section_search.php?lang='.LANGUAGE_ID.'&IBLOCK_ID='.$settings["LINK_IBLOCK_ID"].'&n=&k=&lookup=jsMLI_'.$control_id.'\', 900, 600);">'.
					'<input type="button" value="..." onClick="jsUtils.OpenWindow(\'iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.intval($settings["LINK_IBLOCK_ID"]).'&amp;n='.urlencode($id_prop).'\', 900, 600);">'.
					'&nbsp;<span id="sp_'.$id_prop.'" >'.$mxElement['NAME'].'</span>';
			$strResult .= '</td></tr>';
			if (($arProperty["WITH_DESCRIPTION"]=="Y") && ('' != trim($strHTMLControlName["DESCRIPTION"]))):
				$strResult .= '<tr><td>';
				$strResult .= '<span><input type="text" name="'.$strHTMLControlName["DESCRIPTION"].'" value="'.$arValue["DESCRIPTION"].'" size="50"> - Используется для установки цен</span>';
				$strResult .= '</td></tr>';
			endif;
			$strResult .= '</table>';
		}
		return $strResult;
	}

	public function GetPropertyFieldHtmlMulty($arProperty, $arValues, $strHTMLControlName)
	{
		
		global $APPLICATION;
				
		$settings = CIBlockPropertyElementListMain::PrepareSettings($arProperty);
		
		$strResult = '';
		
		$arResult = false;
		$n=0;
			
			foreach ($arValues as $intPropertyValueID => $arOneValue)
			{
				$mxElement = self::GetPropertyValue($arProperty,$arOneValue);
				if (is_array($mxElement))
				{
					$arResult[] = '<table><tr><td>
							<input type="text" 
								name="'.$strHTMLControlName["VALUE"].'['.$intPropertyValueID.'][VALUE]" 
								id="'.$strHTMLControlName["VALUE"].'['.$intPropertyValueID.']" 
								value="'.$arOneValue['VALUE'].'" size="15">
							<input type="button" value="..." 
								onClick="jsUtils.OpenWindow(\'iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$settings["LINK_IBLOCK_ID"].'&amp;n='.urlencode($strHTMLControlName["VALUE"].'['.$intPropertyValueID.']').'\', 900, 600);"> 
						&nbsp;<span id="sp_'.$strHTMLControlName["VALUE"].'['.$intPropertyValueID.']" >'.$mxElement['NAME'].'</span>
						</td></tr>
						<tr><td>
							<input 
								name="'.$strHTMLControlName["VALUE"].'['.$intPropertyValueID.'][DESCRIPTION]" 
								value="'.htmlspecialcharsEx($arOneValue["DESCRIPTION"]).'" 
								size="15" type="text"> - цена
						</td></tr></table>';
				}
			}

			if (0 < intval($arProperty['MULTIPLE_CNT']))
			{
				for ($i = 0; $i < $arProperty['MULTIPLE_CNT']; $i++)
				{
					$arResult[] = '<table><tr><td>
							<input type="text" 
								name="'.$strHTMLControlName["VALUE"].'[n'.$i.'][VALUE]" 
								id="'.$strHTMLControlName["VALUE"].'[n'.$i.']" 
								value="" size="15">
							<input type="button" value="..." 
								onClick="jsUtils.OpenWindow(\'iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$settings["LINK_IBLOCK_ID"].'&amp;n='.urlencode($strHTMLControlName["VALUE"].'[n'.$i.']').'\', 900, 600);">
							&nbsp;<span id="sp_'.$strHTMLControlName["VALUE"].'[n'.$i.']" ></span>
						</td></tr>
						<tr><td>
							<input name="'.$strHTMLControlName["VALUE"].'[n'.$i.'][DESCRIPTION]" value="" size="15" type="text"> - цена
						</td></tr></table>';
				}
			}
			$arResult[] = '<input class="addMultyRowGiftSet" type="button" onclick="addMultyRowGiftSet('.$i.')" value="Добавить">';
			
			$arResult[] = "
				<script>
				function addMultyRowGiftSet(i){
					var n = i;
					var k = n+1;
					var link_s = fixedEncodeURIComponent('iblock_element_search.php?lang=".LANGUAGE_ID."&amp;IBLOCK_ID=".$settings["LINK_IBLOCK_ID"]."&amp;n=".$strHTMLControlName["VALUE"]."[n'+n+']');
					var str = '<table><tr><td><input type=\"text\" name=\"".$strHTMLControlName["VALUE"]."[n'+n+'][VALUE]\" id=\"".$strHTMLControlName["VALUE"]."[n'+n+']\" value=\"\" size=\"15\">&nbsp;<input type=\"button\" value=\"...\" onClick=\"jsUtils.OpenWindow(\''+link_s+'\', 900, 600);\">&nbsp;<span id=\"sp_".$strHTMLControlName["VALUE"]."[n'+n+']\" ></span></td></tr><tr><td><input name=\"".$strHTMLControlName["VALUE"]."[n'+n+'][DESCRIPTION]\" value=\"\" size=\"15\" type=\"text\"> - цена</td></tr></table>';
					$( '.addMultyRowGiftSet' ).before( str );
					$( '.addMultyRowGiftSet' ).attr( 'onclick', 'addMultyRowGiftSet('+k+')' );
				}	
				function fixedEncodeURIComponent (str) {
				  return str.replace(/[]/g, function(c) {
				    return '%' + c.charCodeAt(0).toString(16);
				  });
				}
			</script>";

			$strResult = implode('<br />',$arResult);
			$strResult = implode('',$arResult);
		return  $strResult;
	}

	public function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName)
	{
		static $cache = array();
	
		$arSettings = self::PrepareSettings($arProperty);
	
		$strResult = '';
		$arValue['VALUE'] = intval($arValue['VALUE']);
		if (0 < $arValue['VALUE'])
		{
			if (!isset($cache[$arValue['VALUE']]))
			{
				$arFilter = array();
				$intIBlockID = intval($arSettings['LINK_IBLOCK_ID']);
				if (0 < $intIBlockID) $arFilter['IBLOCK_ID'] = $intIBlockID;
				$arFilter['ID'] = $arValue['VALUE'];
				$arFilter["ACTIVE"] = "Y";
				$arFilter["ACTIVE_DATE"] = "Y";
				$arFilter["CHECK_PERMISSIONS"] = "Y";
				$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID","IBLOCK_ID","NAME","DETAIL_PAGE_URL"));
				$cache[$arValue['VALUE']] = $rsElements->GetNext(true,false);
			}
			if (is_array($cache[$arValue['VALUE']]))
			{
				if (isset($strHTMLControlName['MODE']) && 'CSV_EXPORT' == $strHTMLControlName['MODE'])
				{
					$strResult = $cache[$arValue['VALUE']]['ID'];
				}
				elseif (isset($strHTMLControlName['MODE']) && ('SIMPLE_TEXT' == $strHTMLControlName['MODE'] || 'ELEMENT_TEMPLATE' == $strHTMLControlName['MODE']))
				{
					$strResult = $cache[$arValue['VALUE']]["NAME"];
				}
				else
				{
					$strResult = '<a href="'.$cache[$arValue['VALUE']]["DETAIL_PAGE_URL"].'">'.htmlspecialcharsEx($cache[$arValue['VALUE']]["NAME"]).'</a>';;
				}
			}
		}
		return $strResult;
	}
	
	/*function GetAdminFilterHTML($arProperty, $strHTMLControlName)
	{
		$lAdmin = new CAdminList($strHTMLControlName["TABLE_ID"]);
		$lAdmin->InitFilter(array($strHTMLControlName["VALUE"]));
		$filterValue = $GLOBALS[$strHTMLControlName["VALUE"]];

		if(isset($filterValue) && is_array($filterValue))
			$values = $filterValue;
		else
			$values = array();

		$settings = CIBlockPropertyElementListMain::PrepareSettings($arProperty);
		if($settings["size"] > 1)
			$size = ' size="'.$settings["size"].'"';
		else
			$size = '';

		if($settings["width"] > 0)
			$width = ' style="width:'.$settings["width"].'px"';
		else
			$width = '';

		$bWasSelect = false;
		$options = CIBlockPropertyElementListMain::GetOptionsHtml($arProperty, $values, $bWasSelect);

		$html = '<select multiple name="'.$strHTMLControlName["VALUE"].'[]"'.$size.$width.'>';
		$html .= '<option value=""'.(!$bWasSelect? ' selected': '').'>'.GetMessage("IBLOCK_PROP_ELEMENT_LIST_ANY_VALUE").'</option>';
		$html .= $options;
		
		return  $html;
	}*/

	/*function GetOptionsHtml($arProperty, $values, &$bWasSelect)
	{
		$options = "";
		$settings = CIBlockPropertyElementListMain::PrepareSettings($arProperty);
		$bWasSelect = false;

		if($settings["group"] === "Y")
		{
			$arElements = CIBlockPropertyElementListMain::GetSections($arProperty["LINK_IBLOCK_ID"]);
			$arTree = CIBlockPropertyElementListMain::GetSections($arProperty["LINK_IBLOCK_ID"]);
			foreach($arElements as $i => $arElement)
			{
				if(
					$arElement["IN_SECTIONS"] == "Y"
					&& array_key_exists($arElement["IBLOCK_SECTION_ID"], $arTree)
				)
				{
					$arTree[$arElement["IBLOCK_SECTION_ID"]]["E"][] = $arElement;
					unset($arElements[$i]);
				}
			}

			foreach($arTree as $arSection)
			{
				$options .= '<optgroup label="'.str_repeat(" . ", $arSection["DEPTH_LEVEL"]-1).$arSection["NAME"].'">';
				if(isset($arSection["E"]))
				{
					foreach($arSection["E"] as $arItem)
					{
						$options .= '<option value="'.$arItem["ID"].'"';
						if(in_array($arItem["~ID"], $values))
						{
							$options .= ' selected';
							$bWasSelect = true;
						}
						$options .= '>'.$arItem["NAME"].'</option>';
					}
				}
				$options .= '</optgroup>';
			}
			foreach($arElements as $arItem)
			{
				$options .= '<option value="'.$arItem["ID"].'"';
				if(in_array($arItem["~ID"], $values))
				{
					$options .= ' selected';
					$bWasSelect = true;
				}
				$options .= '>'.$arItem["NAME"].'</option>';
			}

		}
		else
		{
			foreach(CIBlockPropertyElementListMain::GetSections($arProperty["LINK_IBLOCK_ID"]) as $arItem)
			{
				$options .= '<option value="'.$arItem["ID"].'"';
				if(in_array($arItem["~ID"], $values))
				{
					$options .= ' selected';
					$bWasSelect = true;
				}
				$options .= '>'.$arItem["NAME"].'</option>';
			}
		}

		return  $options;
	}*/

	public function GetAdminListViewHTML($arProperty, $arValue, $strHTMLControlName)
	{
		$strResult = '';
		$mxResult = self::GetPropertyValue($arProperty,$arValue);
		if (is_array($mxResult))
		{
			$strResult = $mxResult['NAME'].' [<a href="/bitrix/admin/'.
					CIBlock::GetAdminElementEditLink(
							$mxResult['IBLOCK_ID'],
							$mxResult['ID'],
							array(
									'WF' => 'Y'
							)
					).'" title="">'.$mxResult['ID'].'</a>]';
		}
		return $strResult;
	}
	
	function GetSections($IBLOCK_ID)
	{
		static $cache = array();
		$IBLOCK_ID = intval($IBLOCK_ID);

		if(!array_key_exists($IBLOCK_ID, $cache))
		{
			$cache[$IBLOCK_ID] = array();
			if($IBLOCK_ID > 0)
			{
				$arSelect = array(
					"ID",
					"NAME",
					"DEPTH_LEVEL",
				);
				$arFilter = array (
					"IBLOCK_ID"=> $IBLOCK_ID,
					//"ACTIVE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
				);
				$arOrder = array(
					"LEFT_MARGIN" => "ASC",
				);
				$rsItems = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
				while($arItem = $rsItems->GetNext())
					$cache[$IBLOCK_ID][$arItem["ID"]] = $arItem;
			}
		}
		return $cache[$IBLOCK_ID];
	}
}
?>
