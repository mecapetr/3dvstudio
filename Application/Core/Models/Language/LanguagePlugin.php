<?php 

class Models_Language_LanguagePlugin extends Zend_Controller_Plugin_Abstract

{

    public function routeStartup(Zend_Controller_Request_Abstract $request)

    {  	   
	  $this->refreshLanguage();      
    }
    public function refreshLanguage()
    {
    	  $adminUser = new User_Models_Adminuser();
    	  
    	  //zjistime jestli jiz byl proveden install (existuje tabulka language_mutation), jinak by nam pred instalem vyskocila chyba ze neexistuje tabulka language_mutations
    	  $row = $adminUser->getDefaultAdapter()->fetchRow("SHOW TABLES LIKE  'language_mutation'");
    	  if(!empty($row)){	   
    	  	
    	  	  $lang = new Zend_Session_Namespace("lang");
    	  	
			  $language = new Models_Language_Language();
			  $mainLangData = $language->getMainLang();
			  $allLanguages = $language->getDbLanguages();
			  
			  /*
			  $request_url 		= $_SERVER['REQUEST_URI'];
			  $request_domain 	= $_SERVER['HTTP_HOST'];
			  
			  $l 				= $this->getLanguage($request_domain,$request_url);
			  
			  if($l != "cz"){
			  	$lang->lang = $l;
			  }
			  	
			  if(empty($lang->lang)){
			  	$lang->lang = "cz";
			  }   */
			  
			  //nacteme soubory s jazyky
		      $translate = new Zend_Translate('csv','Application/Languages/'.$allLanguages[0]->suffix.'/'.$allLanguages[0]->suffix.'.csv', $allLanguages[0]->suffix,array('delimiter' => '~','enclosure' => '^'));
		      
		      foreach($allLanguages as $val){
		      	
		      	$translate->addTranslation('Application/Languages/'.$val->suffix.'/'.$val->suffix.'.csv', $val->suffix);
		      	
		      	//$translate->addTranslation('Application/Languages/En/En.csv', 'en');
		      }
		      		      
			  //nastavine prislusny jazyk, pokud neni zadny nastaven nastavime prvni(CZ)
			    $url   = $_SERVER['REQUEST_URI'];
		      $exUrl = explode("/",$url);
		      if(count($exUrl) > 1 && $url != "/"){
		          if($exUrl[1] == "admin" || $exUrl[1] == "core" || $exUrl[1] == "helper" || $exUrl[1] == "cron"){
		      	  		$translate->setLocale("cz");
		      	  		$lang->lang = "cz";
		      	  }else{
			      	if(in_array($exUrl[1],array("pt","en","ca","ru","hu","fr","de","it","pl","cz","sk"))){
			      	  	$translate->setLocale($exUrl[1]);
			      	  	$lang->lang = $exUrl[1];
		      	  	}else{
		      	  		header("Location:/");
		      	  	}
		      	  }
		      	  	
		      }elseif(!empty($lang->lang)){
			      foreach($allLanguages as $val){
			      	      	
			      	if($lang->lang == $val->suffix)
			      		$translate->setLocale($val->suffix);
			      }
		      }else{
			      $translate->setLocale($mainLangData->suffix);
			      $lang->lang = $mainLangData->suffix;
		      }
		      
		      Zend_Controller_Router_Route::getDefaultTranslator($translate);
		      Zend_Registry::set('Zend_Translate', $translate);
    	  }
    }
    
    public function getLanguage($domain){
    	if($domain == "sedacky-nabytek.cz" || $domain == "www.sedacky-nabytek.cz" || $domain == "sedacky.1vision.cz" || $domain == "nabytek-pavel-novy.localhost"){
    		return "cz";
    	}else if($domain == "nabytek-pavel-novy-sk.localhost" || $domain == "sedacky-sk.1vision.cz" || $domain == "www.sedacky-nabytok.sk" || $domain == "sedacky-nabytok.sk"){
    		return "sk";
    	}
    }

}

?>
