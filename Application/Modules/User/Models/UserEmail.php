<?php

class User_Models_UserEmail extends Models_DbTable
{
    protected $_name = 'user_email';
    public $lastID;
    
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
    
    public function getAllItems($where = null,$order=null){
    	
    	return $this->fetchAll($where,$order);
    }
    
    public function getAllItemsWhere($where){
    	
    	return $this->fetchAll($where);
    }
    
    public function getAllItemsOrderByName(){
        
        return $this->fetchAll($this->select()->where("deleted = '0'")->order("login"));
    }
    
    public function getAllItemsOrder(){
    	
    	return $this->fetchAll($this->select()->order("priority"));
    }
        
    public function getCount(){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM $this->_name ");
    }
    
    public function getCountByCustomer($where){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM $this->_name WHERE $where ");
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
    public function addToUsers($emailID,$toArray,$categories){
    	
    	$emails = "";
    	$toArrayCount = count($toArray);
    	for($i = 0; $i < $toArrayCount; $i++){
    		if($i+1 != $toArrayCount) 	$emails .= "'$toArray[$i]'".",";
    		else						$emails .= "'$toArray[$i]'";
    	}
    	$sql = "
        		INSERT INTO $this->_name (emailID,userID) 
        			SELECT $emailID, C.userID 
        			FROM user C JOIN user_category CC ON C.userID = CC.userID 
        			WHERE C.email IN ($emails) AND CC.categoryID IN ($categories)
        			GROUP BY C.userID
        		";
        
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    }

    public function addToUsersNoCategories($emailID,$toArray){
    	
    	$emails = "";
    	$toArrayCount = count($toArray);
    	for($i = 0; $i < $toArrayCount; $i++){
    		if($i+1 != $toArrayCount) 	$emails .= "'$toArray[$i]'".",";
    		else						$emails .= "'$toArray[$i]'";
    	}
    	$sql = "
        		INSERT INTO $this->_name (emailID,userID) 
        			SELECT $emailID, C.userID 
        			FROM user C
        			WHERE C.email IN ($emails)
        			GROUP BY C.userID
        		";
        
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    }
    public function getAllUsersEmails($emailID){
    	
    	$sql = "
        		SELECT C.email 
        			FROM email E 
        			JOIN $this->_name CE ON (E.emailID = CE.emailID AND E.emailID = ? )
        			JOIN user C ON (CE.userID = C.userID)
        		";
        
    	$data	= array($emailID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }

}
