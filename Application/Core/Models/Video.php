<?php

class Models_Video extends Zend_Db_Table
{
    protected $_name = 'video';
    public $lastID;
        
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
    
    public function getAllItems($where,$order){
    	
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
    
    public function getVideo($id){
    
    	$sql = "
                        SELECT * 
                            FROM $this->_name V, video_advertisement VA
                            WHERE V.videoID = VA.videoID 
                                  AND VA.advertisementID = '$id'     
                    ";
    
    	return $this->getDefaultAdapter()->fetchRow($sql);
    }
    
    public function getAllVideos($id,$table,$tableID){
    
    	$sql = "
                    SELECT * 
                        FROM $this->_name V, $table VT
                        WHERE V.videoID = VT.videoID 
                              AND VT.$tableID = '$id' 
                        ORDER BY V.videoID     
                ";
    
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
    public function getTempVideos($user,$tableType){
    
    	$sql = "
                SELECT * 
                    FROM  video_temp
                    WHERE userID = '$user'
                          AND tableType = '$tableType'    
            ";
    
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
    
    public function getTempVideo($id){
    
    	$sql = "
                SELECT * 
                    FROM  video_temp
                    WHERE videoTempID = '$id'    
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
                UPDATE video_temp
                SET width = '$width',height='$height'
                WHERE videoTempID = '$id'
            ";
    
    	return $this->getDefaultAdapter()->query($sql);
    }
	            
}
