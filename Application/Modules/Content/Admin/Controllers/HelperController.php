<?php
class Content_HelperController extends Library_Adminbase
{
	

    function confirmOrRemoveCommentAction()
    {
    	$filter			 		= new Zend_Filter_StripTags();
    	$articleID   			= $filter->filter($this->_request->getPost("articleID"));
    	$commentID   			= $filter->filter($this->_request->getPost("commentID"));
    	$confirmOrRemove 		= $filter->filter($this->_request->getPost("confirmOrRemove"));
    	$className		 		= $filter->filter($this->_request->getPost("className"));
    	$commentColumnName 		= $filter->filter($this->_request->getPost("commentColumnName"));
    	$articleColumnName 		= $filter->filter($this->_request->getPost("articleColumnName"));
    	$refreshPHTMLfile		= $filter->filter($this->_request->getPost("refreshPHTMLfile"));
    
    	$where 					= "$commentColumnName = $commentID";
    	$class 					= new $className();
    
    	$this->view->confirmComments    = true;
    	if($confirmOrRemove == "confirm"){
    		
	    	if(is_numeric(floor($commentID))){
	    
	    		$data = array("showComment" => 1);
	    		$class->updateData($data,$where);
	    		
	    	}
    		
    	}else if($confirmOrRemove == "remove"){
    		
	    	if(is_numeric(floor($commentID))){
	    
	    		$class->deleteData($where);
	    			
	    	}
    		
    	}
    
    	$this->view->allComents = $class->getAllItems("$articleColumnName = '$articleID'",array("showComment ASC","date DESC"));
    
    	$this->renderScript($refreshPHTMLfile);
    }
    
    function addActualityCommentAction()
    {
    
    	$filter			  = new Zend_Filter_StripTags();
    	$actualityID    = $filter->filter($this->_request->getPost("actualityID"));
    	$name   		= $filter->filter($this->_request->getPost("name"));
    	$email 			= $filter->filter($this->_request->getPost("email"));
    	$text 			= $filter->filter($this->_request->getPost("text"));
    	$date			= date("Y-m-d H:i:s");
    
    	if(is_numeric(floor($actualityID))){
    		 
    		$actualityComment = new Content_Models_ArticleComment();
    		if($name == "")$name = "Anonym";
    		$data = array(
        					"actualityID" 	=> $actualityID,
        					"name" 			=> $name,
        					"email" 		=> $email,
        					"text" 			=> $text,
        					"date" 			=> $date,
    		);
    		 
    		$actualityComment->insertData($data);
    		echo 1;
    	}else echo 2;
    
    	$this->renderScript("/helper/empty.phtml");
    
    }
    function getActualityCommentAction()
    {
    	$filter			  				= new Zend_Filter_StripTags();
    	$actualityID    				= $filter->filter($this->_request->getPost("actualityID"));
    	$this->view->confirmComments    = $confirmComments =  $filter->filter($this->_request->getPost("confirmComments"));
    
    	if(is_numeric(floor($actualityID))){
    		 
    		$actualityComment = new AdminBase_Actuality_ActualityComment();
    		if($confirmComments == "true"){
    			$this->view->allComents = $actualityComment->getAllItems("actualityID = '$actualityID'",array("showComment ASC","date DESC"));
    		}else{
    			$this->view->allComents = $actualityComment->getAllItems("actualityID = '$actualityID' AND showComment = '1'","date ASC");
    		}
    	}else "<div>Při načítání komentářů nastala chyba!</div>";
    
    
    }
    function getNextFtpFileAction(){    
    	
		Zend_Layout::getMvcInstance()->disableLayout();
	    $language 		 			= new Models_Language_Language();
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();	
		
    	$fileName 				= $this->_request->getPost("fileName");
    	$ico					= $this->getIcons($fileName);
    	$this->view->ico 		= $ico;
    	$this->view->fileName 	= $fileName;        
    
    	$tableType = $this->_request->getPost('tableType');
    	$user      = $this->_request->getPost('user');
    	
    	$fileTemp = new Models_FileTemp();
    	$data = array(
        		"userID"    	=> $user,
        		"tableType" 	=> $tableType,
        		"title"     	=> $fileName,
        		"ico"       	=> $ico,
    			"isFromSource"	=> 1
    
    	);
    	$fileTemp->insertData($data);
    	echo "1";
    	$this->renderScript("helper/empty.phtml");
    }
    
    function getOthersLinkAction(){
    	
    	Zend_Layout::getMvcInstance()->disableLayout();
    	
    	$linkID  = $this->_request->getPost("linkID");
    	$linkID1 = $this->_request->getPost("linkID1");

    	$this->view->linkID1 = $linkID1;
    	    	
    	$link = new Content_Models_Link();
    	
    	if($linkID != 0){
    		$this->view->links = $link->getAllItems("parentID = '$linkID'","priority");
    	}
    	
    }
    
    function deleteHeaderElementAction(){
    	 
    	Zend_Layout::getMvcInstance()->disableLayout();
    	
    	$headerID = $this->_request->getPost("elmID");
    	if(!empty($headerID)){
    		
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteHeaderElement($headerID);
    	}
    	
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deleteMapElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    
    	$mapID = $this->_request->getPost("elmID");
    	if(!empty($mapID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteMapElement($mapID);
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deleteSectionHeaderElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    	 
    	$headerID = $this->_request->getPost("elmID");
    	if(!empty($headerID) && is_numeric($headerID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteSectionHeaderElement($headerID);
    	}
    	 
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deleteTextElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    
    	$headerID = $this->_request->getPost("elmID");
    	if(!empty($headerID) && is_numeric($headerID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteTextElement($headerID);
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deleteLinkElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    
    	$linkID = $this->_request->getPost("elmID");
    	if(!empty($linkID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteLinkElement($linkID);
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deleteSublinkElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    
    	$linkID = $this->_request->getPost("elmID");
    	if(!empty($linkID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteSublinkElement($linkID);
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deleteFormElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    
    	$linkID = $this->_request->getPost("elmID");
    	if(!empty($linkID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteFormElement($linkID);
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deleteListElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    
    	$linkID = $this->_request->getPost("elmID");
    	if(!empty($linkID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteListElement($linkID);
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deleteYtvElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    
    	$linkID = $this->_request->getPost("elmID");
    	if(!empty($linkID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteYtvElement($linkID);
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    function deleteArticleElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    
    	$linkID = $this->_request->getPost("elmID");
    	if(!empty($linkID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteArticleElement($linkID);
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deletePhotoFileElementAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    
    	$linkID = $this->_request->getPost("elmID");
    	if(!empty($linkID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deletePhotoFileElement($linkID);
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    
    function deleteSectionAction(){
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    	 
    	$sectionID = $this->_request->getPost("elmID");
    	if(!empty($sectionID) && is_numeric($sectionID)){
    
    		$linkfFactory = new Content_Models_LinkFactory($this->_request, $this->_response);
    		$linkfFactory->deleteSection($sectionID);   		    		
    	}
    	 
    	$this->renderScript("helper/empty.phtml");
    }
    
   
    
}

	
?>