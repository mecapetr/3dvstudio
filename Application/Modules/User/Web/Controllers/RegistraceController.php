<?php

class UserW_RegistraceController extends Library_WebBase
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
        	
	function init()
    {
      	$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->setDefault();

	} // end of init
	
	function indexAction()
	{

		$captchaSpace = new Zend_Session_Namespace("captcha");
		$script       = new Library_Scripts();
		$user         = new User_Models_User();
		
		$captcha      = $this->setCaptcha();
		
		if($this->_request->getPost("register")){
			
			$filter = $this->setData();
			$count = $user->getCount("email = '$this->email'");
			
			if($count == 0){
				
				if($filter->isValid("name")){
					
					if($filter->isValid("surname")){
					
						if($filter->isValid("spol")){
						
														
								if($filter->isValid("email")){
								
									if($filter->isValid("captcha") && $captchaSpace->name == $this->captcha){
									
										if($filter->isValid("pass") && $filter->isValid("pass2") && $this->pass == $this->pass2 ){
																		    
										    $registercode = $this->getRandomString(50);
										    if(empty($this->zip))$this->zip = 0;
										    
										    $data = array(
										    	"degree"       => $this->degree,
									            "name"         => $this->name,
									            "surname"      => $this->surname,
									    	    "spol"         => $this->spol,
									    		"street"       => $this->street,
										    	"zip"          => $this->zip,
										    	"city"         => $this->city,
									            "email"        => $this->email,
									            "tel"          => $this->tel,
										    	"isConfirmed"  => 0,
										    	"blocked"      => 0,
										        "registerCode" => $registercode,
											    "password"     => md5(md5($this->pass."-user!")),
											    "IP"           => $script->getRealIpAddr(),
											    "date"         => date("Y-m-d H:i:s",time()),
											    "dateLogin"    => date("Y-m-d H:i:s",time())
											);
										    
										    $user->getDefaultAdapter()->beginTransaction();
										    try{
											    $user->insertData($data);
											    $lastID = $user->lastID;
											    
											    //$this->addInterest($lastID);
											    
											    $this->sendDataMail($lastID,$registercode);
											    
										    	$user->getDefaultAdapter()->commit();
								 				$this->view->message = "Byli jste úspěšně zaregistrování.<br/>Na vaší e-mailovou adresu byl odeslán potvrzovací e-mail, kde dokončíte svou registraci.";
											    
											}catch(Exception $e){
												$user->getDefaultAdapter()->rollBack();	            // pokud nastane chyba, vrati se zpet tabulka do puvodniho tvaru
												$this->getResponse()->append('main',$e->getMessage());
												$this->getBackData();
												$this->view->error = "Při registraci se vyskytla chyba !";
												
											}

												
										
										}else{
										
											$this->getBackData();
											$this->view->error = "Nezadali jste heslo, případně hesla nejsou stejná!";
										
										}
									}else{
									
										$this->getBackData();
										$this->view->error = "Neopsali jste správně text z obrázku!";
									
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
				$this->view->error = "Uživatel s tímto emailem už existuje!";
			
			}
			
			
		}
		
		$captchaSpace->name = $captcha->getWord();
		
		/*$interest = new Interest();
		$this->view->inter = $interest->getAllItems(null,null);*/
		
		/*$userCategory = new UserCategory();		
		$this->view->allCategories = $userCategory->getAllItems(null,null);*/
		
		$breadcrumb = array(
			"registrace" => "" 
		);
		$this->setBreadCrumb($breadcrumb);
		
    	$this->setTitle(null,true);
    	$this->setDescription("Registrace nových uživatelů, budou se jim zasílat newslettery a uvidí více obsahu na stránkách.");
    	$this->setKeyWords("Registrace, nový uživatel, newsletter");
    	
    	//$this->_response->insert('breadcrumb' , $this->view->render('placeholder/breadcrumb.phtml'));
    	
    	
    } // end of method indexAction
    
    function potvrzeniAction()
    {

    	$user         = new User_Models_User();
    	$filter		  = new Zend_Filter_StripTags();
    	$script		  = new Library_Scripts();
    
    	$userID		  = $filter->filter($this->_request->getParam("id"));
    	$registerCode = $filter->filter($this->_request->getParam("regcode"));
    
    	$where 		  = "userID = $userID AND registerCode  = '$registerCode'";
    	$userData	  = $user->getOneRow($where);
    
    	if(count($userData) > 0){
    			    			
    		if($userData->isConfirmed == 0){
    			
    
    			$data = array(
    			    "isConfirmed"=> 1
    											  					
    			);
    
    			$user->updateData($data,$where);
    			$this->view->message = "Vaše registrace byla úspěšně dokončena. Přihlašovací jméno a heslo jste obdrželi emailem při registraci.";
    
    		}
    		else{
    			$this->_redirect('/');
    		}
    
    	}else{
    		$this->_redirect('/');
    	}
    	
    	$breadcrumb = array(
    		"potvrzení registrace" => "" 
    	);
    	$this->setBreadCrumb($breadcrumb);
    	
    	//nastavení title stránky
    	$this->setTitle("Potvrzení - Registrace");
    	//nastaveni description stránky
    	$this->setDescription("Potvrzení registrace na stránkách ".$this->regDomain);
    	//nastaveni keywords
    	$this->setKeyWords("registrace, registrace svítidel, vip uživatelé ");
    	
    	//$this->_response->insert('breadcrumb' , $this->view->render('placeholder/breadcrumb.phtml'));
    
    }

    function zapomenuteHesloAction()
    {

    	$user       = new User_Models_User();
    	$filter		= new Zend_Filter_StripTags();
    	$script		= new Library_Scripts();
    	
    	$email		= $filter->filter($this->_request->getPost("email"));
    	$send 		= $filter->filter($this->_request->getPost("send"));
    	
    	if($send){
			$where = "email = '$email'";
    		$count = $user->getCount($where);
    		if($count > 0){
    			
    			$password = $script->generatePassword(10);
    			$data = array(
    				"password" => md5(md5($password."-apr!"))
    			);
    			
    			$user->updateData($data,$where);
    			$name = $this->regName;
    			$from = $this->regEmail;
    			$subject = "Nové heslo k příhlášení na server ".$this->regDomain;
	    		$text    = "<p>
	    						Na serveru ".$this->regDomain." Vám bylo vygenerováno nové heslo pro přihlášení.<br />Nyní se můžete přihlásit pod těmito údaji:<br /> 
	    						<label style='width:70px; display:inline-block;'><strong>E-mail:</strong></label> $email <br />
	    						<label style='width:70px; display:inline-block;'><strong>Heslo:</strong></label>$password
	    					</p>
	    					<p style='font-size:10px;'>Toto je pouze informativní email. Z toho důvodu na něj prosím neodpovídejte.</p>";
	    						
    			$to = array($email);
    			    
	    		$this->sendMail($from,$name,$to,$subject,$text);
	    		
    			$this->view->message = "Vaše nové heslo bylo úspěšně odesláno na Vámi zadaný email.";
    
    		}else{
    			$this->view->error = "Zadaný email není v databázi evidován.";
    		}
    
    	}
    	
    	$breadcrumb = array(
    		"zapomenuté heslo" => "" 
    	);
    	$this->setBreadCrumb($breadcrumb);
    	
    	//nastavení title stránky
    	$this->setTitle("Zapomenuté heslo - Registrace");
    	//nastaveni description stránky
    	$this->setDescription("Zapomněli jste své heslo k přihlášení se na stránky ".$this->regDomain." ? Zadejte svůj registrační email a my Vám vygenerujeme nové.");
    	//nastaveni keywords
    	$this->setKeyWords("zapomenuté heslo, heslo, vygenerování hesla ");
    	
    	//$this->_response->insert('breadcrumb' , $this->view->render('placeholder/breadcrumb.phtml'));
    
    }
    
    private function sendDataMail($id,$regCode){
    	 
    	$from    = $this->regEmail;
    	$name    = $this->regName;
    	$to      = array($this->email);
    	$text	 = '<p>Dobrý den,</p>
    					<p>byli jste zaregistrováni na stránkách <strong>'.$this->regDomain.'</strong>. Vaše přihlašovací údaje jsou:</p>
    					<p><strong>email:</strong> '.$this->email.'</p>
    					<p><strong>heslo:</strong> '.$this->pass.' <br /><br/></p>
    					<p><strong>POZOR</strong>, registraci je ale ještě třeba dokončit, pro dokončení klikněte prosím na následující odkaz</p>
    					<p><a href="http://'.$this->regDomain.'/registrace/potvrzeni/'.$id.'/'.$regCode.'">http://'.$this->regDomain.'/registrace/potvrzeni/'.$id.'/'.$regCode.'</a> <br /> <br /></p>
    					<p>S pozdravem a přáním pěkného dne</p>
    					<p>Apro Lux s.r.o.</p>
    	'; 
    	$subject = "Potvrzení registrace na ".$this->regDomain;
    	$this->sendMail($from,$name,$to,$subject,$text);
    	 
    }
    
    private function getData(){
    
    	$data = array(
    	
    	    "degree"  => $this->_request->getPost("degree"),
            "name"    => $this->_request->getPost("name"),
            "surname" => $this->_request->getPost("surname"),
    	    "spol"    => $this->_request->getPost("spol"),
    		"street"  => $this->_request->getPost("street"),
    		"zip"     => $this->_request->getPost("zip"),
    		"city"    => $this->_request->getPost("city"),
            "email"   => $this->_request->getPost("email"),
            "tel"     => $this->_request->getPost("tel"),
    		"pass"    => $this->_request->getPost("pass"),
    		"pass2"   => $this->_request->getPost("pass2"),
    	    "captcha" => $this->_request->getPost("captcha")
           
    	);
    
    
    	return $data;
    
    }
    
    private function setData(){
    
    	$filters    = $this->setFilters();
    	$validators = $this->setValidators();
    	$data       = $this->getData();
    
    	$filter = new Zend_Filter_Input($filters, $validators, $data);
        	
    	$this->degree  = $filter->getUnescaped("degree");
    	$this->name    = $filter->getUnescaped("name");
    	$this->surname = $filter->getUnescaped("surname");
    	$this->spol    = $filter->getUnescaped("spol");
    	$this->street  = $filter->getUnescaped("street");
    	$this->city    = $filter->getUnescaped("city");
    	$this->zip     = $filter->getUnescaped("zip");
    	$this->email   = $filter->getUnescaped("email");
    	$this->tel     = $filter->getUnescaped("tel");
    	$this->pass    = $filter->getUnescaped("pass");
    	$this->pass2   = $filter->getUnescaped("pass2");
    	$this->captcha = $filter->getUnescaped("captcha");
    	
    	$this->interest = $this->_request->getPost("interest");
    
    	return $filter;
    }
    
    private function setFilters(){
    
    	$filters = array(
    	    'degree'  => 'StripTags',
            'name'    => 'StripTags',
            'surname' => 'StripTags',	
            'email'   => 'StripTags',	
    		'spol'    => 'StripTags',
    		'street'  => 'StripTags',
    		'zip'     => 'StripTags',
    		'city'    => 'StripTags',
            'tel'     => 'StripTags',
            'email'   => 'StripTags',
    		'pass'    => 'StripTags',
    		'pass2'   => 'StripTags',
    		'captcha' => 'StripTags'
    	);
    
    	return $filters;
    
    }
    
    private function setValidators(){
    
    	$validators = array(
            'name' => array(  				
                'allowEmpty' => true
    	),
            'surname' => array(  				
                'allowEmpty' => true
    	),
            'degree' => array(  				
                'allowEmpty' => true
    	),
    	    'spol' => array(  				
    	        'allowEmpty' => true
    	),
            'street' => array(  				
                'allowEmpty' => true
    	),
            'zip' => array(  				
                'allowEmpty' => true
    	),
            'city' => array(  				
                'allowEmpty' => true
    	),
            'email' => array( 				
                'allowEmpty' => false,
                'EmailAddress'
    	),
            'tel' => array( 				
                'allowEmpty' => true
    	),
            'pass' => array( 				
                'allowEmpty' => false
    	),
            'pass2' => array( 				
                'allowEmpty' => false
    	),
            'captcha' => array( 				
                'allowEmpty' => false
    	)
    
    	);
    
    	return $validators;
    
    }
    
    private function getBackData(){
    	
    	$this->view->degree   = $this->degree;
    	$this->view->name     = $this->name;
    	$this->view->surname  = $this->surname;
    	$this->view->email    = $this->email;
    	$this->view->tel      = $this->tel;
    	$this->view->spol     = $this->spol;
    	$this->view->proff    = $this->proff;
    	$this->view->interest = $this->interest;
    	$this->view->street   = $this->street;
    	$this->view->zip      = $this->zip;
    	$this->view->city     = $this->city;
    	
    }
    
    private function setCaptcha(){
    
    	
    	$captcha = new Zend_Captcha_Image();
    	$captcha->setDotNoiseLevel(0);
    	$captcha->setLineNoiseLevel(0);
    	$captcha->setName('captcha');
    	$captcha->setFont('./Public/Font/HoboStd.otf');
    	$captcha->setImgDir('./Public/Captcha');
    	$captcha->setWordlen(4);
    	$captcha->setTimeout(1800);
    		
    	$idCaptcha = $captcha->generate();
    	$idCaptcha = $idCaptcha.".png";
    	$this->view->captcha = $idCaptcha;
    	
    	return $captcha;
    	
    }
	private function  getRandomString($length) {	    
	    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
	    $string = "";    
	
	    for ($p = 0; $p < $length; $p++) {
	        $string .= $characters[mt_rand(0, strlen($characters)-1)];
	    }
	
	    return $string;
	}
	
	private function addInterest($lastID){
		
		if($this->interest){
			
			$userInterest = new UserInterest();
			
			foreach($this->interest as $int){
				$data = array(
					"userID"     => $lastID,
					"interestID" => $int
				);
				
				$userInterest->insertData($data);
			}
			
		}
		
	}

    
} // end controller class IndexController