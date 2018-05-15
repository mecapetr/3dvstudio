<?php

class Content_Models_LinkSection extends Models_DbTable 
{
    protected $_name = 'link_section';
    public $lastID;
    
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
    
    public function getAllItems($where,$order){
    	
    	return $this->fetchAll($where,$order);
    } 
        
    public function getCount(){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM $this->_name ");
    }
    
    public function deleteData($where){
    	
    	$this->delete($where);
    	
    }
    public function getOneRow($where){
    	
    	return $this->fetchRow($where);
    }
    public function updateData($data,$where){
    	
    	$this->update($data,$where);
    	
    }
    public function getFooter(){
    	
    	$sql = "SELECT L.linkID
    	            FROM link L 
    	        	JOIN article_link AL ON (L.linkID = AL.linkID)
    	        WHERE AL.level < 2 AND L.inFooter = 1
    	        GROUP BY A.articleID
    	        ORDER BY A.priority
    	        	    				
    	";
    	
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	
    }
    
    public function getHeaderElements($linkID){
    	 
    	$sql = "
    		SELECT LS.niceName as 'secNiceName',LS.name as 'secName', LS.color, LS.wide,LS.isFooter,LSH.linkSectionID,LSH.titleH1,LSH.titleH2,LSH.image,LSH.linkSectionHeaderID,LSH.groupSectionLink,LSH.priority
        	FROM $this->_name LS
        		JOIN link_section_header LSH ON (LSH.linkSectionID = LS.linkSectionID)
        	WHERE LS.linkID = ? AND LS.active = 1
        	        	    				
        ";
    	 
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	 
    }
    
    public function getMapElements($linkID){
    
    	$sql = "
    	SELECT LS.niceName as 'secNiceName',LS.name as 'secName', LS.color, LS.wide,LS.isFooter,LSH.linkSectionID,LSH.title,LSH.lat,LSH.long,LSH.linkSectionMapID,LSH.groupSectionLink,LSH.priority,LSH.elementWidth
    	FROM $this->_name LS
    	JOIN link_section_map LSH ON (LSH.linkSectionID = LS.linkSectionID)
    	WHERE LS.linkID = ? AND LS.active = 1
    
    	";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    public function getArticleElements($linkID){
    
    	$sql = "
        		SELECT 
        			LS.niceName as 'secNiceName',LS.name as 'secName', LS.color, LS.wide,LS.isFooter,
        			LSH.linkSectionID,LSH.type,LSH.linkID,LSH.pageCount,LSH.newCount,LSH.linkSectionArticleID,LSH.priority,
        			L.title as 'linkTitle'
            	FROM $this->_name LS
            		JOIN link_section_article LSH ON (LSH.linkSectionID = LS.linkSectionID)
            		LEFT JOIN link L ON (L.linkID = LSH.linkID)
            	WHERE LS.linkID = ? AND LS.active = 1
            	        	    				
            ";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    public function getLinkElements($linkID){
    
    	$sql = "
        		SELECT 
        			LS.niceName as 'secNiceName',LS.name as 'secName', LS.color, LS.wide,LS.isFooter,
        			LSH.linkSectionID,LSH.text,LSH.titleH2,LSH.image,LSH.linkSectionLinkID,LSH.priority, 
        			LSH.groupSectionLink, LSH.colCount, LSH.linkID, LSH.isSublink, GROUP_CONCAT(LSS.niceName,'~',LSS.name) as 'secNiceName',LSH.elementWidth
            	FROM $this->_name LS
            		JOIN link_section_link LSH ON (LSH.linkSectionID = LS.linkSectionID)
            		LEFT JOIN $this->_name LSS ON (LSS.linkID = LSH.linkID)
            	WHERE LS.linkID = ? AND LS.active = 1
            	GROUP BY LSH.linkSectionLinkID
            	        	    				
            ";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    public function getSublinkElements($linkID){
    
    	$sql = "
            SELECT LS.niceName as 'secNiceName',LS.name as 'secName', LS.color, LS.wide,LS.isFooter,LSH.linkSectionID,LSH.text,LSH.title,LSH.linkSectionSublinkID,LSH.priority, LSH.groupSectionLink, LSH.linkID, LSH.url, LSH.colCount
            FROM $this->_name LS
            	JOIN link_section_sublink LSH ON (LSH.linkSectionID = LS.linkSectionID)
            WHERE LS.linkID = ? AND LS.active = 1
                	        	    				
        ";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    public function getSectionHeaderElements($linkID){
    
    	$sql = "
        		SELECT LS.niceName as 'secNiceName',LS.name as 'secName', LS.color, LS.wide,LS.isFooter,LSH.linkSectionID,LSH.title,LSH.titleH2,LSH.align,LSH.hType,LSH.linkSectionHeaderSectionID,LSH.priority,LSH.elementWidth
            	FROM $this->_name LS
            		JOIN link_section_header_section LSH ON (LSH.linkSectionID = LS.linkSectionID)
            	WHERE LS.linkID = ? AND LS.active = 1
            	        	    				
            ";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    public function getTextElements($linkID){
    
    	$sql = "
        	SELECT LS.niceName as 'secNiceName',LS.name as 'secName', LS.color, LS.wide,LS.isFooter,LSH.linkSectionID,LSH.text,LSH.linkSectionTextID,LSH.priority,LSH.elementWidth, LSH.elementFloat
            FROM $this->_name LS
            	JOIN link_section_text LSH ON (LSH.linkSectionID = LS.linkSectionID)
            WHERE LS.linkID = ? AND LS.active = 1
                	        	    				
        ";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    public function getFormElements($linkID){
    
    	$sql = "
            	SELECT LS.niceName as 'secNiceName',LS.name as 'secName', LS.color, LS.wide,LS.isFooter,LSH.niceTitle,LSH.linkSectionID,LSH.title,LSH.type,LSH.email,LSH.linkSectionFormID,LSH.priority, LSH.groupSectionForm,LSH.elementWidth
                FROM $this->_name LS
                	JOIN link_section_form LSH ON (LSH.linkSectionID = LS.linkSectionID)
                WHERE LS.linkID = ? AND LS.active = 1
                ORDER BY LSH.linkSectionFormID
                    	        	    				
            ";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }  

    
    public function getYtvElements($linkID){
    
    	$sql = "
            	SELECT LS.niceName as 'secNiceName',LS.name as 'secName', LS.color, LS.wide,LS.isFooter,LSH.linkSectionID,LSH.code,LSH.type,LSH.linkSectionYtvID,LSH.priority,LSH.categoryID,LSH.elementWidth
                FROM $this->_name LS
                	JOIN link_section_ytv LSH ON (LSH.linkSectionID = LS.linkSectionID)
                WHERE LS.linkID = ? AND LS.active = 1
                ORDER BY LSH.linkSectionYtvID DESC
                    	        	    				
            ";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }  
    
    public function getSectionCategories($linkSectionID,$priority){
    
    	$sql = "
            	SELECT DISTINCT(C.title), C.categoryID
                FROM link_section_ytv LSY
                	JOIN category C ON (C.categoryID = LSY.categoryID)
                WHERE LSY.linkSectionID = ? AND LSY.priority = ?
                ORDER BY C.priority
                    	        	    				
            ";
    
    	$data	= array($linkSectionID,$priority);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    } 

    public function getPhotoFileElements($linkID){
    
    	$sql = "
        	SELECT LS.niceName as 'secNiceName',LS.name as 'secName', LS.wide, LS.color,LS.isFooter,LSH.linkSectionID,LSH.linkSectionFileID,LSH.priority,LSH.elementWidth,LSH.isSlider,LSH.showDetail,P.title as 'photoTitle',F.title as 'fileTitle',F.ico,F.fileID,P.photoID
            FROM $this->_name LS
            	JOIN link_section_file LSH ON (LSH.linkSectionID = LS.linkSectionID)
            	LEFT JOIN photo P ON (P.photoID = LSH.photoID)
            	LEFT JOIN file F ON (F.fileID = LSH.fileID)
            WHERE LS.linkID = ? AND LS.active = 1   	        	    				
        ";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    	
}
