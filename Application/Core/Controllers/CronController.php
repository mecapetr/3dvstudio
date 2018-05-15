<?php
class Core_CronController extends Library_WebBase
{
	
			
	function init(){
		$this->setDefault();
		$this->translate = Zend_Registry::get("Zend_Translate");
		
		Zend_Layout::getMvcInstance()->disableLayout();
	}
	
	function generateXmlsAction(){
		
		$heureka = new Library_HeurekaExport();
		$heureka->generateXML("http://www.".$this->regDomain["cz"]);
		$heureka->generateXML("http://www.".$this->regDomain["sk"],"sk");
		
		$google = new Library_GoogleExport();
		$google->generateXML("http://www.".$this->regDomain["cz"]);
		$google->generateXML("http://www.".$this->regDomain["sk"],"sk");
				
		$zbozi = new Library_ZboziczExport();
		$zbozi->generateXML("http://www.".$this->regDomain["cz"]);
		$zbozi->generateXML("http://www.".$this->regDomain["sk"],"sk");
		
		$this->renderScript("helper/empty.phtml");
		
	}
	
	function endAuctionsAction(){
		
		$product = new Eshop_Models_Product();
		$scripts = new Library_Scripts();
		$allAuctions = $product->getAllAuctionsForCron();
		$languageMutation = new Models_Language_DB_LanguageMutation();
		
		
		$translate = Zend_Registry::get("Zend_Translate");
		
		$from    = $this->regEmail['cz'];
		$name    = "Aukce - " .  $this->regDomain['cz'];
		//$to      = array($this->regEmail);
		$replyTo = $this->regEmail['cz'];
		$to      = $this->regEmail['cz'];
		
		$subject = "Ukončení aukce";
		
		foreach($allAuctions as $a){
			
			if($a->value){			
				
				$usersLangData = $languageMutation->getRow("LM.suffix = '$a->lang'");
				$aTitle = $scripts->removeDiakritic($a->title);
				$price = number_format(round($a->value / $usersLangData->exchangeRate,2),$usersLangData->decimal,","," ");
				$text = $translate->translate("Gratulujeme, vyhral jste na strankach www.sedacky-nabytek.cz v aukci produkt") . " " . $aTitle. " " . $translate->translate("za cenu") . " " . $price . " " . $usersLangData->currencyCode . ". " . $translate->translate("Budeme vas kontaktovat.");
								
				$this->sendSms($text,$a->tel);
								
				$text   = "<h2>Ukončení aukce</h2>";
				$text  .= "<p>Byla ukončena aukce s produktem ".$a->title." a výhercem se stal uživatel s telefoním číslem ".$a->tel." a částkou " . $price . " " . $usersLangData->currencyCode . ".</p>";
								
				$this->sendMail($from, $name, $to, $subject, $text);
				
			}else{
									
				$text   = "<h2>Ukončení aukce</h2>";
				$text  .= "<p>Byla ukončena aukce s produktem ".$a->title.", ale nikdo na tuto aukci nevložil příhoz.</p>";								
				
				$this->sendMail($from, $name, $to, $subject, $text);
				
			}
			
			$product->updateData(array(
				"endedAuction" => 1
			), "productID = '$a->productID'");
			
		}
		
		$this->renderScript("helper/empty.phtml");
		
	}

  
}

	
?>