<?php

class Eshop_Models_ShippingType extends Models_DbTable
{

    protected $_name = 'shipping_type';
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
    
    public function getAllShipping($lang){
    	
    		$sql = "SELECT STL.price, STL.title, ST.shippingTypeID
    	    			
    	    		FROM $this->_name ST
    	    		JOIN shipping_type_lang STL ON (ST.shippingTypeID = STL.shippingTypeID AND STL.lang = ?)
    	    	";

    		$data	= array($lang);
    		$stmt 	= $this->setStatement($sql,$data);
    		$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    		return  $stmt->fetchAll();

    	
    }
         
}
