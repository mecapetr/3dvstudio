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
		//print_r($linkID);
		$subLinkID   = $this->getUrlSubLinkID($filter,$linkID,"sublink");
		$article     = new Content_Models_Article();
		$link   	 = new Content_Models_Link();
		$list    	 = array();
		$linkContent = "";		
		

		$this->view->displayHeaderSubmenu 	= true;
		//jestliže jsou dva parametry v url obsazené
		if($subLinkID){
							
			$linkC       = $link->getOneRow("linkID = '$linkID'");
			$linkContent = $link->getOneRow("linkID = '$subLinkID'"); 
						
			//jestliže druhý paramter url není odkaz, tak se zjistí jestli je článek
			if(!$linkContent){
			
				$linkContent = $linkC;
				$this->getLinkContent($linkContent);				
				
				$script = new Library_Scripts();
				$id = $script->getFirstParam("sublink", $this->_request);
				
				if(is_numeric($id)){
					
					$id = floor($id);
					
					$this->getMenu($linkC,$linkContent);
					//$this->getCategoryMenu($linkC,$linkContent);
					//$this->getSideCategoryMenu($linkC,$linkContent);
					
					if($linkContent->isEshopCategory){
						
						$productFun = new Eshop_Library_ProductFunctions($this->_request, $this->_response);
						$productFun->productID = $id;
						$productFun->lang      = new Zend_Session_Namespace("lang");
						$productFun->getProductData();
						$productFun->plusViewCount();
						
						$session = new Zend_Session_Namespace("basket");
						$session->lastProductUrl = $_SERVER["REQUEST_URI"];
						
						$this->renderScript("index/product-detail.phtml");
						
					}else{
						
						$aData = $this->getArticle($id); // zobrazí konkrétné článek
						if($aData){
													
							if(!$aData->metaTitle)$aData->metaTitle = $aData->title;												
							$this->setMetadata($aData->keywords,$aData->description,$aData->metaTitle);
							
							//vypise hlavni menu
							$this->getMenu($linkC);
							//vypise kategorie menu
							//$this->getCategoryMenu($linkC);
							//$this->getSideCategoryMenu($linkC);
							
							//drobeckove menu
							$this->setBreadCrumbData($linkContent,$aData);
							
							$this->view->refererSite = "/";
							if(isset($_SERVER["HTTP_REFERER"]))$this->view->refererSite = $_SERVER["HTTP_REFERER"];
	
							$this->renderScript("index/detail.phtml");
													
							
						}else{
							$this->_forward("error","error","core");  //jestliže není článek, vyhodí error
						}
					}
					
				}else{
					$this->_forward("error","error","core");  //jestliže není článek, vyhodí error
				}
				
			}else{
			
				$this->getLinkContent($linkContent,$linkC);
				$this->getMenu($linkC,$linkContent);
				//$this->getCategoryMenu($linkC,$linkContent);
				//$this->getSideCategoryMenu($linkC,$linkContent);
				
				if($linkContent->isEshopCategory == 1){
					// vypíše data odkazu
					$this->getProductCategoryContent($linkContent,$linkC);
				}

				$this->setMetadata($linkContent->keywords,$linkContent->description,$linkContent->metaTitle);
				
        		//drobeckove menu
				$this->setBreadCrumbData($linkC,$linkContent);
				
				$this->view->firstCategory = $linkC;
				
				if($linkContent->isEshopCategory == 0){
					$this->renderScript("index/index.phtml");
				}else{
					$this->renderScript("index/category-products.phtml");
				}
								
			}
			$this->_response->insert('slider' , $this->view->render('placeholders/empty.phtml'));
			
		}elseif($linkID){

			
			//jestliže je jeden parametr v url obsazený	
			
			$atribute = "niceTitle";
			if($this->modulesData->jazykoveMutace)$atribute = "linkID";
			
			$linkLang = new Content_Models_LinkLang();
			$linkContent = $link->getOneRow("linkID = '$linkID'");	
			$this->setTranslate($linkLang, "linkID", $linkContent);
			
			// vypíše data odkazu
			$this->getLinkContent($linkContent);	
			
			if($linkContent->isEshopCategory == 1){	
				// vypíše data odkazu
				$this->getProductCategoryContent($linkContent);
			}
										
			//vypise menu
			$this->getMenu($linkContent);
			//$this->getCategoryMenu($linkContent);
			//$this->getSideCategoryMenu($linkContent);
							
			$this->setMetadata($linkContent->keywords,$linkContent->description,$linkContent->metaTitle);
				
			//drobeckove menu
			$this->setBreadCrumbData($linkContent,null);
				
			$this->view->firstCategory = $linkContent;
	
			if($linkContent->isEshopCategory == 0){
				$this->renderScript("index/index.phtml");
			}else{
				$this->renderScript("index/category-products.phtml");
			}

			$this->_response->insert('slider' , $this->view->render('placeholders/empty.phtml'));
			
		}else{
			
			//jestliže jsme na homepage
			$this->view->homepage = true;
			
			$linkLang = new Content_Models_LinkLang();
			
			$linkContent = $link->getAllItems("parentID = '0' AND isEshopCategory = 0","priority");
			$this->setTranslates($linkLang, "linkID", $linkContent);
			
			$linkContent = $linkContent[0];
			
			$this->getLinkContent($linkContent);  // obsah
			$this->getMenu($linkContent);         // vypise menu
			//$this->getCategoryMenu($linkContent);
			//$this->getSideCategoryMenu($linkContent);
			//$this->getSlider();                   // zobrazi slider	
			
			$this->getRecomendedProducts();		  // vypise doporucene produkty
			$this->setMetadata($linkContent->keywords,$linkContent->description,$linkContent->metaTitle);            // meta data
						
			$this->view->firstCategory = $linkContent;
			
			$this->_response->insert('slider' , $this->view->render('placeholders/slider.phtml'));
			//print_r('asdf');
			$this->renderScript("index/index.phtml");
			
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
			//$this->getCategoryMenu($sublinkContent);
			//$this->getSideCategoryMenu($sublinkContent);
			
			$this->getMenu($sublinkContent);
			
			if($sublinkContent->isEshopCategory == 1){
				// vypíše data odkazu
				$this->getProductCategoryContent($sublinkContent);
			}
		
			$this->setMetadata($sublinkContent->keywords,$sublinkContent->description,$sublinkContent->metaTitle);

			$this->view->lastLink = true;
			
			$this->view->firstCategory = $linkC;
						
    		if($sublinkContent->isEshopCategory == 0){
				$this->renderScript("index/index.phtml");
			}else{
				$this->renderScript("index/category-products.phtml");
			}
			
    	}else{
    		//$this->_forward("error","error","core");  //jestliže není článek, vyhodí error
    	}
    	
    }
   

} // end controller class IndexController