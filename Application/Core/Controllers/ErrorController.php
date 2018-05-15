<?php

/*
 * Třída obsluhujici clanky na hlavni strance
 *
 * @copyright 2008 Polar Televize Ostrava
 *
 */


class Core_ErrorController extends Library_WebBase 
{    


	function init()
	{
	    Zend_Layout::getMvcInstance()->setLayout("error"); 
	   	  
	}

	function indexAction()
	{
	     
	    $errorMessage = "Zde nemáte přístup"; 
	    $this->view->error = $errorMessage;
	    
	    $this->setTitle($errorMessage,false);

 		
	} // end of indexAction
	
	function nodataAction()
	{
	     
	    $errorMessage = "Zatím zde nejsou žádné údaje"; 
	    $this->view->error = $errorMessage;
	    
	    $this->setTitle($errorMessage,false);

        $this->renderScript("error/index.phtml");

 		
	} // end of indexAction
	
	function errorAction()
	{
		
		    $errorMessage = "Požadovaná stránka neexistuje";
			$this->view->error = $errorMessage;
		
		    $errors = $this->_getParam('error_handler');
			if($errors){
	            switch ($errors->type) {

		            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
		
		            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
		
		            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
		
		                // 404 error -- controller or action not found
		
		                //$this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
	
		                // ... get some output to display...
		                $this->view->error = $errorMessage;
		                
		                break;
		
		            default:
		
		                // application error; display error page, but don't
		
		                // change status code
		
		                break;

        		}
        	
			}	
			
	    $this->setTitle($errorMessage,false);
        $this->renderScript("error/index.phtml");
    
 		
	} // end of indexAction
			
}
