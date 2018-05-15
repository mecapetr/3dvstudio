<?php
class User_AdministratorController extends User_Library_WholeUser{
	
	private $login;
	private $password;
	private	$type;
	private	$portal;
	private	$allPortals;
	private $name;
	private $role;
	private $confirmPassword;
	private $email;
		
	//funkce pr�stupn� v�em action
	function init()
    {    
	   
		parent::init();		
    }
    function indexAction()
	{
		$this->_redirect("/admin/uzivatele/uzivatel/pridat");		
	}
	
	function pridatAction()
	{
		
		$this->view->subSelected = "Administratoři";
		$adminuser = new User_Models_Adminuser();
	    
	    $enter = $this->_request->getPost("enter");
	    	    
	    $this->view->action = "/admin/uzivatel/admin-pridat";
	    
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    			    	
	    	$this->login    = $this->_request->getPost("login");
	    	$this->password = $this->_request->getPost("password");
	    	$this->rights   = $this->_request->getPost("rights");

            $currentDate = date("Y-m-d H:i:s",Time());
            
            if(!empty($this->login) && !empty($this->password)){
            	
            	$logCount = $adminuser->getCount("login = '$this->login'");
            	 
            	if($logCount == 0){
			    	$data = array(
	
			    			"login"      => $this->login,
			    	        "password"   => $this->password,
			    	        "date"       => $currentDate,
			    	        "type"       => $this->rights
			    	);
			    	
			    	$adminuser->insertData($data);
			    	
			        $this->view->message = "Administrátor úspěšně přidán";	
	        
		        }else{
		        	$this->getBackData();
		        	$this->view->error = "Uživatel už existuje";
		        }
	    	}else{
	    		
	    		$this->getBackData();
	    		$this->view->error = "Nevyplnili jste login nebo heslo administrátora";
	    		
	    	}
	        
	    }
	    
	    // seznam administrátorů	
        $this->getAdministratorsList($adminuser,"administrator/pridat","uzivatele/administrator","type <> 'superadmin'","type <> 'superadmin'");	
	    							
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
	    
	function upravitAction()
	{
		
	    $adminUser = new User_Models_Adminuser();	    
	    $enter     = $this->_request->getPost("enter");
	    $id        = $this->_request->getParam('id');
		$where     = "adminUserID = '$id'";
		
		$this->view->action  = "/admin/uzivatele/administrator/upravit/id/".$id;
			    
	    if($enter){

	    	$script = new Library_Scripts();
	    			    	
	    	$this->login    = $this->_request->getPost("login");
	    	$this->password = $this->_request->getPost("password");
	    	$this->rights   = $this->_request->getPost("rights");
	    	             
            if(!empty($this->login) && !empty($this->password)){

            	$logCount = $adminUser->getCount("login = '$this->login'");
            	$oldData  = $adminUser->getOneRow("adminUserID = '$id'");
            	 
            	if($logCount == 0 || $this->login == $oldData->login){
            		
			    	$data = array(
	
			    			"login"    => $this->login,
			    	        "password" => $this->password,
			    	        "type"     => $this->rights
			    	        
			    	);
	            	    			    	
			    	$adminUser->updateData($data,$where);
			    									
			        $this->view->message = "Administrátor úspěšně upraven";	
		        
		        }else{
		        	$this->getBackData();
		        	$this->view->error = "Uživatel už existuje";
		        }
	        
	    	}else{
	    		
	    		$this->getBackData();
	    		$this->view->error = "Nevyplnili jste login nebo heslo!";
	    		
	    	}
	        
	    }
	    	    
		$this->view->allItems = $adminUser->getOneRow($where);
	    							
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
	function smazatAction()
    {  		
		  
		  $adminUser = new User_Models_Adminuser();
  
		  $id     = $this->_request->getParam('id');
		  $where  = "adminUserID = '$id' ";
		  $result = $adminUser->getOneRow($where);	  
		  		  
		  $this->view->oneRow = $result;
		  
		  if($this->_request->isPost()){
		  	
		      $delete = $this->_request->getPost('delete');
		      if($delete == 1){
                 	      	
		      	 $adminUser -> deleteData($where);
		      	 $this->view->message = "Administrator úspěšně smazán";
		      	 
		      }else{
		      	
		          $this->_redirect('/admin/uzivatele/administrator/pridat');	
		      	
		      }
		  	
		  }
								
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
    }
    
    function getBackData(){
    	 
    	$this->view->login    = $this->login;
    	$this->view->password = $this->password;
    	$this->view->rights   = $this->rights;
    	 
    }
    	    	
}

?>