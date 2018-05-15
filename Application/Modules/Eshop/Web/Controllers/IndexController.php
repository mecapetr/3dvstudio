<?php

class ContentW_IndexController extends Library_WebBase
{
	
	public $auth;
	public $urlLink;
	
	function init()
    {

        $this->setDefault();
                    	
    	
	} // end of init
	
	function indexAction()
	{

		$filter      = new Zend_Filter_StripTags();
		$linkID      = $this->getUrlLinkID($filter);
		$subLinkID   = $this->getUrlSubLinkID($filter,$linkID,"sublink");
		
		$article     = new Content_Models_Article();
		$link   	 = new Content_Models_Link();
		$list    	 = array();
		$linkContent = "";		

		$this->view->displayHeaderSubmenu 	= true;
		//jestliže jsou dva parametry v url obsazené
		if($subLinkID){
			
			$atribute = "niceTitle";
			if($this->modulesData->jazykoveMutace)$atribute = "linkID";
			
			$linkC       = $link->getOneRow("linkID = '$linkID'");
			$linkContent = $link->getOneRow("linkID = '$subLinkID'"); 
			if(!is_numeric($subLinkID) && $this->modulesData->jazykoveMutace) $linkContent = 0;
			
			//jestliže druhý paramter url není odkaz, tak se zjistí jestli je článek
			if(!$linkContent){
				
				$linkContent = $linkC;
				$this->getLinkContent($linkContent);				
				
				$script = new Library_Scripts();
				$id = $script->getFirstParam("sublink", $this->_request);
				if(is_numeric($id)){
					
					$id = floor($id);
					//echo $id;
					$aData = $this->getArticle($id); // zobrazí konkrétné článek
					if($aData){
												
						if(!$aData->metaTitle)$aData->metaTitle = $aData->title;												
						$this->setMetadata($aData->keywords,$aData->description,$aData->metaTitle);
						
						//vypise hlavni menu
						$this->getMenu($linkC);
						
						//drobeckove menu
						$this->setBreadCrumbData($linkContent,$aData);

						$this->renderScript("index/detail.phtml");
												
						
					}else{
						$this->_forward("error","error","core");  //jestliže není článek, vyhodí error
					}
					
				}else{
					$this->_forward("error","error","core");  //jestliže není článek, vyhodí error
				}
				
			}else{
								
								
				$this->getLinkContent($linkContent,$linkC);
				$this->getMenu($linkC,$linkContent);
				
				if(!$linkContent->metaTitle){
          			$linkContent->metaTitle = $linkContent->title;
          			if($this->modulesData->jazykoveMutace)$linkContent->metaTitle = "link".$linkContent->linkID."title";
				}else{        
            		if($this->modulesData->jazykoveMutace)$linkContent->metaTitle = "link".$linkContent->linkID."metaTitle";        
        		}
				
				$this->setMetadata($linkContent->keywords,$linkContent->description,$linkContent->metaTitle);
				if($this->modulesData->jazykoveMutace)$this->setMetadata("link".$linkContent->linkID."keywords","link".$linkContent->linkID."description",$linkContent->metaTitle);
				
        		//drobeckove menu
				$this->setBreadCrumbData($linkC,$linkContent);
				
				$this->renderScript("index/index.phtml");
								
			}
			$this->_response->insert('slider' , $this->view->render('placeholders/empty.phtml'));
			
		}elseif($linkID){
			
			//jestliže je jeden parametr v url obsazený	
			
			$atribute = "niceTitle";
			if($this->modulesData->jazykoveMutace)$atribute = "linkID";
			
			$linkContent = $link->getOneRow("linkID = '$linkID'");		
			$subCategory = $link->getOneRow("parentID = '$linkContent->linkID'","priority");
					
				// vypíše data odkazu
				$this->getLinkContent($linkContent);
									
				//vypise menu
				$this->getMenu($linkContent);
				
				//nastaví metatagy
				if(!$linkContent->metaTitle){
          			$linkContent->metaTitle = $linkContent->title;
          			if($this->modulesData->jazykoveMutace)$linkContent->metaTitle = "link".$linkContent->linkID."title";
				}else{       
            		if($this->modulesData->jazykoveMutace)$linkContent->metaTitle = "link".$linkContent->linkID."metaTitle";       
        		}			
				$this->setMetadata($linkContent->keywords,$linkContent->description,$linkContent->metaTitle);
				if($this->modulesData->jazykoveMutace)$this->setMetadata("link".$linkContent->linkID."keywords","link".$linkContent->linkID."description",$linkContent->metaTitle);
				
				//drobeckove menu
				$this->setBreadCrumbData($linkContent,null);
	
				$this->renderScript("index/index.phtml");
			
			
			$this->_response->insert('slider' , $this->view->render('placeholders/empty.phtml'));
			
		}else{
			//jestliže jsme na homepage
			$this->view->homepage 				= true;
			
			$linkContent = $link->getAllItems("parentID = '0'","priority");
			$linkContent = $linkContent[0];
			
			$this->getLinkContent($linkContent);  // obsah
			$this->getMenu($linkContent);         // vypise menu
			//$this->getSlider();                   // zobrazi slider	
			
			$this->getRecomendedProducts();		  // vypise doporucene produkty
			$this->setMetadata($linkContent->keywords,$linkContent->description,$linkContent->metaTitle);            // meta data
			
			$this->_response->insert('slider' , $this->view->render('placeholders/slider.phtml'));
		}
		
		$this->setBreadCrumb($list);

    } // end of method indexAction

    function detailAction(){
    	
    	$filter       = new Zend_Filter_StripTags();
		$linkID       = $this->getUrlLinkID($filter);
		$subLinkID    = $this->getUrlSubLinkID($filter,$linkID,"sublink");
		$subSubLinkID = $this->getUrlSubLinkID($filter,$subLinkID,"subsublink");
		
    	$article = new Content_Models_Article();
    	$script  = new Library_Scripts();
    	$link    = new Content_Models_Link();
    	
    	$id = $script->getFirstParam("detail", $this->_request);
    	    	
    	$expl   = explode("-",$subSubLinkID);
    	$sublinkContent = "";
    	if(!(count($expl) > 1 && is_numeric($expl[0]))){
    		$sublinkContent = $link->getOneRow("linkID = '$subSubLinkID'");
    	}
    	if(empty($sublinkContent)){
    		$id = $expl[0];
    	}

    	if(is_numeric($id)){
    		
    		$id = floor($id);
    		$aData = $this->getArticle($id); // zobrazí konkrétné článek
    	
    		if($aData){
    	
    			$link = new Content_Models_Link();
    			
    			$atribute = "niceTitle";
    			if($this->modulesData->jazykoveMutace)$atribute = "linkID";
    				
    			$linkC    = $link->getOneRow("$atribute = '$linkID'");
    			$subLinkC = $link->getOneRow("$atribute = '$subLinkID' AND parentID = '$linkC->linkID'");
    			
    			$this->view->categoryTitle = $subLinkC->title;
    			
    			if(!empty($sublinkContent)){    				
    				$list = array(
	    				$linkC->title    		=> $linkC->niceTitle,
	    				$subLinkC->title 		=> $linkC->niceTitle."/".$subLinkC->niceTitle,
	    				$sublinkContent->title	=> $linkC->niceTitle."/".$subLinkC->niceTitle."/".$sublinkContent->niceTitle,
	    				$aData->title    => ""
	    			);
    			}else{
    				$list = array(
	    				$linkC->title    => $linkC->niceTitle,
	    				$subLinkC->title => $linkC->niceTitle."/".$subLinkC->niceTitle,
	    				$aData->title    => ""
	    			);
    			}
              			
    			//informace o prvním parametru urladresy
    			$this->view->pageType = $linkC;

    			//informace o prvním parametru urladresy
    			$this->view->linkContent = $subLinkC;
    			
    			if(!$aData->metaTitle){
    				$aData->metaTitle = $aData->title." - ".$subLinkC->title." - ".$linkC->title;
    				if(!empty($sublinkContent))$aData->metaTitle = $aData->title." - ".$sublinkContent->title." - ".$subLinkC->title." - ".$linkC->title;
    			}
    			$this->setMetadata($aData->keywords,$aData->description,$aData->metaTitle);
    			$this->getMenu($linkC);
    			$this->setBreadCrumb($list);
    	
    		}else{
    			//$this->_forward("error","error","core");  //jestliže není článek, vyhodí error
    		}
    			
    	}elseif($sublinkContent){
    		    			
			$linkC 			= $link->getOneRow("linkID = $linkID");
			$linkContent 	= $link->getOneRow("linkID = $subLinkID");
			
			$this->getLinkContent($sublinkContent,$linkC,$linkC->linkID);
			$this->getMenu($linkContent);
							
			if(!$sublinkContent->metaTitle){				
				$sublinkContent->metaTitle = $linkContent->title." - ".$linkC->title;
				if(isset($sublinkContent))$sublinkContent->metaTitle = $sublinkContent->title." - ".$linkContent->title." - ".$linkC->title;
			}
			$this->setMetadata($sublinkContent->keywords,$sublinkContent->description,$sublinkContent->metaTitle);

			$this->view->lastLink = true;
						
			$this->renderScript("index/index.phtml");
    	}else{
    		//$this->_forward("error","error","core");  //jestliže není článek, vyhodí error
    	}
    	
    }
       

} // end controller class IndexController