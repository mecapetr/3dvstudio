<?php
class Slider_SliderController extends Library_Adminbase
{
	
	protected 	$title;
	private 	$link;	
	private 	$date;
	protected	$text;
	private 	$mainPhoto;
	private 	$photos;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
		$this->setDefault();	
    	$this->setLinks();
		$this->view->selected = "Slider";	
    }
    function indexAction()
	{
		$this->_redirect("/admin/slider/pridat");		
	}

	function pridatAction()
	{
		$this->view->subSelected = "Přidat slider";
		$slider			    = new Slider_Models_Slider();
		$script				= new Library_Scripts();
		$this->view->date	= date("d.m.Y H:i:s",Time());
	  	
	    //pro vlozeni fotek a videi. Videa mají­ předponu "v"
	    $language 		 = new Models_Language_Language();
		$photo           = new Models_Photo();
		$video           = new Models_Video();
		$connectTable	 = new Slider_Models_PhotoSlider();
		$folder          = "Temp";
		$table           = "photo_slider";
		$path            = "Public/Images/";
		$tableID         = "sliderID";
		$newFolder		 = "Slider";
		// konec vlozeni fotek a videi
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action = "/admin/slider/pridat";
        
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$filter = $this->setData();
        	            
            if($filter->isValid("title-cz")){
            	if(!empty($this->photos) ){            	
			    	
            		$data = array(
			    	        
			    			"title"      => $this->title['cz'], 
			    			"text"       => $this->text['cz'],  
			    			"link"       => $this->link,           				
            				"active"	 => 1,
            				"userAdd"	 => $this->user,
            				"dateAdd"	 => date("Y-m-d H:i:s",Time()),
            				"priority"   => 1
			    			
			    	);
			    	
			    	$slider->insertData($data);
			    	$id = $slider->lastID;
			    	
					//pridani fotek
					$this->addPhoto($id,$table,$connectTable,$tableID,$folder,$newFolder);					
		    		setcookie("mainPhoto","",NULL,"/"); 
		    									    
			    	
		    		$allItems = $slider->getAllItems(null,array("priority","sliderID DESC"));
		    		$script->updatePriority($allItems, $slider, "sliderID");
            	
					//pridani do slovniku
					if($this->modulesData->jazykoveMutace){
					    //vlozeni dat do slovniku
					    $this->updateDictionary('add-slider',$id);
					}
					
			        $this->view->message = "Slider úspěšně přidána";	
            	
            	}else{            		
		    		$this->getBackData();
		    		$this->view->error = "Nevybrali jste fotku";
            	}
	    	}else{
	    		
	    		$this->getBackData();
	    		$this->view->error = "Nevyplnili jste povinné údaje";
	    		
	    	}
	        
	    }
	    
	    if(isset($_COOKIE["mainPhoto"]))$this->view->mainPhoto  = $_COOKIE["mainPhoto"];		
		$this->view->allTempPhotos 								= $photo->getTempPhotos($this->adminUserID,$table);
        $this->view->path        								= $path;
	    $this->view->table         								= $table;
	    $this->view->tableID = $this->view->vTableID	    	= $tableID;
	    $this->view->folder  = $this->view->vFolder				= $folder;
			
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}
	
	function upravitAction()
	{
	
		$slider			= new Slider_Models_Slider();
	    $language 		= new Models_Language_Language();
		
        $id     = $this->_request->getParam('id');        
	    $enter  = $this->_request->getPost("enter");
		$where  = "sliderID = '$id'";
		
		//stare zaznamy slider
		//$oldslider = $slider->getOneRow($where);
		
		//pro vlozeni fotek
		$photo           = new Models_Photo();
		$video           = new Models_Video();
		$connectTable	 = new Slider_Models_PhotoSlider();
		$folder          = "Slider";
		$table           = "photo_slider";
		$path            = "Public/Images/";
		$tableID         = "sliderID";
		$newFolder		 = "Slider";
		$tableIDvalue	 = $id;
		// konec vlozeni fotek
		
	    
	    $this->view->action = "/admin/slider/upravit/id/".$id;
        
		$oldData = $slider->getOneRow($where);
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    if($enter){
	    	
	    	$script = new Library_Scripts();	
	    	$filter = $this->setData();    
	    	
            if($filter->isValid("title-cz")){
    	
                	$data = array(
			    	        
			    	    "title"     => $this->title['cz'],
			    	    "text"      => $this->text['cz'], 
			    		"link"      => $this->link, 
            			"userEdit"	=> $this->user,
			    	    "dateEdit"  => date("Y-m-d H:i:s",Time())
                	
			    	);
			    	
			    	$slider->updateData($data,$where);
					
            
					//pridani fotek		
					$photo->updateMainStatusToZero($id,$table,$tableID); // nastavime vsechny mainPhoto na 0
					$this->updateOldPhoto($id,$table,$tableID);
					$this->addPhoto($id,$table,$connectTable,$tableID,$folder,$newFolder);
									
            
					//pridani do slovniku
					if($this->modulesData->jazykoveMutace){
					    //vlozeni dat do slovniku
						$this->updateDictionary('delete-slider',$oldData);
					    $this->updateDictionary('add-slider',$id);	
					}
								
			        $this->view->message = "Slider úspěšně upraven";	
			    
	    	}else{
	    		
	    		$this->getBackData();
	    		$this->view->error = "Nevyplnili jste povinné údaje";
	    		
	    	}
	        
	    }
	                
	    //nastavime hlavni data
	    $this->setUpdateData($slider,$where);	
	    //nastavime fotky    
		$this->setPhotosData($photo,$id,$table,$tableID,$this->adminUserID);	
		
        $this->view->path          								= $path;
	    $this->view->table        		 						= $table;
	    $this->view->tableID = $this->view->vTableID	    	= $tableID;
	    $this->view->folder  = $this->view->vFolder				= $folder;
	    $this->view->tableIDvalue  								= $tableIDvalue;
					
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	
	function seznamAction()
    {  		
		$this->view->subSelected = "Seznam slideru";
    	$sortableList             = true;
    	$slider                   = new Slider_Models_Slider();
		$this->view->sortableList = $sortableList;        
		$this->view->action       = '/admin/slider/seznam';
		$controller               = "slider";

		if($sortableList){
			
			$items 					 = $slider->getAllItems(null,"priority");
			$this->view->allItems    = $items;
			$this->view->controller  = $controller;
			
		}else{
			
			$totaly              = $slider  ->getCount();		
			$descAsc             = $this   ->_request->getParam('poradi');
	        
	        
			$this->order($descAsc,$controller,$slider,$totaly,'1');
		}						

	    //vlozime placeholdery
	    $this->addPlaceholders();

    }
	
	function smazatAction()
    {  		
		  
		$slider     	 = new Slider_Models_Slider();
        $photo      	 = new Models_Photo();
        $script          = new Library_Scripts();
		$photoslider     = new Slider_Models_PhotoSlider();		
		$table           = "photo_slider";
		$tableID         = "sliderID";
		
		$id =      $this->_request->getParam('id');
		$where =   "sliderID = '$id' ";
		$result =  $slider->getOneRow($where);	  
		  		  
		$this->view->oneRow = $result;
		  
		if($this->_request->isPost()){
		  	
		    $delete = $this->_request->getPost('delete');
		    if($delete == 1){
                 
		      $allPhotos = $photo->getAllPhotos($id,$table,$tableID);	         
		      	 		      	 
		      foreach($allPhotos as $value){
		      	 	
		      	 unlink("./Public/Images/Slider/".$value->title);
		         unlink("./Public/Images/Slider/mala-".$value->title);
		         unlink("./Public/Images/Slider/stredni-".$value->title);
		         $wherePhoto = "photoID = '$value->photoID' ";
		         $photo->deleteData($wherePhoto); 
		         
		         //vymazeme ze slovniku pokud je zaply modul
		         if($this->modulesData->jazykoveMutace)
		         	$this->updateDictionary('delete-photo',$value);
		          
		      }
      
		      $slider       -> deleteData($where);
              $photoslider  -> deleteData($where); 
			  
              $allItems = $slider->getAllItems(null,array("priority"));
              $script->updatePriority($allItems, $slider, "sliderID");
              
              //smazání dat ze slovniku  
              if($this->modulesData->jazykoveMutace)               
			  	$this->updateDictionary('delete-slider',$result);
			  
		      $this->view->message = "Slider úspěšně smazána";
		      	 
		    }else{
		      	
		          $this->_redirect('/admin/slider/seznam');	
		      	
		    }
		  	
		}						
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();
    	  
		
    }
    
	
        
	private function getData(){
		
		$data = array(
            "link"       => $this->_request->getPost("link"),
		    "photos"   	  => $this->_request->getPost("photos")
        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$data["title-".$val->suffix] 		= $this->_request->getPost("title-".$val->suffix);
				$data["text-".$val->suffix] 		= $this->_request->getPost("text-".$val->suffix);
		}    
        
        return $data;
		
	}
	
	private function setData(){
		
		$filters    = $this->setFilters();
	    $validators = $this->setValidators();
	    $data       = $this->getData();
	    $script		= new Library_Scripts();
	    $filter = new Zend_Filter_Input($filters, $validators, $data);

	    $this->title       = $filter->getUnescaped("title");	
	    $this->link        = $filter->getUnescaped("link");   
	    $this->photos	   = $filter->getUnescaped("photos");
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$this->title[$val->suffix] 			= $filter->getUnescaped("title-".$val->suffix);
				$this->text[$val->suffix] 			= $filter->getUnescaped("text-".$val->suffix);
		}
		
	    return $filter;
	}

    private function setUpdateData($slider,$where){
    	
    	//nastavime prekladova data ze slovniku, pokud je zapnut modul jazykobeMutace
		if($this->modulesData->jazykoveMutace){
			//znovunacteme upravene soubory pro preklad 	
			$translatePlugin = new Models_Language_LanguagePlugin();
			$translatePlugin->refreshLanguage();			
		}
		
		$translate 		 		= Zend_Registry::get('Zend_Translate');
	    $allItems				= new stdClass();
		$allDBItems 			= $slider->getOneRow($where);	    
		    
		if($this->modulesData->jazykoveMutace){
			//nastavime vsechny jazyky
			//jazyky vzdy prelozime a ulozime do pole
			foreach($this->allLanguageMutations as $val){
			    	
			    $translate->setLocale($val->suffix);
			    $allItems->title[$val->suffix] 			= trim($translate->translate("slider".$allDBItems->sliderID."title"));
			    $allItems->text[$val->suffix] 			= trim($translate->translate("slider".$allDBItems->sliderID."text"));
			}
		}else{	
				$allSelectedItems = $slider->getOneRow($where);
				$allItems->title['cz'] 			= $allSelectedItems->title;
				$allItems->text['cz'] 			= $allSelectedItems->text;
		}
		
		$allDBItems->title 		= $allItems->title;
		$allDBItems->text 		= $allItems->text;
	   	$this->view->allItems = $this->allItems = $allDBItems;
	   	 	
		
	}
	private function setFilters(){
		
		$filters = array(
            'link'  	=> 'StripTags'		
        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$filters["title-".$val->suffix] 		= 'StripTags';				
		}
        return $filters;
		
	}
	
	private function setValidators(){
		
		$validators = array(
      	    'link' => array(  				
                'allowEmpty' => true
            ),
            'photos' => array(  				
                'allowEmpty' => false
            )

        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$validators["title-".$val->suffix]['allowEmpty'] 		= true;
				$validators["text-".$val->suffix]['allowEmpty'] 		= true;
		}
        return $validators;
		
	}
	
	private function setLinks(){
		
		$links = array();
		
		$links[0] = array(
			'title' => "Přidat slider",
		    'link'  => "/admin/slider/pridat"
		);
		$links[1] = array(
			'title' => "Seznam slideru",
		    'link'  => "/admin/slider/seznam"
		);
				
		$this->view->links = $links;
		
	}
	
    
    private function getBackData(){
    	
    		$this->view->title     = $this->title;
    		$this->view->link      = $this->link;
    		$this->view->text      = $this->text;
    	
    }

}

?>