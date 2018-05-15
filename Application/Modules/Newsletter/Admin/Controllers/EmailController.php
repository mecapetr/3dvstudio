<?php
class Newsletter_EmailController extends Newsletter_Library_WholeNewsletter
{
	
	private $subject;
	private $text;
	private $email;
	private $date;
	private $category;
	private $interest;
	
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();
    }
    
    function indexAction(){
    	
    	$this->_redirect("/admin/newsletter/email/odeslat");
    	
    }
    
    function odeslatAction()
	{
		
	        $user 		= new User_Models_User();
	        $category 	= new Newsletter_Models_Category();
	        $email 		= new Newsletter_Models_Email();
	        $userEmail 	= new User_Models_UserEmail();
	        
	        $translate 	 = Zend_Registry::get('Zend_Translate');
	    
	        $this->view->subSelected = "Odeslat email";
	    
	        $allCategories = $category->getAllItems(null,"priority");
	        if($this->_request->getPost("enter")){
	    
	            $this->subject              = $this->_request->getPost("subject");
	            $this->text                 = $this->_request->getPost("text");
	            $this->email                = $this->_request->getPost("email");
	            $this->selectedCatToEmail   = $this->_request->getPost("category");
	    
	            if(!empty($this->subject) && !empty($this->text)){
	    
	                if($this->email){
	                    $to = array();
	                    $i  = 0;
	                    foreach($this->email as $it){
	                        if(!empty($it)){
	                            $to[$i] = $it; 
	                        }
	                        $i++;
	                    }
	                    $from = $this->regEmail;
	                    $name = $this->regName;
	                    
	                    //$to = array("meca.petr@gmail.com","meca.petr@g.com","meca.petr@seznam.cz");
	                    set_time_limit(3600);
	    				
	                    
	                    //pokud pracujeme s kategoriema                    
		                $toCategories = "";
	                    if(count($allCategories) != 0){
		                    // ulozime do emailu a priradime email jednotlivym lidem
		                    $catCount = count($this->selectedCatToEmail);
		                    for($i = 0; $i < $catCount; $i++){
		                    	if($catCount != $i+1)	$toCategories .= $this->selectedCatToEmail[$i].",";
		                    	else					$toCategories .= $this->selectedCatToEmail[$i];
		                    	
		                    }
			                $categories = $toCategories;
			                if(!empty($toCategories)){
		                    	$allCat = $category->fetchAll("categoryID IN($toCategories)");
			                	$toCategories = "";
			                    $catCount = count($allCat);
			                    for($i = 0; $i < $catCount; $i++){
			                    	if($catCount != $i+1)	$toCategories .= $allCat[$i]->title.",";
			                    	else					$toCategories .= $allCat[$i]->title;
			                    	
			                    }
			                }
	                    }
	                    
	                    $file = "";
	                    if(!empty($this->filename))$file = $this->filename;
	                    $data = array(
		
				      	  	  	    "title"      	=> $this->subject,
				      	  	  	    "text"      	=> $this->text,
				      	  	  	    "date"      	=> date("Y-m-d H:i:s",time()),
				      	  	  	    "toCategories"  => $toCategories,
				      	  	  	    "count"      	=> count($to)
		
				      	  	  	);
	                    $email->insertData($data);
	                
	                    $emailID = $email->lastID;
	                    	                    
	                    //priradime email uzivatelum kterym byl poslan tento email
	                    if(count($allCategories) != 0 && !empty($categories)){	//poku pracujeme s kategoriema
	                    	$userEmail->addToUsers($emailID,$to,$categories);
	                    }else{	//pokud nepracujeme s kategoriema
	                    	$userEmail->addToUsersNoCategories($emailID,$to);	                    	
	                    }

						$send = $this->sendMail($from,$name,$to,$this->subject,$this->text,$emailID);

	                    if($send)$this->view->message = $translate->translate("Email úspěšně odeslán");
	                }
	    
	            }else{
	                $this->getBackData();
	                $this->view->error = $translate->translate("Nevyplnili jste text nebo předmět zprávy");
	            }
	    
	        }
	    
	        $this->view->allCustomers = $user->getAllItems(null,"email");
	        $this->view->allCategories = $allCategories;
        
	        //if(count($allCategories) == 0){
	        	$emailsArr = array();
	        	$emails = $user->getAllItems("sendEmails = '1' AND deleted = '0' AND blocked = 0");
	        	foreach($emails as $val){
	        		$emailsArr[] = $val->email;
	        	}
	        	$this->view->emails = $emailsArr;
	        //}
	        
	        
        
				
	    //vlozime placeholdery
	    $this->addPlaceholders();		
	}     

	function odeslaneAction(){
		
		$sortableList             = false;
		$email                    = new Newsletter_Models_Email();
		$this->view->sortableList = $sortableList;
		$this->view->action       = '/admin/newsletter/email/odeslane';
		$controller               = "email";
		$this->view->subSelected  = "Odeslané emaily";
			
		if($this->_request->getPost("search")){
			$searchText = $this->_request->getPost("searchText");
			$where = " title LIKE '%$searchText%' ";
			$this->view->allItems = $action->getAllItemsWhere($where);
		}else{

			if($sortableList){
					
				$this->view->allItems    = $action->getAllItemsOrder();
				$this->view->controller  = $controller;
					
			}else{
					
				$totaly      = $email->getCount();

				$descAsc     = $this->_request->getParam('poradi');
				$controller  = "email";

				$this->order($descAsc,$controller,$email,$totaly);
					
					
			}
		}
			
		//vlozime placeholdery
		$this->addPlaceholders();

	}
	
	function detailAction(){
		
		$email                    = new Newsletter_Models_Email();
		$userEmail                = new User_Models_UserEmail();
		$attachement	          = new Newsletter_Models_Attachement();
		$this->view->subSelected  = "Detail emailu";
		$id = $this->_request->getParam('id');
		$where = " emailID = '$id' ";
		$allItems = $email->getOneRow($where);
					
		$this->view->allItems 		 = $allItems;
		$this->view->allUsersEmails  = $userEmail->getAllUsersEmails($id);
		$this->view->allAttachements = $attachement->getAllItems($where);
			
		//vlozime placeholdery
		$this->addPlaceholders();
		
	}
	
	function smazatAction(){
		
		$translate 	 = Zend_Registry::get('Zend_Translate');

		$email         = new Newsletter_Models_Email();
		$userEmail = new User_Models_UserEmail();
		$attachemets   = new Newsletter_Models_Attachement();
		
		
		
		$id = $this->_request->getParam('id');
		$where = " emailID = '$id' ";
		$result = $email->getOneRow($where);
		$this->view->oneRow = $result;
		
		if($this->_request->isPost()){
		
			$delete = $this->_request->getPost('delete');
		
			if($delete == 1){
				//vybereme vsechny prilohy k emailu a smazeme je
				$allAtachements = $attachemets->getAllItems($where);
				foreach($allAtachements as $value){
					@unlink("./Public/Attachment/".$value->file);
				}
				//smazeme email
				$email->deleteData($where);
				//smazeme emaily pripojene k uzivateli
				$userEmail->deleteData($where);
				//smazeme prilohy
				$attachemets->deleteData($where);
		
				$this->view->message = $translate->translate("Email úspěšně smazán");
		
			}else{
		
				$this->_redirect('/admin/zakaznik/odeslane-emaily');
		
			}
		
		}
		
		//vlozime placeholdery
		$this->addPlaceholders();
		
	}
	
	private function getBackData(){
         
        $this->view->subject            = $this->subject;
        $this->view->text               = $this->text;
        $this->view->emailsBack         = $this->email;        
        $this->view->selectedCatToEmail = $this->selectedCatToEmail;
    }
	
}

?>