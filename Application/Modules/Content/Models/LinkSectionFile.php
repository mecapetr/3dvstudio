<?php

class Content_Models_LinkSectionFile extends Models_DbTable 
{
    protected $_name = 'link_section_file';
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
    
    public function getFiles($where){
    
    	$sql = "
    		SELECT P.photoID,P.title, F.fileID, F.title as 'fileTitle'
            FROM $this->_name LSF
            LEFT JOIN photo P ON(P.photoID = LSF.photoID)
            LEFT JOIN file F ON(F.fileID = LSF.fileID)
            	    					
            WHERE $where  	
        ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    	
}
