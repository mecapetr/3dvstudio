<?php

/**
*
* Trida oblusuhici prihlaseni se do administrace
 * 
 */


class Seorobots_IndexController extends Library_Adminbase
{
		
	function init()
	{
		$this->view->selectedMenu = "SEO";
		$this->setDefault();
	}
	
    function indexAction()
    {	
    	$this->view->subSelected = "Robots";
    	
    	$robots = new Seo_Robots_Models_Robots();
    	 	
    	if($this->_request->getPost("enter")){
    		
    		$filter = $this->setData();
    		if($filter->isValid("robots")){
    			
    			$robots->updateData(array(
    				"robots" => $this->robots	
    			),"seoID = 1");
    			
    			file_put_contents("robots.txt",$this->robots);
    			
    			$this->view->message = "Úspěšně uloženo";
    			
    		}else{
    			$this->view->error = "Nevyplnili jste text";
    		}
    		
    	}

    	$this->view->allData = $robots->getOneRow("seoID = 1");
		
    	$this->addPlaceholders();
    }
    
    private function getData(){
    
    	$data = array(
    		"robots"  => $this->_request->getPost("robots")	
    	);
    
    	return $data;
    }
    
    private function setData(){
    
    	$filters    = $this->setFilters();
    	$validators = $this->setValidators();
    	$data       = $this->getData();
    	$script		= new Library_Scripts();
    	$filter = new Zend_Filter_Input($filters, $validators, $data);
    
    	$this->robots  = $filter->getUnescaped("robots");
    
    	return $filter;
    }
    
    private function setFilters(){
    
    	$filters = array(
    		'robots'  => 'StripTags'
    	);
    
    	return $filters;
    
    }
    
    private function setValidators(){
    
    	$validators = array(
    
    		'robots' => array(
    		    'allowEmpty' => false
    		)
    	);
    
    
    	return $validators;
    
    }
    
}
