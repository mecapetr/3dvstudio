<?php


class Option_ZmenaHeslaController extends Options_Library_WholeOption 
{
   
	private $oldPassword;
	private $newPassword;
	private $checkPassword;
	
	//funkce pr�stupn� v�em action
	function init()
    {   
    	
    	parent::init();
			
    }
	       
	//nastaveni osobnich udaju
    function indexAction()
    {   
	    
			$this->view->subSelected = "Změnit heslo";
	        $this->view->action = '/admin/nastaveni/zmena-hesla';				
			$this->view->title = "Změna hesla";
			
            
			if($this->_request->isPost()){
			    
				$user = new User_Models_Adminuser();

				//vyjmuti dat z formu
						    	
	    		$filter = $this->setData();
                							
				if($filter->isValid("oldPassword") && $filter->isValid("newPassword") && $filter->isValid("checkPassword")){
				
				    $back = $user->getOneRow("password = '$this->oldPassword' AND login = '$this->user' ");
					    
					if(!empty($back)){  
					    	
					    if($this->newPassword == $this->checkPassword){
							
					    	$data = array("password"=>$this->newPassword);
					    	$where = "adminUserID = '$back->adminUserID' ";
						    $user->updateData($data,$where);
						    
							$this->view->message = 'Heslo úspěšně změněno.';  

					    }else{	
					     		$this->view->error = 'Nově zadaná hesla nejsou stejná!';    			
						}
					
					}else{
					         $this->view->error = 'Zadali jste špatné staré heslo!';    
					}

				}else{		
				     $this->view->error = 'Nevyplnili jste některou z kolonek!';     			
				}
				
				
			}	   						
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
		
    }
    
   
	private function getData(){
		
		$data = array(
            "oldPassword"        => trim($this->_request->getPost("oldPassword")),
			"newPassword"        => trim($this->_request->getPost("newPassword")),
			"checkPassword"      => trim($this->_request->getPost("checkPassword"))
        );
        return $data;
		
	}
	
	private function setData(){
		
		$filters    = $this->setFilters();
	    $validators = $this->setValidators();
	    $data       = $this->getData();
	        
	    $filter = new Zend_Filter_Input($filters, $validators, $data);

	    $this->oldPassword         = $filter->getUnescaped("oldPassword");
	    $this->newPassword         = $filter->getUnescaped("newPassword");
	    $this->checkPassword       = $filter->getUnescaped("checkPassword");
		
	    return $filter;
	}
	
	private function setFilters(){
		
		$filters = array(
            'oldPassword'     => 'StripTags',
			'newPassword'     => 'StripTags',
			'checkPassword'   => 'StripTags'				
        );
        
        return $filters;
		
	}
	
	private function setValidators(){
		
		$validators = array(
      	    'oldPassword' => array(  				
                'allowEmpty' => false
            ),
            'newPassword' => array(  				
                'allowEmpty' => false
            ),
            'checkPassword' => array(  				
                'allowEmpty' => false
            )

        );
        
        return $validators;
		
	}
	
   
}
