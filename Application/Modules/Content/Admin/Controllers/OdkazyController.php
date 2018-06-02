<?php
class Content_OdkazyController extends Content_Library_WholeContent
{
	
	protected $title;
	protected $text;
	private $mainPhoto;
	private $photos;
	private $link;
	private $showArticles;
	protected $showFacebook;
	protected $otherLink;
	private $active;
	private $parent;
	private $linksOutput;
	private $subLinksArr;
	protected $metaTitle;
	protected $oldUrl;
	
	protected $allLanguageMutations;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();	
    }
    function indexAction()
	{
		$this->_redirect("/admin/obsah/odkazy/pridat");		
	}

	function pridatAction()
	{
		$this->view->subSelected = "Přidat odkaz";
		$link			    = new Content_Models_Link();
		$linkFactory		= new Content_Models_LinkFactory($this->_request, $this->_response);
		$script				= new Library_Scripts();
		$this->view->date	= date("j.n.Y",Time());
	  	
	    //pro vlozeni fotek a videi. Videa mají­ předponu "v"
	    $language 		 = new Models_Language_Language();
		$photo           = new Models_Photo();
		$file            = new Models_File();
		$video           = new Models_Video();
		$connectTable	 = new Content_Models_PhotoLink();
		$vConnectTable	 = new Content_Models_VideoLink();
		$fConnectTable   = new Content_Models_FileLink();
		$folder          = "Temp";
		$table           = "photo_link";
		$vTable          = "video_link";
		$fTable          = "file_link";
		$path            = "Public/Images/";
		$vPath           = "Public/Videos/";
		$fPath           = "Public/Files/";
		$tableID         = "linkID";
		$newFolder		 = "Link";
		// konec vlozeni fotek a videi
		
		//vybereme vsechny jazykove mutace
		$mainLangData = $language->getMainLang();
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/obsah/odkazy/pridat";
        $this->view->homepage = 0;
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$filter = $this->setData();

	            if($filter->isValid("title-".$mainLangData->suffix)){
	            	           	

	            		$urlLink = "/".$this->niceTitle[$mainLangData->suffix];
	            		$linkRow = $link->getOneRow("linkID = '$this->parent'");
	            		if($linkRow && $this->parent != 0)$urlLink = "/".$linkRow->niceTitle."/".$this->niceTitle[$mainLangData->suffix];
	            		
	            		$data = array(
				    	        
	            			"parentID"      => $this->parent,
				    		"title"         => $this->title[$mainLangData->suffix],
				    		"niceTitle"	    => $this->niceTitle[$mainLangData->suffix],
				    	    "text"          => "",
	            			"active"	    => $this->active,
	            			"view"		    => 0,
	            			"date"	 		=> date("Y-m-d H:i:s",time()),
	            			"dateEdit"	    => "0000-00-00 00:00:00",
	            			"userAdd"	    => $this->user,
		            		"showFacebook" => $this->showFacebook,
	            			"otherLink"     => $this->otherLink[$mainLangData->suffix],
	            			"priority"      => 1,
	            		    "noDelete"      => 0,
	            			"inMenu"        => $this->inMenu,
	            		    "inFooter"      => $this->inFooter,
	            			"metaTitle"     => $this->metaTitle[$mainLangData->suffix],
	            			"keywords"      => $this->keywords[$mainLangData->suffix],
	            			"description"   => $this->description[$mainLangData->suffix],
	            			"oldUrl"   		=> $this->oldUrl[$mainLangData->suffix]
				    			
				    	);
				    	
				    	$link->insertData($data);
				    	$id = $link->lastID;
				    	
				    	$seo = new Seo_Sitemap_Models_Sitemap();
			   			$seo->updateData(array("doUpdate" => 1), "seoID = 1");
				    	
				    	
						if($this->modulesData->odkazyUrlVideo){
				    		$this->addLinkVideos($id);
						}
						
						//pridani fotek
						if($this->modulesData->odkazyFoto){
							$this->addPhoto($id,$table,$connectTable,$tableID,$folder,$newFolder);					
				    		setcookie("mainPhoto","",NULL,"/"); 
						}
						
			    		//pridani souborů
						if($this->modulesData->odkazySoubor){
			    			$this->addFile($id,$fTable,$fConnectTable,$tableID,$newFolder);
						}
			    		
			    		//pridani videi
						if($this->modulesData->odkazyMp4Video){
							$this->addVideo($id,$vTable,$vConnectTable,$tableID,$newFolder);
						}
						
						// pridani sekci
						$linkFactory->allLanguageMutations = $this->allLanguageMutations;
						$linkFactory->langModule           = $this->modulesData->jazykoveMutace;
						$linkFactory->addSections($id);								    	
			    						    	
						$allItems = $link->getAllItems(null, array("priority","linkID DESC"));
						$script->updatePriority($allItems, $link, "linkID");
	            
						//pridani do slovniku
						if($this->modulesData->jazykoveMutace){

							//zde povolime pridani ve slovniku vzdy
							$this->allowEditOtherLink = true;
					    	//vlozeni dat do slovniku
					    	$this->updateDictionary('add',"link","linkID",$id);
						}
				        $this->view->message = "Odkaz úspěšně přidán";	
	            	
	            	
		    	}else{
		    		
		    		$this->getBackData();
		    		$this->view->error = "Nevyplnili jste povinné údaje";
		    		
		    	}
	    	
	        
	    }
	    
	    //nastavi všechny odkazy
	    $this->setMenuLinks($link,"add");
	    
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
	    //vybereme vsechny soubory z FTP Public/Files/Article
	    $this->view->allFTPFiles = $this->getFTPFiles("/Public/Files/".$newFolder);	
	    	    
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}
		
	function upravitAction()
	{
	
		$link	  = new Content_Models_Link();
		$category = new Content_Models_Category();
		$webVideo = new Content_Models_WebVideoLink();
        $id       = $this->_request->getParam('id');        
	    $enter    = $this->_request->getPost("enter");
		$where    = "linkID = '$id'";
		
		$this->view->elSecCount = array();
				
		//pro vlozeni fotek
	    $language 		 = new Models_Language_Language();
		$photo           = new Models_Photo();
		$file            = new Models_File();
		$video           = new Models_Video();
		$connectTable	 = new Content_Models_PhotoLink();
		$vConnectTable	 = new Content_Models_VideoLink();
		$fConnectTable	 = new Content_Models_FileLink();
		$folder          = "Link";
		$table           = "photo_link";
		$vTable          = "video_link";
		$fTable          = "file_link";
		$path            = "Public/Images/";
		$vPath           = "Public/Videos/";
		$fPath           = "Public/Files/";
		$tableID         = "linkID";
		$newFolder		 = "Link";
		$tableIDvalue	 = $id;
		// konec vlozeni fotek
		
	    
	    $this->view->action = "/admin/obsah/odkazy/upravit/id/".$id;
	    
		$oldData = $link->getOneRow($where);
		$this->allowEditOtherLink = $oldData->allowEditOtherLink;

		//vybereme vsechny jazykove mutace
		$mainLangData = $language->getMainLang();
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
        
		
	    if($enter){
	    	
	    	$script = new Library_Scripts();	
	    	$filter = $this->setData();    
	    	
	    	if($filter->isValid("title-".$mainLangData->suffix)){
	            	           	
	            				    	
	                	$urlLink = "/".$this->niceTitle[$mainLangData->suffix];
	            		$linkRow = $link->getOneRow("linkID = '$this->parent'");
	            		if($linkRow && $this->parent != 0)$urlLink = "/".$linkRow->niceTitle."/".$this->niceTitle[$mainLangData->suffix];
	            		
	            		$data = array(
				    	        
	            			"parentID"      => $this->parent,
				    		"title"         => $this->title[$mainLangData->suffix],
				    		"niceTitle"	    => $this->niceTitle[$mainLangData->suffix],
				    	    "text"          => "",
	            			"active"	    => $this->active,
	            			"dateEdit"	    => date("Y-m-d H:i:s",time()),
	            			"userEdit"	    => $this->user,
		            		"showFacebook"  => $this->showFacebook,
	            			"inMenu"        => $this->inMenu,
	            		    "inFooter"      => $this->inFooter,
	            			"metaTitle"      => $this->metaTitle[$mainLangData->suffix],
	            			"keywords"      => $this->keywords[$mainLangData->suffix],
	            			"description"   => $this->description[$mainLangData->suffix],
	            			"oldUrl"   		=> $this->oldUrl[$mainLangData->suffix]
				    			
				    	);
				    	
				    	if($this->allowEditOtherLink)
				    		$data["otherLink"] = $this->otherLink[$mainLangData->suffix];
				    	$link->updateData($data,$where);
              
              $seo = new Seo_Sitemap_Models_Sitemap();
			   	$seo->updateData(array("doUpdate" => 1), "seoID = 1");				    	
				    	
						if($this->modulesData->odkazyUrlVideo){
							$this->addLinkVideos($id);
							$this->removeLinkVideos();
						}
						
						//pridani fotek		
						if($this->modulesData->odkazyFoto){						 
							$photo->updateMainStatusToZero($id,$table,$tableID); // nastavime vsechny mainPhoto na 0
							$this->updateOldPhoto($id,$table,$tableID);
							$this->addPhoto($id,$table,$connectTable,$tableID,$folder,$newFolder);
						}
						
						//pridani souborů
						if($this->modulesData->odkazySoubor){
							$this->updateOldFile($id, $fTable, $tableID);
							$this->addFile($id,$fTable,$fConnectTable,$tableID,$newFolder);
						}
						
				    	//vlozeni dat do slovniku
					    //$this->updateDictionary('delete',$oldActuality);
				    	//$this->updateDictionary('add');
	
						
						//uprava starych videi a pridani novych
						if($this->modulesData->odkazyMp4Video){							
							//uprava starych videi
							$this->updateOldVideo($id, $vTable, $tableID);
							//pridani videi
							$this->addVideo($id,$vTable,$vConnectTable,$tableID,$newFolder);
						}
	    	
						//pridani do slovniku
						if($this->modulesData->jazykoveMutace){
					    	$this->updateDictionary('edit',"link","linkID",$id);
						}
						
						// uprava sekcí sekci
						$linkFactory		               = new Content_Models_LinkFactory($this->_request, $this->_response);
						$linkFactory->allLanguageMutations = $this->allLanguageMutations;
						$linkFactory->langModule           = $this->modulesData->jazykoveMutace;
						
						$linkFactory->deleteAllSections($id,false);
						$linkFactory->addSections($id);
						
						if($oldData->parentID != $this->parent)
							$this->updateArticleAssignments($id);		
				        $this->view->message = "Odkaz úspěšně upraven";	
				        
	               
		    	}else{
		    		
		    		$this->getBackData();
		    		$this->view->error = "Nevyplnili jste povinné údaje";
		    		
		    	}
	    	
	        
	    }
	     
	    //nastavime hlavni data
	    $this->setUpdateData($link,$where);
	                   
	    
		if($this->modulesData->odkazyUrlVideo){
		    $this->view->allWebVideos	= $webVideo->getVideo($id) ;
		}			
		
		if($this->modulesData->odkazyFoto){	
			$this->setPhotosData($photo,$id,$table,$tableID,$this->adminUserID);
		}
		
		if($this->modulesData->odkazyMp4Video){	
			$this->setVideosData($video,$id,$vTable,$tableID,$this->adminUserID);
		}
		
		if($this->modulesData->odkazySoubor){
			$this->setFilesData($file,$id,$fTable,$tableID,$this->adminUserID);
		}
		
		//vypíše sekce
		$linkFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
		$linkFactory->allLanguageMutations = $this->allLanguageMutations;
		$linkFactory->langModule           = $this->modulesData->jazykoveMutace;
		$linkFactory->getSections($id);
		
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
	    
	    
	    //nastavi všechny odkazy
	    $this->setMenuLinks($link,"add");
	    
	    //nastavime nazev nadrazeneho odkazu pokud existuje
	    if($this->allItems->parentID != 0){
	    	$parentData = $link->getOneRow("linkID = ".$this->allItems->parentID);
	    	$this->view->parentTitle = $parentData->title;
	    }
	    
	    //vybereme vsechny soubory z FTP Public/Files/Article
	    $this->view->allFTPFiles = $this->getFTPFiles("/Public/Files/".$newFolder);	
	    
	    $this->view->allCategories = $category->getAllItems(null, "priority");
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
		
	function seznamAction()
    {  
    			
		$this->view->subSelected  = "Odkazy";
    	$link                  	  = new Content_Models_Link();
		$this->view->sortableList = true;        
		$this->view->action       = '/admin/obsah/odkazy/seznam';
		
		if($this->_request->getPost("deleteButton")){
				
			$toDelete = $this->_request->getPost("delete");
			if(!empty($toDelete)){
		
				$linkFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
		
				foreach($toDelete as $del){
					$linkFactory->deleteLinkData($del);
				}
				
				$seo = new Seo_Sitemap_Models_Sitemap();
			   	$seo->updateData(array("doUpdate" => 1), "seoID = 1");

				$this->view->message = "Vybrané odazy byly úspěšně smazány.";
					
			}
		}
		
	    $this->setMenuLinks($link,"list");		
		$this->view->controller   = "link";
		

	    //vlozime placeholdery
	    $this->addPlaceholders();

    }
    
    private function updateArticleAssignments($linkID){
    	$articleLink 			= new Content_Models_ArticleLink();
    	$link 					= new Content_Models_Link();
    	
    	//nejprve vybereme vsechny clanky ktere jsou pripojeny k editovanemu odkazu
    	$allAffectedArticles	= $articleLink->getAllItems("linkID = $linkID");
    	
    	//projdeme vsechny vybrane clanky
    	foreach($allAffectedArticles as $article){
			//vybereme posledni (nejnize polozeny) odkaz ke kteremu je pripojen dany clanek    		
    		$lastArticleLink = $articleLink->getOneRow("articleID = $article->articleID AND isLastLink = 1");
    		//smazene veskere udaje z article_link tykajici se tohoto clanku
    		$articleLink->deleteData("articleID = $article->articleID");
    		//nechame vygenerovat nove zaznamy pro dany clanek
    		$this->generateRelatedLinks($article->articleID,$lastArticleLink->linkID,$link);
    		
    	}
    }
    private function setMenuLinks($link,$typeOutput){    	
	    $mainLinks = $link->getAllItems("parentID = '0' AND isEshopCategory = 0",'priority');
	    $subLinks  = $link->getAllItems("parentID <> '0' AND isEshopCategory = 0",'priority');
	    
	    $this->subLinksArr = array();
	    $this->linksOutput = "";
	    
	    foreach($subLinks as $val){
	    	$this->subLinksArr[$val->parentID][] = $val;
	    }
	    
    	//pouzijeme rekurzi pro vytvoreni odkazu a k nim prislusné pododkazy
	    if($typeOutput == "add"){
	    	$this->recurseLinks($mainLinks,1);
	    }else if($typeOutput == "list"){
	    	if(!empty($mainLinks[0]))
	    		$this->recurseLinksForList($mainLinks,0);	    	
	    	else
	    		$this->linksOutput = '<div class="no-data text-center">Zatím se zde nenachází žádné odkazy.</div>';
	    }
	    
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
    
    private function recurseLinksForList($children,$recurseLevel){
    	//prochazime postupne od korene a zanorujeme se do childu
    	
        $ulMargin = $recurseLevel*15;
        
        $this->linksOutput .= '<ul class="sortListBasicLinks main-ul data-list list-unstyled col-12">';
        
        foreach($children as $child){
            $this->linksOutput .= '<li id="'.$child->linkID.'" class="clearfix row" >';
            $this->linksOutput .= '     <div class="col-sm-3" style="padding-left:'.$ulMargin.'px;"> <span class="glyphicons glyphicons-move"></span> '.$child->title.'</div>';
            $this->linksOutput .= '		<div class="col-sm-3">'.date("d.m.Y H:i:s",strtotime($child->date)).'</div>';
            $this->linksOutput .= '		<div class="text-center col-sm-3">'.$child->view.'</div>';
            $this->linksOutput .= '		<div class="text-right col-sm-3">';
            $this->linksOutput .= '		    <a title="Uprav" href="'.$this->baseUrl.'/admin/obsah/odkazy/upravit/id/'.$child->linkID.'"><span class="glyphicons glyphicons-pencil"></span></a> ';
            if($child->allowDelete){
                $this->linksOutput .= '		<input class="delete" type="checkbox" name="delete[]" value="'.$child->linkID.'">';
            }
            $this->linksOutput .= '		</div><div class="w-100"></div>';
            
            if(!empty($this->subLinksArr[$child->linkID]))
                $this->recurseLinksForList($this->subLinksArr[$child->linkID],$recurseLevel+1);
                
                $this->linksOutput .= "</li>";
        }
        
        $this->linksOutput .= '</ul>';
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
			"parent"      => $this->_request->getPost("parent"),
		    "mainPhoto"   => $this->_request->getPost("mainPhoto"),
		    "photos"   	  => $this->_request->getPost("photos"),
			"link"        => $this->_request->getPost("link"),	
			"showFacebook"=> $this->_request->getPost("showFacebook"),	 	    
			"active"      => $this->_request->getPost("active"),
			"inMenu"      => $this->_request->getPost("inMenu"),
			"inFooter"    => $this->_request->getPost("inFooter")
        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$data["title-".$val->suffix] 		= $this->_request->getPost("title-".$val->suffix);
				$data["text-".$val->suffix] 		= $this->_request->getPost("text-".$val->suffix);
				$data["metaTitle-".$val->suffix] 	= $this->_request->getPost("metaTitle-".$val->suffix);
				$data["keywords-".$val->suffix] 	= $this->_request->getPost("keywords-".$val->suffix);
				$data["description-".$val->suffix] 	= $this->_request->getPost("description-".$val->suffix);
				$data["otherLink-".$val->suffix] 	= $this->_request->getPost("otherLink-".$val->suffix);
				$data["oldUrl-".$val->suffix] 		= $this->_request->getPost("oldUrl-".$val->suffix);
		}    
        
        return $data;
		
	}
	
	private function setData(){
		
		$filters    = $this->setFilters();
	    $validators = $this->setValidators();
	    $data       = $this->getData();
	    $script		= new Library_Scripts();
	    $filter = new Zend_Filter_Input($filters, $validators, $data);

	    $this->parent       = $filter->getUnescaped("parent");
	    $this->mainPhoto    = $filter->getUnescaped("mainPhoto");
	    $this->photos	    = $filter->getUnescaped("photos");
	    $this->link         = $filter->getUnescaped("link");
	    $this->showFacebook = $filter->getUnescaped("showFacebook");
		$this->active       = $filter->getUnescaped("active");
		$this->inMenu       = $filter->getUnescaped("inMenu");	
		$this->inFooter     = $this->_request->getPost("inFooter");
		$this->inFooter     = $this->_request->getPost("inFooter");
		$this->linkType     = $this->_request->getPost("link-type");
		
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$this->title[$val->suffix] 			= $filter->getUnescaped("title-".$val->suffix);
				$this->text[$val->suffix] 			= $filter->getUnescaped("text-".$val->suffix);
				$this->keywords[$val->suffix] 		= $filter->getUnescaped("keywords-".$val->suffix);
				$this->description[$val->suffix] 	= $filter->getUnescaped("description-".$val->suffix);
				$this->metaTitle[$val->suffix] 		= $filter->getUnescaped("metaTitle-".$val->suffix);	
				$this->otherLink[$val->suffix] 		= $filter->getUnescaped("otherLink-".$val->suffix);		
				$this->oldUrl[$val->suffix] 		= $filter->getUnescaped("oldUrl-".$val->suffix);		

				if($val->generateNiceTitle){
					$this->niceTitle[$val->suffix] 		= $script->url($this->title[$val->suffix]);
				}else{
					$this->niceTitle[$val->suffix] 		= "";
				}
		}
		
		if($this->linkType == 0 && is_numeric($this->linkType)){			
	    		$this->parent = 0;
		}
		if(empty($this->showFacebook))
			$this->showFacebook = 0;
	    return $filter;
	}

    private function setUpdateData($link,$where){
    			
	    $allItems				= new stdClass();
		$allDBItems 			= $link->getOneRow($where);	    
		$allDBItems->date		= date("j.n.Y",strtotime($allDBItems->date));
		
		$language 	  = new Models_Language_Language();
		$mainLangData = $language->getMainLang();
		
		if($this->modulesData->jazykoveMutace){		

			$linkLangDb				= new  Content_Models_LinkLang();
			$allTranslates 			= $linkLangDb->getAllItems($where,"lang");
			$allTranslatesArr		= array();
			foreach ($allTranslates as $val){
				$allTranslatesArr[$val->lang] = $val;
			}
			
			//nastavime vsechny jazyky
			//jazyky vzdy prelozime a ulozime do pole
			foreach($this->allLanguageMutations as $val){
				
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->title[$val->suffix] 		= $allTranslatesArr[$val->suffix]->title 		: $allItems->title[$val->suffix] = "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->text[$val->suffix] 		= $allTranslatesArr[$val->suffix]->text 		: $allItems->text[$val->suffix] = "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->metaTitle[$val->suffix] 	= $allTranslatesArr[$val->suffix]->metaTitle 	: $allItems->metaTitle[$val->suffix] = "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->keywords[$val->suffix] 	= $allTranslatesArr[$val->suffix]->keywords 	: $allItems->keywords[$val->suffix] = "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->description[$val->suffix]= $allTranslatesArr[$val->suffix]->description	: $allItems->description[$val->suffix] = "";
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->otherLink[$val->suffix] 	= $allTranslatesArr[$val->suffix]->otherLink 	: $allItems->otherLink[$val->suffix] = "";
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->oldUrl[$val->suffix] 	= $allTranslatesArr[$val->suffix]->oldUrl 		: $allItems->oldUrl[$val->suffix] = "";
				
			}
		}else{	
				$allSelectedItems = $link->getOneRow($where);
				$allItems->title[$mainLangData->suffix] 			= $allSelectedItems->title;
				$allItems->text[$mainLangData->suffix] 			= $allSelectedItems->text;
				$allItems->metaTitle[$mainLangData->suffix] 		= $allSelectedItems->metaTitle;
				$allItems->keywords[$mainLangData->suffix] 		= $allSelectedItems->keywords;
				$allItems->description[$mainLangData->suffix] 	= $allSelectedItems->description;
				$allItems->otherLink[$mainLangData->suffix] 		= $allSelectedItems->otherLink;
				$allItems->oldUrl[$mainLangData->suffix] 		= $allSelectedItems->oldUrl;
		}
		
		$allDBItems->title 		= $allItems->title;
		$allDBItems->text 		= $allItems->text;
		$allDBItems->metaTitle 	= $allItems->metaTitle;
		$allDBItems->keywords 	= $allItems->keywords;
		$allDBItems->description= $allItems->description;
		$allDBItems->otherLink	= $allItems->otherLink;
		$allDBItems->oldUrl		= $allItems->oldUrl;
		
	   	$this->view->allItems 	= $this->allItems = $allDBItems;
	   	 	
		
	}
	private function setFilters(){
		
		$filters = array(
            'title'  	  => 'StripTags',		
			'link'	      => 'StripTags',
			'otherLink'   => 'StripTags',	
			'keywords'    => 'StripTags',
			'description' => 'StripTags',
			'metaTitle'   => 'StripTags'
        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$validators["otherLink-".$val->suffix] 	= 'StripTags';
		}
		
        return $filters;
		
	}
	
	private function setValidators(){
		
		$validators = array(
      	    
            'mainPhoto' => array(  				
                'allowEmpty' => true
            ),
            'photos' => array(  				
                'allowEmpty' => true
            ),
            'link' => array(  				
                'allowEmpty' => true
            ),
            'showArticles' => array(  				
                'allowEmpty' => true
            ),
            'showFacebook' => array(  				
                'allowEmpty' => true
            ),
            'inMenu' => array(  				
                'allowEmpty' => false
            ),
            'inFooter' => array(  				
                'allowEmpty' => false
            ),
            'active' => array(  				
                'allowEmpty' => false
            ),
            'parent' => array(  				
                'allowEmpty' => true
            )

        );
		
		$language 	  = new Models_Language_Language();
		$mainLangData = $language->getMainLang();
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			if($val->suffix != $mainLangData->suffix){
				$validators["title-".$val->suffix]['allowEmpty'] 		= true;
			}
				$validators["text-".$val->suffix]['allowEmpty'] 		= true;
				$validators["metaTitle-".$val->suffix]['allowEmpty'] 	= true;
				$validators["keywords-".$val->suffix]['allowEmpty'] 	= true;
				$validators["description-".$val->suffix]['allowEmpty'] 	= true;
				$validators["otherLink-".$val->suffix]['allowEmpty'] 	= true;
				$validators["oldUrl-".$val->suffix]['allowEmpty'] 		= true;
		}
		
		$validators["title-".$mainLangData->suffix]['allowEmpty'] 		= false;
        return $validators;
		
	}
	    
    private function getBackData(){
    	
    		$link = new Content_Models_Link();
    	
    		$this->view->title      			 					= $this->title;
	    	$this->view->text        								= $this->text; 
	    	if(isset($_COOKIE["mainPhoto"]))$this->view->mainPhoto	= $_COOKIE["mainPhoto"];	
	    	$this->view->link   									= $this->link;
	    	$this->view->showArticles   							= $this->showArticles;
	    	$this->view->showFacebook   							= $this->showFacebook;
	    	$this->view->otherLink   							    = $this->otherLink;
	    	$this->view->active   							        = $this->active;
	    	$this->view->inMenu   							        = $this->inMenu;
	    	$this->view->inFooter   							    = $this->inFooter;
	    	$this->view->linkType   							    = $this->linkType;
	    	$this->view->parent   							        = $this->parent;
	    	$this->view->keywords   							    = $this->keywords;
	    	$this->view->description   							    = $this->description;
	    	$this->view->metaTitle   							    = $this->metaTitle;

	    	$parentTitle = $link->getOneRow("linkID = $this->parent");
	    	if(!empty($parentTitle->title))
	    		$this->view->parentTitle								= $parentTitle->title;
	    	
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

    }
    
	private function addLinkVideos($id){
		
		$video = new Models_WebVideo();
		$videoL = new Content_Models_WebVideoLink();
	
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
				    		    "linkID"  => $id,
				    			"webVideoID" => $lastID
				    			
				    		);
				    		$videoL->insertData($dataL);
	    				}else{
	    					$this->view->error = "Odkazy na youtube či vimeo nebyly správně vloženy!";
	    				}

	    		}
	    	}
    	}
	}	
	private function removeLinkVideos(){
		
		$video  = new Models_WebVideo();
		$videoL = new Content_Models_WebVideoLink();
	
    	$delVideo = $this->_request->getPost("delVideo");
    	
    	if($delVideo){
	    	foreach($delVideo as $val){

	    		$where = "webVideoID = $val";
	    		$video->deleteData($where);
	    		$videoL->deleteData($where);
	    	}
    	}
	}

}

?>