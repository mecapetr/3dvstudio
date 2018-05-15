<?php

class Install_Models_Base extends Zend_Db_Table
{
           
    public function createDefaultTables(){
    	
    	//VYTVORENI TABULKY admin_user A VLOZENI PRISLUSNYCH HODNOT
    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS admin_user (
    	    
    	        adminUserID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	        login CHAR(20) NOT NULL,
    	        password CHAR(20) NOT NULL,  
    	        date DATETIME,
    	        type CHAR(10)
    	    
    	    )

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$row = $this->getDefaultAdapter()->fetchRow("SELECT * FROM admin_user WHERE login = 'admin'");
    	if(!$row){
	    	$sql = "
	    	
	    	    INSERT INTO admin_user(login,password,date,type) VALUES('admin','1vis987ion',NOW(),'superadmin');
	    	
	    	";
	    	
	    	$this->getDefaultAdapter()->query($sql);
    	}
    	
    	
    	//VYTVORENI TABULKY module A VLOZENI PRISLUSNYCH HODNOT
    	$sql = "
    	
    	    CREATE TABLE IF NOT EXISTS module (
    	    
    	        moduleID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    	        title varchar(255) NOT NULL,
    	        enabled INT NOT NULL
    	    
    	    )

    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	
    	//VYTVORENI TABULKY module A VLOZENI PRISLUSNYCH HODNOT
    	$sql = "
    	    	
	    	CREATE TABLE IF NOT EXISTS `admin_link` (
	    	  `linkID` int(11) NOT NULL AUTO_INCREMENT,
	    	  `parentID` int(11) NOT NULL,
	    	  `title` varchar(255) NOT NULL,
	    	  `url` varchar(255) NOT NULL, 
	    	  `date` datetime NOT NULL,
	    	  `dateEdit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	    	  `userAdd` varchar(60) NOT NULL DEFAULT '',
	    	  `userEdit` varchar(60) NOT NULL DEFAULT '', 
	    	  `priority` int(11) NOT NULL DEFAULT '0',
	    	  `active` tinyint(2) NOT NULL DEFAULT '1',
	    	  `icon` varchar(60) NOT NULL DEFAULT '',
	    	PRIMARY KEY (`linkID`),
	    	KEY `parentID` (`parentID`)
	    	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    	
    	";
    	 
    	$this->getDefaultAdapter()->query($sql);
    	
    	$row = $this->getDefaultAdapter()->fetchRow("SELECT * FROM module");
    	if(!$row){
	    	$sql = "
	    	
	    	    INSERT INTO module(moduleID,title,enabled) VALUES
	    	    ('1','clankySouvisejici','0'),
	    	    ('2','clankyDoporucene','0'),
	    	    ('3','clankyFormular','0'),
	    	    ('4','clankyUrlVideo','0'),
	    	    ('5','clankyFoto','0'),
	    	    ('6','clankySoubor','0'),
	    	    ('7','clankyMp4Video','0'),
	    	    ('8','odkazyUrlVideo','0'),
	    	    ('9','odkazyFoto','0'),
	    	    ('10','odkazySoubor','0'),
	    	    ('11','odkazyMp4Video','0'),
	    	    ('12','jazykoveMutace','1'),
	    	    ('13','clankyFacebook','0'),
	    	    ('14','odkazyFacebook','0'),
	    	    ('15','responsiveDesign','1');
	    	
	    	";
	    	
	    	$this->getDefaultAdapter()->query($sql);
    	}
    	
    
    	
    	//VYTVORENI TABULKY language A VLOZENI PRISLUSNYCH HODNOT
    	$sql = "
    	
    	    
			CREATE TABLE IF NOT EXISTS language_mutation (
			  languageID INT NOT NULL  AUTO_INCREMENT PRIMARY KEY,
			  title VARCHAR(100) NOT NULL,
			  suffix VARCHAR(4) NOT NULL,
			  file VARCHAR(100) NOT NULL,
			  generateNiceTitle TINYINT(1) NOT NULL DEFAULT '1',
			  enabled TINYINT(1) NOT NULL DEFAULT '0',
			  langTitle CHAR(30) NOT NULL,
			  priority TINYINT(1) NOT NULL
			)
    	";
    	
    	$this->getDefaultAdapter()->query($sql);
    	
    	$row = $this->getDefaultAdapter()->fetchRow("SELECT * FROM language_mutation");
    	if(!$row){
	    	$sql = "
	    	
	    	    
				INSERT INTO language_mutation (languageID, title, suffix, file, generateNiceTitle, enabled,langTitle,priority) VALUES
				(1, 'česky', 'cz', 'cz.jpg', 1, 1,'Česky',1),
				(2, 'slovensky', 'sk', 'sk.jpg', 1, 0,'Slovensky',2),
				(3, 'anglicky', 'en', 'en.jpg', 1, 0,'English',3),
				(4, 'rumunsky', 'ro', 'ro.jpg', 1, 0,'Român',4),
				(5, 'ukrajinsky', 'ua', 'ua.jpg', 0, 0,	'Український',5),
				(6, 'německy', 'de', 'de.jpg', 1, 0, 'Deutsch',6),
				(7, 'polsky', 'pl', 'pl.jpg', 1, 0, 'Polski',7);
	    	
	    	";
	    	
	    	$this->getDefaultAdapter()->query($sql);
    	}
    	
    }
    
    
    public function checkTable($table){
    	$sql = "
    		SHOW TABLES like '$table'
    	";
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
                        
}
