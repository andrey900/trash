<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class CBitrixAniartReviewsComponent extends CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		return $arParams;
	}
	/**
	 * Возвращает массив описывающий элемент инфоблока
	 */
	public function GetReviewIblockItemData($arParams, $parentItemId, $lMargin, $arSelectAdd = array(), $arSort)
	{		
		$Result = array();
	
		$arSelect = array('ID');
		$arSelect = array_merge($arSelect, $arSelectAdd);
	
		if(!empty($arParams['IBLOCK_ID']) && is_numeric($arParams['IBLOCK_ID'])){
	
			$O_status_id = $this->GetStatusIdByXML("XML_REVIEW_STATUS_O");
			$S_status_id = $this->GetStatusIdByXML("XML_REVIEW_STATUS_S");
			
			$arFilter = array(
				'IBLOCK_ID' => $arParams['IBLOCK_ID'], 
				'ACTIVE' => 'Y', 
				'INCLUDE_SUBSECTIONS'=>'Y',
				array(
					"LOGIC" => "OR",
					array("PROPERTY_STATUS" => $O_status_id),
					array("PROPERTY_USER_ID" => $arParams['USER_ID'], "!PROPERTY_STATUS" => $S_status_id),
				),
			);
			$arFilter['PROPERTY_COMMENT_ID'] = $parentItemId;
	
			if(count($arFilter) > 1){
				$rsItems = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
				while($obItems = $rsItems->GetNextElement())
				{
					$arFields = $obItems->GetFields();
					$arFields['LEFT_MARGIN'] = $lMargin;
	
					if(strlen($arFields["ACTIVE_FROM"])>0)
						$arFields["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arFields["ACTIVE_FROM"], CSite::GetDateFormat()));
					else
						$arFields["DISPLAY_ACTIVE_FROM"] = "";
	
					$arFields["PROPERTIES"] = $obItems->GetProperties();
						
					$Result[] = $arFields;
				}
			}
		}
	
		return $Result;
	}
	
	public function GetSubElems($item, $lMargin, $arSelectAdd, $arSort){
	
		$returnArray = array();
	
		if($item["ID"]){
			$subItems = $this->GetReviewIblockItemData($this->arParams, $item["ID"], $lMargin, $arSelectAdd, $arSort);
		}
	
		foreach($subItems as $i =>$val){
			if(is_array($val)){
				$return = $val;
				$sub = array();
				$sub = $this->GetSubElems($val, $lMargin+$this->arParams['DEFAULT_LEFT_MARGIN'], $arSelectAdd, $arSort);
	
				if(count($sub)>0)
					$return['SUB_ITEMS'] = $sub;
	
				$returnArray[$val['ID']] = $return;
			}
		}
		return $returnArray;
	}
	
	public function GetCountSubElems($items){
	
		$return = 0;
		$return = count($items);
			
		foreach($items as $i =>$val){
	
			if(is_array($val["SUB_ITEMS"])){
					
				$return_sub = $this->GetCountSubElems($val["SUB_ITEMS"]);
				$return = $return+$return_sub;
			}
		}
		return $return;
	}
	
	public function GetStatusIdByXML($xml){
		
		$status_id = 0;
		$property_enums = CIBlockPropertyEnum::GetList(
				Array("DEF"=>"DESC", "SORT"=>"ASC"), 
				Array(
					"IBLOCK_ID"=>$this->arParams['IBLOCK_ID'], 
					"CODE"=>"STATUS", 
					"XML_ID" => $xml
				)
			);
		while($enum_fields = $property_enums->GetNext())
		{
			$status_id = $enum_fields["ID"];
		}
		
		return $status_id;
	}
}