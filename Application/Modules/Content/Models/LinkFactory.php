<?php

class Content_Models_LinkFactory extends Library_Adminbase
{
	
	public $allLanguageMutations;
	public $langModule;
	
	public function addSections($id){
		
		$isSections = $this->_request->getPost("is-section");
		if(!empty($isSections)){
			
			$script      = new Library_Scripts();
			$linkSection = new Content_Models_LinkSection();
			
			foreach($isSections as $isSec){
				
				$color = $this->_request->getPost("sec-bg-color-".$isSec);
				$wide  = $this->_request->getPost("wide-".$isSec);
				
				//nastavime vsechny jazykove verze
				foreach($this->allLanguageMutations as $val){
					$this->secName["name-".$val->suffix] = $this->_request->getPost("sec-name-".$isSec."-".$val->suffix);
					$this->secName["niceName-".$val->suffix] = $script->url($this->secName["name-".$val->suffix]);
				}
				
				$linkSection->insertData(array(
					"linkID"   => $id,
					"dateAdd"  => date("Y-m-d H:i:s"),
					"color"    => $color,
					"wide"     => $wide,
					"name"     => $this->secName["name-cz"],
					"niceName" => $this->secName["niceName-cz"]
				));
				$lastID = $linkSection->lastID;
				
				$elements = $this->_request->getPost("is-element-".$isSec);
				
				$this->updateDictionary("add","link-section","linkSectionID",$lastID);
				
				if(!empty($elements)){
					foreach($elements as $priority => $el){
						
						$el = explode('-',$el);
											
						if($el[0] == "h")$this->insertHeaderElementData($isSec,$lastID,$priority,$el[1]);
						if($el[0] == "sh")$this->insertSectionHeaderElementData($isSec,$lastID,$priority,$el[1]);
						if($el[0] == "t")$this->insertTextElementData($isSec,$lastID,$priority,$el[1]);
						if($el[0] == "l")$this->insertLinkElementData($isSec,$lastID,$priority,$el[1],$id);
						if($el[0] == "f")$this->insertFormElementData($isSec,$lastID,$priority,$el[1]);
						if($el[0] == "ytv")$this->insertYtvElementData($isSec,$lastID,$priority,$el[1]);
						if($el[0] == "a")$this->insertArticleElementData($isSec,$lastID,$priority,$el[1]);
						if($el[0] == "pf")$this->insertPhotoFileElementData($isSec,$lastID,$priority,$el[1],$id);
						if($el[0] == "m")$this->insertMapElementData($isSec,$lastID,$priority,$el[1],$id);
						
					}
				}
								
			}
			
		}
		
	}
	
	public function getSections($id){
				
		$this->updateSectionData = array();
		
		$linkTranslates = $this->getLinkSectionsTranslates($id);
		
		$linkSection = new Content_Models_LinkSection();	
		$this->getHeaderElements($linkSection,$id,$linkTranslates);
		$this->getSectionHeaderElements($linkSection,$id,$linkTranslates);
		$this->getTextElements($linkSection,$id,$linkTranslates);
		$this->getLinkElements($linkSection,$id,$linkTranslates);
		$this->getFormElements($linkSection,$id,$linkTranslates);
		$this->getYtvElements($linkSection,$id,$linkTranslates);
		$this->getArticleElements($linkSection,$id,$linkTranslates);
		$this->getPhotoFileElements($linkSection,$id,$linkTranslates);
		$this->getMapElements($linkSection,$id,$linkTranslates);
		
		ksort($this->updateSectionData);
		
		foreach($this->updateSectionData as $key => $elms){
			ksort($elms["elements"]);			
			$this->updateSectionData[$key] = $elms;		    			
		}
		
		$this->view->updateSectionData = $this->updateSectionData;
	}
	public function getLinkSectionsTranslates($linkID){
		
		$linkLangDb 					= new Content_Models_LinkLang();
		$linkSectionLangDb 				= new Content_Models_LinkSectionLang();
		$linkSectionArticleLangDb 		= new Content_Models_LinkSectionArticleLang();
		$linkSectionFormLangDb 			= new Content_Models_LinkSectionFormLang();
		$linkSectionFormValuesLangDb 	= new Content_Models_LinkSectionFormValuesLang();
		$linkSectionHeaderLangDb 		= new Content_Models_LinkSectionHeaderLang();
		$linkSectionHeaderSectionLangDb	= new Content_Models_LinkSectionHeaderSectionLang();
		$linkSectionLinkLangDb 			= new Content_Models_LinkSectionLinkLang();
		$linkSectionMapLangDb 			= new Content_Models_LinkSectionMapLang();
		$linkSectionTextLangDb 			= new Content_Models_LinkSectionTextLang();
	
		$linkSectionDb 	= new Content_Models_LinkSection();
		$allSections 	= $linkSectionDb->getAllItems("linkID = $linkID", "linkSectionID");
		
		$allSectionsArr = array();
		foreach($allSections as $val){
			$allSectionsArr[] = $val->linkSectionID;
		}
		
		$allTranslates = array();
		if(!empty($allSectionsArr)){
			$allSectionsIDsIN = implode(",", $allSectionsArr);


			$linkSectionLanguages 				= $linkSectionLangDb->getAllItems("linkSectionID IN ($allSectionsIDsIN)","linkSectionID");
			foreach ($linkSectionLanguages as $val){
				$allTranslates[$val->linkSectionID]['name'][$val->lang] 		= $val->name;
				$allTranslates[$val->linkSectionID]['niceName'][$val->lang] 	= $val->niceName;
			}
			
			$linkSectionArticleLanguages 		= $linkSectionArticleLangDb->getLanguagesWithSection("LS.linkSectionID IN ($allSectionsIDsIN)");
			foreach ($linkSectionArticleLanguages as $val){
				$allTranslates[$val->linkSectionID]["elements"]["article"][$val->linkSectionArticleID]["url"][$val->lang] 		= $val->url;
			}
			$linkSectionFormLanguages 			= $linkSectionFormLangDb->getLanguagesWithSection("LS.linkSectionID IN ($allSectionsIDsIN)");
			foreach ($linkSectionFormLanguages as $val){
				$allTranslates[$val->linkSectionID]["elements"]["form"][$val->linkSectionFormID]["title"][$val->lang] 		= $val->title;
				$allTranslates[$val->linkSectionID]["elements"]["form"][$val->linkSectionFormID]["niceTitle"][$val->lang] 	= $val->niceTitle;
			}
			$linkSectionFormValuesLanguages 	= $linkSectionFormValuesLangDb->getLanguagesWithSection("LS.linkSectionID IN ($allSectionsIDsIN)");
			foreach ($linkSectionFormValuesLanguages as $val){
				$allTranslates[$val->linkSectionID]["elements"]["formValue"][$val->linkSectionFormValueID]["title"][$val->lang] 	= $val->title;
				$allTranslates[$val->linkSectionID]["elements"]["formValue"][$val->linkSectionFormValueID]["value"][$val->lang] 	= $val->value;
			}
			$linkSectionHeaderLanguages 		= $linkSectionHeaderLangDb->getLanguagesWithSection("LS.linkSectionID IN ($allSectionsIDsIN)");
			foreach ($linkSectionHeaderLanguages as $val){
				$allTranslates[$val->linkSectionID]["elements"]["header"][$val->linkSectionHeaderID]["titleH1"][$val->lang] 	= $val->titleH1;
				$allTranslates[$val->linkSectionID]["elements"]["header"][$val->linkSectionHeaderID]["titleH2"][$val->lang] 	= $val->titleH2;
			}
			$linkSectionHeaderSectionLanguages 	= $linkSectionHeaderSectionLangDb->getLanguagesWithSection("LS.linkSectionID IN ($allSectionsIDsIN)");
			foreach ($linkSectionHeaderSectionLanguages as $val){
				$allTranslates[$val->linkSectionID]["elements"]["headerSection"][$val->linkSectionHeaderSectionID]["title"][$val->lang] 	= $val->title;
				$allTranslates[$val->linkSectionID]["elements"]["headerSection"][$val->linkSectionHeaderSectionID]["titleH2"][$val->lang] 	= $val->titleH2;
			}
			$linkSectionLinkLanguages 			= $linkSectionLinkLangDb->getLanguagesWithSection("LS.linkSectionID IN ($allSectionsIDsIN)");
			foreach ($linkSectionLinkLanguages as $val){
				$allTranslates[$val->linkSectionID]["elements"]["link"][$val->linkSectionLinkID]["titleH2"][$val->lang] = $val->titleH2;
				$allTranslates[$val->linkSectionID]["elements"]["link"][$val->linkSectionLinkID]["url"][$val->lang] 	= $val->url;
				$allTranslates[$val->linkSectionID]["elements"]["link"][$val->linkSectionLinkID]["text"][$val->lang] 	= $val->text;
			}
			$linkSectionMapLanguages 			= $linkSectionMapLangDb->getLanguagesWithSection("LS.linkSectionID IN ($allSectionsIDsIN)");
			foreach ($linkSectionMapLanguages as $val){
				$allTranslates[$val->linkSectionID]["elements"]["map"][$val->linkSectionMapID]["title"][$val->lang] 		= $val->title;
			}
			$linkSectionTextLanguages 			= $linkSectionTextLangDb->getLanguagesWithSection("LS.linkSectionID IN ($allSectionsIDsIN)");
			foreach ($linkSectionTextLanguages as $val){
				$allTranslates[$val->linkSectionID]["elements"]["text"][$val->linkSectionTextID]["text"][$val->lang] 		= $val->text;
			}
			
			
			
		}
		return $allTranslates;
		//$linkLangDb->getCompleteLinkLanguages("linkID = $linkID");
	}
	public function insertHeaderElementData($isSec,$lastID,$priority,$elementNumber){
		
		$linkSectionHeader = new Content_Models_LinkSectionHeader();
		$upload            = new Library_UploadFiles();
		$path              = "Public/Images/Link/Section/Header";
		$upload->path      = $path;
		$upload->ownName   = true;
		$upload->smallHeight = 0;
		$upload->smallWidth  = 1920;
		$this->heData = array();
		
		//nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$this->heData["h1-".$val->suffix] = $this->_request->getPost("h-e-h1-".$isSec."-".$elementNumber."-".$val->suffix);
			$this->heData["h2-".$val->suffix] = $this->_request->getPost("h-e-h2-".$isSec."-".$elementNumber."-".$val->suffix);
		}
		
		$hiddenImages = $this->_request->getPost("h-e-f-".$isSec."-".$elementNumber);
		$deleteImages = $this->_request->getPost("h-e-f-d-".$isSec."-".$elementNumber);
		if(empty($deleteImages))$deleteImages = array();
							
		$fileName = "";
		
		foreach($this->heData["h1-cz"] as $key => $elmData){
			
			if(!empty($hiddenImages[$key])){
				$fileName = $hiddenImages[$key];
				if(in_array(($key+1),$deleteImages)){
					$fileName = "";
					unlink("./".$path."/".$hiddenImages[$key]);
					unlink("./".$path."/mala-".$hiddenImages[$key]);
				}
			}
			if(!empty($_FILES["h-e-file-".$isSec."-".$elementNumber]['tmp_name'][$key])){
				
				if(!empty($hiddenImages[$key]) && !in_array(($key+1),$deleteImages)){
					unlink("./".$path."/".$hiddenImages[$key]);
					unlink("./".$path."/mala-".$hiddenImages[$key]);
				}
				
				$upload->fileName    = $upload->niceFile($_FILES["h-e-file-".$isSec."-".$elementNumber]['name'][$key]);
				$upload->tmpFileName = $_FILES["h-e-file-".$isSec."-".$elementNumber]['tmp_name'][$key];
				$upload->upload();
				
				$fileName = $upload->fileName;
			}
			
			if(!empty($fileName) || !empty($this->heData["h1-cz"][$key]) || !empty($this->heData["h2-cz"][$key])){
				$linkSectionHeader->insertData(array(
					"linkSectionID"    => $lastID,
				    "groupSectionLink" => $elementNumber,
					"titleH1"          => $this->heData["h1-cz"][$key],
					"titleH2"          => $this->heData["h2-cz"][$key],
					"image"            => $fileName,
					"priority"         => $priority,
					"dateAdd"          => date("Y-m-d H:i:s"),
					"userAdd"          => ""
				));
				
				$lID = $linkSectionHeader->lastID;
				
				if($this->langModule)$this->updateDictionary("add","link-section-header","linkSectionHeaderID",$lID,null,$key);
			}
		
		}
			
	}
	
	public function insertMapElementData($isSec,$lastID,$priority,$elementNumber){
	
		$mapSectionHeader = new Content_Models_LinkSectionMap();
	
		$this->mData = array();
	
		//nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$this->mData["title-".$val->suffix] = $this->_request->getPost("m-e-title-".$isSec."-".$elementNumber."-".$val->suffix);
		}
	
		$this->mData["lat"]           = $this->_request->getPost("m-e-lat-".$isSec."-".$elementNumber);
		$this->mData["long"]          = $this->_request->getPost("m-e-long-".$isSec."-".$elementNumber);
		$this->mData["elementWidth"]  = $this->_request->getPost("m-e-element-width-".$isSec."-".$elementNumber);
		
		foreach($this->mData["title-cz"] as $key => $elmData){
				
			if(!empty($this->mData["lat"][$key]) && !empty($this->mData["long"][$key])){
				$mapSectionHeader->insertData(array(
						"linkSectionID"    => $lastID,
						"groupSectionLink" => $elementNumber,
						"title"            => $this->mData["title-cz"][$key],
						"lat"              => $this->mData["lat"][$key],
						"long"             => $this->mData["long"][$key],
				        "elementWidth"     => $this->mData["elementWidth"],
						"priority"         => $priority,
						"dateAdd"          => date("Y-m-d H:i:s"),
						"userAdd"          => ""
				));
	
				$lID = $mapSectionHeader->lastID;
				if($this->langModule)$this->updateDictionary("add","link-section-map","linkSectionMapID",$lID,null,$key);
			}
	
		}
			
	}
	
	public function insertArticleElementData($isSec,$lastID,$priority,$elementNumber){
	
		$linkSectionArticle = new Content_Models_LinkSectionArticle();
		
		$this->aeData = array();
	
		//nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$this->aeData["url-".$val->suffix] = $this->_request->getPost("a-e-url-".$isSec."-".$elementNumber."-".$val->suffix);
		}

		$type      = $this->_request->getPost("a-e-type-".$isSec."-".$elementNumber);
		$pageCount = $this->_request->getPost("a-e-pageCount-".$isSec."-".$elementNumber);
		$newCount  = $this->_request->getPost("a-e-newCount-".$isSec."-".$elementNumber);
		$linkID    = $this->_request->getPost("a-e-lID-".$isSec."-".$elementNumber);

		$linkSectionArticle->insertData(array(
			"linkSectionID" => $lastID,
			"type"          => $type,
			"pageCount"     => $pageCount,
			"newCount"      => $newCount,
			"url"           => $this->aeData["url-cz"],
			"linkID"        => $linkID,
			"priority"      => $priority,
			"dateAdd"       => date("Y-m-d H:i:s"),
			"userAdd"       => ""
		));
	
		$lID = $linkSectionArticle->lastID;
	
		if($this->langModule)$this->updateDictionary("add","link-section-article","linkSectionArticleID",$lID);
			
	}
	
	public function insertPhotoFileElementData($isSec,$lastID,$priority,$elementNumber,$linkID){
	
		$files        = $_FILES["ph-file-".$isSec."-".$elementNumber];
		$elementWidth = $this->_request->getPost("ph-file-element-width-".$isSec."-".$elementNumber);
		$isSlider     = $this->_request->getPost("ph-file-isSlider-".$isSec."-".$elementNumber);
		$showDetail   = $this->_request->getPost("ph-file-showDetail-".$isSec."-".$elementNumber);
		
		$linkSectionFile = new Content_Models_LinkSectionFile();
		$photo           = new Models_Photo();
		$fileTable       = new Models_File();

		
		if(count($files["name"]) > 0){
			
			$uploadPhoto = new Library_UploadFiles();
			$uploadPhoto->path         = "Public/Images/Link/Section/File";
			$uploadPhoto->smallHeight    = 116;
			$uploadPhoto->smallWidth     = 0;
			$uploadPhoto->middleHeight = 800;
			$uploadPhoto->middleWidth  = 0;
			$uploadPhoto->ownName      = true;
			
			$uploadFiles          = new Library_UploadGraphics();
			$uploadFiles->path    = "Public/Files/Link/Section/File";
			$uploadFiles->ownName = true;
			
			$photoPriority = 1;
			$filePriority  = 1;
			
			$error = "";
			
			$pfIDs = $this->_request->getPost("pf-IDs-".$isSec."-".$elementNumber);
			if(!empty($pfIDs)){
				$pfIDs = explode("-",$pfIDs);
				$lID   = $pfIDs[0];
				$pr = $pfIDs[1];
					
				$linkSectionFile->updateData(array(
					"linkSectionID" => $lastID,
					"priority"      => $priority,
					"elementWidth"  => $elementWidth,
					"isSlider"      => $isSlider,
					"showDetail"    => $showDetail
				),"linkSectionID = '$lID' AND priority = '$pr'");
			
			}
			
			
			
			foreach($files["name"] as $key => $file){
				
				if(!empty($file)){
				
					$photoID  = 0;
					$fileID   = 0;
					$filename = "";
					$er       = false;
					
					if($files['type'][$key] == 'image/jpeg' || $files['type'][$key] == "image/pjpeg" || $files['type'][$key] == "image/png" || $files['type'][$key] == "image/gif" || $files['type'][$key] == "image/bmp"){
						
						$uploadPhoto->fileName    = $uploadPhoto->niceFile($files['name'][$key]);						
						$uploadPhoto->tmpFileName = $files['tmp_name'][$key];
						$uploadPhoto->upload();
						
						if($uploadPhoto->uploaded){		
	
							list($w, $h) = getimagesize("./".$uploadPhoto->path."/".$uploadPhoto->fileName);
							
							$photo->insertData(array(
								"title"       => $uploadPhoto->fileName,
								"description" => "",
								"mainPhoto"   => 0,
								"width"       => $w,
								"height"      => $h,
								"priority"    => $photoPriority
							));
							$photoID = $photo->lastID;
							$photoPriority++;	
						}else{
							$error .= $uploadPhoto->error." <br />";
							$er = true;
						}				
						
					}else{
						
						$uploadFiles->fileName    = $uploadFiles->niceFile($files['name'][$key]);
						$uploadFiles->tmpFileName = $files['tmp_name'][$key];
						$uploadFiles->mimeType    = $files['type'][$key];
						$uploadFiles->upload();
															
						if($uploadFiles->uploaded){
							
							$ico = $this->getIcons($uploadFiles->fileName);

							$size = filesize("./".$uploadFiles->path."/".$uploadFiles->fileName);
							
							$fileTable->insertData(array(
								"title"       => $uploadFiles->fileName,
								"description" => "",
								"size"        => $size,
								"ico"         => $ico,
								"priority"    => $photoPriority
							));
							$fileID = $fileTable->lastID;
						}else{
							$error .= $uploadFiles->error." <br />";
							$er = true;
						}
						
					}

					if(!$er){
						$linkSectionFile->insertData(array(
							"linkSectionID" => $lastID,
							"photoID"       => $photoID,
							"fileID"        => $fileID,
							"priority"      => $priority,
							"elementWidth"  => $elementWidth,
							"isSlider"      => $isSlider,
							"showDetail"    => $showDetail,
							"dateAdd"       => date("Y-m-d H:i:s"),
							"userAdd"       => ""
						));
					}
				
				}
				
			}

			if(!empty($error))$this->view->error = $error;
			
		}
			
	}
	
	public function insertSectionHeaderElementData($isSec,$lastID,$priority,$elementNumber){
	
		$linkSectionHeaderSection = new Content_Models_LinkSectionHeaderSection();
		
		//nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$this->sheData["h1-".$val->suffix] = $this->_request->getPost("s-h-e-h1-".$isSec."-".$elementNumber."-".$val->suffix);
			$this->sheData["h2-".$val->suffix] = $this->_request->getPost("s-h-e-h2-".$isSec."-".$elementNumber."-".$val->suffix);
		}
		
		$this->sheData["align"]           = $this->_request->getPost("s-h-e-align-".$isSec."-".$elementNumber);
		$this->sheData["hType"]           = $this->_request->getPost("s-h-e-h-type-".$isSec."-".$elementNumber);
		$this->sheData["elementWidth"]    = $this->_request->getPost("s-h-e-element-width-".$isSec."-".$elementNumber);

		$linkSectionHeaderSection->insertData(array(
			"linkSectionID" => $lastID,
			"title"         => $this->sheData["h1-cz"],
			"titleH2"       => $this->sheData["h2-cz"],
		    "hType"         => $this->sheData["hType"],
		    "align"         => $this->sheData["align"],
		    "elementWidth"  => $this->sheData["elementWidth"],
			"priority"      => $priority,
			"dateAdd"       => date("Y-m-d H:i:s"),
			"userAdd"       => ""
		));

		$lID = $linkSectionHeaderSection->lastID;

		if($this->langModule)$this->updateDictionary("add","link-section-header-section","linkSectionHeaderSectionID",$lID);
	
	}
	
	public function insertTextElementData($isSec,$lastID,$priority,$elementNumber){
	
		$linkText = new Content_Models_LinkSectionText();
	
		//nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$this->teData["text-".$val->suffix] = $this->_request->getPost("t-e-".$isSec."-".$elementNumber."-".$val->suffix);
		}
		
		$this->teData["elementWidth"] = $this->_request->getPost("t-e-element-width-".$isSec."-".$elementNumber);
		$this->teData["elementFloat"] = $this->_request->getPost("t-e-element-float-".$isSec."-".$elementNumber);

		$linkText->insertData(array(
				"linkSectionID" => $lastID,
    		    "text"          => $this->teData["text-cz"],
		        "elementWidth"  => $this->teData["elementWidth"],
				"elementFloat"  => $this->teData["elementFloat"],
				"priority"      => $priority,
				"dateAdd"       => date("Y-m-d H:i:s"),
				"userAdd"       => ""
		));

		$lID = $linkText->lastID;

		if($this->langModule)$this->updateDictionary("add","link-section-text","linkSectionTextID",$lID);
	
	}
	
	public function insertYtvElementData($isSec,$lastID,$priority,$elementNumber){
	
		$linkYtv = new Content_Models_LinkSectionYtv();
	
		//nastavime vsechny jazykove verze
		$code         = $this->_request->getPost("ytv-element-".$isSec."-".$elementNumber);
		$category     = $this->_request->getPost("ytv-c-element-".$isSec."-".$elementNumber);
		$delete       = $this->_request->getPost("ytv-delete-".$isSec."-".$elementNumber);
		$elementWidth = $this->_request->getPost("ytv-element-width-".$isSec."-".$elementNumber);
		
		if(empty($delete))$delete = array();
		
		foreach($code as $key => $c){
			if(!empty($code)){
				
				$vData = $this->getVideoTypeCode($c);
								
				if($vData[0] && $vData[1] && !in_array(($key+1),$delete)){
					if(empty($category[$key]))$category[$key] = 0;
					
					$linkYtv->insertData(array(
						"linkSectionID" => $lastID,
						"categoryID"    => $category[$key],
						"code"          => $vData[1],	
					    "type"          => $vData[0],
					    "elementWidth"  => $elementWidth,
					    "priority"      => $priority,
						"dateAdd"       => date("Y-m-d H:i:s"),
						"userAdd"       => ""
					));
				}
			}
		}
	
	}
	
	public function insertLinkElementData($isSec,$lastID,$priority,$elementNumber,$linkID){
	
		$linkSectionLink = new Content_Models_LinkSectionLink();
		$script          = new Library_Scripts();
		$link            = new Content_Models_Link();
	
		$upload            = new Library_UploadFiles();
		$path              = "Public/Images/Link/Section/Link";
		$upload->path      = $path;
		$upload->ownName   = true;
		$upload->smallHeight = 116;
		$upload->smallWidth  = 0;
		$this->heData = array();
		
		//nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$this->leData["text-".$val->suffix]  = $this->_request->getPost("l-e-text-".$isSec."-".$elementNumber."-".$val->suffix);
			$this->leData["h2-".$val->suffix]  = $this->_request->getPost("l-e-h2-".$isSec."-".$elementNumber."-".$val->suffix);
			$this->leData["url-".$val->suffix] = $this->_request->getPost("l-e-url-".$isSec."-".$elementNumber."-".$val->suffix);
		}
		
		$this->leData["linkID"]          = $this->_request->getPost("l-e-linkID-".$isSec."-".$elementNumber);		
		$this->leData["elementWidth"]    = $this->_request->getPost("l-e-element-width-".$isSec."-".$elementNumber);
		
		$hiddenImages = $this->_request->getPost("l-e-f-".$isSec."-".$elementNumber);
		$colCount     = $this->_request->getPost("l-e-colCount-".$isSec."-".$elementNumber);
		$deleteImages = $this->_request->getPost("l-e-f-d-".$isSec."-".$elementNumber);
		
		if(empty($deleteImages))$deleteImages = array();
		if(empty($colCount))$colCount = 1;
			
		$fileName = "";
		
		foreach($this->leData["h2-cz"] as $key => $elmData){
			
			if(!empty($hiddenImages[$key])){
				$fileName = $hiddenImages[$key];
				if(in_array(($key+1),$deleteImages)){
					$fileName = "";
					unlink("./".$path."/".$hiddenImages[$key]);
					unlink("./".$path."/mala-".$hiddenImages[$key]);
				}
			}
			if(!empty($_FILES["l-e-file-".$isSec."-".$elementNumber]['tmp_name'][$key])){
					
				if(!empty($hiddenImages[$key]) && !in_array(($key+1),$deleteImages)){
					unlink("./".$path."/".$hiddenImages[$key]);
					unlink("./".$path."/mala-".$hiddenImages[$key]);
				}
					
				$upload->fileName    = $upload->niceFile($_FILES["l-e-file-".$isSec."-".$elementNumber]['name'][$key]);
				$upload->tmpFileName = $_FILES["l-e-file-".$isSec."-".$elementNumber]['tmp_name'][$key];
				$upload->upload();
					
				$fileName = $upload->fileName;
			}

			if(!empty($elmData) || !empty($fileName)  || !empty($this->leData["text-cz"][$key])){
				
				$lastLinkID = 0;

				$isSublink  = $this->_request->getPost("l-e-isSublink-".$isSec."-".$elementNumber."-".($key+1));
				
				if($isSublink){
					
					foreach($this->allLanguageMutations as $val){
						$this->leData["url-".$val->suffix][$key] = $script->url($this->leData["h2-".$val->suffix][$key]);
					}
					
					$niceTitle = $this->leData["url-cz"][$key];					
					$lastLinkID = 0;

					if(empty($this->leData["linkID"][$key])){
						$data = array(
							
			            	"parentID"      => $linkID,
						    "title"         => $this->leData["h2-cz"][$key],
						    "niceTitle"	    => $niceTitle,
						    "text"          => "",
			            	"active"	    => 1,
			            	"view"		    => 0,
			            	"date"	 		=> date("Y-m-d H:i:s",time()),
			            	"dateEdit"	    => "0000-00-00 00:00:00",
			            	"userAdd"	    => "",
			            	"showArticles"  => 0,
				            "showFacebook"  => 0,
			            	"otherLink"     => "",
			            	"priority"      => 1,
			            	"noDelete"      => 0,
			            	"inMenu"        => 0,
			            	"inFooter"      => 0,
			            	"metaTitle"     => "",
			            	"keywords"      => "",
			            	"description"   => ""			 
						);
					
						$link->insertData($data);
						$lastLinkID = $link->lastID;
							
						$allItems = $link->getAllItems(null, array("priority","linkID DESC"));
						$script->updatePriority($allItems, $link, "linkID");
					
						if($this->langModule){
							foreach($this->allLanguageMutations as $val){
								$this->title[$val->suffix]       = $this->leData["h2-".$val->suffix][$key];
								$this->niceTitle[$val->suffix]   = $this->leData["url-".$val->suffix][$key];
								$this->oldUrl[$val->suffix]      = "";
								$this->text[$val->suffix]        = "";
								$this->metaTitle[$val->suffix]   = "";
								$this->keywords[$val->suffix]    = "";
								$this->description[$val->suffix] = "";
								$this->otherLink[$val->suffix]   = "";
								$this->allowEditOtherLink        = 1;
							}
							$this->updateDictionary("add","link","linkID",$lastLinkID);
						}
							
					}else{
						$lastLinkID = $this->leData["linkID"][$key];
						
						$link->updateData(array(
							"title"     => $this->leData["h2-cz"][$key],
							"niceTitle" => $niceTitle
						), "linkID = '$lastLinkID'");
							
						if($this->langModule){
							foreach($this->allLanguageMutations as $val){
								$this->title[$val->suffix]       = $this->leData["h2-".$val->suffix][$key];
								$this->niceTitle[$val->suffix]   = $this->leData["url-".$val->suffix][$key];
								$this->oldUrl[$val->suffix]      = "";
								$this->text[$val->suffix]        = "";
								$this->metaTitle[$val->suffix]   = "";
								$this->keywords[$val->suffix]    = "";
								$this->description[$val->suffix] = "";
								$this->otherLink[$val->suffix]   = "";
								$this->allowEditOtherLink        = 1;
							}
							$stdClass = new stdClass();
							$stdClass->linkID = $lastLinkID;
					
							$this->updateDictionary("edit","link","linkID",$lastLinkID);
						}
							
					}
					
				}else{
					$isSublink = 0;
				}
				
				$linkSectionLink->insertData(array(
					"linkSectionID"    => $lastID,
					"groupSectionLink" => $elementNumber,
				    "linkID"           => $lastLinkID,
				    "colCount"         => $colCount,
					"isSublink"        => $isSublink,
					"text"             => $this->leData["text-cz"][$key],
				    "titleH2"          => $this->leData["h2-cz"][$key],
				    "url"              => $this->leData["url-cz"][$key],
				    "elementWidth"     => $this->leData["elementWidth"],				    
					"priority"         => $priority,
					"image"            => $fileName,
					"dateAdd"          => date("Y-m-d H:i:s"),
					"userAdd"          => ""
				));
			
				$lID = $linkSectionLink->lastID;
				$fileName = "";
			
				if($this->langModule)$this->updateDictionary("add","link-section-link","linkSectionLinkID",$lID,null,$key);
			}
		}

	}
	
	
	public function insertFormElementData($isSec,$lastID,$priority,$elementNumber){
		
		$linkSectionForm       = new Content_Models_LinkSectionForm();	
		$linkSectionFormValues = new Content_Models_LinkSectionFormValues();
		$script                = new Library_Scripts();
			
		$email  = $this->_request->getPost("f-e-email-".$isSec."-".$elementNumber);			
		
		$fevc             = $this->_request->getPost("form-element-values-count-".$isSec."-".$elementNumber);
		$elementWidth     = $this->_request->getPost("f-e-element-width-".$isSec."-".$elementNumber);
		
		for($i = 0;$i <= $fevc;$i++){
			
			foreach($this->allLanguageMutations as $val){
				$this->fData["title-".$val->suffix][$i] = $this->_request->getPost("f-e-name-".$isSec."-".$elementNumber."-".$i."-".$val->suffix);
			}
			$this->fData["type"][$i] = $this->_request->getPost("f-e-type-".$isSec."-".$elementNumber."-".$i);
			$exType = explode("-",$this->fData["type"][$i]);
			$this->fData["type"][$i] = $exType[0];
			
			if(!empty($this->fData["title-cz"][$i]) && !empty($this->fData["type"][$i])){
				$linkSectionForm->insertData(array(
					"linkSectionID"    => $lastID,
					"groupSectionForm" => $elementNumber,
					"title"            => $this->fData["title-cz"][$i],
				    "niceTitle"        => $script->url($this->fData["title-cz"][$i]),
				    "email"            => $email,
				    "elementWidth"     => $elementWidth,
					"type"             => $this->fData["type"][$i],
					"priority"         => $priority,					
					"dateAdd"          => date("Y-m-d H:i:s"),
					"userAdd"          => ""
				));

				$lID = $linkSectionForm->lastID;
				
				if($this->langModule)$this->updateDictionary("add","link-section-form","linkSectionFormID",$lID,null,$i);
				
				
				foreach($this->allLanguageMutations as $val){
					$this->fvData["value-".$val->suffix] = $this->_request->getPost("f-e-value-".$isSec."-".$elementNumber."-".$i."-".$val->suffix);
					$this->fvData["valueName-".$val->suffix] = $this->_request->getPost("f-e-valueName-".$isSec."-".$elementNumber."-".$i."-".$val->suffix);
				}
				
				foreach($this->fvData["value-cz"] as $key => $fVal){
					
					if(!empty($this->fvData["valueName-cz"][$key]) || $this->fData["type"][$i] == "text" || $this->fData["type"][$i] == "textarea"){
						
						$linkSectionFormValues->insertData(array(
							"linkSectionFormID" => $lID,
							"title" 			=> $this->fvData["valueName-cz"][$key],
							"value"             => $fVal
						));
						$fvID = $linkSectionFormValues->lastID;
						
						if($this->langModule)$this->updateDictionary("add","link-section-form-values","linkSectionFormValueID",$fvID,null,$key);
					}
					
				}
				
			}
		}
					
	}
	
	public function getHeaderElements($linkSection,$id,$linkTranslates){
		
		$translate = Zend_Registry::get("Zend_Translate");
		
		$allHeaderElements = $linkSection->getHeaderElements($id);
		if(!empty($allHeaderElements)){
				
			
			foreach($allHeaderElements as $elm){
		
				foreach($this->allLanguageMutations as $val){
				  
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["header"]["data"][$elm->linkSectionHeaderID]["titleH1"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["header"][$elm->linkSectionHeaderID]["titleH1"][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["header"]["data"][$elm->linkSectionHeaderID]["titleH2"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["header"][$elm->linkSectionHeaderID]["titleH2"][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["name"][$val->suffix] = $linkTranslates[$elm->linkSectionID]['name'][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["niceName"][$val->suffix] = $linkTranslates[$elm->linkSectionID]['niceName'][$val->suffix];
				}
		
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["header"]["data"][$elm->linkSectionHeaderID]["image"] = $elm->image;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["header"]["headerID"] = $elm->linkSectionHeaderID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["header"]["sectionID"] = $elm->linkSectionID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["header"]["groupLinkID"] = $elm->groupSectionLink;
				$this->updateSectionData[$elm->linkSectionID]["option"]["hasHeader"] = 1;
				$this->updateSectionData[$elm->linkSectionID]["option"]["hasSectionHeader"] = 1;
				$this->updateSectionData[$elm->linkSectionID]["option"]["bgColor"] = $elm->color;
				$this->updateSectionData[$elm->linkSectionID]["option"]["wide"] = $elm->wide;
				
				$this->updateSectionData[$elm->linkSectionID]["option"]["secNiceName"] = $elm->secNiceName;
				
		
			}
				
		}
		
	}
	
	public function getMapElements($linkSection,$id,$linkTranslates){
	
	
		$allMapElements = $linkSection->getMapElements($id);
		if(!empty($allMapElements)){
			foreach($allMapElements as $elm){
	
				foreach($this->allLanguageMutations as $val){
						
				    $this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["map"]["data"][$elm->linkSectionMapID]["title"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["map"][$elm->linkSectionMapID]["title"][$val->suffix];
				    
					$this->updateSectionData[$elm->linkSectionID]["option"]["name"][$val->suffix] 		= $linkTranslates[$elm->linkSectionID]['name'][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["niceName"][$val->suffix] 	= $linkTranslates[$elm->linkSectionID]['niceName'][$val->suffix];
				}
	
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["map"]["data"][$elm->linkSectionMapID]["lat"] = $elm->lat;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["map"]["data"][$elm->linkSectionMapID]["long"] = $elm->long;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["map"]["mapID"] = $elm->linkSectionMapID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["map"]["sectionID"] = $elm->linkSectionID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["map"]["groupLinkID"] = $elm->groupSectionLink;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["map"]["elementWidth"] = $elm->elementWidth;

				$this->updateSectionData[$elm->linkSectionID]["option"]["bgColor"] = $elm->color;
				$this->updateSectionData[$elm->linkSectionID]["option"]["wide"] = $elm->wide;
				$this->updateSectionData[$elm->linkSectionID]["option"]["secNiceName"] = $elm->secNiceName;
	
	
			}
	
		}
	
	}
	
	public function getArticleElements($linkSection,$id,$linkTranslates){
		
		$allArticleElements = $linkSection->getArticleElements($id);
		if(!empty($allArticleElements)){
			foreach($allArticleElements as $elm){
	
				foreach($this->allLanguageMutations as $val){						
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["article"]["url"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["article"][$elm->linkSectionArticleID]["url"][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["name"][$val->suffix] 		= $linkTranslates[$elm->linkSectionID]['name'][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["niceName"][$val->suffix] 	= $linkTranslates[$elm->linkSectionID]['niceName'][$val->suffix];
				}
				
	
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["article"]["type"] = $elm->type;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["article"]["linkID"] = $elm->linkID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["article"]["linkTitle"] = $elm->linkTitle;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["article"]["pageCount"] = $elm->pageCount;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["article"]["newCount"] = $elm->newCount;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["article"]["colNumber"] = $this->getColNumber($elm->newCount);
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["article"]["colCount"]  = $elm->newCount;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["article"]["articleID"] = $elm->linkSectionArticleID;

				$this->updateSectionData[$elm->linkSectionID]["option"]["bgColor"] = $elm->color;
				$this->updateSectionData[$elm->linkSectionID]["option"]["wide"] = $elm->wide;
				$this->updateSectionData[$elm->linkSectionID]["option"]["secNiceName"] = $elm->secNiceName;
	
			}
	
		}
	
	}
	
	public function getPhotoFileElements($linkSection,$id,$linkTranslates){
	
	
		$fileElements = $linkSection->getPhotoFileElements($id);
		if(!empty($fileElements)){
			
			foreach($fileElements as $elm){
	
				foreach($this->allLanguageMutations as $val){						
					$this->updateSectionData[$elm->linkSectionID]["option"]["name"][$val->suffix] 		= $linkTranslates[$elm->linkSectionID]['name'][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["niceName"][$val->suffix] 	= $linkTranslates[$elm->linkSectionID]['niceName'][$val->suffix];
				}

				if(!empty($elm->photoTitle)){
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["data"][$elm->linkSectionFileID]["photoFileTitle"] = $elm->photoTitle;
				}else if(!empty($elm->fileTitle)){
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["data"][$elm->linkSectionFileID]["photoFileTitle"] = $elm->fileTitle;
				}

				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["data"][$elm->linkSectionFileID]["ico"]       = $elm->ico;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["data"][$elm->linkSectionFileID]["photoID"]   = $elm->photoID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["data"][$elm->linkSectionFileID]["fileID"]    = $elm->fileID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["fileID"]                                     = $elm->linkSectionID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["priority"]                                   = $elm->priority;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["elementWidth"]                               = $elm->elementWidth;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["isSlider"]                                   = $elm->isSlider;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["photoFile"]["showDetail"]                                 = $elm->showDetail;
				
				$this->updateSectionData[$elm->linkSectionID]["option"]["bgColor"] = $elm->color;
				$this->updateSectionData[$elm->linkSectionID]["option"]["wide"] = $elm->wide;
				$this->updateSectionData[$elm->linkSectionID]["option"]["secNiceName"] = $elm->secNiceName;
				
			}
		}
	
	}
			
	public function getSectionHeaderElements($linkSection,$id,$linkTranslates){
	
	
		$allHeaderElements = $linkSection->getSectionHeaderElements($id);
	
		if(!empty($allHeaderElements)){
			foreach($allHeaderElements as $elm){
	
				foreach($this->allLanguageMutations as $val){
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["sectionHeader"]["titleH1"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["headerSection"][$elm->linkSectionHeaderSectionID]["title"][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["sectionHeader"]["titleH2"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["headerSection"][$elm->linkSectionHeaderSectionID]["titleH2"][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["name"][$val->suffix] = $linkTranslates[$elm->linkSectionID]['name'][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["niceName"][$val->suffix] = $linkTranslates[$elm->linkSectionID]['niceName'][$val->suffix];
				}
				
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["sectionHeader"]["align"] = $elm->align;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["sectionHeader"]["hType"] = $elm->hType;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["sectionHeader"]["sectionHeaderID"] = $elm->linkSectionHeaderSectionID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["sectionHeader"]["elementWidth"]    = $elm->elementWidth;
				$this->updateSectionData[$elm->linkSectionID]["option"]["hasSectionHeader"] = 1;
				$this->updateSectionData[$elm->linkSectionID]["option"]["bgColor"] = $elm->color;
				$this->updateSectionData[$elm->linkSectionID]["option"]["wide"] = $elm->wide;
				$this->updateSectionData[$elm->linkSectionID]["option"]["secNiceName"] = $elm->secNiceName;
				
				
			}
		}
	
	}
	
	public function getTextElements($linkSection,$id,$linkTranslates){
		
		$allHeaderElements = $linkSection->getTextElements($id);
	
		if(!empty($allHeaderElements)){
			foreach($allHeaderElements as $elm){
				foreach($this->allLanguageMutations as $val){
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["textElement"]["text"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["text"][$elm->linkSectionTextID]["text"][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["name"][$val->suffix] = $linkTranslates[$elm->linkSectionID]['name'][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["niceName"][$val->suffix] = $linkTranslates[$elm->linkSectionID]['niceName'][$val->suffix];
				}
	
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["textElement"]["textID"]          = $elm->linkSectionTextID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["textElement"]["elementWidth"]    = $elm->elementWidth;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["textElement"]["elementFloat"]    = $elm->elementFloat;
				$this->updateSectionData[$elm->linkSectionID]["option"]["bgColor"] = $elm->color;
				$this->updateSectionData[$elm->linkSectionID]["option"]["wide"] = $elm->wide;
				$this->updateSectionData[$elm->linkSectionID]["option"]["secNiceName"] = $elm->secNiceName;
				
			}
	
		}
	
	}
	
	public function getLinkElements($linkSection,$id,$linkTranslates){
	
	
		$allLinkElements = $linkSection->getLinkElements($id);
	
		if(!empty($allLinkElements)){
			foreach($allLinkElements as $elm){
	
				foreach($this->allLanguageMutations as $val){
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["data"][$elm->linkSectionLinkID]["text"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["link"][$elm->linkSectionLinkID]["text"][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["data"][$elm->linkSectionLinkID]["h2"][$val->suffix]   = $linkTranslates[$elm->linkSectionID]["elements"]["link"][$elm->linkSectionLinkID]["titleH2"][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["data"][$elm->linkSectionLinkID]["url"][$val->suffix]  = $linkTranslates[$elm->linkSectionID]["elements"]["link"][$elm->linkSectionLinkID]["url"][$val->suffix];
					
					$secName = explode(",",$elm->secNiceName);
					foreach($secName as $key => $scName){
						$explScName = explode("~",$scName);
						if(isset($explScName[0]) && isset($explScName[1]))$secName[$key] = array($explScName[0],$explScName[1]);
						
						if(empty($explScName[0]) && empty($explScName[1])){
							unset($secName[$key]);
						}
						
					}
					
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["data"][$elm->linkSectionLinkID]["secNiceName"]        = $secName;
					$this->updateSectionData[$elm->linkSectionID]["option"]["name"][$val->suffix] 		= $linkTranslates[$elm->linkSectionID]['name'][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["niceName"][$val->suffix] 	= $linkTranslates[$elm->linkSectionID]['niceName'][$val->suffix];
				}
				
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["data"][$elm->linkSectionLinkID]["image"]     = $elm->image;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["data"][$elm->linkSectionLinkID]["linkID"]    = $elm->linkID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["data"][$elm->linkSectionLinkID]["isSublink"] = $elm->isSublink;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["colCount"] = $elm->colCount;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["colNumber"] = $this->getColNumber($elm->colCount);
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["sectionID"] = $elm->linkSectionID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["groupLinkID"] = $elm->groupSectionLink;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["linkElement"]["elementWidth"]  = $elm->elementWidth;
				$this->updateSectionData[$elm->linkSectionID]["option"]["bgColor"] = $elm->color;
				$this->updateSectionData[$elm->linkSectionID]["option"]["wide"] = $elm->wide;
				$this->updateSectionData[$elm->linkSectionID]["option"]["secNiceName"] = $elm->secNiceName;
	
			}
		}
	
	}
	
	private function getColNumber($colCount){
		
		$colNumber = 1;
		switch($colCount){
			
			case 1:$colNumber = 12;break;
			case 2:$colNumber = 6;break;
			case 3:$colNumber = 4;break;
			case 4:$colNumber = 3;break;
			case 6:$colNumber = 2;break;
			
		}
		
		return $colNumber;
	}
	
	
	public function getFormElements($linkSection,$id,$linkTranslates){
	
		$sectionFormValues = new Content_Models_LinkSectionFormValues();
	
		$allLinkElements = $linkSection->getFormElements($id);
		$values = $sectionFormValues->getAllItems(null, null);
		$formelmValues = array();
		
		foreach($values as $v){
			$formelmValues[$v->linkSectionFormID][$v->linkSectionFormValueID] = $v;
		}
			
		if(!empty($allLinkElements)){
			foreach($allLinkElements as $elm){
	
				foreach($this->allLanguageMutations as $val){
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["formElement"]["data"][$elm->linkSectionFormID]["title"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["form"][$elm->linkSectionFormID]["title"][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["formElement"]["data"][$elm->linkSectionFormID]["type"] = $elm->type;
					$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["formElement"]["data"][$elm->linkSectionFormID]["niceTitle"] = $elm->niceTitle;
					
					$this->updateSectionData[$elm->linkSectionID]["option"]["name"][$val->suffix] 		= $linkTranslates[$elm->linkSectionID]['name'][$val->suffix];
					$this->updateSectionData[$elm->linkSectionID]["option"]["niceName"][$val->suffix] 	= $linkTranslates[$elm->linkSectionID]['niceName'][$val->suffix];
					
					if(!empty($formelmValues[$elm->linkSectionFormID])){
						foreach($formelmValues[$elm->linkSectionFormID] as $key => $vv){
							$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["formElement"]["data"][$elm->linkSectionFormID]["values"][$key]["valueName"][$val->suffix] = $linkTranslates[$elm->linkSectionID]["elements"]["formValue"][$key]["title"][$val->suffix];
							$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["formElement"]["data"][$elm->linkSectionFormID]["values"][$key]["value"][$val->suffix]     = $linkTranslates[$elm->linkSectionID]["elements"]["formValue"][$key]["value"][$val->suffix];
						}
					}
				}
				
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["formElement"]["email"] = $elm->email;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["formElement"]["sectionID"] = $elm->linkSectionID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["formElement"]["groupFormID"] = $elm->groupSectionForm;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["formElement"]["elementWidth"]  = $elm->elementWidth;
				$this->updateSectionData[$elm->linkSectionID]["option"]["bgColor"] = $elm->color;
				$this->updateSectionData[$elm->linkSectionID]["option"]["wide"] = $elm->wide;
				$this->updateSectionData[$elm->linkSectionID]["option"]["secNiceName"] = $elm->secNiceName;
			}
	
		}
	
	}
	
	public function getYtvElements($linkSection,$id,$linkTranslates){
		
		$allHeaderElements = $linkSection->getYtvElements($id);
		
		if(!empty($allHeaderElements)){
			foreach($allHeaderElements as $elm){
				
					foreach($this->allLanguageMutations as $val){
						$this->updateSectionData[$elm->linkSectionID]["option"]["name"][$val->suffix] 		= $linkTranslates[$elm->linkSectionID]['name'][$val->suffix];
						$this->updateSectionData[$elm->linkSectionID]["option"]["niceName"][$val->suffix] 	= $linkTranslates[$elm->linkSectionID]['niceName'][$val->suffix];
					}
				
				
				$allCategories = $linkSection->getSectionCategories($elm->linkSectionID,$elm->priority);
	
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["ytvElement"]["data"][$elm->linkSectionYtvID]["code"]       = $elm->code;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["ytvElement"]["data"][$elm->linkSectionYtvID]["type"]       = $elm->type;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["ytvElement"]["data"][$elm->linkSectionYtvID]["categoryID"] = $elm->categoryID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["ytvElement"]["categories"] = $allCategories;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["ytvElement"]["ytvID"]      = $elm->linkSectionYtvID;
				$this->updateSectionData[$elm->linkSectionID]["elements"][$elm->priority]["ytvElement"]["elementWidth"]  = $elm->elementWidth;
				$this->updateSectionData[$elm->linkSectionID]["option"]["bgColor"] = $elm->color;
				$this->updateSectionData[$elm->linkSectionID]["option"]["wide"] = $elm->wide;
				$this->updateSectionData[$elm->linkSectionID]["option"]["secNiceName"] = $elm->secNiceName;
	
			}

		}
	
	}
	
    public function deleteLinkData($del){
    	
    	$link     	    = new Content_Models_Link();
    	$photo          = new Models_Photo();
    	$photoLink      = new Content_Models_PhotoLink();
    	$video          = new Models_Video();
    	$videoLink      = new Content_Models_VideoLink();
    	$webvideo       = new Models_WebVideo();
    	$webvideoLink   = new Content_Models_WebVideoLink();
    	$file		    = new Models_File();
    	$fileLink       = new Content_Models_FileLink();
    	$script         = new Library_Scripts();
    	$articleLink	   = new Content_Models_ArticleLink();
    	$linkSection       = new Content_Models_LinkSection();
    	$linkSectionHeader = new Content_Models_LinkSectionHeader();
    	
    	//zde povolime smazani ve slovniku vzdy
    	$this->allowEditOtherLink = true;
    	
    	$table            = "photo_link";
    	$vTable           = "video_link";
    	$fTable           = "file_link";
    	$tableID          = "linkID";
    	
    	$id = $del;
 	
    	$subLinks  = $link->getAllItems("parentID <> '0'",'priority');
    	 
    	$this->subLinksArr = array();
    	
    	//predpripravime si vsechny subodkazy do pole
    	foreach($subLinks as $val){
    		$this->subLinksArr[$val->parentID][] = $val;
    	}
    	 
    	//rekurzivne projdeme vsechny pododkazy k danemu odkazu, na ktery jsme klikli a ty smazeme
    	if(!empty($this->subLinksArr[$id]))
    	$this->recurseDeleteLinks($this->subLinksArr[$id],$table,$tableID,$fTable,$vTable,$link,$photo,$photoLink,$video,$videoLink,$webvideoLink,$file,$fileLink,$webvideo,$articleLink,$linkSection,$linkSectionHeader);
    	
    	//smazeme odkaz na ktery jsme klikli
    	$this->deleteLink($id,$table,$tableID,$fTable,$vTable,$link,$photo,$photoLink,$video,$videoLink,$webvideoLink,$file,$fileLink,$webvideo,$articleLink,$linkSection,$linkSectionHeader);
    	
    	//smazání dat ze slovniku
    	//$this->updateDictionary('delete',$result);
    		
    	$allItems = $link->getAllItems(null,"priority");
    	$script->updatePriority($allItems, $link, "linkID");
    	
    }
    
    private function recurseDeleteLinks($children,$table,$tableID,$fTable,$vTable,$link,$photo,$photoLink,$video,$videoLink,$webvideoLink,$file,$fileLink,$webvideo,$articleLink,$linkSection,$linkSectionHeader){
    	//prochazime postupne od korene a zanorujeme se do childu
    	foreach($children as $child){
    			
    		if(!empty($this->subLinksArr[$child->linkID]))
    		$this->recurseDeleteLinks($this->subLinksArr[$child->linkID],$table,$tableID,$fTable,$vTable,$link,$photo,$photoLink,$video,$videoLink,$webvideoLink,$file,$fileLink,$webvideo,$articleLink,$linkSection,$linkSectionHeader);
    		 
    		$this->deleteLink($child->linkID,$table,$tableID,$fTable,$vTable,$link,$photo,$photoLink,$video,$videoLink,$webvideoLink,$file,$fileLink,$webvideo,$articleLink,$linkSection,$linkSectionHeader);
    	}
    }
    private function deleteLink($id,$table,$tableID,$fTable,$vTable,$link,$photo,$photoLink,$video,$videoLink,$webvideoLink,$file,$fileLink,$webvideo,$articleLink,$linkSection,$linkSectionHeader){

    	$link 		= new Content_Models_Link();
    	$allPhotos 	= $photo->getAllPhotos($id,$table,$tableID);
    	$linkData 	= $link->getOneRow("linkID = $id");
    	
    	foreach($allPhotos as $value){
    		 
    		unlink("./Public/Images/Link/".$value->title);
    		unlink("./Public/Images/Link/mala-".$value->title);
    		unlink("./Public/Images/Link/stredni-".$value->title);
    		$wherePhoto = "photoID = '$value->photoID' ";
    		$photo->deleteData($wherePhoto);
    		 
    		//vymazeme ze slovniku pokud je zaply modul
    		if($this->modulesData->jazykoveMutace)
    		$this->updateDictionary('delete','photo','photoID',$value->photoID);
    	}
    	 
    	$allFiles = $file->getAllFiles($id,$fTable,$tableID);
    	foreach($allFiles as $value){
    		 
    		unlink("./Public/Files/Link/".$value->title);
    		$whereFile = "fileID = '$value->fileID' ";
    		$file->deleteData($whereFile);
    
    		//vymazeme ze slovniku pokud je zaply modul
    		if($this->modulesData->jazykoveMutace)
    		$this->updateDictionary('delete','file','fileID',$value->fileID);
    		 
    	}
    	 
    	$allVideos = $video->getAllVideos($id, $vTable, $tableID);
    	foreach($allVideos as $value){
    		 
    		unlink("./Public/Images/Previews/Link/".$value->file.".png");
    		unlink("./Public/Images/Previews/Link/mala-".$value->file.".png");
    		unlink("./Public/Videos/Link/".$value->file.".mp4");
    		$whereVideo = "videoID = '$value->videoID' ";
    		$video->deleteData($whereVideo);
    
    		//vymazeme ze slovniku pokud je zaply modul
    		if($this->modulesData->jazykoveMutace)
    		$this->updateDictionary('delete','video','videoID',$value->videoID);
    	}
    	 
    	$allLinkVideos = $webvideoLink->getVideo($id);
    	foreach($allLinkVideos as $value){
    		 
    		$whereVideo = "webVideoID = '$value->webVideoID' ";
    		$webvideo->deleteData($whereVideo);
    		 
    	}
    	 
    	$where =   "linkID = '$id' ";
    	 
    	$webvideoLink   -> deleteData($where);
    	$photoLink      -> deleteData($where);
    	$fileLink       -> deleteData($where);
    	$videoLink      -> deleteData($where);
    	//smazeme v tabulce article_link vsechny clanky ketre byly spojeny s danym odkazem
    	$articles 		= $articleLink->getLastLinkArticles($id);
    	if(!empty($articles)){
    		$IN = "(";
    		for($i = 0; $i < count($articles); $i++){
    			if($i == 0) $IN .= 		$articles[$i]->articleID;
    			else		$IN .= ",".	$articles[$i]->articleID;
    		}
    		$IN .= ")";
    		$articleLink	-> deleteData("articleID IN $IN ");
    	}
    	//nasledne smazeme v tabulce article_link vsechny zaznamy s danym linkID
    	$articleLink	-> deleteData($where);
    	
    	//smazání sekcí a elementů
    	$this->deleteAllSections($id);
    	
    	//smazání dat ze slovniku
    	if($this->modulesData->jazykoveMutace){
			$this->updateDictionary('delete',"link","linkID",$id);
    	}
    	
    	if(!empty($linkData->categoryPhoto)){
    		@unlink("./Public/Images/EshopCategory/".$linkData->categoryPhoto);
    		@unlink("./Public/Images/EshopCategory/mala-".$linkData->categoryPhoto);
    		@unlink("./Public/Images/EshopCategory/stredni-".$linkData->categoryPhoto);
    		@unlink("./Public/Images/EshopCategory/velka-".$linkData->categoryPhoto);
    		@unlink("./Public/Images/EshopCategory/maxi-".$linkData->categoryPhoto);
    	}
    
    	$link -> deleteData($where);
    	 
    }
    
    public function deleteAllSections($id,$deleteImages = true){
    	
    	$linkSection = new Content_Models_LinkSection();
    	$sections = $linkSection->getAllItems("linkID = '$id'",null);
    	foreach($sections as $sec){
    		
			$this->updateDictionary("delete","link-section","linkSectionID",$sec->linkSectionID);
    	
    		//header element
    		$this->deleteHeaderElement(null,$sec->linkSectionID,$deleteImages);
    		$this->deleteSectionHeaderElement(null,$sec->linkSectionID);
    		$this->deleteTextElement(null,$sec->linkSectionID);
    		$this->deleteLinkElement(null,$sec->linkSectionID,$deleteImages);
    		$this->deleteFormElement(null,$sec->linkSectionID);
    		$this->deleteYtvElement(null,$sec->linkSectionID);
    		$this->deleteArticleElement(null,$sec->linkSectionID);
    		$this->deletePhotoFileElement(null,$sec->linkSectionID,$deleteImages);
    		$this->deleteMapElement(null,$sec->linkSectionID);
    	
    		//section header element
    	
    	
    	}
    	$linkSection->deleteData("linkID = '$id'");
    	
    }
    
    public function deleteSection($sectionID){
    	
    	$linkSection = new Content_Models_LinkSection();
    	$linkSection->deleteData("linkSectionID = '$sectionID'");
    	
		$this->updateDictionary("delete","link-section","linkSectionID",$sectionID);
    	
    	$this->deleteHeaderElement(null,$sectionID);
    	$this->deleteSectionHeaderElement(null,$sectionID);
    	$this->deleteTextElement(null,$sectionID);
    	$this->deleteLinkElement(null,$sectionID);
    	$this->deleteFormElement(null,$sectionID);
    	$this->deleteYtvElement(null,$sectionID);
    	$this->deleteArticleElement(null,$sectionID);
    	$this->deletePhotoFileElement(null,$sectionID);
    	$this->deleteMapElement(null,$sectionID);
    	
    }
    
    public function deleteHeaderElement($linkSectionHeaderID = null,$linkSectionID = null,$deleteImages = true){
    	
    	$linkSectionHeader = new Content_Models_LinkSectionHeader();
    	
    	if($linkSectionHeaderID){
    		
    		$linkSectionHeaderID = explode("-",$linkSectionHeaderID);
    		
	    	$allHeaderElement = $linkSectionHeader->getAllItems("linkSectionID = '$linkSectionHeaderID[0]' AND groupSectionLink='$linkSectionHeaderID[1]'",null);
	    	
	    	foreach($allHeaderElement as $he){
	    		if(!empty($he->image) && $deleteImages)unlink("./Public/Images/Link/Section/Header/".$he->image);
	    	    if(!empty($he->image) && $deleteImages)unlink("./Public/Images/Link/Section/Header/mala-".$he->image);
	    		 
				$this->updateDictionary("delete","link-section-header","linkSectionHeaderID",$sectionID);
	    	}
	    	
	    	$linkSectionHeader->deleteData("linkSectionID = '$linkSectionHeaderID[0]' AND groupSectionLink='$linkSectionHeaderID[1]'");
	    	
    	}elseif($linkSectionID){
    		
    		$allHeaderElements = $linkSectionHeader->getAllItems("linkSectionID = '$linkSectionID'",null);
    		foreach($allHeaderElements as $he){
    			 
    			if(!empty($he->image) && $deleteImages)unlink("./Public/Images/Link/Section/Header/".$he->image);
    			if(!empty($he->image) && $deleteImages)unlink("./Public/Images/Link/Section/Header/mala-".$he->image);
    			 
				$this->updateDictionary("delete","link-section-header","linkSectionHeaderID",$he->linkSectionHeaderID);
    		}
    		$linkSectionHeader->deleteData("linkSectionID = '$linkSectionID'");
    		
    	}
    	
    }
    
    public function deleteMapElement($linkSectionMapID = null,$linkSectionID = null){
    
    	$linkSectionMap = new Content_Models_LinkSectionMap();
    
    	if($linkSectionMapID){
    
    		$linkSectionMapID = explode("-",$linkSectionMapID);
    
    		$allHeaderElement = $linkSectionMap->getAllItems("linkSectionID = '$linkSectionMapID[0]' AND groupSectionLink='$linkSectionMapID[1]'",null);
    
    		foreach($allHeaderElement as $he){
    			 
				$this->updateDictionary("delete","link-section-map","linkSectionMapID",$he->linkSectionMapID);
    		}
    
    		$linkSectionMap->deleteData("linkSectionID = '$linkSectionMapID[0]' AND groupSectionLink='$linkSectionMapID[1]'");
    
    	}elseif($linkSectionID){
    
    		$allMapElements = $linkSectionMap->getAllItems("linkSectionID = '$linkSectionID'",null);
    		foreach($allMapElements as $he){
    
				$this->updateDictionary("delete","link-section-map","linkSectionMapID",$he->linkSectionMapID);
    		}
    		$linkSectionMap->deleteData("linkSectionID = '$linkSectionID'");
    
    	}
    
    }
    
    public function deleteArticleElement($linkSectionArticleID = null,$linkSectionID = null,$deleteImages = true){
    	 
    	$linkSectionArticle = new Content_Models_LinkSectionArticle();
    	 
    	if($linkSectionArticleID){
    
    		$allHeaderElement = $linkSectionArticle->getOneRow("linkSectionArticleID = '$linkSectionArticleID'");

			$this->updateDictionary("delete","link-section-article","linkSectionArticleID",$linkSectionArticleID);
    
    		$linkSectionArticle->deleteData("linkSectionArticleID = '$linkSectionArticleID'");
    
    	}elseif($linkSectionID){
    
    		$allHeaderElements = $linkSectionArticle->getAllItems("linkSectionID = '$linkSectionID'",null);
    		foreach($allHeaderElements as $he){
        
				$this->updateDictionary("delete","link-section-article","linkSectionArticleID",$he->linkSectionArticleID);
    		}
    		$linkSectionArticle->deleteData("linkSectionID = '$linkSectionID'");
    
    	}
    	 
    }
    
    public function deletePhotoFileElement($linkSectionFileID = null,$linkSectionID = null,$deleteImages = true){
    
    	$linkSectionFile = new Content_Models_LinkSectionFile();
    	$fileTable  = new Models_File();
    	$photoTable = new Models_Photo();
    	
    	if($deleteImages){
	    	if($linkSectionFileID){
	    		
	    		$linkSectionFileID = explode("-",$linkSectionFileID);
	    		
	    		$allLinkElement = $linkSectionFile->getFiles("LSF.linkSectionID = '$linkSectionFileID[0]' AND LSF.priority='$linkSectionFileID[1]'",null);   		
	    		foreach($allLinkElement as $he){
		    		if(!empty($he->title)){
		    			unlink("./Public/Images/Link/Section/File/".$he->title);
		    			unlink("./Public/Images/Link/Section/File/mala-".$he->title);
		    			unlink("./Public/Images/Link/Section/File/stredni-".$he->title);
		    			$photoTable->deleteData("photoID = '$he->photoID'");
		    		}
		    		
		    		
		    		if(!empty($he->fileTitle) && $deleteImages){
		    			unlink("./Public/Files/Link/Section/File/".$he->fileTitle);
		    			$fileTable->deleteData("fileID = '$he->fileID'");
		    		}
	    		}
	    		$linkSectionFile->deleteData("linkSectionID = '$linkSectionFileID[0]' AND priority='$linkSectionFileID[1]'");
	    
	    	}elseif($linkSectionID){
	    
	    		$allTextElements = $linkSectionFile->getFiles("LSF.linkSectionID = '$linkSectionID'",null);
	    		foreach($allTextElements as $he){
	    			
	    			if(!empty($he->title)){
		    			unlink("./Public/Images/Link/Section/File/".$he->title);
		    			unlink("./Public/Images/Link/Section/File/mala-".$he->title);
		    			unlink("./Public/Images/Link/Section/File/stredni-".$he->title);
		    			$photoTable->deleteData("photoID = '$he->photoID'");
		    		}
		    		
		    		
		    		if(!empty($he->fileTitle) && $deleteImages){
		    			unlink("./Public/Files/Link/Section/File/".$he->fileTitle);
		    			$fileTable->deleteData("fileID = '$he->fileID'");
		    		}
	    			
	    		}
	    		$linkSectionFile->deleteData("linkSectionID = '$linkSectionID'");
	    
	    	}
    	}else{
    		
    		$filesToDelete = $this->_request->getPost("deletePF");
    		if($filesToDelete){
    			foreach($filesToDelete as $fi){
    				
    				$fi = explode("~",$fi);    				
    				if($fi[2]){
    					if(file_exists("./Public/Images/Link/Section/File/".$fi[1]))unlink("./Public/Images/Link/Section/File/".$fi[1]);
    					if(file_exists("./Public/Images/Link/Section/File/mala-".$fi[1]))unlink("./Public/Images/Link/Section/File/mala-".$fi[1]);
    					if(file_exists("./Public/Images/Link/Section/File/stredni-".$fi[1]))unlink("./Public/Images/Link/Section/File/stredni-".$fi[1]);
    					$photoTable->deleteData("photoID = '$fi[2]'");
    				}else{
    					if(file_exists("./Public/Files/Link/Section/File/".$fi[1]))unlink("./Public/Files/Link/Section/File/".$fi[1]);
    					$fileTable->deleteData("fileID = '$fi[3]'");
    				}
    				
    				$linkSectionFile->deleteData("linkSectionFileID = '$fi[0]'");
    			}
    		}
    	}
    
    }
    
    public function deleteSectionHeaderElement($linkSectionHeaderSectionID = null,$linkSectionID = null){
    	 
    	$linkSectionHeaderSection = new Content_Models_LinkSectionHeaderSection();
    	 
    	if($linkSectionHeaderSectionID){
             
			$this->updateDictionary("delete","link-section-header-section","linkSectionHeaderSectionID",$linkSectionHeaderSectionID); 
    		$linkSectionHeaderSection->deleteData("linkSectionHeaderSectionID = '$linkSectionHeaderSectionID'");
    
    	}elseif($linkSectionID){
    
    		$allHeaderElements = $linkSectionHeaderSection->getAllItems("linkSectionID = '$linkSectionID'",null);
    		foreach($allHeaderElements as $he){
        
				$this->updateDictionary("delete","link-section-header-section","linkSectionHeaderSectionID",$he->linkSectionHeaderSectionID); 
    		}
    		$linkSectionHeaderSection->deleteData("linkSectionID = '$linkSectionID'");
    
    	}
    	 
    }
    
    public function deleteTextElement($linkSectionTextID = null,$linkSectionID = null){
    
    	$linkSectionText = new Content_Models_LinkSectionText();
    
    	if($linkSectionTextID){
    
			$this->updateDictionary("delete","link-section-text","linkSectionTextID",$linkSectionTextID); 
    		$linkSectionText->deleteData("linkSectionTextID = '$linkSectionTextID'");
    
    	}elseif($linkSectionID){
    
    		$allTextElements = $linkSectionText->getAllItems("linkSectionID = '$linkSectionID'",null);
    		foreach($allTextElements as $he){
    
				$this->updateDictionary("delete","link-section-text","linkSectionTextID",$he->linkSectionTextID); 
    		}
    		$linkSectionText->deleteData("linkSectionID = '$linkSectionID'");
    
    	}
    
    }
    
    public function deleteYtvElement($linkSectionYtvID = null,$linkSectionID = null){
    
    	$linkSectionYtv = new Content_Models_LinkSectionYtv();
    
    	if($linkSectionYtvID){
    
    		$linkSectionYtv->deleteData("linkSectionYtvID = '$linkSectionYtvID'");
    
    	}elseif($linkSectionID){
    
    		$linkSectionYtv->deleteData("linkSectionID = '$linkSectionID'");
    
    	}
    
    }
    
    public function deleteLinkElement($linkSectionLinkID = null,$linkSectionID = null,$deleteImages = true){
    
    	$linkSectionLink = new Content_Models_LinkSectionLink();
    	
    	if($linkSectionLinkID){
    		
    		$linkSectionLinkID = explode("-",$linkSectionLinkID);
    		
    		$allLinkElement = $linkSectionLink->getAllItems("linkSectionID = '$linkSectionLinkID[0]' AND groupSectionLink='$linkSectionLinkID[1]'",null);   		
    		foreach($allLinkElement as $he){
	    		if(!empty($he->image) && $deleteImages)unlink("./Public/Images/Link/Section/Link/".$he->image);
	    		if(!empty($he->image) && $deleteImages)unlink("./Public/Images/Link/Section/Link/mala-".$he->image);
	    
				$this->updateDictionary("delete","link-section-link","linkSectionLinkID",$lID); 
    		}
    		$linkSectionLink->deleteData("linkSectionID = '$linkSectionLinkID[0]' AND groupSectionLink='$linkSectionLinkID[1]'");
    
    	}elseif($linkSectionID){
    
    		$allTextElements = $linkSectionLink->getAllItems("linkSectionID = '$linkSectionID'",null);
    		foreach($allTextElements as $he){
    
    			if(!empty($he->image) && $deleteImages)unlink("./Public/Images/Link/Section/Link/".$he->image);
    			if(!empty($he->image) && $deleteImages)unlink("./Public/Images/Link/Section/Link/mala-".$he->image);
    			
				$this->updateDictionary("delete","link-section-link","linkSectionLinkID",$he->linkSectionLinkID); 
    		}
    		$linkSectionLink->deleteData("linkSectionID = '$linkSectionID'");
    
    	}
    
    }
    
    public function deleteFormElement($linkSectionFormID = null,$linkSectionID = null){
    
    	$linkSectionForm = new Content_Models_LinkSectionForm();
    	$linkSectionFormValues = new Content_Models_LinkSectionFormValues();
    
    	if($linkSectionFormID){
    
    		$linkSectionFormID = explode("-",$linkSectionFormID);
    
    		$allFormElement = $linkSectionForm->getAllItems("linkSectionID = '$linkSectionFormID[0]' AND groupSectionForm='$linkSectionFormID[1]'",null);
    		foreach($allFormElement as $he){
				$this->updateDictionary("delete","link-section-form","linkSectionFormID",$he->linkSectionFormID); 
    			
    			$allfv = $linkSectionFormValues->getAllItems("linkSectionFormID = '$he->linkSectionFormID'", null);
    			foreach($allfv as $fv){
    				$this->updateDictionary("delete",'link-section-form-values',"linkSectionFormValueID",$fv->linkSectionFormValueID);
    			}
    			$linkSectionFormValues->deleteData("linkSectionFormID = '$he->linkSectionFormID'");
    		}
    		$linkSectionForm->deleteData("linkSectionID = '$linkSectionFormID[0]' AND groupSectionForm='$linkSectionFormID[1]'");
    
    	}elseif($linkSectionID){
    
    		$allLinkElement = $linkSectionForm->getAllItems("linkSectionID = '$linkSectionID'",null);
    		foreach($allLinkElement as $he){
				$this->updateDictionary("delete","link-section-form","linkSectionFormID",$he->linkSectionFormID); 
    			
    			$allfv = $linkSectionFormValues->getAllItems("linkSectionFormID = '$he->linkSectionFormID'", null);
    			foreach($allfv as $fv){
					$this->updateDictionary("delete","link-section-form-values","linkSectionFormValueID",$fv->linkSectionFormValueID); 
    			}
    			$linkSectionFormValues->deleteData("linkSectionFormID = '$he->linkSectionFormID'");
    			
    		}
    		$linkSectionForm->deleteData("linkSectionID = '$linkSectionID'");
    
    	}
    
    }
    
	
}
