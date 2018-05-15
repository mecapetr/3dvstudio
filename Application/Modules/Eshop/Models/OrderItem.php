<?php

class Eshop_Models_OrderItem extends Models_DbTable
{
    protected $_name = 'order_item';
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
    public function getCount($where = 1){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM `$this->_name` WHERE $where");
    }

    public function getCountByCustomer($where){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM `$this->_name` WHERE $where ");
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
    		SELECT MAX(priority) FROM `$this->_name`
    	";
    	return $this->getDefaultAdapter()->fetchOne($sql);
    }

    public function updateView($id){
    
    	$sql = " UPDATE `$this->_name` SET view = view + 1 WHERE articleID = ? ";
    
    	$data	= array($id);
    	$stmt 	= $this->setStatement($sql,$data);
    		
    }
    public function getAllOrdersLimit($where = null,$order = null, $limit1 = null, $limit2 = null, $fetchMode = Zend_Db::FETCH_OBJ){
    
    	$sql = "SELECT *
    
    		FROM `$this->_name` O
    
    	";
    
    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}

    	if(!empty($order)){
    		$sql .= " ORDER BY $order ";
    	}
    	if(!is_null($limit1) && !is_null($limit2)){
    		$sql .= " LIMIT $limit2,$limit1 ";
    	}
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetchAll();
    
    }  

        
}
