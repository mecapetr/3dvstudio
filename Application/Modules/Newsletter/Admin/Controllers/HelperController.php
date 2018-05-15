<?php
class Newsletter_HelperController extends Newsletter_Library_WholeNewsletter
{
	
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	Zend_Layout::getMvcInstance()->disableLayout();
    }
    
    function getEmailsAction(){
    	

    	$categoriesSelected = $this->_request->getPost("categoriesSelected");
    	
    	$user = new User_Models_User();
    	$this->view->allEmails = $user->getEmailsByFilter($categoriesSelected);
    	
    }
	
}

?>