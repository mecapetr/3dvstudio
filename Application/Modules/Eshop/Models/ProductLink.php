<?php

class Eshop_Models_ProductLink extends Models_DbTable
{
    protected $_name = 'product_link';
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
    
    public function updateView($id){
    
    	$sql = " UPDATE $this->_name SET view = view + 1 WHERE productID = ? ";
    
    	$data	= array($id);
    	$stmt 	= $this->setStatement($sql,$data);
    	    
    }

    public function getProductTopLink($productID,$where = 1){
    	
    	$sql = "SELECT AL.linkID
    	    				FROM  $this->_name AL     	    				
    	    				WHERE AL.productID = '$productID' AND AL.level = '0' AND $where
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    	
    }
    public function getTopLinkID($productID){
    	
    	$sql = "SELECT L.linkID
    	    				FROM  $this->_name AL JOIN link L ON (AL.linkID = L.linkID AND AL.productID = '$productID' AND AL.level = '0')
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }
    public function getTopLinkTitle($productID){
    	
    	$sql = "SELECT L.title
    	    				FROM  $this->_name AL JOIN link L ON (AL.linkID = L.linkID AND AL.productID = '$productID' AND AL.level = '0')
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }
	public function getLastLinkTitle($productID){
    	
    	$sql = "SELECT L.title
    	    				FROM  $this->_name AL JOIN link L ON (AL.linkID = L.linkID AND AL.productID = '$productID' AND AL.isLastLink = '1')  
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }

    public function getLastLinkData($productID){
    	
    	$sql = "SELECT L.title,L.linkID
    	    				FROM  $this->_name AL JOIN link L ON (AL.linkID = L.linkID AND AL.productID = '$productID' AND AL.isLastLink = '1')  
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }
    //vrati vsechny clanky, kter jsou primo zarazeny tomuto odkazu cili maji priznak isLastLink = 1
    public function getLastLinkProducts($linkID,$where = 1,$order = "A.priority"){
    	
    	$sql = "SELECT A.title,A.niceTitle, A.anotation,A.view, A.text, A.date, A.productID,A.allowDelete
    	    				FROM $this->_name AL JOIN product A ON (A.productID = AL.productID AND AL.linkID = '$linkID' AND isLastLink = '1')    	    				
    	    				WHERE $where
    	    				ORDER BY $order
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }
    
        
}
