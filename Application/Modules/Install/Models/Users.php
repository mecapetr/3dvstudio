<?php

class Install_Models_Users extends Zend_Db_Table
{

	
	function __construct(){
		
				
	}
    
    public function execute(){
    	
    	$this->createTableUsers();
    	
    }
    
	private function createTableUsers(){

    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS user (
			  userID int(11) NOT NULL AUTO_INCREMENT,
			  email varchar(255) NOT NULL,
			  password varchar(255) NOT NULL,
			  date datetime NOT NULL,
			  degree char(10) NOT NULL DEFAULT '',
			  name varchar(255) NOT NULL DEFAULT '',
			  surname varchar(255) NOT NULL DEFAULT '',
			  spol varchar(255) NOT NULL DEFAULT '',
			  street varchar(255) NOT NULL DEFAULT '',
			  zip int(6) NOT NULL DEFAULT '0',
			  city varchar(255) NOT NULL DEFAULT '',
			  tel varchar(16) NOT NULL DEFAULT '',
			  isConfirmed tinyint(1) NOT NULL,
			  registerCode varchar(256) NOT NULL,
			  blocked tinyint(1) NOT NULL,
			  IP varchar(255) NOT NULL,
			  dateLogin datetime NOT NULL,
			  sendEmails tinyint(1) NOT NULL DEFAULT '0',
  			  deleted tinyint(1) NOT NULL DEFAULT '0',
  			  dateDeleted datetime DEFAULT NULL,
			  PRIMARY KEY (userID)
			);

    	";
    	
    	$this->getDefaultAdapter()->query($sql);

    }
                                
}
