<?php

class Models_PhotoOld extends Models_DbTable 
{
    protected $_name = 'photo_old';
    public $lastID;
    
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
    
    public function getAllItems($where = null,$order = null){
    	
    	return $this->fetchAll($where,$order);
    }
      
        
    public function getCount(){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM $this->_name ");
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
    
    public function updateMainPhoto($productID,$mainPhoto,$img){
    
    	$sql = "UPDATE $this->_name SET mainPhoto = '$mainPhoto' WHERE title = '$img' AND photoID IN (SELECT photoID FROM photo_product WHERE productID = '$productID')";
    
    	return $this->getDefaultAdapter()->query($sql);
    }
    
	public function getPhoto($table,$id){
        
    	$sql = "
            SELECT * 
                FROM $this->_name P, $table PA
                WHERE P.photoID = PA.photoID 
                      AND P.photoID = '$id' 
                 P.priority ASC,P.photoID     
        ";
                      
    	return $this->getDefaultAdapter()->fetchRow($sql);
    }
    public function getAllPhotos($id,$table,$tableID,$order = null,$fetchType = Zend_Db::FETCH_OBJ){
        
    	$sql = "
            SELECT *,P.title as 'file' 
                FROM $this->_name P, $table CT
                WHERE P.photoID = CT.photoID 
                      AND CT.$tableID = '$id' 
                   
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

    public function getNoMainPhotos($id,$table,$tableID){
    
    	$sql = "
    	SELECT *,P.title as 'file'
    	FROM $this->_name P, $table CT
    	WHERE P.photoID = CT.photoID
    	AND CT.$tableID = '$id'
    	AND mainPhoto <> 1
    	ORDER BY  P.priority ASC,P.photoID
    	";
    
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    public function getMainPhoto($id,$table,$tableID){
    
    	$sql = "
                SELECT *,P.title as 'file' 
                    FROM $this->_name P, $table CT
                    WHERE P.photoID = CT.photoID 
                          AND CT.$tableID = '$id'
                          AND mainPhoto = 1 
                    ORDER BY  P.priority ASC,P.photoID    
            ";
    
    	return $this->getDefaultAdapter()->fetchRow($sql);
    }
    
	public function getPhotos($id,$table,$tableID){
        
    	$sql = "
            SELECT * 
                FROM $this->_name P, $table T
                WHERE P.photoID = T.photoID 
                      AND T.$tableID = '$id' 
                ORDER BY  P.priority ASC,P.photoID      
        ";
                      
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
	public function getTempPhotos($user,$tableType){
        
    	$sql = "
            SELECT * 
                FROM  photo_temp
                WHERE userID = '$user'
                      AND tableType = '$tableType'    
        ";
                      
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
	public function getTempPhoto($id){
        
    	$sql = "
            SELECT * 
                FROM  photo_temp
                WHERE photoTempID = '$id'    
        ";
                      
    	return $this->getDefaultAdapter()->fetchRow($sql);
    }
    
	public function deleteTableData($table,$where){
        
    	$sql = "
            DELETE FROM $table
                WHERE $where    
        ";
                      
    	return $this->getDefaultAdapter()->query($sql);
    }
    
	public function updateTempData($width,$height,$id){
        
    	$sql = "
            UPDATE photo_temp
            SET width = '$width',height='$height'
            WHERE photoTempID = '$id'
        ";
                      
    	return $this->getDefaultAdapter()->query($sql);
    }
    
    public function updateMainStatusToZero($id,$table,$tableID){
    	$sql = "
            UPDATE photo P, $table CT
            SET P.mainPhoto = '0'
            WHERE 	CT.$tableID = '$id' AND
            		P.photoID = CT.photoID
        ";
                      
    	return $this->getDefaultAdapter()->query($sql);
    }
    public function updateMainStatus($photoID){
    	$sql = "
            UPDATE photo
            SET mainPhoto = '1'
            WHERE 	photoID = '$photoID'
        ";
                      
    	return $this->getDefaultAdapter()->query($sql);
    }

	public function mainPhotoExist($table,$tableID,$tableIDvalue){

    	$sql = "
      			SELECT count(*) FROM $table CT, $this->_name P
      			WHERE 	CT.$tableID = ? AND
      					CT.photoID  = P.photoID AND
      					P.mainPhoto = '1'
    	"; 
      $data		= array($tableIDvalue);
      $stmt 	= $this->setStatement($sql,$data);       
      $stmt		->setFetchMode(Zend_Db::FETCH_OBJ); 
      return    $stmt->fetchColumn(0);
      
    }
    public function setFirstMainPhoto($minID){
    	$sql = "
            UPDATE $this->_name
            SET mainPhoto = '1'
            WHERE 	photoID = '$minID' 
        ";
                      
    	return $this->getDefaultAdapter()->query($sql);
    }
    public function getMinIDValue($table,$tableID,$tableIDvalue){
    	
    	$sql = "SELECT MIN(photoID) FROM $table WHERE $tableID = ?";
	    
    	$data		= array($tableIDvalue);
	    $stmt 		= $this->setStatement($sql,$data);       
	    $stmt		->setFetchMode(Zend_Db::FETCH_OBJ); 
	    return    	$stmt->fetchColumn(0);
    }
}
