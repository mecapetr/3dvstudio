<?php
class Content_KategorieController extends Content_Library_WholeContent
{
		
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();	
    }
    function indexAction()
	{
		$this->_redirect("/admin/obsah/kategorie/pridat");		
	}

	function pridatAction()
	{
		$this->view->subSelected = "Přidat kategorii";
		$category = new Content_Models_Category();
		$script	  = new Library_Scripts();
		$language = new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/obsah/kategorie/pridat";
        $this->view->homepage = 0;
	    if($enter){
	    		    	
	    		$filter = $this->setData();

	            if($filter->isValid("title-cz")){
	            	           	            		
            		$data = array(
			    	        
			    		"title"         => $this->title['cz'],
			    		"niceTitle"	    => $this->niceTitle['cz'],
            			"active"	    => $this->active,
            			"dateAdd"	 	=> date("Y-m-d H:i:s",time()),
            			"dateEdit"	    => "0000-00-00 00:00:00",
            			"userAdd"	    => $this->user,	            			
            			"priority"      => 1

			    	);
            		$category->insertData($data);
            		$id =  $category->lastID;
            		
            		if($this->modulesData->jazykoveMutace){
            			//vlozeni dat do slovniku
            			$this->updateDictionary('add-category',$id);
            		}
			    	
            		$allItems = $category->getAllItems(null, array("priority","categoryID DESC"));
            		$script->updatePriority($allItems, $category, "categoryID");
			    	
			        $this->view->message = "Kategorie úspěšně přidána";		            	
	            	
		    	}else{
		    		
		    		$this->getBackData();
		    		$this->view->error = "Nevyplnili jste povinné údaje";
		    		
		    	}
   
	    }
    
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}
		
	function upravitAction()
	{
	
		$category = new Content_Models_Category();
		$language = new Models_Language_Language();
		
        $id       = $this->_request->getParam('id');        
	    $enter    = $this->_request->getPost("enter");
		$where    = "categoryID = '$id'";
				    
	    $this->view->action = "/admin/obsah/kategorie/upravit/id/".$id;
	    
		$oldData = $category->getOneRow($where);

		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
        
	    if($enter){
	    	
	    		$script = new Library_Scripts();	
	    		$filter = $this->setData();    
	    	
	    		if($filter->isValid("title-cz")){
	            	           			            		
            		$data = array(
			    	        
			    		"title"         => $this->title['cz'],
			    		"niceTitle"	    => $this->niceTitle['cz'],
            			"active"	    => $this->active,
            			"dateEdit"	    => date("Y-m-d H:i:s",time()),
            			"userEdit"	    => $this->user
			    			
			    	);
			    	
			    	
			    	$category->updateData($data,$where);				    	

					//pridani do slovniku
					if($this->modulesData->jazykoveMutace){
				    	//vlozeni dat do slovniku
					    $this->updateDictionary('delete-category',$oldData->categoryID);
				    	$this->updateDictionary('add-category',$id);	
					}
					
			        $this->view->message = "Kategorie úspěšně upravena";					        
	               
		    	}else{
		    		
		    		$this->getBackData();
		    		$this->view->error = "Nevyplnili jste povinné údaje";
		    		
		    	}
	    	
	        
	    }
	     
	    //nastavime hlavni data
	    $this->setUpdateData($category,$where);

	    $this->addPlaceholders();
	}
	
		
	function seznamAction()
    {  
    			
		$this->view->subSelected  = "Seznam kategorií";
    	$category                 = new Content_Models_Category();
    	$script                   = new Library_Scripts();
		$this->view->sortableList = true;        
		$this->view->action       = '/admin/obsah/kategorie/seznam';
		
		if($this->_request->getPost("deleteButton")){
				
			$toDelete = $this->_request->getPost("delete");
			if(!empty($toDelete)){
		
				foreach($toDelete as $del){
					$this->updateDictionary('delete-category',$del);
					$category->deleteData("categoryID = '$del'");
				}
				
				$allItems = $category->getAllItems(null, array("priority","categoryID DESC"));
				$script->updatePriority($allItems, $category, "categoryID");

				$this->view->message = "Vybrané odazy byly úspěšně smazány.";
					
			}
		}
		
		$this->view->allItems = $category->getAllItems(null, "priority");
		$this->view->controller = "category";
		

	    //vlozime placeholdery
	    $this->addPlaceholders();

    }
    
    
	private function getData(){
		
		$data = array(			    
			"active" => $this->_request->getPost("active")
        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$data["title-".$val->suffix] = $this->_request->getPost("title-".$val->suffix);
		}    
        
        return $data;
		
	}
	
	private function setData(){
		
		$filters    = $this->setFilters();
	    $validators = $this->setValidators();
	    $data       = $this->getData();
	    $script		= new Library_Scripts();
	    $filter = new Zend_Filter_Input($filters, $validators, $data);

		$this->active = $filter->getUnescaped("active");		
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$this->title[$val->suffix] 			= $filter->getUnescaped("title-".$val->suffix);
			
			if($val->generateNiceTitle){					
				$this->niceTitle[$val->suffix] = $script->url($this->title[$val->suffix]);
			}else{
				$this->niceTitle[$val->suffix] = "";
			}
		}

	    return $filter;
	}

    private function setUpdateData($category,$where){
    	
    	//nastavime prekladova data ze slovniku, pokud je zapnut modul jazykobeMutace
		if($this->modulesData->jazykoveMutace){
			//znovunacteme upravene soubory pro preklad 	
			$translatePlugin = new Models_Language_LanguagePlugin();
			$translatePlugin->refreshLanguage();			
		}
		
		$translate 		 		= Zend_Registry::get('Zend_Translate');
	    $allItems				= new stdClass();
		$allDBItems 			= $category->getOneRow($where);	    
		$allDBItems->dateAdd	= date("j.n.Y",strtotime($allDBItems->dateAdd));
		    
		if($this->modulesData->jazykoveMutace){
			//nastavime vsechny jazyky
			//jazyky vzdy prelozime a ulozime do pole
			foreach($this->allLanguageMutations as $val){
			    	
			    $translate->setLocale($val->suffix);
			    $allItems->title[$val->suffix] 	= trim($translate->translate("category".$allDBItems->categoryID."title"));
			    
			}
		}else{	
				$allSelectedItems = $category->getOneRow($where);
				$allItems->title['cz'] 	= $allSelectedItems->title;
				
		}
		
		$allDBItems->title 		= $allItems->title;
		
	   	$this->view->allItems 	= $this->allItems = $allDBItems;
	   	 	
		
	}
	private function setFilters(){
		
		$filters = array();
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$validators["title-".$val->suffix] 	= 'StripTags';
		}
		
        return $filters;
		
	}
	
	private function setValidators(){
		
		$validators = array(
      	    
            'active' => array(  				
                'allowEmpty' => false
            )

        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			if($val->suffix != 'cz'){
				$validators["title-".$val->suffix]['allowEmpty'] = true;
			}
		}
		
		$validators["title-cz"]['allowEmpty'] = false;
        return $validators;
		
	}
	    
    private function getBackData(){

    	$this->view->title = $this->title;

    }
    
}

?>