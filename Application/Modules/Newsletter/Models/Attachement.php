<?php

class Newsletter_Models_Attachement extends Models_DbTable
{
    protected $_name = 'attachement';
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
    public function getEmailsByUserID($userID,$limit){
        	
    	$sql = "
        		SELECT E.emailID,E.title,E.date FROM user C 	JOIN user_email CE ON(C.userID = CE.userID AND C.userID = ?)
        															JOIN email E ON (CE.emailID = E.emailID)
        		LIMIT ?,?
        		";
        
    	$data	= array($userID,$limit[0],$limit[1]);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    public function getCountEmailsByUserID($userID){
    	$sql = "
        		SELECT COUNT(*) FROM user C 	JOIN user_email CE ON(C.userID = CE.userID AND C.userID = ?)
        											JOIN email E ON (CE.emailID = E.emailID)
        		";
        
    	$data	= array($userID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }

}
