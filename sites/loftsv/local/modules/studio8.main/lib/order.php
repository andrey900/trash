<?php

namespace Studio8\Main;

use Bitrix\Main\Application;

class Order
{
	protected $iblock_id = 5;

	protected $props_info = array();

	protected $order;

	public function __construct($id = 0){
		$this->findOrderById($id);
	}

	public function findOrderById($id){
		if( $id <= 0 ) return;
			$this->findInDB(["ID" => (int)$id]);
	}

	public function findByToken($token){
		$this->findInDB(["PROPERTY_TOKEN" => $token]);
	}

	private function findInDB(array $arr){
		$arFilter = array_merge(["IBLOCK_ID" => $this->iblock_id], $arr);
		$this->order = Helpers::_GetInfoElements(false, ["*", "PROPERTY_*"], $arFilter);
		if( $this->order )
			$this->order = current($this->order);
	}

	public function getOrderField($name = ""){
		if( $name && isset($this->order[$name]) )
			return $this->order[$name];
		
		return $this->order;
	}

	public function getOrderProps(){
		if( !$this->props_info ){
			$res = \CIBlock::GetProperties($this->iblock_id, ['SORT'=>"ASC"]);
			while ($arData = $res->Fetch()) {
				$this->props_info[] = $arData;
			}
		}

		return $this->props_info;
	}

	public function propsForOrderForm(){
		$arProps = [];
		foreach ($this->getOrderProps() as $prop) {
			if( strpos($prop['CODE'], "USER_") === false ) continue;
			$arProps[] = $prop;
		}

		return $arProps;
	}

	public function baseTemplateFields(array $arProps){
		if(!$arProps) return;

		$str = "";
		foreach ($arProps as $prop) {
			if( $prop['PROPERTY_TYPE'] == "S" && $prop['ROW_COUNT'] == 1 )
				$str .= sprintf('<input type="text" name="%s" placeholder="%s">', $prop['CODE'], $prop['NAME']);
			elseif( $prop['PROPERTY_TYPE'] == "S" && $prop['ROW_COUNT'] > 1 )
				$str .= sprintf('<textarea class="custom-textarea" name="%s" placeholder="%s"></textarea>', $prop['CODE'], $prop['NAME']);
			elseif( $prop['PROPERTY_TYPE'] =="L" ){
				$property_enums = \CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$this->iblock_id, "PROPERTY_ID"=>$prop['ID']));
				$str .= '<select class="custom-select" name="'.$prop['CODE'].'">';
				while($enum_fields = $property_enums->GetNext())
				{
					$str .= sprintf('<option value="%s">%s</option>', $enum_fields["ID"], $enum_fields["VALUE"]);
				}
                $str .= '</select>';
			}
		}

		return $str;
	}

	public function iblock_id(){
		return $this->iblock_id;
	}

	public function add($propValues){
		$basket = Application::getInstance()->basket;

		$this->el = new \CIBlockElement;

		$propValues['PRICE'] = $basket->getTotalPrice(false);
		$propValues['QUNATITY'] = $basket->getCountGoods();
		$propValues['TOKEN'] = md5($basket->getTotalPrice(false)+time()+$basket->getCountGoods());
		$propValues['PRODUCTS'] = [];
		$propValues['ORDER_PROPERTIES'] = [];
		$i = 0;

		$order_list = "";
		$detailText = "[";
		foreach ($basket->getAllBasket() as $product) {
			$this->makeOrderList($order_list, $product);
			$strName = "Id:" . $product->id . 
				"\r\nНаименование: " . $product->name . 
				"\r\nЦена товара: " . $product->price .
				"\r\nКоличество товара: " . $product->quantity .
				"\r\nСуммарная цена: " . $product->getTotalPrice(false); 
			$propValues['ORDER_PROPERTIES']['n'.$i] = ["VALUE"=>$product->id, "DESCRIPTION"=>$product->quantity];
			$propValues['PRODUCTS']['n'.$i] = $strName;
			$detailText .= (string)$product . ',';
			$i++;
		}
		$detailText = substr($detailText, 0, -1) . "]";

		$arLoadProductArray = Array(
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => $this->iblock_id,
			"PROPERTY_VALUES"=> $propValues,
			"NAME"           => "Новый заказ от - ".date("d-m-Y H:i"),
			"ACTIVE"         => "Y",
			"PREVIEW_TEXT"   => "",
			"DETAIL_TEXT"    => $detailText
		);

		if($order_id = $this->el->Add($arLoadProductArray)){
			$arEventFields = array(
			    "order_id"            => $order_id,
			    "order_list"		  => $order_list,
			    'total_price'		  => $basket->getTotalPrice(),
			    "user_info"			  => $this->makeUserInfo($propValues),
			    "user_email"		  => $propValues['USER_EMAIL']
			);
			\CEvent::Send("NEW_ORDER", SITE_ID, $arEventFields);
			$basket->clear();
			$this->findOrderById($order_id);
			return $order_id;
		}

		return false;
	}

	public function getError(){
		return $this->el->LAST_ERROR;
	}

	public function makeOrderList(&$str, $product){
		$str .= sprintf("<p><b>%s</b>: %s</p>".PHP_EOL, "Наименование", "<a href=\"http://".$_SERVER['SERVER_NAME'].$product->detailPageUrl."\">".$product->name."</a>");
		$str .= sprintf("<p><b>%s</b>: %s</p>".PHP_EOL, "Цена товара", $product->price);
		$str .= sprintf("<p><b>%s</b>: %s</p>".PHP_EOL, "Количество товара", $product->quantity);
		$str .= sprintf("<p><b>%s</b>: %s</p>".PHP_EOL, "Суммарная цена", $product->getTotalPrice());
		$str .= "<p>&nbsp;</p>".PHP_EOL;
	}

	public function makeUserInfo($propValues){
		$str = "";
		foreach ($this->propsForOrderForm() as $prop) {
			if( $prop['PROPERTY_TYPE'] == "L" ){
				$property_enums = \CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$this->iblock_id, "PROPERTY_ID"=>$prop['ID'], "ID" => $propValues[$prop['CODE']]))->Fetch();
				$str .= sprintf("<p><b>%s</b>: %s</p>".PHP_EOL, $prop['NAME'], $property_enums['VALUE']);
				continue;
			}
			$str .= sprintf("<p><b>%s</b>: %s</p>".PHP_EOL, $prop['NAME'], $propValues[$prop['CODE']]);
		}

		return $str;
	}
}