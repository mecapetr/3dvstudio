<?php

/*
 * Třída obsluhujici clanky na hlavni strance
 *
 * @copyright 2008 Polar Televize Ostrava
 *
 */


class Library_WebBase extends Library_WholeWeb 
{    
		
    protected $portalItems;
    protected $url;
    protected $auth;
    protected $userID;
    protected $allow;
    protected $isLogin;
    protected $roundID;
    protected $translate;
    protected $allLanguages;
    protected $sportList;
    protected $sportListParentLink;
	
	function init()
	{
		$this->view->baseUrl = $this->_request->getBaseUrl();  	
	    $this->view->title = 'eCENTRE';
	   	   	    
	}

	function setDefault()
	{
   		$link 		= new Content_Models_Link();
	    $this->auth = Zend_Auth::getInstance();
	    $this->initModules();
	    		    
	    if($this->modulesData->jazykoveMutace){
	    	$this->translate = Zend_Registry::get("Zend_Translate");
	    	$this->getLanguages();
	    	
	    	$this->getConcreteLang();
	    	
	    	$FBlang = "cs_CZ";
	    	if($this->concreteLang->lang == "en")$FBlang = "en_GB";
	    	if($this->concreteLang->lang == "de")$FBlang = "de_DE";
	    	if($this->concreteLang->lang == "sk")$FBlang = "sk_SK";
	    	if($this->concreteLang->lang == "ru")$FBlang = "ru_RU";
	    	
	    	$this->view->FBlang = $FBlang;
	    }
	    
	    $this->view->actualUrl = $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	    $this->view->actualDomain = $this->actualDomain = $_SERVER['HTTP_HOST'];
	    
	    $this->sendFormData();
	   
	    $this->detectMobile();
	    $this->getLayoutSocialIcons();
	    $this->getLayoutTexts();
	    
	    /*
	    $this->checkLogin();
	    $this->setLogin();
	    $this->setLogout();
	    */
	    
		
	} // end of indexAction    
	protected function detectMobile(){
		require_once "Library/Mobile_Detect.php";
		$detect 	= new Mobile_Detect;
		if($detect->isMobile()){
			$this->view->isMobileMode = $this->isMobileMode = true;
		}else{
			$this->view->isMobileMode = $this->isMobileMode = false;
		}
		if(isset($_COOKIE['showDesktopMode'])){
			if( $_COOKIE['showDesktopMode'] == 1) {
				$this->view->showDesktopMode = $this->showDesktopMode = true;
			} else {
				$this->view->showDesktopMode = $this->showDesktopMode = false;
			}
		}else{
			$this->view->showDesktopMode = $this->showDesktopMode = false;
		}
	}
	
	protected function getLayoutSocialIcons(){
	    $socialIcons 				= new Models_SocialIcons();
	    $lang 						= new Zend_Session_Namespace("lang");
	    $allSocialIcons 			= $socialIcons->getAll($lang->lang,"showIcon = 1");
	    
	    
	    $this->view->allSocialIcons = $allSocialIcons;
	}
	protected function getLayoutTexts(){
	    $homepageText 				= new Models_HomepageText();
	    
	    $lang 						= new Zend_Session_Namespace("lang");
	    $allHomepageTexts 			= $homepageText->getAll($lang->lang);
	    $allHomepageTextsArr		= array();
	    
	    foreach ($allHomepageTexts as $val){
	        $allHomepageTextsArr[$val->homepageTextID] = $val;
	    }
	    
	    $this->view->allLayoutTexts = $allHomepageTextsArr;
	}
	protected function sendFormData(){
		
		if($this->_request->isPost()){
			
			$secID = $this->_request->getPost("sec");
			if(!empty($secID) && is_numeric($secID)){
				
				$linkSectionForm = new Content_Models_LinkSectionForm();
				$data = $linkSectionForm->getAllItems("linkSectionID = '$secID'", "linkSectionFormID");
				
				$postData = array();
				$send = false;
				foreach($data as $d){
					
					$p = $this->_request->getPost($d->niceTitle."_".$d->linkSectionFormID);
					if(!empty($p))$send = true;
					$postData[$d->linkSectionFormID]["value"] = $p;
					$postData[$d->linkSectionFormID]["title"] = $d->title;
					$postData[$d->linkSectionFormID]["type"]  = $d->type;
					
				}
				$text  = "";
				if($send){
					
					foreach($postData as $value){
						if($value["type"] == "text"){
							$text .= '<p style="margin-bottom:15px;"><span style="display:inline-block;width:150px;">'.$value["title"].':</span> '.$value["value"].'</p>';
						}
					
						if($value["type"] == "dropdown"){
							$text .= '<p style="margin-bottom:15px;"><span style="display:inline-block;width:150px;">'.$value["title"].':</span> '.$value["value"].'</p>';
						}
					
						if($value["type"] == "checkbox" || $value["type"] == "radio"){
							
							if($value["type"] == "checkbox"){
								if(empty($value["value"]))$value["value"] = array();
								$text .= '<p style="margin-bottom:15px;"><span style="display:inline-block;width:150px;">'.$value["title"].':</span> '.implode(",",$value["value"]).'</p>';
							}
							if($value["type"] == "radio")$text .= '<p style="margin-bottom:15px;"><span style="display:inline-block;width:150px;">'.$value["title"].':</span> '.$value["value"].'</p>';
						
						}
					
						if($value["type"] == "textarea"){
							$text .= '<p style="margin-bottom:15px;">'.$value["value"].'</p>';
						}
					}
					
					$this->sendMail($this->regEmail['cz'], $this->regName['cz'], $data[0]->email, "Zpráva z formuláře", $text);
					$this->view->successFormMessage = "Zpráva byla úspěšně odeslána.";
				}else{
					$this->view->errorFormMessage = "Nevyplnili jste některou z kolonek.";
				}
				
			}
			
		}
		
	}
				
	protected function checkError($content){
 
	    if(count($content) == 0){
	    	$this->_forward("error","Error","webBase");  
	    }else{
	    	
	    	return true;
	    }

	}
	
	protected function setTitle($title){
		
		$this->view->headTitle = $title;

	}
	
	protected function setDescription($description){
		
		$this->view->headDescription = $description;

	}
	protected function setKeyWords($keyWords){
		
		$this->view->headKeyWords = $keyWords;

	}
	
	public function setPaging($table,$pageCount,$productState,$contentRows,$count,$order,$ascDesc,$jquery=true,$pageTitle=''){
		
		//strankovani
        $paging  = new Library_Paging();
		$page    = $this->_request->getParam("page");
        
		if($page)$paging->setPage($page);
		$paging->setJquery($jquery);
		$paging->setContent($contentRows."','$order','$ascDesc");
		$paging->setAllItemCount($count);
		$paging->setPageItemCount($pageCount);
		$paging->setPageTitle($pageTitle);
		$paging->setBlockID("main-paging");   
	    $paging->setNumberClass("number");   
		
		$limit = $paging->execute();
		
		$productState = "paging".$productState;
		$this->view->$productState 	= $paging->getPaging();
		$this->view->actualPage		= $paging->getActualPage();
		$this->view->ascDesc 	= $ascDesc;
		$this->view->order 		= $order;
		
		return $limit;
	}

	public function setBreadCrumb($list){
		
		$breadCrumb = "";
		
		if(count($list) > 0){
			
			$breadCrumb = '';
			
			foreach($list as $key => $l){
				if(!empty($l)){
					$breadCrumb .= '<a href="/'.$l.'">'.$key.'</a> <span class="separator">/</span> ';
				}else{
					$breadCrumb .= $key;
				}
			}
		}
		
		$this->view->breadCrumb = $breadCrumb;
		
	}
	protected function getLanguages(){
		 
		$lang = new Models_Language_DB_LanguageMutation();
		$this->view->languages = $this->allLanguages = $lang->getAllItems("enabled = 1","priority");
		 
	}	
	
	protected function getConcreteLang(){
	
		$lang = new Zend_Session_Namespace("lang");
		$this->view->concreteLang = $this->concreteLang =  $lang;
		
		$langT = new Models_Language_DB_LanguageMutation();
		$this->view->wholeLangData = $this->wholeLangData = $langT->getRow("suffix = '$lang->lang'");
	
	}
	public function getShortTel($tel){
		$shortTel = "";
		$tel = str_replace(" ", "", $tel);
		$shortTel .= substr($tel,0,3) . "..." . substr($tel,-3);
		return $shortTel;
	}
	   
   private function checkLogin(){
   
   	 
	   	if(!empty($this->auth->getStorage()->read()->userID)){
	   
	   		$this->view->loginUser   	= $this->loginUser = $this->auth->getStorage()->read()->name." ".$this->auth->getStorage()->read()->surname;
	   		$this->view->loginName   	= $this->auth->getStorage()->read()->name;
	   		$this->view->loginSurname   = $this->auth->getStorage()->read()->surname;
	   		$this->view->loginStreet 	= $this->auth->getStorage()->read()->street;
	   		$this->view->loginCity   	= $this->auth->getStorage()->read()->city;
	   		$this->view->loginZip    	= $this->auth->getStorage()->read()->zip;
	   		$this->view->loginEmail    	= $this->auth->getStorage()->read()->email;
	   		$this->view->loginTel   	= $this->auth->getStorage()->read()->tel;
	   		$this->view->loginUserID 	= $this->loginUserID = $this->auth->getStorage()->read()->userID;
	   
	   	}
   
   }
   private function setLogin(){
   	
   	if($this->_request->getPost("login")){
   	
   		Zend_Loader::loadClass('Zend_Filter_StripTags');
   		$filter = new Zend_Filter_StripTags();
   	
   		//ovďż˝ďż˝eni nepovolenďż˝ch tagu
   		$password = trim($filter->filter($this -> _request -> getPost('password')));
   		$login = trim($filter->filter($this -> _request -> getPost('name')));
   		
   		//overeni platnosti hesla
   		if ($password != '' && $login != '') {
   			Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
   			Zend_Loader::loadClass('Zend_Auth');
   			$db = Zend_Registry::get('db');
   			$authAdapter = new Zend_Auth_Adapter_DbTable($db);
   			$authAdapter->setTableName('user');
   			$authAdapter->setIdentityColumn('email');
   			$authAdapter->setCredentialColumn('password');
   			$authAdapter->setCredentialTreatment('MD5(MD5(?))');
   			$authAdapter->setIdentity($login);
   			$authAdapter->setCredential($password."-apr!");
   			$auth = Zend_Auth::getInstance();
   			$auth->clearIdentity();
   			$result = $auth->authenticate($authAdapter);
   	
   			//jestli je spravne zadane heslo a jmeno, odkaze do administrace
   			if ($result->isValid()) {
   				$data = $authAdapter->getResultRowObject(null,array('password'));
   	
   				$auth->getStorage()->write($data);
   	
   				$userID = $auth->getStorage()->read()->userID;
   				$where = "userID = '$userID'";
   				$currentDate = date("Y-m-d H:i:s",Time());
   				$user = new AdminBase_Users_User();
   				$data = array(
   								    "date"=>	$currentDate
   				);
   				$user->updateData($data,$where);
   	
   				$this->_redirect('/');
   	
   			} else {
   				$this->view->errorLog = 'Zadali jste špatné uživatelské jméno nebo heslo!';
   			}
   		}
   		else{
   			$this->view->errorLog = 'Nevyplnili jste všechny údaje!';
   		}
   			
   	 }
   	
   }
   
   protected function setLogout(){
   	
	   	if($this->_request->getPost("logout")){
	   		$this->_redirect("/webBase/logout");
	   	}
   	
   }
   
   
   protected function getProductCategoryContent($linkContent,$linkC = ""){
   		$product 		= new Eshop_Models_Product();
   		$paging  		= new Library_Paging();
   		$link  			= new Content_Models_Link();
   		$scripts		= new Library_Scripts();
   		$categoryFilter	= new Eshop_Models_CategoryFilter();
   		$categoryStatus = new Eshop_Models_CategoryStatus();
   		$lang 			= new Zend_Session_Namespace("lang");
   		
   		
   		$page    = $this->_request->getParam("page");
   		
   		$catStat          = $categoryStatus->getAllItems("linkID = '$linkContent->linkID'");
   		$countCatStat     = count($catStat);
   		$sortFilterParams = $this->setFiltersAndOrdrers($linkContent,$linkC);
   		
   		$statusesID = array();
   		$implodeStatuses = "";
   		
   		if($countCatStat > 0){
   			
   			foreach($catStat as $s){
   				$statusesID[] = $s->productStatusID;
   			}
   			
   			$implodeStatuses = implode(",",$statusesID);
   			   			
   			$count = count($product->getAllCategoryProducts($lang->lang,"showProduct = 1 AND LM.suffix = '$lang->lang' AND $sortFilterParams->where",$sortFilterParams->having,$sortFilterParams->orderByComplete,null,null,"JOIN product_product_status PPSS ON (PPSS.productID = P.productID AND PPSS.productStatusID IN (".$implodeStatuses."))"));	 
   		}else{
   			$count = count($product->getAllCategoryProducts($lang->lang,"showProduct = 1 AND PL.linkID = $linkContent->linkID AND LM.suffix = '$lang->lang' AND $sortFilterParams->where",$sortFilterParams->having,$sortFilterParams->orderByComplete));
   		}

   		//$link = $this->implodeUrlLink($linkContent,$link); // sestavi url adresu dle linku
   		if($page)$paging->setPage($page);
   		$paging->setAllItemCount($count);
   		$paging->setPageItemCount(36);
   		$paging->setBlockID("main-paging");
   		$paging->setNumberClass("number");
		$paging->showActualPageNumber(true);
   		$paging->setJquery(false);
   		$paging->setPageTitle($sortFilterParams->linkForPaging);
   		 
   		$limit = $paging->execute();
   		$this->view->allProducts = array();
   		if($limit){
   			 
   			if($countCatStat > 0){
   				$allProducts = $product->getAllCategoryProducts($lang->lang,"showProduct = 1 AND LM.suffix = '$lang->lang' AND $sortFilterParams->where",$sortFilterParams->having,$sortFilterParams->orderByComplete,$limit[1],$limit[0],"JOIN product_product_status PPSS ON (PPSS.productID = P.productID AND PPSS.productStatusID IN (".$implodeStatuses."))");
   				
   			}else{
   				$allProducts = $product->getAllCategoryProducts($lang->lang,"showProduct = 1 AND PL.linkID = $linkContent->linkID AND LM.suffix = '$lang->lang' AND $sortFilterParams->where",$sortFilterParams->having,$sortFilterParams->orderByComplete,$limit[1],$limit[0]);
   			}
   			
   			foreach($allProducts as $k => $pr){


   				$pr->stringPrice = number_format($pr->price,$this->wholeLangData->decimal,","," ")." ".$pr->currencySign;
   				
   				$allProducts[$k]->isInAuction 			= false;
   				if(!empty($pr->dateAuction)){
   					$productAuction	    				= new Auction_Models_ProductAuction();
   					$allProducts[$k]->timeToEnd 		= $scripts->getAuctionTime($pr->dateAuction);
   					$allProducts[$k]->isInAuction 		= true;
   					$allProducts[$k]->minPriceAuction 	= number_format(round($pr->minPriceAuction / $this->wholeLangData->exchangeRate,0),$this->wholeLangData->decimal,","," ");
   					$lasBid								= $productAuction->getLasBidPrice("PA.productID = $pr->productID AND PA.deleted = 0");
   					if(!empty($lasBid)){
						$allProducts[$k]->lasBidPriceFormated		= number_format(round($lasBid->value/ $this->wholeLangData->exchangeRate,2),$this->wholeLangData->decimal,","," ");
   						$allProducts[$k]->lasBidPrice				= number_format(round($lasBid->value/ $this->wholeLangData->exchangeRate,0),0,",","");
   					}else{
						$allProducts[$k]->lasBidPriceFormated		= number_format(round($pr->priceAuction / $this->wholeLangData->exchangeRate,2),$this->wholeLangData->decimal,","," ");
   						$allProducts[$k]->lasBidPrice				= number_format(round($pr->priceAuction / $this->wholeLangData->exchangeRate,0),0,",","");
   					}
   				}
   				
   				if($countCatStat > 0){
   					$pr->statuses  = array();
   				}
   				
   				if(!empty($pr->statuses)){
   					
   					$statuses = explode(",",$pr->statuses);
   					$pr->statuses = array();
   					foreach ($statuses as $stat){
   						$params = explode("-",$stat);
   						$item['title']           = $params[0];
   						$item['color']           = $params[1];
   						$pr->statuses[] = $item;
   					}
   					
   					 
   					if($pr->statuses)$pr->statusesCount = count($pr->statuses);
   					else $pr->statusesCount = 0;
   					
   					if($pr->statusesCount == 2)$pr->statusesCols = "12";
   					if($pr->statusesCount == 3)$pr->statusesCols = "6";
   					if($pr->statusesCount >= 4)$pr->statusesCols = "4";
   					   					
   					foreach ($pr->statuses as $key => $st){
   						
   						if($st['title'] == "SLEVA"){
   							if($pr->discountInPercentage){
   								if($lang->lang == "cz"){
   									$pr->statuses[$key]['title'] = "-" . number_format($pr->discount,0,","," ") . "%";
   								}else{
   									$pr->statuses[$key]['title'] = "-" . $pr->discount . "%";
   								}
   							}else{
   								if($lang->lang == "cz"){
   									$pr->statuses[$key]['title'] = "-" . number_format($pr->discount,0,","," ") . " " .$pr->currencySign;
   								}else{
   									$pr->statuses[$key]['title'] = "-" . $pr->discount . " " .$pr->currencySign;
   								}
   							}
   						}else if($st['title'] == "Dodání"){
	    					$pr->statuses[$key]['title'] =  $pr->deliveryText;
	    				}
   					}
   					
   				}else $pr->statusesCount = 0;
   			}
   			$this->view->allProducts = $allProducts;
   		
   		}else{
   			//$this->_forward("error","error","core");
   		}

   		if($countCatStat > 0){
   			$minDepth = $product->getCategoryMinDepthSize(1,"JOIN product_product_status PPS ON (PPS.productID = P.productID AND PPS.productStatusID IN ($implodeStatuses))");
   			$maxDepth = $product->getCategoryMaxDepthSize(1,"JOIN product_product_status PPS ON (PPS.productID = P.productID AND PPS.productStatusID IN ($implodeStatuses))");
   			$minWidth = $product->getCategoryMinWidthSize(1,"JOIN product_product_status PPS ON (PPS.productID = P.productID AND PPS.productStatusID IN ($implodeStatuses))");
   			$maxWidth = $product->getCategoryMaxWidthSize(1,"JOIN product_product_status PPS ON (PPS.productID = P.productID AND PPS.productStatusID IN ($implodeStatuses))");
   		}else{
	   		$minDepth = $product->getCategoryMinDepthSize("PL.linkID = $linkContent->linkID");
	   		$maxDepth = $product->getCategoryMaxDepthSize("PL.linkID = $linkContent->linkID");
	   		$minWidth = $product->getCategoryMinWidthSize("PL.linkID = $linkContent->linkID");
	   		$maxWidth = $product->getCategoryMaxWidthSize("PL.linkID = $linkContent->linkID");
   		}

   		$this->view->showDepthFilter = false;
   		if((!empty($minDepth) || is_numeric($minDepth)) && (!empty($maxDepth) || is_numeric($maxDepth)) && $minDepth != $maxDepth){
   			$this->view->showDepthFilter = true;
   		}
   		$this->view->showWidthFilter = false;
   		if((!empty($minWidth) || is_numeric($minWidth)) && (!empty($maxWidth) || is_numeric($maxWidth)) && $minWidth != $maxWidth){
   			$this->view->showWidthFilter = true;
   		}
   		
   		$this->view->minDepth = $minDepth;
   		$this->view->maxDepth = $maxDepth;
   		$this->view->minWidth = $minWidth;
   		$this->view->maxWidth = $maxWidth;
   		
   		$minPrice = $maxPrice = 0;
   		if($countCatStat > 0){

   			$minPrice = $product->getCategoryMinPrice(1,"JOIN product_product_status PPS ON (PPS.productID = P.productID AND PPS.productStatusID IN ($implodeStatuses))");
   			$maxPrice = $product->getCategoryMaxPrice(1,"JOIN product_product_status PPS ON (PPS.productID = P.productID AND PPS.productStatusID IN ($implodeStatuses))");
   			
   		}else{
   			$minPrice = $product->getCategoryMinPrice("PL.linkID = $linkContent->linkID");
   			$maxPrice = $product->getCategoryMaxPrice("PL.linkID = $linkContent->linkID");
   		}
   		$this->view->minPrice = number_format($minPrice,0,",","");
   		$this->view->maxPrice = number_format($maxPrice,0,",","");
   		
   		$this->view->paging = $paging->getPaging();
   		
		$allFilters = $categoryFilter->getAllWithCategory("CF.linkID = $linkContent->linkID");
   		
   		$this->view->sortFilterParams 	= $sortFilterParams;
   		$this->view->allFilters 		= $allFilters;
   }
   
   protected function setFiltersAndOrdrers($linkContent = "",$linkC = "",$url = ""){      		

   		$filter		= new Zend_Filter_StripTags();
   		$where    	= $filter->filter(stripslashes($this->_request->getParam("w")));
   		$orderBy    = $filter->filter(stripslashes($this->_request->getParam("by")));
   		$ascDesc    = $filter->filter(stripslashes($this->_request->getParam("ad")));
   		$page    	= $filter->filter(stripslashes($this->_request->getParam("page")));

   		$returnParams = new stdClass();
   		$returnParams->orderBy 			= "P.priority";
   		$returnParams->ascDesc 			= "";
   		$returnParams->ascDescOposite	= "asc";
   		$returnParams->orderByComplete 	= "";
   		$returnParams->linkForSort 		= "";
   		$returnParams->linkForPaging	= "";
   		$returnParams->where			= 1;
   		$returnParams->whereParam		= 1;
   		$returnParams->depthFrom		= null;
   		$returnParams->depthTo			= null;
   		$returnParams->widthFrom		= null;
   		$returnParams->widthTo			= null;
   		$returnParams->priceFrom		= null;
   		$returnParams->priceTo			= null;
   		$returnParams->size				= null;
   		$returnParams->filter			= null;
   		$returnParams->having			= null;
   		$returnParams->page				= 1;

   		if(!empty($where) && $where != 1){
   			$whereHavingParams 			= $this->parseWhereFilterParams($where);
   			$returnParams->whereParam 	= $where;
   			$returnParams->where 		= $whereHavingParams['completeWhere'];
   			$returnParams->having 		= $whereHavingParams['completeHaving'];

   			$returnParams->depthFrom	= $whereHavingParams['depthFrom'];
   			$returnParams->depthTo		= $whereHavingParams['depthTo'];
   			$returnParams->widthFrom	= $whereHavingParams['widthFrom'];
   			$returnParams->widthTo		= $whereHavingParams['widthTo'];
   			$returnParams->priceFrom	= $whereHavingParams['priceFrom'];
   			$returnParams->priceTo		= $whereHavingParams['priceTo'];
   			$returnParams->size			= $whereHavingParams['size'];
   			$returnParams->filter		= $whereHavingParams['filter'];
   		}
   		
   		if(!empty($page)){
   			$returnParams->page = $page;
   		}
   		if(!empty($orderBy)){
   			$returnParams->orderBy = $orderBy;
   		}
   		if(!empty($ascDesc)){
   			$returnParams->ascDesc = $ascDesc;
   			if($ascDesc == "asc"){
   				$returnParams->ascDescOposite = "desc";
   			}else{
   				$returnParams->ascDescOposite = "asc";
   			}
   		}
   		$returnParams->orderByComplete = $returnParams->orderBy . " " . $returnParams->ascDesc;
   		

   		if(!empty($linkContent)){
   			$linkForSort = "/" . $linkContent->niceTitle;   	
	   	}
	   	if(!empty($linkC)){
	   		$linkForSort = "/" . $linkC->niceTitle . $linkForSort;
	   	}
	   	if(!empty($url)){
	   		$linkForSort = $url;
	   	}
	   	
	   	
	   	$returnParams->linkForSort = $linkForSort;

	   	$returnParams->linkForPaging	= $linkForSort . "/w/$where/by/$orderBy/ad/$ascDesc";
	   	
	   	
	   	return $returnParams;
   }
   protected function parseWhereFilterParams($whereParam){
   		$where 	= "";
   		$having = "";
   		parse_str($whereParam, $params);
   		if(!empty($params['depth'])){
   			$params['depth'] 			= explode(",", $params['depth']);
   		}
   		if(!empty($params['width'])){
   			$params['width'] 			= explode(",", $params['width']);
   		}
   		if(!empty($params['price'])){
   			$params['price'] 			= explode(",", $params['price']);
   		}
   		if(!empty($params['filter'])){
   			$params['splittedFilter'] 	= explode(",", $params['filter']);
   		}


   		$where .= $params['price'][0] . " <= P.price AND P.price <= " . $params['price'][1];
   		// hodnota 1 je pro vsechny produkty bez omezeni velikosti
   		if($params['size'] != 1){
   			$where .= " AND " . $params['size'] . " = P.productGeneralSizeID " ;
   		}
   		if(!empty($params['splittedFilter'])){
   			$IN = $params['filter'];
   			$compareFilterCount = count($params['splittedFilter']);
   			$where .= " AND (SELECT COUNT(*) FROM product_filter PF WHERE PF.productID = P.productID AND PF.filterID IN ($IN)) = $compareFilterCount " ;
   		}
   		if(!empty($params['depth'])){
   			$having .= $params['depth'][0] . " <= productDepth AND productDepth <= " . $params['depth'][1];
   		}
   		if(!empty($params['width'])){
   			$having .= " AND " . $params['width'][0] . " <= productWidth AND productWidth <= " . $params['width'][1];
   		}
   		
   		$returnArray = array();
   		(!empty($params['depth'])) 	?  $returnArray['depthFrom'] 	= $params['depth'][0] 	: $returnArray['depthFrom'] = null;
   		(!empty($params['depth'])) 	?  $returnArray['depthTo'] 		= $params['depth'][1] 	: $returnArray['depthTo'] 	= null;
   		(!empty($params['width'])) 	?  $returnArray['widthFrom'] 	= $params['width'][0] 	: $returnArray['widthFrom'] = null;
   		(!empty($params['width'])) 	?  $returnArray['widthTo'] 		= $params['width'][1] 	: $returnArray['widthTo'] 	= null;
   		(!empty($params['price'])) 	?  $returnArray['priceFrom'] 	= $params['price'][0] 	: $returnArray['priceFrom'] = null;
   		(!empty($params['price'])) 	?  $returnArray['priceTo'] 		= $params['price'][1] 	: $returnArray['priceTo'] 	= null;
   		(!empty($params['size'])) 	?  $returnArray['size'] 		= $params['size'] 		: $returnArray['size'] 		= null;
   		(!empty($params['filter'])) ?  $returnArray['filter'] 		= $params['filter'] 	: $returnArray['filter'] 	= null;
   		$returnArray['completeWhere'] 	= $where;
   		$returnArray['completeHaving'] 	= $having;
   		
   		return  $returnArray;		
   }
   protected function getLinkContent($linkContent,$linkC = ""){
   	 
   	if($linkContent){
   
   		$linkPhoto     = new Content_Models_PhotoLink();
   		$linkFile      = new Content_Models_FileLink();
   		$linkWebVideos = new Content_Models_WebVideoLink();
   		$linkVideos 	= new Content_Models_VideoLink();
   		$link          = new Content_Models_Link();
   		$linkLang      = new Content_Models_LinkLang();
   

   		$this->setTranslate($linkLang, "linkID", $linkContent);
   		
   		if(!empty($linkC)){
   			$this->setTranslate($linkLang, "linkID", $linkC);
   		}

   		$this->view->linkContent   = $linkContent;
   		$this->view->linkPhotos    = $linkPhotos = $linkPhoto->getAllItemsWhere("PA.linkID = '$linkContent->linkID'");
   		$this->view->linkWebVideos = $linkWebVideos->getVideo($linkContent->linkID);
   		$this->view->linkFiles     = $linkFile->getAllItemsWhere("PA.linkID = '$linkContent->linkID'");
   		$this->view->linkVideos    = $this->getUploadedVideos($linkContent->linkID,$linkVideos,"linkID");
   		//$this->view->linkVideos  = $linkContent;
   		
   		$mainPhoto = null;
   		foreach ($linkPhotos as $val){
   			if($val->mainPhoto == 1)
   				$mainPhoto = $val;
   		}
   		$this->view->linkMainPhoto = $mainPhoto;
   		
   		//jestliže má dkaz nastavené, že může ukazovat své články
   		
   		$this->getArticles($linkContent,$link);
   			 
   		
   		//zobrazíveškerésekce odkazu
   		$linkFactory                       = new Content_Models_LinkFactory($this->_request, $this->_response);
   		$linkFactory->langModule           = $this->modulesData->jazykoveMutace;
   		$linkFactory->allLanguageMutations = $this->allLanguages;
   		
   		$linkFactory->getSections($linkContent->linkID);
   		
   		$this->view->linksUrl = $this->implodeUrlLink($linkContent, $link);
   
   		//informace o prvním parametru urladresy
   		$this->view->pageType = $linkContent;
   		if($linkC)$this->view->pageType = $linkC;
   
   	}else{
   		$this->_forward("error","error","core");
   	}
   	 
   }
   
   protected function getUploadedVideos($id,$table,$tableID){
   
   	$allVideos 		= $table->getAllVideos("$tableID = '$id'");
   
   	if($this->modulesData->jazykoveMutace){
   		foreach ($allVideos as $val){
   			$val->title 	= $this->translate->translate('video'.$val->videoID.'title');
   			$val->anotation = $this->translate->translate('video'.$val->videoID.'description');
   		}
   	}
   	 
   	return $allVideos;
   }
   protected function getArticle($id){
   
   	$article = new Content_Models_Article();
   	 
   	//zv�� po�et shl�dnut�
   	$article->updateView($id);
   	 
   	//data konkr�tn�ho �l�nku      
   	$lang = new Zend_Session_Namespace("lang");
   	$aData = $article->getArticle($id,$lang->lang);    
   
   	if($aData){
   
   		$this->view->articleData = $aData;
   			
   		$articlePhoto     = new Content_Models_PhotoArticle();
   		$articleFile      = new Content_Models_FileArticle();
   		$articleWebVideos = new Content_Models_WebVideoArticle();
   		$articleVideos    = new Content_Models_VideoArticle();
   			
   		$this->view->articlePhotos    = $articlePhoto->getAllItemsWhere("PA.articleID = '$aData->articleID'");
   		$this->view->articleWebVideos = $articleWebVideos->getVideo($aData->articleID);
   		$this->view->articleVideos 	  = $this->getUploadedVideos($aData->articleID,$articleVideos,"articleID");
   		$this->view->articleFiles     = $articleFile->getAllItemsWhere("PA.articleID = '$aData->articleID'");
   		
   		$this->view->articlePhotosCount    = count($this->view->articlePhotos);
   		$this->view->articleWebVideosCount = count($this->view->articleWebVideos);
   	}
   	return $aData;
   }
   
   protected function translateArticle($aData){
   	 
   	$aData->dateAdd = date("j.n.Y",strtotime($aData->dateAdd));
   	 
   	if($this->modulesData->jazykoveMutace)$aData->niceTitle   = $this->translate->translate("article".$aData->articleID."niceTitle");
   	if($this->modulesData->jazykoveMutace)$aData->title       = $this->translate->translate("article".$aData->articleID."title");
   	if($this->modulesData->jazykoveMutace)$aData->keywords    = $this->translate->translate("article".$aData->articleID."keywords");
   	if($this->modulesData->jazykoveMutace)$aData->description = $this->translate->translate("article".$aData->articleID."description");
   	if($this->modulesData->jazykoveMutace)$aData->metaTitle   = $this->translate->translate("article".$aData->articleID."metaTitle");
   	if($this->modulesData->jazykoveMutace)$aData->text        = $this->translate->translate("article".$aData->articleID."text");
   	if($this->modulesData->jazykoveMutace)$aData->anotation   = $this->translate->translate("article".$aData->articleID."anotation");
   	 
   	return $aData;
   }
   
   protected function getArticles($linkContent,$link){
   
   	$article = new Content_Models_Article();
   	$paging  = new Library_Paging();
   	$page    = $this->_request->getParam("page");
   
   	$count           = $article->getCountArticles($linkContent->linkID);
   
   	$link = $this->implodeUrlLink($linkContent,$link); // sestavi url adresu dle linku
   
   	if($page)$paging->setPage($page);
   	$paging->setAllItemCount($count);
   	$paging->setPageItemCount(12);
   	$paging->setBlockID("main-paging");
   	$paging->setNumberClass("number");
   	$paging->setJquery(false);
   	$paging->setPageTitle($link);
   	$paging->showActualPageNumber(false);
   	$paging->setShowNumbers(true);
   	$paging->setShowArrows(true);
   	$paging->setAddBootstrapClass(true);
   	 
   	$limit = $paging->execute();
   	$this->view->allArticles = array();
   	if($limit){
   
   		$lang = new Zend_Session_Namespace("lang");
   		$this->allDifferentArticles = array();
   		$allArticles = $article->getArticles("A.priority,P.mainPhoto DESC",$limit[1],$limit[0],$linkContent->linkID,$lang->lang);
   		$this->view->allArticles = $this->prepareArticles($allArticles,$article,$link);
   		$this->view->allDifferentArticles = $this->allDifferentArticles;
   		 
   	}else{
   		//$this->_forward("error","error","core");
   	}
   	 
   	$this->view->paging = $paging->getPaging();
   
   }
   
   protected function prepareArticles($allArticles,$article,$link){
   		
   	$levelArticles = $article->getLevelArticles(0);
   	 
   	foreach ($allArticles as $item){
   		$item->dateAdd = date("j.n.Y",strtotime($item->dateAdd));
      
   		foreach($levelArticles as $levA){
   			//echo $levA->otherLink;
   			if($levA->articleID == $item->articleID){    				
   				$item->link  = $link;		
   			}
   		}

   		$otherLinks = explode(",",$item->articleOtherLinks);
   		foreach($otherLinks as $othL){
   			$this->allDifferentArticles[$othL][] = $item;
   		}
   	}
   
   	return $allArticles;
   }
   
   protected function setMetadata($keyWords,$description,$title = null){
   	    	 
   	   $this->setTitle($title);
   	   $this->setDescription($description);
   	   $this->setKeyWords($keyWords);
   	 
   }

   public function setTranslates($tableLangClass,$tableIDParamName,&$itemToTranslate){
   	$language 				= new Models_Language_Language();
   	$translates				= array();

   	$lang 		= new Zend_Session_Namespace("lang");
   	foreach($itemToTranslate as $val){
   		$menuArrIN[] = $val->$tableIDParamName;
   	}
   	if(!empty($menuArrIN)){
   		$menuIN 			= implode(",", $menuArrIN);
   		$translatesArr		= $tableLangClass->getAllItems("$tableIDParamName IN ($menuIN) AND lang = '$lang->lang'", $tableIDParamName);
   		foreach ($translatesArr as $tr){
   			$translates[$tr->$tableIDParamName] = array();
   			foreach ($tr as $key => $val){
   				$translates[$tr->$tableIDParamName][$key] = $val;
   			}
   		}
   	}
   	//print_r($translates);
   
   	$allLanguageMutations 	= $language->getDbLanguages();
   	$langData				= array();
   	foreach($itemToTranslate as $key => $val){
   
   			if(is_array($itemToTranslate[$key])){
   				foreach ($val as $itKey => $it){
   					if(isset($translates[$val->$tableIDParamName][$itKey]) && isset($itemToTranslate[$key][$itKey])){
   						$itemToTranslate[$key][$itKey] = $translates[$val->$tableIDParamName][$itKey];
   					}
   				}
   			}else{
   				foreach ($val as $itKey => $it){
   					if(isset($translates[$val->$tableIDParamName][$itKey]) && isset($itemToTranslate[$key]->$itKey)){
   						$itemToTranslate[$key]->$itKey = $translates[$val->$tableIDParamName][$itKey];
   					}
   				}
   			}
   
   	}
   	//print_r($itemToTranslate);
   }
   public function setTranslate($tableLangClass,$tableIDParamName,&$itemToTranslate){
    $language 				= new Models_Language_Language(); 
    $translates				= array();

    $lang 		= new Zend_Session_Namespace("lang");
   	if(is_array($itemToTranslate)){
   		$p = $itemToTranslate['$tableIDParamName'];
   		$translatesArr		= $tableLangClass->getAllItems("$tableIDParamName = $p AND lang = '$lang->lang'", $tableIDParamName);
   	}else{
   		$p = $itemToTranslate->$tableIDParamName;
   		$translatesArr		= $tableLangClass->getAllItems("$tableIDParamName = $p AND lang = '$lang->lang'", $tableIDParamName);
   	}
   	foreach ($translatesArr as $tr){
   		$translates[$tr->$tableIDParamName] = array();
   		foreach ($tr as $key => $val){
   			$translates[$tr->$tableIDParamName][$key] = $val;
   		}
   	}
   	
   	//print_r($translates);
	
   	$allLanguageMutations 	= $language->getDbLanguages();
   	$langData				= array();
   		
   	if(is_array($itemToTranslate)){ 
   		foreach ($itemToTranslate as $itKey => $it){
   			if(isset($translates[$val->$tableIDParamName][$itKey]) && isset($itemToTranslate[$itKey])){
   				$itemToTranslate[$itKey] = $translates[$val->$tableIDParamName][$itKey];
   			}
   		}
   	}else{   				
   		foreach ($itemToTranslate as $itKey => $it){
   			if(isset($translates[$itemToTranslate->$tableIDParamName][$itKey]) && isset($itemToTranslate->$itKey)){
   				$itemToTranslate->$itKey = $translates[$itemToTranslate->$tableIDParamName][$itKey];
   			}
   		}   				
   	}
   	
   	//print_r($itemToTranslate);
   }
   
   protected function getCategoryMenu($linkContent = "",$subLinkContent = ""){
   	
   	    $catMenu = $this->getCatMenu($linkContent,$subLinkContent,"top");
   		$this->view->menuCategories     = $catMenu[0];
   		$this->view->menuSubCategories  = $catMenu[1];
   		
   		$catLeftMenu = $this->getCatMenu($linkContent,$subLinkContent,"left");
   		$this->view->menuLeftCategories     = $catLeftMenu[0];
   		$this->view->menuLeftSubCategories  = $catLeftMenu[1];
   }

   protected function getCatMenu($linkContent = "",$subLinkContent = "",$menuType = ''){
   	
   		$menuT = "L.inMenu = 1";
   		if($menuType == "left")$menuT = "L.inLeftColumn = 1";
   	 
	   	$link 					= new Content_Models_Link();
	   	$linkLang				= new Content_Models_LinkLang();
	   	
	   	$menuArrIN				= array();
	   	$menu 					= $link->getMenu("L.active = 1 AND L.isEshopCategory = 1 AND $menuT");
		
	   	$this->setTranslates($linkLang, "linkID", $menu);
	   	$count = count($menu);
	   	$i = 1;
	      	 
	   	$subItems = $link->getLevelArticles("L.active = 1 AND L.parentID <> 0 AND L.isEshopCategory = 1 AND $menuT");
	   	$this->setTranslates($linkLang, "linkID", $subItems);
	   		   	
	   	$subMenu = array();
	   		   	
	   	foreach($subItems as $itm){
	   		$std            = new StdClass();
	   		$std->linkID    = $itm->linkID;
	   		$std->title     = $itm->title;
	   		$std->inFooter  = $itm->inFooter;
	   		$std->niceTitle = $this->implodeUrlLink($itm,$link);
	   			
	   		$std->class     = "";
	   		if(!empty($subLinkContent) && $itm->linkID == $subLinkContent->linkID){
	   			$std->class = "selected";
	   		}
	   			
	   		if($itm->otherLink)$std->niceTitle = $itm->otherLink;
	   		$subMenu[$itm->parentID][] = $std;
	   	}
	   	   
	   	foreach($menu as $it){
	   		 
	   		$linkUrl = $this->implodeUrlLink($it,$link);
	   		 
	   		$it->userAdd = "";
	   		$title = explode(" ",$it->title);
	   		if(count($title) == 1)$it->userAdd = "one-row";
	   
	   		if($count == $i)$it->userAdd .= " last";	   		 
	   		if(!$it->otherLink)$it->otherLink = $linkUrl;
	   		if(!empty($linkContent) && $linkContent->linkID == $it->linkID)$it->userAdd .= " selected";
	   		 
	   		//$it->title = str_replace(" ","<br />", $it->title);
	   		$i++;
	   		 
	   	}
	   	   		
   		return array($menu,$subMenu);
   
   }
   protected function getSideCategoryMenu($linkContent = "",$subLinkContent = ""){
   	 
   	$link 					= new Content_Models_Link();
   	$linkLang				= new Content_Models_LinkLang();
   	 
   	$menuArrIN				= array();
   	$menu 					= $link->getMenu("L.active = 1 AND L.isEshopCategory = 1 AND L.inBannerPlace = 1");
   
   	$this->setTranslates($linkLang, "linkID", $menu);
   	 
   	$count = count($menu);
   	$i = 1;   	 
   	    	
   	foreach($menu as $it){
   		 
   		$linkUrl = $this->implodeUrlLink($it,$link);
   		 
   		$it->userAdd = "";
   		$title = explode(" ",$it->title);
   		if(count($title) == 1)$it->userAdd = "one-row";
   
   		if($count == $i)$it->userAdd .= " last";
   		if(!$it->otherLink)$it->otherLink = $linkUrl;
   		if(!empty($linkContent) && $linkContent->linkID == $it->linkID)$it->userAdd .= " selected";
   		 
   		//$it->title = str_replace(" ","<br />", $it->title);
   		$i++;
   		 
   	}
   	$this->view->sideCategories = $menu;
   	 
   }
   
   protected function getMenu($linkContent = "",$subLinkContent = ""){
   	 
   	$link 					= new Content_Models_Link();
	$linkLnag				= new Content_Models_LinkLang();
   	$menu 					= $link->getMenu("L.active = 1 AND L.isEshopCategory = 0 AND L.inMenu = 1");
   	$footerMenu 			= $link->getMenu("L.active = 1 AND L.isEshopCategory = 0 AND L.inFooter = 1");

   	$this->setTranslates($linkLnag, "linkID", $menu);
   	
   	$count = count($menu);
   	$i = 1;
   	
   	$this->setAllMenuLinks($linkContent);

   	$subItems = $link->getLevelArticles("L.active = 1 AND L.parentID <> 0 AND L.isEshopCategory = 0 AND L.inMenu = 1");
   	$this->setTranslates($linkLnag, "linkID", $subItems);
   	
   	$subMenu = array();
   		 
   	foreach($subItems as $itm){
   			$std            = new StdClass();
   			$std->linkID    = $itm->linkID;
   			$std->title     = $itm->title;
   			$std->inFooter  = $itm->inFooter;
   			$std->niceTitle = $this->implodeUrlLink($itm,$link);
   	   
   			$std->class     = "";
   			if(!empty($subLinkContent) && $itm->linkID == $subLinkContent->linkID)$std->class = "selected";
   	   
   			if($itm->otherLink)$std->niceTitle = $itm->otherLink;
   			else $std->niceTitle =  "/".$this->wholeLangData->suffix.$std->niceTitle;
   			
   			$subMenu[$itm->parentID][] = $std;
   	}
   		 
   	$this->view->subLinks = $subMenu;

   	foreach($menu as $it){
   
   		$linkUrl = $this->implodeUrlLink($it,$link);
   
   		$it->userAdd = "";
   		$title = explode(" ",$it->title);
   		if(count($title) == 1)$it->userAdd = "one-row";
   			
   		if($count == $i)$it->userAdd .= " last";
   
   		if(!$it->otherLink)$it->otherLink = "/".$this->wholeLangData->suffix.$linkUrl;
   
   		if(!empty($linkContent) && $linkContent->linkID == $it->linkID)$it->userAdd .= " selected";
   
   		//$it->title = str_replace(" ","<br />", $it->title);
   		$i++;
   
   	}
   	$this->view->menu = $menu;
   	

   	$footerSubItems = $link->getLevelArticles("L.active = 1 AND L.parentID <> 0 AND L.isEshopCategory = 0 AND L.inFooter = 1");
   	$footerSubMenu = array();
   	
   	$this->setTranslates($linkLnag, "linkID", $footerSubItems);
   	
   	foreach($footerSubItems as $itm){
   		$std            = new StdClass();
   		$std->linkID    = $itm->linkID;
   		$std->title     = $itm->title;
   		$std->inFooter  = $itm->inFooter;
   		$std->niceTitle = $this->implodeUrlLink($itm,$link);
   			
   		$std->class     = "";
   		if(!empty($subLinkContent) && $itm->linkID == $subLinkContent->linkID)$std->class = "selected";
   			
   		if($itm->otherLink)$std->niceTitle = $itm->otherLink;
   		else $std->niceTitle =  "/".$this->wholeLangData->suffix.$std->niceTitle;
   		
   		$footerSubMenu[$itm->parentID][] = $std;
   	}
   	
   	$this->view->footerSubLinks = $footerSubMenu;
   	
   	$this->setTranslates($linkLnag, "linkID", $footerMenu);

   	foreach($footerMenu as $it){
   
   		$linkUrl = $this->implodeUrlLink($it,$link);
   
   		$it->userAdd = "";
   		$title = explode(" ",$it->title);
   		if(count($title) == 1)$it->userAdd = "one-row";
   			
   		if($count == $i)$it->userAdd .= " last";
   
   		if(!$it->otherLink)$it->otherLink = "/".$this->wholeLangData->suffix.$linkUrl;
   
   		if(!empty($linkContent) && $linkContent->linkID == $it->linkID)$it->userAdd .= " selected";
   
   		//$it->title = str_replace(" ","<br />", $it->title);
   		$i++;
   
   	}
   	$this->view->footerMenu = $footerMenu;
   	
   }
   
   protected function setAllMenuLinks($linkContent){
   
	   	$link = new Content_Models_Link();
	   	$mainLinks = $link->getAllItems("parentID = '0' AND active = 1",'priority');
	   	$subLinks  = $link->getAllItems("parentID <> '0' AND active = 1",'priority');
	   
	   	$this->subLinksArr = array();
	   	$this->productLink = "";
	   
	   	foreach($subLinks as $val){
	   		$this->subLinksArr[$val->parentID][] = $val;
	   	}
	   
	   	$linkNiceTitle = "";
	   	$this->recurseLinksForAllMenuList($mainLinks,0,$linkNiceTitle,$linkContent);
	   
	   	$this->view->allMenuLink = $this->productLink;
   }
   
   private function recurseLinksForAllMenuList($children,$recurseLevel,$linkNiceTitle,$linkContent){
	   	//prochazime postupne od korene a zanorujeme se do childu
	   	$mainLink = "";
	   	if($recurseLevel == 0){
	   		$ulMargin = 0;
	   		$mainLink = 'main-link';
	   	}else{
	   		$ulMargin = 5;
	   	}
	   
	   	$this->productLink .= '<ul>';
	   
	   	foreach($children as $child){
	   		 
	   		$selected = "";
	   		if($linkContent && $linkContent->linkID == $child->linkID)$selected = "selected";

	   		$linkNiceTitleSlash = "";
	   		if(!empty($linkNiceTitle))$linkNiceTitleSlash = "/";
	   		
	   		$linkNiceTitle2 = $linkNiceTitle.$linkNiceTitleSlash.$this->translate->translate("link".$child->linkID."niceTitle");
	   		 
	   		$this->productLink .= '<li class="'.$mainLink.'" id="'.$child->linkID.'">';
	   		$this->productLink .= ' <a class="'.$mainLink.' '.$selected.'" href="/'.$linkNiceTitle2.'">'.$this->translate->translate("link".$child->linkID."title").'</a>';
	   
	   		if(!empty($this->subLinksArr[$child->linkID]))
	   		$this->recurseLinksForAllMenuList($this->subLinksArr[$child->linkID],$recurseLevel+1,$linkNiceTitle2,$linkContent);
	   
	   		$this->productLink .= "</li>";
	   	}
	   
	   	$this->productLink .= '</ul>';
   }
   
   protected function implodeUrlLink($it,$link){
   	 
   	$this->urlLink = array();
   	 
   	$this->urlLink[] = $it->niceTitle;
   	 
   	$this->setUrlLink($it->parentID,$link);  //vytvori cestu k dane podkategorii pro strankovani
   	 
   	$link = "/".implode("/",$this->urlLink);
   	 
   	return $link;
   }
   
   protected function setUrlLink($parentID,$link){
   	 
   	if($parentID){
   		
   		$lang = new Zend_Session_Namespace("lang");
   		
   		$linkLang = new Content_Models_LinkLang();
   		$parentLink = $linkLang->getLinkdata("LL.linkID = '$parentID' AND LL.lang = '$lang->lang'");
   		
   		array_unshift($this->urlLink, $parentLink->niceTitle);
   
   		$this->setUrlLink($parentLink->parentID,$link);
   	}
   	 
   }
   
   protected function getUrlLinkID($filter){
   	$link = $filter->filter(addslashes($this->_request->getParam("link")));
   	return $this->getLinkID($link,false);
   	 
   }
   protected function getUrlSubLinkID($filter,$linkID,$type){
   	 
	   	$subLinkNiceTitle = $filter->filter(addslashes($this->_request->getParam($type)));

		if(!empty($subLinkNiceTitle)){
		   	$link 			= new Content_Models_Link();
		   	$linkLang 		= new Content_Models_LinkLang();
		   	$articleLang	= new Content_Models_ArticleLang();
		   	
			$lang 		= new Zend_Session_Namespace("lang");		
		   	 
		   	$allLinksArr 	= array();
		   	$allLinks 		= $linkLang->getAllItems("lang = '$lang->lang' AND niceTitle = '$subLinkNiceTitle'","linkID");
		   	
		   	foreach ($allLinks as $l){
		   		$allLinksArr[] = $l->linkID;
		   	}
		   	if(!empty($allLinksArr)){

		   		$allLinksIn = implode(",", $allLinksArr);
		   		$linkData = $link->getOneRow("linkID IN ($allLinksIn) AND parentID = '$linkID'");
		   	
			   	if(empty($linkData)){
			   		$articleData = $articleLang->getOneRow("articleID IN ($allLinksIn) AND linkID = '$linkID'");
			   		if(!empty($articleData)){
			   			return $articleData->articleID;
			   		}else{
			   			return null;
			   		}
			   	}else{
			   		return $linkData->linkID;
			   	}
		   	}else{
		   		return $subLinkNiceTitle;
		   	}
		}else{
   			return null;
   		}
	   		
   }
   
   Protected function getUrlSubSubLinkID($filter,$linkID){
   	 
	   	$link = new Content_Models_Link();
	   	$subLinkNiceTitle = $filter->filter(addslashes($this->_request->getParam("subsublink")));
	   	 
	   	if($this->modulesData->jazykoveMutace){
	   
	   		$locale 			= $this->translate->getLocale();
	   		$this->translate->setLocale('cz');
	   		$subLinkNiceTitle 	= $this->translate->translate($subLinkNiceTitle);
	   		$this->translate->setLocale($locale);
	   		 
	   		return $link->getOneRow("niceTitle = '$subLinkNiceTitle' AND parentID = $linkID")->linkID;
	   		 
	   	}else{
	   		if($subLinkNiceTitle){
	   			$expl = explode("-",$subLinkNiceTitle);
	   			if(count($expl) > 1 && is_numeric($expl[0])){
	   				return $subLinkNiceTitle;
	   			}else{
	   				return $link->getOneRow("niceTitle = '$subLinkNiceTitle' AND parentID = $linkID")->linkID;
	   			}
	   		}else{
	   			return null;
	   		}
	   	}
   }
   
   Protected function getLinkID($linkParam,$sub){

	   	$linkLang 		= new Content_Models_LinkLang();
	   	$articleLang	= new Content_Models_ArticleLang();

	   	if(!empty($linkParam)){
	   		
			$lang 		= new Zend_Session_Namespace("lang");		      
		   	$linkData 	= $linkLang->getLinkdata("LL.lang = '$lang->lang' AND LL.niceTitle = '$linkParam'","parentID = 0");
		   
		   	if(empty($linkData)){
		   		$articleData = $articleLang->getOneRow("lang = '$lang->lang' AND niceTitle = '$linkParam'");
		   		if(!empty($articleData)){
		   			return $articleData->articleID;
		   		}else{
			   		return null;
			   	}
		   	}else{
		   		return $linkData->linkID;
		   	}
	   	}else{
	   		return null;
	   	}

   }
   
   protected function setBreadCrumbData($link,$subLink = "",$subSubLink = ""){
   	    	 
   	$list = array(
   	$link->title    => $link->niceTitle
   	);
   	if($subLink){
   		$list = array(
   		$link->title    => $link->niceTitle,
   		$subLink->title => $link->niceTitle . "/" . $subLink->niceTitle
   		);
   
   	}
   	if($subLink && $subSubLink){
   		$list = array(
   		$link->title    => $link->niceTitle,
   		$subLink->title => $link->niceTitle . "/" . $subLink->niceTitle,
   		$subSubLink->title => $link->niceTitle . "/" . $subLink->niceTitle . "/" . $subSubLink->niceTitle
   		);
   
   	}
   	 
   	$this->setBreadCrumb($list);
   	 
   }

   protected function getSlider(){
   
   	$slider = new Slider_Models_Slider();
   	$slider = $slider->getSliders(null,"priority");
   	foreach($slider as $sl){
   		if($this->modulesData->jazykoveMutace)$sl->text = $this->translate->translate("slider".$sl->sliderID."text");
   	}
   	$this->view->sliders = $slider;
   
   }
   
   protected function getRecomendedProducts(){
   	 
   	$articles = new Content_Models_Article();
   	$this->view->recommended = $articles->getRecomended();
   }
}
