<?php

class Library_Acl 
{    
    protected $acl;
	
	function __construct()
	{

		$_acl = new Zend_Acl();

		$_acl->add(new Zend_Acl_Resource('admin-ex-vozy'));
		$_acl->add(new Zend_Acl_Resource('admin-ex-helper'));
		$_acl->add(new Zend_Acl_Resource('default-helper'));
		$_acl->add(new Zend_Acl_Resource('default-vozy'));
		$_acl->add(new Zend_Acl_Resource('content-w-index'));
		$_acl->add(new Zend_Acl_Resource('day-menu-w-index'));
		$_acl->add(new Zend_Acl_Resource('option-zmena-hesla'));
		$_acl->add(new Zend_Acl_Resource('user-w-registrace'));
		$_acl->add(new Zend_Acl_Resource('core-helper'));
		
		$_acl->add(new Zend_Acl_Resource('admin-ex-objednavky'));
		$_acl->add(new Zend_Acl_Resource('admin-ex-nastaveni-stranek'));
		$_acl->add(new Zend_Acl_Resource('content-clanky'));
		$_acl->add(new Zend_Acl_Resource('content-odkazy'));
		$_acl->add(new Zend_Acl_Resource('content-helper'));
		$_acl->add(new Zend_Acl_Resource('day-menu-index'));
		$_acl->add(new Zend_Acl_Resource('newsletter-newsletter'));
		$_acl->add(new Zend_Acl_Resource('slider-slider'));
		$_acl->add(new Zend_Acl_Resource('user-administrator'));
		$_acl->add(new Zend_Acl_Resource('user-uzivatel'));
		
		$_acl->addRole(new Zend_Acl_Role('2'));
		$_acl->addRole(new Zend_Acl_Role('1'),'2');
		$_acl->addRole(new Zend_Acl_Role('guest'));
		
		$_acl->allow('2','admin-ex-vozy',array('pridat','upravit','seznam','smazat'));	
		$_acl->allow('2','admin-ex-helper');
		$_acl->allow('2','default-helper');
		$_acl->allow('2','default-vozy');
		$_acl->allow('2','content-w-index');
		$_acl->allow('2','day-menu-w-index');
		$_acl->allow('2','option-zmena-hesla');
		$_acl->allow('2','user-w-registrace');
		$_acl->allow('2','core-helper');
		
		$_acl->allow('1','admin-ex-vozy');	
		$_acl->allow('1','admin-ex-objednavky');
		$_acl->allow('1','admin-ex-nastaveni-stranek');
		$_acl->allow('1','content-clanky');		
		$_acl->allow('1','content-odkazy');		
		$_acl->allow('1','content-helper');	
		$_acl->allow('1','day-menu-index');	
		$_acl->allow('1','newsletter-newsletter');	
		$_acl->allow('1','slider-slider');	
		$_acl->allow('1','user-administrator');
		$_acl->allow('1','user-uzivatel');	
		
		$this->setAcl($_acl);
	   	    
	}

	public function getAcl(){
		
		return $this->acl;
		
	}
	
	public function setAcl($acl){
		
		$this->acl = $acl;
		
	}
		
}