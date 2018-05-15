<?php

class Install_Models_Slider extends Zend_Db_Table
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
    	
    	$this->createTableSlider();
		//$this->createXml();
		$this->createPhotoFile();
    	
    }
    
	private function createTableSlider(){

    	$sql = "
    	
    	   CREATE TABLE IF NOT EXISTS slider (
			  sliderID int(11) NOT NULL AUTO_INCREMENT,
			  title varchar(255) NOT NULL,
			  text text NOT NULL,
  			  link text NOT NULL,
			  priority tinyint(3) NOT NULL,
			  active tinyint(1) NOT NULL,
			  dateAdd datetime NOT NULL,
			  dateEdit datetime DEFAULT NULL,
			  userAdd varchar(255) DEFAULT '',
			  userEdit varchar(255) DEFAULT '',
			  PRIMARY KEY (sliderID)
		)

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS photo (
    	    
    	        photoID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	        title VARCHAR(255) NOT NULL,
    	        mainPhoto TINYINT(1),
    	        width INT NULL,
    	        height INT NULL
    	            	    
    	    )

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS photo_slider (
    	    
    	        photoID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	        sliderID INT NOT NULL
    	            	    
    	    )

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	
    	$sql = "
    	
		CREATE TABLE IF NOT EXISTS video_slider (
		  videoID int(11) NOT NULL,
		  sliderID int(11) NOT NULL,
		  PRIMARY KEY (videoID),
		  KEY sliderID (sliderID)
		) 
    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "

			CREATE TABLE IF NOT EXISTS photo_temp (
			  photoTempID int(11) NOT NULL AUTO_INCREMENT,
			  userID int(11) NOT NULL,
			  tableType varchar(255) NOT NULL,
			  file varchar(255) NOT NULL,
			  width int(11) NOT NULL,
			  height int(11) NOT NULL,
			  PRIMARY KEY (photoTempID)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
    	$script->createFile("./Public/Images/Temp");
    	$script->createFile("./Public/Images/Slider");
    	
    	if(!file_exists("./Public/Images/Previews"))$script->createFile("./Public/Images/Previews");
    	$script->createFile("./Public/Images/Previews/Slider");
    	
    	if(!file_exists("./Public/Videos/Temp"))$script->createFile("./Public/Videos/Temp");
    	$script->createFile("./Public/Videos/Slider");
    	    	
    }
                            
}
