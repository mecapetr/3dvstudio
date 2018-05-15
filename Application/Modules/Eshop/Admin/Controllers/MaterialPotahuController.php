<?php
class Eshop_MaterialPotahuController extends Eshop_Library_WholeEshop
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
		$this->_redirect("/admin/eshop/material-potahu/pridat");		
	}

	function pridatAction()
	{
		$this->view->subSelected 	= "Přidat materiál potahu";
		$coverMaterial	    		= new Eshop_Models_CoverMaterial();
		$script						= new Library_Scripts();

		$language 		 			= new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/eshop/material-potahu/pridat";
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$filter = $this->setData();
            
            $data = array(			    	        
			    "title"         	=> $this->title['cz']			    			
			);
			    	
			$coverMaterial->insertData($data);
			$id = $coverMaterial->lastID;		
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				//vlozeni dat do slovniku
				$this->updateDictionary('add',"cover-material","coverMaterialID",$id);
			}						    	
		    		
			$this->view->message = "Materiál potahu úspěšně přidána";	
	            
	        
	    }
	      	    
	    	    
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}
		
	function upravitAction()
	{

		$coverMaterial	    	= new Eshop_Models_CoverMaterial();
        $id       = $this->_request->getParam('id');        
	    $enter    = $this->_request->getPost("enter");
		$where    = "coverMaterialID = '$id'";				

		$language 		 			= new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
	    
	    $this->view->action = "/admin/eshop/material-potahu/upravit/id/".$id;	        
		
	    if($enter){
	    	
	    	$script = new Library_Scripts();	
	    	$filter = $this->setData();    
	    	
	            	           		            		
            $data = array(
			    	        
            			"title"      => $this->title['cz']
			    			
			);
			    	
			$coverMaterial->updateData($data,$where);
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				//vlozeni dat do slovniku
				$this->updateDictionary('edit',"cover-material","coverMaterialID",$id);
			}
					
			$this->view->message = "Materiál potahu úspěšně upraven";	
			   
	        
	    }
	     
	    //nastavime hlavni data
	   	$this->setUpdateData($where);
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
		
	function seznamAction()
    {  
    			
		$this->view->subSelected  	= "Materiály pro potahy";
		$coverMaterial	    		= new Eshop_Models_CoverMaterial();
		$this->view->sortableList 	= false;        
		$this->view->action       	= '/admin/eshop/material-potahu/seznam';
		
		if($this->_request->getPost("deleteButton")){
				
			$toDelete = $this->_request->getPost("delete");
			if(!empty($toDelete)){
				
				foreach($toDelete as $del){
					$coverMaterial->deleteData(" coverMaterialID = $del");
				}

				$this->view->message = "Vybrané materialy byly úspěšně smazány.";
					
			}
		}
				
		
		$this->view->allItems = $coverMaterial->getAllItems(null,"title");

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

    	$coverMaterial		= new Eshop_Models_CoverMaterial();
    	
	    $allItems				= new stdClass();
		$allDBItems 			= $coverMaterial->getOneRow($where);	    
		
		if($this->modulesData->jazykoveMutace){		

			$coverMaterialLang			= new  Eshop_Models_CoverMaterialLang();
			$allTranslates 			= $coverMaterialLang->getAllItems($where,"lang");
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
				$allSelectedItems 				= $coverMaterial->getOneRow($where);
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