<?php

/**
*
* Trida oblusuhici instalaci admina
 * 
 */


class Install_IndexController extends Library_Adminbase 
{
		
	function init()
    {   
		
		//nastavi layout
		Zend_Layout::getMvcInstance()->setLayout("install");
					
    }
	
    function indexAction()
    {	
	    
    	//jestlize bylo zmacknuto tlacitko instal
    	if($this->_request->getPost("enter")){
    		
	    	$install = new Install_Models_Base();
	    	//vytvoreni tabulky pro prihlaseni do adminu
	    	$install->createDefaultTables();
	    	//vytvoreni ostatnich zaskrtlych tabulek
	    	$this->createOthersTables();
	    	//zprava o uspesnem provedeni
	    	$this->view->message = 'Administrace úspěšně nainstalovaná. <br /> <a href = "/admin" > Zpět na přihlášení </a>';
	    	
    	}
    	
    	$this->_response->insert('header',$this->view->render('placeholders/header.phtml'));
        
    }
    
    private function createOthersTables(){
    	
    	$modules        = $this->_request->getPost('modules');
    	$installModules = new Install_Library_InstallModules();
    	  	
    	if(!empty($modules)){
	    	foreach($modules as $modul){	    		    		
	    		$this->getModule($modul,$installModules);	    		
	    	}
    	}
   	
    	
    }
	private function getModule($modul,$installModules){

		$request = $this->_request;
		
    	switch($modul){
    		
    		case 1: $installModules->installContent($request);
    		        break;
    		case 2: $installModules->installBanner($request);
    		        break;   		
    		case 3: $installModules->installUsers($request);
    		        break;   		
    		case 4: $installModules->installSlider($request);
    		        break;
    		case 5: $installModules->installNewsletter($request);
    		        break;
    		case 6: $installModules->installDayMenu($request);
    		        break;
    		    		
    	}
    	
    }
                
}
