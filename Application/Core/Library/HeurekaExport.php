<?php

 class Library_HeurekaExport {

 	public function generateXML($domain,$lang = "cz"){
 		
 		$translate = Zend_Registry::get("Zend_Translate");
		$translate->setLocale($lang);

 		$delivery  = $translate->translate("VLASTNI_PREPRAVA");
 		
		$content = '<?xml version="1.0" encoding="utf-8"?>';
		$content .= "\n<SHOP>";
		
 		$product 		= new Eshop_Models_Product();
 		$allProducts 	= $product->getXmlFeed($lang);
 		
 		$heurekaData 	= array();
 				 		 		 		
		foreach($allProducts as $value){
						
			$deliveryDate = 33;
			if($value->productStatusID)$deliveryDate = 0;
			
			$category = $value->categories;
			if(!empty($value->hCat))$category = $value->hCat;
			
			$linkNiceTitle = explode("|",$value->categoriesNiceTitle);
			
 			$data 			= new stdClass();
 			
 			$decimal = 0;
 			if($lang == "sk")$decimal = 2;
			
			$data->productID 	= $value->productID;
			$data->product 		= $value->hTitle;
			$data->productName 	= $value->hpTitle;
			$data->description 	= $value->hText;
			$data->url 			= $domain."/".$linkNiceTitle[0]."/$value->productID-".$value->niceTitle;
			$data->imgUrl 		= $domain."/Public/Images/Product/mala-$value->mainPhoto";
			$data->priceVat 	= number_format($value->price,$decimal,"",""); //cena s DPH
			$data->categoryText	= $category;
			$data->deliveryDate = $deliveryDate;
			$data->heurekaCPC 	= "1";	// kolik jsme ochotni dat maximalne za proklik
			$data->linkTitle    = $linkNiceTitle[0];
			$data->hManu        = $value->hManu;
				
			$heurekaData[] = $data;
					  
		}		
		
		foreach($heurekaData as $val){

			//if(!empty($val->productName) && !empty($val->product) && !empty($val->description)){
				$content .= "\n<SHOPITEM>";
					$content .= "\n<ITEM_ID>$val->productID</ITEM_ID>";
					$content .= "\n<PRODUCTNAME>$val->productName</PRODUCTNAME>";
					$content .= "\n<PRODUCT>$val->product</PRODUCT>";
					$content .= "\n<DESCRIPTION>$val->description</DESCRIPTION>";
					$content .= "\n<URL>$val->url</URL>";
					$content .= "\n<IMGURL>$val->imgUrl</IMGURL>";
					$content .= "\n<PRICE_VAT>$val->priceVat</PRICE_VAT>";
					$content .= "\n<CATEGORYTEXT>$val->categoryText</CATEGORYTEXT>";
					$content .= "\n<DELIVERY_DATE>$val->deliveryDate</DELIVERY_DATE>";
					$content .= "\n<DELIVERY>";
					$content .= "\n<DELIVERY_ID>".$delivery."</DELIVERY_ID>";
					$content .= "\n<DELIVERY_PRICE>0</DELIVERY_PRICE>";
					$content .= "\n<DELIVERY_PRICE_COD>0</DELIVERY_PRICE_COD>";
					$content .= "\n</DELIVERY>";
					$content .= "\n<HEUREKA_CPC>$val->heurekaCPC</HEUREKA_CPC>";
					if(!empty($val->hManu))$content .= "\n<MANUFACTURER>$val->hManu</MANUFACTURER>";
					
				
					
				$content .= "\n</SHOPITEM>";
			//}
		}
		$content .= "\n</SHOP>";	// ukoncime xmlko
		
		$hFile = "heureka_export.xml";
		if($lang == "sk")$hFile = "heureka_export_sk.xml";
		
		//ulozime xml
		$fp = @fopen('./Public/Heureka/'.$hFile,'w');
		
		if(!$fp) {
		    die('Error cannot create XML file');
		}
		fwrite($fp,$content);
		fclose($fp);
	}
	
 }