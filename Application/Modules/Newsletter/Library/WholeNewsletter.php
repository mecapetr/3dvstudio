<?php
class Newsletter_Library_WholeNewsletter extends Library_Adminbase
{
			
	//funkce pr�stupn� v�em action
	function init()
    {    
	   
		$this->setLinks();
		$this->view->selected = "Newsletter";
		$this->setDefault();	
    }
			
	protected function setLinks(){
		
		$links = array();
		
		$links[0] = array(
			'title' => "Odeslat email",
		    'link'  => "/admin/newsletter/email/odeslat"
		);
		$links[1] = array(
			'title' => "Odeslané emaily",
		    'link'  => "/admin/newsletter/email/odeslane"
		);	
		$links[2] = array(
			'title' => "Přidat kategorii",
		    'link'  => "/admin/newsletter/kategorie/pridat"
		);
		$links[3] = array(
		    'title' => "Seznam kategorií",
			'link'  => "/admin/newsletter/kategorie/seznam"
		);
		$this->view->links = $links;
		
	}
    	    	
}

?>