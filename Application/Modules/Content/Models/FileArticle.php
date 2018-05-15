<?php

class Content_Models_FileArticle extends Zend_Db_Table
{
    protected $_name = 'file_article';
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
    	    FROM $this->_name PA, file P
    	    WHERE PA.fileID = P.fileID
    	          AND $where
    	    ORDER BY  P.priority
    	    ";
    	return $this->getDefaultAdapter()->fetchAll($sql);
    }
            
}
