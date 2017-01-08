<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//достаем доступные значения для каждого из типов свойств
$Property = $arParams["PROPERTY"];
$SelectedValues = $arParams["SELECTED_VALUES"];

switch($Property->GetData("PROPERTY_TYPE"))
{
	case "N":
		//для данного типа достанем максимальное и минимальное значения с учетом ранее установленных фильтров
		$property = "PROPERTY_".$Property->GetID();
		
		//ищем модели 
		$arFilter	= array(
			"ACTIVE"		=> "Y",
			"IBLOCK_ID"		=> $arParams["MAIN_IBLOCK"],
		);
		$BitrixFilter	= $arParams["FILTER"]->ObtainFullBitrixFilter();
		if(!empty($BitrixFilter)){
			$arFilter = array_merge($arFilter, $BitrixFilter);
		}

		if($Property->GetData('IBLOCK_ID') != $arParams['MAIN_IBLOCK'])
		{
			if($Property->IsOffer())
			{
				$rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID"));
				while($arItem = $rsItems->Fetch()){
					$arItemsIDs[] = $arItem["ID"];
				}
				$arFilter = array(
					"ACTIVE"									=> "Y",
					"IBLOCK_ID"									=> $Property->GetData("IBLOCK_ID"),
					"PROPERTY_".$arParams["OFFERS_PROPERTY_ID"]	=> $arItemsIDs,
					//"!".$property								=> false,
				);
				$BitrixSubFilter =  $SelectedValues->CreateBitrixFilter();
				if(!empty($BitrixSubFilter["OFFER=>MAIN"])){
					$arFilter = array_merge($arFilter, $BitrixSubFilter["OFFER=>MAIN"]);
				}
			}
			else{
				//TODO
			}
		}
		else{
			//$arFilter["!".$property] = false;
			
			if (!empty($arParams["SECTION_ID"]))
			{
				$arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
				$arFilter["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"];
			}
			
			// Добавил возможность указывать в свойствах фильтра дополнитеные свойства
			if (!empty($arParams["MORE_PROPERTY"]))
			{
				foreach ($arParams["MORE_PROPERTY"] as $key => $value)
				{
					$arFilter[$key] = $value;
				}
			}
		}
	
		$rsElements = CIBlockElement::GetList(array($property => 'DESC'), $arFilter, false, false, array('ID', 'IBLOCK_ID', $property));
		while($arElement = $rsElements->Fetch())
		{
			$Value = $arElement[$property.'_VALUE'];
			if(++$i == 1){
				$Property->SetValue("MAX", ceil($Value));
			}
		}
		$Property->SetValue("MIN", floor($Value));

		if($SelectedValues->IsSelected($Property->GetID())){
			$tmp = $SelectedValues->Get($Property->GetID());
			foreach($tmp["VALUES"] as $valueID=>$valueName){
				$SelectedValues->SetValueName($Property->GetID(), $valueID, $valueID);
			}
		}
	break;
	case "S":
		$property = "PROPERTY_".$Property->GetID();
		$arFilter = array(
			"ACTIVE"		=> "Y",
			"IBLOCK_ID"		=> $Property->GetData("IBLOCK_ID"),
			"!".$property	=> false
		);

		$rsElements = CIBlockElement::GetList(array(), $arFilter, array($property));
		while($arElement = $rsElements->Fetch()){

			$arProperty = array(
				"ID"	=> $arElement[$property."_VALUE"],
				"NAME"	=> $arElement[$property."_VALUE"],
				"COUNT"	=> $arElement["CNT"]
			);

			$Property->SetValue($arProperty["ID"], $arProperty);

			if($SelectedValues->IsSelected($Property->GetID(), $arProperty["ID"])){
				$SelectedValues->SetValueName($Property->GetID(), $arProperty["ID"], $arProperty["NAME"]);
			}
		}

		if($Property->GetParam("SHOWCOUNT") === "Y"){
			$Property->RemoveNotExistValues();
			$Property->SetParam("SHOWCOUNT", "N");
		}

	break;
	case "L":
		$arFilter = array(
			"IBLOCK_ID"	=> $Property->GetData("IBLOCK_ID"),
		);
		$arSelect = array(
			"ID", "IBLOCK_ID", "NAME", "XML_ID", "SORT"
		);

		$rsProperty = CIBlockProperty::GetPropertyEnum($Property->GetID(), array("SORT" => "ASC", "VALUE" => "ASC"), $arFilter, false, false, $arSelect);
		while($arProperty = $rsProperty->GetNext())
		{
			$arProperty["NAME"]		= $arProperty["VALUE"];
			$arProperty["~NAME"]	= $arProperty["~VALUE"];

			$Property->SetValue($arProperty["ID"],$arProperty);
			if($SelectedValues->IsSelected($Property->GetID(), $arProperty["ID"])){
				$SelectedValues->SetValueName($Property->GetID(), $arProperty["ID"], $arProperty["NAME"]);
			}
		}

	break;
	case "E": //привязка к элементам инфоблока
		if($Property->GetData("LINK_IBLOCK_ID") > 0 || $Property->GetParam("LINKIBLOCKID") > 0)
		{
			$arFilter = array(
				"ACTIVE"	=> "Y",
				"IBLOCK_ID"	=> (($Property->GetData("LINK_IBLOCK_ID") > 0)?$Property->GetData("LINK_IBLOCK_ID"):$Property->GetParam("LINKIBLOCKID"))
			);

			if ( $val = $Property->GetParam("SECTIONCODE") ) {
				$arFilter['SECTION_CODE'] = $val;
			}
			
			/*brands*/
			$iblockID = ($Property->GetData("LINK_IBLOCK_ID") > 0)?$Property->GetData("LINK_IBLOCK_ID"):$Property->GetParam("LINKIBLOCKID");
			if($iblockID == BRANDS_IBLOCK_ID){
				$secXmlID = "";
				$arSelectCurSec = Array("XML_ID");
				$arFilterCurSec = Array(
					"IBLOCK_ID"=>SHARE_CATALOG_IBLOCK_ID, 
					"CODE"  => $_REQUEST['SECTION_CODE']
				);
				$resCurSec = CIBlockSection::GetList(Array($by=>$order), $arFilterCurSec, false, $arSelectCurSec);
				 while($ar_resultCurSec = $resCurSec->GetNext())
				{
					$secXmlID = $ar_resultCurSec["XML_ID"];
				}
				//echo $secXmlID;
				if($secXmlID){
					$arSelectCurSec = Array("ID");
					$arFilterCurSec = Array(
						"IBLOCK_ID"=>BRANDS_IBLOCK_ID,
						"XML_ID"  => $secXmlID
					);
					$resCurSec = CIBlockSection::GetList(Array($by=>$order), $arFilterCurSec, false, $arSelectCurSec);
					while($ar_resultCurSec = $resCurSec->GetNext())
					{
						$arFilter['SECTION_ID'] = $ar_resultCurSec['ID'];
					}
				}					
			}
			/*end brands*/
			
			//echo "<pre>"; print_r(($Property->GetData("LINK_IBLOCK_ID") > 0)?$Property->GetData("LINK_IBLOCK_ID"):$Property->GetParam("LINKIBLOCKID")); echo "</pre>";
			$arSelect = array(
				"ACTIVE","ID", "IBLOCK_ID", "SORT", "NAME", "XML_ID", "IBLOCK_SECTION_ID", "CODE", "PREVIEW_PICTURE"
			);

			$rsElements = CIBlockElement::GetList(array("SORT" => "ASC", "NAME" => "ASC"), $arFilter, false, false, $arSelect);

			while($arElement = $rsElements->GetNext())
			{
				//$arElement["PROPERTIES"] = $obElement->GetProperties();
				$arElement["COUNT"] = 0;

				$Property->SetValue($arElement["ID"],$arElement);
				if($SelectedValues->IsSelected($Property->GetID(), $arElement["ID"])){
					$SelectedValues->SetValueName($Property->GetID(), $arElement["ID"], $arElement["NAME"]);
				}
			}
		}
	break;
	case "G": //привязка к секциям
		if($Property->GetData("LINK_IBLOCK_ID") > 0)
		{
			$arFilter = array(
				"ACTIVE"	=> "Y",
				"IBLOCK_ID"	=> $Property->GetData("LINK_IBLOCK_ID")
			);

			$rsSection = CIBlockSection::GetList(array("SORT" => "ASC", "NAME" => "ASC"), $arFilter);
			while($arSection = $rsSection->GetNext())
			{
				$arSection["COUNT"] = 0;

				$Property->SetValue($arSection["ID"], $arSection);
				if($SelectedValues->IsSelected($Property->GetID(), $arSection["ID"])){
					$SelectedValues->SetValueName($Property->GetID(), $arSection["ID"], $arSection["NAME"]);
				}
			}
		}
	break;
}

if(!function_exists("GetRightValue"))
{
	function GetRightValue($arElement,$PropertyID){
		return isset($arElement["PROPERTY_".$PropertyID."_ENUM_ID"])?$arElement["PROPERTY_".$PropertyID."_ENUM_ID"]:$arElement["PROPERTY_".$PropertyID."_VALUE"];
	}
}

//Если нужно ищем количество элементов для каждого свойства

if($Property->GetParam("SHOWCOUNT") === "Y" && $Property->ValuesCount() > 0)
{
	$PropertyID	= $Property->GetID();
	$arFilter	= array(
		"ACTIVE"						=> "Y",
		"IBLOCK_ID"						=> $arParams["MAIN_IBLOCK"],
	);
	
	if (!empty($arParams["SECTION_ID"]))
	{
		$arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
		$arFilter["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"];
	}
	
	// Добавил возможность указывать в свойствах фильтра дополнитеные свойства
	if (!empty($arParams["MORE_PROPERTY"]))
	{
		foreach ($arParams["MORE_PROPERTY"] as $key => $value)
		{
			$arFilter[$key] = $value;
		}
	}
	
	$BitrixFilter= $arParams["FILTER"]->ObtainFullBitrixFilter($PropertyID);
	
	$Prefix = "";
	if($SelectedValues->IsSelected($PropertyID)){
		$Prefix = "+";
	}
	
	//Если свойстов не принадлежит инфоблоку для которого фильтруется товары, тогда возможны 2 варианта:
	if($Property->GetData("IBLOCK_ID") != $arParams["MAIN_IBLOCK"])
	{
		//1. Фильтруемый инфоблок - товары, а свойство из торговых предложений
		if($Property->IsOffer())
		{
			//Выбираем товары с учетом уже выбранных ранее фильтров
			$arItemsIDs = array();				
			$rsItems = CIBlockElement::GetList(array(), array_merge($arFilter, $BitrixFilter), false, false, array("ID"));
			while($arItem = $rsItems->Fetch()){
				$arItemsIDs[] = $arItem["ID"];
			}
			if(!empty($arItemsIDs))
			{
				//Для выбранных товаров выбираем их торговые предложения и группируем их по текущему свойству и товару, к которому они привязаны
				$arFilter = array(
					"ACTIVE"									=> "Y",
					"IBLOCK_ID"									=> $Property->GetData("IBLOCK_ID"),
					"PROPERTY_".$arParams["OFFERS_PROPERTY_ID"]	=> $arItemsIDs,
					"!PROPERTY_".$PropertyID					=> false
				);
				
				$BitrixSubFilter =  $SelectedValues->CreateBitrixFilter($PropertyID, true);
				if(!empty($BitrixSubFilter["OFFER=>MAIN"])){
					$arFilter = array_merge($arFilter, $BitrixSubFilter["OFFER=>MAIN"]);
				}
				
				$arGroup = array(
					"PROPERTY_".$PropertyID,
					"PROPERTY_".$arParams["OFFERS_PROPERTY_ID"]	
				);
				
				$arCounts = $arOffers = array();
				$arFilteredItemsIDs = array();
				$rsOffers = CIBlockElement::GetList(array(), $arFilter, $arGroup);
				while($arOffer = $rsOffers->Fetch())
				{
					$PropertyValue	= GetRightValue($arOffer, $PropertyID);
					$ItemID			= GetRightValue($arOffer, $arParams["OFFERS_PROPERTY_ID"]);
					if($SelectedValues->IsSelected($PropertyID, $PropertyValue)){
						//если свойство выбрано, то
						//исключаем этот товар(коллекцию), чтобы на количество не влияли другие торговые предложения этого товара
						$arFilteredItemsIDs[] = $ItemID;
					}
					$arOffers[$ItemID][] = $PropertyValue;
				}

				//а дальше - 3 цикла. пара-пара-пам!
				if(!empty($arOffers))
				{
					//посчитаем количество товаров(исключая ненужные) для каждого значения свойства
					$arFilteredItemsIDs = array_unique($arFilteredItemsIDs);
					foreach($arOffers as $ItemID => $PropertyValues)
					{
						if(!in_array($ItemID, $arFilteredItemsIDs))
						{
							 foreach($PropertyValues as $PropertyValue){
							 	$arCounts[$PropertyValue]++;
							 }
						}
					}
					//присвоим каждому значению нужного свойства к-во коллекций
					if(!empty($arCounts))
					{
						foreach($arCounts  as $PropertyValue => $ItemsCount){
							$Property->SetValueValue($PropertyValue, "COUNT", (int)$ItemsCount);
							$Property->SetValueValue($PropertyValue, "~COUNT", $Prefix.$ItemsCount);
						}
					}
				}
			}
		}
		//2.Фильтруемых инфоблок - торговые предложения а свойство из инфоблока товаров
		else
		{
			$SubQueryFilter = array_merge($arFilter, $BitrixFilter);
            $arFilter = array(
                "IBLOCK_ID"					=> $Property->GetData['IBLOCK_ID'],
                "ACTIVE"					=> "Y",
                "!PROPERTY_".$PropertyID	=> false,
                "ID"						=> CIBlockElement::SubQuery("PROPERTY_".$arParams["OFFERS_PROPERTY_ID"], $SubQueryFilter)
            );
            $BitrixSubFilter = $SelectedValues->CreateBitrixFilter($PropertyID);
            if(!empty($BitrixSubFilter['MAIN=>OFFER'])){
            	$arFilter = array_merge($arFilter, $BitrixSubFilter['MAIN=>OFFER']);
            }
            $arModelIDs = array();
            $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, array('ID', 'IBLOCK_ID' ,'PROPERTY_'.$PropertyID));
            while($arElement = $rsElements->Fetch()){
            	$PropertyValue = GetRightValue($arElement, $PropertyID);
            	$arModelIDs[] = $arElement['ID'];
            	$arModelsProperty[$arElement['ID']] = $PropertyValue;
            }

            //Получаем товарные предложения для найденных моделей
            if(!empty($arModelIDs)){
	            $arFilter = array(
	            	'ACTIVE'	=> 'Y',
	            	'IBLOCK_ID'	=> $arParams["MAIN_IBLOCK"],
	            	"PROPERTY_".$arParams["OFFERS_PROPERTY_ID"] => $arModelIDs
	            );
	            $BitrixSubFilter = $SelectedValues->CreateBitrixFilter($PropertyID, true);
	            if(!empty($BitrixSubFilter)){
	            	if(!isset($BitrixSubFilter['MAIN=>MAIN'])){
	            		$BitrixSubFilter['MAIN=>MAIN'] = $BitrixSubFilter;
	            	}
	            	if(!empty($BitrixSubFilter['MAIN=>MAIN'])){
	            		$arFilter = array_merge($arFilter,$BitrixSubFilter['MAIN=>MAIN']);
	            	}
	            }
	            $rsElements = CIBlockElement::GetList(array(), $arFilter, array("PROPERTY_".$arParams["OFFERS_PROPERTY_ID"]));
	            while($arElement = $rsElements->Fetch()){
	            	$PropertyValue = GetRightValue($arElement, $arParams["OFFERS_PROPERTY_ID"]);
	            	$arModelOffersCount[$PropertyValue] = $arElement['CNT'];
	            }
	            $arPropertiesCount = array();
	            foreach($arModelsProperty as $ModelID => $PropertyValue){
	            	$arPropertiesCount[$PropertyValue]+=$arModelOffersCount[$ModelID];
	            }

	            foreach($arPropertiesCount as $PropertyValue => $PropertyCount){
	            	$Property->SetValueValue($PropertyValue, "COUNT", (int)$PropertyCount);
	            	$Property->SetValueValue($PropertyValue, "~COUNT", $Prefix.$PropertyCount);
	            }
            }
		}
	}
	else
	{
		$arFilter["!PROPERTY_".$PropertyID]	= false;
		
		$rsElements = CIBlockElement::GetList(array(), array_merge($arFilter, $BitrixFilter), array("PROPERTY_".$PropertyID));
		
		while($arElement = $rsElements->Fetch())
		{
			
			$ValueID = GetRightValue($arElement, $PropertyID);
			if($ValueID){
				$ValueData = $Property->GetValue($ValueID);
				if(isset($ValueData)){
					$Property->SetValueValue($ValueID, "COUNT", (int)$arElement["CNT"]);
					$Property->SetValueValue($ValueID, "~COUNT", $Prefix.$arElement["CNT"]);
				}
			}
		}
	}
	//удаляем значения свойств для которых количество = 0
	$Property->RemoveNotExistValues();
}



//отображение
$tpl_name = $Property->GetParam("TEMPLATE");
$tpl_path = "/template.php";
$result_modifier_path = "/result_modifier.php";

//! путь к шаблону фильтра в папке /bitrix/template/.default
$default_filter_path = $_SERVER['DOCUMENT_ROOT']."/bitrix/templates/.default/components/aniart/custom.filter.oop/filters/.default/";
//! путь к шаблону фильтра в папке текущего шаблона
$current_filter_path = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH."/components/aniart/custom.filter.oop/filters/.default/";
//! путь к папке компонента /bitrix/components/aniart/custom.filter.oop
$component_filter_path = $_SERVER['DOCUMENT_ROOT']."/bitrix/components/aniart/custom.filter.oop/filters/.default/";

//! сперва проверяем наличие папки фильтра в шаблоне компонента в .default
//  затем в папке текущего шаблона сайта и только в последнюю очередь
//  в папке, где располагается сам компонент 
if (file_exists($default_filter_path.$tpl_name)) {
	$filter_path = $default_filter_path;
} elseif (file_exists($current_filter_path.$tpl_name)) {
	$filter_path = $current_filter_path; 
} else {
	$filter_path = $component_filter_path;
}

if (file_exists($filter_path.$tpl_name)) {
	if(file_exists($filter_path.$tpl_name.$tpl_path)) {
		include $filter_path.$tpl_name.$tpl_path;
	} else {
		ShowError("Не найден шаблон \"{$Property->GetParam("TEMPLATE")}\" для фильтра \"{$Property->GetParam("TYPE")}\" в {$filter_path}");		
	}	    
	if(file_exists($filter_path.$tpl_name.$result_modifier_path))
		include $filter_path.$tpl_name.$result_modifier_path;	
}
?>
