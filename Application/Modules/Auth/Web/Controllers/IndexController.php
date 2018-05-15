<?php

/**
*
* Trida oblusuhici prihlaseni se do administrace
 * 
 */


class AuthW_IndexController extends Zend_Controller_Action
{
		
    function indexAction()
    {	
	    
    	
    	
    	
		$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->title = "Přihlásit";
		
		//formulďż˝ďż˝ pro vstup do administrace
		$this->view->action = '/login';
		$this->view->buttonText = 'Vstoupit';
		
		if($this->_request->isPost()){
		
		    Zend_Loader::loadClass('Zend_Filter_StripTags');
            $filter = new Zend_Filter_StripTags();
			//ovďż˝ďż˝eni nepovolenďż˝ch tagu 
			$password = trim($filter->filter($this -> _request -> getPost('password')));
			$email = trim($filter->filter($this -> _request -> getPost('email')));
			
			
			//overeni platnosti hesla
			if ($password != '' && $email != '') {
			    
				Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
				Zend_Loader::loadClass('Zend_Auth');
				
			    $db = Zend_Registry::get('db');
                $authAdapter = new Zend_Auth_Adapter_DbTable($db);
                $authAdapter->setTableName('admin_user');
                $authAdapter->setIdentityColumn('email');
                $authAdapter->setCredentialColumn('password');
                $authAdapter->setCredentialTreatment('MD5(MD5(?)) AND isConfirmed = 1 AND blocked = 0 AND deleted = 0');
                $authAdapter->setIdentity($email);
                $authAdapter->setCredential($password."-user!");
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
               
			    //jestli je spravne zadane heslo a jmeno, odkaze do administrace
			    if ($result->isValid()) {
					
                    $data = $authAdapter->getResultRowObject(null,array('password'));
                    $auth->getStorage()->write($data);
															
                    $this->_redirect('/admin/obsah/clanky');
					
                } else {
				
                    $this->view->message = 'Zadali jste špatné uživatelské jméno nebo heslo!';
					
                }
			
			}
			else{
			
		        $this->view->message = 'Nevyplnili jste všechny údaje!';  
			
			}
		
		}
		
		$this->_response->insert('header', $this->view->render('placeholders/header.phtml'));
        
    }
    
    function logoutAction(){

    	Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');

    }
    
    private function checkInstall(){

    	$install = new Install_Models_Base();
    	$checkInstall = $install->checkTable('admin_user');
    	if(count($checkInstall) == 0)$this->_redirect("/admin/install/index");
    	
    }
    
}
