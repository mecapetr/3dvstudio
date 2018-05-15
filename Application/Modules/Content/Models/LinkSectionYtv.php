<?php

class Content_Models_LinkSectionYtv extends Models_DbTable 
{
    protected $_name = 'link_section_ytv';
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
    
    public function getAllVideos($category,$linkSectionID,$priority,$offset){
    	
    	$catSql = "";
    	if($category != 0)$catSql = "categoryID = ? AND";
    	
    	$sql = "
    		SELECT code,type
    	    FROM $this->_name
    	    
    	    WHERE $catSql linkSectionID = ? AND priority = ?
    	    ORDER BY linkSectionYtvID DESC
    	    LIMIT ?,6 	    	        	    				
    	";
    	    	 
    	$data = array($linkSectionID,$priority,$offset);
    	if($category != 0)$data	= array($category,$linkSectionID,$priority,$offset);
    	
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	
    }
    	
}
