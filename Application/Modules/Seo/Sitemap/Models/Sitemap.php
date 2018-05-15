<?php

class Seo_Sitemap_Models_Sitemap extends Models_DbTable
{
    protected $_name = 'seo';
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
    
    	$sql = "SELECT A.typeID, AL.techParam,AL.title,AL.keywords,AL.description,AL.metaTitle,AL.niceTitle, AL.anotation, AL.text, A.dateAdd, A.articleID, P.title as file,A.showFacebook
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
    
}
