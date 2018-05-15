<?php
class Newsletter_KategorieController extends Newsletter_Library_WholeNewsletter
{
	
	private $title;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();
    }
    function indexAction()
    {
    	$this->_redirect("/admin/newsletter/kategorie/pridat");
    }
    function pridatAction()
	{
		

		  $translate = Zend_Registry::get('Zend_Translate');	
		
		  $category = new Newsletter_Models_Category();	
		  $scripts = new Library_Scripts();
		  
		  $this->view->action = "/admin/newsletter/kategorie/pridat";
          $this->view->subSelected = "Přidat kategorii";
          
                    
		  if($this->_request->getPost('enter')){

		  	  $filter = $this->setData();
		  	  
		  	  if($filter->isValid('title')){
		  	  	
		      	  	  	$data = array(

		      	  	  	    "title"     => $this->title,
		      	  	  	    "dateAdd"   => date("Y-m-d H:i:s", Time()),
		    				"dateEdit"  => "0000-00-00 00:00:00",
		    				"adminAdd"  => $this->user,
		    				"adminEdit" => "",
	    		    		"priority"  => 1

		      	  	  	);

		      	  	  	$category->insertData($data);	

		      	  	  	$allItems = $category->getAllItems(null,array("priority","categoryID DESC"));
		      	  	  	$scripts->updatePriority($allItems, $category, "categoryID");
		      	  	  	      	  	  	
		      	  	    $this->view->message = $translate->translate("Kategorie úspěšně přidána.");
      	  	  	
		      	  }else{

		      	  	  $this->getBackData();		      	  	
		      	      $this->view->error = $translate->translate("Nevyplnili jste povinné údaje!");		      	  	

		      	  }


		  }
		  
		  $this->view->allItems = $category->getAllItems(null,"dateAdd");		
	
	    //vlozime placeholdery
	    $this->addPlaceholders();		
	}        
	
	function seznamAction()
	
	{
		 
		
		$this->view->subSelected  = "Seznam kategorií";
		$category                 = new Newsletter_Models_Category();
		$this->view->action       = '/admin/newsletter/kategorie/seznam';
	  
		if($this->_request->getPost("search")){

			$searchText = trim($this->_request->getPost("input-text"));
			$where = " title LIKE '$searchText%'";
			$this->view->allItems = $category->getAllItemsWhere($where);

		}else{
			 
			$totaly      = $category->getCount();
			$descAsc     = $this->_request->getParam('poradi');
			$controller  = "zakaznik";

			$this->order($descAsc,$controller,$category,$totaly,null,"priority");

		}
			
		$this->view->allSearchItems = $category->getAllItems(null,"dateAdd");
		$this->view->controller = "category";
			
		//vlozime placeholdery
		$this->addPlaceholders();
		
	
	}
	
	function upravitAction()
	{
		
			$translate 	= Zend_Registry::get('Zend_Translate');
			$category 	= new Newsletter_Models_Category();
				
			$id = $this->_request->getParam('id');
			$where = " categoryID = '$id' ";
				
			$this->view->action = "/admin/newsletter/kategorie/upravit/id/".$id;
	
			 
			if($this->_request->getPost('enter')){
	
				$filter = $this->setData();
	
				if($filter->isValid('title')){
				  
					$data = array(
	
				        "title"     => $this->title,
				    	"dateEdit"  => date("Y-m-d H:i:s", Time()),
				    	"adminEdit" => $this->user
					);
	
					$category->updateData($data,$where);
					 
					$this->view->message = $translate->translate("Kategorie úspěšně upravena.");
	
				}else{
	
					$this->getBackData();
					$this->view->error = $translate->translate("Nevyplnili jste povinné údaje!");
	
				}
	
	
			}
				
			$this->view->allItems = $category->getOneRow($where,"dateAdd");
				
				
			//vlozime placeholdery
			$this->addPlaceholders();
		
	
	}
	function smazatAction()
	{
		 
		
		$translate 	= Zend_Registry::get('Zend_Translate');
		$category 	= new Newsletter_Models_Category();
		$scripts    = new Library_Scripts();
			
		$id = $this->_request->getParam('id');
		$where = " categoryID = '$id' ";
		$result = $category->getOneRow($where);
		$this->view->oneRow = $result;

		if($this->_request->isPost()){

			$delete = $this->_request->getPost('delete');

			if($delete == 1){
				 
				$category->deleteData($where);

				$allItems = $category->getAllItems(null,"priority");
				$scripts->updatePriority($allItems, $category, "categoryID");
				 
				$this->view->message = $translate->translate("Kategorie úspěšně smazána.");

			}else{

				$this->_redirect('/admin/newsletter/kategorie/seznam');

			}

		}
			
		//vlozime placeholdery
		$this->addPlaceholders();
	
		
	}
	
	private function getData(){
		 
		 
		$data = array(
	            
	        "title"   => $this->_request->getPost("title")
	    	    
		);
			 
		return $data;
	
	}
	
	private function setData(){
		 
		$filters    = $this->setFilters();
		$validators = $this->setValidators();
		$data       = $this->getData();
	
		$filter = new Zend_Filter_Input($filters, $validators, $data);
	
		$this->title   = $filter->getUnescaped("title");

		 
		return $filter;
	}
	
	private function setFilters(){
	
		$filters = array(
	    	'title' => 'StripTags'
		);
	
		return $filters;
	
	}
	
	private function setValidators(){
	
		$validators = array(
          	'title' => array(  				
                'allowEmpty' => false
		)
		);
	
		return $validators;
	
	}

	private function getBackData(){
	
		$this->view->title = $this->title;
	
	}
	
}

?>