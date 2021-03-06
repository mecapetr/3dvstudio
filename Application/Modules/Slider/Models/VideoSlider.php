<?php

class Slider_Models_VideoSlider extends Zend_Db_Table
{
    protected $_name = 'video_slider';
    public $lastID;
    
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
                    
    public function deleteData($where){
    	
    	$this->delete($where);
    	
    }
    
	public function getAllVideos($where){
    	
    	$sql = "
    	    SELECT * 
    	    FROM $this->_name VA, video V
    	    WHERE VA.videoID = V.videoID
    	          AND $where
    	    ORDER BY V.videoID
    	    ";
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
            
}
