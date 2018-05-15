<?php
class Eshop_BarvyPotahuController extends Eshop_Library_WholeEshop
{

	protected $title;

	protected $allLanguageMutations;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();	
    }
    function indexAction()
	{
		$this->_redirect("/admin/eshop/barvy-potahu/pridat");		
	}

	function pridatAction()
	{
		$this->view->subSelected 	= "Přidat barvu potahu";
		$coverColor	    			= new Eshop_Models_CoverColor();
		$script						= new Library_Scripts();

		$language 		 			= new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/eshop/barvy-potahu/pridat";
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$filter = $this->setData();
            
            $data = array(			    	        
			    "title"         	=> $this->title['cz']			    			
			);
			    	
			$coverColor->insertData($data);
			$id = $coverColor->lastID;		
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				//vlozeni dat do slovniku
				$this->updateDictionary('add',"cover-color","coverColorID",$id);
			}						    	
		    		
			$this->view->message = "Barva potahu úspěšně přidána";	
	            
	        
	    }
	      	    
	    	    
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}
		
	function upravitAction()
	{

		$coverColor	    	= new Eshop_Models_CoverColor();
        $id       = $this->_request->getParam('id');        
	    $enter    = $this->_request->getPost("enter");
		$where    = "coverColorID = '$id'";				

		$language 		 			= new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
	    
	    $this->view->action = "/admin/eshop/barvy-potahu/upravit/id/".$id;	        
		
	    if($enter){
	    	
	    	$script = new Library_Scripts();	
	    	$filter = $this->setData();    
	    	
	            	           		            		
            $data = array(
			    	        
            			"title"      => $this->title['cz']
			    			
			);
			    	
			$coverColor->updateData($data,$where);
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				//vlozeni dat do slovniku
				$this->updateDictionary('edit',"cover-color","coverColorID",$id);
			}
					
			$this->view->message = "Barva potahu úspěšně upravena";	
			   
	        
	    }
	     
	    //nastavime hlavni data
	   	$this->setUpdateData($where);
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
		
	function seznamAction()
    {  
    			
		$this->view->subSelected  	= "Barvy pro potahy";
		$coverColor	    			= new Eshop_Models_CoverColor();
		$this->view->sortableList 	= false;        
		$this->view->action       	= '/admin/eshop/barvy-potahu/seznam';
		
		if($this->_request->getPost("deleteButton")){
				
			$toDelete = $this->_request->getPost("delete");
			if(!empty($toDelete)){
				
				foreach($toDelete as $del){
					$coverColor->deleteData(" coverColorID = $del");
				}

				$this->view->message = "Vybrané barvy byly úspěšně smazány.";
					
			}
		}
				
		
		$this->view->allItems = $coverColor->getAllItems(null,"title");

	    //vlozime placeholdery
	    $this->addPlaceholders();

    }
    	      

    private function getData(){
    
    	$data = array(
    	);
    
    	//nastavime vsechny jazykove verze
    	foreach($this->allLanguageMutations as $val){
    		$data["title-".$val->suffix] 		= $this->_request->getPost("title-".$val->suffix);
    	}
    
    	return $data;
    
    }

	private function setData(){
		
		$filters    = $this->setFilters();
	    $validators = $this->setValidators();
	    $data       = $this->getData();
	    $script		= new Library_Scripts();
	    $filter = new Zend_Filter_Input($filters, $validators, $data);

		
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$this->title[$val->suffix] 			= $filter->getUnescaped("title-".$val->suffix);

				if(empty($this->title[$val->suffix])){
					$this->title[$val->suffix] 		= "";
				}
		}		
		
	    return $filter;
	}

    private function setUpdateData($where){

    	$coverColor		= new Eshop_Models_CoverColor();
    	
	    $allItems				= new stdClass();
		$allDBItems 			= $coverColor->getOneRow($where);	    
		
		if($this->modulesData->jazykoveMutace){		

			$coverColorLang			= new  Eshop_Models_CoverColorLang();
			$allTranslates 			= $coverColorLang->getAllItems($where,"lang");
			$allTranslatesArr		= array();
			foreach ($allTranslates as $val){
				$allTranslatesArr[$val->lang] = $val;
			}
			
			//nastavime vsechny jazyky
			//jazyky vzdy prelozime a ulozime do pole
			foreach($this->allLanguageMutations as $val){
				
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->title[$val->suffix] 		= $allTranslatesArr[$val->suffix]->title 		: $allItems->title[$val->suffix] = "";
			    
			}
		}else{	
				$allSelectedItems 				= $coverColor->getOneRow($where);
				$allItems->title['cz'] 			= $allSelectedItems->title;
		}
		
		$allDBItems->title 		= $allItems->title;
		
	   	$this->view->allItems 				= $this->allItems = $allDBItems;

	}
	private function setFilters(){
		
		$filters = array(
        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$validators["title-".$val->suffix] 	= 'StripTags';
		}
		
        return $filters;
		
	}
	
	private function setValidators(){
		
		$validators = array(
      	    

        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$validators["title-".$val->suffix]['allowEmpty'] 		= true;
		}
		
        return $validators;
		
	}

}

?>