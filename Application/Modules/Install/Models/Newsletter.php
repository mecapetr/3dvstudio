<?php

class Install_Models_Newsletter extends Zend_Db_Table
{

	private $width;
	private $height;
	private $path;
	
	function __construct(){
		
				
	}
	public function setWidth($width){
    	
    	$this->width = $width;
    	
    }
	public function setHeight($height){
    	
    	$this->height = $height;
    	
    }
	public function setPath($path){
    	
    	$this->path = $path;
    	
    }	
    
    public function execute(){
    	
    	$this->createTableNewsletter();
		//$this->createXml();
		$this->createPhotoFile();
    	
    }
    
	private function createTableNewsletter(){

    	$sql = "
    	
    	   CREATE TABLE IF NOT EXISTS email (
			  emailID int(11) NOT NULL AUTO_INCREMENT,
			  title varchar(255) NOT NULL,
			  text longtext NOT NULL,
			  date datetime NOT NULL,
			  toCategories text NOT NULL,
			  count int(5) NOT NULL,
			  PRIMARY KEY (emailID)
		   )
		

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS attachement (
			  attachementID int(11) NOT NULL AUTO_INCREMENT,
			  emailID int(11) NOT NULL,
			  file varchar(255) NOT NULL,
			  PRIMARY KEY (attachementID)
			)

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	
    		CREATE TABLE IF NOT EXISTS category (
			  categoryID int(11) NOT NULL AUTO_INCREMENT,
			  title varchar(255) NOT NULL,
			  dateAdd datetime NOT NULL,
			  dateEdit datetime NOT NULL,
			  adminAdd varchar(255) NOT NULL,
			  adminEdit varchar(255) NOT NULL,
			  priority tinyint(4) NOT NULL,
			  PRIMARY KEY (categoryID)
			)
    	
    	";
    	 
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	    	
    	    CREATE TABLE IF NOT EXISTS user_category (
			  userID int(11) NOT NULL,
			  categoryID int(11) NOT NULL,
			  PRIMARY KEY (userID,categoryID)
			)
    	    	
    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	    	    	
    		CREATE TABLE IF NOT EXISTS user_email (
			  userID int(11) NOT NULL,
			  emailID int(11) NOT NULL,
			  PRIMARY KEY (userID,emailID)
			)
    	    	    	
    	";
    	 
    	$this->getDefaultAdapter()->query($sql);
    	
    	
    }
    
    private function createXml(){
    	
    	$f = fopen($this->path,"w+");
    	
    	$content = '<?xml version="1.0" encoding="utf-8"?>
    	            <actuality>
    	            	<width>'.$this->width.'</width>
    	            	<height>'.$this->height.'</height>
    	            </actuality>
    	';
    	
    	fwrite($f,$content);
    	fclose($f);
    	
    }
    
    private function createPhotoFile(){
    	
    	$script = new Library_Scripts();
    	$script->createFile("./Public/Attachment");
    	 	    	
    }
                            
}
