<?php

class AuthW_ZapomenuteHesloController extends Library_WebBase
{
	
	public $degree;
    public $name;
    public $surname;
    public $spol;
    public $proff;
    public $street;
    public $zip;
    public $city;
    public $email;
    public $tel;
    public $pass;
    public $pass2;
    public $login;
    public $cond;
    protected $translate;
    	
	function init()
    {
    	$this->setDefault();   	    	
    	        
	} // end of init
	
	function indexAction()
	{
		
		if($this->_request->getPost("enter")){
			
			$filter = $this->setData();
							
			if($filter->isValid("email")){
				
				$user = new User_Models_User();
				$cData    = $user->getOneRow("email = '$this->email'");
					
				if(count($cData) > 0){
					
					$passwordCode = $this->getRandomString(50);
					$where        = "userID = '$cData->userID'";
					$newPassword  = $this->getRandomString(8);	
					$hasPassword  = md5(md5($newPassword."-user!"));
					/*						    										    
				    $user->getDefaultAdapter()->beginTransaction();
				    try{*/

					    	$data = array(
    							"passwordCode"     => $passwordCode,
    							"passwordConfirm"  => 0,
    							"password"         => $hasPassword,
    							"validityCode"     => date("Y-m-d H:i:s",(time()+86400))						
					    	);
					    	
					    	$user->updateData($data,$where);
					    	
					    	$this->sendDataMail($cData->userID,$passwordCode,$cData->email,$newPassword);
					    	$this->view->message = $this->translate->translate("Na Vaši e-mailovou adresu bylo odesláno nové vygenerované heslo. Platnost tohoto hesla je 24 hodin.");
					    

				    	//$user->getDefaultAdapter()->commit();
		 			/*	
					}catch(Exception $e){
						$user->getDefaultAdapter()->rollBack();	            // pokud nastane chyba, vrati se zpet tabulka do puvodniho tvaru
						$this->getResponse()->append('main',$e->getMessage());
						$this->getBackData();
						$this->view->error = $this->translate->translate("ERROR!");
						
					}*/

				}else{

					$this->getBackData();
					$this->view->error = $this->translate->translate("Zadaný email neexistuje!");
					
				}
				
			}else{

				$this->getBackData();
				$this->view->error = $this->translate->translate("Nezadali jste email!");
			
			}
			
			
		}
		
		$this->_response->insert('loggedMenu', $this->view->render('placeholders/empty.phtml'));
		
    	
    } // end of method indexAction
    
    function potvrzeniAction()
    {

    	$user   = new User_Models_User();
    	$filter = new Zend_Filter_StripTags();
    
    	$userID	  = $filter->filter($this->_request->getParam("id"));
    	$passCode = $filter->filter($this->_request->getParam("passcode"));

    	$where 	  = "userID = $userID AND passwordCode  = '$passCode'";
    	$userData = $user->getOneRow($where);
    
    	if(count($userData) > 0){
    			    			
    		if($userData->passwordConfirm == 0){
    			
    			if(strtotime($userData->validityCode) >= time()){
    			
	    			$data = array(
	    			    "passwordConfirm"=> 1							  					
	    			);
	    
	    			$user->updateData($data,$where);
	    			$this->view->message = $this->translate->translate('Vaše heslo bylo úspěšně potvrzeno.');
	    
    			}else{
    				$this->view->error = $this->translate->translate('Platnost potvrzovcího kódu vypršela.');
    			}
    		}else{
    			$this->_redirect('/');
    			//$this->view->error = "error 1";
    		}
    
    	}else{
    		$this->_redirect('/');
    		//$this->view->error = "error 2";
    	}
    	
    	$this->_response->insert('loggedMenu', $this->view->render('placeholders/empty.phtml'));
 
    }
    
    private function sendDataMail($id,$passCode,$email,$newPassword){
    	 
    	
    	$from    = $this->regEmail;
    	$name    = $this->regDomain;
    	$to      = array($email);
    	
    	
    	$text	 = '
    	    					
    		<p>'.$this->translate->translate("Dobrý den").'<strong>'.$this->login.'</strong>,<br /> '.$this->translate->translate("na stránce").' <a style="color:#2B93C9;" href="http://'.$name.'">'.$name.'</a> '.$this->translate->translate('Vám bylo vygenerováno nové heslo, a to:').'.</p>
    	    					    	    					
    	    <p><label style="width:150px;font-weight:bold;">'.$this->translate->translate("Heslo").':</label> <strong>'.$newPassword.'</strong></p>
    	   
    	    					
    	    <p style="color:#555555;"><strong>'.$this->translate->translate("POZOR").'</strong>, '.$this->translate->translate('toto heslo je třeba ještě potvrdit, pro potvrzení klikněte prosím na následující odkaz. Tento odkaz má platnost 24 hodin.').'
    	    <br /> <a style="color:#2B93C9;" href="http://'.$name.'/zapomenute-heslo/potvrzeni/'.$id.'/'.$passCode.'">http://'.$name.'/zapomenute-heslo/potvrzeni/'.$id.'/'.$passCode.'</a> <br /> <br /></p>
    	    					
    	    <p style="color:#8c8c8c;">'.$this->translate->translate("S pozdravem a přáním pěkného dne").' <br/> '.$this->regName.' <br /> <a style="color:#2B93C9;" href="http://'.$name.'">'.$name.'</a></p>
    	    					
    	';
    	$subject = $this->translate->translate("Nové heslo")." - ".$name;
    	
    	$this->sendMail($from,$name,$to,$subject,$text);

    	 
    }
    
    private function getData(){
    
    	$data = array(
    		"email" => $this->_request->getPost("email")
    	);
     
    	return $data;
    
    }
    
    private function setData(){
    
    	$filters    = $this->setFilters();
    	$validators = $this->setValidators();
    	$data       = $this->getData();
    
    	$filter = new Zend_Filter_Input($filters, $validators, $data);
        	
    	$this->email = $filter->getUnescaped("email");   	
    
    	return $filter;
    }
    
    private function setFilters(){
    
    	$filters = array(
    	    
    		'email' => 'StripTags'
    		
    	);
    
    	return $filters;
    
    }
    
    private function setValidators(){
    
    	$validators = array(
           'email' => array( 				
                'allowEmpty' => false,
                'EmailAddress'
    	)
    
    	);
    
    	return $validators;
    
    }
    
    private function getBackData(){
    	
    	$this->view->mail = $this->email;
    	
    }
    
	private function  getRandomString($length) {	    
	    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
	    $string = "";    
	
	    for ($p = 0; $p < $length; $p++) {
	        $string .= $characters[mt_rand(0, strlen($characters)-1)];
	    }
	
	    return $string;
	}
	
	private function setSublinks(){
		$subLinks = array();
		$this->view->subLinks = $subLinks;
	}
    
} // end controller class IndexController