<?php

class Content_Models_LinkSectionHeader extends Models_DbTable 
{
    protected $_name = 'link_section_header';
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
    	
}
