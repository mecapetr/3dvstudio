<?php
class Eshop_PotahyController extends Eshop_Library_WholeEshop
{

	protected $title;
	protected $titleSupplier;
	protected $text;
	protected $shortcut;
	protected $supplier;
	protected $coverMaterialID;
	
	protected $allLanguageMutations;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();	
    }
    function indexAction()
	{
		$this->_redirect("/admin/eshop/potahy/pridat");		
	}

	function pridatAction()
	{
		$this->setSublinks();
		$this->view->subSelected = "Přidat potah";
		$cover			    = new Eshop_Models_Cover();
		$supplier  			= new Eshop_Models_Supplier();
		$coverMaterial 	 	= new Eshop_Models_CoverMaterial();
		$script				= new Library_Scripts();
		
		$this->view->showOnlyMainSublink 	= true;
		$this->view->sublinkTitle			= "Základní údaje";
	  	
	    $language 		 = new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/eshop/potahy/pridat";
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$filter = $this->setData();
	            		
            $data = array(			    	        
			    "title"         	=> $this->title['cz'],
			    "titleSupplier"	   	=> $this->titleSupplier,
			    "text"         	 	=> $this->text['cz'],
            	"shortcut"	    	=> $this->shortcut	,
            	"coverMaterialID"	=> $this->coverMaterialID		    			
			 );
			    	
			 $cover->insertData($data);
			 $id = $cover->lastID;
			    
			$this->insertSuppliers($id);											    	
		    						    	
			$allItems = $cover->getAllItems(null, array("priority","coverID DESC"));
			$script->updatePriority($allItems, $cover, "coverID");
            
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				//vlozeni dat do slovniku
				$this->updateDictionary('add',"cover","coverID",$id);
			}
			$this->_redirect("/admin/eshop/potahy/upravit/id/" . $id . "/added/1");
	            	
	        
	    }    	    
	    $this->view->allSuppliers 	= $supplier->getAllItems(null,"priority");
	    $this->view->allCoverMaterials 		= $coverMaterial->getAllItems(null,"title");
	    
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}

	function upravitAction()
	{
	
		$cover	  = new Eshop_Models_Cover();
		$language = new Models_Language_Language();
		$coverMaterial 	 	= new Eshop_Models_CoverMaterial();
		$id       = $this->_request->getParam('id');
		$added    = $this->_request->getParam('added');
		$enter    = $this->_request->getPost("enter");
		$where    = "coverID = '$id'";
	
		$this->view->sublinkTitle = "Základní údaje";
	
		$this->setSublinks($id);
		 
		$this->view->action = "/admin/eshop/potahy/upravit/id/".$id;
		 
		$oldData = $cover->getOneRow($where);
	
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
	
	
		if($enter){
	
			$script = new Library_Scripts();
			$filter = $this->setData();
	
			$data = array(
					"title"         	=> $this->title['cz'],
					"titleSupplier"	   	=> $this->titleSupplier,
					"text"         	 	=> $this->text['cz'],
					"shortcut"	    	=> $this->shortcut,
            		"coverMaterialID"	=> $this->coverMaterialID
			);
	
			$cover->updateData($data,$where);
	
			$this->insertSuppliers($id);
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				$this->updateDictionary('edit',"cover","coverID",$id);
			}
			$this->view->message = "Potah úspěšně upraven";
			 
		}
	
		if(!empty($added)){
			$this->view->message = "Potah úspěšně přidán";
		}
		//nastavime hlavni data
		$this->setUpdateData($cover,$where);

		$this->view->allCoverMaterials 		= $coverMaterial->getAllItems(null,"title");
		 
		//vlozime placeholdery
		$this->addPlaceholders();
	}	
	
	
		
	function seznamAction()
    {  

		$this->view->subSelected  	= "Potahy";
    	$cover 						= new Eshop_Models_Cover();
    	$photo						= new Models_Photo();
    	$photoCover					= new Eshop_Models_PhotoCover();
    	$supplierCover				= new Eshop_Models_SupplierCover();
    	$supplier					= new Eshop_Models_Supplier();
    	
    	
		$this->view->sortableList 	= true;        
		$this->view->action       	= '/admin/eshop/potahy/seznam';
		
		if($this->_request->getPost("deleteButton")){
				
			$toDelete = $this->_request->getPost("delete");
			if(!empty($toDelete)){

		
				foreach($toDelete as $del){
					$cover->deleteData("coverID = $del");
					$supplierCover->deleteData("coverID = $del");
			 		$this->updateDictionary('delete',"cover","coverID",$del);
			 		
			 		$allPhotos 	= $photo->getAllPhotos($del,"photo_cover","coverID");
			 		 
			 		foreach($allPhotos as $value){
			 		
			 			@unlink("./Public/Images/Cover/".$value->title);
			 			@unlink("./Public/Images/Cover/mala-".$value->title);
			 			@unlink("./Public/Images/Cover/stredni-".$value->title);
			 			@unlink("./Public/Images/Cover/velka-".$value->title);
			 			@unlink("./Public/Images/Cover/maxi-".$value->title);
			 			$wherePhoto = "photoID = '$value->photoID' ";
			 			$photo->deleteData($wherePhoto);
			 			$photoCover->deleteData($wherePhoto);
			 			$this->updateDictionary('delete','photo','photoID',$value->photoID);
			 		}
				}

				$this->view->message = "Vybrané potahy byly úspěšně smazány.";
					
			}
		}
			

		$this->view->allItems 		= $cover->getAllItemsWithSupliers(null,"priority");
		$this->view->allSuppliers 	= $supplier->getAllItems();
		
	    //vlozime placeholdery
	    $this->addPlaceholders();

    }

    function fotkyAction()
    {
    
    	$cover	  	= new Eshop_Models_Cover();
    	$coverColor	= new Eshop_Models_CoverColor();
    	$language = new Models_Language_Language();
    	$id       = $this->_request->getParam('id');
    	$enter    = $this->_request->getPost("enter");
    	$delete   = $this->_request->getPost("delete");
    	$upload   = $this->_request->getPost("upload");
    	$where    = "coverID = '$id'";
    
    	$this->view->sublinkTitle			= "Fotky";
    
    	$this->setSublinks($id);
    	 
    	$this->view->action = "/admin/eshop/potahy/fotky/id/".$id;
    	 
    	$oldData = $cover->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    

    	if($enter || $upload || $delete){
    		$this->updateOldPhotos($id);
    	}    			
    	if($upload){    		
    		$this->addNewPhotos($id);
    	}
    	if($delete){
    		$this->deleteOldPhotos($id);
    	}
    	if($enter){
    		$this->view->message .= "<div>Aktuální fotky úspěšně upraveny</div>"; 		
    	}
    	 
    	$this->view->coverData = $cover->getOneRow($where);
    	$this->view->allCoverColors = $coverColor->getAllItems(null,"title");
    	 
    	$this->setPhotosUpdateData($id);
    	
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }

    function getAllPhotosAction()
    {
    
    	Zend_Layout::getMvcInstance()->disableLayout();
    	$photo    		= new Models_Photo();
    	$language 		= new Models_Language_Language();
    	$cover			= new Eshop_Models_Cover();
    	 
    	$id       	= $this->_request->getParam('id');
    	$coverData 	= $cover->getOneRow("coverID = $id");
    	 
    	$response = array();
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    	 
    	$allItems			= new stdClass();
    	$allDBItems 		= $photo->getAllPhotos($id,"photo_cover","coverID","number",Zend_Db::FETCH_ASSOC);
    	$response['status'] 		= 	1;
    	$response['allCoverPhotos'] = 	$allDBItems;
    	$response['coverTitle'] 	= 	$coverData->title;
    	echo json_encode($response);
    	$this->renderScript("placeholders/empty.phtml");
    	 
    }
    function prevodPotahuAction()
    {
/*
    	Zend_Layout::getMvcInstance()->disableLayout();
    	

    	$translate 		 		= Zend_Registry::get('Zend_Translate');

    	$photo    		= new Models_Photo();
    	$photoCover 	= new Eshop_Models_PhotoCover();
    	$photoLang 		= new Models_PhotoLang();
    	$photoOld  		= new Models_PhotoOld();    	
    	$language 		= new Models_Language_Language();
    	$cover			= new Eshop_Models_Cover();
    	$coverLang		= new Eshop_Models_CoverLang();
    	$coverOld		= new Eshop_Models_CoverOld();
    	
    	$allOldCovers 	= $coverOld->getAllItems();
    	set_time_limit(3600);
    	$cover->getDefaultAdapter()->beginTransaction();    	
    	try{
	    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
	    	foreach($allOldCovers as $val){
	    		$cover->insertData(array(
	    			"coverID"			=> $val->coverID,
	    			"coverMaterialID" 	=> 0,
	    			"title" 			=> $val->title,
	    			"titleSupplier" 	=> $val->title2,
	    			"text" 				=> $val->text,
	    			"shortcut" 			=> $val->shortcut,
	    			"priority" 			=> $val->priority 
	    		));
	    		foreach ($this->allLanguageMutations as $lang){
	    			$translate->setLocale($lang->suffix);
	    			
	    			$title 	= $translate->translate("cover".$val->coverID."title");
	    			$text 	= $translate->translate("cover".$val->coverID."text");
	    			$coverLang->insertData(array(
	    				"coverID" 	=> $val->coverID,
	    				"lang"		=> $lang->suffix,
	    				"title"		=> $title,
	    				"text"		=> $text
	    			));
	    		}
	    		$allPhotos = $photoOld->getAllPhotos($val->coverID, "photo_cover_old", "coverID");
	    		foreach($allPhotos as $oldPhoto){
	    			$photo->insertData(array(
	    				"title" => $oldPhoto->title,
	    				"description" => $oldPhoto->description,
	    				"description2" => $oldPhoto->description2,
	    				"number" => $oldPhoto->number,
	    				"mainPhoto" => $oldPhoto->mainPhoto,
	    				"width" => $oldPhoto->width,
	    				"height" => $oldPhoto->height,
	    				"priority" => $oldPhoto->priority
	    			));
	    			$photoID = $photo->lastID;
	    			
	    			$photoCover->insertData(array(
	    				"coverID" 		=> $val->coverID,
	    				"photoID" 		=> $photoID,
	    				"coverColorID"	=> 0
	    			));
		    		foreach ($this->allLanguageMutations as $lang){
		    			
	    				$translate->setLocale($lang->suffix);
	    				
		    			$description 	= $translate->translate("photo".$oldPhoto->photoID);
		    			$description2 	= $translate->translate("photo".$oldPhoto->photoID."description2");
		    			$photoLang->insertData(array(
		    				"photoID" 		=> $photoID,
		    				"lang"			=> $lang->suffix,
		    				"description"	=> $description,
		    				"description2"	=> $description2
		    			));
		    		}
	    		}
	    	}

	    	$cover->getDefaultAdapter()->commit();
    	}catch(Exception $e){
			$cover->getDefaultAdapter()->rollBack();	            // pokud nastane chyba, vrati se zpet tabulka do puvodniho tvaru
			$this->getResponse()->append('main',$e->getMessage() . " " . $e->getTraceAsString());			
		}
		$this->renderScript("placeholders/empty.phtml");
    	*/
    }
    function testFotekAction()
    {

    	Zend_Layout::getMvcInstance()->disableLayout();
    	

    	$translate 		 		= Zend_Registry::get('Zend_Translate');

    	$photo    		= new Models_Photo();
    	$photoCover 	= new Eshop_Models_PhotoCover();
    	$photoLang 		= new Models_PhotoLang();
    	$photoOld  		= new Models_PhotoOld();    	
    	$language 		= new Models_Language_Language();
    	$cover			= new Eshop_Models_Cover();
    	$coverLang		= new Eshop_Models_CoverLang();
    	$coverOld		= new Eshop_Models_CoverOld();
    	
    	$allOldCovers 	= $coverOld->getAllItems();

    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    		$i = 0;
    	foreach($allOldCovers as $val){
    		$allPhotos = $photoOld->getAllPhotos($val->coverID, "photo_cover_old", "coverID");
    		
    		foreach($allPhotos as $oldPhoto){
    			if(!file_exists("./Public/Images/Cover/" . $oldPhoto->title)){
    				print_r($oldPhoto->title."<br/>");
    				$i++;
    			}
    		}
    		
    	}

    	print_r($i);
		$this->renderScript("placeholders/empty.phtml");
    	
    }
    protected function addNewPhotos($id){
    	$files = $_FILES["photos"];

    	$photo          = new Models_Photo();
    	$photoCover 	= new Eshop_Models_PhotoCover();
    	
    	
    	if(count($files["name"]) > 0){
    	
    		$uploadPhoto 				= new Library_UploadFiles();
    		$uploadPhoto->path         	= "Public/Images/Cover";
    		$uploadPhoto->smallHeight   = 135;
	    	$uploadPhoto->smallWidth    = 0;
	    	$uploadPhoto->middleHeight 	= 380;
	    	$uploadPhoto->middleWidth  	= 0;
	    	$uploadPhoto->largeHeight 	= 420;
	    	$uploadPhoto->largeWidth  	= 0;
	    	$uploadPhoto->maxiHeight 	= 760;
	    	$uploadPhoto->maxiWidth  	= 0;
    		$uploadPhoto->ownName   			= true;
    	
    		$photoPriority = 1;

    		$error = "";
    		$success = false;
    		foreach($files["name"] as $key => $file){
    	
    			if(!empty($file)){
    	
    				$photoID  = 0;
    				$fileID   = 0;
    				$filename = "";
    				$er       = false;
    	
    				if($files['type'][$key] == 'image/jpeg' || $files['type'][$key] == "image/pjpeg" || $files['type'][$key] == "image/png" || $files['type'][$key] == "image/gif" || $files['type'][$key] == "image/bmp"){
    	
    					$uploadPhoto->fileName    = $uploadPhoto->niceFile($files['name'][$key]);
    					$uploadPhoto->tmpFileName = $files['tmp_name'][$key];
    					$uploadPhoto->upload();
    					if($uploadPhoto->uploaded){
    	
    						list($w, $h) = getimagesize("./".$uploadPhoto->path."/".$uploadPhoto->fileName);
    	
    						$photo->insertData(array(
    								"title"       => $uploadPhoto->fileName,
    								"description" => "",
    								"mainPhoto"   => 0,
    								"width"       => $w,
    								"height"      => $h,
    								"priority"    => $photoPriority
    						));
    						$photoID = $photo->lastID;
    						$photoPriority++;
    						$success = true;

    						$photoData 				= new stdClass();
    						$photoData->inputName 	= "";
    						$photoData->photoID 	= $photoID;
    						$this->updateDictionary('add','photo','photoID',$photoID,$photoData);
    					}else{
    						$error .= $uploadPhoto->error." <br />";
    						$er = true;
    					}
    	
    				}
    	
    				if(!$er){
    					$photoCover->insertData(array(
    							"photoID"       => $photoID,
    							"coverID"       => $id
    					));
    				}
    	
    			}
    	
    		}
    		if(!empty($error))$this->view->error = $error;
    		if($success)$this->view->message .= "<div>Nové fotky úspěšně nahrány</div>";
    	
    	}
    }

    protected function updateOldPhotos($id){
    
    	$photo          = new Models_Photo();
    	$photoCover 	= new Eshop_Models_PhotoCover();
    	//upravime description u fotek, ktere jiz byly pridany
    	$allUploadedPhotos = $photo->getAllPhotos($id,"photo_cover","coverID");
    	if(count($allUploadedPhotos)>0){
    		$mainPhoto 	= $this->_request->getPost("mainPhoto");
    		foreach($allUploadedPhotos as $item){    
    			
    				$photoDesc 		= $this->_request->getPost("$item->photoID-photoDesc-cz");
    				$photoNumber 	= $this->_request->getPost("$item->photoID-photoNumber");
    				if(empty($photoNumber)){
    					$photoNumber = null;
    				}
    				
    				$isMainPhoto = 0;
    				if($mainPhoto == $item->photoID){
    					$isMainPhoto = 1;
    				}
    				$photo->updateData(array("description" => $photoDesc,"number" => $photoNumber,"mainPhoto" => $isMainPhoto),"photoID = '$item->photoID'");	// aktualizujeme description u fotky

    				$coverColorID 	= $this->_request->getPost("$item->photoID-coverColorID");
    				$photoCover->updateData(array("coverColorID" => $coverColorID),"photoID = '$item->photoID'");	// aktualizujeme cislo u fotky

    				//vybereme popis fotky a vlozime do slovnikovych souboru jednotlive preklady
    				$photoData 				= new stdClass();
    				$photoData->inputName 	= "$item->photoID-photoDesc";
    				$photoData->photoID 	= $item->photoID;
    				$this->updateDictionary('edit','photo','photoID',$item->photoID,$photoData);
    			
    
    		}
    	}
    }
    protected function deleteOldPhotos($id){
    
    	$photo          = new Models_Photo();
    	$photoCover 	= new Eshop_Models_PhotoCover();
    	//upravime description u fotek, ktere jiz byly pridany
    	$allUploadedPhotos = $photo->getAllPhotos($id,"photo_cover","coverID");
    	$photoSelected = false;
    	if(count($allUploadedPhotos)>0){
    		foreach($allUploadedPhotos as $item){
    
    			$delete 		= $this->_request->getPost("$item->photoID-deletePhoto");
    			if(!empty($delete)){
    				$photoSelected = true;
    				$photo->deleteData("photoID = $item->photoID");
    				$photoCover->deleteData("photoID = $item->photoID");
    				$this->updateDictionary('delete','photo','photoID',$item->photoID);
    				@unlink("./Public/Images/Cover/$item->file");
    				@unlink("./Public/Images/Cover/mala-$item->file");
    				@unlink("./Public/Images/Cover/stredni-$item->file");
    				@unlink("./Public/Images/Cover/velka-$item->file");
    				@unlink("./Public/Images/Cover/maxi-$item->file");
    					
    			}
    
    		}
    	}
    	if($photoSelected){
    		$this->view->message .= "<div>Zvolené fotky úspěšně smazány</div>";
    	}else{
    		$this->view->error .= "<div>Nevybrali jste žádnou fotku pro smazání</div>";
    	}
    }
    private function setPhotosUpdateData($id){

    	$photo    		= new Models_Photo();
    	$allItems		= new stdClass();
	    $allDBItems 	= $photo->getAllPhotos($id,"photo_cover","coverID");
	    
		foreach ($allDBItems as $value){
			
				
			$photoLangDb			= new  Models_PhotoLang();
			$allTranslates 			= $photoLangDb->getAllItems("photoID = $value->photoID","lang");
			$allTranslatesArr		= array();
			foreach ($allTranslates as $val){
				$allTranslatesArr[$val->lang] = $val;
			}
			
			//nastavime vsechny jazyky
		    //jazyky vzdy prelozime a ulozime do pole
		    foreach($this->allLanguageMutations as $val){		    	
				
		    	(isset($allTranslatesArr[$val->suffix])) ?  $allItems->description[$val->suffix] = $allTranslatesArr[$val->suffix]->description : $allItems->description[$val->suffix] = "";
		    	
		    }
		    
	    	$value->description = $allItems->description;
			
		}				
		$this->view->allPhotos 		= $this->allPhotos 		= $allDBItems ;	
    }
    private function insertSuppliers($coverID){
    	
    	$supplierCover = new Eshop_Models_SupplierCover();
    	$supplierCover->deleteData("coverID = $coverID");
    	foreach ($this->supplier as $val){
    		
    		$percentage = $this->_request->getPost("percentage-".$val);
    		
    		$supplierCover->insertData(array(
    			"coverID" 		=> $coverID,
    			"supplierID"	=> $val,
    			"percentage"	=> $percentage
    		));
    		
    	}
    }
    private function addCategoryPhoto($linkID){
    	$link			   = new Content_Models_Link();
    	$upload            = new Library_UploadFiles();
    	$path              = "Public/Images/EshopCategory";
    	$upload->path      = $path;
    	$upload->ownName   = true;
    	$upload->smallHeight    = 135;
    	$upload->smallWidth    	= 0;
    	$upload->middleHeight 	= 380;
    	$upload->middleWidth  	= 0;
    	$upload->largeHeight 	= 420;
    	$upload->largeWidth  	= 0;
    	$upload->maxiHeight 	= 760;
    	$upload->maxiWidth  	= 0;
    	$this->heData = array();
    	
    	$fileName = "";
    	$hiddenImages = $this->_request->getPost("hiddenPhoto");
    	$deleteImages = $this->_request->getPost("deletePhoto");
    	if(!empty($hiddenImages[0]) && !empty($deleteImages[0])){
			$fileName = "";
			@unlink("./".$path."/".$hiddenImages[0]);
			@unlink("./".$path."/mala-".$hiddenImages[0]);
			@unlink("./".$path."/stredni-".$hiddenImages[0]);
			@unlink("./".$path."/velka-".$hiddenImages[0]);
			@unlink("./".$path."/maxi-".$hiddenImages[0]);
			
		}
    	if(!empty($_FILES["photo"]['tmp_name'][0])){
    			
    		if(!empty($hiddenImages[0])){
    			@unlink("./".$path."/".$hiddenImages[0]);
    			@unlink("./".$path."/mala-".$hiddenImages[0]);
    			@unlink("./".$path."/stredni-".$hiddenImages[0]);
    			@unlink("./".$path."/velka-".$hiddenImages[0]);
    			@unlink("./".$path."/maxi-".$hiddenImages[0]);
    		}
    			
    		$upload->fileName    = $upload->niceFile($_FILES["photo"]['name'][0]);
    		$upload->tmpFileName = $_FILES["photo"]['tmp_name'][0];
    		$upload->upload();
    			
    		$fileName = $upload->fileName;
    	}
    	$link->updateData(array(
    		"categoryPhoto"	=> $fileName
    	), "linkID = $linkID");
    } 
        
	private function getData(){
		
		$data = array(
			"titleSupplier"     	=> $this->_request->getPost("titleSupplier"),
		    "shortcut"   			=> $this->_request->getPost("shortcut"),
		    "supplier"   	=> $this->_request->getPost("supplier"),
		    "coverMaterialID"   	=> $this->_request->getPost("coverMaterialID")
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

	    $this->titleSupplier       	= $filter->getUnescaped("titleSupplier");
	    $this->shortcut    			= $filter->getUnescaped("shortcut");
	    $this->supplier	= $filter->getUnescaped("supplier");
	    $this->coverMaterialID		= $filter->getUnescaped("coverMaterialID");
		
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$this->title[$val->suffix] 			= $filter->getUnescaped("title-".$val->suffix);
				$this->text[$val->suffix] 			= $filter->getUnescaped("text-".$val->suffix);	

				if(empty($this->text[$val->suffix])){
					$this->text[$val->suffix] 		= "";
				}
				if(empty($this->title[$val->suffix])){
					$this->title[$val->suffix] 		= "";
				}
		}		
		
	    return $filter;
	}

    private function setUpdateData($cover,$where){

    	$supplier		= new Eshop_Models_Supplier();
    	$supplierCover				= new Eshop_Models_SupplierCover();
    	
	    $allItems				= new stdClass();
		$allDBItems 			= $cover->getOneRow($where);	    
		
		if($this->modulesData->jazykoveMutace){		

			$coverLangDb			= new  Eshop_Models_CoverLang();
			$allTranslates 			= $coverLangDb->getAllItems($where,"lang");
			$allTranslatesArr		= array();
			foreach ($allTranslates as $val){
				$allTranslatesArr[$val->lang] = $val;
			}
			
			//nastavime vsechny jazyky
			//jazyky vzdy prelozime a ulozime do pole
			foreach($this->allLanguageMutations as $val){
				
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->title[$val->suffix] 		= $allTranslatesArr[$val->suffix]->title 		: $allItems->title[$val->suffix] = "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->text[$val->suffix] 		= $allTranslatesArr[$val->suffix]->text 		: $allItems->text[$val->suffix] = "";
			    
			}
		}else{	
				$allSelectedItems 				= $cover->getOneRow($where);
				$allItems->title['cz'] 			= $allSelectedItems->title;
				$allItems->text['cz'] 			= $allSelectedItems->text;
		}
		
		$allDBItems->title 		= $allItems->title;
		$allDBItems->text 		= $allItems->text;
		
	   	$this->view->allItems 				= $this->allItems = $allDBItems;

	   	$allSuppliersArr 	= array();
	   	$allSuppliers 		= $supplierCover->getAllItems($where);
	   	$allSuppliersItems 	= $supplier->getAllItems(null,"priority");
		foreach ($allSuppliers as $val){
			$allSuppliersArr[$val->supplierID] = $val;
		}
		$this->view->allSuppliersArr 	= $allSuppliersArr;
		$this->view->allSuppliers 		= $allSuppliers;
		$this->view->allSuppliersItems 	= $allSuppliersItems;
	}
	private function setFilters(){
		
		$filters = array(
            'titleSupplier'  	=> 'StripTags',
            'shortcut'  		=> 'StripTags',
            'coverMaterialID'  	=> 'StripTags'
        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$validators["title-".$val->suffix] 	= 'StripTags';
		}
		
        return $filters;
		
	}
	
	private function setValidators(){
		
		$validators = array(
      	    
            'titleSupplier' => array(  				
                'allowEmpty' => true
            ),
            'shortcut' => array(  				
                'allowEmpty' => true
            ),
            'supplier' => array(  				
                'allowEmpty' => true
            ),
            'coverMaterialID' => array(  				
                'allowEmpty' => true
            )

        );
	
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$validators["title-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text-".$val->suffix]['allowEmpty'] 		= true;
		}
		
        return $validators;
		
	}
	    
	private function setSublinks($id = 0){
		$this->view->subLinks = array();
		if($id == 0){
			$this->view->subLinks[] = array(
					"title" => 	"Základní údaje",
					"url"	=>	"/admin/eshop/potahy/pridat",
					"isMain"=> 	1
			);
		}else{
			$this->view->subLinks[] = array(
					"title" => 	"Základní údaje",
					"url"	=>	"/admin/eshop/potahy/upravit/id/$id",
					"isMain"=> 	1
			);
		}
		$this->view->subLinks[] = array(
			"title" => 	"Fotky",
				"url"	=>	"/admin/eshop/potahy/fotky/id/$id",
			"isMain"=> 	0
		);
		
	}
	
	
    
    
}

?>