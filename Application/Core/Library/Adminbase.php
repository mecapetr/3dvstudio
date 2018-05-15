<?php
class Library_Adminbase extends Library_WholeWeb 
{
   
   	var $auth;
	var $user;
	var $baseUrl;
	
	//defaultni nastaveni pro admina
	function setDefault()
    {    
		parent::init();
		//zjistime jestli jsou nove komentare k aktualitam
		//$this->getNewActualityComment();
		
	    $baseUrl = $this->_request->getBaseUrl();
    	$this->view->baseUrl = $baseUrl;

    	$this->view->headerDomain = "www.domena.cz";
    		
    	$this->view->adminTitle = "Admin";
		$this->auth = Zend_Auth::getInstance();
    
		$this->checkNoLogoutToken();
		
		//zjisti jaky uzivatel je zrovna prihlaseny
	    $this->user = $this->auth->getStorage()->read()->login;		    
	    $this->view->user = $this->user;
	    
	    $this->adminUserID = $this->auth->getStorage()->read()->adminUserID;		    
	    $this->view->adminUserID = $this->adminUserID;
	    	
		//nastavi layout a placeholdery
		Zend_Layout::getMvcInstance()->setLayout("admin");

		//nastaví admin menu
		$this->setHeaderLinks();
				
		//nastavi ze vsechny prvky v seznamu dané sekce se nebudou moct dát pohybovat
		$this->view->sortableList = false;

				
    }
    
    function checkNoLogoutToken(){

    	$this->auth = Zend_Auth::getInstance();
    	if(!$this->auth->hasIdentity() ||  empty($this->auth->getStorage()->read()->adminUserID)){
    	
    		$adminUser = new User_Models_AdminUser();
    		if(isset($_COOKIE['noLCT'])){
    	
    			$noLogoutConfirmToken	= stripslashes($_COOKIE['noLCT']);   	
    	
    				$userData = $adminUser->getOneRow("noLogoutToken = '$noLogoutConfirmToken'");
    				
    				if(!empty($userData)){
    					$scripts 	= new Library_Scripts();
    					
	    				$db = Zend_Registry::get('db');
	    				$authAdapter = new Zend_Auth_Adapter_DbTable($db);
	    				$authAdapter->setTableName('admin_user');
	    				$authAdapter->setIdentityColumn('login');
	    				$authAdapter->setCredentialColumn('password');
	    				$authAdapter->setIdentity($userData->login);
	    				$authAdapter->setCredential($userData->password);
	    				$result = $this->auth->authenticate($authAdapter);
	    				$data = $authAdapter->getResultRowObject(null,array('password'));
	    	
	    				$this->auth->getStorage()->write($data);
	    				

	    				$noLCT 		= $scripts->generatePassword(200);
	    				$adminUser->updateData(array(
	    						"noLogoutToken" => $noLCT
	    				), "adminUserID = $data->adminUserID");
	    				setcookie("noLCT",$noLCT, time() + 86400,"/admin/");
	    				
    				}else{
		    			$this->_redirect("/admin");
		    		}
    		}else{
    			$this->_redirect("/admin");
    		}
    	}
    }
    function getNewArticleComment(){
    	$actualityComment = new Content_Models_ArticleComment();
    	$this->view->newActualityComments = $actualityComment->getCount("showComment = 0");
    }
    function addPlaceholders(){
    	
		$this->_response->insert('header',      $this->view->render('placeholders/header.phtml'));
		$this->_response->insert('headerLinks', $this->view->render('placeholders/headerLinks.phtml'));
		$this->_response->insert('headerSubLinks',   $this->view->render('placeholders/headerSubLinks.phtml'));
    }
    function order($descAsc,$controller,$table,$totaly,$where = null,$order = "date DESC",$action = "seznam"){
    	
    	$onPage = 20;
    	
        if(!$this->_request->getPost('order')){
			
        	$path                  = "/admin/".$controller."/".$action ;		    
			$page                  = $this->_request->getParam('strana');	
							
			$allItems              = $this->setPagination($page,$path,$totaly,$table,$onPage,$order,$where);  		
			$this->view->allItems  = $allItems;
			
		}	
		if($this->_request->getPost('order') || !empty($descAsc)){
				
			$orderBy  = $this->_request->getPost('orderBy');
			$descAsc  = $this->_request->getPost('descAsc');
			
			if($this->_request->getParam('poradi')){
				$orderBy            = $this->_request->getParam('seradit');
			    $descAsc            = $this->_request->getParam('poradi');
			}
			
		    $this->view->orderBy = $orderBy;
			$this->view->descAsc = $descAsc;

			$path                  = "/admin/".$controller."/".$action."/seradit/".$orderBy."/poradi/".$descAsc ;		    
			$page                  = $this->_request->getParam('strana');	
			$order				   = $orderBy." ".$descAsc;
			
			$allItems              = $this->setPagination($page,$path,$totaly,$table,$onPage,$order,$where);  		
			$this->view->allItems  = $allItems;

		}
    	
    }
    
    /**
     * Funkce pro strankovani v adminu
     *
     * @param unknown_type $page    aktualni stranka
     * @param unknown_type $path    
     * @param unknown_type $totaly  celkovy pocet zaznamu v tabulce 
     * @param unknown_type $dotaz2  
     * @param unknown_type $onPage  pocet zaznamu na strance
     * @return unknown
     */
	public function setPagination($page,$path,$totaly,$table,$onPage,$order,$where){
		
	    if (!isset($page)){
			$page=1; //pokud nen� $zobrazena_strana nastavena, nastav�me ji na hodnotu 1
	    }
								
		$pagesNumber = ceil($totaly/$onPage);
		$limit1=(($page-1)*$onPage);
		$limit2=($onPage);
		$archiv = $table->fetchAll($where,$order,$limit2,$limit1);
		$this->nextPrevious($page, $pagesNumber, $path);
		
		return $archiv;
		
	}
	
	function nextPrevious($page, $pagesNumber, $path)
	{
		If (($page==1) && ($page==$pagesNumber)){ 
			
			return ""; 
			
		}elseif (($page!=1) && ($page==$pagesNumber)){ 
		
			$this->view->nextPrevious = "<div class=\"nextPrevious left\"><a href=\"".$path."/strana/".($page-1)."\"><< Předchozí</a></div>";
		
		}elseif(($page!=$pagesNumber) && ($page>1)){ 
		
			
		$this->view->nextPrevious ="<div class=\"nextPrevious center\"><a href=\"".$path."/strana/".($page-1)."\"><< Předchozí</a><span class=\"betweenNextPrevious\">&nbsp;</span><a href=\"".$path."/strana/".($page+1)."\">Další >></a></div>";
		
		}elseif (($page==1) && ($pagesNumber > 1)){
		
			$this->view->nextPrevious = "<div class=\"nextPrevious\"><a href=\"".$path."/strana/".($page+1)."\">Další >></a></div>";
		
		}else
		return "";
	}
	
	private function setHeaderLinks(){
        
	    $adminLinks = new Install_Models_AdminLink();

	    $mainLinks = $adminLinks->getMainLinks();
	    $subLinks  = $adminLinks->getAllItems("parentID <> '0' AND active = 1",'priority');
	    
	    $subLinkList = array();
	    foreach($subLinks as $sl){
	    	$subLinkList[$sl->parentID][] = $sl;
	    }
	   
	    foreach($mainLinks as $ml){
	    		    	
	    	if(!empty($subLinkList[$ml->linkID]))$ml->subLinks = $subLinkList[$ml->linkID];
	    }
	    
        $this->view->adminMenu = $mainLinks;
        
    }
	protected function updateOldPhoto($id,$table,$tableID){
	
		$language 	  = new Models_Language_Language();
		$mainLangData = $language->getMainLang();
		
    	$photo          = new Models_Photo();
    	//upravime description u fotek, ktere jiz byly pridany
    	$allUploadedPhotos = $photo->getAllPhotos($id,$table,$tableID);
    	if(count($allUploadedPhotos)>0){
	    	foreach($allUploadedPhotos as $item){
	    		
	    		$photoDesc = $this->_request->getPost("$item->photoID-e-photoDesc-".$mainLangData->suffix);
    			//vybereme popis fotky a vlozime do slovnikovych souboru jednotlive preklady 
    			$photo->updateData(array("description" => $photoDesc),"photoID = '$item->photoID'");	// aktualizujeme description u fotky
    			
	    		if($this->modulesData->jazykoveMutace){    				
	    			//vybereme popis fotky a vlozime do slovnikovych souboru jednotlive preklady 
		    		$photoData = new stdClass();  			
		    		$photoData->inputName = "$item->photoID-e-photoDesc";
		    		$photoData->photoID = $item->photoID;	
    				$this->updateDictionary('edit','photo','photoID',$item->photoID,$photoData);
    			}
	    		
	    	}    		
    	}
	}
    protected function addPhoto($id,$table,$connectTable,$tableID,$folder,$newFolder){
    
    	
    	$photo          = new Models_Photo();
    	$script         = new Library_Scripts();
    	
    	$language 	  = new Models_Language_Language();
    	$mainLangData = $language->getMainLang();
    	    	
    	//pridani docasnych fotek
    	$photos 			= $this->_request->getPost("photos");
    	$mainPhotoID 		= $this->_request->getPost("mainPhoto");
    	$arrayForPriority 	= array();
    	
    	if(!empty($mainPhotoID)){
    		$mainPhotoID = explode("-",$mainPhotoID);
    		if($mainPhotoID[1] == "e")$photo->updateMainStatus($mainPhotoID[0]); // pokud je oznacena hlavni fotka jiz ulozena nastavime ji jako hlavni
    	}
    	if(count($photos)>0){
    		foreach($photos as $item){
   
    			$item    = explode("~",$item);
    			$newItem = $item[0];
    			if(file_exists("./Public/Images/".$newFolder."/".$item[0])){
    				$newItem = rand(0,999)."-".$item[0];
    			}
    
    			//zkontrolujeme jestli je fotka oznacena jako hlavni
    			$tempPhotoID = explode("-",$item[3]);
    			$isMainPhoto = 0;
    			if($mainPhotoID[0] == $tempPhotoID[0])$isMainPhoto = 1;
    			
    			//vybereme popis fotky
    			$photoData = new stdClass();  
    			$photoData->inputName =	"$tempPhotoID[0]-t-photoDesc";
    			$photoDesc = $this->_request->getPost("$tempPhotoID[0]-t-photoDesc-".$mainLangData->suffix);
    			
    			$data  = array("title"=>$newItem,"description"=>$photoDesc,"width"=>$item[1],"height"=>$item[2],"mainPhoto"=>$isMainPhoto,"priority" => 2147483647);
    			$photo->insertData($data);
    			$photoID = $photoData->photoID = $photo->lastID;    			
    			
    			
    			//vlozime do prekladove tabulky pokud je zaply modul
    			if($this->modulesData->jazykoveMutace){
    				$this->updateDictionary('add','photo','photoID',$photoID,$photoData);
    			}
    			
    			$dataPA  = array("photoID"=>$photoID,"".$tableID.""=>$id);
    			$connectTable->insertData($dataPA);
    
    			rename("./Public/Images/Temp/".$item[0],"./Public/Images/".$newFolder."/".$newItem);
    			rename("./Public/Images/Temp/mala-".$item[0],"./Public/Images/".$newFolder."/mala-".$newItem);
    			rename("./Public/Images/Temp/stredni-".$item[0],"./Public/Images/".$newFolder."/stredni-".$newItem);
    
    		}
    		$allPhotos = $photo->getAllPhotos($id,$table,$tableID);
			$script->updatePriority($allPhotos, $photo,"photoID");
    	}
    	$where = "userID='$this->adminUserID' AND tableType='$table'";
    	$photo->deleteTableData("photo_temp",$where);
    	
    	
    }
    
    protected function updateOldVideo($id,$table,$tableID){
    
    	$video          = new Models_Video();
    	$scripts 		= new Library_Scripts();
    	
    	$language 	  = new Models_Language_Language();
    	$mainLangData = $language->getMainLang();
    	
    	//upravime description u fotek, ktere jiz byly pridany
    	$allUploadedVideos = $video->getAllVideos($id,$table,$tableID);
    	if(count($allUploadedVideos)>0){
	    	foreach($allUploadedVideos as $item){
	    		
	    		$videoDesc 	= $this->_request->getPost("$item->videoID-e-videoDesc-".$mainLangData->suffix);
	    		$videoTitle = $this->_request->getPost("$item->videoID-e-videoTitle-".$mainLangData->suffix);
    			$niceTitle  = $scripts->url($videoTitle);    			
    			 
    			$video->updateData(array("anotation" => $videoDesc,"title" => $videoTitle, "niceTitle" => $niceTitle),"videoID = '$item->videoID'");	// aktualizujeme description u fotky
    			
    			if($this->modulesData->jazykoveMutace){
	    				
		    		//vybereme popis fotky a vlozime do slovnikovych souboru jednotlive preklady 
		    		$videoData 							= new stdClass();  			
		    		$videoData->inputNameDescription 	= "$item->videoID-e-videoDesc";
		    		$videoData->inputNameTitle 			= "$item->videoID-e-videoTitle";
		    		$videoData->videoID 				=  $item->videoID;
    				$this->updateDictionary('edit','video','vidoID',$item->videoID,$videoData);
    			}	    	
	    		
	    	}    		
    	}
    }
    protected function addVideo($id,$table,$connectTable,$tableID,$newFolder){
    
    	$video   = new Models_Video();
    	$scripts = new Library_Scripts();
    	
    	$language 	  = new Models_Language_Language();
    	$mainLangData = $language->getMainLang();
    	 
    	$videos    = $this->_request->getPost("videos");
    
    	if(count($videos)>0){
    			
    		$title     = $this->_request->getPost("video-title");
    		$anotation = $this->_request->getPost("video-description");
    		$i         = 0;
    		foreach($videos as $item){
    
    			$item    = explode("~",$item);
    			$newItem = $item[0];
    			if(file_exists("./Public/Videos/".$newFolder."/".$item[0])){
    				$newItem = rand(0,999)."-".$item[0];
    			}
    
    			$tempVideoID = explode("-",$item[3]);
    			
    			//vybereme popis fotky
    			$videoData 							= new stdClass();  
    			$videoData->inputNameDescription 	=	"$tempVideoID[0]-t-videoDesc";
    			$videoData->inputNameTitle 			=	"$tempVideoID[0]-t-videoTitle";
    			$videoDesc 							= $this->_request->getPost("$tempVideoID[0]-t-videoDesc-".$mainLangData->suffix);
    			$videoTitle 						= $this->_request->getPost("$tempVideoID[0]-t-videoTitle-".$mainLangData->suffix);
    			
    			
    			$niceTitle   = $scripts->url($videoTitle);
    			$data  = array(
    					"title"     => $videoTitle,
    					"niceTitle" => $niceTitle,
    					"anotation" => $videoDesc,
    					"file"      => $newItem,
    					"footage"   => $item[1],
    					"fileSize"  => $item[2],
    					"userAdd"   => $this->user,
    					"userEdit"  => "ještě nebylo editováno",
    			    	"dateAdd"   => date("Y-m-d H:i",Time()),
    			    	"dateEdit"  => "0000-00-00 00:00:00"
    					
    			);
    			$video->insertData($data);
    			$videoID = $video->lastID;
    			
    			//doplnime $videoData o videoID a vlozime do slovniku
    			if($this->modulesData->jazykoveMutace){
	    			$videoData->videoID = $videoID;    
    				$this->updateDictionary('add','video','vidoID',$videoID,$videoData);
    			}
	    			
    			$dataPA  = array("videoID"=>$videoID,"".$tableID.""=>$id);
    			$connectTable->insertData($dataPA);
    
    			rename("./Public/Videos/Temp/".$item[0].".mp4","./Public/Videos/".$newFolder."/".$newItem.".mp4");
    			rename("./Public/Images/Previews/Temp/".$item[0].".png","./Public/Images/Previews/".$newFolder."/".$newItem.".png");
    			rename("./Public/Images/Previews/Temp/mala-".$item[0].".png","./Public/Images/Previews/".$newFolder."/mala-".$newItem.".png");
    
    			$i++;
    		}
    			
    		$where = "userID='$this->adminUserID' AND tableType='$table'";
    		$video->deleteTableData("video_temp",$where);
    	}
    
    
    }
    
    protected function getVideoTypeCode($videoURL){

    	$type = $code = "";
    	if(strpos($videoURL,"youtube")){
    
    		//pokud parsujeme youtube adresu
    		$type = "youtube";
    		$parsedToCode = explode("v=",$videoURL);
    		$parsedToCode = explode("&",$parsedToCode[1]);
    		$code = $parsedToCode[0];
    
    	}else if(strpos($videoURL,"vimeo")){
    
    		//pokud parsujeme vimeo
    		$type = "vimeo";
    		$parsedToCode = explode("/",$videoURL);
    		$code = end($parsedToCode);
    	}
    	 
    	return array($type,$code);
    }
    protected function updateOldFile($id,$table,$tableID){
    	
    	$file         = new Models_File();
    	$language 	  = new Models_Language_Language();
    	$mainLangData = $language->getMainLang();
    	
    	//upravime description u souboru, ktere jiz byly pridany
    	$allUploadedFiles = $file->getAllFiles($id,$table,$tableID);
    	if(count($allUploadedFiles)>0){
	    	foreach($allUploadedFiles as $item){
	    		
	    		$fileDesc = $this->_request->getPost("$item->fileID-e-fileDesc-".$mainLangData->suffix);
    			//vybereme popis fotky
    			$file->updateData(array("description" => $fileDesc),"fileID = '$item->fileID'");	// aktualizujeme description u souboru
    			
    			if($this->modulesData->jazykoveMutace){	
	    				
		    		//vybereme popis fotky a vlozime do slovnikovych souboru jednotlive preklady 
		    		$fileData = new stdClass();  			
		    		$fileData->inputName = "$item->fileID-e-fileDesc";
		    		$fileData->fileID = $item->fileID;
    				$this->updateDictionary('edit','file','fileID',$item->fileID,$fileData);
    			}
	    	
	    		
	    	}    		
    	}
    }
    protected function addFile($id,$table,$connectTable,$tableID,$newFolder){
    
    	$file = new Models_File();  

    	$language 	  = new Models_Language_Language();
    	$mainLangData = $language->getMainLang();
    	
    	//pridani docasnych souboru
    	$files = $this->_request->getPost("files");
    
    	if(count($files)>0){
    		foreach($files as $item){
    
    			$item    		= explode("~",$item);
    			$newItem 		= $item[0];
    			$isFromSource	= $item[3];
    			if(!$isFromSource && file_exists("./Public/Files/".$newFolder."/".$item[0])){
    				$newItem = rand(0,999)."-".$item[0];
    			}
    			
    			//vybereme popis fotky a vlozime do slovnikovych souboru jednotlive preklady 
    			$tempFileID = explode("-",$item[1]);
    			$fileData = new stdClass();  
    			$fileData->inputName =	"$tempFileID[0]-t-fileDesc";
    			$fileDesc = $this->_request->getPost("$tempFileID[0]-t-fileDesc-".$mainLangData->suffix);
    			
    			if($isFromSource)
    				$size = filesize("./Public/Files/".$newFolder."/".$item[0]);    				
    			else
    				$size = filesize("./Public/Files/Temp/".$item[0]);
    				
    			$data  = array("title"=>$newItem,"description" => $fileDesc,"size"=>$size,"ico"=>$item[2]);
    			$file->insertData($data);
    			$fileID = $file->lastID;
    			
    			//jeste pridame ID do $fileData a aktualizujeme slovnik
    			if($this->modulesData->jazykoveMutace){
    				$fileData->fileID = $fileID;    
    				$this->updateDictionary('add','file','fileID',$fileID,$fileData);
    			}
    			
    			$dataPA  = array("fileID"=>$fileID,"".$tableID.""=>$id);
    			$connectTable->insertData($dataPA);
    			if(!$isFromSource)    
    				rename("./Public/Files/Temp/".$item[0],"./Public/Files/".$newFolder."/".$newItem);
    
    		}
    	}
    	$where = "userID='$this->adminUserID' AND tableType='$table'";
    	$file->deleteTableData("file_temp",$where);
    
    }

    protected function setPhotosData($photoTable,$id,$table,$tableID,$user){
    	
    	$allItems				= new stdClass();
	    $allDBItems 			= $photoTable->getAllPhotos($id,$table,$tableID);
	    
		foreach ($allDBItems as $value){
			
			if($value->mainPhoto == 1){
				setcookie("mainPhoto",$value->photoID."-e",NULL,"/"); 
				$this->view->mainPhoto =  $value->photoID."-e";
			}
			if($this->modulesData->jazykoveMutace){
				
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
			}else{
					//vlozime do pole primo data z DB
					$allItems->description['cz']			= $value->description;
			}

			$value->description 	= $allItems->description;
	    	$value->description2 	= $allItems->description2;
			
		}				
		$this->view->allPhotos 		= $this->allPhotos 		= $allDBItems ;	
		$this->view->allTempPhotos	= $this->allTempPhotos 	= $photoTable->getTempPhotos($user,$table);		
    }

    protected function setFilesData($fileTable,$id,$table,$tableID,$user){
    	
		$translate 		 		= Zend_Registry::get('Zend_Translate');
    	$allItems				= new stdClass();
	    $allDBItems 			= $fileTable->getAllFiles($id,$table,$tableID);
	    
		foreach ($allDBItems as $value){		
			
			if($this->modulesData->jazykoveMutace){	
				
				$fileLangDb			= new  Models_FileLang();
				$allTranslates 			= $fileLangDb->getAllItems("fileID = $value->fileID","lang");
				$allTranslatesArr		= array();
				foreach ($allTranslates as $val){
					$allTranslatesArr[$val->lang] = $val;
				}
				
				//nastavime vsechny jazyky
			    //jazyky vzdy prelozime a ulozime do pole
			    foreach($this->allLanguageMutations as $val){
			    	(isset($allTranslatesArr[$val->suffix])) ?  $allItems->description[$val->suffix] = $allTranslatesArr[$val->suffix]->description : $allItems->description[$val->suffix] = "";
			    }
			}else{
					//vlozime do pole primo data z DB
					$allItems->description['cz']			= $value->description;
			}
		    
	    	$value->description = $allItems->description;
			
		}			
		$this->view->allFiles 		= $this->allFiles 		= $allDBItems ;	
		$this->view->allTempFiles	= $this->allTempFiles 	= $fileTable->getTempFiles($user,$table);		
    }
    protected function setVideosData($videoTable,$id,$table,$tableID,$user){
    	
		$translate 		 		= Zend_Registry::get('Zend_Translate');
    	$allItems				= new stdClass();
	    $allDBItems 			= $videoTable->getAllVideos($id,$table,$tableID);
	    
		foreach ($allDBItems as $value){
			
			if($this->modulesData->jazykoveMutace){					

				$videoLangDb			= new  Models_VideoLang();
				$allTranslates 			= $videoLangDb->getAllItems("videoID = $value->videoID","lang");
				$allTranslatesArr		= array();
				foreach ($allTranslates as $val){
					$allTranslatesArr[$val->lang] = $val;
				}
				
				//nastavime vsechny jazyky
			    //jazyky vzdy prelozime a ulozime do pole
			    foreach($this->allLanguageMutations as $val){	
			    	(isset($allTranslatesArr[$val->suffix])) ?  $allItems->description[$val->suffix] 	= $allTranslatesArr[$val->suffix]->description 	: $allItems->description[$val->suffix] = "";
			    	(isset($allTranslatesArr[$val->suffix])) ?  $allItems->title[$val->suffix] 			= $allTranslatesArr[$val->suffix]->title 		: $allItems->title[$val->suffix] = "";
			    }
			}else{
					//vlozime do pole primo data z DB
					$allItems->description['cz']			= $value->anotation;
					$allItems->title['cz']					= $value->title;
			}
		    
	    	$value->anotation 	= $allItems->description;
	    	$value->title 		= $allItems->title;
			
		}			
		$this->view->allVideos 		= $this->allVideos 		= $allDBItems ;	
		$this->view->allTempVideos	= $this->allTempVideos 	= $videoTable->getTempvideos($user,$table);		
    }
    protected function updateDictionary($operationType,$table_name,$tableParamIDName,$tableRowID,$otherArgs = null,$elmIndex = null){
    	$allLanguages = array();
    	$obj	= $this;
    	$language = new Models_Language_Language(); 
    	
		$this->allLanguageMutations = $language->getDbLanguages();
	     //nastavime vsechny jazykove verze
		$translate 		 		= Zend_Registry::get('Zend_Translate');		
		$langData				= array();
		foreach($this->allLanguageMutations as $val){
			
			//pokud data mazeme
			if($table_name == 'article'){
				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"articleID" 	=> 	$tableRowID,
							"lang"			=>	$val->suffix,
							"title"			=>	$obj->title[$val->suffix],
							"niceTitle"		=>	$obj->niceTitle[$val->suffix],
							"anotation"		=>	$obj->anotation[$val->suffix],
							"text"			=>	$obj->text[$val->suffix],
							"formTitle"		=>	$obj->formTitle[$val->suffix],
							"metaTitle"		=>	$obj->metaTitle[$val->suffix],
							"keywords"		=>	$obj->keywords[$val->suffix],
							"description"	=>	$obj->description[$val->suffix]
					);
							
				}
				$language->setTableObject(new Content_Models_ArticleLang());
				
			}else if($table_name == 'link'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"linkID" 		=> 	$tableRowID,
		    			"lang"			=>	$val->suffix,
		    			"title"			=>	$obj->title[$val->suffix],
		    			"niceTitle"		=>	$obj->niceTitle[$val->suffix],
		    			"metaTitle"		=>	$obj->metaTitle[$val->suffix],
		    			"keywords"		=>	$obj->keywords[$val->suffix],
		    			"description"	=>	$obj->description[$val->suffix],
		    			"text"			=>	$obj->text[$val->suffix],
		    			"oldUrl"		=>	$obj->oldUrl[$val->suffix]
		    		);
					if(isset($obj->allowEditOtherLink) && !empty($obj->allowEditOtherLink))
						$langData[$val->suffix]["otherLink"] 		 = $obj->otherLink[$val->suffix];
					
	    		}
	    		$language->setTableObject(new Content_Models_LinkLang());
	    	}else if($table_name == 'product'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"productID" 	=> 	$tableRowID,
		    			"lang"			=>	$val->suffix,
		    			"title"			=>	$obj->title[$val->suffix],
		    			"niceTitle"		=>	$obj->niceTitle[$val->suffix],
		    			"metaTitle"		=>	$obj->metaTitle[$val->suffix],
		    			"keywords"		=>	$obj->keywords[$val->suffix],
		    			"description"	=>	$obj->description[$val->suffix],
		    			"text"			=>	$obj->text[$val->suffix],
		    			"deliveryTime"	=>	$obj->deliveryTime[$val->suffix],
		    			"oldUrl"		=>	$obj->oldUrl[$val->suffix]
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_ProductLang());
	    	}else if($table_name == 'product-prices'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"productID" 	=> 	$tableRowID,
		    			"lang"			=>	$val->suffix,
		    			"price"			=>	$obj->price[$val->suffix],
		    			"originalPrice"	=>	$obj->originalPrice[$val->suffix]
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_ProductLang());
	    	}else if($table_name == 'product-discount'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"productID" 	=> 	$tableRowID,
		    			"lang"			=>	$val->suffix,
		    			"discount"		=>	$obj->discount[$val->suffix],
		    			"deliveryText"	=>	$obj->deliveryText[$val->suffix]
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_ProductLang());
	    	}else if($table_name == 'product-heureka'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"productID" 	=> 	$tableRowID,
		    			"lang"			=>	$val->suffix,
		    			"hTitle"		=>	$obj->hTitle[$val->suffix],
		    			"hpTitle"		=>	$obj->hpTitle[$val->suffix],
		    			"hCat"			=>	$obj->hCat[$val->suffix],
		    			"hText"			=>	$obj->hText[$val->suffix]
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_ProductLang());
	    	}else if($table_name == 'product-zbozi'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"productID" 	=> 	$tableRowID,
		    			"lang"			=>	$val->suffix,
		    			"zCat"			=>	$obj->zCat[$val->suffix],
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_ProductLang());
	    	}else if($table_name == 'product-auction'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"productID" 	=> 	$tableRowID,
		    			"lang"			=>	$val->suffix,
		    			"textAuction"	=>	$obj->textAuction[$val->suffix],
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_ProductLang());
	    	}else if($table_name == 'product-size'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"productID" 	=> 	$tableRowID,
		    			"lang"			=>	$val->suffix,
		    			"textSize"		=>	$obj->textSize[$val->suffix],
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_ProductLang());
	    	}else if($table_name == 'product-category'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"productCategoryID" 	=> 	$tableRowID,
		    			"lang"					=>	$val->suffix,
		    			"title"					=>	$obj->title[$val->suffix],
		    			"text"					=>	$obj->text[$val->suffix]
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_ProductCategoryLang());
	    	}else if($table_name == 'eshop-product'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"eshopProductID" 		=> 	$tableRowID,
		    			"lang"					=>	$val->suffix,
		    			"title"					=>	$obj->title[$val->suffix],
		    			"text"					=>	$obj->text[$val->suffix],
		    			"price"					=>	$obj->price[$val->suffix]
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_EshopProductLang());
	    	}	   
	    	
	    	else if($table_name == 'cover-color'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"coverColorID" 	=> 	$tableRowID,
		    			"lang"			=>	$val->suffix,
		    			"title"			=>	$obj->title[$val->suffix],
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_CoverColorLang());
	    	}else if($table_name == 'cover-material'){		
	    		//pridani hlavnich textovych prvku jako title text anotace atd
	    		//zde $tableData = linkID
	    		if($operationType == "add" || $operationType == "edit"){
		    		$langData[$val->suffix] = array(
		    			"coverMaterialID" 	=> 	$tableRowID,
		    			"lang"				=>	$val->suffix,
		    			"title"				=>	$obj->title[$val->suffix],
		    		);					
	    		}
	    		$language->setTableObject(new Eshop_Models_CoverMaterialLang());
	    	}else if($table_name == 'link-section'){	
	    		
	    		if($operationType == "add" || $operationType == "edit"){
	    			$langData[$val->suffix] = array(
	    					"linkSectionID" => 	$tableRowID,
	    					"lang"			=>	$val->suffix,
	    					"name"			=>	$obj->secName["name-".$val->suffix],
	    					"niceName"		=>	$obj->secName["niceName-".$val->suffix]
	    			);
	    					
	    		}
	    		$language->setTableObject(new Content_Models_LinkSectionLang());
				
			}else if($table_name == 'category'){		
	    		

				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"linkSectionHeaderID" 	=> 	$tableRowID,
							"lang"					=>	$val->suffix,
							"title"					=>	$obj->title[$val->suffix],
							"niceTitle"				=>	$obj->niceTitle[$val->suffix]
					);
				
				}
				$language->setTableObject(new Content_Models_CategoryLang());
				
			}else if($table_name == 'link-section-header'){		

				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"linkSectionHeaderID" 	=> 	$tableRowID,
							"lang"					=>	$val->suffix,
							"titleH1"				=>	$obj->heData["h1-".$val->suffix][$elmIndex],
							"titleH2"				=>	$obj->heData["h2-".$val->suffix][$elmIndex]
					);
				
				}
				$language->setTableObject(new Content_Models_LinkSectionHeaderLang());
				
				
	    	}else if($table_name == 'link-section-map'){			

				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"linkSectionMapID" 		=> 	$tableRowID,
							"lang"					=>	$val->suffix,
							"title"					=>	$obj->mData["title-".$val->suffix][$elmIndex]
					);
				
				}
				$language->setTableObject(new Content_Models_LinkSectionMapLang());
				
	    	}else if($table_name == 'link-section-article'){		

	    		if($operationType == "add" || $operationType == "edit"){
	    			$langData[$val->suffix] = array(
	    					"linkSectionArticleID" 	=> 	$tableRowID,
	    					"lang"					=>	$val->suffix,
	    					"url"					=>	$obj->aeData["url-".$val->suffix]
	    			);
	    		
	    		}
	    		$language->setTableObject(new Content_Models_LinkSectionArticleLang());
				
			}else if($table_name == 'link-section-header-section'){		   		

				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"linkSectionHeaderSectionID" 	=> 	$tableRowID,
							"lang"							=>	$val->suffix,
							"title"							=>	$obj->sheData["h1-".$val->suffix],
							"titleH2"						=>	$obj->sheData["h2-".$val->suffix]
					);
					 
				}
				$language->setTableObject(new Content_Models_LinkSectionHeaderSectionLang());
				
				
	    	}else if($table_name == 'link-section-text'){		   			

				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"linkSectionTextID" 	=> 	$tableRowID,
							"lang"					=>	$val->suffix,
							"text"					=>	$obj->teData["text-".$val->suffix]
					);
				
				}
				$language->setTableObject(new Content_Models_LinkSectionTextLang());
				
	    	}else if($table_name == 'link-section-link'){			    						

				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"linkSectionLinkID" 	=> 	$tableRowID,
							"lang"					=>	$val->suffix,
							"text"					=>	$obj->leData["text-".$val->suffix][$elmIndex],
							"titleH2"				=>	$obj->leData["h2-".$val->suffix][$elmIndex],
							"url"					=>	$obj->leData["url-".$val->suffix][$elmIndex]
					);
				
				}
				$language->setTableObject(new Content_Models_LinkSectionLinkLang());
				
	    	}else if($table_name == 'link-section-form'){			    		
	    		
				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"linkSectionFormID" 	=> 	$tableRowID,
							"lang"					=>	$val->suffix,
							"title"					=>	$obj->fData["title-".$val->suffix][$elmIndex]
					);
				
				}
				$language->setTableObject(new Content_Models_LinkSectionFormLang());
				
	    	}else if($table_name == 'link-section-form-values'){			    					

				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"linkSectionFormValueID" 	=> 	$tableRowID,
							"lang"					=>	$val->suffix,
							"title"					=>	$obj->fvData["valueName-".$val->suffix][$elmIndex],
							"value"					=>	$obj->fvData["value-".$val->suffix][$elmIndex]
					);								   
				
				}
				$language->setTableObject(new Content_Models_LinkSectionFormValuesLang());
				
	    	}else if($table_name == 'slider'){						
				
				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"linkSectionFormValueID"=> 	$tableRowID,
							"lang"					=>	$val->suffix,
							"title"					=>	$obj->title[$val->suffix],
							"text"					=>	$obj->text[$val->suffix]
					);
				
				}
				$language->setTableObject(new Slider_Models_SliderLang());
				
			}else if($table_name == 'filter'){						
				
				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"filterID"	=> 	$tableRowID,
							"lang"		=>	$val->suffix,
							"title"		=>	$obj->title[$val->suffix]
					);
				
				}
				$language->setTableObject(new Eshop_Models_FilterLang());
				
			}else if($table_name == 'photo'){

				//pridani hlavnich textovych prvku jako title text anotace atd
				//zde $tableData = linkID
				if($operationType == "add" || $operationType == "edit"){

					$description	= "";
					if(isset($otherArgs->inputName)){
						$description 	= $this->_request->getPost($otherArgs->inputName."-".$val->suffix);
					}
					$description2	= "";
					if(isset($otherArgs->inputName2)){
						$description2 	= $this->_request->getPost($otherArgs->inputName2."-".$val->suffix);
					}
					$langData[$val->suffix] = array(
							"photoID" 		=> 	$tableRowID,
							"lang"			=>	$val->suffix,
							"description"	=>	$description,
							"description2"	=>	$description2
					);
							
				}
				$language->setTableObject(new Models_PhotoLang());
				
			}else if($table_name == 'file'){			

				//pridani hlavnich textovych prvku jako title text anotace atd
				//zde $tableData = linkID
				if($operationType == "add" || $operationType == "edit"){
					$description = $this->_request->getPost($otherArgs->inputName."-".$val->suffix);
					$langData[$val->suffix] = array(
							"fileID" 		=> 	$tableRowID,
							"lang"			=>	$val->suffix,
							"description"	=>	$description
					);
						
				}
				$language->setTableObject(new Models_FileLang());
				
			}else if($table_name == 'video'){		
				

				//pridani hlavnich textovych prvku jako title text anotace atd
				//zde $tableData = linkID
				if($operationType == "add" || $operationType == "edit"){					

					$description = $this->_request->getPost($otherArgs->inputNameDescription."-".$val->suffix);
					$title 		 = $this->_request->getPost($otherArgs->inputNameTitle."-".$val->suffix);
					
					$langData[$val->suffix] = array(
							"fileID" 		=> 	$tableRowID,
							"lang"			=>	$val->suffix,
							"description"	=>	$description,
							"title"			=>	$title
					);
				
				}
				$language->setTableObject(new Models_VideoLang());
				
			}else if($table_name == 'cover'){
				
				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"coverID" 		=> 	$tableRowID,
							"lang"			=>	$val->suffix,
							"title"			=>	$obj->title[$val->suffix],
							"text"			=>	$obj->text[$val->suffix]
					);
							
				}
				$language->setTableObject(new Eshop_Models_CoverLang());
				
			}else if($table_name == 'homepage-section'){
				
				if($operationType == "add" || $operationType == "edit"){
					$langData[$val->suffix] = array(
							"homepageSectionID" => 	$tableRowID,
							"lang"				=>	$val->suffix,
							"title"				=>	$obj->title[$val->suffix],
							"url"				=>	$obj->url[$val->suffix]
					);
							
				}
				$language->setTableObject(new Models_HomepageSectionLang());
				
			}	
	    	$allLanguages[] = $val->suffix;
		}    	
		
    	$language->setLanguages($allLanguages);               // nastavĂ­ vĹˇechny jazyky
    	$language->setData($langData);                        // nastavĂ­ pĹ™Ă­sluĹˇnĂˇ data jazykĹŻm
    	$language->setOperationType($operationType);          // nastavĂ­, jestli se bude do slovnĂ­ku pridavat nebo ubĂ­rat
    	$language->setTableParamIDName($tableParamIDName);
    	$language->setTableRowID($tableRowID);
    	$language->changeLanguageText();                      // typ ukonu s daty (pridat,upravit,smazat)
       return true;
    }

    protected function addItemToDictionary($itemType,$itemId,$inputName){
    	$language = new Models_Language_Language();
    	$allLanguages = $language->getDbLanguages();
    	
    	$allDatas = array();
    	$allLanguages = array();
    	
    	foreach($this->allLanguageMutations as $val){
			$inputValue = $this->_request->getPost($inputName."-".$val->suffix); 
			$langData = array(); 		
	    	//pridani hlavnich textovych prvku jako title text anotace atd
			$langData[] 		 = array($itemType.$itemId,$inputValue);
			
			$allDatas[] = $langData;			
    		$allLanguages[] = $val->suffix;
    	}
    	 
    	$language->setLanguages($allLanguages);                // nastavĂ­ vĹˇechny jazyky
    	$language->setDatas($allDatas);                        // nastavĂ­ pĹ™Ă­sluĹˇnĂˇ data jazykĹŻm
    	$language->setType('add');                             // nastavĂ­, jestli se bude do slovnĂ­ku pridavat nebo ubĂ­rat
    	$language->changeLanguageText();                       // typ ukonu s daty (pridat,upravit,smazat)
    }
    protected function getFTPFiles($directoryPath){    	
    	$this->view->FTPfolder = $directoryPath;
    	// create an array to hold directory list
	    $results = array();	
	    // create a handler for the directory
	    $handler = opendir(".".$directoryPath);	
	    // open directory and walk through the filenames
	    while ($file = readdir($handler)) {	
	      // if file isn't this directory or its parent, add it to the results
	      if ($file != "." && $file != "..") {
	        $results[] = $file;
	      }	
	    }
	
	    // tidy up: close the handler
	    closedir($handler);	
	    // done!
	    return $results;
    }

    protected function getIco($fileName){
    	 
    	$fileName = explode(".",$fileName);
    	$fileName = end($fileName);
    	 
    	$ico      = "default.png";
    	 
    	switch($fileName){
    
    		case "doc": $ico = "doc.png";
    		break;
    		case "docx": $ico = "doc.png";
    		break;
    		case "odt": $ico = "doc.png";
    		break;
    		case "xls": $ico = "xls.png";
    		break;
    		case "xlsx": $ico = "xls.png";
    		break;
    		case "ods": $ico = "xls.png";
    		break;
    		case "pdf": $ico = "pdf.png";
    		break;
    		case "jpg": $ico = "img.png";
    		break;
    		case "png": $ico = "img.png";
    		break;
    		case "gif": $ico = "img.png";
    		break;
    		case "bmp": $ico = "img.png";
    		break;
    		case "jpeg": $ico = "img.png";
    		break;
    		case "dwg": $ico = "dwg.png";
    		break;
    		case "zip": $ico = "zip.png";
    		break;
    		default:	$ico = "default.png";
    		break;
    
    	}
    	 
    	return $ico;
    	 
    }
    
    protected function getIcons($fileName){
    
    	$fileName = explode(".",$fileName);
    	$fileName = end($fileName);
    
    	$ico = "txt";
    
    	switch($fileName){
    
    		case "doc": $ico = "doc";
    		break;
    		case "docx": $ico = "doc";
    		break;
    		case "odt": $ico = "doc";
    		break;
    		case "xls": $ico = "xls";
    		break;
    		case "xlsx": $ico = "xls";
    		break;
    		case "ods": $ico = "xls";
    		break;
    		case "pdf": $ico = "pdf";
    		break;
    		case "jpg": $ico = "jpg";
    		break;
    		case "png": $ico = "png";
    		break;
    		case "gif": $ico = "gif";
    		break;
    		case "bmp": $ico = "bmp";
    		break;
    		case "jpeg": $ico = "jpg";
    		break;
    		case "dwg": $ico = "dwg";
    		break;
    		case "zip": $ico = "zip";
    		break;
    		case "csv": $ico = "csv";
    		break;
    		default:	$ico = "txt";
    		break;
    
    	}
    
    	return $ico;
    
    }
	
}

?>