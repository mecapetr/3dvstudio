<?php

class Slider_Models_PhotoSlider extends Zend_Db_Table
{
    protected $_name = 'photo_slider';
    public $lastID;
    
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
                    
    public function deleteData($where){
    	
    	$this->delete($where);
    	
    }
    
	public function getAllItemsWhere($where){
    	
    	$sql = "
    	    SELECT * 
    	    FROM $this->_name PA, photo P
    	    WHERE PA.photoID = P.photoID
    	          AND $where
    	    ORDER BY  P.mainPhoto DESC,P.photoID
    	    ";
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
            
}
