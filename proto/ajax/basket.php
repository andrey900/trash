<?php
define("NO_KEEP_STATISTIC", true); // отключим статистику
require ($_SERVER ["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');
//error_reporting(E_ALL);

//print_r($_REQUEST);//die();
//p(json_decode($_REQUEST['params']));

class Basket{
	
	private $objParams;
	private $objReturn;
	
	function __construct(){
		$this->objParams = json_decode($_REQUEST['params']);
		 $this->objReturn = new stdClass();
	}
	
	public function update(){
		try {
			if((int)$this->objParams->quantity == 0)
				$this->delete()->_return();
				//throw new Exception("ZERO");
			elseif((int)$this->objParams->quantity < 1){
				throw new Exception("Minus");
			} else {
				$arFields = array("QUANTITY" => $this->objParams->quantity);
				CSaleBasket::Update($this->objParams->elemId, $arFields);
				
				$this->objReturn->id	  = $this->objParams->elemId;
				$this->objReturn->content = $this->_getBasket($this->objParams->elemId);
				$this->_return();
				//p($this->objReturn);
			}
		} catch (Exception $e) {
			$this->objReturn->error = 'Возникла ошибка: '.  $e->getMessage(). "\n";
			$this->_return();
		}
	}
	
	public function delete(){
		if (CSaleBasket::Delete($this->objParams->elemId)){
			$this->objReturn->content = "<div>Запись успешно удалена</div>";
			$this->objReturn->remove = true;}
		else 
			$this->objReturn->error = "При удалении возникли ошибки";
		
		return $this;
	}
	
	public function add(){
		Add2BasketByProductID((int)$this->objParams->elemId, (int)$this->objParams->quantity);
		return $this;
	}
	
	public function allUpdate(){
		$arFields = array("QUANTITY" => $this->objParams->quantity);
		CSaleBasket::Update($this->objParams->elemId, $arFields);
		$this->objReturn->content = $this->_getBasket(0, 'basket');
		$this->objReturn->smallBasket = $this->_getBasket(0, 'small');
		$this->objReturn->smallBasketCount = $_SESSION['countBasketElement'];
		$this->_return();
	}
	
	private function _getBasket($elemId, $template=''){
		if( empty($template) )
			$template = 'ajax';
		
		global $APPLICATION;
		ob_start();
		
		$APPLICATION->IncludeComponent(
				"bitrix:sale.basket.basket",
				$template,
				Array(
						'AJAX'    => 'Y',
						'ITEM_ID' => (int)$elemId,
				)
		);
		
		// Получаем содержимое буфера в переменную:
		$content = ob_get_contents();
		// Чистим бфер
		ob_end_clean();
		
		return $content;
	}
	
	private function _return($content){
		echo json_encode($this->objReturn);
	}
}

if( !isset($_REQUEST['method']) || !method_exists('Basket', $_REQUEST['method']) ){
	die('Error!');
}

if( !isset($_REQUEST['ajax']) ){
	if( !empty($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']);
	else
		header("Location: /");
}

$basket = new Basket;
$basket->$_REQUEST['method']();
?>