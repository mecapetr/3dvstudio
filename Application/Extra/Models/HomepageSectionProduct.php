<?php

class Models_HomepageSectionProduct extends Models_DbTable
{
    protected $_name = 'homepage_section_product';
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

    public function getAll($lang = 'cz',$where = null,$order = null,$fetchMode = Zend_Db::FETCH_OBJ, $limit1 = null, $limit2 = null){
    
    	$sql = "SELECT
    	HSP.homepageSectionID,P.productID,PRL.price, PRL.title,PRL.niceTitle, PH.title as 'mainPhoto',P.discountInPercentage,PRL.discount,PRL.deliveryText,P.dateAuction,P.textAuction,P.minPriceAuction,P.endedAuction,P.priceAuction,
    	LM.currencySign, GROUP_CONCAT(DISTINCT CONCAT(PS.title,'-',PPS.color) ORDER BY PPS.priority ASC) as 'statuses',
    	LL.niceTitle as 'linkNiceTitle'
    	FROM $this->_name HSP
    	JOIN product P ON (HSP.productID = P.productID)
    	JOIN product_link PL ON (P.productID = PL.productID)
    	JOIN link L ON (PL.linkID = L.linkID AND L.parentID = 0 AND L.isEshopCategory)
    	JOIN link_lang LL ON (LL.linkID = L.linkID AND LL.lang = ?)
    
    	JOIN product_lang PRL ON (P.productID = PRL.productID AND PRL.lang = ?)
    	JOIN photo_product PP ON (PP.productID = P.productID)
    	JOIN photo PH ON (PH.photoID = PP.photoID AND PH.mainPhoto = 1)
    	JOIN product_display_language PDL ON (PDL.productID = P.productID)
    	JOIN language_mutation LM ON (PDL.languageID = LM.languageID)
    	LEFT JOIN product_product_status PPS ON (PPS.productID = P.productID)
    	LEFT JOIN product_status PS ON (PS.productStatusID = PPS.productStatusID)
    
    	";
    
    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
    	$sql .= "GROUP BY P.productID";
    
    	if(!empty($order)){
    		$sql .= " ORDER BY $order ";
    	}
    	if(!is_null($limit1) && !is_null($limit2)){
    		$sql .= " LIMIT $limit2,$limit1 ";
    	}
    
    
    
    	$data	= array($lang,$lang);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetchAll();
    
    }
}
