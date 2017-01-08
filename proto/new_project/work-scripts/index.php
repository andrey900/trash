<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Производство");
?>
<div style="width:600px;">
<?
CModule::IncludeModule("iblock");
$n = 0;
$total = 0;
$ok = 0;
$ids = array();
$arSelect = Array("ID", "NAME", "PROPERTY_DOP_TEXT", "PROPERTY_DOP_TEXT_DESCRIPTION");
$arFilter = Array("IBLOCK_ID"=>1, "!PROPERTY_DOP_TEXT_VALUE"=>false, "<ID"=>20000);
die();
$res = CIBlockElement::GetList(Array("ID"=>"DESC"), $arFilter, false, /*false*/Array("nPageSize"=>1000), $arSelect);
while($ob = $res->GetNextElement())
{
 	 $arFields = $ob->GetFields();
 	 //p($arFields);
 	 $word_html = array();
 	 $prop = array();
 	 foreach($arFields['PROPERTY_DOP_TEXT_VALUE'] as $i =>$propValue){
 	 	$text = "";
 	 	
 	 	$text = $propValue['TEXT'];
 	 	if($text){
	 	 	$type = "text";
	 	 	if(preg_match('/<[a-zA-Z0-9]+.*?>/', $text))
	 	 		$type = "html";
	 	 	
	 	 	if($propValue['TYPE'] == "html" && $type == "text"){
	 	 		$word = preg_split('#([\n\r]+)#Usi',$text);
	 	 		//$word = array_diff($word,array(''));
	 	 	
	 	 		//p($word);
	 	 		$word_f = array();
	 	 		$br = 0;
	 	 		foreach($word as $wordLine){
	 	 			if(strlen(trim($wordLine))<=0)
	 	 				$br++;
	 	 			if($br >=2 && strlen(trim($wordLine))<=0){
	 	 				
	 	 			}else{
	 	 				if(strlen(trim($wordLine))>0)
	 	 					$br = 0;
	 	 				$word_f[] = trim($wordLine);
	 	 			}
	 	 		}
	 	 		//p($word_f);
	 	 		if(count($word_f)>1 && strlen($word_f[0])>0){
	 	 			$word_html[] = array("TEXT" => implode("\n<br/>\n", $word_f)."\n<br/>\n", "DESCRIPTION" => $arFields['PROPERTY_DOP_TEXT_DESCRIPTION'][$i]);
	 	 			$n++;

	 	 			$ids[] = $arFields['ID'];
	 	 		}
	 	 	}
	 	 	$total++;
 	 	}
 	 }
 	 
 	 if(count($word_html)>0){
	 	 $el_id = $arFields['ID'];
	 	 $iblock_id = 1;
	 	 
	 	 foreach($word_html as $htmlBlock){
	 	 	if(!empty($htmlBlock)>0)
	 	 		$prop['DOP_TEXT'][] = array('VALUE'=>array('TYPE'=>'HTML', 'TEXT'=>$htmlBlock['TEXT']), 'DESCRIPTION'=>$htmlBlock['DESCRIPTION']);
	 	 }
	 	 //p($prop);
	 	 if(!empty($prop['DOP_TEXT'])){
	 		CIBlockElement::SetPropertyValuesEx($el_id, $iblock_id, $prop);
	 		
	 	 	$ok++;
	 	 }
 	 }
 	
}
echo $n." из ".$total."<br/>";
echo $ok."<br/>";
//echo "<pre>"; print_r($ids); echo "</pre>";
?> 
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
