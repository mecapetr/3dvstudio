<?php

class Eshop_Models_ProductCategory extends Models_DbTable
{
    protected $_name = 'product_category';
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
    
    public function getProductCategories($lang,$productID){
    	
    	$sql = "SELECT
    	    	PCL.title, C.percent,C.productCategoryID
    	    	FROM $this->_name C
    	    	JOIN product_category_lang PCL ON (PCL.productCategoryID = C.productCategoryID AND PCL.lang = ?)
    	    	WHERE C.productID = ?
    	    	ORDER BY C.priority
        ";
    	

    	$data	= array($lang,$productID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();

    }
    
    public function getAllCoversWithSuppliers($where = null, $order = null){
    
    $sql = "SELECT 
    			C.coverMaterialID,
    			C.title,
    			C.titleSupplier,
    			C.text,
    			C.shortcut as 'coverShortcut',
    			S.name,
    			S.shortcut as 'supplierShortcut'
    		FROM $this->_name C
    		JOIN supplier_cover SC ON (C.coverID = SC.coverID)
    		JOIN supplier S ON (SC.supplierID = S.supplierID)
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
    	return  $stmt->fetchAll();
    	    
    }
    public function getAll($where = null, $order = null){
    
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
    	return  $stmt->fetchAll();
    	    
    }
    

        
}
