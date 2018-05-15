<?php

class Models_Language_DB_LanguageMutation extends Models_DbTable 
{
    protected $_name = 'language_mutation';
    public $lastID;
    
    public function insertData($data){
    	
    	$this->insert($data);
    	$this->lastID = $this->getDefaultAdapter()->lastInsertId($this->_name);
    	
    }
    
    public function getAllItems($where = null,$order = null){
    	
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
    
    public function getAllLangs(){
    
    	$sql = "
    		SELECT *
            FROM $this->_name
            WHERE enabled = 1
            ORDER BY priority
        ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
       
    public function getRow($where = 1,$fetchMode = Zend_Db::FETCH_OBJ){
    
    	$sql = "SELECT
    			LM.title,
    			LM.langTitle,
    			LM.suffix,
    			LM.file,
    			LM.generateNiceTitle,
    			LM.enabled,
    			LM.langTitle,
    			LM.currencySign,
    			LM.decimal,
    			LM.priority,
    			C.currencyID,
    			C.title as 'currencyTitle',
    			C.code as 'currencyCode',
    			C.exchangeRate,
    			C.main
            FROM $this->_name LM
            LEFT JOIN currency C ON (LM.currencyID = C.currencyID)
            WHERE $where
        ";
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetch();
    
    }
}
