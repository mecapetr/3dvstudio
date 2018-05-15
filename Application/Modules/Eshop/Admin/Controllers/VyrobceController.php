<?php
class Eshop_VyrobceController extends Eshop_Library_WholeEshop
{

	protected $name;
	protected $shortcut;
	
	protected $allLanguageMutations;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();	
    }
    function indexAction()
	{
		$this->_redirect("/admin/eshop/vyrobce/pridat");		
	}

	function pridatAction()
	{
		$this->view->subSelected 	= "Přidat výrobce";
		$supplier	    	= new Eshop_Models_Supplier();
		$script						= new Library_Scripts();
	  			
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/eshop/vyrobce/pridat";
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$filter = $this->setData();
            
            $data = array(
			    	        
			    		"name"         	=> $this->name,
			    		"shortcut"	   	 	=> $this->shortcut				    			
			);
			    	
			$supplier->insertData($data);
			$id = $supplier->lastID;								    	
		    		
			$this->view->message = "Výrobce úspěšně přidán";	
	            
	        
	    }
	      	    
	    	    
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}
		
	function upravitAction()
	{

		$supplier	    	= new Eshop_Models_Supplier();
        $id       = $this->_request->getParam('id');        
	    $enter    = $this->_request->getPost("enter");
		$where    = "supplierID = '$id'";				
		
	    
	    $this->view->action = "/admin/eshop/vyrobce/upravit/id/".$id;	        
		
	    if($enter){
	    	
	    	$script = new Library_Scripts();	
	    	$filter = $this->setData();    
	    	
	            	           		            		
            $data = array(
			    	        
            			"name"      => $this->name,
			    		"shortcut"   => $this->shortcut
			    			
			);
			    	
			$supplier->updateData($data,$where);

					
			$this->view->message = "Výrobce úspěšně upraven";	
			   
	        
	    }
	     
	    //nastavime hlavni data
	   	$this->view->allItems 	= $this->allItems = $supplier->getOneRow("supplierID = $id");
		
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
		
	function seznamAction()
    {  
    			
		$this->view->subSelected  	= "Výrobci";
		$supplier	    	= new Eshop_Models_Supplier();
		$this->view->sortableList 	= true;        
		$this->view->action       	= '/admin/eshop/vyrobce/seznam';
		
		if($this->_request->getPost("deleteButton")){
				
			$toDelete = $this->_request->getPost("delete");
			if(!empty($toDelete)){
				
				foreach($toDelete as $del){
					$supplier->deleteData(" supplierID = $del");
				}

				$this->view->message = "Vybraní výrobci byli úspěšně smazáni.";
					
			}
		}
				
		
		$this->view->allItems = $supplier->getAllItems(null,"priority");

	    //vlozime placeholdery
	    $this->addPlaceholders();

    }
    	      
        
	private function getData(){
		
		$data = array(
			"name"      => $this->_request->getPost("name"),
		    "shortcut"   => $this->_request->getPost("shortcut")
        );
	   
        
        return $data;
		
	}
	
	private function setData(){
		
		$filters    = $this->setFilters();
	    $validators = $this->setValidators();
	    $data       = $this->getData();
	    $script		= new Library_Scripts();
	    $filter = new Zend_Filter_Input($filters, $validators, $data);

	    $this->name       = $filter->getUnescaped("name");
	    $this->shortcut    = $filter->getUnescaped("shortcut");
		
	    return $filter;
	}

	private function setFilters(){
		
		$filters = array(
            'name'  	  => 'StripTags',		
			'shortcut'	  => 'StripTags'
        );
			
        return $filters;
		
	}
	
	private function setValidators(){
		
		$validators = array(
      	    
            'name' => array(  				
                'allowEmpty' => false
            ),
            'shortcut' => array(  				
                'allowEmpty' => false
            )

        );
	
        return $validators;
		
	}
	    
    private function getBackData(){
   	
    	
    		$this->view->name      			 		= $this->name;
	    	$this->view->shortcut        				= $this->shortcut; 


    }

}

?>