<?php

 class Library_GoogleExport {

 	public function generateXML($domain,$lang = "cz"){
 		
 		$translate = Zend_Registry::get("Zend_Translate");
		$translate->setLocale($lang);
 		
 		$delivery  = $translate->translate("VLASTNI_PREPRAVA");
 		
		$content = '<?xml version="1.0" encoding="utf-8"?>';
		$content .= "\n<rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\">";
		$content .= "\n<channel>";
		$content .= "\n<title>Seznam produktů firmy Sedačky nábytek</title>";
		$content .= "\n<link>http://www.sedacky-nabytek.cz</link>";
		$content .= "\n<description>Veškeré produkty firmy Sedačky nábytek</description>";
		
 		
		$product 		= new Eshop_Models_Product();
 		$allProducts 	= $product->getXmlFeed($lang,"google");
 		
 		$feedData 	= array();
 	 		 		 		
		foreach($allProducts as $value){
			
						
			$deliveryDate = 33;
			if($value->productStatusID)$deliveryDate = 0;
			
			$category = $value->categories;
			if(!empty($value->hCat))$category = $value->hCat;
			
			$linkNiceTitle = explode("|",$value->categoriesNiceTitle);
			
 			$data 			= new stdClass();
			
			$data->productID 	= $value->productID;
			$productName 	    = $value->hpTitle;
			$productName        = explode(",",$productName);
			
			if(!empty($productName[1]))$productName = $productName[1];
			else $productName = $productName[0];
			
			$mainPhoto = "";
			$moreFiles = array();
			$files = explode("|",$value->files);
			
			if(count($files) > 1){
				foreach($files as $fi){
					
					$imgData = explode("~",$fi);

					if(isset($imgData[1]) && $imgData[1] == 1){
						$mainPhoto = $imgData[0];
					}elseif(isset($imgData[0])){
						$moreFiles[] = $imgData[0];
					}
				}
				
			}else{
				$imgData = explode("~",$files[0]);
				$mainPhoto = $imgData[0];
			}
			
			$decimal = 0;
			if($lang == "sk")$decimal = 2;
						
			$data->productName  = $productName;
			$data->description 	= $value->hText;
			$data->url 			= $domain."/".$linkNiceTitle[0]."/$value->productID-".$value->niceTitle;
			$data->imgUrl 		= $domain."/Public/Images/Product/mala-$mainPhoto";
			$data->moreFiles 	= $moreFiles;
			$data->priceVat 	= number_format($value->price,$decimal,"",""); //cena s DPH;
			$data->categoryText	= $category;
			$data->deliveryDate = $deliveryDate;
			$data->heurekaCPC 	= "1";	// kolik jsme ochotni dat maximalne za proklik
			$data->linkTitle    = $linkNiceTitle[0];
			$data->hManu        = $value->hManu;
								
			$feedData[] = $data;
					  
		}	

		$currency = "CZK";
		if($lang == "sk")$currency = "EUR";
		
		$this->googleContent($feedData,$currency,$lang,$domain,$content);
		$this->facebookContent($feedData,$currency,$lang,$domain,$content);
		
	}
	
	private function facebookContent($feedData,$currency,$lang,$domain,$content){
	
		foreach($feedData as $val){
	
			if(!empty($val->productName) && !empty($val->description)){
				$content .= "\n<item>";
				$content .= "\n<g:id>$val->productID</g:id>";
				$content .= "\n<title>$val->productName</title>";
				$content .= "\n<g:title>$val->productName</g:title>";
				$content .= "\n<description>$val->description</description>";
				$content .= "\n<g:description>$val->description</g:description>";
				$content .= "\n<link>$val->url</link>";
				$content .= "\n<g:link>$val->url</g:link>";
				$content .= "\n<g:mobile_link>$val->url</g:mobile_link>";
				$content .= "\n<g:image_link>$val->imgUrl</g:image_link>";
					
				if(!empty($val->moreFiles)){
					$cou = 1;
					foreach($val->moreFiles as $f){
						if($cou <= 10)$content .= "\n<g:additional_image_link>$domain/Public/Images/Product/mala-$f</g:additional_image_link>";
						$cou++;
					}
				}
				$content .= "\n<g:price>$val->priceVat $currency</g:price>";
				$content .= "\n<g:brand>$val->hManu</g:brand>";
				$content .= "\n<g:gtin>".str_pad($val->productID, 12, '0', STR_PAD_LEFT)."</g:gtin>";
				$content .= "\n<g:mpn>".str_pad($val->productID, 12, '0', STR_PAD_LEFT)."</g:mpn>";
				$content .= "\n<g:product_type>$val->categoryText</g:product_type>";
				$content .= "\n<g:condition>new</g:condition>";
				$content .= "\n<g:availability>in stock</g:availability>";
				$content .= "\n<g:identifier_exists>FALSE</g:identifier_exists>";
				$content .= "\n<g:shipping>";
				$content .= "\n<g:country>CZ</g:country>";
				$content .= "\n<g:price>0,00 CZK</g:price>";
				$content .= "\n</g:shipping>";
		
				$content .= "\n</item>";
			}
		}
		$content .= "\n</channel>";
		$content .= "\n</rss>";
	
		$hFile = "facebook_export.xml";
		if($lang == "sk")$hFile = "facebook_export_sk.xml";
	
		file_put_contents('./Public/Facebook/'.$hFile, $content);
	
	}
	
	private function googleContent($feedData,$currency,$lang,$domain,$content){
		
		foreach($feedData as $val){
		
			if(!empty($val->productName) && !empty($val->description)){
				$content .= "\n<item>";
				$content .= "\n<g:id>$val->productID</g:id>";
				$content .= "\n<title>$val->productName</title>";
				$content .= "\n<description>$val->description</description>";
				$content .= "\n<link>$val->url</link>";
				$content .= "\n<g:mobile_link>$val->url</g:mobile_link>";
				$content .= "\n<g:image_link>$val->imgUrl</g:image_link>";
					
				if(!empty($val->moreFiles)){
					$cou = 1;
					foreach($val->moreFiles as $f){
						if($cou <= 10)$content .= "\n<g:additional_image_link>$domain/Public/Images/Article/mala-$f</g:additional_image_link>";
						$cou++;
					}
				}
				$content .= "\n<g:price>$val->priceVat $currency</g:price>";
				$content .= "\n<g:product_type>$val->categoryText</g:product_type>";
				$content .= "\n<g:condition>new</g:condition>";
				$content .= "\n<g:availability>in stock</g:availability>";
				$content .= "\n<g:identifier_exists>FALSE</g:identifier_exists>";
				$content .= "\n<g:shipping>";
				$content .= "\n<g:country>CZ</g:country>";
				$content .= "\n<g:price>0,00 CZK</g:price>";
				$content .= "\n</g:shipping>";
						
				$content .= "\n</item>";
			}
		}
		$content .= "\n</channel>";
		$content .= "\n</rss>";
		
		$hFile = "google_export.xml";
		if($lang == "sk")$hFile = "google_export_sk.xml";
		
		file_put_contents('./Public/Google/'.$hFile, $content);
		
	}
	
 }