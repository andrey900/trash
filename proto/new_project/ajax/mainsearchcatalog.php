<? define("NO_KEEP_STATISTIC", true); // Отключение сбора статистики для AJAX-запросов?>
<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/include/sphinxSearch.php");

$searchphrase = CAniartTools::full_trim($_REQUEST["value"]);
$morfology = new CAniartMorfology();
$searchphrase = $morfology->stem_word($searchphrase);

$ar_elem_id = SphinxSearch($searchphrase);
/*
if ( !empty($searchphrase) ) {
	$index = "idx_ssr_name";

	$cl = new SphinxClient();
	$cl->SetServer( "localhost", 9312 );
	$cl->SetLimits(0, MAX_COUNT_SEARCH);
	$cl->SetMatchMode( SPH_MATCH_ALL  );
	$result = $cl->Query($searchphrase, $index); 	
	
	if ( $result === false ) {
		echo "Query failed: " . $cl->GetLastError() . ".\n";
	}	else {
		if ( $cl->GetLastWarning() ) {
			echo "WARNING: " . $cl->GetLastWarning();
		}

		if ( !empty($result["matches"]) ) {
			$ar_elem_id = array();
			$ar_iblock_id = array();
				
			foreach ($result["matches"] as $key => $value) {
				$ar_elem_id[] = $key;
				if (!in_array($value["attrs"]["iblock_id"],$ar_iblock_id)) {
					$ar_iblock_id[] = $value["attrs"]["iblock_id"];
				}
			}
		}
	}
	$result["m_total_found"] = $result["total_found"];
}
*/
if ( count($ar_elem_id) == 0 ) {
	$arSP = explode(' ', $searchphrase);
	$Sphrases = array();
	foreach ($arSP as $phrase) {
		$Sphrases[] = $morfology->stem_word($phrase);
	}

	unset($searchphrase);unset($result);
	$result = array("m_total_found"=>0);
	$ar_elem_id = array();
	$ar_iblock_id = array();

	foreach ($Sphrases as $searchphrase) {
		$ar_elem_idst = SphinxSearch($searchphrase, 10);
		foreach ($ar_elem_idst as $key => $value) {
			$ar_elem_id[] = $value;
		}
		/*$index = "idx_ssr_name";

		$cl = new SphinxClient();
		$cl->SetServer( "localhost", 9312 );
		$cl->SetLimits(0, MAX_COUNT_SEARCH/10);
		$cl->SetMatchMode( SPH_MATCH_ALL  );
		$result = $cl->Query($searchphrase, $index); 	
		
		if ( $result === false ) {
			echo "Query failed: " . $cl->GetLastError() . ".\n";
		}	else {
			if ( $cl->GetLastWarning() ) {
				echo "WARNING: " . $cl->GetLastWarning();
			}

			if ( !empty($result["matches"]) ) {
					
				foreach ($result["matches"] as $key => $value) {
					$ar_elem_id[] = $key;
					if (!in_array($value["attrs"]["iblock_id"],$ar_iblock_id)) {
						$ar_iblock_id[] = $value["attrs"]["iblock_id"];
					}
				}
			}
		}
		$result["m_total_found"] = $result["total_found"]+$result['m_total_found'];*/
	}//endforeach
}

if ( !empty($ar_elem_id) == 0) {
	$json_out=array("query"=>$_GET['query'],"suggestions"=>array("совпадений не найдено"),
		"data"=>array('совпадений не найдено'));
	echo json_encode($json_out);
	die();
}

$arSelect = Array("ID", "NAME", "CODE", 
				  "IBLOCK_SECTION_ID", 
				  "IBLOCK_TYPE_ID", 
				  "IBLOCK_ID", "IBLOCK_CODE", 
				  "DETAIL_PICTURE", "XML_ID", 
				  "DETAIL_PAGE_URL");
$arFilter = Array(
	"IBLOCK_ID"=>array(
		IBLOCK_CATALOG
	), 
	"ACTIVE"=>"Y", 
	"ID"=>$ar_elem_id
);

$list = CIBlockElement::GetList(Array("SORT"=>"DESC"), $arFilter, false, Array("nPageSize" => MAX_COUNT_SEARCH), $arSelect);

$count_rec = 0;
$list->NavStart(MAX_COUNT_SEARCH);

$ar_results = array();

while($item = $list->GetNext()) {
	if (!empty($item["DETAIL_PICTURE"])) {
		$item["DETAIL_PICTURE"] = CFile::GetPath($item["DETAIL_PICTURE"]);
	}
	$ar_results[$item["IBLOCK_TYPE_ID"]][] = $item;

	$ar_old_result[] = array("label"=>strtolower($item['NAME']),
						     "value"=>$item['NAME'],
						     "image"=>$item['DETAIL_PICTURE'],
						     "url"  =>$item['DETAIL_PAGE_URL']);
}

$json_out = array(
		"query" => $_REQUEST['value'],
		"html"  => $ar_html_result,
		"data"  => $ar_old_result,
		/*"result"=> $ar_results*/
	);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($json_out);

/************* !!!!!!!!!!!!!!! ***************/
die();

/*предыдущая обработка*/
	$word = $_REQUEST['value'];
	$arFilter = Array(
			 'IBLOCK_ID'=>1,
			 "ACTIVE_DATE"=>"Y",
		     "ACTIVE"=>"Y",
		     "SECTION_GLOBAL_ACTIVE"=>"Y",
			 );

	$arSelect = Array('ID','NAME','DETAIL_PAGE_URL');
	CModule::IncludeModule("iblock");
	$rows = array();
	//$word = str_replace(' ','%',$word);

	if(is_numeric($word))
	{

        $arFilter[] =
		array("LOGIC" => "OR",
				 array('ID'=>intval($word)),
				 array('XML_ID'=>intval($word)),
				 array('PROPERTY_CODTOVARA'=>intval($word)),
		);

		$res = CIBlockElement::GetList(Array('NAME' => 'asc' ), $arFilter, false, array('nPageSize'=>20), $arSelect);

		while($ob = $res->GetNext())
	    {
            //echo '<pre>'.print_r($ob,1).'</pre>'.__FILE__.' # '.__LINE__;
		 	$rows[] = $ob;
	    }
	}
	else
	{
	    $findName = false;
	    $arFilter['NAME'] = '%'.$word.'%';

		$res = CIBlockElement::GetList(Array('NAME' => 'asc' ), $arFilter, false, array('nPageSize'=>20), $arSelect);

		while($ob = $res->GetNext())
	    {
	           $rows[] = $ob;
			   $findName = true;
	    }

		if(!$findName)
		{
			$word= str_replace(array(';',','),"",$word);
			$word_orig = $word;

			$search_explode = explode(' ',$word);
			foreach ($search_explode as $k => $v)
			{
				$search_explode[$k] = '%'.trim($v).'%';
				if (strlen($v)<3)
				{
					unset($search_explode[$k]);
				}
			}
			$search_explode_b = $search_explode;
			$search_explode_bcl = $search_explode;
			foreach($search_explode_b as &$value)
				$value = '|'.str_replace('%','',$value).'||' ;
			 unset($value);

			foreach($search_explode_bcl as &$value)
				$value = str_replace('%','',$value);
			 unset($value);


			  $arSelect = Array('ID','NAME','DETAIL_PAGE_URL');
	         $arFilter = Array(
			 'IBLOCK_ID'=>1,
			 array("LOGIC" => "OR",
				 array('NAME'=>$search_explode),
				 //array('ID'=>$search_explode),
				/* array('PREVIEW_TEXT'=>$search_explode),
				 array('DETAIL_TEXT'=>$search_explode),     */
				 /*array('PROPERTY_DOP_TEXT'=>$search_explode),
				 array('PROPERTY_CHAR'=>$search_explode),  */
				 ),

			 );

	         $res = CIBlockElement::GetList(Array('NAME' => 'asc' ), $arFilter, false, array('nPageSize'=>20), $arSelect);
	         while($ob = $res->GetNext())
	         {
			       $rows[] = $ob;
	               /*$rows[] = array(
	               'id'=>$ob['ID'],
	               'barcode'=>$ob['ID'],
	               'categoryid'=>$ob['SECTION_ID'],
				   'name'=> $ob['NAME'],
				   'url'=> $ob['DETAIL_PAGE_URL']
				   );  */
	         }
		 }
	}



    if(count($rows) == 0)
	{
		 $arItog[] = 'совпадений не найдено';
    }
	else
	{
        foreach($rows as $ob)
			$arItog[] = array('label'=>str_ireplace(strtolower($word),'|'.$word.'||',strtolower($ob['NAME'])),'value'=>$ob['NAME'],'url'=>$ob['DETAIL_PAGE_URL']) ;
	}

	$send = array('data'=>$arItog);
	header('Content-Type: application/json; charset=utf-8');
	$JAISON = json_encode($send);
	echo $JAISON;

?>