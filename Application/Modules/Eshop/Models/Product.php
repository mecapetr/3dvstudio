<?php

class Eshop_Models_Product extends Models_DbTable
{
    protected $_name = 'product';
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
    
    	$sql = " UPDATE $this->_name SET views = views + 1 WHERE productID = ? ";
    
    	$data	= array($id);
    	$stmt 	= $this->setStatement($sql,$data);
    	    
    }
    public function updateOrderCount($id,$count){
    
    	$sql = " UPDATE $this->_name SET soldCount = soldCount + $count WHERE productID = ? ";
    
    	$data	= array($id);
    	$stmt 	= $this->setStatement($sql,$data);
    	    
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
    public function getAllItemsWithCategory($where = null,$order = null){

    	$sql = "SELECT *,
    					(SELECT GROUP_CONCAT(DISTINCT(title) SEPARATOR '<br/>')  FROM product_link PL JOIN link L ON (PL.linkID = L.linkID AND PL.level = 0) WHERE P.productID = PL.productID ) as 'linkTitle',
    					(SELECT GROUP_CONCAT(DISTINCT(title) SEPARATOR '<br/>')  FROM product_link PLL JOIN link LL ON (PLL.linkID = LL.linkID AND PLL.level = 1) WHERE P.productID = PLL.productID ) as 'sublinkTitle'
    		FROM $this->_name P
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
    	return  $stmt->fetchAll();
    
    }
    
    public function getProductData($lang,$id){
    	
    	$sql = "SELECT
    	    P.productID,P.supplierID,P.dateAuction,P.textAuction,P.minPriceAuction,P.endedAuction,P.priceAuction,
            PRL.price,PRL.originalPrice, PRL.title, PRL.text,PRL.textSize,PH.title as 'mainPhoto',LM.currencySign,
    	    PT.photo as 'ptPhoto',PT.productTypeID,V.value as 'vatValue'
    	   
    		FROM $this->_name P
    	    JOIN vat V ON (V.vatID = P.vatID)   	    			
    	    JOIN product_lang PRL ON (P.productID = PRL.productID AND PRL.lang = ?)
    	    JOIN photo_product PP ON (PP.productID = P.productID)
    	    JOIN photo PH ON (PH.photoID = PP.photoID AND PH.mainPhoto = 1)
    	    JOIN language_mutation LM ON (PRL.lang = LM.suffix)
    	    LEFT JOIN product_type PT ON (PT.productTypeID = P.productTypeID)
    	    
    	    WHERE P.productID = ?			
    	    			
    	";
    	
    	$data	= array($lang,$id);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchObject();
    	
    }
    
    public function getProductLinkShortcuts($id){
    	 
    	$sql = "SELECT GROUP_CONCAT(L.shortcut ORDER BY PL.level SEPARATOR '-') as 'shortcuts'
        	   
        		FROM $this->_name P
        	    JOIN product_link PL ON (P.productID = PL.productID)	    			
        	    JOIN link L ON (L.linkID = PL.linkID)
        	    
        	    WHERE P.productID = ?	

        	    GROUP BY P.productID
        	    			
        	";
    	 
    	$data	= array($id);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchObject();
    	 
    }
    
    public function getAllCategoryProducts($lang,$where = null,$having = null,$order = null, $limit1 = null, $limit2 = null,$join = ""){

    	$sql = "SELECT 
    				P.productID,PRL.price, PRL.title,PRL.niceTitle, PH.title as 'mainPhoto',P.discountInPercentage,PRL.discount,PRL.deliveryText,P.dateAuction,P.textAuction,P.minPriceAuction,P.endedAuction,P.priceAuction,
    				LM.currencySign, GROUP_CONCAT(DISTINCT CONCAT(PS.title,'-',PPS.color) ORDER BY PPS.priority ASC) as 'statuses',
    				
    				(
    					SELECT value
    					FROM product_size_number PSN
    					JOIN product_size_number_position PSNP ON (PSN.productSizeNumberPositionID = PSNP.productSizeNumberPositionID)
    					WHERE PSNP.isWidth = 1 AND PSN.productID = P.productID
    					
    				) as 'productWidth',
    				
    				(
    					SELECT value
    					FROM product_size_number PSN
    					JOIN product_size_number_position PSNP ON (PSN.productSizeNumberPositionID = PSNP.productSizeNumberPositionID)
    					WHERE PSNP.isDepth = 1 AND PSN.productID = P.productID
    					
    				) as 'productDepth',
    				
    				CASE WHEN PSS.productStatusID IS NULL 
	    				THEN 0
				        ELSE 1
			        END AS 'isNew'
    				
    			FROM $this->_name P
    			JOIN product_link PL ON (P.productID = PL.productID)
    			
    			JOIN product_lang PRL ON (P.productID = PRL.productID AND PRL.lang = ?)
    			JOIN photo_product PP ON (PP.productID = P.productID)
    			JOIN photo PH ON (PH.photoID = PP.photoID AND PH.mainPhoto = 1)
    			JOIN product_display_language PDL ON (PDL.productID = P.productID)
    			JOIN language_mutation LM ON (PDL.languageID = LM.languageID)
    			$join
    			LEFT JOIN product_product_status PPS ON (PPS.productID = P.productID)
    			LEFT JOIN product_status PS ON (PS.productStatusID = PPS.productStatusID)
    			LEFT JOIN product_status PSS ON (PSS.productStatusID = PPS.productStatusID AND PSS.productStatusID = 2)
    			
    			
    	";

    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
    	$sql .= "GROUP BY P.productID";

    	if(!empty($having)){
    		$sql .= " HAVING $having ";
    	}
    	if(!empty($order)){
    		$sql .= " ORDER BY $order ";
    	}
        if(!is_null($limit1) && !is_null($limit2)){
       		$sql .= " LIMIT $limit2,$limit1 ";
        }
    	$data	= array($lang);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    
    }
    public function getCountCategoryProducts($where = null,$order = null){

    	$sql = "SELECT COUNT(*)
    			FROM $this->_name P
    			JOIN product_link PL ON (P.productID = PL.productID)
    			JOIN link L ON (PL.linkID = L.linkID)
    			JOIN photo_product PP ON (PP.productID = P.productID)
    			JOIN photo PH ON (PH.photoID = PP.photoID AND PH.mainPhoto = 1)
    			JOIN product_display_language PDL ON (PDL.productID = P.productID)
    			JOIN language_mutation LM ON (PDL.languageID = LM.languageID)
    			
    	";

    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
    	$sql .= "GROUP BY P.productID";
    	
        
    
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    
    }


    public function getProductWithTopCategory($lang = 'cz',$where = null,$fetchMode = Zend_Db::FETCH_OBJ){
    
    	$sql = "SELECT
    	P.productID,PRL.niceTitle,LL.niceTitle as 'linkNiceTitle'
    	FROM $this->_name P
    	JOIN product_link PL ON (P.productID = PL.productID)
    	JOIN link L ON (PL.linkID = L.linkID AND L.parentID = 0 AND L.isEshopCategory)
    	JOIN link_lang LL ON (LL.linkID = L.linkID AND LL.lang = ?)
    
    	JOIN product_lang PRL ON (P.productID = PRL.productID AND PRL.lang = ?)
    	JOIN product_display_language PDL ON (PDL.productID = P.productID)
    	JOIN language_mutation LM ON (PDL.languageID = LM.languageID AND LM.suffix = '$lang')
    
    	";
    
    	if(!empty($where)){
    		$sql .= " WHERE $where ";
    	}
    
    
    	$data	= array($lang,$lang);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode($fetchMode);
    	return  $stmt->fetch();
    
    }

    public function getAllProductsWithTopCategory($lang = 'cz',$where = null,$order = null, $limit1 = null, $limit2 = null, $fetchMode = Zend_Db::FETCH_OBJ){
    
    	$sql = "SELECT
    	P.productID,PRL.price, PRL.title,PRL.niceTitle, PH.title as 'mainPhoto',P.discountInPercentage,PRL.discount,PRL.deliveryText,
    	LM.currencySign, GROUP_CONCAT(DISTINCT PS.title) as 'statuses',
    	LL.niceTitle
    
    	FROM $this->_name P
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
      

    public function getAllSearchedProducts($lang = 'cz',$where = null,$order = null, $limit1 = null, $limit2 = null, $fetchMode = Zend_Db::FETCH_OBJ){
    
    	$sql = "SELECT
    	P.productID,PRL.price, PRL.title,PRL.niceTitle, PH.title as 'mainPhoto',P.discountInPercentage,PRL.discount,PRL.deliveryText,P.dateAuction,P.textAuction,P.minPriceAuction,P.endedAuction,P.priceAuction,
    	LM.currencySign, GROUP_CONCAT(DISTINCT CONCAT(PS.title,'-',PPS.color) ORDER BY PPS.priority ASC) as 'statuses',
    	LL.niceTitle,
    				
    	CASE WHEN PSS.productStatusID IS NULL 
	    	THEN 0
			ELSE 1
		END AS 'isNew'
    
    	FROM $this->_name P
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
    	LEFT JOIN product_status PSS ON (PSS.productStatusID = PPS.productStatusID AND PSS.productStatusID = 2)
    
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
    
    
    public function getAllAuctionsForCron(){
    	
    	 
    	$sql = "SELECT P.productID, P.title, P.dateAuction,P.priceAuction, PA.value, PA.tel, PA.lang
    	
    	FROM $this->_name P
    	LEFT JOIN product_auction PA ON (PA.productID = P.productID AND PA.value = (SELECT MAX(value) FROM product_auction PAA WHERE PAA.productID = P.productID))
    	WHERE P.showProduct = 1 AND P.dateAuction IS NOT NULL AND endedAuction = 0 AND dateAuction < NOW()
    	ORDER BY P.dateAuction DESC
    	 
    	
    	";
    	
    	//echo $sql;
    	
    	$data	= array();
    	 
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }
    
    public function getXmlFeed($lang,$type = ""){
    	
    	$mainPhoto = "AND PH.mainPhoto = 1";
    	$googleSqlSelect = "";
    	
    	if($type == "google"){   		
    		$mainPhoto       = "";
    		$googleSqlSelect = ",GROUP_CONCAT(DISTINCT CONCAT(PH.title,'~',PH.mainPhoto) SEPARATOR '|') as files";	
    	}
    	
    	$sql = "SELECT
    	    	P.productID,PRL.price,P.hManu, 
    	    	PRL.title,PRL.niceTitle,PRL.price,PRL.hCat,PRL.hTitle,PRL.hpTitle,PRL.hText,
    	    	PH.title as 'mainPhoto',
    	    	PS.productStatusID,
    	    	GROUP_CONCAT(DISTINCT LL.title ORDER BY PL.level SEPARATOR ' | ') as 'categories',
    	    	GROUP_CONCAT(DISTINCT LL.niceTitle ORDER BY PL.level SEPARATOR '|') as 'categoriesNiceTitle'
    	    	$googleSqlSelect
    	    				
    	    	FROM $this->_name P
    	    	JOIN product_link PL ON (P.productID = PL.productID)
    	    	JOIN link_lang LL ON (LL.linkID = PL.linkID AND LL.lang = ?)
    	    			
    	    	JOIN product_lang PRL ON (P.productID = PRL.productID AND PRL.lang = ?)
    	    	JOIN photo_product PP ON (PP.productID = P.productID)
    	    	JOIN photo PH ON (PH.photoID = PP.photoID $mainPhoto)
    	    	JOIN product_display_language PDL ON (PDL.productID = P.productID)
    	    	JOIN language_mutation LM ON (PDL.languageID = LM.languageID AND suffix = ?)
    	    	LEFT JOIN product_product_status PPS ON (PPS.productID = P.productID)
    	    	LEFT JOIN product_status PS ON (PS.productStatusID = PPS.productStatusID AND PS.productStatusID = 4)
    	    	
    	    	GROUP BY P.productID
    	    			
    	 ";
    	   	
    	$data	= array($lang,$lang,$lang);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	
    }
    
    public function getCategoryMaxDepthSize($where = 1,$join = ""){
    
    	$sql = "SELECT MAX(value)
    	FROM $this->_name P
    	JOIN product_link PL ON (P.productID = PL.productID)
    	JOIN product_size_number PSN ON (PSN.productID = P.productID)
    	JOIN product_size_number_position PSNP ON (PSN.productSizeNumberPositionID = PSNP.productSizeNumberPositionID AND PSNP.isDepth = 1)
    	$join
    	WHERE $where
    	";
    
    	//echo $sql;
    
    	$data	= array();
    	 
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }
    public function getCategoryMinDepthSize($where = 1,$join = ""){

    	$sql = "SELECT MIN(value)
    	 		FROM $this->_name P
    	 		JOIN product_link PL ON (P.productID = PL.productID)
    			JOIN product_size_number PSN ON (PSN.productID = P.productID)
    			JOIN product_size_number_position PSNP ON (PSN.productSizeNumberPositionID = PSNP.productSizeNumberPositionID AND PSNP.isDepth = 1)
    			$join
    			WHERE $where
    	";
    	 
    	//echo $sql;
    	 
    	$data	= array();
    	
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }

    public function getCategoryMaxWidthSize($where = 1,$join = ""){
    
    	$sql = "SELECT MAX(value)
    	FROM $this->_name P
    	JOIN product_link PL ON (P.productID = PL.productID)
    	JOIN product_size_number PSN ON (PSN.productID = P.productID)
    	JOIN product_size_number_position PSNP ON (PSN.productSizeNumberPositionID = PSNP.productSizeNumberPositionID AND PSNP.isWidth = 1)
    	$join
    	WHERE $where
    	";
    
    	//echo $sql;
    
    	$data	= array();
    	 
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }
    public function getCategoryMinWidthSize($where = 1,$join = ""){

    	$sql = "SELECT MIN(value)
    	 		FROM $this->_name P
    	 		JOIN product_link PL ON (P.productID = PL.productID)
    			JOIN product_size_number PSN ON (PSN.productID = P.productID)
    			JOIN product_size_number_position PSNP ON (PSN.productSizeNumberPositionID = PSNP.productSizeNumberPositionID AND PSNP.isWidth = 1)
    			$join
    			WHERE $where
    	";
    	 
    	//echo $sql;
    	 
    	$data	= array();
    	
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }
    public function getCategoryMaxPrice($where = 1,$join = ""){

    	$sql = "SELECT MAX(P.price)
    	 		FROM $this->_name P
    	 		JOIN product_link PL ON (P.productID = PL.productID)
    	 		$join
    			WHERE $where
    	";
    	 
    	//echo $sql;
    	 
    	$data	= array();
    	
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }
    public function getCategoryMinPrice($where = 1,$join = ""){

    	$sql = "SELECT MIN(P.price)
    	 		FROM $this->_name P
    	 		JOIN product_link PL ON (P.productID = PL.productID)
    	 		$join
    			WHERE $where
    	";
    	 
    	//echo $sql;
    	 
    	$data	= array();
    	
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchColumn(0);
    }
}
