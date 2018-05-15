<?php

class Eshop_Models_Order extends Models_DbTable
{
    protected $_name = 'order';
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
    public function getRow($where = null, $fetchMode = Zend_Db::FETCH_OBJ){
    
    	$sql = "SELECT 
    
    			O.orderID,
    			O.orderNumber,
    			O.orderDate,
    			O.completeOrder,
    			O.description,
    			O.company,
    			O.name,
    			O.surname,
    			O.email,
    			O.tel,
    			O.street,
    			O.city,
    			O.zip,
    			O.ic,
    			O.dic,
    			O.countryID,
    			O.deliveryCompany,
    			O.deliveryName,
    			O.deliverySurname,
    			O.deliveryEmail,
    			O.deliveryTel,
    			O.deliveryStreet,
    			O.deliveryCity,
    			O.deliveryZip,
    			O.deliveryCountryID,
    			O.dateCompleted,
    			ST.title as 'shippingTypeTitle',
    			ST.price as 'shippingTypePrice' ,
    			ST.shippingTypeID ,
    			PT.title as 'paymentTypeTitle',
    			PT.paymentTypeID,
    			C.currencyID,
    			C.title as 'currencyTitle',
    			CO.title as 'countryTitle',
    			COO.title as 'deliveryCountryTitle'
    		
    	
    		FROM `$this->_name` O
    		LEFT JOIN shipping_type ST ON (O.shippingTypeID = ST.shippingTypeID)
    		LEFT JOIN payment_type PT ON (O.paymentTypeID = PT.paymentTypeID)
    		JOIN currency C ON (O.currencyID = C.currencyID)
    		JOIN country CO ON (O.countryID = CO.countryID)
    		JOIN country COO ON (O.deliveryCountryID = COO.countryID)
    
    	";
    
    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetch();
    
    } 

    public function getCountForUpdate($where = 1){
    
    	 
    	$sql = "
    	    	SELECT COUNT(*)
    	    	FROM `$this->_name` O
    	        
    	    	WHERE $where
    	    	FOR UPDATE    
        	";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }

        
}
