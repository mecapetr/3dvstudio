<?php

class AdminBAse_Actuality_ActualityComment extends DbTable
{
    protected $_name = 'actuality_comment';
    public $lastID;
        
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
    
    public function getAllItems($where,$order){
    	
    	return $this->fetchAll($where,$order);
    }
            
    public function getCount($where = "1"){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM $this->_name WHERE $where");
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
	    

    public function getAllCounts(){
    	$sql = "SELECT  COUNT(actualityID) as count,actualityID	FROM actuality_comment  WHERE showComment = 1  						
					GROUP BY actualityID
    				";
			  
    	$data	= array();
	    $stmt 	= $this->setStatement($sql,$data);  
	    $stmt	->setFetchMode(Zend_Db::FETCH_OBJ); 
      	return  $stmt->fetchAll();
    }

    public function getAllUnconfirmedCounts(){
    	$sql = "SELECT  COUNT(actualityID) as count,actualityID	FROM actuality_comment  WHERE showComment = 0 						
					GROUP BY actualityID
    				";
			  
    	$data	= array();
	    $stmt 	= $this->setStatement($sql,$data);  
	    $stmt	->setFetchMode(Zend_Db::FETCH_OBJ); 
      	return  $stmt->fetchAll();
    }
    
    
        
}
