<?php
class EshopW_HelperController extends Library_WebBase
{
	var $lang;
	var $translate;
	
	public function init(){
		
		Zend_Layout::getMvcInstance()->disableLayout();
		$lang = new Zend_Session_Namespace("lang");
		$this->lang = $lang->lang;
		
		$this->translate = Zend_Registry::get("Zend_Translate");
	}
	
	public function changeItemCountAction(){
	
		$this->getConcreteLang();
	
		$response = array(
			"error" => ""
		);
	
		$filter = $this->setData();
		
		if($filter->isValid("count")){
	
			if($filter->isValid("index") || $this->index == 0){
					
				$productFunction = new Eshop_Library_ProductFunctions($this->_request, $this->_response);
				$currentData = $productFunction->changeItemCount($this->index,$this->count);
					
				$products = $productFunction->getBasketContent();
					
				$totalPrice     = number_format($products["totalPrice"],$this->wholeLangData->decimal,","," ")." ".$this->wholeLangData->currencySign;
				$totalLowPrice  = number_format($products["totalLowPrice"],2,","," ")." ".$this->wholeLangData->currencySign;
					
				$response["totalPriceString"]    = $totalPrice;
				$response["totalPrice"]          = $products["totalPrice"];
				$response["totalLowPriceString"] = $totalLowPrice;
				$response["products"]            = $products["products"];
				$response["decimal"]             = $this->wholeLangData->decimal;
				$response["currency"]            = $this->wholeLangData->currencySign;
				
				$response["currentPriceString"]    = $currentData["priceString"];
				$response["currentLowPriceString"] = $currentData["lowPriceString"];
				
				$response["decimal"]             = $this->wholeLangData->decimal;
				$response["currency"]            = $this->wholeLangData->currencySign;
		
			}else{
		
				$response["error"] = $this->translate->translate("Špatný produkt");
			}
		}else{
		
			$response["error"] = $this->translate->translate("Špatný počet");
		}
	
		echo json_encode($response);
	
		$this->renderScript("helper/empty.phtml");
	
	
	}
	
	public function removeFromBasketAction(){
		
		$this->getConcreteLang();
		
		$response = array(
			"error" => ""
		);
		
		$filter = $this->setData();
		
		if($filter->isValid("index") || $this->index == 0){
			
			$productFunction = new Eshop_Library_ProductFunctions($this->_request, $this->_response);
			$productFunction->removeItemFromBasket($this->index);
			
			$products = $productFunction->getBasketContent();
			
			$totalPrice     = number_format($products["totalPrice"],$this->wholeLangData->decimal,","," ")." ".$this->wholeLangData->currencySign;
			$totalLowPrice  = number_format($products["totalLowPrice"],2,","," ")." ".$this->wholeLangData->currencySign;
			
			$response["totalPriceString"]    = $totalPrice;
			$response["totalPrice"]          = $products["totalPrice"];
			$response["totalLowPriceString"] = $totalLowPrice;
			$response["products"]            = $products["products"];
			$response["decimal"]             = $this->wholeLangData->decimal;
			$response["currency"]            = $this->wholeLangData->currencySign;

		}else{
						
			$response["error"] = $this->translate->translate("Špatný produkt");
		}
		
		echo json_encode($response);
		
		$this->renderScript("helper/empty.phtml");
		
		
	}
	
	public function addToBasketAction(){
		
		$response = array(
			"error" => ""
		);
		
		$this->getConcreteLang();
		
		$filter = $this->setData();
		if($filter->isValid("epID")){
			
			$this->epID = floor($this->epID);
			
			if($filter->isValid("count")){
				
				$eshopProd = new Eshop_Models_EshopProduct();
				$eshopProdData = $eshopProd->getEshopProductToCheck($this->lang,$this->epID);
				
				if($eshopProdData->predefinedCoversType != 2 || ($eshopProdData->predefinedCoversType == 2 && ((is_numeric($this->photoID) || empty($this->photoID)) && (is_numeric($this->photo2ID) || empty($this->photo2ID)))  && ((is_numeric($this->coverID) || empty($this->coverID)) && (is_numeric($this->cover2ID) || empty($this->cover2ID))))){

					$percentage  = 0;
					
					if(!empty($eshopProdData->percentage)){
						$percentage = $eshopProdData->percentage;
						if($eshopProdData->percentage2 > $percentage)$percentage = $eshopProdData->percentage2;
						
					}else{
						$coverID  = $this->coverID;
						$cover2ID = $this->cover2ID;
						
						$supplierCover = new Eshop_Models_SupplierCover();
						$coverData  = $supplierCover->getOneRow("supplierID = '$eshopProdData->supplierID' AND coverID = '$coverID'");
						$cover2Data = $supplierCover->getOneRow("supplierID = '$eshopProdData->supplierID' AND coverID = '$cover2ID'");
						
						if($coverData)$percentage = $coverData->percentage;
						if($cover2Data && $cover2Data->percentage > $percentage)$percentage = $cover2Data->percentage;
						
						
					}
					$price = $eshopProdData->productPrice + ($eshopProdData->productPrice / 100 * $percentage);

					$price = $price * $this->count;
					
					$lowPrice = round($price / (1 + ($eshopProdData->vatValue/100)),2);
					
					$photoID      = $this->photoID;
					$photo        = $this->photo;
					$coverSubcode = $this->cover1Subcode;
					
					if(!empty($eshopProdData->photoID)){
						$photoID = $eshopProdData->photoID;
						$photoTable = new Models_Photo();
						$photoData = $photoTable->getOneRow("photoID = '$photoID'");
						
						$coverSubcode = $eshopProdData->coverName." ".$photoData->number;
						$photo = $photoData->title;
						
					};
					
					$photo2ID      = $this->photo2ID;
					$photo2        = $this->photo2;
					$coverSubcode2 = $this->cover2Subcode;
					
					if(!empty($eshopProdData->photo2ID)){
						$photo2ID = $eshopProdData->photo2ID;
						$photoTable = new Models_Photo();
						$photo2Data = $photoTable->getOneRow("photoID = '$photo2ID'");					
						$coverSubcode2 = $eshopProdData->coverName2." ".$photo2Data->number;
						
					}
					
					$coverTitle = $this->cover1Title;
					if(!empty($eshopProdData->coverTitle))$coverTitle = $eshopProdData->coverTitle;
					
					$coverTitle2 = $this->cover2Title;
					if(!empty($eshopProdData->coverTitle))$coverTitle2 = $eshopProdData->coverTitle2;
					
					$sideID = $this->side;
					if(!empty($eshopProdData->sideID)){
						$sideID = $eshopProdData->sideID;
					}
					
					$sideTitle = "";
					if($sideID != 0){
						$sideTable = new Eshop_Models_Side();
						$sideData = $sideTable->getOneRow("sideID = '$sideID'");
						
						$sideTitle = $sideData->title;
					}
					
								
					$session = new Zend_Session_Namespace("basket");
					
					$std = new stdClass();
					$std->epID =           $this->epID;
					$std->productID =      $eshopProdData->productID;
					$std->count =          $this->count;
					$std->price =          $price;
					$std->priceString =    number_format($price,$this->wholeLangData->decimal,","," ")." ".$this->wholeLangData->currencySign;
					$std->lowPrice =       $lowPrice;
					$std->lowPriceString = number_format($lowPrice,2,","," ")." ".$this->wholeLangData->currencySign;
					$std->sideID =         $sideID;
					$std->side =           $this->translate->translate($sideTitle);
					$std->productPhoto =   "/Public/Images/EshopProduct/mala-".$eshopProdData->photo;
					$std->productTitle =   $eshopProdData->title;	
				  $std->productUrl =     $this->productUrl;
					$std->photoID =        $photoID;
					$std->photo =          $photo;
					$std->photo2ID =       $photo2ID;
					$std->photo2 =         $photo2;				
					$std->cover1Title =    $this->translate->translate($coverTitle);
					$std->cover2Title =    $this->translate->translate($coverTitle2);
					$std->cover1Subcode =  $coverSubcode;
					$std->cover2Subcode =  $coverSubcode2;
					
					$session->product[] = $std;
					
					$prodFun = new Eshop_Library_ProductFunctions($this->_request, $this->_response);
		    		$basketContent = $prodFun->getBasketContent();
					
					$response["count"]      = $basketContent["productCount"];
					$response["totalPrice"] = $basketContent["totalPrice"];
					$response["products"]   = $basketContent["products"];
					$response["product"]    = $std;
					
					$session->productTotalPrice    = $basketContent["totalPrice"];
					$session->productTotalLowPrice = $basketContent["totalLowPrice"];
				
				}else{
					$response["error"] = $this->translate->translate("Nesprávné potahy");
				}
			
			}else{
				$response["error"] = $this->translate->translate("Počet kusů musí být větší než 0");
			}
			
		}else{
			$response["error"] = $this->translate->translate("Špatný produkt");
		}
		
		echo json_encode($response);
		$this->renderScript("helper/empty.phtml");
		
	}
	
	private function getData(){
	
		$data = array(
		
			"epID" =>          $this->_request->getPost("epID"),
			"count" =>         $this->_request->getPost("count"),
			"side" =>          $this->_request->getPost("side"),
			"productPhoto" =>  $this->_request->getPost("productPhoto"),
			"productTitle" =>  $this->_request->getPost("productTitle"),
			"productUrl" =>    $this->_request->getPost("productUrl"),  
			"photoID" =>       $this->_request->getPost("photoID"),
			"photo" =>         $this->_request->getPost("photo"),
			"photo2ID" =>      $this->_request->getPost("photo2ID"),
			"photo2" =>        $this->_request->getPost("photo2"),
			"cover1Title" =>   $this->_request->getPost("cover1Title"),
			"cover2Title" =>   $this->_request->getPost("cover2Title"),
			"cover1Subcode" => $this->_request->getPost("cover1Subcode"),
			"cover2Subcode" => $this->_request->getPost("cover2Subcode"),
		    "coverID" 		=> $this->_request->getPost("coverID"),
			"cover2ID" 		=> $this->_request->getPost("cover2ID"),
			"index"         => $this->_request->getPost("index"),
			"count"         => $this->_request->getPost("count")

		);
		
		return $data;
	
	}
	
	private function setData(){
	
		$filters    = $this->setFilters();
		$validators = $this->setValidators();
		$data       = $this->getData();
		$script		= new Library_Scripts();
		$filter = new Zend_Filter_Input($filters, $validators, $data);
	
			
		$this->epID =          $filter->getUnescaped("epID");
		$this->count =         $filter->getUnescaped("count");
		$this->side =          $filter->getUnescaped("side");
		$this->productPhoto =  $filter->getUnescaped("productPhoto");
		$this->productTitle =  $filter->getUnescaped("productTitle");
		$this->productUrl =    $filter->getUnescaped("productUrl");
		$this->photoID =       $filter->getUnescaped("photoID");
		$this->photo =         $filter->getUnescaped("photo");
		$this->photo2ID =      $filter->getUnescaped("photo2ID");
		$this->photo2 =        $filter->getUnescaped("photo2");
		$this->cover1Title =   $filter->getUnescaped("cover1Title");
		$this->cover2Title =   $filter->getUnescaped("cover2Title");
		$this->coverID =       $filter->getUnescaped("coverID");
		$this->cover2ID =      $filter->getUnescaped("cover2ID");
		$this->cover1Subcode = $filter->getUnescaped("cover1Subcode");
		$this->cover2Subcode = $filter->getUnescaped("cover2Subcode");
		$this->index         = $filter->getUnescaped("index");
		$this->count         = $filter->getUnescaped("count");
	
		return $filter;
	}
	

	private function setFilters(){
	
		$filters = array(

			"epID" =>          'StripTags',
			"count" =>         'StripTags',
			"side" =>          'StripTags',
			"productPhoto" =>  'StripTags',
			"productTitle" =>  'StripTags',
		    "productUrl" =>    'StripTags',
			"photoID" =>       'StripTags',
			"photo" =>         'StripTags',
			"photo2ID" =>      'StripTags',
			"photo2" =>        'StripTags',
			"cover1Title" =>   'StripTags',
			"cover2Title" =>   'StripTags',
		    "coverID" =>       'StripTags',
			"cover2ID" =>      'StripTags',
			"cover1Subcode" => 'StripTags',
			"cover2Subcode" => 'StripTags',
			"index" =>         'StripTags',
			"count" =>         'StripTags'
		);
	
		return $filters;
	
	}
	
	private function setValidators(){
	
		$validators = array(
		 
			"epID" =>          array('allowEmpty' => false,'Int'),
			"count" =>         array('allowEmpty' => false,'Int'),
			"side" =>          array('allowEmpty' => true),
			"productPhoto" =>  array('allowEmpty' => true),
			"productTitle" =>  array('allowEmpty' => true),
		    "productUrl" =>    array('allowEmpty' => true),
			"photoID" =>       array('allowEmpty' => true),
			"photo" =>         array('allowEmpty' => true),
			"photo2ID" =>      array('allowEmpty' => true),
			"photo2" =>        array('allowEmpty' => true),
			"cover1Title" =>   array('allowEmpty' => true),
			"cover2Title" =>   array('allowEmpty' => true),
			"coverID" =>       array('allowEmpty' => true),
			"cover2ID" =>      array('allowEmpty' => true),
			"cover1Subcode" => array('allowEmpty' => true),
			"cover2Subcode" => array('allowEmpty' => true),
			"index" =>         array('allowEmpty' => false,'Int'),
			"count" =>         array('allowEmpty' => false,'Int')
		
		);
	
		return $validators;
	
	}
   
    
}

	
?>