<?php

class Eshop_Models_CategoryFilter extends Models_DbTable
{
    protected $_name = 'category_filter';
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
    public function getAll($where = null,$order = null,$fetchMode = Zend_Db::FETCH_OBJ){
    
    	$sql = "SELECT * 
    	FROM `$this->_name` 
    
    	";

    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
    	if(!empty($order)){
    		$sql .= " ORDER BY $order ";
    	}
    
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetchAll();
    
    }     
    public function getAllWithCategory($where = null,$order = null,$fetchMode = Zend_Db::FETCH_OBJ){
    
    	$sql = "SELECT 
    			F.title,
    			F.filterID
    	FROM `$this->_name` CF
    	JOIN filter F ON (CF.filterID = F.filterID)
    	JOIN link C ON (C.linkID = CF.linkID)
    
    	";

    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
    	if(!empty($order)){
    		$sql .= " ORDER BY $order ";
    	}
    
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetchAll();
    
    }
    
    
        
}
