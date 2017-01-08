<?php 
	class CSiteMapCustom extends CSiteMap {

		public $arIBLOCK_IDS;

		public $arFiles;

		function Create($site_id, $max_execution_time, $NS, $arOptions = array()) {
			@set_time_limit(0);
	        if(!is_array($NS))
	        {
	            $NS = Array(
	                "ID"=>0,
	                "CNT"=>0,
	                "FILE_SIZE"=>0,
	                "FILE_ID"=>1,
	                "FILE_URL_CNT"=>0,
	                "ERROR_CNT"=>0,
	                "PARAM2"=>0,
	            );
	        }
	        else
	        {
	            $NS = Array(
	                "ID"=>intval($NS["ID"]),
	                "CNT"=>intval($NS["CNT"]),
	                "FILE_SIZE"=>intval($NS["FILE_SIZE"]),
	                "FILE_ID"=>intval($NS["FILE_ID"]),
	                "FILE_URL_CNT"=>intval($NS["FILE_URL_CNT"]),
	                "ERROR_CNT"=>intval($NS["ERROR_CNT"]),
	                "PARAM2"=>intval($NS["ID"]),
	            );
	        }

	        if(is_array($max_execution_time))
	        {
	            $record_limit = $max_execution_time[1];
	            $max_execution_time = $max_execution_time[0];
	        }
	        else
	        {
	            $record_limit = 5000;
	        }

	        if($max_execution_time > 0)
	        {
	            $end_of_execution = time() + $max_execution_time;
	        }
	        else
	        {
	            $end_of_execution = 0;
	        }
	        $bForumTopicsOnly = false;
	     	$bBlogNoComments = false;
	     	$strProto = "http://";
	     	$rsSite=CSite::GetByID($site_id);
	     	if($arSite=$rsSite->Fetch())
	        {
	            $SERVER_NAME = trim($arSite["SERVER_NAME"]);
	            if(strlen($SERVER_NAME) <= 0)
	            {
	                $this->m_error=GetMessage("SEARCH_ERROR_SERVER_NAME", array("#SITE_ID#" => '<a href="site_edit.php?LID='.urlencode($site_id).'&lang='.urlencode(LANGUAGE_ID).'">'.htmlspecialcharsbx($site_id).'</a>'))."<br>";
	                return false;
	            }
	            //Cache events
	            $this->m_events = GetModuleEvents("search", "OnSearchGetURL", true);

	            //Clear error file
	            if($NS["ID"]==0 && $NS["CNT"]==0)
	            {
	                $e=fopen($arSite["ABS_DOC_ROOT"].$arSite["DIR"]."sitemap_errors.xml", "w");
	                $strBegin="<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
	                fwrite($e, $strBegin);
	            }
	            //Or open it for append
	            else
	            {
	                $e=fopen($arSite["ABS_DOC_ROOT"].$arSite["DIR"]."sitemap_errors.xml", "a");
	            }
	            if(!$e)
	            {
	                $this->m_error=GetMessage("SEARCH_ERROR_OPEN_FILE")." ".$arSite["ABS_DOC_ROOT"].$arSite["DIR"]."sitemap_errors.xml"."<br>";
	                return false;
	            }
	            //Open current sitemap file
	            if($NS["FILE_SIZE"]==0)
	            {
	                $f=fopen($arSite["ABS_DOC_ROOT"].$arSite["DIR"]."sitemap_".sprintf("%03d",$NS["FILE_ID"]).".xml", "w");
	                $strBegin="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
	                fwrite($f, $strBegin);
	                $NS["FILE_SIZE"]+=strlen($strBegin);

	            }
	            else
	            {
	                $f=fopen($arSite["ABS_DOC_ROOT"].$arSite["DIR"]."sitemap_".sprintf("%03d",$NS["FILE_ID"]).".xml", "a");
	            }
	            if(!$f)
	            {
	                $this->m_error=GetMessage("SEARCH_ERROR_OPEN_FILE")." ".$arSite["ABS_DOC_ROOT"].$arSite["DIR"]."sitemap_".sprintf("%03d",$NS["FILE_ID"]).".xml"."<br>";
	                return false;
	            }

	            CTimeZone::Disable();

	            $bFileIsFull=false;
	            foreach ($this->arIBLOCK_IDS as $IBLOCK_ID) {
	            	$db_list = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y'), false, array('ID', 'SECTION_PAGE_URL', 'TIMESTAMP_X'));
	            	while (!$bFileIsFull && $ar = $db_list->GetNext()) {
	            		if ($ar['SECTION_PAGE_URL'] && !$this->saveOneUrl($strProto, $arSite, $ar['SECTION_PAGE_URL'], $ar['TIMESTAMP_X'], $e, $f, $NS, $bFileIsFull)) {
			                fclose($e);
			                fclose($f);
	            			return $NS;
	            		}
	            	}

	            	if ($bFileIsFull) {
	            		break;
	            	}

	            	$db_list = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y', 'SECTION_ACTIVE' => 'Y', 'SECTION_GLOBAL_ACTIVE' => 'Y'), false, false, array('DETAIL_PAGE_URL', 'TIMESTAMP_X'));
	            	while (!$bFileIsFull && $ar = $db_list->GetNext()) {
	            		/* Проверим, что секция либо активная, либо их нет */
	            		if ($ar['DETAIL_PAGE_URL'] && !$this->saveOneUrl($strProto, $arSite, $ar['DETAIL_PAGE_URL'], $ar['TIMESTAMP_X'], $e, $f, $NS, $bFileIsFull)) {
 			                fclose($e);
			                fclose($f);
	            			return $NS;
	            		}

	            	}
	            	if ($bFileIsFull) {
	            		break;
	            	}
	            }
	            if (!$bFileIsFull) {
	            	foreach ($this->arFiles as $fileUrl) {
	            		if (!file_exists($_SERVER['DOCUMENT_ROOT']."/".$fileUrl)) {
	            			continue;
	            		}
	            		$time = date('d.m.Y H:m:s', filemtime($_SERVER['DOCUMENT_ROOT']."/".$fileUrl));
	            		if (!$this->saveOneUrl($strProto, $arSite, '/'.$fileUrl, $time, $e, $f, $NS, $bFileIsFull)) {
			                fclose($e);
			                fclose($f);
	            			return $NS;
	            		}

		            	if ($bFileIsFull) {
		            		break;
		            	}
	            	}
	            }

	            CTimeZone::Enable();

	            if($bFileIsFull)
	            {
	                fwrite($e,"</urlset>\n");
	                fclose($e);
	                fwrite($f,"</urlset>\n");
	                fclose($f);

	                $NS["FILE_SIZE"]=0;
	                $NS["FILE_URL_CNT"]=0;
	                $NS["FILE_ID"]++;
	                return $NS;
	            }
	            elseif($record_limit<=0)
	            {
	                return $NS;
	            }
	            else
	            {
	                fwrite($e,"</urlset>\n");
	                fclose($e);
	                fwrite($f,"</urlset>\n");
	                fclose($f);
	            }

		            //WRITE INDEX FILE HERE
	            $f=fopen($arSite["ABS_DOC_ROOT"].$arSite["DIR"]."sitemap_index.xml", "w");
	            if(!$f)
	            {
	                $this->m_error=GetMessage("SEARCH_ERROR_OPEN_FILE")." ".$arSite["ABS_DOC_ROOT"].$arSite["DIR"]."sitemap_index.xml"."<br>";
	                return false;
	            }
             	$strBegin="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<sitemapindex xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
             	fwrite($f, $strBegin);
             	for($i = 0; $i <= $NS["FILE_ID"]; $i++)
	            {
	                $strFile = $arSite["DIR"]."sitemap_".sprintf("%03d",$i).".xml";
	                $strTime = $this->TimeEncode(filemtime($arSite["ABS_DOC_ROOT"].$strFile));
	                fwrite($f,"\t<sitemap>\n\t\t<loc>".$this->URLEncode($strProto.$arSite["SERVER_NAME"].$strFile, "UTF-8")."</loc>\n\t\t<lastmod>".$strTime."</lastmod>\n\t</sitemap>\n");
	            }
	            fwrite($f,"</sitemapindex>\n");
	            fclose($f);
	            $this->m_errors_count=$NS["ERROR_CNT"];
	            $this->m_errors_href=$strProto.$arSite["SERVER_NAME"].$arSite["DIR"]."sitemap_errors.xml";
	            $this->m_href=$strProto.$arSite["SERVER_NAME"].$arSite["DIR"]."sitemap_index.xml";
	            return true;
	   		}
   		 	else
	        {
	            $this->m_error=GetMessage("SEARCH_ERROR_SITE_ID")."<br>";
	            return false;
	        }
        }

        private function saveOneUrl($strProto, $arSite,$url, $time, $e, $f, &$NS, &$bFileIsFull) {
        	if(preg_match("/^[a-z]+:\\/\\//", $url))
        		$strURL = $url;
    		else
        		$strURL = $strProto.$arSite["SERVER_NAME"].$url;
			$strURL = $this->LocationEncode($this->URLEncode($strURL, "UTF-8"));
		 	$strTime = $this->TimeEncode(MakeTimeStamp(ConvertDateTime($time, "DD.MM.YYYY HH:MI:SS"), "DD.MM.YYYY HH:MI:SS"));
		 	$strToWrite="\t<url>\n\t\t<loc>".$strURL."</loc>\n\t\t<lastmod>".$strTime."</lastmod>\n\t</url>\n";
		 	if(strlen($strURL) > 2048)
			{
	            fwrite($e, $strToWrite);
	            $NS["ERROR_CNT"]++;
			}
			else
			{
	            fwrite($f, $strToWrite);
	            $NS["CNT"]++;
	            $NS["FILE_SIZE"]+=strlen($strToWrite);
	            $NS["FILE_URL_CNT"]++;
			}
			//Next File on file size or url count limit
	        if($NS["FILE_SIZE"]>9000000 || $NS["FILE_URL_CNT"]>=50000)
	        {
	            $bFileIsFull=true;
	        }
	        elseif($end_of_execution)
	        {
	            if(time() > $end_of_execution)
	            {
	                CTimeZone::Enable();
	                return false;
	            }
	        }
	        return true;
        }

	}
?>

