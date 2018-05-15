<?php

class Content_Models_LinkSectionLinkLang extends Models_DbTable 
{
    protected $_name = 'link_section_link_lang';
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
    public function getLanguagesWithSection($where,$orderBy = null,$fetchMode = Zend_Db::FETCH_OBJ){

    	$sql = "SELECT 
    			LS.linkSectionID,
    			LL.linkSectionLinkID,
    			L.lang,
    			L.text,
    			L.titleH2,
    			L.url
    			FROM $this->_name L 
    			JOIN link_section_link LL ON (L.linkSectionLinkID = LL.linkSectionLinkID)
    			JOIN link_section LS ON (LS.linkSectionID = LS.linkSectionID)
    			WHERE $where
    	";
    	if(!empty($orderBy)){
    		$sql .= " ORDER BY $orderBy ";
    	}
    	 
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetchAll();
    	
    }
    	
}
