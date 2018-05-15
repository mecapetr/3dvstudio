<?php


class Options_Library_WholeOption extends Library_Adminbase 
{
   	
	//funkce pr�stupn� v�em action
	function init()
    {   
    	
    	$this->setLinks();
		$this->setDefault();
			
    }
	       
    private function setLinks(){
    
    	$links = array();
    
    	$links[0] = array(
    			'title' => "Změna hesla",
    		    'link'  => "/admin/nastaveni/zmena-hesla"
    	);
    	$this->view->links = $links;
    
    }
	
   
}
