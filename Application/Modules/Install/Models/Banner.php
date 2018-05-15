<?php

class Install_Models_Banner extends Zend_Db_Table
{
	
	function __construct(){
		
				
	}
    
    public function execute(){
    	
    	$this->createTableBanner();
		$this->createFile();
    	
    }
    
	private function createTableBanner(){

    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS banner (
    	    
    	        bannerID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	        title VARCHAR(255) NOT NULL,
    	        file VARCHAR(255) NOT NULL,  
    	        type CHAR(30) NULL,
    	        link VARCHAR(255) NULL,
    	        date DATETIME NOT NULL,
    	        visible TINYINT(1) NOT NULL,
    	        priority TINYINT(4) NOT NULL,
    	        userAdd  VARCHAR(60),
    	        userEdit VARCHAR(60),
    	        dateAdd  DATETIME,
    	        dateEdit DATETIME
    	    
    	    )

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    }
        
    private function createFile(){
    	
    	$script = new Library_Scripts();
    	$script->createFile("./Public/Banner");
    	
    }
                            
}
