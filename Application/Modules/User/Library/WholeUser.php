<?php
class User_Library_WholeUser extends Library_Adminbase
{
			
	//funkce pr�stupn� v�em action
	function init()
    {    
	   
		$this->setLinks();
		$this->view->selected = "Uživatelé";
		$this->setDefault();		
    }
	
	    
	protected function getAdministratorsList($adminuser,$add,$controller,$where,$countWhere = "1",$action = "pridat"){
		
		$sortableList             = false;
		$this->view->sortableList = $sortableList;        
		$this->view->action       = '/admin/uzivatele/'.$add;
				
		$search = $this->_request->getPost("search");
		if($search){
			$user = $this->_request->getPost("user");
			$this->view->allItems = $adminuser->getAllItems("userID = '$user'",null);
			
		}else{
		
			if($sortableList){
	
				$this->view->allItems    = $comodityType->getAllItems(null,"priority");
				$this->view->controller  = $controller;
				
			}else{
				
				$totaly             = $adminuser->getCount($countWhere);		
				$descAsc            = $this->_request->getParam('poradi');
		       
		        
				$this->order($descAsc,$controller,$adminuser,$totaly,$where,"date DESC",$action);
			}
		}
		
	}
		
	protected function setLinks(){
		
		$links = array();
		
		$links[0] = array(
			'title' => "Přidat uživatele",
		    'link'  => "/admin/uzivatele/uzivatel/pridat"
		);
		$links[1] = array(
		    'title' => "Seznam uživatelů",
		    'link'  => "/admin/uzivatele/uzivatel/seznam"
		);
		$links[3] = array(
			'title' => "Administratoři",
		    'link'  => "/admin/uzivatele/administrator/pridat"
		);		
		$this->view->links = $links;
		
	}
    	    	
}

?>