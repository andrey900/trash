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
$Update = array();
$arSelect = Array("ID", "NAME", "DETAIL_TEXT");
$arFilter = Array("IBLOCK_ID"=>1, "!DETAIL_TEXT"=>false, "<ID"=>2501);
die();
$res = CIBlockElement::GetList(Array("ID"=>"ASC"), $arFilter, false, Array("nPageSize"=>1000), $arSelect);
while($ob = $res->GetNextElement())
{
 	$arFields = $ob->GetFields();
 	$word_html = array(); 
 	$text = "";
	$text = $arFields['DETAIL_TEXT'];
	 
	$type = "text";
	if(preg_match('/<[a-zA-Z0-9]+.*?>/', $text))
	 	$type = "html";
	
 	if($arFields['DETAIL_TEXT_TYPE'] == "html" && $type == "text"){
 		$word = preg_split('#([\n\r]+)#Usi',$text);
		//$word = array_diff($word,array(''));
		
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
			$word_html = implode("\n<br/>\n", $word_f)."\n<br/>\n";
			
			$n++;
			$Update[$arFields['ID']] = $word_html;
		}
 	}
 	$total++;
 	
}
//p($Update);
foreach($Update as $iOne => $one){
	if(strlen($one)>0){
		$el = new CIBlockElement;
		$arLoadProductArray = Array(
				"DETAIL_TEXT" =>  $one,
				"DETAIL_TEXT_TYPE" =>  'html'
		);
		$PRODUCT_ID = $iOne;  // изменяем элемент с кодом (ID) 2
		if($el->Update($PRODUCT_ID, $arLoadProductArray)){
			$ok++;	
			$ids[] = $PRODUCT_ID;
		}
	}
}


echo $n." из ".$total."<br/>";
echo "Обновлено ".$ok."<br/>";
echo "<pre>"; print_r($ids); echo "</pre>";
?> 
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
