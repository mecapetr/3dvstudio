<?php

class Models_PhotoSmallBanner extends Models_DbTable
{

    protected $_name = 'photo_small_banner';
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
            


    public function getAllPhotos($where = 1,$order = null,$fetchType = Zend_Db::FETCH_OBJ){
    
    	$sql = "
    	SELECT *,P.title as 'file'
    	FROM photo P
    	JOIN $this->_name CT ON (P.photoID = CT.photoID)
    	WHERE $where
    	 
    	";
    	if(empty($order)){
    		$sql .= "ORDER BY P.priority ASC,P.photoID";
    	}else{
    		$sql .= "ORDER BY $order";
    	}
    
    	$data		= array();
    	$stmt 		= $this->setStatement($sql,$data);
    	$stmt		->setFetchMode($fetchType);
    	return    	$stmt->fetchAll();
    	 
    }

    public function getAllPhotosWithLang($lang = 'cz',$where = 1,$order = 'P.priority',$fetchType = Zend_Db::FETCH_OBJ){
    
    	$sql = "
    	SELECT
    	P.photoID,
    	P.title,
    	PL.description,
    	PL.description2
    	FROM photo P
    	JOIN $this->_name CT ON (P.photoID = CT.photoID)
    	JOIN photo_lang PL ON (P.photoID = PL.photoID AND PL.lang = '$lang')
    	WHERE $where
    
    	";
    	if(empty($order)){
    		$sql .= "ORDER BY P.priority ASC,P.photoID";
    	}else{
    		$sql .= "ORDER BY $order";
    	}
    
    	$data		= array();
    	$stmt 		= $this->setStatement($sql,$data);
    	$stmt		->setFetchMode($fetchType);
    	return    	$stmt->fetchAll();
    
    }
        
}
