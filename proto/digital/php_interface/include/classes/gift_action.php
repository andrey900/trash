<?
/**
 * Класс обрабатывающий акции "Подарки от..." 
 * @author ak
 *
 */
class CCustomGiftAction
{
	var $catalogIBlockID;
	var $offersIBlockID;
	var $giftIBlockID;
	var $arAction; // массив акций, проиндексирвоанный по ID акции (элемента в инфоблоке)
	
	//const DEFAULT_PRICE_GIFT = 10;
	const DEFAULT_PRICE_GIFT = 0;
	const loc_DEFAULT_CITY = "Алматы";
	const NO_PHOTO_100x100 = "/upload/no_photo_100x100.jpg";
	
	/**
	 * Инициализируем объект и формируем список подарков, которые относятся к нему
	 *  
	 * @param integer $catalogIBlockID
	 * @param integer $giftIBlockID
	 * @param array $arGiftID
	 */
	function  __construct($catalogIBlockID, $giftIBlockID, $arGiftID) 
	{
		CModule::IncludeModule("catalog");
		CModule::IncludeModule("iblock");
		
		$this->giftID = $arGiftID;
		
		//global $curCity;
		//$this->curCity = (empty($curCity)? self::loc_DEFAULT_CITY :$curCity);
		
		$this->catalogIBlockID = $catalogIBlockID;
		$this->giftIBlockID = $giftIBlockID;

		$arCatalog = CCatalog::GetByID($this->catalogIBlockID);
		
		$this->offersIBlockID = $arCatalog["OFFERS_IBLOCK_ID"];

		$this->LoadActiveActions();
	}

	/**
	 * Метод формирует HTML-шаблон для иконки акции/подарки в каталоге
	 * 
	 * @param unknown $productID
	 * @param unknown $productPrice
	 * @param unknown $arGiftsID
	 * @return string
	 */
	function GetTemplateForCatalog($productID, $productPrice)
	{
		if (empty($this->giftID))
			$result = '';
		else
		{
			/*24.09*/
			$gift = array();
			foreach ($this->arAction["LINK_GIFT_ACTIONS"] as $period => $arGifts){
				foreach ($arGifts["GIFT_ID"] as $giftID => $giftName){
						
					foreach ($this->arAction["GIFT_ACTIONS"][$giftID]["LIST_GIFT"] as $arProduct){
						if($arProduct["PREVIEW_PICTURE"]){
							$gift = $arProduct;
							break;
						}
					}
				}
			}
			//p($gift);
			/*end 24.09*/
					
			$result = 
				'<div class="gifts-promotions">
					<div class="gifts-promotions-title"> Комплект </div>
					<div class="gifts-promotions-img">
						<a 
							class="fancybox-dialog fancybox.ajax" 
							data-ajax="false"
							href="/catalog/ajax/show_gift_action.php?product_id='.$productID.'&price='.$productPrice.'&id='.base64_encode(serialize($this->giftID)).'" 
							data-fancybox-type="ajax"
						>
							<img 
								src="'.CImageEx::Resize(array(
									"SOURCE" => $gift["PREVIEW_PICTURE"],
									"WIDTH" => 100,
									"HEIGHT" => 100,
								)).'" 
								title="'.$gift["NAME"].'" 
								alt="'.$gift["NAME"].'" 
							/>
						</a>
					</div>
				</div>';
			
			$countActions = count($this->arAction["LINK_GIFT_ACTIONS"]);
			
			if ($countActions > 1)
			{
				$result .= '<div class="gifts-promotions-count"><span>'.$countActions.'</span></div>';
			}									
		}
		
		return $result;
	}
	
	private function LoadActiveActions()
	{
		// Загружаем список активных акций на текущий момент
		$arFilter = array(
				"IBLOCK_ID" => $this->giftIBlockID,
				"ACTIVE" => "Y",
				"ACTIVE_DATE" => "Y",
				"ID" => $this->giftID,
		);
		
		$arSelect = array(
				"ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "DATE_ACTIVE_TO", "DETAIL_TEXT", "PREVIEW_TEXT",
				"PROPERTY_LINK_TO_PRODUCT", "PROPERTY_PRICE_GIFT", "PROPERTY_LOGIC", "PROPERTY_IS_SET",
				"PROPERTY_PRICE_PRODUCT_1_SET", "PROPERTY_PRICE_PRODUCT_2_SET"
		);
		
		$dbList = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		
		while ($dbItem = $dbList->GetNext())
		{
			
			if (empty($dbItem["PROPERTY_LINK_TO_PRODUCT_VALUE"]))  {
				$this->arAction["GIFT_ACTIONS"][$dbItem["ID"]]["MSG"][] = 'В акции "'.$dbItem["NAME"].'" ['.$dbItem["ID"].'] отсутствуют ссылки на товары';
				return;
			}
		
			if (!empty($this->arAction["FULL_NAME"]))
				$this->arAction["FULL_NAME"] .= ", ".$dbItem["NAME"];
			else
				$this->arAction["FULL_NAME"] = $dbItem["NAME"];
				
			$ar_result_1 = $this->GetListGiftProduct($dbItem["PROPERTY_LINK_TO_PRODUCT_VALUE"]);
			$ar_result_2 = $this->GetListGiftProduct($dbItem["PROPERTY_LINK_TO_PRODUCT_VALUE"], true);
		
			$this->arAction["GIFT_ACTIONS"][$dbItem["ID"]] = array(
					"ID" =>	$dbItem["ID"],
					"NAME" =>	$dbItem["NAME"],
					"DATE_ACTIVE_FROM" =>	$this->GetOnlyDate($dbItem["DATE_ACTIVE_FROM"]),
					"DATE_ACTIVE_TO" =>	$this->GetOnlyDate($dbItem["DATE_ACTIVE_TO"]),
					"DETAIL_TEXT" =>	$dbItem["DETAIL_TEXT"],
					"DETAIL_TEXT_TYPE" =>	$dbItem["DETAIL_TEXT_TYPE"],
					"PREVIEW_TEXT" =>	$dbItem["PREVIEW_TEXT"],
					"PREVIEW_TEXT_TYPE" =>	$dbItem["PREVIEW_TEXT_TYPE"],
					"PRICE_GIFT" => empty($dbItem["PROPERTY_PRICE_GIFT_VALUE"])?self::DEFAULT_PRICE_GIFT:$dbItem["PROPERTY_PRICE_GIFT_VALUE"],
					"LINK_TO_GIFT_PRODUCT" =>	$dbItem["PROPERTY_LINK_TO_PRODUCT_VALUE"],
					"LIST_GIFT" => $ar_result_1["RESULT"],
					"LIST_GIFT_FULL" => $ar_result_2["RESULT"],
					"LIST_PRODUCT" => $this->GetListProductInAction($dbItem["ID"]),
					//"CURRENT_CITY" => $this->curCity,
					"LOGIC_AND" => $dbItem["PROPERTY_LOGIC_VALUE"] == "AND"?"Y":"N",
					"IS_SET" => empty($dbItem["PROPERTY_IS_SET_VALUE"])?"N":"Y",
					"PRICE_PRODUCT_1_SET" => $dbItem["PROPERTY_PRICE_PRODUCT_1_SET_VALUE"],
					"PRICE_PRODUCT_2_SET" => $dbItem["PROPERTY_PRICE_PRODUCT_2_SET_VALUE"],
			);
		
			// если список LIST_GIFT пустой, то акцию удаляем
			// ak@, 22.08.2014
			if (empty($this->arAction["GIFT_ACTIONS"][$dbItem["ID"]]["LIST_GIFT"]))
			{ 
				unset($this->arAction["GIFT_ACTIONS"][$dbItem["ID"]]);
			}
			else
			{
				if ($this->arAction["GIFT_ACTIONS"][$dbItem["ID"]]["LOGIC_AND"] == "Y")
					$this->arAction["LOGIC_AND"][] = $dbItem["ID"];
				else
					$this->arAction["LOGIC_OR"][] = $dbItem["ID"];
				
				// если надо вставим сообщения диагностики
				if ( $ar_result_1["FAILS"] || !count($ar_result_1["RESULT"])) {
					$this->arAction["GIFT_ACTIONS"][$dbItem["ID"]]['MSG'][] = sprintf(
							!count($this->arAction["GIFT_ACTIONS"][$dbItem["ID"]]["LIST_GIFT"]) ?
							'Подарки есть, но у них проблема с остатками  или ценой (всего подарков в данном городе:%d с ошибками:%d)  | (всего остатки в сети:%d)' :
							'Для некоторых товаров проблема с ценой (всего подарков:%d с ошибками:%d)  | (всего остатки в сети:%d)', $ar_result_1["ALL"], $ar_result_1["FAILS"], $ar_result_2["ALL"]);
				}
			}
		}

		// если всё равно нет изображений для акции, тогда указывам no_photo
		if (empty($this->arAction["PREVIEW_PICTURE"])) $this->arAction["PREVIEW_PICTURE"] = self::NO_PHOTO_100x100;
		
		// проверяем, чтобы кол-во акций И не превышало 1
		if (count($this->arAction["LOGIC_AND"]) > 1)
		{
			reset($this->arAction["LOGIC_AND"]);
			$this->arAction["LOGIC_AND"][] = current($this->arAction["LOGIC_AND"]);
			$this->arAction["GIFT_ACTIONS"][$dbItem["ID"]]['MSG'][] = "Внимание!!! Присутствует более двух активных акций с логикой 'И'";
		}
		
		// Формируем пары
		if (empty($this->arAction["LOGIC_AND"]))
		{
			foreach ($this->arAction["LOGIC_OR"] as $giftID)
				$this->arAction["LINK_GIFT_ACTIONS"][$giftID]["GIFT_ID"][$giftID] = $this->arAction["GIFT_ACTIONS"][$giftID]["NAME"];
		}
		else
		{
			$giftAndID = current($this->arAction["LOGIC_AND"]);
			foreach ($this->arAction["LOGIC_OR"] as $giftID)
			{
				$this->arAction["LINK_GIFT_ACTIONS"][$giftAndID.$giftID]["GIFT_ID"][$giftAndID] = $this->arAction["GIFT_ACTIONS"][$giftAndID]["NAME"];
				$this->arAction["LINK_GIFT_ACTIONS"][$giftAndID.$giftID]["GIFT_ID"][$giftID] = $this->arAction["GIFT_ACTIONS"][$giftID]["NAME"];
			}
		}
		
		// Вычисляем дату окончания комплексной акции
		foreach ($this->arAction["LINK_GIFT_ACTIONS"] as $period => $arGifts)
		{
			foreach ($arGifts["GIFT_ID"] as $giftID => $nameGift)
			{
				$dateTo = MakeTimeStamp($this->arAction["GIFT_ACTIONS"][$giftID]["DATE_ACTIVE_TO"], "DD.MM.YYYY HH:MI:SS");
				if (empty($this->arAction["LINK_GIFT_ACTIONS"][$period]["DATE_TO"]))
					$this->arAction["LINK_GIFT_ACTIONS"][$period]["DATE_TO"] = $dateTo;
				elseif($this->arAction["LINK_GIFT_ACTIONS"][$period]["DATE_TO"] > $dateTo)
				$this->arAction["LINK_GIFT_ACTIONS"][$period]["DATE_TO"] = $dateTo;
			}
		}
		
		// Обходим массив акций и, если элемент пустой, то устанавливаем сообщение об ошибке
		foreach ($arGiftID as $giftID)
		{
			if (empty($this->arAction["GIFT_ACTIONS"][$giftID])) {
				$this->arAction["GIFT_ACTIONS"][$dbItem["ID"]]['MSG'][] = 'Акция уже не активна, в ней нет товаров либо удалена';
			}
		}
	}
	
	function GetInfo()
	{
		return $this->arAction;
	}

	/**
	 * Получаем список товаров, принимающих участие в акции
	 * 
	 * @param unknown $actionID
	 * @return multitype:NULL
	 */
	private function GetListProductInAction($actionID)
	{
		$result = array();
		$arFilter = array(
				"IBLOCK_ID" => $this->catalogIBlockID,
				"PROPERTY_GIFTS" => $actionID,
				"ACTIVE" => "Y",
		);
	
		$arSelect = array(
				"ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "CATALOG_GROUP_".PRICE_BASE_ID
		);
	
		$dbList = CIBlockElement::GetList(array("NAME" => "ASC", "SORT" => "ASC"), $arFilter, false, false, $arSelect);
			
		while ($dbItem = $dbList->GetNext())
		{
			$dbItem["PREVIEW_PICTURE"] = (empty($dbItem["PREVIEW_PICTURE"])?self::NO_PHOTO_100x100:CFile::GetPath($dbItem["PREVIEW_PICTURE"]));
			$result[$dbItem["ID"]] = RemoveDuplicateKeyFromDBArray($dbItem);
		}
	
		return $result;
	}
	
	/**
	 * Получаем список товаров, являющихся подарками в акции. Для полного списка формируем дополнительные 
	 * условия с логикой ИЛИ:
	 *  	ACTIVE => Y
	 *  	PROPERTY_IS_GIFT => false
	 *  OR
	 *  	!PROPERTY_IS_GIFT => false
	 * 
	 * @param unknown $arLinkID
	 * @param string $fullList
	 * @return multitype:number Ambigous <multitype:multitype: , unknown>
	 */
	private function GetListGiftProduct($arLinkID, $fullList = false)
	{
		$result = array();
		$arFilter = array(
			"IBLOCK_ID" => $this->catalogIBlockID,
			"ID" => $arLinkID,
		);
		
		// см. описание к методу
		if (!$fullList) 
		{
			$arFilter[] = array(
				"LOGIC" => "OR",
				array(
					"ACTIVE" => "Y", 
					"PROPERTY_IS_GIFT" => false, 
					"PROPERTY_GOROD_VALUE" => $this->curCity
				),
				array(
					"!PROPERTY_IS_GIFT" => false,
				),
			); 
		}
		
		$arSelect = array(
			"ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "PROPERTY_IS_GIFT", "CATALOG_GROUP_".PRICE_BASE_ID
		);
		
		$i=0;
		$dbList = CIBlockElement::GetList(array("NAME" => "ASC", "SORT" => "ASC"), $arFilter, false, false, $arSelect);
			
		for ($j=0; $dbItem = $dbList->GetNext(); $j++)
		{
			if(empty($dbItem["PREVIEW_PICTURE"]))
			{
				$picture = self::NO_PHOTO_100x100;
			}
			else
			{
				$picture = CFile::GetPath($dbItem["PREVIEW_PICTURE"]);
				//p("<img src='".$picture."' />");
				if (empty($this->arAction["PREVIEW_PICTURE"])) $this->arAction["PREVIEW_PICTURE"] = $picture;
			}
			 
			$name = $dbItem["NAME"];
			$detailPageURL = $dbItem["DETAIL_PAGE_URL"];
			//$arCity = $dbItem["PROPERTY_GOROD_VALUE"];
			$price = $dbItem["CATALOG_PRICE_".PRICE_BASE_ID];
			
			// извлекаем торговое предложение для выбранного города
			$offersInfo = $this->GetOffersInfo($dbItem["ID"], $fullList);

			// ОТКЛЮЧЕНО! Использовалось в том случае, если етсь торогове прдложение, переписано для товара
			// if (empty($offersInfo) && !empty($dbItem["PROPERTY_IS_GIFT_VALUE"])) $offersInfo = $dbItem;
			
			$offersInfo = $dbItem;
				
			if (!empty($offersInfo))
			{
				$result[$dbItem["ID"]] = $offersInfo;
				// финт ушами -- используем фото, название, URL из товара
				$result[$dbItem["ID"]]["NAME"] = $name;
				$result[$dbItem["ID"]]["PREVIEW_PICTURE"] = $picture;
				$result[$dbItem["ID"]]["DETAIL_PAGE_URL"] = $detailPageURL;
				//$result[$dbItem["ID"]]["LIST_CITY"] = $arCity;
				//$result[$dbItem["ID"]]["PRESENT_IN_CITY"] = (in_array($this->curCity, $result[$dbItem["ID"]]["LIST_CITY"])?"Y":"N");
				$result[$dbItem["ID"]]["PRICE"] = $price;

				//if ($result[$dbItem["ID"]]["PRESENT_IN_CITY"]=='N') {
				//	$i++; // значит нет остатков в городе
				//}
			} else {
				$i++; // значит нет остатков или проблема с ценой
			}
			
		}		

		// сортируем записи в $result согласно расположению ID в $arLinkID
		$arSortedResult = array();
		$this->arAction["PREVIEW_PICTURE"] = ""; // "финт ушами", чтобы не создавать конфликт в коде при объединении веток :)
		foreach ($arLinkID as $productID)
		{
			if (empty($result[$productID])) continue;
				
			if (empty($this->arAction["PREVIEW_PICTURE"])) $this->arAction["PREVIEW_PICTURE"] = $result[$productID]["PREVIEW_PICTURE"];
				
			$arSortedResult[$productID] = $result[$productID];
		}
		$result = $arSortedResult;
		
		return Array( 'RESULT'=>$result, "FAILS"=>$i, "ALL"=>$j);
	}
		
	private function GetOffersInfo($productID, $fullList = false)
	{
		$result = array();
		$arFilter = array(
				"IBLOCK_ID" => $this->offersIBlockID,
				"ACTIVE" => "Y",
				"PROPERTY_CML2_LINK" => $productID,
				">CATALOG_QUANTITY" => 0 
		);
		
		if (!$fullList)
			$arFilter["PROPERTY_GOROD_VALUE"] = $this->curCity;
		
		$arSelect = array(
				"ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"
		);
		
		$dbList = CIBlockElement::GetList(array("NAME" => "ASC", "SORT" => "ASC"), $arFilter, false, false, $arSelect);
			
		if ($dbItem = $dbList->GetNext())
		{
				
			$result = RemoveDuplicateKeyFromDBArray($dbItem);
		}
		
		return $result;
	}
	
	private function GetOnlyDate($dateStr) { return date("d.m.Y", strtotime($dateStr));	}
}
?>
