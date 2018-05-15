<?php
class EshopW_ObjednavkaController extends Library_WebBase
{
		
	var  $totalPrice;
	var  $totalLowPrice;
	var  $customerID;
	var  $orderNumber;
	var  $cetelemProducts;
	
	function init(){		
		$this->translate = Zend_Registry::get("Zend_Translate");

		$this->setDefault();
		$this->getMenu();
		$this->getCategoryMenu();
		$this->getSideCategoryMenu();
	}

	function indexAction(){
        $filter = new Zend_Filter_StripTags();
        
        $session = new Zend_Session_Namespace("basket");
        $this->view->lastProductUrl = $session->lastProductUrl;
        $this->view->products       = $session->product;
        $this->view->totalPrice     = number_format($session->productTotalPrice,$this->wholeLangData->decimal,","," ")." ".$this->wholeLangData->currencySign;
        $this->view->totalLowPrice  = number_format($session->productTotalLowPrice,2,","," ")." ".$this->wholeLangData->currencySign;
        
        $country = new Models_Country();
        $this->view->allCountries = $country->getAllItems("active = 1");
		
		$paymentType = new Eshop_Models_PaymentType();
		$lang = $this->concreteLang->lang;
		$this->view->paymentTypes = $paymentType->getAllPayments($lang);
		
		$shippingType = new Eshop_Models_ShippingType();
		$shippingType = $shippingType->getAllShipping($lang);
		
		foreach($shippingType as $st){
			
			if($st->price == "0.00")$st->priceString = $this->translate->translate("ZDARMA");
			else $st->priceString = number_format($st->price,$this->wholeLangData->decimal,","," ")." ".$this->wholeLangData->currencySign;
			
		}
		
		$this->view->shippingType = $shippingType;
		
		$option = new Eshop_Models_Option();
		$link   = new Content_Models_Link();
		
		$optData  = $option->getOneRow("label = 'condition_linkID'");
		$linkData = $link->getOneRow("linkID = '$optData->value'");
		
		
		$this->view->url = $this->implodeUrlLink($linkData,$link);
				
						
	}
	
	function orderedAction(){
				
		if($this->_request->getPost("send") == 1){	

			$filter = $this->setData();
			if($filter->isValid("email")){
				if($filter->isValid("phone")){
					
					
						$session = new Zend_Session_Namespace("basket");
						if($session->product && count($session->product) > 0){
						
							$this->country   = explode("~",$this->country);
							$this->countryID = $this->country[0];
							$this->country   = $this->country[1];
									
							$this->countryDelivery   = explode("~",$this->countryDelivery);
							$this->countryDeliveryID = $this->countryDelivery[0];
							$this->countryDelivery   = $this->countryDelivery[1];
							
							$this->customerID  = $this->addCustomer();
							$this->orderNumber = $this->addOrderToDb();
							
							if(!empty($this->orderNumber)){
								
								$this->sendOrderEmail("client");
								$this->sendOrderEmail("eshop");
								
								$this->view->message = $this->translate->translate('Vaše objednávka byla uložena v systému. Budeme Vás kontaktovat pro dokončení objednávky.').'<br /> '.$this->translate->translate('DĚKUJEME ZA VAŠI OBJEDNÁVKU.');
	              $this->view->allProducts = $session->product;
                $this->view->orderNumber = $this->orderNumber;
                $this->view->orderID     = $this->orderID;
                $this->view->totalPriceWhithoutShippingAndPayment      =  $this->totalPriceWhithoutShippingAndPayment;
                $this->view->totalPriceWhithoutShippingAndPaymentNoVat =  $this->totalPriceWhithoutShippingAndPaymentNoVat;
                
  
								if($this->concreteLang->lang == "cz" && $this->payment == "4"){
									$this->view->repayment = '<img alt="" src="/Public/Images/Layout/Content/essox_repayment.png" /><br />'.$this->translate->translate('Zvolili jste nákup na splátky, můžete tak ihned vyplnit žádost o spotřebitelský úvěr společnosti Essox.').' <br /> '.$this->translate->translate('Formulář kalkulačky splátek a online žádosti o úvěr otevřete kliknutím').' <a class="repayment" href="" lang="'.$this->concreteLang->lang.'" data="'.$this->getEssoxLiteUrl("", "", "", $this->totalPrice).'" title="'.$this->translate->translate('Kalkulačka splátek').'"><strong>'.$this->translate->translate('ZDE').'</strong></a><script>$(document).ready(function() {$("a.repayment").click();});</script>';
								}elseif($this->concreteLang->lang == "sk" && $this->payment == "5"){
									
									$cetelemProducts = implode(",",$this->cetelemProducts);
									if(strlen($cetelemProducts) > 30){
										$cetelemProducts = substr($cetelemProducts,0,27)."...";
									}
	
									$this->view->repayment = '<a href="" lang="'.$this->concreteLang->lang.'" data="/cetelem/'.$this->totalPrice.'/'.$cetelemProducts.'/'.$this->orderNumber.'/1" title="Kalkulačka splátok" class="repayment rep"><img alt="" src="/Public/Images/Layout/Content/cetelem_rep.png" /></a><br />'.$this->translate->translate('Zvolili ste nákup na splátky, môžete tak ihneď vyplniť žiadosť o úver spoločnosti Cetelem.').' <br /> '.$this->translate->translate('Formulár kalkulačky splátok a online žiadosti o úver otvoríte kliknutím').' <a href="" lang="'.$this->concreteLang->lang.'" data="/cetelem/'.$this->totalPrice.'/'.$cetelemProducts.'/'.$this->orderNumber.'/1" title="Kalkulačka splátok" class="repayment"><strong>'.$this->translate->translate('ZDE').'</strong></a><script>$(document).ready(function() {$("a.rep").click();});</script>';
								}
								
								$prodFun = new Eshop_Library_ProductFunctions($this->_request, $this->_response);
								$prodFun->emptyBasket();
								
								$this->view->basketContent = $prodFun->getBasketContent();
							}
						}else{
							
							$this->view->error = $this->translate->translate('V košíku nemáte žádné zboží');
						}
					
					
		
				}
					
			}
				
		}else{
			$this->_redirect("/".$this->translate->translate('nakupni-kosik'));
		}
		
	}
	
	private function getEssoxLiteUrl($URL, $userName, $code, $price)
	{
		$userName = "sedackynabytekeshop";                                 // doplnit uživatelské jméno podle implementačního protokolu
		$code = "C36DxRSKOpDk62Y";                                     // doplnit přístupový kód podle implementačního protokolu
		$URL = "https://eshop.essox.cz";                                      // toto pole by mělo být předem definováno (pro kontrolu je opět v impl.protokolu)
	
		// tuto proměnnou napojte na celkovou cenu košíku, obvykle $TOTAL
		$seed = time().rand();
		$hash = sha1($userName.'#'.$code.'#'.$price.'#'.$seed);
		return $URL.'/Login.aspx?a='.$userName.'&b='.$price.'&c='.$seed.'&d='.$hash;
	}
	
	private function addCustomer(){
		
		$customer = new Eshop_Models_Customer();
		$custData = $customer->getOneRow("email = '$this->email'");
		
		$storeAreaID = 1;
		if(!empty($this->zip)){
			$char = substr($this->zip, 0);
			$storeArea = new Eshop_Models_StoreArea();
			$saData = $storeArea->getOneRow("zipIdentifier LIKE '%$char%'");
			if($saData)$storeAreaID = $saData->storeAreaID;
		}
		
		if(empty($this->newsletter))$this->newsletter = 0;
		
		if(!$custData){
			
			$time = time();
			$currentdate = date("Y-m-d H:i:s",$time);
					
			$customer->insertData(array(
				"customerTypeID" =>    1,
				"storeAreaID" =>       $storeAreaID,
				"countryID" =>         $this->countryID,
				"name" =>              $this->name,
				"surname" =>           $this->surname,
				"company" =>           $this->nameCompany,
				"email" =>             $this->email,
				"tel" =>               $this->phone,
				"street" =>            $this->street,
				"city" =>              $this->city,
				"zip" =>               $this->zip,
				"ic" =>                $this->ic,
				"dic" =>               $this->dic,
				"deliveryCompany" =>   "",
				"deliveryName" =>      $this->nameDelivery,
				"deliverySurname" =>   $this->surnameDelivery,
				"deliveryEmail" =>     "",
				"deliveryTel" =>       "",
				"deliveryStreet" =>    $this->streetDelivery,
				"deliveryCity" =>      $this->cityDelivery,
				"deliveryZip" =>       $this->zipDelivery,
				"deliveryCountryID" => $this->countryDeliveryID,
				"dateAdd" =>           $currentdate,
				"sendNewsletter" =>    $this->newsletter
			));
			
			return $customer->lastID;
			
		}else{
			
			$customer->updateData(array(
				"storeAreaID" =>       $storeAreaID,
				"countryID" =>         $this->countryID,
				"name" =>              $this->name,
				"surname" =>           $this->surname,
				"company" =>           $this->nameCompany,
				"tel" =>               $this->phone,
				"street" =>            $this->street,
				"city" =>              $this->city,
				"zip" =>               $this->zip,
				"ic" =>                $this->ic,
				"dic" =>               $this->dic,
				"deliveryCompany" =>   "",
				"deliveryName" =>      $this->nameDelivery,
				"deliverySurname" =>   $this->surnameDelivery,
				"deliveryStreet" =>    $this->streetDelivery,
				"deliveryCity" =>      $this->cityDelivery,
				"deliveryZip" =>       $this->zipDelivery,
				"deliveryCountryID" => $this->countryDeliveryID,
				"sendNewsletter" =>    $this->newsletter
			),"customerID = '$custData->customerID'");
			
			return $custData->customerID;

		}
		
	}
	
	private function addOrderToDb(){
		
		$order = new Eshop_Models_Order();
						
		$time = time();
		$currentdate = date("Y-m-d H:i:s",$time);
		
		$order->getDefaultAdapter()->beginTransaction();
		try{
			
			$orderCount    = $order->getCountForUpdate("DATE_FORMAT(orderDate,'%Y-%m-%d') = '".date("Y-m-d",$time)."'");
			$signature     = "O";			
			$orderNumber   = $signature."-".date("dmY",$time)."-".str_pad(($orderCount + 1), 5, '0', STR_PAD_LEFT);
			
			$storeAreaID = 1;
			if(!empty($this->zip)){
				$char = substr($this->zip, 0);
				$storeArea = new Eshop_Models_StoreArea();
				$saData = $storeArea->getOneRow("zipIdentifier LIKE '%$char%'");
				if($saData)$storeAreaID = $saData->storeAreaID;
			}
			
			$order->insertData(array(
				"adminUserID" =>       0,
				"customerID"  =>       $this->customerID,
				"paymentTypeID" =>     $this->payment,
				"shippingTypeID" =>    $this->shipping,
				"storeAreaID" =>       $storeAreaID,
				"supplierID" =>        0,
				"currencyID" =>        $this->wholeLangData->currencyID,
				"orderNumber" =>       $orderNumber,
				"orderDate" =>         $currentdate,
				"completeOrder" =>     0,
				"orderCustomerType" => -1,
				"fetchUp" =>           0,
				"fetchUpText" =>       "",
				"description" =>       $this->text,
				"company" =>           $this->nameCompany,
				"name" =>              $this->name,
				"surname" =>           $this->surname,
				"email" =>             $this->email,
				"tel" =>               $this->phone,
				"street" => 		   $this->street,
				"city" =>              $this->city,
				"zip" =>               $this->zip,
				"ic" =>                $this->ic,
				"dic" =>               $this->dic,
				"countryID" =>         $this->countryID,
				"deliveryCompany" =>   "",
				"deliveryName" =>      $this->nameDelivery,
				"deliverySurname" =>   $this->surnameDelivery,
				"deliveryEmail" =>     "",
				"deliveryTel" =>       "",
				"deliveryStreet" =>    $this->streetDelivery,
				"deliveryCity" =>      $this->cityDelivery,
				"deliveryZip" =>       $this->zipDelivery,
				"deliveryCountryID" => $this->countryDeliveryID,
				"dateAdd" =>           $currentdate

			));
			
			$lastID = $order->lastID;
      $this->orderID = $lastID;
      
			$this->insertOrderProducts($lastID);
			
			$order->getDefaultAdapter()->commit();
			
			return $orderNumber;

		}catch(Exception $e){
			$order->getDefaultAdapter()->rollBack();
			$this->view->error = $e->getMessage(); 
		}
		
	}
	
	private function insertOrderProducts($orderID){
		
		$orderItem = new Eshop_Models_OrderItem();
		$prodFun   = new Eshop_Library_ProductFunctions($this->_request, $this->_response);

				
		$session = new Zend_Session_Namespace("basket");
		foreach($session->product as $pr){
			
			if(empty($pr->cover1Title))$pr->cover1Title = "";
			if(empty($pr->cover2Title))$pr->cover2Title = "";
			
			if(empty($pr->cover1Subcode))$pr->cover1Subcode = "";
			if(empty($pr->cover2Subcode))$pr->cover2Subcode = "";
			
			$orderItem->insertData(array(
				"orderID"        => $orderID, 
				"eshopProductID" => $pr->epID, 
				"category"       => "", 
				"title"          => $pr->productTitle, 
				"count"          => $pr->count,  
				"text"           => "", 
				"priceNoVat"     => $pr->lowPrice, 
				"price"          => $pr->price, 
				"store"          => 0, 
				"side"           => $pr->side, 
				"cover1Title"    => $pr->cover1Title, 
				"cover2Title"    => $pr->cover2Title,
				"cover1"         => $pr->cover1Subcode, 
				"cover2"         => $pr->cover2Subcode, 
				"productCode"    => ""
			));
			
			if($this->concreteLang->lang == "sk" && $this->payment == "5")$this->cetelemProducts[] = $pr->productTitle;
			
			$prodFun->productID = $pr->productID;
			$prodFun->plusOrderCount($pr->count);
		}
	}
	
	private function sendOrderEmail($type){
		
		$from = $this->regEmail[$this->concreteLang->lang];
		$name = $this->regName[$this->concreteLang->lang];

		$subject = $this->getSubject($type);		
		$text    = $this->getText($type);
		
		$toEmail = $this->email;
		if($type == "eshop")$toEmail = $from;
		$this->sendMail($from, $name, array($toEmail), $subject, $text);
		
	}
	
	private function getSubject($type){
		
		$subject = "";
		switch($type){
			
			case "client": $subject = $this->translate->translate("Potvrzení objednávky");break;
			case "eshop":  $subject = $this->translate->translate("Nová objednávka - ").$this->regName[$this->concreteLang->lang];break;
			
		}
		
		return $subject;
		
	}
	
	private function getText($type){
		
		$name = ",";
		if(!empty($this->name) || !empty($this->surname)){
			$name = " ".$this->name;
			if(!empty($this->surname))$name .= " ".$this->surname.",";
			else $name .= ",";
		}
		
		$text = "";		
		if($type == "client"){
			$text .= '<p class="color:#333333;margin:15px 0px;">'.$this->translate->translate('Dobrý den').$name.' <br /> '.$this->translate->translate('děkujeme za Vaši objednávku.').' </p><p class="margin:15px 0px;"><strong style="color:#b02423;font-size:16px;">'.$this->translate->translate('Pro dokončení objednávky Vás budeme kontaktovat.').'</strong>';
			$text .= '<p class="color:#333333;margin:15px 0px;">'.$this->translate->translate("Pokud máte jakékoliv dotazy nebo si s něčím nevíte rady, neváhejte nás kontaktovat.").' <br />';
			$text .= "<strong>Tel.</strong>: ".$this->regTel[$this->concreteLang->lang]." <br/>";
			$text .= "<strong>Email</strong>: ".$this->regEmail[$this->concreteLang->lang]."</p>";
			$text .= '<p class="color:#333333;margin:15px 0px;">'.$this->translate->translate("Děkujeme za objednávku.").'</p>';
		}
		
		if($type == "eshop"){
			$text .= '<h2 style="color:#b02423;">Nová objednávka</h2>';
		}
		
		$text .= '<hr style="height:1px;background-color:#ddd;border:0px none;margin:20px 0px;" />';
		$text .= $this->getContact();
		$text .= $this->getProducts();
		
		return $text;
		
	}
	
	private function getContact(){
		
		$content = '
		
			<table style="width:100%;border-collapse:collapse;">
				
				<tbody>
					<tr><td colspan="2"><h2 style="font-size:15px;color:#333;padding:0px 0px 10px 0px;margin-top:0px;margin-bottom:0px;">'.$this->translate->translate('Číslo objednávky').': '.$this->orderNumber.'</h2></td></tr>
					<tr>
						<td style="width:50%;margin-right:1px;"><h2 style="font-size:15px;background-color:#b02423;color:#fff;padding:10px;margin-top:0px;margin-bottom:0px;">'.$this->translate->translate("Fakturační adresa").'</h2></td>
						<td style="width:50%;margin-left:1px;"><h2 style="font-size:15px;background-color:#b02423;color:#fff;padding:10px;margin-top:0px;margin-bottom:0px;">'.$this->translate->translate("Doručovací adresa").'</h2></td>
					</tr>
					<tr>
						<td style="vertical-align:top;padding:10px;">
							<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Jméno a příjmení').':</span> '.$this->name.' '.$this->surname.'</p>
							<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Ulice a č.p.').':</span> '.$this->street.'</p>
							<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Město').':</span> '.$this->city.'</p>
							<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('PSČ').':</span> '.$this->zip.'</p>
							<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Stát').':</span> '.$this->country.'</p>
						
							<hr style="margin:20px 0px;height:1px;background-color:#ddd;border:0px none;" />
							
							<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Email').':</span> '.$this->email.'</p>
							<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Telefon').':</span> '.$this->phone.'</p>
							';
							
							if($this->companyExtra){
								$content .= '
								<hr style="margin:20px 0px;height:1px;background-color:#ddd;border:0px none;" />
								<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Společnost').':</span> '.$this->nameCompany.'</p>
								<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('IČ').':</span> '.$this->ic.'</p>
								<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('DIČ').':</span> '.$this->dic.'</p>';
							}
						$content .= '
						</td>
						
						<td style="vertical-align:top;padding:10px;">';
							if($this->deliveryExtra){
								
								$content .= '
									<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Jméno a příjmení').':</span> '.$this->nameDelivery.' '.$this->surnameDelivery.'</p>
									<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Ulice a č.p.').':</span> '.$this->streetDelivery.'</p>
									<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Město').':</span> '.$this->cityDelivery.'</p>
									<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('PSČ').':</span> '.$this->zipDelivery.'</p>
									<p><span style="width:130px;font-weight:bold;display:inline-block;">'.$this->translate->translate('Stát').':</span> '.$this->countryDelivery.'</p>
								';
								
							}else{
								$content .= '
									<p style="text-align:center;">'.$this->translate->translate('Stejná jako fakturační.').'</p>
								';
								
							}
							
							
			            $content .= '
						</td>
						
					<tr>
				</tbody>
			
			</table>

		';
		return $content;
		
	}
	
	private function getProducts(){
	
		$lang = $this->concreteLang->lang;
		
		$paymentTypeLang = new Eshop_Models_PaymentTypeLang();
		$paymentData = $paymentTypeLang->getOneRow("paymentTypeID = '$this->payment' AND lang = '$lang'");
		
		$shippingTypeLang = new Eshop_Models_ShippingTypeLang();
		$shippingData = $shippingTypeLang->getOneRow("shippingTypeID = '$this->shipping' AND lang = '$lang'");
	
		$session = new Zend_Session_Namespace("basket");
		
		$content = '
			<h2 style="font-size:15px;background-color:#b02423;color:#fff;padding:10px;margin-top:0px;margin-bottom:15px;">'.$this->translate->translate("Objednané zboží").'</h2>
			<table style="width:100%;border-collapse:collapse;">
			<thead>
				<tr>
					<th style="padding:10px;text-align:left;">'.$this->translate->translate("Název").'</th>
					<th style="padding:10px;text-align:center;">'.$this->translate->translate("Vzory zboží").'</th>
					<th style="padding:10px;text-align:center;">'.$this->translate->translate("Orientace").'</th>
					<th style="padding:10px;text-align:center;">'.$this->translate->translate("Počet").'</th>
					<th style="padding:10px;text-align:center;">'.$this->translate->translate("Cena bez DPH").'</th>
					<th style="padding:10px;text-align:right;">'.$this->translate->translate("Cena s DPH").'</th>
				</tr>
			</thead>
			<tbody>
		';
		
		$totalPrice = $totalLowPrice = 0;
		
		foreach($session->product as $pr){
			
			$content .= '<tr>
				<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:left;">'.$pr->productTitle.'</td>
				<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:center;">
					<p style="margin-top:0px;font-size: 11px;"><strong>'.$this->translate->translate($pr->cover1Title).'</strong><br />'.$pr->cover1Subcode.'</p>
					<p style="margin-bottom:0px;font-size: 11px;"><strong>'.$this->translate->translate($pr->cover2Title).'</strong><br />'.$pr->cover2Subcode.'</p>
				</td>
				<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:center;">'.$this->translate->translate($pr->side).'</td>
				<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:center;">'.$pr->count.'</td>
				<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:center;">'.$pr->lowPriceString.'</td>
				<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:right;">'.$pr->priceString.'</td>
			</tr>';
			
			$totalPrice += $pr->price;
			$totalLowPrice += $pr->lowPrice;
			
		}
    
    $this->totalPriceWhithoutShippingAndPayment = $totalPrice;
    $this->totalPriceWhithoutShippingAndPaymentNoVat = $totalLowPrice;
		
		$content .= '<tr>
			<td colspan="3" style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;border-top:4px solid #ddd;text-align:left;"><strong>'.$this->translate->translate("Způsob dopravy").'</strong>: '.$shippingData->title.'</td>
			<td colspan="3" style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;border-top:4px solid #ddd;text-align:right;">'.number_format($shippingData->price,$this->wholeLangData->decimal,","," ").' '.$this->wholeLangData->currencySign.'</td>
		</tr>';
		
		$totalPrice += $shippingData->price;
		
		$paymentPrice = $paymentData->price;
		
		if($paymentData->percentage == 1){
							
			$paymentPrice = round($totalPrice / 100 * $paymentData->price,2);
			$totalPrice += $paymentPrice;
				
		}else{
			$totalPrice += $paymentData->price;
		}
		
		if($paymentPrice != "0.00"){
			$content .= '<tr>
				<td colspan="3" style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:left;"><strong>'.$this->translate->translate("Způsob platby").'</strong>: '.$paymentData->title.'</td>
				<td colspan="3" style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:right;">'.number_format($paymentPrice,$this->wholeLangData->decimal,","," ").' '.$this->wholeLangData->currencySign.'</td>
			</tr>';
		}else{
			$content .= '<tr>
				<td colspan="6" style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:left;"><strong>'.$this->translate->translate("Způsob platby").'</strong>: '.$paymentData->title.'</td>
			</tr>';
		}
		
		
		$content .= '<tr>
			<td colspan="3" style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:left;"><strong>'.$this->translate->translate("Poznámka k objednávce").'</strong>: '.$this->text.'</td>
		</tr>';
		
		
		
		$content .= '<tr>
			<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;border-top:4px solid #ddd;text-align:left;"><strong>'.$this->translate->translate("Celkem").'</strong></td>
			<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;border-top:4px solid #ddd;text-align:center;"></td>
			<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;border-top:4px solid #ddd;text-align:center;"></td>
			<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;border-top:4px solid #ddd;text-align:center;"></td>
			<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;border-top:4px solid #ddd;text-align:center;"></td>
			<td style="padding:10px;border-top:1px solid #ddd;border-bottom:1px solid #ddd;border-top:4px solid #ddd;text-align:right;">'.number_format($totalPrice,$this->wholeLangData->decimal,","," ").' '.$this->wholeLangData->currencySign.'</td>
		</tr>';

		$content .= '</tbody></table>';
		
		$this->totalPrice    = $totalPrice;
		$this->totalLowPrice = $totalLowPrice;
			
		return $content;
	
	}

	
	private function getData(){
	
		$data = array(
	
				"email" =>              $this->_request->getPost("email"),
				"phone" =>              $this->_request->getPost("phone"),
				"name" =>               $this->_request->getPost("name"),
				"surname" =>            $this->_request->getPost("surname"),
				"street" =>             $this->_request->getPost("street"),
				"city" =>   			$this->_request->getPost("city"),
				"zip" =>   				$this->_request->getPost("zip"),
				"country" =>     		$this->_request->getPost("country"),  
				"deliveryExtra" => 	    $this->_request->getPost("deliveryExtra"),
				"companyExtra" =>       $this->_request->getPost("companyExtra"),
				"nameDelivery" =>       $this->_request->getPost("nameDelivery"),
				"surnameDelivery" =>    $this->_request->getPost("surnameDelivery"),
				"streetDelivery" =>     $this->_request->getPost("streetDelivery"),
				"cityDelivery" =>       $this->_request->getPost("cityDelivery"),
				"zipDelivery" =>        $this->_request->getPost("zipDelivery"),
				"countryDelivery" =>    $this->_request->getPost("countryDelivery"),
				"nameCompany" =>        $this->_request->getPost("nameCompany"),
				"ic" =>                 $this->_request->getPost("ic"),
				"dic"  =>               $this->_request->getPost("dic"),
				"payment"  =>           $this->_request->getPost("payment"),
				"shipping"  =>          $this->_request->getPost("shipping"),
				"text"  =>              $this->_request->getPost("text"),
				"conditions"  =>        $this->_request->getPost("conditions"),
				"newsletter"  =>        $this->_request->getPost("newsletter")
		);
		return $data;
	}
	
	private function setData(){
	
		$filters    = $this->setFilters();
		$validators = $this->setValidators();
		$data       = $this->getData();
		$script		= new Library_Scripts();
		$filter = new Zend_Filter_Input($filters, $validators, $data);
	
		$this->email =             $filter->getUnescaped("email");
		$this->phone =             $filter->getUnescaped("phone");
		$this->name =              $filter->getUnescaped("name");
		$this->surname =           $filter->getUnescaped("surname");
		$this->street =            $filter->getUnescaped("street");
		$this->city =              $filter->getUnescaped("city");
		$this->zip =               $filter->getUnescaped("zip");
		$this->country =           $filter->getUnescaped("country");
		$this->deliveryExtra =     $filter->getUnescaped("deliveryExtra");
		$this->companyExtra =      $filter->getUnescaped("companyExtra");
		$this->nameDelivery =      $filter->getUnescaped("nameDelivery");
		$this->surnameDelivery =   $filter->getUnescaped("surnameDelivery");
		$this->streetDelivery =    $filter->getUnescaped("streetDelivery");
		$this->cityDelivery 	 = $filter->getUnescaped("cityDelivery");
		$this->zipDelivery 		 = $filter->getUnescaped("zipDelivery");
		$this->countryDelivery   = $filter->getUnescaped("countryDelivery");
		$this->nameCompany       = $filter->getUnescaped("nameCompany");
		$this->ic        		 = $filter->getUnescaped("ic");
		$this->dic         		 = $filter->getUnescaped("dic");
		$this->payment         	 = $filter->getUnescaped("payment");
		$this->shipping          = $filter->getUnescaped("shipping");
		$this->text              = $filter->getUnescaped("text");
		$this->conditions        = $filter->getUnescaped("conditions");
		$this->newsletter        = $filter->getUnescaped("newsletter");
	
		return $filter;
	}
	
	
	private function setFilters(){
	
		$filters = array(
	
				"email" =>           'StripTags',
				"phone" =>           'StripTags',
				"name" =>            'StripTags',
				"surname" =>         'StripTags',
				"street" =>          'StripTags',
				"city" =>  		     'StripTags',
				"zip" =>             'StripTags',
				"country" =>         'StripTags',
			    "deliveryExtra" =>   'StripTags',
				"companyExtra" =>    'StripTags',
				"nameDelivery" =>    'StripTags',
				"surnameDelivery" => 'StripTags',
				"streetDelivery" =>  'StripTags',
				"cityDelivery" =>    'StripTags',
				"zipDelivery" =>     'StripTags',
				"countryDelivery" => 'StripTags',
				"nameCompany" =>     'StripTags',
				"ic" =>              'StripTags',
				"dic" =>             'StripTags',
				"payment" =>         'StripTags',
				"shipping" =>        'StripTags',
				"text" =>            'StripTags',
				"conditions" =>      'StripTags',
				"newsletter" =>      'StripTags'
		);
	
		return $filters;
	
	}
	
	private function setValidators(){
	
		$validators = array(
			
			"email" =>           array('allowEmpty' => false),
			"phone" =>           array('allowEmpty' => false),
			"name" =>            array('allowEmpty' => true),
			"surname" =>         array('allowEmpty' => true),
			"street" =>          array('allowEmpty' => true),
			"city" =>            array('allowEmpty' => true),
			"zip" =>             array('allowEmpty' => true),
		    "country" =>         array('allowEmpty' => true),
			"deliveryExtra" =>   array('allowEmpty' => true),
			"companyExtra" =>    array('allowEmpty' => true),
			"nameDelivery" =>    array('allowEmpty' => true),
			"surnameDelivery" => array('allowEmpty' => true),
			"streetDelivery" =>  array('allowEmpty' => true),
			"cityDelivery" =>    array('allowEmpty' => true),
			"zipDelivery" =>     array('allowEmpty' => true),
			"countryDelivery" => array('allowEmpty' => true),
			"nameCompany" =>     array('allowEmpty' => true),
			"ic" =>              array('allowEmpty' => true),
			"dic" =>             array('allowEmpty' => true),
			"payment" =>         array('allowEmpty' => true),
			"shipping" =>        array('allowEmpty' => true),
			"text" =>            array('allowEmpty' => true),
			"conditions" =>      array('allowEmpty' => false),
			"newsletter" =>      array('allowEmpty' => true)
	
		);
	
		return $validators;
	
	}
    
}

?>