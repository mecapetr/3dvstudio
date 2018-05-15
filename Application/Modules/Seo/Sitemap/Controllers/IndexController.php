<?php

/**
*
* Trida oblusuhici prihlaseni se do administrace
 * 
 */


class Seositemap_IndexController extends Library_Adminbase
{
		
	function init()
	{
		$this->view->selectedMenu = "SEO";
		$this->setDefault();
	}
	
    function indexAction()
    {	
    	$this->view->subSelected = "Sitemap";
    	
    	$siteMap          = new Seo_Sitemap_Models_Sitemap();
    	$seoArticle       = new Seo_Sitemap_Models_SitemapArticles();
    	$seoLinks         = new Seo_Sitemap_Models_SitemapLinks();
    	$siteMapFunctions = new Seo_Sitemap_Library_SitemapFunctions();
    	
    	if($this->_request->getPost("generate")){
    		$siteMapFunctions->generate($this->regDomain["cz"]);
    		$this->view->message = "Úspěšně vygenerováno.";
    	}
    	
    	if($this->_request->getPost("enter")){
    		
    		$filter = $this->setData();
    		if($filter->isValid("generateTypeID")){
    			
    			if($filter->isValid("changefreq")){
    			
	    			$siteMap->updateData(array(
	    				"generateTypeID" => $this->generateTypeID,	
	    				"changefreq"     => $this->changefreq	
	    			),"seoID = 1");
	    			
	    			$this->addSeoArticles($seoArticle);
	    			$this->addLinkArticles($seoLinks);
	    				    			
	    			$this->view->message = "Úspěšně uloženo";
    			
    			}else{
    				$this->view->error = "Špatná frekvence.";
    			}
    			
    		}else{
    			$this->view->error = "Špatný typ generování";
    		}
    		
    	}
    	
    	$this->view->files  = $siteMapFunctions->getAllXmls();
    	$this->view->path   =  $siteMapFunctions->getPath();
    	$this->view->domain = $this->regDomain["cz"];
    	
    	$this->allArticles = $seoArticle->getAllItems();
    	$this->allLinks    = $seoLinks->getAllItems();
    	
    	$link = new Content_Models_Link();
    	$this->setMenuLinks($link,"articles");
    	$this->setMenuLinks($link,"links");

    	$this->view->allData = $siteMap->getOneRow("seoID = 1");
    			
    	$this->addPlaceholders();
    }
    
    private function getAllXmls(){
    	
    	
    	
    }
    
    private function addSeoArticles($seoArticle){
    	
    	$seoArticle->deleteData(null);
    	if(count($this->articles) > 0){
    		foreach($this->articles as $a){
    			$seoArticle->insertData(
    				array("linkID" => $a)
    			);
    		}
    	}
    	
    }
    
    private function addLinkArticles($seoLinks){
    	 
    	$seoLinks->deleteData(null);
    	if(count($this->links) > 0){
    		foreach($this->links as $l){
    			$seoLinks->insertData(
    				array("linkID" => $l)
    			);
    		}
    	}
    	 
    }
    
    private function setMenuLinks($link,$type){
    	$mainLinks = $link->getAllItems("parentID = '0' AND isEshopCategory = 0",'priority');
    	$subLinks  = $link->getAllItems("parentID <> '0' AND isEshopCategory = 0",'priority');
    	 
    	$this->subLinksArr = array();
    	$this->linksOutput = "";
    	 
    	foreach($subLinks as $val){
    		$this->subLinksArr[$val->parentID][] = $val;
    	}
    	 
    	//pouzijeme rekurzi pro vytvoreni odkazu a k nim prislusné pododkazy
    	if(!empty($mainLinks[0]))
    		$this->recurseLinksForList($type,$mainLinks,0);
    	else
    		$this->linksOutput = '<div class="no-data text-center">Zatím se zde nenachází žádné odkazy.</div>';
    	
    	 
    	$this->view->{$type} = $this->linksOutput;
    }
    
    private function recurseLinksForList($type,$children,$recurseLevel){
    	//prochazime postupne od korene a zanorujeme se do childu
    	 
    	$ulMargin = $recurseLevel*15;
    
    	$this->linksOutput .= '<ul class="data-list">';
    	 
    	foreach($children as $child){
    		$this->linksOutput .= '<li id="'.$child->linkID.'" class="clearfix" >';
    		
    		$checked = "";
    		foreach($this->allArticles as $art){
    			if($art->linkID == $child->linkID && $type == "articles")$checked = "checked";
    		}
    		foreach($this->allLinks as $art){
    			if($art->linkID == $child->linkID && $type == "links")$checked = "checked";
    		}
    		
    		$this->linksOutput .= '     <div style="padding-left:'.$ulMargin.'px;"> <label class="checkbox-inline"><input '.$checked.' class="delete" type="checkbox" name="'.$type.'[]" value="'.$child->linkID.'"> '.$child->title.'</label></div>';
    		$this->linksOutput .= '		<div class="clear-left"></div>';
    		 
    		if(!empty($this->subLinksArr[$child->linkID]))
    		$this->recurseLinksForList($type,$this->subLinksArr[$child->linkID],$recurseLevel+1);
    
    		$this->linksOutput .= "</li>";
    	}
    
    	$this->linksOutput .= '</ul>';
    }
    
    private function getData(){
    
    	$data = array(
    		"generateTypeID"  => $this->_request->getPost("generateTypeID"),
    		"changefreq"      => $this->_request->getPost("changefreq")
    	);
    	
    	$this->articles = $this->_request->getPost("articles");
    	$this->links    = $this->_request->getPost("links");
    
    	return $data;
    }
    
    private function setData(){
    
    	$filters    = $this->setFilters();
    	$validators = $this->setValidators();
    	$data       = $this->getData();
    	$script		= new Library_Scripts();
    	$filter = new Zend_Filter_Input($filters, $validators, $data);
    
    	$this->generateTypeID  = $filter->getUnescaped("generateTypeID");
    	$this->changefreq      = $filter->getUnescaped("changefreq");
    	    
    	return $filter;
    }
    
    private function setFilters(){
    
    	$filters = array(
    		'generateTypeID' => 'StripTags',
    		'changefreq'     => 'StripTags'
    	);
    
    	return $filters;
    
    }
    
    private function setValidators(){
    
    	$validators = array(
    
    		'generateTypeID' => array(
    		    'allowEmpty' => false
    		),
	    	'changefreq' => array(
	    	    'allowEmpty' => false
	    	)
    	);
    	
    	return $validators;
    
    }
    
}
