<?php

class Content_Models_LinkLang extends Models_DbTable 
{
    protected $_name = 'link_lang';
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
    
    public function getLinkdata($where,$linkWhere = "1"){
    
    	$sql = "SELECT L.linkID,L.parentID,LL.niceTitle	
    		FROM $this->_name LL
    		JOIN link L ON (LL.linkID = L.linkID AND $linkWhere)
    		WHERE $where
        ";
    	 
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchObject();
    }
      
    
    public function getCompleteLinkLanguages($where = 1){

    	$sql = "SELECT 
    				L.linkID,
    				LS.linkSectionID,
    				LSLA.lang as 'linkSectionName',
    				LSLA.name as 'linkSectionName',
    				LSLA.niceName as 'linkSectionNiceName',
    				LSAL.url as 'linkSectionArticleUrl',
    				LSFOL.title as 'linkSectionFormNicetitle',
    				LSFOL.niceTitle as 'linkSectionFormNiceTitle',
    				LSFOVL.title as 'linkSectionFormValueTitle',
    				LSFOVL.value as 'linkSectionFormValue',
    				LSHL.titleH1 as 'linkSectionHeaderTitleH1',
    				LSHL.titleH2 as 'linkSectionHeaderTitleH2',
    				LSHSL.title as 'linkSectionHeaderSectionTitle',
    				LSHSL.titleH2 as 'linkSectionHeaderSectionTitleH2',
    				LSLL.text as 'linkSectionLinkLangText',
    				LSLL.titleH2 as 'linkSectionLinkTitleH2',
    				LSLL.url as 'linkSectionLinkUrl',
    				LSML.title as 'linkSectionMapTitle',
    				LSTL.text as 'linkSectionTextText'
    				
        	    	FROM link L
        	        JOIN link_section LS ON (L.linkID = LS.linkID)
        	        JOIN link_section_lang LSLA ON (LS.linkSectionID = LSLA.linkSectionID)
        	        
        	        LEFT JOIN link_section_article LSA ON (LS.linkSectionID = LSA.linkSectionID)
        	        LEFT JOIN link_section_form LSFO ON (LS.linkSectionID = LSFO.linkSectionID)
        	        LEFT JOIN link_section_form_values LSFOV ON (LSFO.linkSectionFormID = LSFOV.linkSectionFormID)
        	        LEFT JOIN link_section_header LSH ON (LS.linkSectionID = LSH.linkSectionID)
        	        LEFT JOIN link_section_header_section LSHS ON (LS.linkSectionID = LSHS.linkSectionID)
        	        LEFT JOIN link_section_link LSL ON (LS.linkSectionID = LSL.linkSectionID)
        	        LEFT JOIN link_section_map LSM ON (LS.linkSectionID = LSM.linkSectionID)
        	        LEFT JOIN link_section_text LST ON (LS.linkSectionID = LST.linkSectionID)        	        
        	        
        	        LEFT JOIN link_section_article_lang LSAL ON (LSA.linkSectionArticleID = LSAL.linkSectionArticleID)
        	        LEFT JOIN link_section_form_lang LSFOL ON (LSFO.linkSectionFormID = LSFOL.linkSectionFormID)
        	        LEFT JOIN link_section_form_values_lang LSFOVL ON (LSFOV.linkSectionFormValueID = LSFOVL.linkSectionFormValueID)
        	        LEFT JOIN link_section_header_lang LSHL ON (LSH.linkSectionHeaderID = LSHL.linkSectionHeaderID)
        	        LEFT JOIN link_section_header_section_lang LSHSL ON (LSHS.linkSectionHeaderSectionID = LSHSL.linkSectionHeaderSectionID)
        	        LEFT JOIN link_section_link_lang LSLL ON (LSL.linkSectionLinkID = LSLL.linkSectionLinkID)
        	        LEFT JOIN link_section_map_lang LSML ON (LSM.linkSectionMapID = LSML.linkSectionMapID)
        	        LEFT JOIN link_section_text_lang LSTL ON (LST.linkSectionTextID = LSTL.linkSectionTextID)
        	        
        	        WHERE $where
        	";
    	
    	$data	= array();
    	$stmt 	= $this->setStatement($sql,$data);
    	$stmt	->setFetchMode(Zend_Db::FETCH_OBJ);
    	return  $stmt->fetchAll();
    }
	
}
