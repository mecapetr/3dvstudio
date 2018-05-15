<?php

/*
 * Třída obsluhujici clanky na hlavni strance
 *
 * @copyright 2008 Polar Televize Ostrava
 *
 */


class Library_Paging 
{    

	private  $jquery;         // bude se stranka zobrazovat přes jquery nebo ne
	private  $page;           // stranka na ktere se nachazim
	private  $pageItemCount;  // pocat zaznamu na strance
	private  $pageTitle;      // název strany v url
	private  $actualPage;     // název strany v url
	private  $allItemCount;   // pocet vsech zaznamu
	private  $numberClass;    // třída pro čísla
	private  $blockID;        // ID celého bloku strankovani
	private  $paging;         // vysledne strankovani
	private  $content;        // kde se vysledný obsah vsech dat a strankovani vlozi
	private  $showActualPageNumber 	= false;
	private  $showNumbers 			= false;
	private  $showArrows 			= false;
	private  $addBootstrapClass		= false;


	function __construct()
	{
		$this->setPage(1);    
	}
	
	public function execute(){
		
		$pageItemCount = $this->pageItemCount;
	    $allItemsCount = $this->allItemCount;
	    
	    if(is_numeric($pageItemCount) && is_numeric($allItemsCount) && is_numeric($this->page)){	

	    	$this->page = ceil($this->page);
			$pagesCount = ceil($allItemsCount / $pageItemCount);
			
			if($pagesCount >= $this->page){
			     
				$limit1     = ($this->page - 1) * $pageItemCount;
				$limit2     = $pageItemCount;
				
				$limit = array($limit1,$limit2);
				if($allItemsCount > 0 && $pagesCount > 1){
					$this->setPaging($this->getCompletePaging($pagesCount));
				}else{
					$this->setPaging("");
				}
				return $limit;
			
			}else{
	    		return false;
	    	}
		
	    }else{
	    	return false;
	    }
		
	}
	
	private function getCompletePaging($pagesCount){
		
		
		$pageBefore = $this->page - 1;
		$pageAfter  = $this->page + 1;
			
		$page       = $this->page;
		$pageTitle  = $this->pageTitle;
		$setContent = $this->content;
		$jquery     = $this->jquery;

		$bootstrapClass = "";
		if($this->addBootstrapClass){
			$bootstrapClass = "pagination";
		}
		
		$content = '<div id="'.$this->getBlockID().'">';
		$content .= '<nav><ul class="' . $bootstrapClass . '">';
		
		if($page == 1 || !$this->showArrows){
		    $content .= '';
		}else{   
			  
			if($jquery)
		    	$content .= '<li><a href="" onclick="page(\'1\',\''.$setContent.'\');return false;">&lt;&lt; První</a></li><li><a onclick="page(\''.$pageBefore.'\',\''.$setContent.'\');return false;" href="">&lt; Předchozí</a></li>';
		    else
		        $content .= '<li><a href="'.$pageTitle.'/strana/1">&laquo;</a></li><li><a href="'.$pageTitle.'/strana/'.$pageBefore.'">&lsaquo;</a></li>';
		          
		}
		    
		
		    $content .= $this->getNumbers($pagesCount);
		    
		    
		if($page == $pagesCount || !$this->showArrows){
		    $content .= '';
		}else{
			
		    if($jquery)
		    	$content .= '<li><a href="" onclick="page(\''.$pageAfter.'\',\''.$setContent.'\');return false;" > Další &gt; </a></li><li><a href="" onclick="page(\''.$pagesCount.'\',\''.$setContent.'\');return false;">Poslední &gt;&gt;</a></li>';
		    else
		        $content .= '<li><a href="'.$pageTitle.'/strana/'.$pageAfter.'">&rsaquo;</a></li><li><a href="'.$pageTitle.'/strana/'.$pagesCount.'">&raquo;</a></li>';
		    
		}
		$content .= '</ul></nav>';
		$content .= '</div>';
		
		return $content;
		
	}
	
	private function getNumbers($pagesCount){

		$translate 		 		= Zend_Registry::get('Zend_Translate');
		
		$from    = "";
		$to      = "";
		$content = "";
		
		$page = $this->page;
		
	    if($page < 6)
	    	$from = 1;
	    else
	   		$from =  $page - 4;
	
	    $to = $page + 4;
	    if($to > $pagesCount) $to = $pagesCount;

	    $selected = "";
	    $jquery = $this->jquery;
	    $setContent = $this->content;
	    
		for($i = $from; $i <= $to;$i++ ){

			if($page == $i){
				$selected = "active";
				$this->setActualPage($i);
			}
			else $selected = "";
			
			if($jquery){
				$content .= '<li class="'.$selected.'"><a href="" onclick="page(\''.$i.'\',\''.$setContent.'\');return false;" class="'.$this->numberClass.' '.$selected.'">';
				if($this->showNumbers){
					$content .= $i;
				}		
				$content .= '</a></li>';	
			}else{
				$content .= '<li class="'.$selected.'"><a href="'.$this->pageTitle.'/strana/'.$i.'" class="'.$this->numberClass.' '.$selected.'">';				
				if($this->showNumbers){
					$content .= $i;
				}
				$content .= '</a></li>';	
			}
			
		}

		if($this->showActualPageNumber){
			$content .= '<li class="actual-page">' . $translate->translate('Strana') . " " .  $page . '</li>';
		}
		return $content; 
		
	}
	
	public function setPage($page){
		
		$this->page = $page;
		
	}
	
	public function setPageItemCount($pageItemCount){
		
		$this->pageItemCount = $pageItemCount;
		
	}
	
	public function setPageTitle($pageTitle){
		
		$this->pageTitle = $pageTitle;
		
	}
	
	public function setActualPage($actualPage){
		
		$this->actualPage = $actualPage;
		
	}
	
	
	
	public function setAllItemCount($allItemCount){
		
		$this->allItemCount = $allItemCount;
		
	}
	
	public function setNumberClass($numberClass){
		
		$this->numberClass = $numberClass;
		
	}
	
	public function setBlockID($blockID){
		
		$this->blockID = $blockID;
		
	}

	public function setPaging($paging){
	
		$this->paging = $paging;
	
	}
	

	
	public function setShowNumbers($showNumbers){		
		$this->showNumbers = $showNumbers;		
	}
	public function setShowArrows($showArrows){		
		$this->showArrows = $showArrows;		
	}
	public function setAddBootstrapClass($addBootstrapClass){		
		$this->addBootstrapClass = $addBootstrapClass;		
	}
	
	public function setContent($content){
		
		$this->content = $content;
		
	}
	
	public function setJquery($jquery){
		
		$this->jquery = $jquery;
		
	}
	public function showActualPageNumber($showActualPageNumber){
		$this->showActualPageNumber = $showActualPageNumber;
	}
	public function getActualPage(){
		
		return $this->actualPage;
		
	}
	public function getPage(){
		
		return $this->page;
		
	}
	
	public function getPageItemCount(){
		
		return $this->pageItemCount;
		
	}
	
	public function getPageTitle(){
		
		return $this->pageTitle;
		
	}
	
	public function getAllItemCount(){
		
		return $this->allItemCount;
		
	}
	
	public function getNumberClass(){
		
		return $this->numberClass;
		
	}
	
	public function getBlockID(){
		
		return $this->blockID;
		
	}
	
	public function getPaging(){
		
		return $this->paging;
		
	}
	
	public function getContent(){
		
		return $this->content;
		
	}
	
	public function getJquery(){
		
		return $this->jquery;
		
	}

	public function getShowNumbers(){
		return $this->showNumbers;
	}
	public function getShowArrows(){
		return $this->showArrows;
	}
	public function getAddBootstrapClass(){
		return $this->addBootstrapClass;
	}
		
}
