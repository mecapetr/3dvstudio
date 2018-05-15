<?php

class User_Models_UserCategory extends Models_DbTable
{
    protected $_name = 'user_category';
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
    public function addCategories($userID,$categories){
    	
    	$sql = "
        	    INSERT INTO $this->_name VALUES
        	    
        	";
    	
		$length = count($categories);    	
    	for($i=0; $i < $length; $i++){
    		if($i+1 != $length)	$sql.= "('$userID','$categories[$i]'),";
    		else				$sql.= "('$userID','$categories[$i]')";
    	}
    	
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    }
    public function allCustomersToCategory($categoryID){
    	$sql = "
        	    INSERT INTO $this->_name(categoryID,userID) SELECT '$categoryID', userID
				FROM user        	    
        	    
        	";   	
    	
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    }
}
