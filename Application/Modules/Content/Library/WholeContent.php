<?php
class Content_Library_WholeContent extends Library_Adminbase
{
	
		
	//funkce pr�stupn� v�em action
	function init()
    {    
		$this->setDefault();	
		$this->view->selectedMenu = "Obsah";	
    }
	
    public function generateRelatedLinks($id,$linkID,$link){
    	$articleLink = new Content_Models_ArticleLink();
    	$i = 0;
    	$parentID = 0;
    	$linkDataArr = array();
    	do{
    		
    		$oneLink 					= $link->getOneRow("linkID = '$linkID'");
    		$parentID 					= $linkID = $oneLink->parentID;
    		
    		$linkData    				= new stdClass();
    		$linkData->linkID			= $oneLink->linkID;
    		
    		//jestli se jedna o posledni odkaz (to znamena odkaz kteremu je clanek primo prirazen)
    		if($i == 0)
    			$linkData->isLastLink	= 1;	
    		else 
    			$linkData->isLastLink	= 0;
    			
    		$linkDataArr[$i] 			= $linkData;
    		$i++;
    		
    	}while($parentID != 0);
    	
    	//nyni musime jeste nastavit levely jednotlivych linku tak aby hlavni odkaz zacinal vzdy level = 0
    	for($j = $i - 1; $j >= 0; $j--){
    		$articleLink->insertData(array("articleID" => $id, "linkID" => $linkDataArr[$j]->linkID, "level" => ($i - 1 - $j), "isLastLink" => $linkDataArr[$j]->isLastLink));
    	}
    }
}

?>