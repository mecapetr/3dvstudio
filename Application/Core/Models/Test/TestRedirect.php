<?php 
class Models_Test_TestRedirect extends Zend_Controller_Plugin_Abstract{
	
       

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
    	
    	$ip 	= $_SERVER['REMOTE_ADDR'];  
    	$request_url 	= $_SERVER['REQUEST_URI'];	
		$request_domain = $_SERVER['HTTP_HOST'];
		$lang 		= new Zend_Session_Namespace("lang");
		
		if($lang->lang == "cz"){
			$wwwURL = "http://www.sedacky-nabytek.cz";
			$URL = "http://sedacky-nabytek.cz";
		}else{
			$wwwURL = "http://www.sedacky-nabytok.sk";
			$URL = "http://sedacky-nabytok.sk";
		}
		
		$locationWww =  $wwwURL;

		$wwwURL .= $request_url;
		$URL .= $request_url;

		//print_r($wwwURL);
		//print_r("</br>");
		//print_r($URL);

		$product 		= new Eshop_Models_Product();
		$productLang	= new Eshop_Models_ProductLang();
		$linkLang 		= new Content_Models_LinkLang();
		$link 			= new Content_Models_Link();
		
		$productLangData = $productLang->getOneRow("oldURL = '$wwwURL' OR oldURL = '$URL'");
		if(!empty($productLangData)){
			
			$productData = $product->getProductWithTopCategory($lang->lang,"P.productID = $productLangData->productID");
			$location = $locationWww . "/" . $productData->linkNiceTitle . "/" . $productData->productID . "-" . $productData->niceTitle;
			
			//zamezime smycce v presmerovani
			if($location != $wwwURL){
				header ('HTTP/1.1 301 Moved Permanently');
  				header ('Location: ' . $location);
			}
		}else{
			
			$linkLangData = $linkLang->getOneRow("oldURL = '$wwwURL' OR oldURL = '$URL'");

			if(!empty($linkLangData)){
				
				
				$linkData = $link->getLinkWithLang($lang->lang,"L.linkID = $linkLangData->linkID");
				$parentData = "";
				$parentParentData = "";
				if($linkData->parentID != 0){
					$parentData = $link->getLinkWithLang($lang->lang,"L.linkID = $linkData->parentID");

					if($parentkData->parentID != 0){
						$parentParentData = $link->getLinkWithLang($lang->lang,"L.linkID = $parentkData->parentID");
							
					}
				}
				
				$location = $locationWww; 
				if(!empty($parentParentData)){
					$location .= "/" . $parentParentkData->niceTitle;
				}
				if(!empty($parentData)){
					$location .= "/" . $parentData->niceTitle;
				}

				$location .= "/" . $linkData->niceTitle;
				
				/*
				echo $location."<br />"; 
				echo $wwwURL."<br />";
				echo $URL."<br />";
				*/
				//zamezime smycce v presmerovani
				if($location != $wwwURL){
					header ('HTTP/1.1 301 Moved Permanently');
					header ('Location: ' . $location);
				}
			}
		}
	
		
		

			
    }
    
}
  
 ?>