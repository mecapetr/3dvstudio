<?php

class Eshop_Models_Option extends Models_DbTable
{
    protected $_name = 'option';
    public $lastID;
    
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
    
    public function getAllItems($where,$order){
    	
    	return $this->fetchAll($where,$order);
    }
        
    public function getCount($where = "1"){
    	
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
                
}
