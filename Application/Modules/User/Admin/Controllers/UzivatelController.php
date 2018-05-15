<?php
class User_UzivatelController extends User_Library_WholeUser
{
	
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
		$this->view->selectedMenu = "Uživatelé";
    }
    
    function indexAction()
	{
		$this->_redirect("/admin/uzivatele/uzivatel/pridat");		
	}
	
	    
	function pridatAction()
	{
		$this->view->subSelected = "Přidat uživatele";
		$user = new User_Models_User();
	    
	    $enter = $this->_request->getPost("enter");
	    
	    $category = new Newsletter_Models_Category();
	    $this->view->allCategories = $category->getAllItems(null,"priority");
	    	    	    	    
	    $this->view->sendEmails = 1;
	    
	    if($enter){
	    	
	    	$script = new Library_Scripts();

	    	$filter = $this->setData();
	    		    	
	    	$where 		= "email = '$this->email' AND deleted = 0";
	    	$emailExist	= $user->getOneRow($where);	   
	
            $currentDate = date("Y-m-d H:i:s",Time());

            if(count($emailExist) == 0){
            	if($filter->isValid("name")){
					
					if($filter->isValid("surname")){
					
						if($filter->isValid("spol")){

								if($filter->isValid("email")){
									
									if($filter->isValid("password")){
										
										if($filter->isValid("zip")){

									    	$data = array(
							
									    		"email"				=> $this->email,
									    		"password"   		=> md5(md5($this->password."-user!")),
									    		"date"       		=> $currentDate,
									    		"degree"       		=> $this->degree,
									    		"name"      		=> $this->name,
									    		"surname"      		=> $this->surname,
									    		"spol"      		=> $this->spol,
									    		"street"      		=> $this->street,
									    		"zip"      			=> $this->zip,
									    		"city"      		=> $this->city,
									    		"tel"      			=> $this->tel,
									    		"isConfirmed"		=> 1,
									    	    "registerCode"	    => "",
									    		"blocked"			=> 0,
									    		"IP"                => " ",
									    		"dateLogin"         => "0000-00-00 00:00:00",
									    		"sendEmails"        => $this->sendEmails
							
									    	);
									    	
									    	$user->insertData($data);
									    	$userID = $user->lastID;
									    	
									    	if($this->categories){
									    		
									    		$userCategory = new User_Models_UserCategory();
									    		$userCategory->addCategories($userID,$this->categories);
									    		
									    	}
									    			    	
									        $this->view->message = "Uživatel úspěšně přidán";	
								        }else{
								        	 
								        	$this->getBackData();
								        	$this->view->error = "PSČ musí být celé číslo";
								        	 
								        }
							        }else{
							        
							        	$this->getBackData();
							        	$this->view->error = "Nevyplnili jste heslo!";
							        
							        }

								}else{
								
									$this->getBackData();
									$this->view->error = "Email není ve správném tvaru!";
								
								}
						
						}else{
						
							$this->getBackData();
							$this->view->error = "Nezadali jste název společnosti!";
						
						}
					
					}else{
					
						$this->getBackData();
						$this->view->error = "Nezadali jste příjmení!";
					
					}
					
				}else{
					
					$this->getBackData();
					$this->view->error = "Nezadali jste jméno!";
					
				}
		        
		    }else{
	    	
		    		$this->getBackData();
		    		$this->view->error = "Uživatel s tímto E-mailem již existuje";
	    	}
	    }

	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
	function seznamAction(){
		
		$this->view->subSelected = "Uživatelé";
		$user = new User_Models_User();

		$where = " isConfirmed = 1 AND deleted = 0 ";
		
		
		if($this->_request->getPost("deleteButton")){
			
			$delete = $this->_request->getPost('delete');
			if($delete){
				
				$data = array(
					"deleted" => 1,
					"dateDeleted" => date("Y-m-d H:i:s",time())
				);
				
				foreach($delete as $del){
					$user->updateData($data,"userID = '$del'");
				}
				
				$this->view->message = "Uživatelé úspěšně smazáni";
			
			}
			
		}
		
		// seznam administrátorů
		$this->getAdministratorsList($user,"uzivatel/seznam","uzivatele/uzivatel",$where,1,"seznam");

		$this->view->allSearchItems = $user->getAllItems($where,null);
		
		//vlozime placeholdery
		$this->addPlaceholders();
		
	}
	
	function upravitAction()
	{
		
	    $user         = new User_Models_User();    
	    $userCategory = new User_Models_UserCategory();
	    $category     = new Newsletter_Models_Category();
	    
	    $enter        = $this->_request->getPost("enter");
	    $id           = $this->_request->getParam('id');
		$where        = "userID = '$id'";
		
		$this->view->action  = "/admin/uzivatele/uzivatel/upravit/id/".$id;
			    
	    if($enter){

	    	$oldData   = $user->getOneRow($where);
	    	$emialData = $user->getOneRow("email = '$this->email' AND deleted = 0");
	    	
	    	$script = new Library_Scripts();
	    			    	
	    	$filter = $this->setData();	    	

	    	if($oldData->email == $this->email || count($emialData) == 0){	
	    		
            	if($filter->isValid("name")){
					
					if($filter->isValid("surname")){
					
						if($filter->isValid("spol")){

								if($filter->isValid("email")){
									
										if($filter->isValid("zip")){

									    	$data = array(
							
									    		"email"	     => $this->email,
									    		"degree"     => $this->degree,
									    		"name"       => $this->name,
									    		"surname"    => $this->surname,
									    		"spol"       => $this->spol,
									    		"street"     => $this->street,
									    		"zip"        => $this->zip,
									    		"city"       => $this->city,
									    		"tel"        => $this->tel,
									    		"blocked"    => $this->blocked,
									    		"sendEmails" => $this->sendEmails
							
									    	);
			    	
			    							$user->updateData($data,$where);
			    							
			    										    							
			    							if($this->categories){
			    											    								
			    								$userCategory->deleteData($where);
			    								$userCategory->addCategories($id,$this->categories);
			    							}
			    							
		    								$this->view->message = "Uživatel úspěšně upraven";	
	        
			    	
							            	if(!empty($this->password) && !empty($this->passwordConfirm) && $this->passwordConfirm == $this->password ){
							            		
							            		$data = array(	
							            				"password" => md5(md5($this->password."-user!")),            				
										    	);	
										    	
										    	$user->updateData($data,$where);
									    	    $this->view->message = "Uživatel úspěšně upraven";	
								        	    	
							            	}else if($this->passwordConfirm != $this->password){        
									    		$this->view->error = "Zadali jste různá hesla";
							            	}
							            	
							            }else{
							            	
							            	$this->getBackData();
							            	$this->view->error = "PSČ musí být celé číslo";
							            	
							            }
							       
							            	
							    }else{
							            	
							        $this->getBackData();
							        $this->view->error = "Email není ve správném tvaru!";
							            	
							    }
							            	
							}else{
							            	
							    $this->getBackData();
							    $this->view->error = "Nezadali jste název společnosti!";
							            	
							}
						            		
						}else{
							            			
						    $this->getBackData();
							$this->view->error = "Nezadali jste příjmení!";
							            			
						}
							            		
					}else{
							            			
						$this->getBackData();
						$this->view->error = "Nezadali jste jméno!";
							            			
					}
            	
				}else{
					
					$this->getBackData();
					$this->view->error = "Zadaný email k přihlášení už existuje!";
					
				}
	    	
	    }

		$this->view->allItems = $user->getOneRow($where);

		$this->view->allUsersCategories 	= $userCategory->getAllItems("userID = '$id'");
		$this->view->allCategories  		= $category->getAllItems(null,"priority");
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
	function smazatAction()
    {  		
		  
		  $user   = new User_Models_User();  
		  $id     = $this->_request->getParam('id');
		  $where  = " userID = '$id' ";
		  $result = $user->getOneRow($where);	  
		  		  
		  $this->view->oneRow = $result;
		  
		  if($this->_request->isPost()){
		  	
		      $delete = $this->_request->getPost('delete');
		      if($delete == 1){
                 	      	
		      	 $data = array(
		      		"deleted" => 1,
		      		"dateDeleted" => date("Y-m-d H:i:s",time())
		      	 );
		      	
		      	 $user->updateData($data,$where);
		      	 
		      	 $this->view->message = "Uživatel úspěšně smazán";
		      	 
		      }else{
		      	
		          $this->_redirect('/admin/uzivatele/uzivatel/pridat');	
		      	
		      }
		  	
		  }
								
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
    }
    
    private function getData(){
    
    	$data = array(
            "email"       	  => $this->_request->getPost("email"),
            "password"    	  => $this->_request->getPost("password"),
    		"degree"   	  	  => $this->_request->getPost("degree"),
    		"name"   	  	  => $this->_request->getPost("name"),
    		"surname"     	  => $this->_request->getPost("surname"),
    		"spol"   	  	  => $this->_request->getPost("spol"),
    		"street"   	  	  => $this->_request->getPost("street"),
    		"zip"   	  	  => $this->_request->getPost("zip"),
    		"city"   	  	  => $this->_request->getPost("city"),
    		"tel"   	  	  => $this->_request->getPost("tel"),
    		"newPassword"     => $this->_request->getPost("newPassword"),
    		"passwordConfirm" => $this->_request->getPost("passwordConfirm"),
    		"blocked"   	  => $this->_request->getPost("blocked"),
    		"categories"	  => $this->_request->getPost("categories"),
    		"sendEmails"	  => $this->_request->getPost("sendEmails")
    	);
    	 
    
    	return $data;
    
    }
    
    private function setData(){
    
    	$filters    = $this->setFilters();
    	$validators = $this->setValidators();
    	$data       = $this->getData();
    	$script		= new Library_Scripts();
    	$filter     = new Zend_Filter_Input($filters, $validators, $data);
    
    	$this->email    	   = $filter->getUnescaped("email");
    	$this->password 	   = $filter->getUnescaped("password");
    	$this->degree		   = $filter->getUnescaped("degree");
    	$this->name	    	   = $filter->getUnescaped("name");
    	$this->surname  	   = $filter->getUnescaped("surname");
    	$this->spol	    	   = $filter->getUnescaped("spol");
    	$this->street   	   = $filter->getUnescaped("street");
    	$this->zip	       	   = $filter->getUnescaped("zip");
    	$this->city	    	   = $filter->getUnescaped("city");
    	$this->tel	    	   = $filter->getUnescaped("tel");
    	$this->newPassword	   = $filter->getUnescaped("newPassword");
    	$this->passwordConfirm = $filter->getUnescaped("passwordConfirm");
    	$this->blocked	       = $filter->getUnescaped("blocked");
    	$this->categories 	   = $filter->getUnescaped("categories");
    	$this->sendEmails 	   = $filter->getUnescaped("sendEmails");
    
    	return $filter;
    }
    
    private function setFilters(){
    
    	$filters = array(
            'email'    		  => 'StripTags',
            'password' 		  => 'StripTags',
            'degree'   		  => 'StripTags',
            'name'     		  => 'StripTags',
            'surname'  		  => 'StripTags',
            'spol'     		  => 'StripTags',
            'street'   		  => 'StripTags',
            'zip'  	   		  => 'StripTags',
            'city'     		  => 'StripTags',
            'tel'  	  		  => 'StripTags',
            'newPassword'  	  => 'StripTags',
            'passwordConfirm' => 'StripTags',
            'blocked'  	      => 'StripTags',
    		'categories' 	  => 'StripTags',
    		'sendEmails' 	  => 'StripTags'
    	);
    
    	return $filters;
    
    }
    
    private function setValidators(){
    
    	$validators = array(
          	    'email' => array(  				
                    'allowEmpty' => false,
                    'EmailAddress'
    	),
                'password' => array(  				
                    'allowEmpty' => false
    	),
                'degree' => array(  				
                    'allowEmpty' => true
    	),
                'name' => array(  				
                    'allowEmpty' => true
    	),
                'surname' => array(  				
                    'allowEmpty' => true
    	),
                'spol' => array(  				
                    'allowEmpty' => true
    	),
                'street' => array(  				
                    'allowEmpty' => true
    	),
                'zip' => array(  				
                    'allowEmpty' => true,
                    'int'
    	),
                'city' => array(  				
                    'allowEmpty' => true
    	),
                'tel' => array(  				
                    'allowEmpty' => true
    	),
                'newPassword' => array(  				
                    'allowEmpty' => true
    	),
                'passwordConfirm' => array(  				
                    'allowEmpty' => true
    	),
                'blocked' => array(  				
                    'allowEmpty' => true
    	),
                'categories' => array(  				
                    'allowEmpty' => true
    	),
                'sendEmails' => array(  				
                    'allowEmpty' => true
    	)
    
    	);
    
    	return $validators;
    
    }
	    
    private function getBackData(){
    	
    	$this->view->email      = $this->email;
	    $this->view->password   = $this->password;
	    $this->view->degree     = $this->degree;
	   	$this->view->name       = $this->name;
	   	$this->view->surname    = $this->surname;
	   	$this->view->street     = $this->street;
	   	$this->view->zip        = $this->zip;
	   	$this->view->city       = $this->city;
	   	$this->view->tel        = $this->tel;
	   	$this->view->spol       = $this->spol;
	   	$this->view->categories = $this->categories;
	   	$this->view->sendEmails = $this->sendEmails;
	    		    	
    	
    }
    	    	
}

?>