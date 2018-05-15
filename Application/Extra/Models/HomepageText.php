<?php

class Models_HomepageText extends Models_DbTable
{
    protected $_name = 'homepage_text';
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
    public function getAll($lang = 'cz',$where = 1,$order = null,$fetchMode = Zend_Db::FETCH_OBJ){

    	$sql = "SELECT 
    			HT.homepageTextID,
    			HTL.text 
    			FROM `$this->_name` HT
    			JOIN homepage_text_lang HTL ON (HT.homepageTextID = HTL.homepageTextID AND HTL.lang = '$lang')
    			WHERE $where ";
    	if(!empty($order))
    		$sql .= " ORDER BY $order";
    	 
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetchAll();
    }    
    public function getRow($lang = 'cz',$where = null,$order = null,$fetchMode = Zend_Db::FETCH_OBJ){

    	$sql = "SELECT     		 
    			HT.homepageTextID,
    			HTL.text 
    			FROM `$this->_name` HT
    			JOIN homepage_text_lang HTL ON (HT.homepageTextID = HTL.homepageTextID AND HTL.lang = '$lang')
    	";

    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
       if(!empty($order)){
       	$sql .= " ORDER BY $order ";
       }
        
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetchObject();
    
    }
    
        
}
