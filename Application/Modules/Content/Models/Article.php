<?php

class Content_Models_Article extends Models_DbTable
{
    protected $_name = 'article';
    public $lastID;
    
    public function insertData($data){
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    }
    public function getAllItems($where=null,$order=null){
    	return $this->fetchAll($where,$order);
    }

	public function getAllItemsOrderLimit($limitFrom,$limitTo){
    	return $this->fetchAll($this->select()->order("priority")->limit($limitTo,$limitFrom));
    }
    public function getCount(){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM $this->_name ");
    }

    public function getCountByCustomer($where){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM $this->_name WHERE $where ");
    }
    
    public function getAllItemsWhereOrder($where){
    	
    	return $this->getDefaultAdapter()->fetchAll("SELECT * FROM equipment WHERE $where ORDER BY priority ");
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
    
    public function getMaxPriority(){

    	$sql = "
    		SELECT MAX(priority) FROM $this->_name
    	";
    	return $this->getDefaultAdapter()->fetchOne($sql);
    }
    
    public function updateView($id){
    
    	$sql = " UPDATE $this->_name SET view = view + 1 WHERE articleID = ? ";
    
    	$data	= array($id);
    	$stmt 	= $this->setStatement($sql,$data);
    	    
    }
    
    public function getArticle($id,$lang = "cz"){
    
    	$sql = "SELECT AL.title,AL.keywords,AL.description,AL.metaTitle,AL.niceTitle, AL.anotation, AL.text, A.dateAdd, A.articleID, P.title as file,A.showFacebook
            	FROM photo P 
            	    LEFT JOIN photo_article PA  ON(P.photoID    = PA.photoID AND P.mainPhoto = 1)
        	    	RIGHT JOIN $this->_name A 	ON (A.articleID = PA.articleID)
        	    	JOIN article_lang AL ON (A.articleID = AL.articleID AND lang = '$lang')
            	WHERE A.active = 1 AND A.articleID = ?
            	
        ";
    
    	$data	= array($id);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchObject();
    
    }
    
    public function getCountArticles($linkID){
    
    	$sql = "SELECT A.title
            	        FROM photo P 
            	        	LEFT JOIN photo_article PA  ON(P.photoID    = PA.photoID AND P.mainPhoto = 1)
        	    			RIGHT JOIN $this->_name A 	ON (A.articleID = PA.articleID)
        	    			JOIN article_link AL ON (A.articleID = AL.articleID AND AL.linkID = ?)
        	    			JOIN link L ON(AL.linkID = L.linkID)
            	    	WHERE A.active = 1
            	    	GROUP BY A.articleID
            	    				
            	    	";
    
    	$data	= array($linkID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->rowCount();
    
    }
    
    public function getArticles($order,$limit2,$limit1,$linkID,$lang = "cs"){
    	 
    	$sql = "SELECT 
    				ALA.title,ALA.niceTitle, ALA.anotation, A.dateAdd, A.articleID, L.otherLink as 'link', 
    				P.title as file,A.showFacebook, GROUP_CONCAT(ARLI.linkID) as 'articleOtherLinks'
        	    				FROM photo P 
        	    					LEFT JOIN photo_article PA  ON(P.photoID    = PA.photoID AND P.mainPhoto = 1)
        	    					RIGHT JOIN $this->_name A 	ON (A.articleID = PA.articleID)
        	    					JOIN article_lang ALA ON(ALA.articleID = A.articleID AND ALA.lang = ?)
        	    					JOIN article_link AL ON (A.articleID = AL.articleID AND AL.linkID = ?)
        	    					JOIN link L ON(AL.linkID = L.linkID)
        	    					
        	    					JOIN article_link ARLI ON (AL.articleID = ARLI.articleID)
        	    					
        	    				WHERE A.active = 1
        	    				GROUP BY A.articleID
        	    				ORDER BY $order
        	    				LIMIT ?,?
        	    	";
    
    	$data	= array($lang,$linkID,$limit1,$limit2);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	 
    }
    
    public function getArticlesToSitemap($lang = "cs"){
    
    	$sql = "SELECT ALA.niceTitle, A.articleID, AL.linkID
            	    FROM $this->_name A
            	    JOIN article_lang ALA ON(ALA.articleID = A.articleID AND ALA.lang = ?)
            	    JOIN article_link AL ON (A.articleID = AL.articleID AND isLastLink = 1)
				            	    					
            	    WHERE A.active = 1 AND AL.linkID NOT IN (SELECT linkID FROM seo_articles)
            	    
        ";
    
    	$data	= array($lang);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    public function getLevelArticles($level){
    
    	$sql = "SELECT A.title,A.niceTitle, A.anotation, A.dateAdd, A.articleID, L.niceTitle as link, L.otherLink,L.linkID, P.title as file,A.showFacebook
            	    				FROM photo P 
            	    					LEFT JOIN photo_article PA  ON(P.photoID    = PA.photoID AND P.mainPhoto = 1)
            	    					RIGHT JOIN $this->_name A 	ON (A.articleID = PA.articleID)
            	    					JOIN article_link AL ON (A.articleID = AL.articleID AND AL.level = ?)
            	    					JOIN link L ON(AL.linkID = L.linkID)
            	    				WHERE A.active = 1
            	    				GROUP BY A.articleID
            	    				
            	    	";
    
    	$data	= array($level);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    public function getAllLevelLinkArticles($linkID,$where = 1,$order = "A.priority"){
    	
    	$sql = "SELECT A.title,A.niceTitle, A.anotation,A.view, A.text, A.date, A.articleID,A.allowDelete,A.showFacebook
    	    				FROM  $this->_name A 	JOIN article_link AL ON (A.articleID = AL.articleID AND AL.linkID = '$linkID') 
    	    										JOIN link L ON (L.linkID = AL.linkID)    	    				
    	    				WHERE $where
    	    				GROUP BY A.articleID
    	    				ORDER BY $order
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	
    }
    public function getOneLevelLinkArticles($linkID,$where = 1,$order = "priority"){
    	
    	$sql = "SELECT A.title,A.niceTitle, A.anotation,A.view, A.text, A.date, A.articleID,A.allowDelete,A.showFacebook
    	    				FROM  $this->_name A JOIN article_link AL ON (A.articleID = AL.articleID AND AL.linkID = '$linkID' AND AL.isLastLink = '1')    	    				
    	    				WHERE $where
    	    				GROUP BY A.articleID
    	    				ORDER BY $order
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	
    }
    public function getNoLinkArticles($where = 1, $order = "A.priority"){
    	
    	$sql = "SELECT A.title,A.niceTitle, A.anotation,A.view, A.text, A.date, A.articleID,A.allowDelete,A.showFacebook
    	    				FROM  $this->_name A LEFT JOIN article_link AL ON (A.articleID = AL.articleID )    	    				
    	    				WHERE AL.articleID IS NULL AND $where
    	    				ORDER BY $order
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }
    
    public function getRecomended(){
    
    	$sql = "SELECT A.title,A.niceTitle, A.anotation,A.view, A.text, A.date, A.articleID,A.allowDelete, P.title as 'photoTitle',A.showFacebook
                	    				FROM photo P 
                	    					LEFT JOIN photo_article PA  ON(P.photoID    = PA.photoID AND P.mainPhoto = 1)
                	    					RIGHT JOIN $this->_name A 	ON (A.articleID = PA.articleID)
                	    					JOIN article_link AL ON (A.articleID = AL.articleID )
                	    				WHERE A.recommended = 1 AND A.active = 1
                	    				GROUP BY A.articleID
                	    				ORDER BY A.priority
                	    	";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
        
}
