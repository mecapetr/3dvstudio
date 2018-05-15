<?php
class Eshop_ProduktController extends Eshop_Library_WholeEshop
{

	protected $title;
	protected $niceTitle;
	protected $date;
	protected $showProduct;
	protected $showFacebook;
	protected $keywords;
	protected $metaTitle;
	protected $description;
	protected $text;
	protected $deliveryTime;
	protected $showInLanguages;
	protected $linkID;
	protected $price;
	protected $originalPrice;
	protected $priceTextBefore;
	protected $productStatusID;
	protected $dateAuction;
	protected $timeAuction;
	protected $priceAuction;
	protected $minPriceAuction;
	protected $textAuction;
	protected $oldUrl;
	protected $supplierID;
	protected $filterID;     
	protected $productGeneralSizeID;
	
	protected $allLanguageMutations;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();	
    }
    function indexAction()
	{
		$this->_redirect("/admin/eshop/produkt/pridat");		
	}

	function pridatAction()
	{
		$this->setSublinks();
		$this->view->subSelected = "Přidat produkt";
		$product			= new Eshop_Models_Product();
		$supplier	= new Eshop_Models_Supplier();
	    $link 				= new Content_Models_Link();
		$script				= new Library_Scripts();
		
		$this->view->showOnlyMainSublink 	= true;
		$this->view->sublinkTitle			= "Základní údaje";
	  	
	    $language 		 = new Models_Language_Language();
		
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
		
	    $enter = $this->_request->getPost("enter");

	    $this->view->action  = "/admin/eshop/produkt/pridat";
	    if($enter){
	    	
	    	$script = new Library_Scripts();
	    	
	    	$filter = $this->setData();
	            		
            $data = array(			    	        
			    "title"         		=> $this->title['cz'],
				"niceTitle"	   			=> $this->niceTitle['cz'],
			    "supplierID"	=> $this->supplierID,
			    "date"         			=> date("Y-m-d H:i:s",strtotime($this->date)),
			    "text"         	 		=> $this->text['cz'],
			    "deliveryTime"     		=> $this->deliveryTime['cz'],
            	"showProduct"	    	=> $this->showProduct,
            	"showFacebook"	    	=> $this->showFacebook,
            	"dateAdd"	    		=> date("Y-m-d H:i:s"),
            	"userAdd"	    		=> $this->user,
			    "metaTitle"      		=> $this->metaTitle['cz'],
			    "keywords"      		=> $this->keywords['cz'],
			    "description"      		=> $this->description['cz']	,
			    "oldUrl"      			=> $this->oldUrl['cz']	    			
			 );
            
			 $product->insertData($data);
			 $id = $product->lastID;
			    										    	
			$this->insertDisplayLanguages($id);
			
			//priradime produktu veskere odkazy(kategorie) rekurzivne az ke korenu, pod ktere dany odkaz patri
			if(count($this->linkID) > 0){
				$addedLinks = array();
				foreach($this->linkID as $li){
					//priradime clanku veskere odkazy rekurzivne az ke korenu, pod ktere dany odkaz patri
					if(!empty($li)){
						if(in_array($li, $addedLinks))continue;
						$addedLinks[] = $li;
						$this->generateProductCategoryLinks($id,$li,$link);
					}
				}
			}
			
			$allItems = $product->getAllItems(null, array("priority","productID DESC"));
			$script->updatePriority($allItems, $product, "productID");
            
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				//vlozeni dat do slovniku
				$this->updateDictionary('add',"product","productID",$id);
			}
			$this->_redirect("/admin/eshop/produkt/upravit/id/" . $id . "/added/1");
	            	
	        
	    }    	    
	    $this->view->allSuppliers = $supplier->getAllItems();
	    
	    //nastavi všechny kategorie
	    $this->setMenuLinks();
	    //vlozime placeholdery
	    $this->addPlaceholders();								
	    
	}

	function upravitAction()
	{
	
		$product	  	= new Eshop_Models_Product();
		$productLink	= new Eshop_Models_ProductLink();
		$link			= new Content_Models_Link();
		$language 		= new Models_Language_Language();
		$supplier	= new Eshop_Models_Supplier();
		$id       = $this->_request->getParam('id');
		$added    = $this->_request->getParam('added');
		$enter    = $this->_request->getPost("enter");
		$where    = "productID = '$id'";
	
		$this->view->sublinkTitle			= "Základní údaje";
	
		$this->setSublinks($id);
		 
		$this->view->action = "/admin/eshop/produkt/upravit/id/".$id;
		 
		$oldData = $product->getOneRow($where);
	
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
	
	
		if($enter){
	
			$script = new Library_Scripts();
			$filter = $this->setData();
	
			 
			$data = array(	        
			    "title"         		=> $this->title['cz'],
			    "supplierID"	=> $this->supplierID,
				"niceTitle"	   			=> $this->niceTitle['cz'],
			    "date"         			=> date("Y-m-d H:i:s",strtotime($this->date)),
			    "text"         	 		=> $this->text['cz'],
			    "deliveryTime"      	=> $this->deliveryTime['cz'],
            	"showProduct"	    	=> $this->showProduct,
            	"showFacebook"	    	=> $this->showFacebook,
            	"dateEdit"	    		=> date("Y-m-d H:i:s"),
            	"userEdit"	    		=> $this->user,
			    "metaTitle"      		=> $this->metaTitle['cz'],
			    "keywords"      		=> $this->keywords['cz'],
			    "description"      		=> $this->description['cz'],
			    "oldUrl"      			=> $this->oldUrl['cz']		
			);
			
			$product->updateData($data,$where);
			    				
			//priradime clanku veskere odkazy rekurzivne az ke korenu, pod ktere dany odkaz patri
			if(count($this->linkID) > 0){
			
				$productLink->deleteData($where);
				$addedLinks = array();
				foreach($this->linkID as $li){
					if(in_array($li, $addedLinks))continue;
					$addedLinks[] = $li;
					//priradime clanku veskere odkazy rekurzivne az ke korenu, pod ktere dany odkaz patri
					if(!empty($li))$this->generateProductCategoryLinks($id,$li,$link);
				}
			}
			
			$this->insertDisplayLanguages($id);
	
			//pridani do slovniku
			if($this->modulesData->jazykoveMutace){
				$this->updateDictionary('edit',"product","productID",$id);
			}
			$this->view->message = "Produkt úspěšně upraven";
			 
		}
	
		if(!empty($added)){
			$this->view->message = "Produkt úspěšně přidán";
		}
		//nastavime hlavni data
		$this->setUpdateData($product,$where);
		 
		//vybere daný linkID a linkTitle který byl tomuto článku přiřazen
		$linkData  = $productLink->getLastLinkData($id);
		$linkCount = count($linkData);
		$linkTitleList = array();
		if($linkCount > 0){
			 
			foreach($linkData as $l){
		
				$linkTitleList[$l->linkID] = $l->title;
			  
			}
		}
		$this->view->linkCount     = $linkCount;
		$this->view->linkTitleList = $linkTitleList;
		$this->view->linkID        = $linkData;

		$this->view->allSuppliers = $supplier->getAllItems();
		
		//nastavi všechny kategorie
		$this->setMenuLinks();
		//vlozime placeholdery
		$this->addPlaceholders();
	}	
	
	
		
	function seznamAction()
    {  

		$this->view->subSelected  	= "Produkty";
    	$product					= new Eshop_Models_Product();
    	$photo						= new Models_Photo();
    	$photoProduct				= new Eshop_Models_PhotoProduct();
    	$eshopProduct				= new Eshop_Models_EshopProduct();
    	$productDisplayLanguage		= new Eshop_Models_ProductDisplayLanguage();
    	$productCategory			= new Eshop_Models_ProductCategory();
    	$productCategoryCover		= new Eshop_Models_ProductCategoryCover();
    	$productLink				= new Eshop_Models_ProductLink();
    	$productProductStatus		= new Eshop_Models_ProductProductStatus();
    	$productSizeNumber			= new Eshop_Models_ProductSizeNumber();
    	$link						= new Content_Models_Link();
    	
		$this->view->sortableList 	= true;        
		$this->view->action       	= '/admin/eshop/produkt/seznam';
		
		if($this->_request->getPost("deleteButton")){
				
			$toDelete = $this->_request->getPost("delete");
			if(!empty($toDelete)){

		
				foreach($toDelete as $del){
					$product->deleteData("productID = $del");
			 		$this->updateDictionary('delete',"product","productID",$del);

			 		$productDisplayLanguage->deleteData("productID = $del");
			 		$productLink->deleteData("productID = $del");
			 		$productProductStatus->deleteData("productID = $del");
			 		$productSizeNumber->deleteData("productID = $del");
			 		
			 		$allPhotos 	= $photo->getAllPhotos($del,"photo_product","productID");
			 		 
			 		foreach($allPhotos as $value){
			 			 
			 			@unlink("./Public/Images/Product/".$value->title);
			 			@unlink("./Public/Images/Product/mala-".$value->title);
			 			@unlink("./Public/Images/Product/stredni-".$value->title);
			 			@unlink("./Public/Images/Product/velka-".$value->title);
			 			@unlink("./Public/Images/Product/maxi-".$value->title);
			 			$wherePhoto = "photoID = '$value->photoID' ";
			 			$photo->deleteData($wherePhoto);
			 			$photoProduct->deleteData($wherePhoto);
			 			
			 			//vymazeme ze slovniku pokud je zaply modul
			 			if($this->modulesData->jazykoveMutace)
			 				$this->updateDictionary('delete','photo','photoID',$value->photoID);
			 		}
			 		
			 		$allEshopProducts = $eshopProduct->getAllItems("productID = $del");
			 		foreach($allEshopProducts as $val){
			 			$eshopProduct->deleteData("eshopProductID = $val->eshopProductID");
			 			$this->updateDictionary('delete','eshop-product','eshopProductID',$val->eshopProductID);
			 		
			 			if(!empty($val->photo)){
			 				@unlink("./Public/Images/EshopProduct/".$val->photo);
			 				@unlink("./Public/Images/EshopProduct/mala-".$val->photo);
			 				@unlink("./Public/Images/EshopProduct/stredni-".$val->photo);
			 				@unlink("./Public/Images/EshopProduct/velka-".$val->photo);
			 				@unlink("./Public/Images/EshopProduct/maxi-".$val->photo);
			 			}
			 		}
			 		
			 		$allProductCategories	= $productCategory->getAllItems("productID = $del");
					foreach ($allProductCategories as $val){
		    			$productCategory->deleteData("productCategoryID = $val->productCategoryID");
		    			$productCategoryCover->deleteData("productCategoryID = $val->productCategoryID");
		
		    			$this->updateDictionary('delete','product-category','productCategoryID',$val->productCategoryID);
		    		}
			 		
				}

				$this->view->message = "Vybrané produkty byly úspěšně smazány.";
					
			}
		}
			
		$allCategories = $link->getMenu("L.isEshopCategory", "L.priority");

		$this->view->allCategories 	= $allCategories;
		$this->view->allItems 		= $product->getAllItemsWithCategory(null,"P.priority");

	    //vlozime placeholdery
	    $this->addPlaceholders();

    }

    function fotkyAction()
    {
    
    	$product  = new Eshop_Models_Product();
    	$language = new Models_Language_Language();
    	$id       = $this->_request->getParam('id');
    	$enter    = $this->_request->getPost("enter");
    	$delete   = $this->_request->getPost("delete");
    	$upload   = $this->_request->getPost("upload");
    	$where    = "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Fotky";
    
    	$this->setSublinks($id);
    	 
    	$this->view->action = "/admin/eshop/produkt/fotky/id/".$id;
    	 
    	$oldData = $product->getOneRow($where);
    
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
    	 
    	$this->view->productData = $product->getOneRow($where);
    	 
    	$this->setPhotosUpdateData($id);
    	
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }


    function rozmeryAction()
    {

    	$product	  				= new Eshop_Models_Product();
    	$productType	  			= new Eshop_Models_ProductType();
    	$productSizeNumberPosition	= new Eshop_Models_ProductSizeNumberPosition();
    	$productSizeNumber			= new Eshop_Models_ProductSizeNumber();
    	
    	$language 		= new Models_Language_Language();
    	$id       = $this->_request->getParam('id');
    	$enter    = $this->_request->getPost("enter");
    	$where    = "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Rozměry";
    
    	$this->setSublinks($id);
    
    	$this->view->action = "/admin/eshop/produkt/rozmery/id/".$id;
    
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    
    	if($enter){
    
    		$script = new Library_Scripts();    		
    		
    		$filter = $this->setData();
    		
    		$this->updateDictionary('edit',"product-size","productID",$id);
    		
    		
    		$productTypeID 					= $this->_request->getPost("productTypeID");
    		if(empty($productTypeID)){
    			$productTypeID = 0;
    		}
    		$product->updateData(array(
    			"productTypeID" => $productTypeID,     
	    		"productGeneralSizeID"	=> $this->productGeneralSizeID,
    			"textSize" 		=> $this->textSize['cz']
    		), $where);
    		
    		$productSizeNumber->deleteData("productID = $id");
    		if($productTypeID != 0){
    			$allProductSizeNumberPositions 	= $productSizeNumberPosition->getAllItems("productTypeID = $productTypeID");
	    		foreach ($allProductSizeNumberPositions as $val){
	    			$productSizeNumberPositionValue 		= $this->_request->getPost($val->productSizeNumberPositionID . "-productSizeNumberPositionID");
	    			if(!empty($productSizeNumberPositionValue)){
	    				$productSizeNumber->insertData(array(
	    					"productTypeID" 					=> $productTypeID,
	    					"productID" 						=> $id,
	    					"productSizeNumberPositionID" 		=> $val->productSizeNumberPositionID,
	    					"value" 							=> $productSizeNumberPositionValue    						
	    				));
	    			}
	    		}
    		}
    
    		$this->view->message = "Ceny úspěšně upraveny";
    
    	}

    	$allTypes 				= $productType->getAll(null,"priority");
    	$allSizeNuberPositions 	= $productSizeNumberPosition->getAllItems();
    	
    	$allSizeNuberPositionsArr = array();
    	foreach ($allSizeNuberPositions as $val){
    		$allSizeNuberPositionsArr[$val->productTypeID][] = $val;
    	}
    	foreach ($allTypes as $key => $val){
    		if(!empty($allSizeNuberPositionsArr[$val->productTypeID])){
    			$allTypes[$key]->numberPositions = $allSizeNuberPositionsArr[$val->productTypeID];
    		}
    	}
    	
    	$allProductSizes 	= $productSizeNumber->getAllItems("productID = $id");
    	$allProductSizesArr = array();
    	
    	foreach ($allProductSizes as $val) {
    		$allProductSizesArr[$val->productTypeID][$val->productSizeNumberPositionID] = $val->value;
    	}

    	$this->view->allProductSizesArr = $allProductSizesArr;
		$this->view->allTypes = $allTypes;
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);  	
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }
    function cenyAction()
    {
    
    	$product	  	= new Eshop_Models_Product();
    	$language 		= new Models_Language_Language();
    	$id       = $this->_request->getParam('id');
    	$enter    = $this->_request->getPost("enter");
    	$where    = "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Ceny";
    
    	$this->setSublinks($id);
    		
    	$this->view->action = "/admin/eshop/produkt/ceny/id/".$id;
    		
    	$oldData = $product->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    
    	if($enter){
    
    		$script = new Library_Scripts();
    		$filter = $this->setData();
    
    
    		$data = array(
    				"price"         	=> $this->price['cz'],
    				"originalPrice"    	=> $this->originalPrice['cz'],
    				"priceTextBefore"   => $this->priceTextBefore
    		);
    
    		$product->updateData($data,$where);
    		 
    
    		//pridani do slovniku
    		if($this->modulesData->jazykoveMutace){
    			$this->updateDictionary('edit',"product-prices","productID",$id);
    		}
    		$this->view->message = "Ceny úspěšně upraveny";
    
    	}
    	
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);  		
    	
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }

    function statusASlevyAction()
    {
    
    	$product	  			= new Eshop_Models_Product();
    	$language 				= new Models_Language_Language();
    	$productStatus 			= new Eshop_Models_ProductStatus();
    	$productProductStatus 	= new Eshop_Models_ProductProductStatus();
    	$id       		= $this->_request->getParam('id');
    	$enter    		= $this->_request->getPost("enter");
    	$where    		= "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Status a slevy";
    
    	$this->setSublinks($id);
    
    	$this->view->action = "/admin/eshop/produkt/status-a-slevy/id/".$id;
    
    	$oldData = $product->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    
    	if($enter){
    
    		$script = new Library_Scripts();
    		$filter = $this->setData();
    
    
    		$data = array(
    				"discount"         		=> $this->discount['cz'],
    				"deliveryText"    		=> $this->deliveryText['cz'],
    				"discountInPercentage"  => $this->discountInPercentage
    		);
    
    		$product->updateData($data,$where);
    		 
    		$this->insertStatuses($id);
    
    		//pridani do slovniku
    		if($this->modulesData->jazykoveMutace){
    			$this->updateDictionary('edit',"product-discount","productID",$id);
    		}
    		$this->view->message = "Status a slevy úspěšně upraveny";
    
    	}
    	 
    	$this->view->allStatusses = $productStatus->getAllItems();
    	 
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);
    	 
    
    	$allStatussesArr 	= array();
    	$allDisplayProductStatuses 		= $productProductStatus->getAllItems($where);
    	foreach ($allDisplayProductStatuses as $val){
    		$allStatussesArr[$val->productStatusID] = $val;
    	}
    	$this->view->allStatussesArr 	= $allStatussesArr;
    	 
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }
    function filtryAction()
    {    
    	$product	  			= new Eshop_Models_Product();
    	$language 				= new Models_Language_Language();
    	$fil 					= new Eshop_Models_Filter();
    	$productFilter 			= new Eshop_Models_ProductFilter();
    	$id       				= $this->_request->getParam('id');
    	$enter    				= $this->_request->getPost("enter");
    	$where    				= "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Filtry";
    
    	$this->setSublinks($id);
    
    	$this->view->action = "/admin/eshop/produkt/filtry/id/".$id;
    
    	$oldData = $product->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    
    	if($enter){
    
    		$script = new Library_Scripts();
    		$filter = $this->setData();
    		$this->insertFilters($id);
    		$this->view->message = "Status a slevy úspěšně upraveny";
    
    	}
    
    	
    
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);
    
    
    	$this->view->allFil = $fil->getAll(null,"priority");
	    
	    $allFiltersArr = array();
	    $allProductFilters = $productFilter->getAll($where);
	    foreach ($allProductFilters as $val){
	    	$allFiltersArr[$val->filterID] = $val;
	    }
	    $this->view->allFiltersArr = $allFiltersArr;
    
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }
    function heurekaAction()
    {
    
    	$product	  	= new Eshop_Models_Product();
    	$language 		= new Models_Language_Language();
    	$id       		= $this->_request->getParam('id');
    	$enter    		= $this->_request->getPost("enter");
    	$where    		= "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Heuréka";
    
    	$this->setSublinks($id);
    
    	$this->view->action = "/admin/eshop/produkt/heureka/id/".$id;
    
    	$oldData = $product->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    
    	if($enter){
    
    		$script = new Library_Scripts();
    		$filter = $this->setData();
    
    
    		$data = array(
    			"hTitle"    => $this->hTitle['cz'],
    			"hpTitle"   => $this->hpTitle['cz'],
    			"hCat"  	=> $this->hCat['cz'],
    			"hManu"  	=> $this->hManu,
    			"hText"  	=> $this->hText['cz']
    		);
    
    		$product->updateData($data,$where);
    		 
    
    		//pridani do slovniku
    		if($this->modulesData->jazykoveMutace){
    			$this->updateDictionary('edit',"product-heureka","productID",$id);
    		}
    		$this->view->message = "Heuréka úspěšně upravena";
    
    	}
    	 
    	 
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);
    	 
    	 
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }

    function zboziczAction()
    {
    
    	$product	  	= new Eshop_Models_Product();
    	$language 		= new Models_Language_Language();
    	$id       		= $this->_request->getParam('id');
    	$enter    		= $this->_request->getPost("enter");
    	$where    		= "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Zboží.cz";
    
    	$this->setSublinks($id);
    
    	$this->view->action = "/admin/eshop/produkt/zbozicz/id/".$id;
    
    	$oldData = $product->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    
    	if($enter){
    
    		$script = new Library_Scripts();
    		$filter = $this->setData();
    
    
    		$data = array(
    			"zCat"	=> $this->zCat['cz']
    		);
    
    		$product->updateData($data,$where);
    		 
    
    		//pridani do slovniku
    		if($this->modulesData->jazykoveMutace){
    			$this->updateDictionary('edit',"product-zbozi","productID",$id);
    		}
    		$this->view->message = "Zboží.cz úspěšně upraveno";
    
    	}
    
    
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);
    
    
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }

    function kategorieAPotahyAction()
    {
    
    	$product	  			= new Eshop_Models_Product();
    	$cover	  				= new Eshop_Models_Cover();
    	$productCategory		= new Eshop_Models_ProductCategory();
    	$productCategoryCover	= new Eshop_Models_ProductCategoryCover();
    	$language 				= new Models_Language_Language();
    	$id       				= $this->_request->getParam('id');
    	$enter    				= $this->_request->getPost("enter");
    	$delete   				= $this->_request->getPost("delete");
    	$where    				= "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Kategorie a potahy";
    
    	$this->setSublinks($id);
    
    	$this->view->action = "/admin/eshop/produkt/kategorie-a-potahy/id/".$id;
    
    	$oldData = $product->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    	if($delete){
    		$deleteProductCategoryIDArr = $this->_request->getPost("deleteProductCategoryID");
    		foreach ($deleteProductCategoryIDArr as $val){
    			$productCategory->deleteData("productCategoryID = $val");
    			$productCategoryCover->deleteData("productCategoryID = $val");

    			$this->updateDictionary('delete','product-category','productCategoryID',$val);
    		}
    	}
    	if($enter){
    
    		$productCategoryID   	= $this->_request->getParam('productCategoryID');
    		$script 				= new Library_Scripts();
    		$filter 				= $this->setData();
    
    		$data = array(
    				"productID"     => $id,
    				"title"    		=> $this->title['cz'],
    				"percent"  		=> $this->percent,
    				"text"    		=> $this->text['cz']
    		);
    
    		if(empty($productCategoryID)){
    			$productCategory->insertData($data);
    			$productCategoryID = $productCategory->lastID;
    			$this->updateDictionary('add',"product-category","productCategoryID",$productCategoryID);
    			 
    			$this->view->message = "Kategorie úspěšně přidána";
    		}else{
    			$productCategory->updateData($data,"productCategoryID = $productCategoryID");
    			$this->updateDictionary('edit',"product-category","productCategoryID",$productCategoryID);
    			 
    			$this->view->message = "Kategorie úspěšně upravena";
    		}
    
    		$this->insertProductCategories($productCategoryID);
    
    
    		$allItems = $productCategory->getAllItems($where, array("priority","productCategoryID DESC"));
    		$script->updatePriority($allItems, $productCategory, "productCategoryID");
    		 
    
    
    
    	}
    
    
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);
    
    
    	$allCovers 				= $cover->getAllCoversWithSuppliers("S.supplierID = $oldData->supplierID");
    	$this->view->allCovers 	= $allCovers;
    	 
    	$allproductCategories 				= $productCategory->getAll($where,"priority");
    	$this->view->allproductCategories 	= $allproductCategories;
    	 
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }
    function kategorieAPotahyEditAction()
    {

    	Zend_Layout::getMvcInstance()->disableLayout();
    	
    	$product	  			= new Eshop_Models_Product();
    	$cover	  				= new Eshop_Models_Cover();
    	$productCategory		= new Eshop_Models_ProductCategory();
    	$productCategoryCover	= new Eshop_Models_ProductCategoryCover();
    	$language 				= new Models_Language_Language();
    	$id       				= $this->_request->getParam('id');
    	$where    				= "productCategoryID = '$id'";
    	    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();


    	//nastavime hlavni data
    	$allItems = $this->setProductCategoryUpdateData($productCategory,$where);
    	
    	$productData			= $product->getOneRow("productID = $allItems->productID");
    	
    	$allSavedCovers 		= $productCategoryCover->getAllItems($where);
    	$allSavedCoversArr		= array();
    	foreach ($allSavedCovers as $val){
    		$allSavedCoversArr[$val->coverID] = $val;
    	}
		$this->view->allSavedCoversArr = $allSavedCoversArr;
    	
    	$allCovers 				= $cover->getAllCoversWithSuppliers("S.supplierID = $productData->supplierID");
    	$this->view->allCovers 	= $allCovers;
    	
    }
    

    function eshopAction()
    {

    	$product	  			= new Eshop_Models_Product();
    	$eshopProduct	  		= new Eshop_Models_EshopProduct();
    	$eshopProductLang  		= new Eshop_Models_EshopProductLang();
    	$cover	  				= new Eshop_Models_Cover();
    	$coverTitle				= new Eshop_Models_CoverTitle();
    	$productCategory		= new Eshop_Models_ProductCategory();
    	$productCategoryCover	= new Eshop_Models_ProductCategoryCover();    	    	$productType            = new Eshop_Models_ProductType();
    	$language 				= new Models_Language_Language();
    	$id       				= $this->_request->getParam('id');
    	$enter    				= $this->_request->getPost("enter");
    	$delete   				= $this->_request->getPost("delete");
    	$where    				= "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Eshop";
    
    	$this->setSublinks($id);
    
    	$this->view->action = "/admin/eshop/produkt/eshop/id/".$id;
    
    	$oldData = $product->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    	if($delete){
    		$deleteEshopProductIDArr = $this->_request->getPost("deleteEshopProductID");
    		foreach ($deleteEshopProductIDArr as $val){
    			$eshopProduct->deleteData("eshopProductID = $val");
    			$this->updateDictionary('delete','eshop-product','eshopProductID',$val);
    			
    			if(!empty($oldData->photo)){
    				@unlink("./Public/Images/EshopProduct/".$oldData->photo);
    				@unlink("./Public/Images/EshopProduct/mala-".$oldData->photo);
    				@unlink("./Public/Images/EshopProduct/stredni-".$oldData->photo);
    				@unlink("./Public/Images/EshopProduct/velka-".$oldData->photo);
    				@unlink("./Public/Images/EshopProduct/maxi-".$oldData->photo);
    			}

    		}
    		
    	}
    	if($enter){
    
    		$eshopProductID   		= $this->_request->getParam('eshopProductID');
    		$script 				= new Library_Scripts();
    		$filter 				= $this->setData();

    		$chooseSideAllowed = 0;
    		$chooseSideDisabled = 0;
    		if($this->sideID == 3){
    			$chooseSideAllowed = 1;
    			$this->sideID = 0;
    		}else if($this->sideID == 4){
    			$this->sideID = 0;
    			$chooseSideDisabled = 1;
    		}    		$prodType = explode("~",$this->productTypeID);    		
    		$data = array(
    				"productID"     		=> $id,
    				"productCategoryID" 	=> $this->productCategoryID,    				    				"productTypeID" 	    => $prodType[0],    				
    				"title"    				=> $this->title['cz'],
    				"text"    				=> $this->text['cz'],
    				"price" 				=> $this->price['cz'],
    				"store" 				=> $this->store,
    				"sideID" 				=> $this->sideID,
    				"chooseSideAllowed"		=> $chooseSideAllowed,
    				"chooseSideDisabled"	=> $chooseSideDisabled,
    				"predefinedCoversType" 	=> $this->predefinedCoversType,
    				"showFirstCover" 		=> $this->showFirstCover,
    				"showSecondCover" 		=> $this->showSecondCover,
    				"cover1TitleID" 		=> $this->cover1TitleID,
    				"cover2TitleID" 		=> $this->cover2TitleID,
    				"cover1ID" 				=> $this->cover1ID,
    				"cover2ID" 				=> $this->cover2ID,
    				"cover1photoID" 		=> $this->cover1photoID,
    				"cover2photoID" 		=> $this->cover2photoID,
            		"showProduct"	    	=> $this->showProduct
    		);
    		
    
    		if(empty($eshopProductID)){
    			$eshopProduct->insertData($data);
    			$eshopProductID = $eshopProduct->lastID;
    			$this->updateDictionary('add',"eshop-product","eshopProductID",$eshopProductID);
    			 
    			$this->view->message = "Produkt úspěšně přidán";
    		}else{
    			$eshopProduct->updateData($data,"eshopProductID = $eshopProductID");
    			$this->updateDictionary('edit',"eshop-product","eshopProductID",$eshopProductID);
    			 
    			$this->view->message = "Produkt úspěšně upraven";
    		}
    
    		$this->addEshopProductPhoto($eshopProductID);
    
    
    		$allItems = $eshopProduct->getAllItems($where, array("priority","eshopProductID DESC"));
    		$script->updatePriority($allItems, $eshopProduct, "eshopProductID");
    		 
    
    
    
    	}
    
    
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);
    	 
    	 
    	$this->view->allDimensions 	        = $productType->getAll(null,"priority");
    	$this->view->allProductCategories 	= $productCategory->getAll($where,"priority");
    	$this->view->allCoverTitles			= $coverTitle->getAllItems();
    	$this->view->allCovers				= $cover->getAllCoversWithSuppliers("SC.supplierID = $oldData->supplierID","C.priority");
    	$this->view->allEshopProducts 		= $eshopProduct->getAll("EP.productID = $id", "EP.priority","eshopProductID DESC");
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }

    function vyloucitPotahyAction()
    {
    
    	$product	  			= new Eshop_Models_Product();
    	$productExcludedCover	= new Eshop_Models_ProductExcludedCover();
    	$cover					= new Eshop_Models_Cover();
    	$language 				= new Models_Language_Language();
    	$id       				= $this->_request->getParam('id');
    	$enter    				= $this->_request->getPost("enter");
    	$where    				= "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Vyloučit potahy";
    
    	$this->setSublinks($id);
    
    	$this->view->action = "/admin/eshop/produkt/vyloucit-potahy/id/".$id;
    
    	$oldData = $product->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    	if($enter){
    
    		$productExcludedCover->deleteData($where);
    		
    		$coverIDs   		= $this->_request->getPost('coverIDs');
    		foreach ($coverIDs as $val){
	    		$data = array(
	    				"productID" => $id,
	    				"coverID" 	=> $val,
	    				
	    		);
    			
	    		$productExcludedCover->insert($data);
    		}  		 
    
    
    
    	}
    
    
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);
    
    	$allExcludedCovers 		= $productExcludedCover->getAllItems($where);
    	$allExcludedCoversArr 	= array();
    	 
    	foreach ($allExcludedCovers as $val){
    		$allExcludedCoversArr[$val->coverID] = $val;
    	}
    	$allCovers = $cover->getAllCoversWithSuppliers("S.supplierID = $oldData->supplierID");
    	$this->view->allCovers 			= $allCovers;
    	$this->view->allExcludedCovers 	= $allExcludedCoversArr;
    	
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }
    
    function eshopEditAction()
    {
    
    	Zend_Layout::getMvcInstance()->disableLayout();

    	$product	  			= new Eshop_Models_Product();
    	$eshopProduct	  		= new Eshop_Models_EshopProduct();
    	$eshopProductLang  		= new Eshop_Models_EshopProductLang();
    	$cover	  				= new Eshop_Models_Cover();
    	$coverTitle				= new Eshop_Models_CoverTitle();
    	$productCategory		= new Eshop_Models_ProductCategory();
    	$productCategoryCover	= new Eshop_Models_ProductCategoryCover();    	    	$productType            = new Eshop_Models_ProductType();
    	$language 				= new Models_Language_Language();
    	$id       				= $this->_request->getParam('id');
    	$where    				= "eshopProductID = '$id'";
    		
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    
    	//nastavime hlavni data
    	$allItems = $this->setEshopProductUpdateData($eshopProduct,$where);

    	$eshopProductData 					= $eshopProduct->getOneRow($where);
    	$productData						= $product->getOneRow("productID = $eshopProductData->productID");
    	$this->view->allDimensions 	        = $productType->getAll(null,"priority");
    	$this->view->allProductCategories 	= $productCategory->getAll("productID = $eshopProductData->productID","priority");
    	$this->view->allCoverTitles			= $coverTitle->getAllItems();
    	$this->view->allCovers				= $cover->getAllCoversWithSuppliers("SC.supplierID = $productData->supplierID","C.priority");
    	 
    }
    function aukceAction()
    {
    
    	$product	  	= new Eshop_Models_Product();
    	$language 		= new Models_Language_Language();
    	$id       		= $this->_request->getParam('id');
    	$enter    		= $this->_request->getPost("enter");
    	$where    		= "productID = '$id'";
    
    	$this->view->sublinkTitle			= "Aukce";
    
    	$this->setSublinks($id);
    
    	$this->view->action = "/admin/eshop/produkt/aukce/id/".$id;
    
    	$oldData = $product->getOneRow($where);
    
    	//vybereme vsechny jazykove mutace
    	$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    
    	if($enter){
    
    		$script = new Library_Scripts();
    		$filter = $this->setData();
    
    
    		$data = array(
    				"textAuction"         	=> $this->textAuction['cz'],
    				"dateAuction"    		=> $this->dateAuction,
    				"minPriceAuction"  		=> $this->minPriceAuction,
    				"priceAuction"  		=> $this->priceAuction
    		);
    
    		$product->updateData($data,$where);
    		 
    
    		//pridani do slovniku
    		if($this->modulesData->jazykoveMutace){
    			$this->updateDictionary('edit',"product-auction","productID",$id);
    		}
    		$this->view->message = "Aukce úspěšně upravena";
    
    	}
    
    
    	//nastavime hlavni data
    	$this->setUpdateData($product,$where);
    
    
    	//vlozime placeholdery
    	$this->addPlaceholders();
    }

    private function addEshopProductPhoto($eshopProductID){
    	$eshopProduct	   = new Eshop_Models_EshopProduct();
    	$upload            = new Library_UploadFiles();
    	$path              = "Public/Images/EshopProduct";
    	$upload->path      = $path;
    	$upload->ownName   = true;
    	
    	$upload->smallHeight    	= 135;
    	$upload->smallWidth     	= 0;
    	$upload->middleHeight 	= 380;
    	$upload->middleWidth  	= 0;
    	$upload->largeHeight 	= 420;
    	$upload->largeWidth  	= 0;
    	$upload->maxiHeight 	= 760;
    	$upload->maxiWidth  	= 0;
    	
    	$this->heData = array();
    	 
    	$fileName = null;
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
    	if(!is_null($fileName)){
    	$eshopProduct->updateData(array(
    			"photo"	=> $fileName
    	), "eshopProductID = $eshopProductID");
    	}
    }
    
    protected function addNewPhotos($id){
    	$files = $_FILES["photos"];

    	$photo          = new Models_Photo();
    	$photoProduct 	= new Eshop_Models_PhotoProduct();
    	
    	
    	if(count($files["name"]) > 0){
    	
    		$uploadPhoto 				= new Library_UploadFiles();
    		$uploadPhoto->path         	= "Public/Images/Product";
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
    					$photoProduct->insertData(array(
    							"photoID"       => $photoID,
    							"productID"       => $id
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
    	$photoProduct 	= new Eshop_Models_PhotoProduct();
    	//upravime description u fotek, ktere jiz byly pridany
    	$allUploadedPhotos = $photo->getAllPhotos($id,"photo_product","productID");
    	if(count($allUploadedPhotos)>0){
    		$mainPhoto 	= $this->_request->getPost("mainPhoto");
    		foreach($allUploadedPhotos as $item){    
    			
    				$photoDesc 	= $this->_request->getPost("$item->photoID-photoDesc-cz");
	    			$photoDesc2 = $this->_request->getPost("$item->photoID-photoDesc2-cz");
    				
    				$isMainPhoto = 0;
    				if($mainPhoto == $item->photoID){
    					$isMainPhoto = 1;
    				}
    				$photo->updateData(array("description" => $photoDesc,"description2" => $photoDesc2,"mainPhoto" => $isMainPhoto),"photoID = '$item->photoID'");	// aktualizujeme description u fotky
    				 
    				//vybereme popis fotky a vlozime do slovnikovych souboru jednotlive preklady
    				$photoData 				= new stdClass();
    				$photoData->inputName 	= "$item->photoID-photoDesc";
	    			$photoData->inputName2 	= "$item->photoID-photoDesc2";
    				$photoData->photoID 	= $item->photoID;
    				$this->updateDictionary('edit','photo','photoID',$item->photoID,$photoData);
    			
    
    		}
    	}
    }
    protected function deleteOldPhotos($id){
    
    	$photo          = new Models_Photo();
    	$photoProduct 	= new Eshop_Models_PhotoProduct();
    	//upravime description u fotek, ktere jiz byly pridany
    	$allUploadedPhotos = $photo->getAllPhotos($id,"photo_product","productID");
    	$photoSelected = false;
    	if(count($allUploadedPhotos)>0){
    		foreach($allUploadedPhotos as $item){
    
    			$delete 		= $this->_request->getPost("$item->photoID-deletePhoto");
    			if(!empty($delete)){
    				$photoSelected = true;
    				$photo->deleteData("photoID = $item->photoID");
    				$photoProduct->deleteData("photoID = $item->photoID");
    				$this->updateDictionary('delete','photo','photoID',$item->photoID);
    				@unlink("./Public/Images/Product/$item->file");
    				@unlink("./Public/Images/Product/mala-$item->file");
    				@unlink("./Public/Images/Product/stredni-$item->file");
    				@unlink("./Public/Images/Product/velka-$item->file");
    				@unlink("./Public/Images/Product/maxi-$item->file");
    					
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
	    $allDBItems 	= $photo->getAllPhotos($id,"photo_product","productID");
	    
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
		    	(isset($allTranslatesArr[$val->suffix])) ?  $allItems->description2[$val->suffix] = $allTranslatesArr[$val->suffix]->description2 : $allItems->description2[$val->suffix] = "";
		    	 
		    }

		    $value->description = $allItems->description;
	    	$value->description2 = $allItems->description2;
			
		}				
		$this->view->allPhotos 		= $this->allPhotos 		= $allDBItems ;	
    }

    private function insertDisplayLanguages($productID){
    	$productDisplayL = new Eshop_Models_ProductDisplayLanguage();
    	$productDisplayL->deleteData("productID = $productID");
    	if(!empty($this->showInLanguages)){
	    	foreach ($this->showInLanguages as $val){
	    		$productDisplayL->insertData(array(
	    				"productID" 	=> $productID,
	    				"languageID"	=> $val
	    		));
	    	
	    	}
    	}
    }
    private function insertProductCategories($productCategoryID){
    	$productCategoryCover = new Eshop_Models_ProductCategoryCover();
    	$productCategoryCover->deleteData("productCategoryID = $productCategoryID");
    	if(!empty($this->coverID)){
	    	foreach ($this->coverID as $val){
	    		$productCategoryCover->insertData(array(
	    				"productCategoryID" => $productCategoryID,
	    				"coverID"			=> $val
	    		));
	    
	    	}
    	}
    }
    private function insertStatuses($productID){
    	$productPS = new Eshop_Models_ProductProductStatus();
    	$productPS->deleteData("productID = $productID");
    	if(!empty($this->productStatusID)){
	    	foreach ($this->productStatusID as $val){

	    		$color 		= $this->_request->getPost($val . '-statusColor');
	    		$priority	= $this->_request->getPost($val . '-statusPriority');
	    		if(empty($color)){
	    			$color = "";
	    		}
	    		if(empty($priority)){
	    			$priority = 0;
	    		}
	    		
	    		$productPS->insertData(array(
	    				"productID" 		=> $productID,
	    				"productStatusID"	=> $val,
	    				"color"				=> $color,
	    				"priority"			=> $priority
	    		));
	    	
	    	}
    	}
    }

    private function insertFilters($productID){
    	$productFilter = new Eshop_Models_ProductFilter();
    	$productFilter->deleteData("productID = $productID");
    	if(!empty($this->filterID)){
    		foreach ($this->filterID as $val){
    			$productFilter->insertData(array(
    					"productID" => $productID,
    					"filterID"	 => $val
    			));
    
    		}
    	}
    }
	private function getData(){
		
		$data = array(
			"date"     				=> $this->_request->getPost("date"),
		    "showProduct"   		=> $this->_request->getPost("showProduct"),
		    "showFacebook"   		=> $this->_request->getPost("showFacebook"),
		    "showInLanguages"   	=> $this->_request->getPost("showInLanguages"),
		    "linkID"   				=> $this->_request->getPost("linkID"),
		    "priceTextBefore"   	=> $this->_request->getPost("priceTextBefore"),
		    "discountInPercentage"  => $this->_request->getPost("discountInPercentage"),
		    "productStatusID"  		=> $this->_request->getPost("productStatusID"),
		    "hManu"  				=> $this->_request->getPost("hManu"),
		    "dateAuction"  			=> $this->_request->getPost("dateAuction"),
		    "timeAuction"  			=> $this->_request->getPost("timeAuction"),
		    "priceAuction"  		=> $this->_request->getPost("priceAuction"),
		    "minPriceAuction"  		=> $this->_request->getPost("minPriceAuction"),
		    "supplierID"  			=> $this->_request->getPost("supplierID"),
		    "percent"  				=> $this->_request->getPost("percent"),
			"coverID"  				=> $this->_request->getPost("coverID"),
			"productCategoryID"  	=> $this->_request->getPost("productCategoryID"),					    "productTypeID"  	    => $this->_request->getPost("productTypeID"),
			"store"  				=> $this->_request->getPost("store"),
			"sideID"  				=> $this->_request->getPost("sideID"),
			"predefinedCoversType"  => $this->_request->getPost("predefinedCoversType"),
			"showFirstCover"  		=> $this->_request->getPost("showFirstCover"),
			"showSecondCover"  		=> $this->_request->getPost("showSecondCover"),
			"cover1TitleID"  		=> $this->_request->getPost("cover1TitleID"),
			"cover2TitleID"  		=> $this->_request->getPost("cover2TitleID"),
			"cover1ID"  			=> $this->_request->getPost("cover1ID"),
			"cover2ID"  			=> $this->_request->getPost("cover2ID"),
			"cover1photoID"  		=> $this->_request->getPost("cover1photoID"),
			"cover2photoID"  		=> $this->_request->getPost("cover2photoID"),
			"filterID"  			=> $this->_request->getPost("filterID")	   	,
			"productGeneralSizeID"  => $this->_request->getPost("productGeneralSizeID")	
        );
		
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$data["title-".$val->suffix] 		= $this->_request->getPost("title-".$val->suffix);
				$data["text-".$val->suffix] 		= $this->_request->getPost("text-".$val->suffix);
				$data["deliveryTime-".$val->suffix] = $this->_request->getPost("deliveryTime-".$val->suffix);
				$data["metaTitle-".$val->suffix] 	= $this->_request->getPost("metaTitle-".$val->suffix);
				$data["keywords-".$val->suffix] 	= $this->_request->getPost("keywords-".$val->suffix);
				$data["description-".$val->suffix] 	= $this->_request->getPost("description-".$val->suffix);
				$data["price-".$val->suffix] 		= $this->_request->getPost("price-".$val->suffix);
				$data["originalPrice-".$val->suffix]= $this->_request->getPost("originalPrice-".$val->suffix);
				$data["discount-".$val->suffix]		= $this->_request->getPost("discount-".$val->suffix);
				$data["deliveryText-".$val->suffix]	= $this->_request->getPost("deliveryText-".$val->suffix);
				$data["hTitle-".$val->suffix]		= $this->_request->getPost("hTitle-".$val->suffix);
				$data["hpTitle-".$val->suffix]		= $this->_request->getPost("hpTitle-".$val->suffix);
				$data["hCat-".$val->suffix]			= $this->_request->getPost("hCat-".$val->suffix);
				$data["hText-".$val->suffix]		= $this->_request->getPost("hText-".$val->suffix);
				$data["zCat-".$val->suffix]			= $this->_request->getPost("zCat-".$val->suffix);
				$data["textAuction-".$val->suffix]	= $this->_request->getPost("textAuction-".$val->suffix);
				$data["oldUrl-".$val->suffix]		= $this->_request->getPost("oldUrl-".$val->suffix);
				$data["textSize-".$val->suffix]		= $this->_request->getPost("textSize-".$val->suffix);
				
		}    
		
        return $data;

	}
	
	private function setData(){
		
		$filters    = $this->setFilters();
	    $validators = $this->setValidators();
	    $data       = $this->getData();
	    $script		= new Library_Scripts();
	    $filter = new Zend_Filter_Input($filters, $validators, $data);

	    $this->date       			= $filter->getUnescaped("date");
	    $this->showProduct    		= $filter->getUnescaped("showProduct");
	    $this->showFacebook			= $filter->getUnescaped("showFacebook");
	    $this->showInLanguages		= $filter->getUnescaped("showInLanguages");
	    $this->linkID				= $filter->getUnescaped("linkID");
	    $this->priceTextBefore		= $filter->getUnescaped("priceTextBefore");
	    $this->discountInPercentage	= $filter->getUnescaped("discountInPercentage");
	    $this->productStatusID		= $filter->getUnescaped("productStatusID");
	    $this->hManu				= $filter->getUnescaped("hManu");
	    $this->dateAuction			= $filter->getUnescaped("dateAuction");
	    $this->timeAuction			= $filter->getUnescaped("timeAuction");
	    $this->priceAuction			= $filter->getUnescaped("priceAuction");
	    $this->minPriceAuction		= $filter->getUnescaped("minPriceAuction");
	    $this->supplierID			= $filter->getUnescaped("supplierID");
	    $this->percent				= $filter->getUnescaped("percent");
	    $this->coverID				= $filter->getUnescaped("coverID");
	    $this->productCategoryID	= $filter->getUnescaped("productCategoryID");	    	    $this->productTypeID	    = $filter->getUnescaped("productTypeID");
	    $this->store				= $filter->getUnescaped("store");
	    $this->sideID				= $filter->getUnescaped("sideID");
	    $this->predefinedCoversType	= $filter->getUnescaped("predefinedCoversType");
	    $this->showFirstCover		= $filter->getUnescaped("showFirstCover");
	    $this->showSecondCover		= $filter->getUnescaped("showSecondCover");
	    $this->cover1TitleID		= $filter->getUnescaped("cover1TitleID");
	    $this->cover2TitleID		= $filter->getUnescaped("cover2TitleID");
	    $this->cover1ID				= $filter->getUnescaped("cover1ID");
	    $this->cover2ID				= $filter->getUnescaped("cover2ID");
	    $this->cover1photoID		= $filter->getUnescaped("cover1photoID");
	    $this->cover2photoID		= $filter->getUnescaped("cover2photoID");
	    $this->filterID				= $filter->getUnescaped("filterID");    
	    $this->productGeneralSizeID = $filter->getUnescaped("productGeneralSizeID"); 
	    
	    if(empty($this->dateAuction) || empty($this->timeAuction)){
	    	$this->dateAuction 		= null;
	    }else{
	    	$this->dateAuction 		= date("Y-m-d",strtotime($this->dateAuction)) . " " . $this->timeAuction;
	    }
		if(empty($this->percent)){
			$this->percent 		= 0.00;
		}

		if(empty($this->showFirstCover)){
			$this->showFirstCover 		= 0;
		}
		if(empty($this->showSecondCover)){
			$this->showSecondCover 		= 0;
		}

		if(empty($this->cover1TitleID)){
			$this->cover1TitleID 		= 0;
		}
		if(empty($this->cover2TitleID)){
			$this->cover2TitleID 		= 0;
		}

		if(empty($this->cover1ID)){
			$this->cover1ID 		= 0;
		}
		if(empty($this->cover2ID)){
			$this->cover2ID 		= 0;
		}

		if(empty($this->cover1photoID)){
			$this->cover1photoID 		= 0;
		}
		if(empty($this->cover2photoID)){
			$this->cover2photoID 		= 0;
		}
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$this->title[$val->suffix] 			= $filter->getUnescaped("title-".$val->suffix);
				$this->text[$val->suffix] 			= $filter->getUnescaped("text-".$val->suffix);	
				$this->deliveryTime[$val->suffix] 	= $filter->getUnescaped("deliveryTime-".$val->suffix);	
				$this->metaTitle[$val->suffix] 		= $filter->getUnescaped("metaTitle-".$val->suffix);	
				$this->keywords[$val->suffix] 		= $filter->getUnescaped("keywords-".$val->suffix);	
				$this->description[$val->suffix] 	= $filter->getUnescaped("description-".$val->suffix);	
				$this->price[$val->suffix] 			= $filter->getUnescaped("price-".$val->suffix);	
				$this->originalPrice[$val->suffix] 	= $filter->getUnescaped("originalPrice-".$val->suffix);	
				$this->discount[$val->suffix] 		= $filter->getUnescaped("discount-".$val->suffix);	
				$this->deliveryText[$val->suffix] 	= $filter->getUnescaped("deliveryText-".$val->suffix);
				$this->hTitle[$val->suffix] 		= $filter->getUnescaped("hTitle-".$val->suffix);
				$this->hpTitle[$val->suffix] 		= $filter->getUnescaped("hpTitle-".$val->suffix);
				$this->hCat[$val->suffix] 			= $filter->getUnescaped("hCat-".$val->suffix);
				$this->hText[$val->suffix] 			= $filter->getUnescaped("hText-".$val->suffix);	
				$this->zCat[$val->suffix] 			= $filter->getUnescaped("zCat-".$val->suffix);	
				$this->textAuction[$val->suffix] 	= $filter->getUnescaped("textAuction-".$val->suffix);	
				$this->oldUrl[$val->suffix] 		= $filter->getUnescaped("oldUrl-".$val->suffix);	
				$this->textSize[$val->suffix] 		= $filter->getUnescaped("textSize-".$val->suffix);	

				if(empty($this->text[$val->suffix])){
					$this->text[$val->suffix] 		= "";
				}
				if(empty($this->title[$val->suffix])){
					$this->title[$val->suffix] 		= "";
				}
				if(empty($this->deliveryTime[$val->suffix])){
					$this->deliveryTime[$val->suffix] 		= "";
				}
				if(empty($this->metaTitle[$val->suffix])){
					$this->metaTitle[$val->suffix] 		= "";
				}
				if(empty($this->keywords[$val->suffix])){
					$this->keywords[$val->suffix] 		= "";
				}
				if(empty($this->description[$val->suffix])){
					$this->description[$val->suffix] 		= "";
				}
				if(empty($this->price[$val->suffix])){
					$this->price[$val->suffix] 		= 0.00;
				}
				if(empty($this->originalPrice[$val->suffix])){
					$this->originalPrice[$val->suffix] 		= 0.00;
				}
				if(empty($this->discount[$val->suffix])){
					$this->discount[$val->suffix] 		= 0.00;
				}
				if(empty($this->deliveryText[$val->suffix])){
					$this->deliveryText[$val->suffix] 		= "";
				}
				if(empty($this->discountInPercentage)){
					$this->discountInPercentage 		= 0;
				}
				if(empty($this->hTitle)){
					$this->hTitle[$val->hTitle] 	= "";
				}
				if(empty($this->hpTitle)){
					$this->hpTitle[$val->hTitle] 		= "";
				}
				if(empty($this->hCat)){
					$this->hCat[$val->hTitle] 		= "";
				}
				if(empty($this->hText)){
					$this->hText[$val->hTitle] 		= "";
				}
				if(empty($this->zCat)){
					$this->zCat[$val->hTitle] 		= "";
				}
				if(empty($this->textAuction)){
					$this->textAuction[$val->hTitle] 		= "";
				}
				if(empty($this->oldUrl)){
					$this->oldUrl[$val->hTitle] 		= "";
				}
				if(empty($this->textSize)){
					$this->textSize[$val->hTitle] 		= "";
				}

				
				if($val->generateNiceTitle){
					$this->niceTitle[$val->suffix] 		= $script->url($this->title[$val->suffix]);
				}else{
					$this->niceTitle[$val->suffix] 		= "";
				}
		}		
		
	    return $filter;
	}
    private function setUpdateData($product,$where){

    	$productLink			= new Eshop_Models_ProductLink();
	    $allItems				= new stdClass();
		$allDBItems 			= $product->getRow($where);	    
		
		if($this->modulesData->jazykoveMutace){		

			$productLangDb			= new  Eshop_Models_ProductLang();
			$productDisplayL		= new  Eshop_Models_ProductDisplayLanguage();
			$allTranslates 			= $productLangDb->getAllItems($where,"lang");
			$allTranslatesArr		= array();
			foreach ($allTranslates as $val){
				$allTranslatesArr[$val->lang] = $val;
			}
			
			//nastavime vsechny jazyky
			//jazyky vzdy prelozime a ulozime do pole
			foreach($this->allLanguageMutations as $val){
				
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->title[$val->suffix] 			= $allTranslatesArr[$val->suffix]->title 			: $allItems->title[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->text[$val->suffix] 			= $allTranslatesArr[$val->suffix]->text 			: $allItems->text[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->deliveryTime[$val->suffix] 	= $allTranslatesArr[$val->suffix]->deliveryTime 	: $allItems->deliveryTime[$val->suffix] 	= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->metaTitle[$val->suffix] 		= $allTranslatesArr[$val->suffix]->metaTitle 		: $allItems->metaTitle[$val->suffix] 		= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->keywords[$val->suffix] 		= $allTranslatesArr[$val->suffix]->keywords 		: $allItems->keywords[$val->suffix] 		= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->description[$val->suffix] 	= $allTranslatesArr[$val->suffix]->description 		: $allItems->description[$val->suffix] 		= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->price[$val->suffix] 			= $allTranslatesArr[$val->suffix]->price 			: $allItems->price[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->originalPrice[$val->suffix] 	= $allTranslatesArr[$val->suffix]->originalPrice 	: $allItems->originalPrice[$val->suffix] 	= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->discount[$val->suffix] 		= $allTranslatesArr[$val->suffix]->discount 		: $allItems->discount[$val->suffix] 		= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->deliveryText[$val->suffix] 	= $allTranslatesArr[$val->suffix]->deliveryText 	: $allItems->deliveryText[$val->suffix] 	= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->hTitle[$val->suffix] 		= $allTranslatesArr[$val->suffix]->hTitle 			: $allItems->hTitle[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->hpTitle[$val->suffix] 		= $allTranslatesArr[$val->suffix]->hpTitle 			: $allItems->hpTitle[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->hCat[$val->suffix] 			= $allTranslatesArr[$val->suffix]->hCat 			: $allItems->hCat[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->hText[$val->suffix] 			= $allTranslatesArr[$val->suffix]->hText 			: $allItems->hText[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->zCat[$val->suffix] 			= $allTranslatesArr[$val->suffix]->zCat 			: $allItems->zCat[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->textAuction[$val->suffix] 	= $allTranslatesArr[$val->suffix]->textAuction 		: $allItems->textAuction[$val->suffix] 		= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->oldUrl[$val->suffix] 		= $allTranslatesArr[$val->suffix]->oldUrl 			: $allItems->oldUrl[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->textSize[$val->suffix] 		= $allTranslatesArr[$val->suffix]->textSize 		: $allItems->textSize[$val->suffix] 		= "";
			     
			}
		}else{	
				$allSelectedItems 				= $product->getOneRow($where);
				$allItems->title['cz'] 			= $allSelectedItems->title;
				$allItems->text['cz'] 			= $allSelectedItems->text;
				$allItems->deliveryTime['cz'] 	= $allSelectedItems->deliveryTime;
				$allItems->metaTitle['cz'] 		= $allSelectedItems->metaTitle;
				$allItems->keywords['cz'] 		= $allSelectedItems->keywords;
				$allItems->description['cz'] 	= $allSelectedItems->description;
				$allItems->price['cz'] 			= $allSelectedItems->price;
				$allItems->originalPrice['cz'] 	= $allSelectedItems->originalPrice;
				$allItems->discount['cz'] 		= $allSelectedItems->discount;
				$allItems->deliveryText['cz'] 	= $allSelectedItems->deliveryText;
				$allItems->hTitle['cz'] 		= $allSelectedItems->hTitle;
				$allItems->hpTitle['cz'] 		= $allSelectedItems->hpTitle;
				$allItems->hCat['cz'] 			= $allSelectedItems->hCat;
				$allItems->hText['cz'] 			= $allSelectedItems->hText;
				$allItems->zCat['cz'] 			= $allSelectedItems->zCat;
				$allItems->textAuction['cz'] 	= $allSelectedItems->textAuction;
				$allItems->oldUrl['cz'] 		= $allSelectedItems->oldUrl;
				$allItems->textSize['cz'] 		= $allSelectedItems->textSize;
		}
		
		$allDBItems->title 			= $allItems->title;
		$allDBItems->text 			= $allItems->text;
		$allDBItems->deliveryTime 	= $allItems->deliveryTime;
		$allDBItems->metaTitle 		= $allItems->metaTitle;
		$allDBItems->keywords 		= $allItems->keywords;
		$allDBItems->description 	= $allItems->description;
		$allDBItems->price 			= $allItems->price;
		$allDBItems->originalPrice 	= $allItems->originalPrice;
		$allDBItems->discount 		= $allItems->discount;
		$allDBItems->deliveryText 	= $allItems->deliveryText;
		$allDBItems->hTitle 		= $allItems->hTitle;
		$allDBItems->hpTitle 		= $allItems->hpTitle;
		$allDBItems->hCat 			= $allItems->hCat;
		$allDBItems->hText 			= $allItems->hText;
		$allDBItems->zCat 			= $allItems->zCat;
		$allDBItems->textAuction 	= $allItems->textAuction;
		$allDBItems->oldUrl 		= $allItems->oldUrl;
		$allDBItems->textSize 		= $allItems->textSize;
		
		if(!empty($allDBItems->dateAuction)){
			$splittedAuctionDate 		= explode(" ", $allDBItems->dateAuction);
			$allDBItems->dateAuction 	= date("j.n.Y",strtotime($splittedAuctionDate[0]));
			$allDBItems->timeAuction 	= $splittedAuctionDate[1];
		}else{
			$allDBItems->dateAuction 	= "";
			$allDBItems->timeAuction 	= "";
		}
		
	   	$this->view->allItems 		= $this->allItems = $allDBItems;
	   	
	   	
	   	
	   	$allDisplayLanguagesArr 	= array();
	   	$allDisplayLanguages 		= $productDisplayL->getAllItems($where);
	   	foreach ($allDisplayLanguages as $val){
	   		$allDisplayLanguagesArr[$val->languageID] = $val;
	   	}
	   	$this->view->allDisplayLanguagesArr 	= $allDisplayLanguagesArr;

	   	
	   	
	   	
	}
    private function setProductCategoryUpdateData($productCategory,$where){
	    $allItems				= new stdClass();
		$allDBItems 			= $productCategory->getOneRow($where);	    
		
		if($this->modulesData->jazykoveMutace){		

			$productCategoryLangDb	= new  Eshop_Models_ProductCategoryLang();
			$allTranslates 			= $productCategoryLangDb->getAllItems($where,"lang");
			$allTranslatesArr		= array();
			foreach ($allTranslates as $val){
				$allTranslatesArr[$val->lang] = $val;
			}
			
			//nastavime vsechny jazyky
			//jazyky vzdy prelozime a ulozime do pole
			foreach($this->allLanguageMutations as $val){
				
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->title[$val->suffix] 			= $allTranslatesArr[$val->suffix]->title 			: $allItems->title[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->text[$val->suffix] 			= $allTranslatesArr[$val->suffix]->text 			: $allItems->text[$val->suffix] 			= "";
			     
			}
		}else{	
				$allSelectedItems 				= $productCategory->getOneRow($where);
				$allItems->title['cz'] 			= $allSelectedItems->title;
				$allItems->text['cz'] 			= $allSelectedItems->text;
				
		}
		
		$allDBItems->title 			= $allItems->title;
		$allDBItems->text 			= $allItems->text;		
	   	$this->view->allItems 		= $this->allItems = $allDBItems; 	
	   	return $allDBItems;
	}
    private function setEshopProductUpdateData($eshopProduct,$where){
	    $allItems				= new stdClass();
		$allDBItems 			= $eshopProduct->getRow($where);	    
		
		if($this->modulesData->jazykoveMutace){		

			$eshopProductLangDb	= new  Eshop_Models_EshopProductLang();
			$allTranslates 			= $eshopProductLangDb->getAllItems($where,"lang");
			$allTranslatesArr		= array();
			foreach ($allTranslates as $val){
				$allTranslatesArr[$val->lang] = $val;
			}
			
			//nastavime vsechny jazyky
			//jazyky vzdy prelozime a ulozime do pole
			foreach($this->allLanguageMutations as $val){
				
				(isset($allTranslatesArr[$val->suffix])) ?  $allItems->title[$val->suffix] 			= $allTranslatesArr[$val->suffix]->title 			: $allItems->title[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->text[$val->suffix] 			= $allTranslatesArr[$val->suffix]->text 			: $allItems->text[$val->suffix] 			= "";
			    (isset($allTranslatesArr[$val->suffix])) ?  $allItems->price[$val->suffix] 			= $allTranslatesArr[$val->suffix]->price 			: $allItems->price[$val->suffix] 			= "";
			     
			}
		}else{	
				$allSelectedItems 				= $eshopProduct->getOneRow($where);
				$allItems->title['cz'] 			= $allSelectedItems->title;
				$allItems->text['cz'] 			= $allSelectedItems->text;
				$allItems->price['cz'] 			= $allSelectedItems->price;				
		}
		
		$allDBItems->title 			= $allItems->title;
		$allDBItems->text 			= $allItems->text;	
		$allDBItems->price 			= $allItems->price;		
	   	$this->view->allItems 		= $this->allItems = $allDBItems; 
	   	return $allDBItems;
	}
	
	
	private function setFilters(){
		
		$filters = array(
            'date'  				=> 'StripTags',
            'showProduct'  			=> 'StripTags',
            'showFacebook'  		=> 'StripTags',
            'showInLanguages'  		=> 'StripTags',
            'priceTextBefore'  		=> 'StripTags',
            'discountInPercentage'  => 'StripTags',
            'hManu'  				=> 'StripTags',
            'dateAuction'  			=> 'StripTags',
            'timeAuction'  			=> 'StripTags',
            'priceAuction'  		=> 'StripTags',
            'minPriceAuction'  		=> 'StripTags',
            'supplierID'  			=> 'StripTags',
            'percent'	  			=> 'StripTags'
        );
		
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$validators["title-".$val->suffix] 			= 'StripTags';
			$validators["metaTitle-".$val->suffix] 		= 'StripTags';
			$validators["keywords-".$val->suffix] 		= 'StripTags';
			$validators["description-".$val->suffix] 	= 'StripTags';
			$validators["price-".$val->suffix] 			= 'StripTags';
			$validators["originalPrice-".$val->suffix] 	= 'StripTags';
			$validators["discount-".$val->suffix] 		= 'StripTags';
			$validators["deliveryText-".$val->suffix] 	= 'StripTags';
			$validators["hTitle-".$val->suffix] 		= 'StripTags';
			$validators["hpTitle-".$val->suffix] 		= 'StripTags';
			$validators["hCat-".$val->suffix] 			= 'StripTags';
			$validators["hText-".$val->suffix] 			= 'StripTags';
			$validators["zCat-".$val->suffix] 			= 'StripTags';
			$validators["textAuction-".$val->suffix] 	= 'StripTags';
			$validators["oldUrl-".$val->suffix] 		= 'StripTags';
		}
		
        return $filters;
		
	}
	
	private function setValidators(){
	
			
		$validators = array(
      	    
            'date' => array(  				
                'allowEmpty' => true
            ),
            'showProduct' => array(  				
                'allowEmpty' => true
            ),
            'showFacebook' => array(  				
                'allowEmpty' => true
            ),
            'showInLanguages' => array(  				
                'allowEmpty' => true
            ),
            'linkID' => array(  				
                'allowEmpty' => true
            ),
            'priceTextBefore' => array(  				
                'allowEmpty' => true
            ),
            'discountInPercentage' => array(  				
                'allowEmpty' => true
            ),
            'productStatusID' => array(  				
                'allowEmpty' => true
            ),
            'hManu' => array(  				
                'allowEmpty' => true
            ),
            'dateAuction' => array(  				
                'allowEmpty' => true
            ),
            'timeAuction' => array(  				
                'allowEmpty' => true
            ),
            'priceAuction' => array(  				
                'allowEmpty' => true
            ),
            'minPriceAuction' => array(  				
                'allowEmpty' => true
            ),
            'supplierID' => array(  				
                'allowEmpty' => true
            ),
            'percent' => array(  				
                'allowEmpty' => true
            ),
            'coverID' => array(  				
                'allowEmpty' => true
            ),
            'productCategoryID' => array(  				
                'allowEmpty' => true
            ),            'productTypeID' => array(  				                'allowEmpty' => true            ),
            'store' => array(  				
                'allowEmpty' => true
            ),
            'sideID' => array(  				
                'allowEmpty' => true
            ),
            'predefinedCoversType' => array(  				
                'allowEmpty' => true
            ),
            'showFirstCover' => array(  				
                'allowEmpty' => true
            ),
            'showSecondCover' => array(  				
                'allowEmpty' => true
            ),
            'cover1TitleID' => array(  				
                'allowEmpty' => true
            ),
            'cover2TitleID' => array(  				
                'allowEmpty' => true
            ),
            'cover1ID' => array(  				
                'allowEmpty' => true
            ),
            'cover2ID' => array(  				
                'allowEmpty' => true
            ),
            'cover1photoID' => array(  				
                'allowEmpty' => true
            ),
            'cover2photoID' => array(  				
                'allowEmpty' => true
            ),
            'cover2photoID' => array(  				
                'allowEmpty' => true
            ),
            'filterID' => array(  				
                'allowEmpty' => true
            ),
            'productGeneralSizeID' => array(  				
                'allowEmpty' => true
            )
				
        );
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$validators["title-".$val->suffix]['allowEmpty'] 			= true;
			$validators["text-".$val->suffix]['allowEmpty'] 			= true;
			$validators["deliveryTime-".$val->suffix]['allowEmpty'] 	= true;
			$validators["metaTitle-".$val->suffix]['allowEmpty'] 		= true;
			$validators["keywords-".$val->suffix]['allowEmpty'] 		= true;
			$validators["description-".$val->suffix]['allowEmpty'] 		= true;
			$validators["price-".$val->suffix]['allowEmpty'] 			= true;
			$validators["originalPrice-".$val->suffix]['allowEmpty'] 	= true;
			$validators["discount-".$val->suffix]['allowEmpty'] 		= true;
			$validators["deliveryText-".$val->suffix]['allowEmpty'] 	= true;
			$validators["hTitle-".$val->suffix]['allowEmpty'] 			= true;
			$validators["hpTitle-".$val->suffix]['allowEmpty'] 			= true;
			$validators["hCat-".$val->suffix]['allowEmpty'] 			= true;
			$validators["hText-".$val->suffix]['allowEmpty'] 			= true;
			$validators["zCat-".$val->suffix]['allowEmpty'] 			= true;
			$validators["textAuction-".$val->suffix]['allowEmpty'] 		= true;
			$validators["oldUrl-".$val->suffix]['allowEmpty'] 			= true;
			$validators["textSize-".$val->suffix]['allowEmpty'] 		= true;
		}
		
        return $validators;
		
	}

	private function setMenuLinks(){
		$link 		= new Content_Models_Link();
		$mainLinks = $link->getAllItems("parentID = '0' AND isEshopCategory = 1",'priority');
		$subLinks  = $link->getAllItems("parentID <> '0' AND isEshopCategory = 1",'priority');
		 
		$this->subLinksArr = array();
		$this->linksOutput = "";
		 
		foreach($subLinks as $val){
			$this->subLinksArr[$val->parentID][] = $val;
		}
		 
		//pouzijeme rekurzi pro vytvoreni odkazu a k nim prislusné pododkazy
		$this->recurseLinks($mainLinks,1);
		 
		$this->view->linksOutput = $this->linksOutput;
	}
    private function recurseLinks($children,$first = 0){
    	
    	$icon = "";
    	$nested = "";
    	if(!$first){
    		$icon = "<span class='glyphicons glyphicons-chevron-up'></span>";
    		$nested = "nested";
    	}
    	
    	//prochazime postupne od korene a zanorujeme se do childu
    	foreach($children as $child){
    		$this->linksOutput .= "<span class='link $nested'><span class='text $nested'>$icon <span>$child->title</span></span><span class='link-id'>$child->linkID</span>";
    		
    			if(!empty($this->subLinksArr[$child->linkID]))
    				$this->recurseLinks($this->subLinksArr[$child->linkID]);
    			
    		$this->linksOutput .= "</span>";
    	}
    }
	private function setSublinks($id = 0){
		$this->view->subLinks = array();
		if($id == 0){
			$this->view->subLinks[] = array(
					"title" => 	"Základní údaje",
					"url"	=>	"/admin/eshop/produkt/pridat",
					"isMain"=> 	1
			);
		}else{
			$this->view->subLinks[] = array(
					"title" => 	"Základní údaje",
					"url"	=>	"/admin/eshop/produkt/upravit/id/$id",
					"isMain"=> 	1
			);
		}
		$this->view->subLinks[] = array(
			"title" => 	"Fotky",
			"url"	=>	"/admin/eshop/produkt/fotky/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Rozměry",
			"url"	=>	"/admin/eshop/produkt/rozmery/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Ceny",
			"url"	=>	"/admin/eshop/produkt/ceny/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Status a slevy",
			"url"	=>	"/admin/eshop/produkt/status-a-slevy/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Heuréka",
			"url"	=>	"/admin/eshop/produkt/heureka/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Zboží.cz",
			"url"	=>	"/admin/eshop/produkt/zbozicz/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Kategorie a potahy",
			"url"	=>	"/admin/eshop/produkt/kategorie-a-potahy/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Eshop",
			"url"	=>	"/admin/eshop/produkt/eshop/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Aukce",
			"url"	=>	"/admin/eshop/produkt/aukce/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Vyloučit potahy",
			"url"	=>	"/admin/eshop/produkt/vyloucit-potahy/id/$id",
			"isMain"=> 	0
		);
		$this->view->subLinks[] = array(
			"title" => 	"Filtry",
			"url"	=>	"/admin/eshop/produkt/filtry/id/$id",
			"isMain"=> 	0
		);
		
	}
	
	
    
    
}

?>