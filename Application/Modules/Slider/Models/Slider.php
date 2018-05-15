<?php

class Slider_Models_Slider extends Models_DbTable
{
    protected $_name = 'slider';
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

    public function getSliders(){
    
    	$sql = "SELECT S.link, S.title,S.sliderID, S.text,P.title as 'photoTitle'
            	FROM photo P 
            	    LEFT JOIN photo_slider PS  ON(P.photoID    = PS.photoID)
        	    	RIGHT JOIN $this->_name S 	ON (S.sliderID = PS.sliderID)
            	GROUP BY S.sliderID
        ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
                            
}
