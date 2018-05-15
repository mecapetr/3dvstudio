<?php

class Install_Models_Content extends Zend_Db_Table
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
    	
    	$this->createTableArticle();
    	$this->createTableRelatedArticle();
		$this->createPhotoFile();
    	
    }
    
	private function createTableArticle(){

		
    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS article (
    	    
    	        articleID INT NOT NULL AUTO_INCREMENT,
    	        title VARCHAR(255) NOT NULL,
    	        niceTitle VARCHAR(255) NOT NULL,  
    	        anotation TEXT NOT NULL,
    	        text TEXT NOT NULL,
    	        recommended TINYINT(2) NOT NULL DEFAULT 0,
    	        date DATETIME NOT NULL,
    	        active TINYINT(2) NOT NULL,
    	        view INT NOT NULL,
    	        userAdd  VARCHAR(60),
    	        userEdit VARCHAR(60),
    	        dateAdd  DATETIME,
    	        dateEdit DATETIME,
    	        priority INT(11) NOT NULL,
    	        formTitle VARCHAR(255) NOT NULL DEFAULT '',
    	        showForm TINYINT(1) NOT NULL DEFAULT '0',
    	        showFacebook TINYINT(1) NOT NULL DEFAULT '0',
    	        metaTitle VARCHAR(255) NOT NULL DEFAULT '',
				keywords VARCHAR(255) NOT NULL DEFAULT '',
    	        description VARCHAR(255) NOT NULL DEFAULT '',
    	        allowDelete TINYINT(2) NOT NULL DEFAULT '1',
    	        PRIMARY KEY (articleID),
				KEY priority (priority)
    	    
    	    )

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS photo (
    	    
    	        photoID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	        title VARCHAR(255) NOT NULL,
    	        description TEXT NULL,
    	        mainPhoto TINYINT(1),
    	        width INT NULL,
    	        height INT NULL,
    	        priority INT NOT NULL DEFAULT '0'
    	            	    
    	    )

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS photo_article (
    	    
    	        photoID INT NOT NULL AUTO_INCREMENT,
    	        articleID INT NOT NULL,
    	        PRIMARY KEY (photoID),
		  		KEY actualityID (articleID)    	    
    	    )

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	
    	$sql = "
    	
		CREATE TABLE IF NOT EXISTS video_article (
		  videoID int(11) NOT NULL,
		  articleID int(11) NOT NULL,
		  PRIMARY KEY (videoID),
		  KEY articleID (articleID)
		);
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
			);

    	";
    	   	
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	
			CREATE TABLE IF NOT EXISTS article_comment (
			  articleCommentID int(11) NOT NULL AUTO_INCREMENT,
			  articleID int(11) NOT NULL,
			  date datetime NOT NULL,
			  name varchar(255) NOT NULL,
			  email varchar(255) NOT NULL,
			  text text NOT NULL,
			  showComment tinyint(2) NOT NULL DEFAULT '0',
			  PRIMARY KEY (articleCommentID),
		 	  KEY articleID (articleID)
			)
    	
    	    	";
    	 
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	
    	    CREATE TABLE IF NOT EXISTS video (
    	    	    
    	        videoID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	    	title VARCHAR(255) NULL,
    	    	niceTitle VARCHAR(255) NULL,
    	    	anotation TEXT NULL,
    	    	file VARCHAR(255) NOT NULL,
    	    	footage INT NULL,
    	    	fileSize INT NULL,
    	    	userAdd  VARCHAR(60) NULL,
    	    	userEdit VARCHAR(60) NULL,
    	    	dateAdd  DATETIME NULL,
    	    	dateEdit DATETIME NULL
    	    )
    	
    	";
    	 
    	$this->getDefaultAdapter()->query($sql);
    	 
    	$sql = "
    	    	
    	    CREATE TABLE IF NOT EXISTS video_temp (
    	    	    
    	        videoTempID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	    	userID INT NOT NULL,  
    	    	tableType VARCHAR(255) NOT NULL,
    	    	file VARCHAR(255) NOT NULL,
    	    	footage INT NULL,
    	    	filesize INT NULL	        
    	    )
    	
    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	 
    	$sql = "
    	    	    	
    	    CREATE TABLE IF NOT EXISTS file (
    	    	    	    
    	        fileID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	    	title VARCHAR(255) NULL,
    	        description TEXT NULL,
    	    	size INT(11) NOT NULL,
    	    	ico CHAR(30) NOT NULL,
    	        priority INT NOT NULL DEFAULT '0'
    	    )
    	    	
    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	    	
    	    CREATE TABLE IF NOT EXISTS file_article (
    	    	    	    
    	    	fileID INT NOT NULL AUTO_INCREMENT,
    	        articleID INT NOT NULL,
    	        PRIMARY KEY (fileID),
		  		KEY actualityID (articleID)	        
    	    )
    	    	
    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	 
    	$sql = "
    	
	    	CREATE TABLE IF NOT EXISTS file_temp (
	    	  fileTempID int(11) NOT NULL AUTO_INCREMENT,
	    	  userID int(11) NOT NULL,
	    	  tableType varchar(255) NOT NULL,
	    	  title varchar(255) NOT NULL,
	    	  ico varchar(255) NOT NULL,	
  			  isFromSource tinyint(2) NOT NULL DEFAULT '0' COMMENT 'Jestli se jedná o záznam, kdy soubor připojujeme ale nevkládáme nový',
	    	  PRIMARY KEY (fileTempID),
	    	  KEY userID (userID)
	    	);
    	 
    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	    	    	
    	    	  CREATE TABLE IF NOT EXISTS link (
    	    	    	    	    
    	    	      linkID INT NOT NULL AUTO_INCREMENT,
    	    	      parentID INT NOT NULL,
    	    	      title varchar(255) NOT NULL,
					  niceTitle varchar(255) NOT NULL,
    	        	  text TEXT NOT NULL,
					  date datetime NOT NULL,
					  dateEdit datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  userAdd varchar(60) NOT NULL DEFAULT '',
					  userEdit varchar(60) NOT NULL DEFAULT '',					  
					  otherLink varchar(500) NOT NULL DEFAULT '',
					  priority int(11) NOT NULL DEFAULT '0',
					  showArticles tinyint(1) NOT NULL DEFAULT '0',
    	        	  showFacebook TINYINT(1) NOT NULL DEFAULT '0',
					  active tinyint(2) NOT NULL DEFAULT '1',
					  view int(11) NOT NULL DEFAULT '0',
					  noDelete tinyint(1) NOT NULL DEFAULT '0',
					  inMenu tinyint(1) NOT NULL,
					  inFooter tinyint(1) NOT NULL,
					  metaTitle VARCHAR(255) NOT NULL DEFAULT '',
					  keywords VARCHAR(255) NOT NULL DEFAULT '',
    	        	  description VARCHAR(255) NOT NULL DEFAULT '',
    	        	  allowDelete TINYINT(2) NOT NULL DEFAULT '1',    	        	  
			  allowEditOtherLink TINYINT(2) NOT NULL DEFAULT '1',
					  PRIMARY KEY (linkID),
    			  	  KEY actualityID (parentID)	        
    	    	    )
    	    	    	
    	    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	    	    	
    	    CREATE TABLE IF NOT EXISTS file_link (
    	    	    	    	    
    	        fileID INT NOT NULL AUTO_INCREMENT,
    	    	linkID INT NOT NULL,
    	    	PRIMARY KEY (fileID),
    			KEY actualityID (linkID)	        
    	    )
    	    	    	
    	";
    	 
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	
    	    CREATE TABLE IF NOT EXISTS video_link (
    			  videoID int(11) NOT NULL,
    			  linkID int(11) NOT NULL,
    			  PRIMARY KEY (videoID),
    			  KEY actualityID (linkID)
    		);
    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	
    	    CREATE TABLE IF NOT EXISTS photo_link (
    	    	    
    	        photoID INT NOT NULL AUTO_INCREMENT,
    	    	linkID INT NOT NULL,
    	    	PRIMARY KEY (photoID),
    			KEY actualityID (linkID)    	    
    	    )
    	
    	";
    	 
    	$this->getDefaultAdapter()->query($sql);
    	    	 
    	$sql = "
	    	CREATE TABLE IF NOT EXISTS web_video (
	    	  webVideoID int(11) NOT NULL AUTO_INCREMENT,
	    	  type varchar(255) NOT NULL,
	    	  code varchar(255) NOT NULL,
	    	  userAdd varchar(255) NOT NULL,
	    	  dateAdd datetime NOT NULL,
	    	  userEdit varchar(255) DEFAULT NULL,
	    	  dateEdit datetime DEFAULT NULL,
	    	  PRIMARY KEY (webVideoID)
	    	);
        ";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    CREATE TABLE IF NOT EXISTS web_video_article (
    		    webVideoID INT NOT NULL,
    		    articleID  INT NOT NULL,
    		    PRIMARY KEY (webVideoID),
    		    KEY articleID (articleID)
    		);
    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    CREATE TABLE IF NOT EXISTS web_video_link (
    	        webVideoID INT NOT NULL,
    		    linkID  INT NOT NULL,
    		    PRIMARY KEY (webVideoID),
    		    KEY linkID (linkID)
    	    );
    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    CREATE TABLE IF NOT EXISTS article_link (
    	    	articleLinkID INT NOT NULL AUTO_INCREMENT,
    	        articleID INT NOT NULL,
    		    linkID  INT NOT NULL,
    		    level  INT NOT NULL,
    		    isLastLink  TINYINT(2) NOT NULL,
    		    PRIMARY KEY (articleLinkID),
				KEY articleID (articleID),
				KEY linkID (linkID),
				KEY level (level),
				KEY isLastLink (isLastLink)
    	    );
    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	    	    CREATE TABLE IF NOT EXISTS `link_section` (
    						  `linkSectionID` int(11) NOT NULL AUTO_INCREMENT,
    						  `linkID` int(11) NOT NULL,
    						  `name` varchar(255) NOT NULL DEFAULT '',
  							  `niceName` varchar(255) NOT NULL DEFAULT '',
    						  `color` varchar(10) NOT NULL,
    						  `active` tinyint(1) NOT NULL DEFAULT '1',
    						  `isFooter` tinyint(1) NOT NULL DEFAULT '0',
    						  `dateAdd` datetime NOT NULL,
    						  PRIMARY KEY (`linkSectionID`),
    						  KEY `linkID` (`linkID`)
    						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$this->getDefaultAdapter()->query($sql);						    						
    	$sql = "					
    						CREATE TABLE IF NOT EXISTS `link_section_form` (
    						  `linkSectionFormID` int(11) NOT NULL AUTO_INCREMENT,
    						  `linkSectionID` int(11) NOT NULL,
    						  `groupSectionForm` int(11) NOT NULL,
    						  `title` varchar(255) NOT NULL,
    						  `niceTitle` varchar(255) NOT NULL,
    						  `type` varchar(60) NOT NULL,
    						  `email` varchar(255) NOT NULL,
    						  `priority` int(11) NOT NULL,
    						  `dateAdd` datetime NOT NULL,
    						  `userAdd` varchar(255) NOT NULL,
    						  PRIMARY KEY (`linkSectionFormID`),
    						  KEY `linkSectionID` (`linkSectionID`),
    						  KEY `groupSectionForm` (`groupSectionForm`)
    						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$this->getDefaultAdapter()->query($sql);						    						
    	$sql = "
    						
    						
    						CREATE TABLE IF NOT EXISTS `link_section_form_values` (
    						  `linkSectionFormValueID` int(11) NOT NULL AUTO_INCREMENT,
    						  `linkSectionFormID` int(11) NOT NULL,
    						  `title` varchar(255) NOT NULL,
    						  `value` varchar(255) NOT NULL,
    						  PRIMARY KEY (`linkSectionFormValueID`),
    						  KEY `linkSectionFormID` (`linkSectionFormID`)
    						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$this->getDefaultAdapter()->query($sql);						    						
    	$sql = "
    						
    						
    						CREATE TABLE IF NOT EXISTS `link_section_header` (
    						  `linkSectionHeaderID` int(11) NOT NULL AUTO_INCREMENT,
    						  `linkSectionID` int(11) NOT NULL,
    						  `groupSectionLink` int(11) NOT NULL,
    						  `titleH1` varchar(255) NOT NULL,
    						  `titleH2` text NOT NULL,
    						  `image` varchar(255) NOT NULL,
    						  `priority` int(11) NOT NULL,
    						  `dateAdd` datetime NOT NULL,
    						  `userAdd` varchar(255) NOT NULL,
    						  PRIMARY KEY (`linkSectionHeaderID`),
    						  KEY `linkSectionID` (`linkSectionID`)
    						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$this->getDefaultAdapter()->query($sql);						    						
    	$sql = "
    						
    						
    						CREATE TABLE IF NOT EXISTS `link_section_header_section` (
    						  `linkSectionHeaderSectionID` int(11) NOT NULL AUTO_INCREMENT,
    						  `linkSectionID` int(11) NOT NULL,
    						  `title` varchar(255) NOT NULL,
    						  `titleH2` text NOT NULL,
    						  `align` varchar(60) NOT NULL DEFAULT 'left',
    						  `priority` int(11) NOT NULL,
    						  `dateAdd` datetime NOT NULL,
    						  `userAdd` varchar(255) NOT NULL,
    						  PRIMARY KEY (`linkSectionHeaderSectionID`),
    						  KEY `linkSectionID` (`linkSectionID`)
    						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$this->getDefaultAdapter()->query($sql);						    						
    	$sql = "
    						
    						
    						CREATE TABLE IF NOT EXISTS `link_section_link` (
    						  `linkSectionLinkID` int(11) NOT NULL AUTO_INCREMENT,
    						  `linkSectionID` int(11) NOT NULL,
    						  `groupSectionLink` int(11) NOT NULL,
    						  `linkID` int(11) NOT NULL,
    						  `isSublink` tinyint(4) NOT NULL,
    						  `colCount` int(11) NOT NULL,
    						  `text` text NOT NULL,
    						  `titleH2` text NOT NULL,
    						  `image` varchar(255) NOT NULL,
    						  `url` varchar(255) NOT NULL,
    						  `priority` int(11) NOT NULL,
    						  `dateAdd` datetime NOT NULL,
    						  `userAdd` varchar(255) NOT NULL,
    						  PRIMARY KEY (`linkSectionLinkID`),
    						  KEY `linkSectionID` (`linkSectionID`),
    						  KEY `linkID` (`linkID`),
    						  KEY `isSublink` (`isSublink`),
    						  KEY `groupSectionLink` (`groupSectionLink`)
    						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$this->getDefaultAdapter()->query($sql);	
						    						
    	$sql = "
    						
    						
    						CREATE TABLE IF NOT EXISTS `link_section_text` (
    						  `linkSectionTextID` int(11) NOT NULL AUTO_INCREMENT,
    						  `linkSectionID` int(11) NOT NULL,
    						  `text` text NOT NULL,
    						  `priority` int(11) NOT NULL,
    						  `dateAdd` datetime NOT NULL,
    						  `userAdd` varchar(255) NOT NULL,
    						  PRIMARY KEY (`linkSectionTextID`),
    						  KEY `linkSectionID` (`linkSectionID`)
    						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    	    	    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
	    	CREATE TABLE IF NOT EXISTS `link_section_file` (
	    	  `linkSectionFileID` int(11) NOT NULL AUTO_INCREMENT,
	    	  `linkSectionID` int(11) NOT NULL,
	    	  `photoID` int(11) NOT NULL,
	    	  `fileID` int(11) NOT NULL,
	    	  `priority` int(11) NOT NULL,
	    	  `userAdd` varchar(60) NOT NULL,
	    	  `dateAdd` datetime NOT NULL,
	    	PRIMARY KEY (`linkSectionFileID`),
	    	KEY `linkSection` (`linkSectionID`),
	    	KEY `photoID` (`photoID`),
	    	KEY `fileID` (`fileID`),
	    	KEY `priority` (`priority`)
	    	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    	
    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	CREATE TABLE `link_section_map` (
    	`linkSectionMapID` int(11) NOT NULL AUTO_INCREMENT,
    	`linkSectionID` int(11) NOT NULL,
    	`groupSectionLink` int(11) NOT NULL,
    	`title` varchar(255) NOT NULL,
    	`lat` varchar(30) NOT NULL,
    	`long` varchar(30) NOT NULL,
    	`priority` int(11) NOT NULL,
    	`dateAdd` datetime NOT NULL,
    	`userAdd` varchar(60) NOT NULL,
    	PRIMARY KEY (`linkSectionMapID`),
    	KEY `linkSectionID` (`linkSectionID`),
    	KEY `groupSectionLink` (`groupSectionLink`)
    	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
	    	CREATE TABLE IF NOT EXISTS `link_section_ytv` (
	    	  `linkSectionYtvID` int(11) NOT NULL AUTO_INCREMENT,
	    	  `linkSectionID` int(11) NOT NULL,
	    	  `categoryID` int(11) NOT NULL,
	    	  `code` varchar(60) NOT NULL,
	    	  `type` varchar(255) NOT NULL DEFAULT '',
	    	  `priority` int(11) NOT NULL,
	    	  `dateAdd` datetime NOT NULL,
	    	  `userAdd` varchar(60) NOT NULL,
	    	PRIMARY KEY (`linkSectionYtvID`),
	    	KEY `linkSectionID` (`linkSectionID`),
	    	KEY `categoryID` (`categoryID`)
	    	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	CREATE TABLE  `link_section_article` (
	    	`linkSectionArticleID` INT NOT NULL AUTO_INCREMENT,
	    	`linkSectionID` INT NOT NULL ,
	    	`type` TINYINT( 1 ) NOT NULL ,
	    	`pageCount` INT NOT NULL ,
	    	`newCount` INT NOT NULL ,
	    	`url` VARCHAR( 255 ) NOT NULL ,
	    	`priority` INT NOT NULL ,
	    	`userAdd` VARCHAR( 60 ) NOT NULL ,
	    	`dateAdd` DATETIME NOT NULL,
	    	PRIMARY KEY (`linkSectionArticleID`),
		    KEY `linkSectionID` (`linkSectionID`)
	    	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    		CREATE TABLE IF NOT EXISTS `category` (
			  `categoryID` int(11) NOT NULL,
			  `priority` int(11) NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `niceTitle` varchar(255) NOT NULL,
			  `active` tinyint(1) NOT NULL,
			  `dateAdd` datetime NOT NULL,
			  `dateEdit` datetime NOT NULL,
			  `userAdd` varchar(60) NOT NULL,
			  `userEdit` varchar(60) NOT NULL DEFAULT '',
	    	PRIMARY KEY (`categoryID`),
	    	KEY `priority` (`priority`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    	";
    	$this->getDefaultAdapter()->query($sql);
    	
    	$sql = "
    	    	
    	    INSERT INTO admin_link (`linkID`, `parentID`, `title`, `url`, `date`, `dateEdit`, `userAdd`, `userEdit`, `priority`, `active`, `icon`) VALUES 
    	    ('1', '0', 'Obsah', '/admin/obsah/clanky/pridat', '2015-08-05 14:16:51', '0000-00-00 00:00:00', '', '', '1', '1','list-alt'), 
    	    ('2', '1', 'Přidat článek', '/admin/obsah/clanky/pridat', '2015-08-05 14:16:51', '0000-00-00 00:00:00', '', '', '1', '1',''), 
    	    ('3', '1', 'Seznam článků', '/admin/obsah/clanky/seznam', '2015-08-05 14:16:51', '0000-00-00 00:00:00', '', '', '2', '1',''), 
    	    ('4', '1', 'Přidat odkaz', '/admin/obsah/odkazy/pridat', '2015-08-05 14:16:51', '0000-00-00 00:00:00', '', '', '3', '1',''), 
    	    ('5', '1', 'Seznam odkazů', '/admin/obsah/odkazy/seznam', '2015-08-05 14:16:51', '0000-00-00 00:00:00', '', '', '4', '1','');	   
    	
    	";
    	 
    	$this->getDefaultAdapter()->query($sql);
    	
    }
    
	private function createTableRelatedArticle(){

    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS related_article (
				articleID INT NOT NULL ,
				relatedArticleID INT NOT NULL ,
				PRIMARY KEY (  articleID ,  relatedArticleID )
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
    	
    	$script->createFile("./Public/Images/Temp");
    	$script->createFile("./Public/Images/Article");
    	$script->createFile("./Public/Images/Link");
    	$script->createFile("./Public/Images/Link/Section");
    	$script->createFile("./Public/Images/Link/Section/Header");
    	$script->createFile("./Public/Images/Link/Section/Link");
    	$script->createFile("./Public/Images/Link/Section/File");
    	    	
    	$script->createFile("./Public/Images/Previews");
    	$script->createFile("./Public/Images/Previews/Article");
    	$script->createFile("./Public/Images/Previews/Link");
    	$script->createFile("./Public/Images/Previews/Temp");
    	
    	$script->createFile("./Public/Videos/Temp");
    	$script->createFile("./Public/Videos/Article");
    	$script->createFile("./Public/Videos/Link");
    	
    	$script->createFile("./Public/Files/Temp");
    	$script->createFile("./Public/Files/Article");
    	$script->createFile("./Public/Files/Link");
    	$script->createFile("./Public/Files/Link/Section");
    	$script->createFile("./Public/Files/Link/Section/File");
    	    	
    }
                            
}
