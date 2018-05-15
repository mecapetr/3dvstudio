<?php

class Install_Models_DayMenu extends Zend_Db_Table
{

	private $width;
	private $height;
	private $path;
	
	function __construct(){
		
				
	}

	public function setPath($path){
    	
    	$this->path = $path;
    	
    }	
    
    public function execute(){
    	
    	$this->createTable();
    	
    }
    
	private function createTable(){

    	$sql = "
    	
    	   CREATE TABLE IF NOT EXISTS menu_date (
			  menuDateID int(11) NOT NULL AUTO_INCREMENT,
			  menuFrom date NOT NULL,
			  menuTo date NOT NULL,
			  PRIMARY KEY (menuDateID)
			)
		

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS menu_food (
			  menuFoodID int(11) NOT NULL AUTO_INCREMENT,
			  day varchar(255) NOT NULL,
			  weekNumber int(11) NOT NULL,
  			  year int(11) NOT NULL,
			  supe varchar(255) NOT NULL,
			  weight int(11) NOT NULL,
			  food varchar(255) NOT NULL,
			  price int(11) NOT NULL,
			  PRIMARY KEY (menuFoodID)
			)

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	
    	$sql = "
    	    	
    	    INSERT INTO menu_date (menuDateID, menuFrom, menuTo) VALUES (1, NOW(), NOW());
    	
    	";
    	 
    	$this->getDefaultAdapter()->query($sql);
    	
    }
    
                            
}
