<?php

class Eshop_Models_ProductSizeNumberPosition extends Models_DbTable
{
    protected $_name = 'product_size_number_position';
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
    public function getRow($where = null,$order = null){

    	$sql = "SELECT *
    		FROM $this->_name
    	";

    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
       if(!empty($order)){
       	$sql .= " ORDER BY $order ";
       }
        
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchObject();
    
    }
    
    public function getDimensions($productID,$productTypeID){
    
    	$sql = "SELECT PSN.value,PSNP.x,PSNP.y
        		FROM $this->_name PSNP
        		LEFT JOIN product_size_number PSN ON (PSN.productSizeNumberPositionID = PSNP.productSizeNumberPositionID AND PSN.productID = ?)
        		WHERE PSNP.productTypeID = ?
        ";

    
    	$data	= array($productID,$productTypeID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    
    
        
}
