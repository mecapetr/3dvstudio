<?php
class Eshop_FiltryController extends Eshop_Library_WholeEshop
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
		$this->_redirect("/admin/eshop/filtry/pridat");		
	}

	function pridatAction()
	{
		$this->view->subSelected 	= "Přidat filtr";
		$filter	    				= new Eshop_Models_Filter();
		$script						= new Library_Scripts();

		$language 		 			= new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/eshop/filtry/pridat";
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$f = $this->setData();
            
            $data = array(			    	        
			    "title"         	=> $this->title['cz']			    			
			);
			    	
			$filter->insertData($data);
			$id = $filter->lastID;		
			

			$allItems = $filter->getAllItems(null, "priority");
			$script->updatePriority($allItems, $filter, "filterID");
			
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				//vlozeni dat do slovniku
				$this->updateDictionary('add',"filter","filterID",$id);
			}						    	
		    		
			$this->view->message = "Filtr úspěšně přidán";	
	            
	        
	    }
	      	    
	    	    
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}
		
	function upravitAction()
	{

		$filter	  = new Eshop_Models_Filter();
        $id       = $this->_request->getParam('id');        
	    $enter    = $this->_request->getPost("enter");
		$where    = "filterID = '$id'";				

		$language 		 			= new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
	    
	    $this->view->action = "/admin/eshop/filtry/upravit/id/".$id;	        
		
	    if($enter){
	    	
	    	$script = new Library_Scripts();	
	    	$f = $this->setData();    
	    	
	            	           		            		
            $data = array(
			    	        
            	"title"      => $this->title['cz']
			    			
			);
			    	
			$filter->updateData($data,$where);
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				//vlozeni dat do slovniku
				$this->updateDictionary('edit',"filter","filterID",$id);
			}
					
			$this->view->message = "Filtr úspěšně upraven";	
			   
	        
	    }
	     
	    //nastavime hlavni data
	   	$this->setUpdateData($where);
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
		
	function seznamAction()
    {  
    			
		$this->view->subSelected  	= "Filtry";
		$filter	    				= new Eshop_Models_Filter();
		$this->view->sortableList 	= true;        
		$this->view->action       	= '/admin/eshop/filtry/seznam';
		
		if($this->_request->getPost("deleteButton")){
				
			$toDelete = $this->_request->getPost("delete");
			if(!empty($toDelete)){
				
				foreach($toDelete as $del){
					$filter->deleteData(" filterID = $del");
				}

				$this->view->message = "Vybrané filtry byly úspěšně smazány.";
					
			}
		}
				
		
		$this->view->allItems = $filter->getAllItems(null,"priority");

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

    	$filter		= new Eshop_Models_Filter();
    	
	    $allItems				= new stdClass();
		$allDBItems 			= $filter->getOneRow($where);	    
		
		if($this->modulesData->jazykoveMutace){		

			$filterLang				= new  Eshop_Models_FilterLang();
			$allTranslates 			= $filterLang->getAllItems($where,"lang");
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
				$allSelectedItems 				= $filter->getOneRow($where);
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