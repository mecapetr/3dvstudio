<?php
class Eshop_Library_ProductFunctions extends Library_WebBase
{
	
	var $productID;
	var $lang;
			
	public function getProductData(){

		$this->getConcreteLang();
		
		$product 	   = new Eshop_Models_Product();
		$productStatus = new Eshop_Models_ProductProductStatus();
		$script		   = new Library_Scripts();
		
		$productData = $product->getProductData($this->lang->lang,$this->productID);
    
    if(!$productData)$this->_redirect("/");
    
		$productData->stringPrice = number_format($productData->price,$this->wholeLangData->decimal,","," ")." ".$productData->currencySign;
		$productData->originalStringPrice = number_format($productData->originalPrice,$this->wholeLangData->decimal,","," ")." ".$productData->currencySign;
		
		$productData->stringEssoxPrice = number_format($productData->price/10,$this->wholeLangData->decimal,","," ")." ".$productData->currencySign;
		
		$productData->isInAuction = false;
		if(!empty($productData->dateAuction)){
			$productAuction	    		= new Auction_Models_ProductAuction();
			$productData->timeToEnd 	=  $script->getAuctionTime($productData->dateAuction);
			$productData->isInAuction 	= true;

			$productData->price 					= number_format($productData->price,$this->wholeLangData->decimal,","," ");
			$productData->priceAuction 				= number_format(round($productData->priceAuction / $this->wholeLangData->exchangeRate,2),$this->wholeLangData->decimal,","," ");
			$productData->minPriceAuction 			= number_format(round($productData->minPriceAuction / $this->wholeLangData->exchangeRate,0),$this->wholeLangData->decimal,","," ");
			$lasBid									= $productAuction->getLasBidPrice("PA.productID = $productData->productID AND PA.deleted = 0");
			if(!empty($lasBid)){
				$productData->lasBidPriceFormated	= number_format(round($lasBid->value/ $this->wholeLangData->exchangeRate,2),$this->wholeLangData->decimal,","," ");
				$productData->lasBidPrice			= number_format(round($lasBid->value/ $this->wholeLangData->exchangeRate,0),0,",","");
			}else{
				$productData->lasBidPriceFormated	= $productData->priceAuction;
				$productData->lasBidPrice			= number_format(round($productData->priceAuction / $this->wholeLangData->exchangeRate,0),0,",","");
			}
			
			$productData->allBids = $productAuction->getAllBids("PA.productID = $productData->productID AND PA.deleted = 0");
			foreach ($productData->allBids as $key => $val){
				$productData->allBids[$key]->dateAdd 	= date("j.n.Y H:i:s",strtotime($productData->allBids[$key]->dateAdd));
				$productData->allBids[$key]->tel 		= $this->getShortTel($productData->allBids[$key]->tel);
				$productData->allBids[$key]->value 		= number_format(round($productData->allBids[$key]->value / $this->wholeLangData->exchangeRate,2),$this->wholeLangData->decimal,","," ");
			}
		}
		
		if($productData->productTypeID){
			$productSNP = new Eshop_Models_ProductSizeNumberPosition();
			$this->view->dimensions = $productSNP->getDimensions($productData->productID,$productData->productTypeID);
		}
		
		$this->view->productData = $productData;
		
		$this->view->isDiscount = $productStatus->getCount("productID = '$this->productID' AND productStatusID = '1'");
		
		$coverMaterial = new Eshop_Models_CoverMaterial();
		$this->view->allMaterials = $coverMaterial->getAllMaterials($this->lang->lang);
		
		$this->getProductPhotos($productData);
		$this->getEshopProducts($productData);
		$this->getEshopCovers($productData);
		
	}
	public function getAuctionTime($date){
		 
		$str = strtotime($date) - time();
		 
		if($str > 0){
			$days = (int)($str / 86400);
	
			$mod = $str % 86400;
			$hours = (int)($mod / 3600);
	
			$mod = $mod % 3600;
			$minutes = (int)($mod / 60);
	
			$seconds = $mod % 60;
	
			if($hours < 10)$hours     = "0".$hours;
			if($minutes < 10)$minutes = "0".$minutes;
			if($seconds < 10)$seconds = "0".$seconds;
	
			return $days."d ".$hours.":".$minutes.":".$seconds;
		}else{
			return "aukce skončila";
		}
		 
	}
	public function getProductPhotos($productData){
	
		$photoProduct = new Eshop_Models_PhotoProduct();
		$photos = $photoProduct->getProductPhotos($productData->productID,$this->lang->lang);
	
		$this->view->productPhotos = $photos;
	
	}
	
	public function getEshopCovers($productData){
		
		$cover         = new Eshop_Models_Cover();
		$categoryCover = new Eshop_Models_ProductCategoryCover();
		
		$covers = $cover->getAllCoversBySuppliers($this->lang->lang,$productData->supplierID,$productData->productID);
		$pcat   = $categoryCover->getAllCategoryCoversByProduct($productData->productID);

		$coverList = array();
		foreach($covers as $cov){
			
			$coverList[$cov->coverID]["mark"] = "";
			foreach($pcat as $pc){
				if($pc->coverID == $cov->coverID){
					if($coverList[$cov->coverID]["mark"]){
						$coverList[$cov->coverID]["mark"] .= ", ";
					}
					$coverList[$cov->coverID]["mark"] .= "c-".$pc->productCategoryID;
					//break;
				}
			}
						
			if(!empty($cov->coverMaterialID)){
				
				if(!empty($coverList[$cov->coverID]["mark"]))$coverList[$cov->coverID]["mark"] .= ", ";
				
				if(!empty($coverList[$cov->coverID]["mark"]) && $coverList[$cov->coverID]["mark"] != "m-".$cov->coverMaterialID.", "){
					$coverList[$cov->coverID]["mark"] .= "m-".$cov->coverMaterialID;
				}else{
					$coverList[$cov->coverID]["mark"] = "m-".$cov->coverMaterialID;
				}
			}
			if(!empty($cov->mainPhoto)){
				$coverList[$cov->coverID]["mainPhoto"] = $cov->photoTitle;
			}
			$coverList[$cov->coverID]["title"] = $cov->title;
			$coverList[$cov->coverID]["data"][] = $cov;
		}
		$this->view->covers = $coverList;
		
	}
	
	public function getEshopProducts($productData){
				
		$productCategory = new Eshop_Models_ProductCategory();
		$this->view->eshopCategories = $eshopCategories = $productCategory->getProductCategories($this->lang->lang,$this->productID);
		
		$eshopProduct = new Eshop_Models_EshopProduct();
		$products = $eshopProduct->getWebProducts($this->lang->lang,$this->productID);
				
		$productList = array();
		foreach($products as $pr){
			$pr->stringPrice = number_format($pr->price,$this->wholeLangData->decimal,","," ")." ".$pr->currencySign;
			
			$pr->linkShortcuts = explode("-",$pr->linkShortcuts);
			$maxShortcuts = $pr->totalLinkCount / $pr->linkCount;
			
			$linkShortcuts = "";
			
			for($i = 0;$i < $maxShortcuts;$i++){
				if(!empty($linkShortcuts))$linkShortcuts .= "-";
				$linkShortcuts .= $pr->linkShortcuts[$i];
			}
			
			$dimension = "";
			if(!empty($pr->shortcut))$dimension = "-".$pr->shortcut;
			
			$pr->productCode = $linkShortcuts."-".$pr->productTitle."-".$pr->productID.$dimension;
			
			if($pr->price == 0 || empty($pr->price)){
				foreach($eshopCategories as $cat){
					if($cat->productCategoryID == $pr->productCategoryID){
						$pr->price = $productData->price + ($productData->price / 100 * $cat->percent);
						$pr->stringPrice = number_format($pr->price,$this->wholeLangData->decimal,","," ")." ".$pr->currencySign;
						break;
					}
				}
			}
			
			$productList[$pr->productCategoryID][] = $pr;
		}
		
		$this->view->eshopProducts = $productList;
		
	}
	
	public function changeItemCount($k,$count){
		
		$this->getConcreteLang();
		
		$session = new Zend_Session_Namespace("basket");
		foreach($session->product as $key => $pr){
			if($key == $k){
				
				$session->productTotalPrice -=  $pr->price;
				$session->productTotalLowPrice -=  $pr->lowPrice;
				
				$pr->price    = $pr->price / $pr->count;
				$pr->lowPrice = $pr->lowPrice / $pr->count;
												
				$pr->price    = $pr->price * $count;
				$pr->lowPrice = $pr->lowPrice * $count;
				
				$pr->count = $count;
				
				$pr->priceString =    number_format($pr->price,$this->wholeLangData->decimal,","," ")." ".$this->wholeLangData->currencySign;
				$pr->lowPriceString = number_format($pr->lowPrice,2,","," ")." ".$this->wholeLangData->currencySign;
				
				$session->productTotalPrice +=  $pr->price;
				$session->productTotalLowPrice +=  $pr->lowPrice;				
								
				return array("price" => $pr->price, "lowPrice" => $pr->lowPrice,"priceString" => $pr->priceString, "lowPriceString" => $pr->lowPriceString);

			}
		}
		
	}
	
	public function removeItemFromBasket($k){
		
		$session = new Zend_Session_Namespace("basket");
		foreach($session->product as $key => $pr){
			if($key == $k){
				
				$session->totalPrice -=  $pr->price;
				$session->totalLowPrice -=  $pr->lowPrice;
				$session->productCount -=  $pr->count;
				
				unset($session->product[$key]);
			}
		}
	}
	
	public function getBasketContent(){
		
		$session = new Zend_Session_Namespace("basket");
		$totalPrice = 0;
		$totalLowPrice = 0;
		$totalProductCount = 0;
		
		if(count($session->product) > 0){
			
			foreach($session->product as $pr){
				$totalPrice += $pr->price;
				$totalLowPrice += $pr->lowPrice;
				$totalProductCount += $pr->count;
			}
			
		}
		
		return array(
			"totalPrice"    => $totalPrice,
		    "totalLowPrice" => $totalLowPrice,
			"products"      => $session->product,
			"productCount"  => $totalProductCount
		);
		
	}
	public function emptyBasket(){
	
		$session = new Zend_Session_Namespace("basket");
		$session->product = array();
		$session->productTotalPrice    = 0;
		$session->productTotalLowPrice = 0;
	
	}
	
	public function plusOrderCount($count){
		
		$product = new Eshop_Models_Product();
		$product->updateOrderCount($this->productID,$count);
		
	}
	
	public function plusViewCount(){
		
		$product = new Eshop_Models_Product();
		$product->updateView($this->productID);
		
	}
	
}

?>