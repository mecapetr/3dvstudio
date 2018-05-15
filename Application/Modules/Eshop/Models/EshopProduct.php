<?php

class Eshop_Models_EshopProduct extends Models_DbTable
{
    protected $_name = 'eshop_product';
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
    
    
    
    public function getWebProducts($lang,$productID){
    	
    	$sql = "SELECT 
    	        EP.eshopProductID,EP.productCategoryID,EP.photo,EP.store,EP.sideID,EP.chooseSideAllowed,EP.showFirstCover,EP.showSecondCover,
    			EP.chooseSideDisabled,EP.predefinedCoversType,EP.productID,P.title as 'productTitle',
    			EPL.title,EPL.text,EPL.price,
    			CT.title as 'coverTitle',CTT.title as 'coverTitle2',CP.title as 'photoCover',CPP.title as 'photoCover2',
    			CP.number as 'photoNumber',CPP.number as 'photoNumber2',CP.photoID as 'photoID',CPP.photoID as 'photoID2',
    			CL.title as 'coverT',CCL.title as 'coverT2',LM.currencySign,
    			GROUP_CONCAT(L.shortcut ORDER BY PL.productLinkID SEPARATOR '-') as 'linkShortcuts',PT.shortcut,
    			SUM(PL.isLastLink) as 'linkCount',
    			COUNT(PL.productID) as 'totalLinkCount'
    	    					
    	    	FROM $this->_name EP
    	    	JOIN product P ON (P.productID = EP.productID)
    	    	JOIN product_link PL ON (PL.productID = P.productID)
    	    	JOIN link L ON (L.linkID = PL.linkID)
    	    	JOIN eshop_product_lang EPL ON (EPL.eshopProductID = EP.eshopProductID AND EPL.lang = ?)
    	    	JOIN language_mutation LM ON (EPL.lang = LM.suffix AND EPL.lang = ?)
    	    	LEFT JOIN product_type PT ON (PT.productTypeID = EP.productTypeID)
    	    	LEFT JOIN cover_title CT ON (EP.cover1TitleID = CT.coverTitleID)
    	    	LEFT JOIN cover_title CTT ON (EP.cover2TitleID = CTT.coverTitleID)
    	    	LEFT JOIN cover C ON (EP.cover1ID = C.coverID)
    	    	LEFT JOIN cover_lang CL ON (C.coverID = CL.coverID AND CL.lang = ?)
    	    	LEFT JOIN cover CC ON (EP.cover2ID = CC.coverID)
    	    	LEFT JOIN cover_lang CCL ON (CC.coverID = CCL.coverID AND CCL.lang = ?)
    	    	LEFT JOIN photo CP ON (EP.cover1photoID = CP.photoID)
    	    	LEFT JOIN photo CPP ON (EP.cover2photoID = CPP.photoID)
    	    	
    	    	WHERE EP.productID = ? AND EP.showProduct = 1
    	    	GROUP BY EP.eshopProductID
    	    	ORDER BY EP.priority
    	";
    	
    	
    	
    	$data	= array($lang,$lang,$lang,$lang,$productID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    	
    }
    

    public function getAll($where = null, $order = null){
    
    	$sql = "SELECT 
    			EP.eshopProductID,
    			EP.hashID,
    			EP.productID,
    			EP.productCategoryID,
    			EP.title,
    			EP.text,
    			EP.photo,
    			EP.price,
    			EP.store,
    			EP.showProduct,
    			EP.sideID,
    			EP.chooseSideAllowed,
    			EP.chooseSideDisabled,
    			EP.predefinedCoversType,
    			EP.showFirstCover,
    			EP.showSecondCover,
    			EP.cover1TitleID,
    			EP.cover2TitleID,
    			EP.cover1ID,
    			EP.cover2ID,
    			EP.cover1photoID,
    			EP.cover2photoID,
    			PC.title as 'productCategoryTitle',
    			PC.productCategoryID,
    			CT.title as 'cover1Title',
    			CTT.title as 'cover2Title',
    			C.title as 'cover1CategoryTitle',
    			CC.title as 'cover2CategoryTitle',
    			CP.title as 'cover1PhotoTitle',
    			CP.description as 'cover1PhotoDescription',
    			CP.description2 as 'cover1PhotoDescription2',
    			CP.number as 'cover1Number',
    			CPP.title as 'cover2PhotoTitle',
    			CPP.description as 'cover2PhotoDescription',
    			CPP.description2 as 'cover2PhotoDescription2',
    			CPP.number as 'cover2Number'
    				
    	FROM $this->_name EP
    	LEFT JOIN product_category PC ON (EP.productCategoryID = PC.productCategoryID)
    	LEFT JOIN cover_title CT ON (EP.cover1TitleID = CT.coverTitleID)
    	LEFT JOIN cover_title CTT ON (EP.cover2TitleID = CTT.coverTitleID)
    	LEFT JOIN cover C ON (EP.cover1ID = C.coverID)
    	LEFT JOIN cover CC ON (EP.cover2ID = CC.coverID)
    	LEFT JOIN photo CP ON (EP.cover1photoID = CP.photoID)
    	LEFT JOIN photo CPP ON (EP.cover2photoID = CPP.photoID)
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
    public function getRow($where = null, $order = null){
    
    $sql = "SELECT  
    			EP.eshopProductID,
    			EP.hashID,
    			EP.productID,
    			EP.productCategoryID,
    			EP.productTypeID,
    			EP.title,
    			EP.text,
    			EP.photo,
    			EP.price,
    			EP.store,
    			EP.sideID,
    			EP.chooseSideAllowed,
    			EP.chooseSideDisabled,
    			EP.predefinedCoversType,
    			EP.showFirstCover,
    			EP.showProduct,
    			EP.showSecondCover,
    			EP.cover1TitleID,
    			EP.cover2TitleID,
    			EP.cover1ID,
    			EP.cover2ID,
    			EP.cover1photoID,
    			EP.cover2photoID,
    			PC.title as 'productCategoryTitle',
    			PC.productCategoryID,
    			CT.title as 'cover1Title',
    			CTT.title as 'cover2Title',
    			C.title as 'cover1CategoryTitle',
    			CC.title as 'cover2CategoryTitle',
    			CP.title as 'cover1PhotoTitle',
    			CP.description as 'cover1PhotoDescription',
    			CP.description2 as 'cover1PhotoDescription2',
    			CP.number as 'cover1Number',
    			CPP.title as 'cover2PhotoTitle',
    			CPP.description as 'cover2PhotoDescription',
    			CPP.description2 as 'cover2PhotoDescription2',
    			CPP.number as 'cover2Number'
    				
    		FROM $this->_name EP
	    	LEFT JOIN product_category PC ON (EP.productCategoryID = PC.productCategoryID)
	    	LEFT JOIN cover_title CT ON (EP.cover1TitleID = CT.coverTitleID)
	    	LEFT JOIN cover_title CTT ON (EP.cover2TitleID = CTT.coverTitleID)
	    	LEFT JOIN cover C ON (EP.cover1ID = C.coverID)
	    	LEFT JOIN cover CC ON (EP.cover2ID = CC.coverID)
	    	LEFT JOIN photo CP ON (EP.cover1photoID = CP.photoID)
	    	LEFT JOIN photo CPP ON (EP.cover2photoID = CPP.photoID)
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
    
    public function getEshopProductToCheck($lang,$eshopProductID){
    
    	$sql = "SELECT EP.photo,EP.sideID,EP.predefinedCoversType,P.productID,EPL.price as 'productPrice',EPL.title,
    	CP.photoID,CPP.photoID as 'photo2ID',CT.title as 'coverTitle',CTT.title as 'coverTitle2',CL.title as 'coverName',CLL.title as 'coverName2',
    	V.value as 'vatValue',SCC.percentage as 'percentage2',SC.percentage,SUP.supplierID
        			
        				
        		FROM $this->_name EP
        		JOIN eshop_product_lang EPL ON (EPL.eshopProductID = EP.eshopProductID AND EPL.lang = ?)
        		JOIN product P ON (P.productID = EP.productID)
        		JOIN supplier SUP ON (SUP.supplierID = P.supplierID)
        		JOIN vat V ON (V.vatID = P.vatID)
        		    	    	
    	    	LEFT JOIN cover_title CT ON (EP.cover1TitleID = CT.coverTitleID)
    	    	LEFT JOIN cover_title CTT ON (EP.cover2TitleID = CTT.coverTitleID)
    	    	
    	    	LEFT JOIN cover_lang CL ON (EP.cover1ID = CL.coverID AND CL.lang = ?)
    	    	LEFT JOIN cover_lang CLL ON (EP.cover2ID = CLL.coverID AND CLL.lang = ?)
    	    	
    	    	LEFT JOIN supplier_cover SC ON (EP.cover1ID = SC.coverID)
    	    	LEFT JOIN supplier_cover SCC ON (EP.cover2ID = SCC.coverID)
    	    	
    	    	LEFT JOIN photo CP ON (EP.cover1photoID = CP.photoID)
    	    	LEFT JOIN photo CPP ON (EP.cover2photoID = CPP.photoID)
    	    	
    	    	WHERE EP.eshopProductID = ?
        	";

    
    
    	$data	= array($lang,$lang,$lang,$eshopProductID);
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchObject();
    		
    }
        
}
