<?php
class Content_ClankyController extends Content_Library_WholeContent
{
	
	protected $title;
	protected $text;
	protected $date;
	protected $anotation;
	protected $mainPhoto;
	protected $photos;
	protected $linkID;
	protected $typePageAct;
	protected $toDetailSub;
	protected $otherLink;
	protected $formTitle;
	protected $showForm;
	protected $showFacebook;
	protected $showArticle;
	protected $showSubLink;
	protected $recommended;
	protected $related;
	protected $metaTitle;
	
	protected $allLanguageMutations;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();	
    	$this->view->articleSection = true;
			
    }
    function indexAction()
	{
		$this->_redirect("/admin/obsah/clanky/pridat");		
	}

	function pridatAction()
	{
		$this->view->subSelected = "Přidat článek";
		$article			= new Content_Models_Article();
		$script				= new Library_Scripts();
		$this->view->date	= date("j.n.Y",Time());
	  	
	    //pro vlozeni fotek a videi. Videa mají­ předponu "v"
	    $language 		 = new Models_Language_Language();
		$articleLink 	 = new Content_Models_ArticleLink();
	    $link 			 = new Content_Models_Link();
		$photo           = new Models_Photo();
		$file            = new Models_File();
		$video           = new Models_Video();
		$connectTable	 = new Content_Models_PhotoArticle();
		$vConnectTable	 = new Content_Models_VideoArticle();
		$fConnectTable   = new Content_Models_FileArticle();
		$folder          = "Temp";
		$table           = "photo_article";
		$vTable          = "video_article";
		$fTable          = "file_article";
		$path            = "Public/Images/";
		$vPath           = "Public/Videos/";
		$fPath           = "Public/Files/";
		$tableID         = "articleID";
		$newFolder		 = "Article";
		// konec vlozeni fotek a videi
		
		//vybereme vsechny jazykove mutace
		
		$mainLangData = $language->getMainLang();
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/obsah/clanky/pridat";
        $this->view->homepage = 0;
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$filter = $this->setData();
	    	
	    		if( /*empty($this->photos) || $filter->isValid("mainPhoto")*/ true){
		            if($filter->isValid("title-".$mainLangData->suffix) && $filter->isValid("date") && !empty($this->linkID)){

		            		if(empty($this->anotation[$mainLangData->suffix]))$this->anotation[$mainLangData->suffix] = "";
		            		if(empty($this->text[$mainLangData->suffix]))$this->text[$mainLangData->suffix] = "";
		            	
		            		$data = array(
					    	        
					    			"title"        => $this->title[$mainLangData->suffix],
					    			"niceTitle"	   => $this->niceTitle[$mainLangData->suffix],
					    	        "text"         => $this->text[$mainLangData->suffix],
		            		    "anotation"    => $this->anotation[$mainLangData->suffix],
		            				"active"	   => $this->showArticle,
		            				"recommended"  => $this->recommended,
		            				"view"		   => 0,
		            				"date"	 	   => date("Y-m-d H:i:s",strtotime($this->date)),
		            				"userAdd"	   => $this->user,
		            				"dateAdd"	   => date("Y-m-d H:i:s",Time()),
		            				"formTitle"    => $this->formTitle[$mainLangData->suffix],
		            				"showForm" 	   => $this->showForm,
		            				"showFacebook" => $this->showFacebook,
		            				"priority"     => 1,
		            				"metaTitle"    => $this->metaTitle[$mainLangData->suffix],
		            				"keywords"     => $this->keywords[$mainLangData->suffix],
		            				"description"  => $this->description[$mainLangData->suffix]
					    			
					    	);
					    	
					    	$article->insertData($data);
					    	$id = $article->lastID;
					    	
					    	$seo = new Seo_Sitemap_Models_Sitemap();
				            $seo->updateData(array("doUpdate" => 1), "seoID = 1");
					    	
					    	//priradime clanku veskere odkazy rekurzivne az ke korenu, pod ktere dany odkaz patri
		            		if(count($this->linkID) > 0){
					    		$addedLinks = array();
						    	foreach($this->linkID as $li){
							    	//priradime clanku veskere odkazy rekurzivne az ke korenu, pod ktere dany odkaz patri
							    	if(!empty($li)){
							    		if(in_array($li, $addedLinks))continue;
							    		$addedLinks[] = $li;
							    		$this->generateRelatedLinks($id,$li,$link);
							    	}
						    	}
					    	}
					    	
							if($this->modulesData->clankyUrlVideo){
					    		$this->addWebVideos($id);
							} 
					    	
							//pridani fotek
							if($this->modulesData->clankyFoto){
								$this->addPhoto($id,$table,$connectTable,$tableID,$folder,$newFolder);					
					    		setcookie("mainPhoto","",NULL,"/");
							} 
				    		
				    		//pridani souborů
							if($this->modulesData->clankySoubor){
				    			$this->addFile($id,$fTable,$fConnectTable,$tableID,$newFolder);			    		
							} 
				    		//pridani videi
							if($this->modulesData->clankyMp4Video){
								$this->addVideo($id,$vTable,$vConnectTable,$tableID,$newFolder);						
							} 
							
							//pridani souvisejicich produktu
							$this->setRelatedArticles($id);
							
							//uprava priority clanku
							$topLinkID = $articleLink->getArticleTopLink($id);
							$allItems = $article->getAllLevelLinkArticles($topLinkID,1,"A.priority,A.articleID DESC");
							$script->updatePriority($allItems, $article,"articleID");
				    		
							//pridani do slovniku
							if($this->modulesData->jazykoveMutace){
						    	//vlozeni dat do slovniku
						    	$this->updateDictionary('add','article','articleID',$id);
							}
					        $this->view->message = "Článek úspěšně přidán";	
		            	
		            	
			    	}else{
			    		
			    		$this->getBackData();
			    		$this->view->error = "Nevyplnili jste povinné údaje";
			    		
			    	}
	    		}else{
			    		
			    		$this->getBackData();
			    		$this->view->error = "Nenahráli jste fotku nebo jste neurčili hlavní fotku z již nahraných !";
			    		
			    }
	    	
	        
	    }
	    
	    if(isset($_COOKIE["mainPhoto"]))$this->view->mainPhoto  = $_COOKIE["mainPhoto"];
		
	    if($this->modulesData->clankyFoto)		
			$this->view->allTempPhotos 								= $photo->getTempPhotos($this->adminUserID,$table);	    
		if($this->modulesData->clankyMp4Video)
			$this->view->allTempVideos 								= $video->getTempVideos($this->adminUserID,$vTable);		
		if($this->modulesData->clankySoubor)
			$this->view->allTempFiles 								= $file ->getTempFiles($this->adminUserID,$fTable);
		
		
		
        $this->view->path        								= $path;
	    $this->view->table         								= $table;
	    
	    $this->view->tableID = $this->view->vTableID = $this->view->fTableID = $tableID;
	    $this->view->folder  = $this->view->vFolder	 = $this->view->fFolder	 = $folder;
	    
	    $this->view->vPath          							= $vPath;
	    $this->view->vTable         							= $vTable;
	    $this->view->vTableIDvalue  							= "";       

	    $this->view->fPath          							= $fPath;
	    $this->view->fTable         							= $fTable;
	    $this->view->fTableIDvalue  							= "";
	    	    	    	    
	    //vybereme vsechny clanky pro vybrani souvisejicich predmetu
	    if($this->modulesData->clankySouvisejici){
	    	$this->view->allArticles = 	$article->getAllItems();
	    }	
	    //vybereme vsechny soubory z FTP Public/Files/Article
	    $this->view->allFTPFiles = $this->getFTPFiles("/Public/Files/".$newFolder);
	    
	    //nastavi všechny odkazy
	    $this->setMenuLinks($link,"add");
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}
		
	function upravitAction()
	{
	
	    $link 			 = new Content_Models_Link();
	    $language 		 = new Models_Language_Language();
		$article  		 = new Content_Models_Article();
		$articleLink	 = new Content_Models_ArticleLink();
		$webVideo 		 = new Content_Models_WebVideoArticle();
		$relatedArticle	 = new Content_Models_RelatedArticle();
        $id       = $this->_request->getParam('id');        
	    $enter    = $this->_request->getPost("enter");
		$where    = "articleID = '$id'";
		
		//stare zaznamy aktuality
		//$oldActuality = $aktuality->getOneRow($where);
		
		//pro vlozeni fotek
		$photo           = new Models_Photo();
		$file            = new Models_File();
		$video           = new Models_Video();
		$connectTable	 = new Content_Models_PhotoArticle();
		$vConnectTable	 = new Content_Models_VideoArticle();
		$fConnectTable	 = new Content_Models_FileArticle();
		$folder          = "Article";
		$table           = "photo_article";
		$vTable          = "video_article";
		$fTable          = "file_article";
		$path            = "Public/Images/";
		$vPath           = "Public/Videos/";
		$fPath           = "Public/Files/";
		$tableID         = "articleID";
		$newFolder		 = "Article";
		$tableIDvalue	 = $id;
		// konec vlozeni fotek
		
	    
	    $this->view->action = "/admin/obsah/clanky/upravit/id/".$id;
        
	    //data pred editaci
		$oldData 		= $article->getOneRow($where);
		$oldTopLinkID 	= $articleLink->getTopLinkID($id);	//pro editaci priority nize po updatu
		
		//vybereme vsechny jazykove mutace
		$mainLangData = $language->getMainLang();
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    if($enter){
	    	
	    	$script = new Library_Scripts();	
	    	$filter = $this->setData();    
	    		if( /*empty($this->photos) || $filter->isValid("mainPhoto")*/  true){
		            if($filter->isValid("title-".$mainLangData->suffix) && $filter->isValid("date") && !empty($this->linkID)){
			    	             
		            	if(empty($this->anotation[$mainLangData->suffix]))$this->anotation[$mainLangData->suffix] = "";
		            	if(empty($this->text[$mainLangData->suffix]))$this->text[$mainLangData->suffix] = "";
		            	
		                	$data = array(
					    	        
					    			"title"        => $this->title[$mainLangData->suffix],
					    			"niceTitle"	   => $this->niceTitle[$mainLangData->suffix],
					    	        "text"         => $this->text[$mainLangData->suffix],
		            		        "anotation"    => $this->anotation[$mainLangData->suffix],
		            				"active"	    => $this->showArticle,
		            				"recommended"   => $this->recommended,
		            				"date"	 	    => date("Y-m-d H:i:s",strtotime($this->date)),
		            				"userEdit"	 	=> $this->user,
					    	        "dateEdit"      => date("Y-m-d H:i:s",Time()),
		            				"formTitle" 	=> $this->formTitle[$mainLangData->suffix],
		            				"showForm" 		=> $this->showForm,
		            				"showFacebook"  => $this->showFacebook,
		            				"metaTitle"     => $this->metaTitle[$mainLangData->suffix],
		            				"keywords"      => $this->keywords[$mainLangData->suffix],
		            				"description"   => $this->description[$mainLangData->suffix]
					    			
					    	);
					    	$article->updateData($data,$where);	
                
                $seo = new Seo_Sitemap_Models_Sitemap();
				        $seo->updateData(array("doUpdate" => 1), "seoID = 1");			    	
					    	
					    	//priradime clanku veskere odkazy rekurzivne az ke korenu, pod ktere dany odkaz patri
		            		if(count($this->linkID) > 0){
		            				
	            				$articleLink->deleteData("articleID = '$id'");
	            				$addedLinks = array();
						    	foreach($this->linkID as $li){
						    		if(in_array($li, $addedLinks))continue;
						    		$addedLinks[] = $li;
							    	//priradime clanku veskere odkazy rekurzivne az ke korenu, pod ktere dany odkaz patri
							    	if(!empty($li))$this->generateRelatedLinks($id,$li,$link);
						    	}
					    	}
					    	
					    	//kdyz priradime odkaz jinemu hlavnimu odkazu nez byl, tak aktualizujeme prioritu u techto odkazu jak stareho tak noveho
							$topLinkID 		= $articleLink->getTopLinkID($id);
					    	if($oldTopLinkID != $topLinkID){
							  //uprava priority clanku
							  $allItems = $article->getAllLevelLinkArticles($oldTopLinkID,1,"A.priority,A.articleID DESC");
							  $script->updatePriority($allItems, $article,"articleID");
							  
							  $allItems = $article->getAllLevelLinkArticles($topLinkID,1,"A.priority,A.articleID DESC");
							  $script->updatePriority($allItems, $article,"articleID");
					    	}
					    	
							if($this->modulesData->clankyUrlVideo){
						    	$this->addWebVideos($id);
								$this->removeWebVideos();
							}
							
							//pridani fotek		
							if($this->modulesData->clankyFoto){ 
								$photo->updateMainStatusToZero($id,$table,$tableID); // nastavime vsechny mainPhoto na 0
								$this->updateOldPhoto($id,$table,$tableID);
								$this->addPhoto($id,$table,$connectTable,$tableID,$folder,$newFolder);
							}
	
							//pridani souborů
							if($this->modulesData->clankySoubor){
								$this->updateOldFile($id, $fTable, $tableID);
								$this->addFile($id,$fTable,$fConnectTable,$tableID,$newFolder);
							}
							
							if($this->modulesData->clankyMp4Video){
								//uprava starych videi
								$this->updateOldVideo($id, $vTable, $tableID);
								//pridani videi
								$this->addVideo($id,$vTable,$vConnectTable,$tableID,$newFolder);
							}
							
							//pridani souvisejicich produktu
							if($this->modulesData->clankySouvisejici){
								$this->setRelatedArticles($id);
							}
							
							//pridani do slovniku
							if($this->modulesData->jazykoveMutace){
						    	//vlozeni dat do slovniku
						    	$this->updateDictionary('edit','article','articleID',$id);
							}
					        $this->view->message = "Článek úspěšně upraven";	
					        
		                
			    	}else{
			    		
			    		$this->getBackData();
			    		$this->view->error = "Nevyplnili jste povinné údaje !";
			    		
			    	}
	    		}else{
			    		
			    		$this->getBackData();
			    		$this->view->error = "Nenahráli jste fotku nebo jste neurčili hlavní fotku z již nahraných !";
			    		
			    }
	    	
	        
	    }
	    
	    //nastavime hlavni data
	    $this->setUpdateData($article,$where);
	    
		if($this->modulesData->clankyUrlVideo){
	    	$this->view->allWebVideos	= $webVideo->getVideo($id) ;
		}		
		
		if($this->modulesData->clankyFoto){ 
			$this->setPhotosData($photo,$id,$table,$tableID,$this->adminUserID);			
		}		
		
		if($this->modulesData->clankyMp4Video){
			$this->setVideosData($video,$id,$vTable,$tableID,$this->adminUserID);
		}
		
		if($this->modulesData->clankySoubor){			
			$this->setFilesData($file,$id,$fTable,$tableID,$this->adminUserID);
		}
		
        $this->view->path          								= $path;
	    $this->view->table        		 						= $table;
	    
	    $this->view->tableID = $this->view->vTableID  = $this->view->fTableID = $tableID;
	    $this->view->folder  = $this->view->vFolder   = $this->view->fFolder  = $folder;
	    
	    $this->view->tableIDvalue  								= $tableIDvalue;
	    	
	    $this->view->vPath          							= $vPath;
	    $this->view->vTable         							= $vTable;
	    $this->view->vTableIDvalue  							= $tableIDvalue;

	    $this->view->fPath          							= $fPath;
	    $this->view->fTable         							= $fTable;
	    $this->view->fTableIDvalue  							= $tableIDvalue;
	    
	    //vybereme vsechny clanky pro vybrani souvisejicich predmetu a vsechny vlozene souvisejici
	    if($this->modulesData->clankySouvisejici){
		    $this->view->allRelated 								= $relatedArticle->getAllRelated($id);
		    $this->view->allArticles 								= 	$article->getAllItems();
	    }
	    
	    //sublinky
	    $this->view->sublinks = $link->getAllItems("parentID ='0'","priority");
	    	    
	    
	    //nastavi všechny odkazy
	    $this->setMenuLinks($link,"add");
	    
	    //vybere daný linkID a linkTitle který byl tomuto článku přiřazen
	    $linkData  = $articleLink->getLastLinkData($id);
	    $linkCount = count($linkData);
	    $linkTitleList = array();
	    if($linkCount > 0){
	    	
	    	foreach($linkData as $l){
	    		
	    		$linkTitleList[$l->linkID] = $l->title;	    

	    	}
	    }	
	    $this->view->linkCount     = $linkCount;
	    $this->view->linkTitleList = $linkTitleList;
	    $this->view->linkID        = $linkData;
	    
	    //vybereme vsechny soubory z FTP Public/Files/Article
	    $this->view->allFTPFiles = $this->getFTPFiles("/Public/Files/".$newFolder);
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
		
	function seznamAction()
    {  		
		$this->view->subSelected = "Články";
    	$sortableList             = true;
    	$article                  = new Content_Models_Article();
    	$articleLink              = new Content_Models_ArticleLink();
    	$link                     = new Content_Models_Link();
		$this->view->sortableList = $sortableList;        
		$this->view->action       = '/admin/clanky/seznam';
		$controller               = "article";

		if($this->_request->getPost("deleteButton")){
			$this->deleteArticles();
		}

	    //sublinky
	    $subLink 				 = new Content_Models_Link();
	    $allSublinks 			 = $subLink->getAllItems("parentID = '0'","priority");
	    $this->view->allSublinks = $allSublinks;		    
	    $selectedSublinkID1      = 0;
	    
	    if($this->_request->getPost("vybrat")){
	    	$this->view->subLinkID  = $selectedSublinkID  = $this->_request->getPost("subLinkID");
	    	$this->view->subLinkID1 = $selectedSublinkID1 = $this->_request->getPost("subLinkID-1");
	    	
	    }else{
	    	if(isset($allSublinks[0])){
	    		$selectedSublinkID  = $allSublinks[0]->linkID;
	    		
	    		
	    	}else{
	    		$selectedSublinkID = 0;
	    	}
	    	$this->view->subLinkID  = $selectedSublinkID;
	    	$this->view->subLinkID1 = $selectedSublinkID1;
	    }
	    
	    $items = $article->getAllLevelLinkArticles($selectedSublinkID);
	    if($selectedSublinkID1)$items = $article->getAllLevelLinkArticles($selectedSublinkID1);
	    
	    if($selectedSublinkID == 0){
	    	$items = $article->getNoLinkArticles(1,"A.priority");
	    }
	    
	    $allItems = array();
		foreach($items as $val){
			$item = new stdClass();
			$item->articleID 		= $val->articleID;
			$item->title 			= $val->title;
			$item->view 			= $val->view;
			$item->allowDelete 		= $val->allowDelete;
			$item->topLinkTitle 	= $articleLink->getTopLinkTitle($val->articleID);
			
			$lastLinkTitle 	        = $articleLink->getLastLinkTitle($val->articleID);
			
			$listLink = array();
			foreach($lastLinkTitle as $llt){
				
				$listLink[] = $llt->title;
				
			}
			$item->lastLinkTitle = implode(", ",$listLink);
			$allItems[]				= $item; 
		}
		$this->view->allItems    = $allItems;
		$this->view->controller  = $controller;
							
	    //$this->view->allComments = $this->getComments();
	    
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();

    }
    	
	function deleteArticles()
    {  		
		  
		$article     	  = new Content_Models_Article();
        $photo      	  = new Models_Photo();
		$photoArticle     = new Content_Models_PhotoArticle();		
		$video      	  = new Models_Video();
		$webvideo         = new Models_WebVideo();
		$videoArticle     = new Content_Models_VideoArticle();
		$webvideoArticle  = new Content_Models_WebVideoArticle();
		$file			  = new Models_File();
		$fileArticle	  = new Content_Models_FileArticle();
		$relatedArticle	  = new Content_Models_RelatedArticle();
		$script           = new Library_Scripts();
		$articleLink	  = new Content_Models_ArticleLink();
		//$actualityComment = new ActualityComment();
		
		$table            = "photo_article";
		$vTable           = "video_article";
		$fTable           = "file_article";
		$tableID          = "articleID";
				  
		    $delete = $this->_request->getPost('delete');
		    if(count($delete) > 0){

		    	foreach($delete as $id){
		    		
		    	  $where =   "articleID = '$id' ";
		    	  $result =  $article->getOneRow($where);
		    		
			      $allPhotos = $photo->getAllPhotos($id,$table,$tableID);	         
			      	 		      	 
			      foreach($allPhotos as $value){
			      	 	
			      	 unlink("./Public/Images/Article/".$value->title);
			         unlink("./Public/Images/Article/mala-".$value->title);
			         unlink("./Public/Images/Article/stredni-".$value->title);
			         $wherePhoto = "photoID = '$value->photoID' ";
			         $photo->deleteData($wherePhoto); 
			         
			         //vymazeme ze slovniku pokud je zaply modul
			         if($this->modulesData->jazykoveMutace)
			         	$this->updateDictionary('delete','photo','photoID',$value->photoID);
			      }
			      
			      $allFiles = $file->getAllFiles($id,$fTable,$tableID);
			      foreach($allFiles as $value){
			      	 	
			      	 unlink("./Public/Files/Article/".$value->title);
			         $whereFile = "fileID = '$value->fileID' ";
			         $file->deleteData($whereFile); 
			         
			         //vymazeme ze slovniku pokud je zaply modul
			         if($this->modulesData->jazykoveMutace)
			         	$this->updateDictionary('delete','file','fileID',$value->fileID);
			          
			      }
			      
			      $allVideos = $video->getAllVideos($id, $vTable, $tableID);
			      foreach($allVideos as $value){
			      	 
			      	unlink("./Public/Images/Previews/Article/".$value->file.".png");
			      	unlink("./Public/Images/Previews/Article/mala-".$value->file.".png");
			      	unlink("./Public/Videos/Article/".$value->file.".mp4");
			      	$whereVideo = "videoID = '$value->videoID' ";
			      	$video->deleteData($whereVideo);
			      	
			         //vymazeme ze slovniku pokud je zaply modul
			         if($this->modulesData->jazykoveMutace)
			         	$this->updateDictionary('delete','video','videoID',$value->videoID);
			      
			      }
			      
			      $allLinkVideos = $webvideoArticle->getVideo($id);
			      foreach($allLinkVideos as $value){
			      	 
			      	$whereVideo = "webVideoID = '$value->webVideoID' ";
			      	$webvideo->deleteData($whereVideo);
			      	 
			      }
			      
			      $article        	-> deleteData($where);
			      
				  //uprava priority clanku
				  $topLinkID = $articleLink->getArticleTopLink($id);
				  $allItems = $article->getAllLevelLinkArticles($topLinkID,1,"A.priority,A.articleID DESC");
				  $script->updatePriority($allItems, $article,"articleID");
				  
			      $webvideoArticle  -> deleteData($where);
	              $photoArticle     -> deleteData($where);
	              $fileArticle      -> deleteData($where);
	              $videoArticle     -> deleteData($where); 
	              $relatedArticle	-> deleteData($where);
				  $articleLink		-> deleteData($where);
	              //$actualityComment -> deleteData($where);
	        
                             
	              
	              
	              //smazání dat ze slovniku  
	              if($this->modulesData->jazykoveMutace)        
			        $this->updateDictionary('delete','article','articleID',$id);
				  
			      $this->view->message = "Článek úspěšně smazán";
		    	} 
		    	
		    	$seo = new Seo_Sitemap_Models_Sitemap();
			   	$seo->updateData(array("doUpdate" => 1), "seoID = 1");
		    	
		    }else{
		      	
		          $this->_redirect('/admin/obsah/clanky/seznam');	
		      	
		    }
		  	
								
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
    	  
		
    }
    private function setMenuLinks($link,$typeOutput){    	
	    $mainLinks = $link->getAllItems("parentID = '0'",'priority');
	    $subLinks  = $link->getAllItems("parentID <> '0'",'priority');
	    
	    $this->subLinksArr = array();
	    $this->linksOutput = "";
	    
	    foreach($subLinks as $val){
	    	$this->subLinksArr[$val->parentID][] = $val;
	    }
	    
    	//pouzijeme rekurzi pro vytvoreni odkazu a k nim prislusné pododkazy
	    $this->recurseLinks($mainLinks,1);
	    
	    $this->view->linksOutput = $this->linksOutput;
    }
    
    private function recurseLinks($children,$first = 0){
    	
    	$icon = "";
    	$nested = "";
    	if(!$first){
    		$icon = "<span class='glyphicons glyphicons-chevron-up'></span>";
    		$nested = "nested";
    	}
    	
    	//prochazime postupne od korene a zanorujeme se do childu
    	foreach($children as $child){
    		$this->linksOutput .= "<span class='link $nested'><span class='text $nested'>$icon <span>$child->title</span></span><span class='link-id'>$child->linkID</span>";
    		
    			if(!empty($this->subLinksArr[$child->linkID]))
    				$this->recurseLinks($this->subLinksArr[$child->linkID]);
    			
    		$this->linksOutput .= "</span>";
    	}
    }
    private function setRelatedArticles($id){
    	
		if($this->modulesData->clankySouvisejici){
			
	       	$relatedArticles = new Content_Models_RelatedArticle();
	       	$relatedArticles->deleteData("articleID = $id");
	       	if(!empty($this->related)){
		       	foreach($this->related as $val){
		       		$relatedArticles->insertData(array("articleID" => $id,"relatedArticleID" => $val));
		       	}
	       	}
		}
		
    }
	private function getComments(){
    	
    	$actualityComment 	= new ActualityComment();
    	$allCommentsCount 	= $actualityComment->getAllUnconfirmedCounts();
    	$allCommentsArr		= array();
    	
    	foreach($allCommentsCount as $value){
    		$allCommentsArr[$value->actualityID] = $value->count;
    	}
    	return $allCommentsArr;
    }
        
	private function getData(){
		
		$data = array(
            "date"        => $this->_request->getPost("date"),
		    "mainPhoto"   => $this->_request->getPost("mainPhoto"),
		    "photos"   	  => $this->_request->getPost("photos"),
		    "linkID"        => $this->_request->getPost("linkID"),
		    "toDetailSub" => $this->_request->getPost("toDetailSub"),
			"homepage"    => $this->_request->getPost("homepage"),
			"otherLink"   => $this->_request->getPost("ownLink"),
			"showForm"    => $this->_request->getPost("showForm"),
			"showSubLink" => $this->_request->getPost("showSubLink"),
			"showArticle" => $this->_request->getPost("showArticle"),
			"showFacebook"=> $this->_request->getPost("showFacebook"),
			"recommended" => $this->_request->getPost("recommended"),
			"related" 	  => $this->_request->getPost("related")
        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$data["title-".$val->suffix] 		= $this->_request->getPost("title-".$val->suffix);
				$data["anotation-".$val->suffix] 	= $this->_request->getPost("anotation-".$val->suffix);
				$data["text-".$val->suffix] 		= $this->_request->getPost("text-".$val->suffix);
				$data["formTitle-".$val->suffix] 	= $this->_request->getPost("formTitle-".$val->suffix);
				$data["metaTitle-".$val->suffix] 	= $this->_request->getPost("metaTitle-".$val->suffix);
				$data["keywords-".$val->suffix] 	= $this->_request->getPost("keywords-".$val->suffix);
				$data["description-".$val->suffix] 	= $this->_request->getPost("description-".$val->suffix);
		}
        	    
        
        return $data;
		
	}
	
	private function setData(){
		
		$filters    = $this->setFilters();
	    $validators = $this->setValidators();
	    $data       = $this->getData();
	    $script		= new Library_Scripts();
	    $filter = new Zend_Filter_Input($filters, $validators, $data);

	    $this->date         	= date("Y-m-d H:i:s",strtotime($filter->getUnescaped("date")));
	    $this->mainPhoto    	= $filter->getUnescaped("mainPhoto");
	    $this->photos	    	= $filter->getUnescaped("photos");
	    $this->linkID      	    = $filter->getUnescaped("linkID");
	    $this->toDetailSub		= $filter->getUnescaped("toDetailSub");
		$this->homepage     	= $filter->getUnescaped("homepage");
		$this->otherLink     	= $filter->getUnescaped("otherLink");
		$this->showForm     	= $filter->getUnescaped("showForm");
		$this->showSubLink     	= $filter->getUnescaped("showSubLink");
		$this->showArticle     	= $filter->getUnescaped("showArticle");
		$this->showFacebook     = $filter->getUnescaped("showFacebook");
		$this->recommended     	= $filter->getUnescaped("recommended");
		$this->related     		= $filter->getUnescaped("related");
		
		$this->typePageAct  = $this->_request->getPost("typePageAct");
		
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$this->title[$val->suffix] 			= $filter->getUnescaped("title-".$val->suffix);
				$this->text[$val->suffix] 			= $filter->getUnescaped("text-".$val->suffix);
				$this->anotation[$val->suffix] 		= $filter->getUnescaped("anotation-".$val->suffix);
				$this->keywords[$val->suffix] 		= $filter->getUnescaped("keywords-".$val->suffix);
				$this->description[$val->suffix] 	= $filter->getUnescaped("description-".$val->suffix);
				$this->metaTitle[$val->suffix] 		= $filter->getUnescaped("metaTitle-".$val->suffix);		

				if($val->generateNiceTitle){					
					$this->niceTitle[$val->suffix] 		= $script->url($this->title[$val->suffix]);
				}else{
					$this->niceTitle[$val->suffix] 		= "";
				}
				
				$this->formTitle[$val->suffix] 		= $filter->getUnescaped("formTitle-".$val->suffix);
				if(empty($this->formTitle[$val->suffix]))
					$this->formTitle[$val->suffix] = "";
		}
		
		if(empty($this->recommended))$this->recommended = 0;	
		if(empty($this->showForm))$this->showForm = 0;	  
		if(empty($this->showFacebook))
			$this->showFacebook = 0;	
		
	    return $filter;
	}

    private function setUpdateData($article,$where){
    	
    	//nastavime prekladova data ze slovniku, pokud je zapnut modul jazykobeMutace
		
		//znovunacteme upravene soubory pro preklad 	
		$translatePlugin = new Models_Language_LanguagePlugin();
		$language 		 = new Models_Language_Language();
		
		$mainLangData = $language->getMainLang();
		$translatePlugin->refreshLanguage();			
		
		
		$translate 		 		= Zend_Registry::get('Zend_Translate');
	    $allItems				= new stdClass();
		$allDBItems 			= $article->getOneRow($where);	    
		$allDBItems->date		= date("j.n.Y",strtotime($allDBItems->date));
		    
		if($this->modulesData->jazykoveMutace){			
			
			$articleLangDb			= new  Content_Models_ArticleLang();
			$allTranslates 			= $articleLangDb->getAllItems($where,"lang");
			$allTranslatesArr		= array();
			foreach ($allTranslates as $val){
				$allTranslatesArr[$val->lang] = $val;
			}
			
			//nastavime vsechny jazyky
			//jazyky vzdy prelozime a ulozime do pole
			foreach($this->allLanguageMutations as $val){
				
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->title[$val->suffix] 			= $allTranslatesArr[$val->suffix]->title 			: $allItems->title[$val->suffix] = "";
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->anotation[$val->suffix] 		= $allTranslatesArr[$val->suffix]->anotation 		: $allItems->anotation[$val->suffix] = "";
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->text[$val->suffix] 			= $allTranslatesArr[$val->suffix]->text 			: $allItems->text[$val->suffix] = "";
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->formTitle[$val->suffix] 		= $allTranslatesArr[$val->suffix]->formTitle 		: $allItems->formTitle[$val->suffix] = "";
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->metaTitle[$val->suffix] 		= $allTranslatesArr[$val->suffix]->metaTitle 		: $allItems->metaTitle[$val->suffix] = "";
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->keywords[$val->suffix] 		= $allTranslatesArr[$val->suffix]->keywords 		: $allItems->keywords[$val->suffix] = "";
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->description[$val->suffix] 	= $allTranslatesArr[$val->suffix]->description 		: $allItems->description[$val->suffix] = "";
				
			}
		}else{	
				$allSelectedItems = $article->getOneRow($where);
				$allItems->title[$mainLangData->suffix] 			= $allSelectedItems->title;
				$allItems->anotation[$mainLangData->suffix] 		= $allSelectedItems->anotation;
				$allItems->text[$mainLangData->suffix] 			= $allSelectedItems->text;
				$allItems->formTitle[$mainLangData->suffix] 		= $allSelectedItems->formTitle;
				$allItems->metaTitle[$mainLangData->suffix] 		= $allSelectedItems->metaTitle;
				$allItems->keywords[$mainLangData->suffix] 		= $allSelectedItems->keywords;
				$allItems->description[$mainLangData->suffix] 	= $allSelectedItems->description;
		}
		
		$allDBItems->title 		= $allItems->title;
		$allDBItems->anotation 	= $allItems->anotation;
		$allDBItems->text 		= $allItems->text;
		$allDBItems->formTitle 	= $allItems->formTitle;
		$allDBItems->metaTitle 	= $allItems->metaTitle;
		$allDBItems->keywords 	= $allItems->keywords;
		$allDBItems->description= $allItems->description;
	   	$this->view->allItems = $this->allItems = $allDBItems;
	   	 	
		
	}
	private function setFilters(){
		
		$filters = array();       
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$filters["title-".$val->suffix] 		= 'StripTags';
				$filters["formTitle-".$val->suffix] 	= 'StripTags';				
		}
        
        return $filters;
		
	}
	
	private function setValidators(){
		
		$validators = array(
      	    
      		'date' => array( 				
                'allowEmpty' => false
            ),
            'mainPhoto' => array(  				
                'allowEmpty' => false
            ),
            'photos' => array(  				
                'allowEmpty' => false
            ),
            'linkID' => array(  				
                'allowEmpty' => false
            ),
            'toDetailSub' => array(  				
                'allowEmpty' => true
            ),
            'homepage' => array(  				
                'allowEmpty' => false
            ),
            'otherLink' => array(  				
                'allowEmpty' => true
            ),
            'showForm' => array(  				
                'allowEmpty' => false
            ),
            'showSubLink' => array(  				
                'allowEmpty' => false
            ),
            'showArticle' => array(  				
                'allowEmpty' => false
            ),
            'showFacebook' => array(  				
                'allowEmpty' => false
            ),
            'recommended' => array(  				
                'allowEmpty' => false
            ),
            'related' => array(  				
                'allowEmpty' => true
            )

        );
	
		$language 	  = new Models_Language_Language();
		$mainLangData = $language->getMainLang();
        
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			if($val->suffix != $mainLangData->suffix){
				$validators["title-".$val->suffix]['allowEmpty'] 		= true;
				$validators["anotation-".$val->suffix]['allowEmpty'] 	= true;
				$validators["text-".$val->suffix]['allowEmpty'] 		= true;
			}
			$validators["formTitle-".$val->suffix]['allowEmpty'] 	= true;
			$validators["metaTitle-".$val->suffix]['allowEmpty'] 	= true;
			$validators["keywords-".$val->suffix]['allowEmpty'] 	= true;
			$validators["description-".$val->suffix]['allowEmpty'] 	= true;
		}
		$validators["title-".$mainLangData->suffix]['allowEmpty'] 		= false;
		$validators["anotation-".$mainLangData->suffix]['allowEmpty'] 	= false;
		$validators["text-".$mainLangData->suffix]['allowEmpty'] 		= false;		
	    return $validators;
		
	}
	    
    private function getBackData(){
    	
    		$link = new Content_Models_Link();
    		
    		$this->view->title      			 					= $this->title;
	    	$this->view->text        								= $this->text; 
    	    $this->view->niceTitle   								= $this->niceTitle;
	    	$this->view->date        								= date("d.m.Y H:i:s",strtotime($this->date));
	    	$this->view->anotation   								= $this->anotation;
	    	if(isset($_COOKIE["mainPhoto"]))$this->view->mainPhoto	= $_COOKIE["mainPhoto"];	
	    	$this->view->linkID   									= $this->linkID;
	    	$this->view->toDetailSub   							    = $this->toDetailSub;
	    	$this->view->otherLink   							    = $this->otherLink;
	    	$this->view->formTitle   							    = $this->formTitle;
	    	$this->view->showForm   							    = $this->showForm;
	    	$this->view->show   							    	= $this->showSubLink;
	    	$this->view->showFacebook   							= $this->showFacebook;
	    	$this->view->active   							    	= $this->showArticle;
	    	$this->view->recommended						    	= $this->recommended;
	    	$this->view->metaTitle   							    = $this->metaTitle;
	    	$this->view->keywords   							    = $this->keywords;
	    	$this->view->description   							    = $this->description;
	    	
	    	$this->view->linkCount = count($this->linkID);
	    	$linkTitleList = array();
	    	foreach($this->linkID as $l){
		    	$linkTitle = $link->getOneRow("linkID = $l");
		    	if(!empty($linkTitle->title))
		    		$linkTitleList[$l] = $linkTitle->title;
		    	else $linkTitleList[$l] = "";
	    	} 	
	    	$this->view->linkTitleList = $linkTitleList;
    	    	
	    	//$this->jscripts($this->sublink,$this->link);
	    	
	    	$hidvideo = $this->_request->getPost("hidvideo");
	    	 
	    	if($hidvideo){
	    		$list = array();
	    		for($i = 1;$i <= $hidvideo;$i++){
	    			 
	    			$videoURL  = $this->_request->getPost("videoURL-$i");
	    			if(!empty($videoURL)){
	    	
	    				$list[] = $videoURL;
	    					
	    			}
	    		}
	    		 
	    		$this->view->backWebVideos = $list;
	    	}
	    	
	    	//nastavime vsechny souvisejici clanky(produkty)
	    	if($this->modulesData->clankySouvisejici){
	    		if(!empty($this->related)){
			    	$Article = new Content_Models_Article();
			    	$ids = "";
			    	$j=0;
			    	foreach($this->related as $val){
			    		if($j == 0) $ids .= $val;
			    		else		$ids .= ",".$val;
			    		$j++;
			    	}
			    	$this->view->related = $Article->getAllItems("articleID IN ($ids)");
	    		}
	    	}
    	
    }
    
    private function jscripts($sublinkID,$linkID){
    	
    	echo'
    	    <script>
    		    getSubLinkEdit("'.$sublinkID.'","'.$linkID.'");		
    		</script>
    	';
    	
    }

		
	private function addWebVideos($id){
		
		$video = new Models_WebVideo();
		$videoA = new Content_Models_WebVideoArticle();
	
    	$hidvideo = $this->_request->getPost("hidvideo");
    	
    	if($hidvideo){
	    	for($i = 1;$i <= $hidvideo;$i++){
	    		
	    		$videoURL  = $this->_request->getPost("videoURL-$i");
	    		if(!empty($videoURL)){
	    			
	    				$videoData = $this->getVideoTypeCode($videoURL);
	    				
	    				if($videoData[0] && $videoData[1]){
				    		$data = array(
				    			"type"    	=> $videoData[0],
				    			"code" 		=> $videoData[1],
				    			"userAdd"  	=> $this->user,
				    			"dateAdd" 	=> date("Y-m-d H:i:s")
				    		);
				    		$video->insertData($data);
				    		$lastID = $video->lastID;
				    		
				    		$dataL = array(
				    		    "articleID"  => $id,
				    			"webVideoID" => $lastID
				    			
				    		);
				    		$videoA->insertData($dataL);
	    				}else{
	    					$this->view->error = "Odkazy na youtube či vimeo nebyly správně vloženy!";
	    				}

	    		}
	    	}
    	}
	}	
	private function removeWebVideos(){
		
		$video  = new Models_WebVideo();
		$videoA = new Content_Models_WebVideoArticle();
	
    	$delVideo = $this->_request->getPost("delVideo");
    	
    	if($delVideo){
	    	foreach($delVideo as $val){

	    		$where = "webVideoID = $val";
	    		$video->deleteData($where);
	    		$videoA->deleteData($where);
	    	}
    	}
	}
    			    
    private function addSubSubLinks($id,$subsubLink,$script,$link){
    	
    	$hidsub = $this->_request->getPost("hidsub");
    	
    	if($hidsub){
	    	for($i = 1;$i <= $hidsub;$i++){
	    		
	    		$subtitles  = $this->_request->getPost("subtitle-$i");
	    		$toDetail  = $this->_request->getPost("toDetail-$i");
	    		$diffLink  = $this->_request->getPost("ownLink-$i");
	    		$showSubLink = $this->_request->getPost("showSubLink-$i");
	    		if(empty($toDetail))$toDetail = 0;
	    		if($subtitles){
	    			
		    		
		    			$niceTitle = $script->url($subtitles);
		    				
		    			$data = array(
		    			    "subLinkID" => $id,
		    				"title"     => $subtitles,
		    				"niceTitle" => $niceTitle,
		    				"toDetail"  => $toDetail,
		    				"otherLink"  => $diffLink,
		    				"link"      => $link."/".$niceTitle,
    						"active"		=> $showSubLink
		    			);
		    			$subsubLink->insertData($data);
		    		
	    		
	    		}
	    	
	    	}
    	}
    	
    }
    
    private function updateSubSubLinks($id,$subSublink,$script,$link){
    	
    	$hidsub = $this->_request->getPost("hidsub");
    	 
    	if($hidsub){
    		for($i = 1;$i <= $hidsub;$i++){
    			 
    			$subtitles   = $this->_request->getPost("subtitle-$i");
    			$subtitlesID = $this->_request->getPost("subtitle-id-$i");
    			$toDetail    = $this->_request->getPost("toDetail-$i");
	    		$diffLink    = $this->_request->getPost("ownLink-$i");
	    		$showSubLink = $this->_request->getPost("showSubLink-$i");
    			
    			$where = "subSubLinkID = '$subtitlesID'";
    			
    			if($subtitles){
    	
    				$niceTitle = $script->url($subtitles);
    	
	    			if(empty($toDetail))$toDetail = 0;
    				$data = array(
    					"subLinkID" => $id,
    			    	"title"     => $subtitles,
    			    	"niceTitle" => $niceTitle,
    					"toDetail"  => $toDetail,
		    			"otherLink"  => $diffLink,
    			    	"link"      => $link."/".$niceTitle,
    					"active"		=> $showSubLink
    				);
    				
    				if(!empty($subtitlesID)){
    					
    					$subSublink->updateData($data,$where);
    				}else{    					
    					$subSublink->insertData($data);
    				}
    	   		   
    			}else{
    				
    				$subSublink->deleteData($where);
    				
    			}
    	
    		}
    	}
    	
    }

    function orderA($descAsc,$controller,$table,$totaly,$where = null,$order = "date DESC",$action = "seznam"){
    		
    	$onPage = 20;
    		
    	if(!$this->_request->getPost('order')){
    
    		$path                  = "/admin/".$controller."/".$action ;
    		$page                  = $this->_request->getParam('strana');
    
    		$allItems              = $this->setAPagination($page,$path,$totaly,$table,$onPage,$order,$where);
    		$this->view->allItems  = $allItems;
    
    	}
    	if($this->_request->getPost('order') || !empty($descAsc)){
    
    		$orderBy  = $this->_request->getPost('orderBy');
    		$descAsc  = $this->_request->getPost('descAsc');
    		$subLink  = $this->_request->getParam('sublink');
    		
    		if($subLink)$where = "SL.subLinkID = '$subLink'";
    		else $where = 1;
    		
    		if($this->_request->getParam('poradi')){
    			$orderBy            = $this->_request->getParam('seradit');
    			$descAsc            = $this->_request->getParam('poradi');
    			$subLink            = $this->_request->getParam('sublink');
    		}
    
    		$this->view->orderBy = $orderBy;
    		$this->view->descAsc = $descAsc;
    		$this->view->subLink = $subLink;
    
    		$path                  = "/admin/".$controller."/seznam/seradit/".$orderBy."/sublink/".$subLink."/poradi/".$descAsc ;
    		$page                  = $this->_request->getParam('strana');
    		$order				   = $orderBy." ".$descAsc;
    
    		$allItems              = $this->setAPagination($page,$path,$totaly,$table,$onPage,$order,$where);
    		$this->view->allItems  = $allItems;
    
    	}
    		
    }
    
        
    public function setAPagination($page,$path,$totaly,$table,$onPage,$order,$where){
    
    	if (!isset($page)){
    		$page=1; //pokud nen� $zobrazena_strana nastavena, nastav�me ji na hodnotu 1
    	}
    
    	$pagesNumber = ceil($totaly/$onPage);
    	$limit1=(($page-1)*$onPage);
    	$limit2=($onPage);
    	$archiv = $table->getArticles($where,$order,$limit2,$limit1);
    	$this->nextPrevious($page, $pagesNumber, $path);
    
    	return $archiv;
    
    }
}

?>