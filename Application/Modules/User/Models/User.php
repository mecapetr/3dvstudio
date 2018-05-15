<?php

class User_Models_User extends Models_DbTable
{
    protected $_name = 'user';
    public $lastID;
    
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
    
    public function getAllItems($where = null,$order = null){
    	
    	return $this->fetchAll($where,$order);
    }
        
    public function getCount($where = "1"){
    	
    	return $this->getDefaultAdapter()->fetchOne("SELECT COUNT(*) FROM $this->_name WHERE $where");
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
    
    public function getEmailsByFilter($categoriesSelected){
    	
    	$year 		= date("Y",time());
    	$month 		= date("m",time());
    	$day 		= date("d",time());
    	$hour 		= date("H",time());
    	$minute 	= date("i",time());
    	$seconds 	= date("s",time());
    
    	$compareDate 	=  date("Y-m-d H:i:s",mktime($hour,$minute,$seconds,$month-2,$day,$year));
    
    	//pokud je zvolena kategorie, tak filtrujeme podle kategorie, jinak vybirame vsechny nezavisle na kategorii
    	if(!empty($categoriesSelected)){
    		$sql="
    		            	SELECT C.email FROM category CAT
    					   	JOIN user_category CC ON(CAT.categoryID IN ($categoriesSelected) AND CC.categoryID = CAT.categoryID)					       
    					    JOIN $this->_name C ON(C.userID = CC.userID)
    		            ";
    	}else{
    		$sql="
    		    SELECT C.email FROM $this->_name C 
    		";
    	}
    
    	
    	$sql.=" WHERE C.sendEmails = 1 AND C.deleted = 0 AND blocked = 0";
    	
    
    	$sql.=" GROUP BY C.email";
    	 
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }
            
}
