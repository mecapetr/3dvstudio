<?php

class Content_Models_WebVideoArticle extends Models_DbTable
{
    protected $_name = 'web_video_article';
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
                        FROM web_video WV, web_video_article WVA
                        WHERE WV.webVideoID = WVA.webVideoID 
                              AND WVA.articleID = ?     
            ";
    
    	$data  = array($id);
    	$stmt  = $this->setStatement($sql,$data);
    	$stmt  ->setFetchMode(Zend_Db::FETCH_OBJ);
    	return $stmt->fetchAll();
    }
	            
}
