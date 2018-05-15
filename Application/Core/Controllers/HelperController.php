<?php
class Core_HelperController extends Library_Adminbase
{
	
	private $contentItemID;
	private $title;
	private $titleEn;
	private $text;
	private $textEn;
	private	$date;
	
	private $smallPhotoHeigh    = 90; 
	private $smallPhotoWidth    = 0;
	private $middlePhotoHeigh   = 800;
	private $middlePhotoWidth   = 0;
	private $resizeByLongerSide = false;
		
	function init(){
		parent::init();
		
		Zend_Layout::getMvcInstance()->disableLayout();
	}
	
	function getLangsAction(){
		 
		$lang = new Models_Language_DB_LanguageMutation();
		$languages = $lang->getAllLangs();
		
		echo json_encode($languages);
		
		$this->renderScript("helper/empty.phtml");
	}
	
	function getCategoriesAction(){
			
		$category = new Content_Models_Category();
		$catData  = $category->getCategories();
	
		echo json_encode($catData);
	
		$this->renderScript("helper/empty.phtml");
	}
	
	function getAllLinksAction(){
		
		$link = new Content_Models_Link();
		
		$mainLinks = $link->getAllItems("parentID = '0' AND isEshopCategory = 0",'priority');
		$subLinks  = $link->getAllItems("parentID <> '0' AND isEshopCategory = 0",'priority');
		 
		$this->subLinksArr = array();
		$this->linksOutput = "";
		 
		foreach($subLinks as $val){
			$this->subLinksArr[$val->parentID][] = $val;
		}

		$this->recurseLinks($mainLinks,1);
			
		echo $this->linksOutput;
		
		$this->renderScript("helper/empty.phtml");
		
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
	
	function getYtvVideosAction(){
		
		$category      = $this->_request->getPost("category");
		$linkSectionID = $this->_request->getPost("linkSection");
		$offset        = $this->_request->getPost("offset");
		$priority      = $this->_request->getPost("priority");
		
		if((!empty($category) || $category == 0) && is_numeric($category) && !empty($linkSectionID) && is_numeric($linkSectionID) && (!empty($offset) || $offset == 0) && is_numeric($offset) && (!empty($priority) || $priority == 0) && is_numeric($priority)){
			
			$cat = new Content_Models_LinkSectionYtv();
			$catData  = $cat->getAllVideos($category,$linkSectionID,$priority,$offset);
		
			$content = "";
			foreach($catData as $cd){
				
				$content .= '
					<div class="col-md-4 col-sm-6 col-xs-12 margin-bottom-30">';
						if($cd->type == "youtube"){
							$content .= '<iframe width="100%" height="auto" src="http://www.youtube.com/embed/'.$cd->code.'" frameborder="0" allowfullscreen></iframe>';
						}else{
							$content .= '<iframe src="http://player.vimeo.com/video/'.$cd->code.'?title=0&amp;byline=0&amp;portrait=0" width="100%" height="auto" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
						} 
				$content .= '</div>';
				
				
			}
			
			$data = array($content,count($catData));
			echo json_encode($data);
		
		}
	
		$this->renderScript("helper/empty.phtml");
	}

    function savesortedlistAction(){
    	
        $sort       = $this->_request->getPost("sort");
        $control    = $this->_request->getPost("control");
        $controler  = "";
        
        switch($control){
        	
            case "slider" : $controler = new Slider_Models_Slider();break;
            case "article" : $controler = new Content_Models_Article();break;
            case "category" : $controler = new Newsletter_Models_Category();break;
        	        	
        }
        
        $sort = explode(",",$sort);
        $i = 1;
        foreach($sort as $s){
        	
        	$data = array("priority" => $i);
        	$where = " ".$control."ID = '$s' ";
            $controler->updateData($data,$where);
        	$i++;
        }
        
    	
    }

    function getFtpListAction(){
    	 
    	$FTPfolder = $this->_request->getPost("FTPfolder");
    	$this->view->allFTPFiles = $this->getFTPFiles($FTPfolder);
    	 
    }
    function updateLinkPriorityAction(){
    	 
    	$sort = $this->_request->getPost("listID");
    	$link = new Content_Models_Link();
    	
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "linkID = '$s' ";
    		$link->updateData($data,$where);
    		$i++;
    	}
    	$this->renderScript("helper/empty.phtml");
 
    }
    function updateArticlePriorityAction(){
    
    	$sort = $this->_request->getPost("listID");
    	$article = new Content_Models_Article();
    
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "articleID = '$s' ";
    		$article->updateData($data,$where);
    		$i++;
    	}
    	$this->renderScript("helper/empty.phtml");
    
    }
    function updateSupplierPriorityAction(){
    
    	$sort 				= $this->_request->getPost("listID");
    	$supplier 			= new Eshop_Models_Supplier();
    
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "supplierID = '$s' ";
    		$supplier->updateData($data,$where);
    		$i++;
    	}
    	$this->renderScript("helper/empty.phtml");
    }
    function updateCoverPriorityAction(){
    
    	$sort 		= $this->_request->getPost("listID");
    	$cover 		= new Eshop_Models_Cover();
    
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "coverID = '$s' ";
    		$cover->updateData($data,$where);
    		$i++;
    	}
    	$this->renderScript("helper/empty.phtml");
    }
    function updateCategoryPriorityAction(){
    
    	$sort = $this->_request->getPost("listID");
    	$category = new Content_Models_Category();
    	 
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "categoryID = '$s' ";
    		$category->updateData($data,$where);
    		$i++;
    	}
    	$this->renderScript("helper/empty.phtml");
    
    }
    function updateProductCategoryPriorityAction(){
    
    	$sort = $this->_request->getPost("listID");
    	$productCategory = new Eshop_Models_ProductCategory();
    	 
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "productCategoryID = '$s' ";
    		$productCategory->updateData($data,$where);
    		$i++;
    	}
    	$this->renderScript("helper/empty.phtml");
    
    }
    function updateProductPriorityAction(){

    	$sort = $this->_request->getPost("listID");
    	$startPos = $this->_request->getPost("startPos");
    	$product = new Eshop_Models_Product();
    	 
    	$sort = explode("-",$sort);
    	$i = $startPos + 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "productID = '$s' ";
    		$product->updateData($data,$where);
    		$i++;
    	}
    	$this->renderScript("helper/empty.phtml");
    
    }
    function updatePhotoPriorityAction(){
    	 
    	$sort 	= $this->_request->getPost("listID");
    	$photos = new Models_Photo();
    	
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "photoID = '$s' ";
    		$photos->updateData($data,$where);
    		$i++;
    	}
    
		$this->renderScript("helper/empty.phtml");
    }

    function updateFilePriorityAction(){
    
    	$sort 	= $this->_request->getPost("listID");
    	$files = new Models_File();
    	 
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "fileID = '$s' ";
    		$files->updateData($data,$where);
    		$i++;
    	}
    
    	$this->renderScript("helper/empty.phtml");
    }
    function updateEshopProductPriorityAction(){
    	 
    	$sort 	= $this->_request->getPost("listID");
    	$eshopProduct = new Eshop_Models_EshopProduct();
    	
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "eshopProductID = '$s' ";
    		$eshopProduct->updateData($data,$where);
    		$i++;
    	}
    
		$this->renderScript("helper/empty.phtml");
    }
    function updateHomepageSectionPriorityAction(){
    	 
    	$sort 				= $this->_request->getPost("listID");
    	$homepageSection 	= new Models_HomepageSection();
    	
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "homepageSectionID = '$s' ";
    		$homepageSection->updateData($data,$where);
    		$i++;
    	}
    
		$this->renderScript("helper/empty.phtml");
    }
    function updateHomepageSectionProductsPriorityAction(){
    	 
    	$sort 					= $this->_request->getPost("listID");
    	$homepageSectionProduct = new Models_HomepageSectionProduct();
    	
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "homepageSectionProductID = '$s' ";
    		$homepageSectionProduct->updateData($data,$where);
    		$i++;
    	}
    
		$this->renderScript("helper/empty.phtml");
    }
    function updateSocialIconsPriorityAction(){
    	 
    	$sort 				= $this->_request->getPost("listID");
    	$socialIcons 		= new Models_SocialIcons();
    	
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "socialIconsID = '$s' ";
    		$socialIcons->updateData($data,$where);
    		$i++;
    	}
    
		$this->renderScript("helper/empty.phtml");
    }
    function updateFilterPriorityAction(){
    	 
    	$sort 				= $this->_request->getPost("listID");
    	$filter 			= new Eshop_Models_Filter();
    	
    	$sort = explode("-",$sort);
    	$i = 1;
    	foreach($sort as $s){
    		 
    		$data = array("priority" => $i);
    		$where = "filterID = '$s' ";
    		$filter->updateData($data,$where);
    		$i++;
    	}
    
		$this->renderScript("helper/empty.phtml");
    }
    
    function getSubLinkAction(){
    	 
    	$id = $this->_request->getPost("link");
    	$this->view->backData = $this->_request->getPost("backData");
    	
    	$link = new Content_Models_Link();
    	$this->view->sublinks = $link->getAllItems("parentID = '$id'", "priority");
    	 
    }
    
	function getVideosAction(){
    	
        $url = $this->_request->getPost("url");
        echo $url;
        $f = fopen($url,"r");
        fclose($f);
        
    }
                
    function languageAction()
	{
		
		$lang = new Models_Language_DB_LanguageMutation();
		$languages = $lang->getAllItems("enabled = 1","priority");
		
		$langList = array();
		foreach($languages as $l){
			array_push($langList,$l->suffix);
		}
		
		$language = $this->_request->getPost("lang");
		if(in_array($language, $langList)){
			$lang = new Zend_Session_Namespace("lang");
			$lang->setExpirationSeconds(43200);
			$lang->lang = $language;
			echo 1;
		}else{
			echo 0;
		}
		
		$this->renderScript("helper/empty.phtml");
        
	} 
	
	
	/**************
	 * upload fotek
	 *************/
	
	function photoUploadifyAction(){
		
		if (!empty($_FILES)) {
			
			$respnse = array("error" => "");

			$formName  = 'photo-files';
			$path      = '/Public/Images/'.$this->_request->getParam('folder'); 
			$ext       = array("gif","jpg","png","jpeg","GIF","JPG","PNG","JPEG");
			$fileParts = pathinfo($_FILES[$formName]['name'][0]);

			$this->smallPhotoHeigh    = $this->_request->getParam("sheight"); 
			$this->smallPhotoWidth    = $this->_request->getParam("swidth");
			$this->middlePhotoHeigh   = $this->_request->getParam("mheight");
			$this->middlePhotoWidth   = $this->_request->getParam("mwidth");
			$this->resizeByLongerSide = $this->_request->getParam("resized");
			
			if($this->resizeByLongerSide == "true")$this->resizeByLongerSide = true;
			else $this->resizeByLongerSide = false;
			
		    $upload = new Library_UploadFiles();
		    $upload->formName  = $formName;
		    $upload->path      = $path;
		    
			if(in_array($fileParts['extension'],$ext)){
		    	
		    	if($_FILES[$formName]["tmp_name"][0]){
				    $upload->smallWidth           = $this->smallPhotoWidth;
				    $upload->smallHeight          = $this->smallPhotoHeigh;
				    $upload->middleWidth        = $this->middlePhotoWidth;
				    $upload->middleHeight       = $this->middlePhotoHeigh;
				    $upload->resizeByLongerSide = $this->resizeByLongerSide;
				    
				    $upload->upload();
				    
				    if($upload->uploaded){
				    
					    $tableType = $this->_request->getParam('tableType');
			   		    $user      = $this->_request->getParam('user');
				        
					    list($width, $height, $type, $atr) = getimagesize(".".$path."/stredni-".$upload->fileName);
					    $photoTemp = new Models_PhotoTemp();
					    $data = array(
					        "userID"    => $user,
					    	"tableType" => $tableType,
					    	"file"      => $upload->fileName,
					    	"width"     => $width,
					    	"height"    => $height
					    
					    );
					    $photoTemp->insertData($data);
				    
				    }else{
				    	$respnse["error"] = $upload->error;
				    }
		    	}else{
		    		$respnse["error"] = 'Soubor s názvem <strong>'.$_FILES[$formName]["name"][0].'</strong> nebyl nahrán, protože jeho velikost je větší než '.ini_get("upload_max_filesize").'.';
		    	}
		    }	
		}else{
			$respnse["error"] = "Špatná fotka";
		}
		
		echo json_encode($respnse);
	}
	
	
	function getAddPhotosAction(){

		$folder       = $this->_request->getPost("folder");
		$user         = $this->_request->getPost("ui");
		$table        = $this->_request->getPost("table");
		$tableID      = $this->_request->getPost("tableID");
		$tableIDvalue = $this->_request->getPost("tableIDvalue");
		$deleteValues = $this->_request->getPost("delete");
			
		$language = new Models_Language_Language();
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
			
		$photo = new Models_Photo();  	
		$deleteValues = explode(",",$deleteValues);
    	if(count($deleteValues) >= 1 && !empty($deleteValues[0])){
    		
    		$oldItem = '';
    		$oldFile = '';
    		foreach($deleteValues as $item){

    			$item = explode("-",$item);
    			if($item[1]=="e"){
    				$oldItem = $photo->getOneRow("photoID = '$item[0]'");
    				$oldFile = $oldItem->title;
    				$where   = "photoID = '$item[0]'";
    				$photo->deleteData($where);
    				$photo->deleteTableData($table,$where);
    				
    				//odstranime popisek ze slovniku
    				if($this->modulesData->jazykoveMutace)
    					$this->updateDictionary('delete','photo',"photoID",$item[0]);
    				
    				unlink("./Public/Images/".$folder."/".$oldFile);
	                unlink("./Public/Images/".$folder."/mala-".$oldFile);
	    			unlink("./Public/Images/".$folder."/stredni-".$oldFile);
	    			
    			}else{
    				$oldItem = $photo->getTempPhoto($item[0]);
    				$oldFile = $oldItem->file;
    				$where   = "photoTempID = '$item[0]'";
    				$photo->deleteTableData("photo_temp",$where);
    				
    				unlink("./Public/Images/Temp/".$oldFile);
	                unlink("./Public/Images/Temp/mala-".$oldFile);
	    			unlink("./Public/Images/Temp/stredni-".$oldFile);
    			
    			}

    		}
    		
    	}
    	
    	$hasMainPhotoCount = $photo->mainPhotoExist($table,$tableID,$tableIDvalue);
    	if($hasMainPhotoCount == 0){
    		
    		$minID = $photo->getMinIDValue($table,$tableID,$tableIDvalue);
    		if(!empty($minID)){
	    		$this->view->mainPhoto = $minID."-e";    	
	    		$photo->setFirstMainPhoto($minID);    			
				setcookie("mainPhoto",$minID."-e",NULL,"/"); 
    		}else if(isset($_COOKIE["mainPhoto"]))$this->view->mainPhoto  = $_COOKIE["mainPhoto"];
			
    	}else if(isset($_COOKIE["mainPhoto"]))$this->view->mainPhoto  = $_COOKIE["mainPhoto"];

    	    	
    	$this->view->table         								= $table;
		$this->view->tableID       								= $tableID;
		$this->view->tableIDvalue  								= $tableIDvalue;
		$this->view->user         					 			= $user;
		$this->view->folder        								= $folder;	
		
	    
		$this->setPhotosData($photo,$tableIDvalue,$table,$tableID,$user);	
	   
    	    	
    }
	function cropPhotoAction(){

		$photo        = new Models_Photo();
		$crop         = $this->_request->getPost("crop");
		$fileID       = $this->_request->getParam("file");
		$path         = $this->_request->getPost("path");
		$table        = $this->_request->getPost("table");
		$tableID      = $this->_request->getPost("tableID");
		$tableIDvalue = $this->_request->getPost("tableIDvalue");
		$user         = $this->_request->getPost("ui");
		$folder       = $this->_request->getPost("folder");
		
		$fileIDitems  = explode("-",$fileID);
		$fileID       = $fileIDitems[0];
		$oldItemsFile = "";
		if($fileIDitems[1] == "e"){
	 		$oldItems     = $photo->getOneRow("photoID = '$fileID'");
	 		$oldItemsFile = $oldItems->title;
	 		$path         = $path."/".$folder;
	 	}else{
			$oldItems     = $photo->getTempPhoto($fileID);
			$oldItemsFile = $oldItems->file;
			$path         = $path."/Temp";	    
	 	}
		
		$cropPhoto = new Library_CropPhotos();
	    $cropPhoto->setCrop($crop);  
	    $cropPhoto->setSmallHeight($this->smallPhotoHeigh);
	    $cropPhoto->setFile($oldItemsFile); 
	    $cropPhoto->setPath($path);
	    $cropPhoto->setRequest($this->_request);
	    
	    $widthHeight = $cropPhoto->execute();
	    
		if($fileIDitems[1] == "e"){
		    $data = array("width"=>$widthHeight['width'],"height"=>$widthHeight['height']);
		 	$photo->updateData($data,"photoID = '$fileID'");
		}else{
		    $photo->updateTempData($widthHeight['width'],$widthHeight['height'],$fileID );
		}
		
		$this->view->folder        								= $folder;
		$this->view->user          								= $user;
		$this->view->table         								= $table;
		$this->view->tableIDvalue  								= $tableIDvalue;
		$this->view->tableID       								= $tableID;
		if(isset($_COOKIE["mainPhoto"]))$this->view->mainPhoto  = $_COOKIE["mainPhoto"];
		
	    $this->view->allTempPhotos 								= $photo->getTempPhotos($user,$table);	 
	    if($folder != "Temp"){    
	    	$this->view->allPhotos     = $photo->getPhotos($tableIDvalue,$table,$tableID); 	    
	    }
		$this->renderScript("helper/get-add-photos.phtml");
		

    }
	 
    
    //pro obecne pridavani fotek (k aktualitam, clankum atd.)
	function photoAction()
    {  	

    	$photo  = new Models_Photo();	
    	$id     = $this->_request->getParam('id');
    	$folder = $this->_request->getPost('folder');
		$table  = $this->_request->getPost('table');
		$action = $this->_request->getPost('action');
		$user   = $this->_request->getPost('ui');
		$path   = $this->_request->getPost('path');
		
		$this->view->id = $id;
    	$id = explode("-",$id);
    	
    	if($id[1] == "e"){
    		$photoItems         = $photo->getPhoto($table,$id[0]);
    		$this->view->file   = $photoItems->title;
    		$this->view->folder = $folder;
    	}else{
    		$photoItems         = $photo->getTempPhoto($id[0]);
    		$this->view->file   = $photoItems->file;
    		$this->view->folder = "Temp";
    	}
    	
    	
    	$this->view->backUrl = $action;
    	$this->view->table   = $table;
    	$this->view->user    = $table;
    	$this->view->path   = $path;

    }
    
    
	/*
	 *  upload videi
	 * 
	 */
    
    function videoUploadifyAction(){
    	
	    if (!empty($_FILES)) {
			
				
			$formName  = 'video-filedata';
			$folder    = $_REQUEST['folder'];
			$path      = $folder; 
			$ext       = array("mp4","MP4");
			$fileParts = pathinfo($_FILES[$formName]['name']);
			
		    $upload = new Library_UploadFiles();
		    $upload->formName  = $formName;
		    $upload->path      = $path;
		    $upload->upload();
		    
		    $tableType = $this->_request->getPost('tableType');
		    $user      = $this->_request->getPost('user');
			    
		    $fileName = explode(".",$upload->fileName);
		    $file     = array_pop($fileName);
		    $fileName = implode(".",$fileName);
		    $fileSize = filesize("./Public/Videos/Temp/".$fileName.".mp4");
		    
		    // nahled videa		    
		    $videoClass = new Library_Video();
		    $videoClass->setWeb("ozone.cz");
		    $videoClass->setFolder("Temp");
		    $videoClass->setFile($fileName);
		    $videoClass->makePreview();
		    
		    $footage = $videoClass->getVideoDuration();
		    
		    $video = new Models_VideoTemp();
		    $data = array(
		        "userID"    => $user,
		    	"tableType" => $tableType,
		    	"file"      => $fileName,
		    	"footage"   => $footage,
		    	"fileSize"  => $fileSize
		    
		    );
		    $video->insertData($data);
		    
			echo "1";
			
		}
    }
    
	function getAddVideosAction(){

		$folder       = $this->_request->getPost("folder");
		$user         = $this->_request->getPost("ui");
		$table        = $this->_request->getPost("table");
		$tableID      = $this->_request->getPost("tableID");
		$tableIDvalue = $this->_request->getPost("tableIDvalue");
		$deleteValues = $this->_request->getPost("delete");
			
		$language = new Models_Language_Language();
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
				
		$video = new Models_Video();  	
		$deleteValues = explode(",",$deleteValues);
    	if(count($deleteValues) >= 1 && !empty($deleteValues[0])){
    		
    		$oldItem = '';
    		$oldFile = '';
    		foreach($deleteValues as $item){

    			$item = explode("-",$item);
    			if($item[1]=="e"){
    				$oldItem = $video->getOneRow("videoID = '$item[0]'");
    				$oldFile = $oldItem->file;
    				$where   = "videoID = '$item[0]'";
    				$video->deleteData($where);
    				$video->deleteTableData($table,$where);
    				
    				//odstranime popisek ze slovniku
    				if($this->modulesData->jazykoveMutace)
    					$this->updateDictionary('delete-video',$oldItem);
    				
    				unlink("./Public/Videos/".$folder."/".$oldFile.".mp4");
    				unlink("./Public/Images/Previews/".$folder."/".$oldFile.".png");
	                unlink("./Public/Images/Previews/".$folder."/mala-".$oldFile.".png");

	    			
    			}else{
    				$oldItem = $video->getTempVideo($item[0]);
    				$oldFile = $oldItem->file;
    				$where   = "videoTempID = '$item[0]'";
    				$video->deleteTableData("video_temp",$where);
    				
    				unlink("./Public/Videos/Temp/".$oldFile.".mp4");
	                unlink("./Public/Images/Previews/Temp/".$oldFile.".png");
	                unlink("./Public/Images/Previews/Temp/mala-".$oldFile.".png");
    			
    			}
    			
    			

    		}
    		
    	}
    	
    	    		
    	$this->view->vTable         = $table;
		$this->view->vTableID       = $tableID;
		$this->view->vTableIDvalue  = $tableIDvalue;
		$this->view->vUser         	= $user;
		$this->view->vFolder        = $folder;	
		
	    
		$this->setVideosData($video,$tableIDvalue,$table,$tableID,$user);
    	    	
    }
    
    /**
    *
    *  upload různých souboru jako pdf, doc, xls, pod
    */
    
    function fileUploadifyAction(){
    
    	if (!empty($_FILES)) {
    
    		$formName  = 'file-uploadify';
    		$path      = $_REQUEST['folder'];
    		
    		if($_FILES[$formName]["tmp_name"]){
    
	    		$upload = new Library_UploadGraphics();
	    		$upload->formName = $formName;
	    		$upload->path     = $path;
	    
	    		$upload->upload();
	    		
	    		if($upload->uploaded){
	    		
		    		$tableType = $this->_request->getPost('tableType');
		    		$user      = $this->_request->getPost('user');
		    
		    		$ico = $this->getIcons($upload->fileName);
		    
		    		$fileTemp = new Models_FileTemp();
		    		$data = array(
		        			"userID"    => $user,
		        			"tableType" => $tableType,
		        			"title"     => $upload->fileName,
		        			"ico"       => $ico
		    
		    		);
		    		$fileTemp->insertData($data);
	    		
	    		}else{
			    	echo $upload->error;
			    }
    
    		}else{
	    		echo 'Soubor s názvem <strong>'.$_FILES[$formName]["name"].'</strong> nebyl nahrán, protože jeho velikost je větší než '.ini_get("upload_max_filesize").'.';
	    	}
    
    	}
    }
    
    
    function getAddFilesAction(){
    
    	$folder       = $this->_request->getPost("folder");
    	$user         = $this->_request->getPost("ui");
    	$table        = $this->_request->getPost("table");
    	$tableID      = $this->_request->getPost("tableID");
    	$tableIDvalue = $this->_request->getPost("tableIDvalue");
    	$deleteValues = $this->_request->getPost("delete");
			
		$language = new Models_Language_Language();
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
    
    	$file = new Models_File();
    	$deleteValues = explode(",",$deleteValues);
    	if(count($deleteValues) >= 1 && !empty($deleteValues[0])){
    
    		$oldItem = '';
    		$oldFile = '';
    		foreach($deleteValues as $item){
    
    			$item = explode("-",$item);
    			if($item[1]=="e"){
    				$oldItem = $file->getOneRow("fileID = '$item[0]'");
    				$oldFile = $oldItem->title;
    				$where   = "fileID = '$item[0]'";
    				$file->deleteData($where);
    				$file->deleteTableData($table,$where);
    
    				//odstranime popisek ze slovniku
    				if($this->modulesData->jazykoveMutace)
    					$this->updateDictionary('delete','file',"fileID",$item[0]);
    				
    				//unlink("./Public/Files/".$folder."/".$oldFile);
    
    			}else{
    				$oldItem = $file->getTempFile($item[0]);
    				$oldFile = $oldItem->title;
    				$where   = "fileTempID = '$item[0]'";
    				$file->deleteTableData("file_temp",$where);
    
    				if(!$oldItem->isFromSource)
    					unlink("./Public/Files/Temp/".$oldFile);
    					
    			}
    
    		}
    
    	}
    
    	$this->view->fTable        = $table;
    	$this->view->fTableID      = $tableID;
    	$this->view->fTableIDvalue = $tableIDvalue;
    	$this->view->fUser         = $user;
    	$this->view->fFolder       = $folder;    
    	
		$this->setFilesData($file,$tableIDvalue,$table,$tableID,$user);
    
    }

    function storeNewsletterEmailAction(){

    	$translate 			= Zend_Registry::get("Zend_Translate");
    	$filter 			= new Zend_Filter_StripTags();
    	$newsletterEmail	= new Content_Models_NewsletterEmail();
    	$scripts			= new Library_Scripts();
    	$email      		= $filter->filter(stripslashes($this->_request->getPost("email")));
    	
    	if($scripts->check_email($email)){
	    	$emailExist = $newsletterEmail->getOneRow("email = '$email'");
	    	if(empty($emailExist)){
		    	$newsletterEmail->insert(array(
		    		"email" => $email
		    	));
		    	$response['status'] 	= 1;
		    	$response['message'] 	= $translate->translate("Email úspěšně uložen. Děkujeme za Váš zájem.");
	    	}else{
		    	$response['status'] 	= 2;
		    	$response['message'] 	= $translate->translate("Zadaný email je již v naší databázi evidován.");
	    	}
    	}else{
	    	$response['status'] 	= 2;
	    	$response['message'] 	= $translate->translate("Zadali jste nesprávný formát emailu!");
    	}
    	echo json_encode($response);
		
		$this->renderScript("helper/empty.phtml");
    	
    }
  
}

	
?>