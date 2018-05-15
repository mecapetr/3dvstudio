<?php

class Models_HomepageSection extends Models_DbTable
{
    protected $_name = 'homepage_section';
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
    		HS.homepageSectionID,
    		HS.homepageSectionColorID,
    		HS.rowCount,
    		HS.showSection,
    		HS.priority,
    		HSC.color,
    		HSL.title,
    		HSL.url
    	FROM `$this->_name` HS
    	JOIN homepage_section_color HSC ON (HS.homepageSectionColorID = HSC.homepageSectionColorID)
    	JOIN homepage_section_lang HSL ON (HS.homepageSectionID = HSL.homepageSectionID)
    	WHERE $where AND HSL.lang = '$lang'";
    	if(!empty($order))
    		$sql .= " ORDER BY $order";
    	
    	 
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetchAll();
    }
    

    
    public function getRow($where = null,$order = null){

    	$sql = "SELECT *
    		FROM $this->_name
    	";

    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
       if(!empty($order)){
       	$sql .= " ORDER BY $order ";
       }
        
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchObject();
    
    }
    
    public function getAllAddedProducts($where = null,$order = null,$fetchMode = Zend_Db::FETCH_OBJ){

    	$sql = "SELECT 
    				HS.homepageSectionID,
    				HSP.homepageSectionProductID,
    				HS.title,
    				HS.url,
    				HS.rowCount,
    				HS.showSection,
    				HSC.color,
    				HSC.title as 'colorTitle',
    				P.title as 'productTitle',
    				P.productID,
    				(SELECT GROUP_CONCAT(DISTINCT(title) SEPARATOR '<br/>')  FROM product_link PL JOIN link L ON (PL.linkID = L.linkID AND PL.level = 0) WHERE P.productID = PL.productID ) as 'linkTitle',
    				(SELECT GROUP_CONCAT(DISTINCT(title) SEPARATOR '<br/>')  FROM product_link PLL JOIN link LL ON (PLL.linkID = LL.linkID AND PLL.level = 1) WHERE P.productID = PLL.productID ) as 'sublinkTitle'
    		
    				
    				
    	FROM $this->_name HS
    	JOIN homepage_section_color HSC ON (HS.homepageSectionColorID = HSC.homepageSectionColorID)
    	JOIN homepage_section_product HSP ON (HS.homepageSectionID = HSP.homepageSectionID)
    	JOIN product P ON (HSP.productID = P.productID)
    	
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
    	return  $stmt->fetchAll();
    }
        
}
