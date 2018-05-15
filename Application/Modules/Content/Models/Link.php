<?php

class Content_Models_Link extends Models_DbTable 
{
    protected $_name = 'link';
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
    public function getMenu($where = 1){
    	 
    	$sql = "SELECT L.linkID,L.title,L.niceTitle,L.text,L.userAdd,L.otherLink,L.parentID, LL.linkID as 'hasSubmenu'
        	    	FROM link L 
        	        LEFT JOIN link LL ON (L.linkID = LL.parentID AND LL.active = 1)
        	        WHERE $where AND L.parentID = 0
        	        GROUP BY L.linkID
        	        ORDER BY L.priority   	    				
        	";
    	 
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	 
    }
    public function getSubLinks($where = 1){
    
    	$sql = "
    		SELECT L.parentID,L.title,L.niceTitle,L.otherLink, LL.niceTitle as 'parentNiceTitle', P.title as 'file'
            FROM link L 
            	LEFT JOIN link LL ON (L.parentID = LL.linkID AND LL.active = 1)
            	LEFT JOIN photo_link PL ON(L.linkID = PL.linkID)
            	LEFT JOIN photo P ON (P.photoID = PL.photoID)
            WHERE $where AND L.active = 1 AND L.parentID <> 0
            GROUP BY L.linkID
            ORDER BY L.priority   	    				
        ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    public function getFooter(){
    	
    	$sql = "SELECT L.linkID,L.title,L.niceTitle,L.userAdd,L.otherLink,L.parentID, LL.linkID as 'hasSubmenu',L.icon
        	    	FROM link L 
        	        LEFT JOIN link LL ON (L.linkID = LL.parentID AND LL.active = 1)
        	        WHERE L.active = 1 AND L.inFooter = 1 AND L.parentID = 0
        	        GROUP BY L.linkID
        	        ORDER BY L.priority   	    				
        	";
    	 
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	
    }
    
    public function getArticleByNiceTitle($subSubCat){
    	
    	$sql = "SELECT AL.articleID
    	    	    FROM link L 
    	    	    JOIN article_link AL ON (L.linkID = AL.linkID)
    	    	WHERE L.niceTitle = ?
    	    	      	    				
    	";
    	 
    	$data	= array($subSubCat);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchObject();
    	
    }
    
    public function getSublink($articleID,$linkID){
    	 
    	$sql = "SELECT AL.articleID,L.title, L.linkID,L.parentID
        	    	    FROM link L 
        	    	    JOIN article_link AL ON (L.linkID = AL.linkID)
        	    	WHERE AL.articleID = ? AND L.parentID = ?
        	    	      	    				
        	";
    
    	$data	= array($articleID,$linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchObject();
    	 
    }
    public function getTypesForSearch(){
    	
    	$sql = "SELECT L.linkID,L.niceTitle
    	            FROM link L 
    	        	JOIN article_link AL ON (L.linkID = AL.linkID)
    	        	WHERE AL.articleID IN(SELECT ALLL.articleID FROM article_link ALLL WHERE ALLL.linkID = '5' ) AND AL.level = '1'
    	        GROUP BY L.linkID
    	        	    				
    	";
    	
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }
    public function getRecomended(){
    
    	$sql = "SELECT L.title,L.niceTitle,LL.linkID as 'linkID2',L.linkID, L.homepagePhoto, AL.level
               FROM $this->_name L
               LEFT JOIN $this->_name LL ON (LL.linkID = L.parentID)
               LEFT JOIN article_link AL ON (L.LinkID = AL.linkID)
               WHERE L.recommended = 1 AND L.active = 1
               GROUP BY L.linkID
               ORDER BY L.homepagePriority
               
        ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    public function getLevelArticles($where = 1){
    
    	$sql = "SELECT L.linkID, L.parentID,L.otherLink,L.niceTitle,L.title, L.inFooter
                FROM link L
                WHERE $where AND L.active = 1
                ORDER BY L.priority
                
        ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    
    public function getLinkWithLang($lang = "cz",$where = 1, $fetchMode = Zend_Db::FETCH_OBJ){
    
    	$sql = "SELECT 
    				L.linkID,
    				L.parentID,
    				LL.niceTitle
                FROM link L
                JOIN link_lang LL ON (L.linkID = LL.linkID AND LL.lang = '$lang')
                WHERE $where AND L.active = 1
        ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetch();
    
    }
    
    public function getLinksToSitemap($lang){
    
    	$sql = "
    		SELECT L.linkID
        	FROM link L
            WHERE L.active = 1 AND L.linkID NOT IN (SELECT linkID FROM seo_links)
        ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    public function searchLinks($lang = "cz",$searchText){
    
    	$sql = "SELECT
        				L.linkID,
        				L.parentID,
        				LL.niceTitle,
        				LL.title
                    FROM link L
                    JOIN link_lang LL ON (L.linkID = LL.linkID AND LL.lang = '$lang')
                    WHERE LL.title LIKE '%$searchText%' AND L.active = 1
            ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
	
}


