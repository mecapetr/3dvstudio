<?php

class Models_File extends Models_DbTable 
{
    protected $_name = 'file';
    public $lastID;
    
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
    
    public function getAllItems(){
    	
    	return $this->fetchAll();
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
    
        
	public function getFile($table,$id){
        
    	$sql = "
            SELECT * 
                FROM $this->_name P, $table PA
                WHERE P.fileID = PA.fileID 
                      AND P.fileID = '$id' 
                ORDER BY P.priority     
        ";
                      
    	return $this->getDefaultAdapter()->fetchRow($sql);
    }
    public function getAllFiles($id,$table,$tableID){
        
    	$sql = "
            SELECT *
                FROM $this->_name P, $table CT
                WHERE P.fileID = CT.fileID 
                      AND CT.$tableID = '$id' 
                ORDER BY P.priority     
        ";
                      
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
    public function getAllF(){
    
    	$sql = "
                SELECT *
                    FROM $this->_name P, file_actuality CT
                    WHERE P.fileID = CT.fileID 
                    ORDER BY P.priority     
            ";
    
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
        
	public function getFiles($id,$table,$tableID){
        
    	$sql = "
            SELECT * 
                FROM $this->_name P, $table T
                WHERE P.fileID = T.fileID 
                      AND T.$tableID = '$id' 
                ORDER BY P.priority     
        ";
                      
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
	public function getTempFiles($user,$tableType){
        
    	$sql = "
            SELECT * 
                FROM  file_temp
                WHERE userID = '$user'
                      AND tableType = '$tableType'    
        ";
                      
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
	public function getTempFile($id){
        
    	$sql = "
            SELECT * 
                FROM  file_temp
                WHERE fileTempID = '$id'    
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
        
    
    public function getMinIDValue($table,$tableID,$tableIDvalue){
    	
    	$sql = "SELECT MIN(fileID) FROM $table WHERE $tableID = ?";
	    
    	$data		= array($tableIDvalue);
	    $stmt 		= $this->setStatement($sql,$data);       
	    $stmt		->setFetchMode(Zend_Db::FETCH_OBJ); 
	    return    	$stmt->fetchColumn(0);
    }
}
