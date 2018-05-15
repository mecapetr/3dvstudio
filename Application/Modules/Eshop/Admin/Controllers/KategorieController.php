<?php
class Eshop_KategorieController extends Eshop_Library_WholeEshop
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
		$this->_redirect("/admin/eshop/kategorie/pridat");		
	}

	function pridatAction()
	{
		$this->view->subSelected = "Přidat kategorii";
		$link			    = new Content_Models_Link();
		$fil			    = new Eshop_Models_Filter();
		$pStat			    = new Eshop_Models_ProductStatus();
		$script				= new Library_Scripts();
		$this->view->date	= date("j.n.Y",Time());
	  	
	    $language 		 = new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/eshop/kategorie/pridat";
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$filter = $this->setData();

	            //if($filter->isValid("title-cz")){
	            	           	
	            		$urlLink = "/".$this->niceTitle['cz'];
	            		$linkRow = $link->getOneRow("linkID = '$this->parent'");
	            		if($linkRow && $this->parent != 0)$urlLink = "/".$linkRow->niceTitle."/".$this->niceTitle['cz'];
	            		
	            		$data = array(
				    	        
	            			"parentID"      	=> $this->parent,
	            		    "shortcut"      	=> $this->shortcut,
				    		"title"         	=> $this->title['cz'],
				    		"niceTitle"	   	 	=> $this->niceTitle['cz'],
				    	    "text"         	 	=> $this->text['cz'],
	            			"active"	    	=> $this->active,
	            			"view"		    	=> 0,
	            			"date"	 			=> date("Y-m-d H:i:s",time()),
	            			"dateEdit"	    	=> "0000-00-00 00:00:00",
	            			"userAdd"	    	=> $this->user,
		            		"showFacebook" 		=> 0,
	            			"otherLink"     	=> "",
	            			"priority"      	=> 1,
	            		    "noDelete"      	=> 0,
	            			"inMenu"        	=> $this->inMenu,
	            			"inBannerPlace"     => $this->inBannerPlace,
	            			"inLeftColumn"      => $this->inLeftColumn,
	            		    "inFooter"      	=> 0,
	            			"isEshopCategory"	=> 1,
	            			"metaTitle"     	=> $this->metaTitle['cz'],
	            			"keywords"      	=> $this->keywords['cz'],
	            			"description"   	=> $this->description['cz']	,
	            			"oldUrl"   		=> $this->oldUrl['cz']			    			
				    	);
				    	
				    	$link->insertData($data);
				    	$id = $link->lastID;
				    
				    												    	
			    						    	
						$allItems = $link->getAllItems(null, array("priority","linkID DESC"));
						$script->updatePriority($allItems, $link, "linkID");
	            
						$this->addCategoryPhoto($id);
						$this->insertFilters($id);
						$this->insertStatuses($id);
						//pridani do slovniku
						if($this->modulesData->jazykoveMutace){

							//zde povolime pridani ve slovniku vzdy
							$this->allowEditOtherLink = true;
					    	//vlozeni dat do slovniku
					    	$this->updateDictionary('add',"link","linkID",$id);
						}
				        $this->view->message = "Kategorie úspěšně přidána";	
	            	
	            /*
		    	}else{
		    		
		    		$this->getBackData();
		    		$this->view->error = "Nevyplnili jste povinné údaje";
		    		
		    	}
	    	*/
	        
	    }

	    $this->view->allF = $fil->getAll(null,"priority");
	    $this->view->allStatuses = $pStat->getAllItems(null,"title");
	    //nastavi všechny odkazy
	    $this->setMenuLinks($link,"add");

	    
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}
		
	function upravitAction()
	{
	
		$link	  		= new Content_Models_Link();
	    $language 		= new Models_Language_Language();
		$fil	  		= new Eshop_Models_Filter();
    	$categoryFilter = new Eshop_Models_CategoryFilter();
    	$categoryStatus = new Eshop_Models_CategoryStatus();
    	$pStat			= new Eshop_Models_ProductStatus();
    	
        $id       = $this->_request->getParam('id');        
	    $enter    = $this->_request->getPost("enter");
		$where    = "linkID = '$id'";				
		
	    $this->view->action = "/admin/eshop/kategorie/upravit/id/".$id;
	    
		$oldData = $link->getOneRow($where);

		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
        
	    if($enter){
	    	
	    	$script = new Library_Scripts();	
	    	$filter = $this->setData();    
	    	
	    		//if($filter->isValid("title-cz")){
	            	           		            		
	            		$data = array(
				    	        
	            			"parentID"      => $this->parent,
	            		    "shortcut"      => $this->shortcut,
				    		"title"         => $this->title['cz'],
				    		"niceTitle"	    => $this->niceTitle['cz'],
				    	    "text"          => $this->text['cz'],
	            			"active"	    => $this->active,
	            			"inMenu"        => $this->inMenu,
	            			"inBannerPlace" => $this->inBannerPlace,
	            			"inLeftColumn"  => $this->inLeftColumn,
	            			"dateEdit"	    => date("Y-m-d H:i:s",time()),
	            			"userEdit"	    => $this->user,
	            			"metaTitle"      => $this->metaTitle['cz'],
	            			"keywords"      => $this->keywords['cz'],
	            			"description"   => $this->description['cz'],
	            			"oldUrl"   		=> $this->oldUrl['cz']
				    			
				    	);
				    	
				    	$link->updateData($data,$where);

				    	$this->addCategoryPhoto($id);
						$this->insertFilters($id);
						$this->insertStatuses($id);
						//pridani do slovniku
						if($this->modulesData->jazykoveMutace){
					    	$this->updateDictionary('edit',"link","linkID",$id);
						}
						
				        $this->view->message = "Kategorie úspěšně upravena";	
				        
	               /*
		    	}else{
		    		
		    		$this->getBackData();
		    		$this->view->error = "Nevyplnili jste povinné údaje";
		    		
		    	}
	    		*/
	        
	    }
	     
	    //nastavime hlavni data
	    $this->setUpdateData($link,$where);	                   
	    
		
	    //nastavi všechny odkazy
	    $this->setMenuLinks($link,"add");
	    
	    //nastavime nazev nadrazeneho odkazu pokud existuje
	    if($this->allItems->parentID != 0){
	    	$parentData = $link->getOneRow("linkID = ".$this->allItems->parentID);
	    	$this->view->parentTitle = $parentData->title;
	    }	    

	    $this->view->allF = $fil->getAll(null,"priority");
	    
	    $allFiltersArr = array();
	    $allCategoryFilters = $categoryFilter->getAll($where);
	    foreach ($allCategoryFilters as $val){
	    	$allFiltersArr[$val->filterID] = $val;
	    }
	    $this->view->allFiltersArr = $allFiltersArr;
	    
	    $this->view->allStatuses = $pStat->getAllItems(null,"title");
	    
	    $allStatusArr = array();
	    $allStatusFilters = $categoryStatus->getAll($where);
	    foreach ($allStatusFilters as $val){
	    	$allStatusArr[$val->productStatusID] = $val;
	    }
	    $this->view->allStatusArr = $allStatusArr;
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	

	function seznamAction()
	{
		 
		$this->view->subSelected  = "Kategorie";
		$link                  	  = new Content_Models_Link();
		$this->view->sortableList = true;
		$this->view->action       = '/admin/eshop/kategorie/seznam';
	
		if($this->_request->getPost("deleteButton")){
	
			$toDelete = $this->_request->getPost("delete");
			if(!empty($toDelete)){
	
				$linkFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
	
				foreach($toDelete as $del){
					$linkFactory->deleteLinkData($del);
				}
	
				$this->view->message = "Vybrané kategorie byly úspěšně smazány.";
					
			}
		}
	
		$this->setMenuLinks($link,"list");
		$this->view->controller   = "link";
	
	
		//vlozime placeholdery
		$this->addPlaceholders();
	
	}
	function seznamPodkategoriiAction()
    {  
    			

    	Zend_Layout::getMvcInstance()->disableLayout();
    	$allSublinks = array();
    	$id = $this->_request->getParam("id");
		if(!empty($id)){
			
			$link = new Content_Models_Link();
			$allSublinks = $link->getSubLinks("L.parentID = $id");
			
		}
		$this->view->allSublinks = $allSublinks;

	    //vlozime placeholdery
	    $this->addPlaceholders();

    }
    private function insertFilters($categoryID){
    	$categoryFilter = new Eshop_Models_CategoryFilter();
    	$categoryFilter->deleteData("linkID = $categoryID");
    	if(!empty($this->filterID)){
    		foreach ($this->filterID as $val){
    			$categoryFilter->insertData(array(
    					"linkID" => $categoryID,
    					"filterID"	 => $val
    			));
    
    		}
    	}
    }
    
    private function insertStatuses($id){
    	
    	$categoryStatus = new Eshop_Models_CategoryStatus();
    	$categoryStatus->deleteData("linkID = $id");
    	
    	if(!empty($this->statusesID)){
    		foreach ($this->statusesID as $val){
    			$categoryStatus->insertData(array(
    				"linkID"          => $id,
    				"productStatusID" => $val
    			));
    
    		}
    	}
    }
    
    private function setMenuLinks($link,$typeOutput){    	
	    $mainLinks = $link->getAllItems("parentID = '0' AND isEshopCategory = 1",'priority');
	    $subLinks  = $link->getAllItems("parentID <> '0' AND isEshopCategory = 1",'priority');
	    
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
    private function addCategoryPhoto($linkID){
    	$link			   = new Content_Models_Link();
    	$upload            = new Library_UploadFiles();
    	$path              = "Public/Images/EshopCategory";
    	$upload->path      = $path;
    	$upload->ownName   = true;
    	$upload->smallHeight    = 135;
    	$upload->smallWidth     = 0;
    	$upload->middleHeight 	= 380;
    	$upload->middleWidth  	= 0;
    	$upload->largeHeight 	= 420;
    	$upload->largeWidth  	= 0;
    	$upload->maxiHeight 	= 760;
    	$upload->maxiWidth  	= 0;
    	$this->heData = array();
    	
    	$fileName = null;
    	$hiddenImages = $this->_request->getPost("hiddenPhoto");
    	$deleteImages = $this->_request->getPost("deletePhoto");
    	if(!empty($hiddenImages[0]) && !empty($deleteImages[0])){
			$fileName = "";
			@unlink("./".$path."/".$hiddenImages[0]);
			@unlink("./".$path."/mala-".$hiddenImages[0]);
			@unlink("./".$path."/stredni-".$hiddenImages[0]);
			@unlink("./".$path."/velka-".$hiddenImages[0]);
			@unlink("./".$path."/maxi-".$hiddenImages[0]);
			
		}
    	if(!empty($_FILES["photo"]['tmp_name'][0])){
    			
    		if(!empty($hiddenImages[0])){
    			@unlink("./".$path."/".$hiddenImages[0]);
    			@unlink("./".$path."/mala-".$hiddenImages[0]);
    			@unlink("./".$path."/stredni-".$hiddenImages[0]);
    			@unlink("./".$path."/velka-".$hiddenImages[0]);
    			@unlink("./".$path."/maxi-".$hiddenImages[0]);
    		}
    			
    		$upload->fileName    = $upload->niceFile($_FILES["photo"]['name'][0]);
    		$upload->tmpFileName = $_FILES["photo"]['tmp_name'][0];
    		$upload->upload();
    			
    		$fileName = $upload->fileName;
    	}
    	if(!is_null($fileName)){
	    	$link->updateData(array(
	    		"categoryPhoto"	=> $fileName
	    	), "linkID = $linkID");
    	}
    }
    private function recurseLinksForList($children,$recurseLevel){
    	//prochazime postupne od korene a zanorujeme se do childu
    	
    	$ulMargin = $recurseLevel*15;
    		
    	$this->linksOutput .= '<ul class="sortListBasic main-ul data-list">';
    	
	    	foreach($children as $child){
	    		$this->linksOutput .= '<li id="'.$child->linkID.'" class="clearfix" >';        
		        $this->linksOutput .= '     <div class="col-sm-3" style="padding-left:'.$ulMargin.'px;"> <span class="glyphicons glyphicons-move"></span> '.$child->title.'</div>';
		        $this->linksOutput .= '		<div class="text-center col-sm-2">'.$child->shortcut.'</div>';
		        $this->linksOutput .= '		<div class="col-sm-3">'.date("d.m.Y H:i:s",strtotime($child->date)).'</div>';
				$this->linksOutput .= '		<div class="text-center col-sm-2">'.$child->view.'</div>';
				$this->linksOutput .= '		<div class="text-right col-sm-2">';
				$this->linksOutput .= '		    <a title="Uprav" href="'.$this->baseUrl.'/admin/eshop/kategorie/upravit/id/'.$child->linkID.'"><span class="glyphicons glyphicons-pencil"></span></a> ';
				
				if($child->allowDelete){
					$this->linksOutput .= '		<input class="delete" type="checkbox" name="delete[]" value="'.$child->linkID.'">';
				}
				$this->linksOutput .= '		</div><div class="clear-left"></div>';
	    		
	    			if(!empty($this->subLinksArr[$child->linkID]))
	    				$this->recurseLinksForList($this->subLinksArr[$child->linkID],$recurseLevel+1);
	    			
	    		$this->linksOutput .= "</li>";
	    	}
	    	
    	$this->linksOutput .= '</ul>';
    }
    
    public function kontrolaZkratkyAction(){
    	
    	Zend_Layout::getMvcInstance()->disableLayout();

    	$shortcut = $this->_request->getParam("z");
    	$linkID   = $this->_request->getParam("l");
    	
    	$link = new Content_Models_Link();
    	$response = array("error" => "");
    	
    	if($linkID != 0){
	    	$linkData = $link->getOneRow("shortcut = '$shortcut' AND isEshopCategory = 1 AND linkID <> '$linkID'");
	    	if($linkData){
	    		$response["error"] = "Tato zkratka už u některé z kategorií existuje. Vyberte prosím jinou.";
	    	}
    	}else{
    		$linkData = $link->getOneRow("shortcut = '$shortcut' AND isEshopCategory = 1");
    		if($linkData){
    			$response["error"] = "Tato zkratka už u některé z kategorií existuje. Vyberte prosím jinou.";
    		}
    	}
    	
    	echo json_encode($response);
    	$this->renderScript("helper/empty.phtml");
    }
	      
        
	private function getData(){
		
		$data = array(
			"parent"      	=> $this->_request->getPost("parent"),
		    "mainPhoto"   	=> $this->_request->getPost("mainPhoto"),
		    "photos"   	  	=> $this->_request->getPost("photos"),
			"link"        	=> $this->_request->getPost("link"),	
			"showFacebook"	=> $this->_request->getPost("showFacebook"),	 	    
			"active"      	=> $this->_request->getPost("active"),
			"inMenu"      	=> $this->_request->getPost("inMenu"),
			"inFooter"    	=> $this->_request->getPost("inFooter"),
			"inBannerPlace" => $this->_request->getPost("inBannerPlace"),
			"inLeftColumn"  => $this->_request->getPost("inLeftColumn"),
			"filterID"		=> $this->_request->getPost("filterID"),
		    "statusesID"	=> $this->_request->getPost("statusesID"),
			"shortcut"		=> $this->_request->getPost("shortcut")
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
	    $this->shortcut     = $filter->getUnescaped("shortcut");
	    $this->mainPhoto    = $filter->getUnescaped("mainPhoto");
	    $this->photos	    = $filter->getUnescaped("photos");
	    $this->link         = $filter->getUnescaped("link");
	    $this->showFacebook = $filter->getUnescaped("showFacebook");
		$this->active       = $filter->getUnescaped("active");
		$this->inMenu       = $filter->getUnescaped("inMenu");	
		$this->inFooter     = $this->_request->getPost("inFooter");
		$this->inBannerPlace= $this->_request->getPost("inBannerPlace");
		$this->inLeftColumn = $this->_request->getPost("inLeftColumn");
		$this->linkType     = $this->_request->getPost("link-type");
		$this->filterID     = $this->_request->getPost("filterID");
		$this->statusesID   = $this->_request->getPost("statusesID");
		
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$this->title[$val->suffix] 			= $filter->getUnescaped("title-".$val->suffix);
				$this->text[$val->suffix] 			= $filter->getUnescaped("text-".$val->suffix);
				$this->keywords[$val->suffix] 		= $filter->getUnescaped("keywords-".$val->suffix);
				$this->description[$val->suffix] 	= $filter->getUnescaped("description-".$val->suffix);
				$this->metaTitle[$val->suffix] 		= $filter->getUnescaped("metaTitle-".$val->suffix);	
				$this->otherLink[$val->suffix] 		= $filter->getUnescaped("otherLink-".$val->suffix);	
				$this->oldUrl[$val->suffix] 		= $filter->getUnescaped("oldUrl-".$val->suffix);		

				if(empty($this->otherLink[$val->suffix])){
					$this->otherLink[$val->suffix] 		= "";
				}
				if(empty($this->text[$val->suffix])){
					$this->text[$val->suffix] 		= "";
				}
				if(empty($this->title[$val->suffix])){
					$this->title[$val->suffix] 		= "";
				}
				if(empty($this->oldUrl[$val->suffix])){
					$this->oldUrl[$val->suffix] 		= "";
				}
				
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
				$allItems->title['cz'] 			= $allSelectedItems->title;
				$allItems->text['cz'] 			= $allSelectedItems->text;
				$allItems->metaTitle['cz'] 		= $allSelectedItems->metaTitle;
				$allItems->keywords['cz'] 		= $allSelectedItems->keywords;
				$allItems->description['cz'] 	= $allSelectedItems->description;
				$allItems->otherLink['cz'] 		= $allSelectedItems->otherLink;
				$allItems->oldUrl['cz'] 		= $allSelectedItems->oldUrl;
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
		    'shortcut'    => 'StripTags',
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
            ),
            'inBannerPlace' => array(  				
                'allowEmpty' => true
            ),
            'inLeftColumn' => array(  				
                'allowEmpty' => true
            ),
            'filterID' => array(  				
                'allowEmpty' => true
            ),
            'statusesID' => array(  				
                'allowEmpty' => true
            ),
            'shortcut' => array(  				
                'allowEmpty' => true
            )

        );

        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			if($val->suffix != 'cz'){
				$validators["title-".$val->suffix]['allowEmpty'] 		= true;
			}
				$validators["text-".$val->suffix]['allowEmpty'] 		= true;
				$validators["metaTitle-".$val->suffix]['allowEmpty'] 	= true;
				$validators["keywords-".$val->suffix]['allowEmpty'] 	= true;
				$validators["description-".$val->suffix]['allowEmpty'] 	= true;
				$validators["otherLink-".$val->suffix]['allowEmpty'] 	= true;
				$validators["oldUrl-".$val->suffix]['allowEmpty'] 		= true;
		}
		
		$validators["title-cz"]['allowEmpty'] 		= false;
        return $validators;
		
	}
	    
    private function getBackData(){
    	
    		$link = new Content_Models_Link();
    	
    		$this->view->title      			 					= $this->title;
    		$this->view->shortcut      			 					= $this->shortcut;
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

}

?>