<?php

class Eshop_Models_PhotoProduct extends Models_DbTable
{

    protected $_name = 'photo_product';
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
    
	public function getAllItemsWhere($where){
    	
    	$sql = "
    	    SELECT * 
    	    FROM $this->_name PA, photo P
    	    WHERE PA.photoID = P.photoID
    	          AND $where
    	    ORDER BY P.mainPhoto DESC,P.priority
    	    ";
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
    public function getProductPhotos($productID,$lang = "cz"){
    
    	$sql = "
    		SELECT P.title,PL.description as 'photoDesc',PL.description2 as 'photoDesc2'
    	    FROM $this->_name PP
    	    JOIN photo P ON (P.photoID = PP.photoID)
        	JOIN photo_lang PL ON (P.photoID = PL.photoID AND PL.lang = ?)
    	    WHERE productID = ?
    	    ORDER BY P.priority
		";
    	
    	$data	= array($lang,$productID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    		
    }
            

	
    
        
}
