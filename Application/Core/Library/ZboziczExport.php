<?php

 class Library_ZboziczExport {

 	public function generateXML($domain,$lang = "cz"){
 		
 		$translate = Zend_Registry::get("Zend_Translate");
 		$translate->setLocale($lang);
 		
 		
		$content = '<?xml version="1.0" encoding="utf-8"?>';
		$content .= "\n<SHOP xmlns=\"http://www.zbozi.cz/ns/offer/1.0\">";
		
 		
		$product 		= new Eshop_Models_Product();
 		$allProducts 	= $product->getXmlFeed($lang);
 		
 		$feedData 	= array();
 				 		 		 		
		foreach($allProducts as $value){
						
			$deliveryDate = 10;
			if($value->productStatusID)$deliveryDate = 0;
			
 			$data = new stdClass();
 			
 			$category = $value->categories;
 			if(!empty($value->hCat))$category = $value->hCat;
 				
 			$linkNiceTitle = explode("|",$value->categoriesNiceTitle);
			
			$data->productID 	= $value->productID;
			$hTitle             = $value->hTitle;
			if(!empty($hTitle)){

				$hTitle             = explode(",",$hTitle);
				
				if(count($hTitle) > 1){
					$hTit               = explode(" ",trim($hTitle[1]));
					$pre                = $hTit[0];
					unset($hTit[0]);					
					$data->product 		= implode(" ",$hTit)." ".$pre;
					
					//if(!empty($hTitle[2]) && $hTitle[2] != " doprava zdarma" && $hTitle[2] != "doprava zdarma")$data->product .= ",".$hTitle[2]; 
				}
			}else{
				$data->product = $hTitle;
			}
		
			$decimal = 0;
			if($lang == "sk")$decimal = 2;
			
			$data->productName 	= $value->hpTitle;
			$data->description 	= $value->hText;
			$data->url 			= $domain."/".$linkNiceTitle[0]."/".$value->productID."-".$value->niceTitle;
			$data->imgUrl 		= $domain."/Public/Images/Product/mala-$value->mainPhoto";
			$data->priceVat 	= number_format($value->price,$decimal,"",""); //cena s DPH
			$data->categoryText	= $category;
			$data->deliveryDate = $deliveryDate;
			$data->heurekaCPC 	= "1";	// kolik jsme ochotni dat maximalne za proklik

			$feedData[] = $data;
					  
		}		
		
		foreach($feedData as $val){

			if(!empty($val->product) && !empty($val->description)){
				$content .= "\n<SHOPITEM>";
					
					$content .= "\n<ITEM_ID>$val->productID</ITEM_ID>";
					$content .= "\n<PRODUCTNAME>$val->product</PRODUCTNAME>";
					$content .= "\n<DESCRIPTION>$val->description</DESCRIPTION>";
					$content .= "\n<URL>$val->url</URL>";
					$content .= "\n<ITEM_TYPE>new</ITEM_TYPE>";
					$content .= "\n<IMGURL>$val->imgUrl</IMGURL>";
					$content .= "\n<PRICE_VAT>$val->priceVat</PRICE_VAT>";
					$content .= "\n<CATEGORYTEXT>$val->categoryText</CATEGORYTEXT>";
					$content .= "\n<DELIVERY_DATE>$val->deliveryDate</DELIVERY_DATE>";
					$content .= "\n<EXTRA_MESSAGE>free_delivery</EXTRA_MESSAGE>";

				$content .= "\n</SHOPITEM>";
			}
		}
		$content .= "\n</SHOP>";	// ukoncime xmlko

		$hFile = "zbozi_export.xml";
		if($lang == "sk")$hFile = "zbozi_export_sk.xml";
		
		//ulozime xml
		$fp = @fopen('./Public/Zbozicz/'.$hFile,'w');
		
		if(!$fp) {
		    die('Error cannot create XML file');
		}
		fwrite($fp,$content);
		fclose($fp);
	}
	
 }