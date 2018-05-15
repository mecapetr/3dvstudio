<?php

class Content_Models_ArticleLink extends Models_DbTable
{
    protected $_name = 'article_link';
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
    
    	$sql = " UPDATE $this->_name SET view = view + 1 WHERE articleID = ? ";
    
    	$data	= array($id);
    	$stmt 	= $this->setStatement($sql,$data);
    	    
    }

    public function getArticleTopLink($articleID,$where = 1){
    	
    	$sql = "SELECT AL.linkID
    	    				FROM  $this->_name AL     	    				
    	    				WHERE AL.articleID = '$articleID' AND AL.level = '0' AND $where
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    	
    }
    public function getTopLinkID($articleID){
    	
    	$sql = "SELECT L.linkID
    	    				FROM  $this->_name AL JOIN link L ON (AL.linkID = L.linkID AND AL.articleID = '$articleID' AND AL.level = '0')
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }
    public function getTopLinkTitle($articleID){
    	
    	$sql = "SELECT L.title
    	    				FROM  $this->_name AL JOIN link L ON (AL.linkID = L.linkID AND AL.articleID = '$articleID' AND AL.level = '0')
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }
	public function getLastLinkTitle($articleID){
    	
    	$sql = "SELECT L.title
    	    				FROM  $this->_name AL JOIN link L ON (AL.linkID = L.linkID AND AL.articleID = '$articleID' AND AL.isLastLink = '1')  
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }

    public function getLastLinkData($articleID){
    	
    	$sql = "SELECT L.title,L.linkID
    	    				FROM  $this->_name AL JOIN link L ON (AL.linkID = L.linkID AND AL.articleID = '$articleID' AND AL.isLastLink = '1')  
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }
    //vrati vsechny clanky, kter jsou primo zarazeny tomuto odkazu cili maji priznak isLastLink = 1
    public function getLastLinkArticles($linkID,$where = 1,$order = "A.priority"){
    	
    	$sql = "SELECT A.title,A.niceTitle, A.anotation,A.view, A.text, A.date, A.articleID,A.allowDelete
    	    				FROM $this->_name AL JOIN article A ON (A.articleID = AL.articleID AND AL.linkID = '$linkID' AND isLastLink = '1')    	    				
    	    				WHERE $where
    	    				ORDER BY $order
    	    	";
    		
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }
    
        
}
