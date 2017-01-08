<? 
define("NO_KEEP_STATISTIC", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

class AniartReview{

	private $objParams;
	private $objReturn;

	function __construct(){
		$this->objParams = $_REQUEST;
		$this->objReturn = new stdClass();
	}
	
	public function VoteReview(){
		
		global $APPLICATION;
		global $USER;
		$userId = $USER->GetID();
		
		CModule::IncludeModule("iblock");
		
		$return = array();
		$reviewData = $this->GetItemData($this->objParams['IBLOCK_ID'], $this->objParams['ID'], array("PROPERTY_".$this->objParams['vote_prop']."_COUNTER", "PROPERTY_".$this->objParams['vote_prop']."_USERS"));
		$return["res"] = "<div class='green'>Спасибо. Ваш голос принят!</div>";
		
		$reviewDataLike = 0;
		if($reviewData["PROPERTY_".$this->objParams['vote_prop']."_COUNTER_VALUE"]>0)
			$reviewDataLike = $reviewData["PROPERTY_".$this->objParams['vote_prop']."_COUNTER_VALUE"];
		
		$updateProps = array();
		
		$likeUsers = array();
		if(is_array($reviewData["PROPERTY_".$this->objParams['vote_prop']."_USERS_VALUE"]))
			$likeUsers = $reviewData["PROPERTY_".$this->objParams['vote_prop']."_USERS_VALUE"];
		
		if($this->objParams['action'] == "plus"){		
			$reviewDataLike = $reviewDataLike+1;
					
			array_push($likeUsers, $userId);	
			
			$updateProps = array($this->objParams['vote_prop']."_COUNTER" => $reviewDataLike, $this->objParams['vote_prop']."_USERS"=>$likeUsers);
		}else{
			if($reviewDataLike>0)
				$reviewDataLike = $reviewDataLike-1;
			
			if(($key = array_search($userId, $likeUsers)) !== false) {
				unset($likeUsers[$key]);
			}
			
			if(count($likeUsers) == 0)
				$likeUsers = false;
			
			$updateProps = array($this->objParams['vote_prop']."_COUNTER" => $reviewDataLike, $this->objParams['vote_prop']."_USERS"=>$likeUsers);
		}
				
		$return["like_counter"] = $reviewDataLike;
		
		CIBlockElement::SetPropertyValuesEx($this->objParams['ID'], false, $updateProps);
		
		$return["ok"] = "<div class='review_ok_message'>Ваш голос принят!</div>";
		
		
		$this->objReturn = $return;
		$this->_return();
	}
	
	public function AddReview(){
			
		global $APPLICATION;
		global $USER;
		$userId = $USER->GetID();
		
		CModule::IncludeModule("iblock");
		
		$status_id = 0;
		$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$this->objParams['IBLOCK_ID'], "CODE"=>"STATUS", "XML_ID" => "XML_REVIEW_STATUS_P"));
		while($enum_fields = $property_enums->GetNext())
		{
			$status_id = $enum_fields["ID"];
		}
		
		$return = array();
		$return["bxajaxid"] = $this->objParams['ajax_id'];
		$return["page"] = $this->objParams['page'];
		$return["parent"] = $this->objParams['parent'];
	
		
		$el = new CIBlockElement;
		$PROP = array();
		$PROP['OBJECT_ID'] = $this->objParams['GOOD'];
		$PROP['USER_ID'] = $userId;
		$PROP['COMMENT_ID'] = $this->objParams['REVIEW_ID'];
		$PROP['STATUS'] = array("VALUE" => $status_id);
		$PROP['OBJECT_PUBLICK_LINK'] = $this->GetItemPublicLink($this->objParams['GOOD']);
		$PROP['CITY'] = $this->objParams['city'];
		
		$arLoadProductArray = Array(
				'IBLOCK_SECTION_ID' => false,
				'IBLOCK_ID' => $this->objParams['IBLOCK_ID'],
				'PROPERTY_VALUES' => $PROP,
				'NAME' => $this->objParams['review_author'],
				'ACTIVE' => 'Y',
				'DATE_ACTIVE_FROM'=>ConvertTimeStamp(time()+CTimeZone::GetOffset(), "FULL"),
				'DETAIL_TEXT' => $this->objParams['review_text'],
		);
		
			
		if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
			$return["res"] = "<div class='review_ok_message'>Спасибо. Ваш отзыв успешно добавлен!</div>";
		} else {
			$return["error"] = "<div class='error'>Произошла ошибка. Попробуйте еще раз</div>"; 
		}
		$this->objReturn = $return;
		$this->_return();
	}
	
	public function DeleteReview(){
		global $APPLICATION;
		global $USER;
		global $DB;
		
		$userId = $USER->GetID();
		
		$return = array();
		$return["bxajaxid"] = $this->objParams['ajax_id'];
		$return["page"] = $this->objParams['page'];
		
		CModule::IncludeModule("iblock");	
		$reviewData = $this->GetItemData($this->objParams['IBLOCK_ID'], $this->objParams['ID'], array("PROPERTY_USER_ID"));
		
		if($reviewData['PROPERTY_USER_ID_VALUE'] == $userId){
			$DB->StartTransaction();	
			if(!CIBlockElement::Delete($this->objParams['ID'])){		
				$return["error"] = "<div class='error'>Произошла ошибка. Попробуйте еще раз</div>"; 		
				$DB->Rollback();
			}else{
				$DB->Commit();
				$return["res"] = "<div class='review_ok_message'>Отзыв успешно удален!</div>";
			}
		}
		
		$this->objReturn = $return;
		$this->_return();
	}
	
	public function GetItemData($IblockId, $ItemId, $arSelectAdd = array(), $arSort = array(), $is_ID_key=false)
	{
		$Result = array();
	
		$arSelect = array('IBLOCK_ID', 'ID', 'NAME');
		$arSelect = array_merge($arSelect, $arSelectAdd);
	
		if(!empty($IblockId) && is_numeric($IblockId)){
			$arFilter = array('IBLOCK_ID' => $IblockId, 'ACTIVE' => 'Y', 'INCLUDE_SUBSECTIONS'=>'Y');
			if(is_numeric($ItemId) || is_array($ItemId)){
				$arFilter['ID'] = $ItemId;
			}else{
				$arFilter['CODE'] = $ItemId;
			}
			if(count($arFilter) > 1){
				$rsItems = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
				while($obItems = $rsItems->GetNextElement())
				{
					$arFields = $obItems->GetFields();
					if(is_array($ItemId)){
						if($is_ID_key == false)
							$Result[] = $arFields;
						else
							$Result[$arFields['ID']] = $arFields;
					}else
						$Result = $arFields;
				}
			}
		}
	
		return $Result;
	}
	
	public function GetItemPublicLink($ItemId){	
		
		global $APPLICATION;
		$link = "http://".$_SERVER['HTTP_HOST'].'/catalog/';
		
		$element_code = false;
		$section_code = false;
		$section_id = false;
		
		$res = CIBlockElement::GetByID($ItemId);
		if($ar_res = $res->GetNext()){
			$section_id = $ar_res['IBLOCK_SECTION_ID'];	
			$element_code = $ar_res['CODE'];	
		}

		if($section_id){
			$res_section = CIBlockSection::GetByID($section_id);
			if($ar_res_section = $res_section->GetNext()){
				$section_code = $ar_res_section['CODE'];
			}
		}
		
		$link .= $section_code.'/'.$element_code.'/';
				
		return $link;	
	}	
	
	private function _return($content){
		echo json_encode($this->objReturn);
	}	
}	

if( !isset($_REQUEST['method']) || !method_exists('AniartReview', $_REQUEST['method']) ){
	die('Error!');
}
	
/*if( !isset($_REQUEST['ajax']) ){
	if( !empty($_SERVER['HTTP_REFERER']))
		header("Location: ".$_SERVER['HTTP_REFERER']);
	else
		header("Location: /");
}*/
	
$aniartReview = new AniartReview;
$aniartReview->$_REQUEST['method']();
?>