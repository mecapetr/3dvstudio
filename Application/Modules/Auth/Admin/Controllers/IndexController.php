<?php

/**
*
* Trida oblusuhici prihlaseni se do administrace
 * 
 */


class Auth_IndexController extends Library_WholeWeb
{
		
    function indexAction()
    {	
	    
    	Zend_Layout::getMvcInstance()->setLayout("adminlogin");
    	
    	//otestuje jestli je třeba nainstalovat admina
    	$this->checkInstall();
    	
		$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->title = "Administrace";
		
		//formulďż˝ďż˝ pro vstup do administrace
		$this->view->action = '/admin/';
		$this->view->buttonText = 'Vstoupit';
		
		if($this->_request->isPost()){
		
		    Zend_Loader::loadClass('Zend_Filter_StripTags');
            $filter = new Zend_Filter_StripTags();
			//ovďż˝ďż˝eni nepovolenďż˝ch tagu 
			$password = trim($filter->filter($this -> _request -> getPost('password')));
			$login = trim($filter->filter($this -> _request -> getPost('login')));
			
			
			//overeni platnosti hesla
			if ($password != '' && $login != '') {
			    
				Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
				Zend_Loader::loadClass('Zend_Auth');
				                                    
				$scripts 	= new Library_Scripts();
				$adminUser 	= new User_Models_Adminuser();
			    $db = Zend_Registry::get('db');
                $authAdapter = new Zend_Auth_Adapter_DbTable($db);
                $authAdapter->setTableName('admin_user');
                $authAdapter->setIdentityColumn('login');
                $authAdapter->setCredentialColumn('password');
                $authAdapter->setIdentity($login);
                $authAdapter->setCredential($password);
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
               
			    //jestli je spravne zadane heslo a jmeno, odkaze do administrace
			    if ($result->isValid()) {
                    $data = $authAdapter->getResultRowObject(null,array('password'));
                    $auth->getStorage()->write($data);
                       
					           $noLCT 		= $scripts->generatePassword(200);
					           $adminUser->updateData(array(
						          "noLogoutToken" => $noLCT
					           ), "adminUserID = $data->adminUserID");
                    setcookie("noLCT",$noLCT, time() + 86400,"/admin/");
                    
                    $adminLink 		= new Install_Models_AdminLink();
					           $modules 	= new Models_Module();
                    
                    $eshopEnabled	= $modules->getOneRow("title = 'eshopEnabled'");
                    $aLinkData 		= $adminLink->getOneRow("linkID = '2'");
                    
                    $seo = new Seo_Sitemap_Models_Sitemap();
                    $seoData = $seo->getOneRow("seoID = 1");
                    
                    if($seoData->doUpdate == 1){
                      $siteMapFunctions = new Seo_Sitemap_Library_SitemapFunctions();
        						  $siteMapFunctions->generate($this->regDomain["cz"]);
        						  $seo->updateData(array("doUpdate" => 0), "seoID = 1");
        			    	}
                    
                    if($eshopEnabled->enabled){
                    	$this->_redirect('/admin/eshop/produkt/seznam');
                    }else{
                    	$this->_redirect('/admin/obsah/clanky/seznam');
                    }
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
    	setcookie("noLCT", "", time() - 3600,"/admin/");  
        $this->_redirect('/admin');

    }
    
    private function checkInstall(){

    	$install = new Install_Models_Base();
    	$checkInstall = $install->checkTable('admin_user');
    	if(count($checkInstall) == 0)$this->_redirect("/admin/install/index");
    	
    }
    
}
