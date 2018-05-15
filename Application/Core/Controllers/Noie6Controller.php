<?php

/*
 * Třída obsluhujici clanky na hlavni strance
 *
 * @copyright 2008 Polar Televize Ostrava
 *
 */


class Core_Noie6Controller extends Zend_Controller_Action
{    
		

	function init()
	{
	}

    function indexAction()
	{
		
		Zend_Layout::getMvcInstance()->setLayout("noie");	
		$this->view->version = 6;
        
	} // end of indexAction
	
	
}
