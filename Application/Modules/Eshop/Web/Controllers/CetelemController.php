<?php
class EshopW_CetelemController extends Library_WebBase
{
			
	function init(){
			
		$this->translate = Zend_Registry::get("Zend_Translate");
				
	}
	
	function indexAction(){
		
		Zend_Layout::getMvcInstance()->disableLayout();
		
		$price       = $this->_request->getParam("price");
		$sendData    = $this->_request->getParam("sendData");
		$product     = $this->_request->getParam("product");
		$orderNumber = $this->_request->getParam("orderNumber");
				
		$obj       = $this->_request->getPost("obj");
		if(!empty($obj))$orderNumber = $obj;
		
		$oznTovaru = $this->_request->getPost("oznTovaru");
		if(!empty($oznTovaru))$product = $oznTovaru;
		
		$sendDataP = $this->_request->getPost("sendDataP");
		if(!empty($oznTovaru))$sendData = $sendDataP;

		$send        = $this->_request->getPost("send");
		$spl         = $this->_request->getPost("spl");
		$poj         = $this->_request->getPost("poj");
		
		$primaPlatbaPost  = $this->_request->getPost("primaPlatba");
		$pocetSplatekPost = $this->_request->getPost("pocetSplatek");
				
		if(is_numeric($price)){
			
			$price = floor($price);
			
			$typSplatek = file_get_contents("http://www.cetelem.sk/online/ws_bareminfo.php?kodpredajcu=2648988");
			$pojisteni  = file_get_contents("https://www.cetelem.sk/online/ws_options+JSON.php?kodpredajcu=2648988&typciselnika=poistenie");
			
			$typSplatek = new SimpleXMLElement($typSplatek);			
			$this->view->splatky = $typSplatek;
				
			$pojisteni = new SimpleXMLElement($pojisteni);
			$this->view->pojisteni = $pojisteni;
			
			$pojCode = "A3";
			if(!empty($poj)) $pojCode = $poj;
			$this->view->backPoj = $pojCode;
			
			$splCode = $typSplatek->barem[0]["id"];
			$this->view->defaultSpl = $splCode;
			if(!empty($spl)) $splCode = $spl;
			$this->view->backSpl = $splCode;
			
			$pocetSplatok = $typSplatek->barem[0]->rozsah_splatky;
			if($splCode != $typSplatek->barem[0]["id"])$pocetSplatok = 10;
			if(!empty($pocetSplatekPost))$pocetSplatok = $pocetSplatekPost;
			
			$typSplatek->barem[0]->rozsah_pp = str_replace("%","",$typSplatek->barem[0]->rozsah_pp);
			$primaPlatba = number_format($price/100*$typSplatek->barem[0]->rozsah_pp,2,",","");
			
			if($splCode != $typSplatek->barem[0]["id"])$primaPlatba = number_format(0,2,",","");
			if(!empty($primaPlatbaPost))$primaPlatba = number_format($primaPlatbaPost,2,",","");
			
			$material   = 330;

			$vysledek = file_get_contents("https://www.cetelem.sk/online/ws_xmlcalc+JSON.php?kodPredajcu=2648988&kodBaremu=".$splCode."&kodPoistenia=".$pojCode."&kodTovaru=330&cenaTovaru=".$price."&priamaPlatba=".$primaPlatba."&pocetSplatok=".$pocetSplatok."&odklad=0");			
			$vysledek = new SimpleXMLElement($vysledek);
			
			if($vysledek->status == "ok" || $vysledek->status == "error/warning"){
			
				$rozsahSplatkyMin    = 0;
				$rozsahSplatkyMax    = 0;
				$rozsahUverMin       = 0;
				$rozsahUverMax       = 0;
				$primaPlatbaProcenta = 0;
				
				foreach($typSplatek->barem as $barem){
					if($splCode == $barem["id"]){
						$rozsahSplatkyMin    = $barem->rozsah_splatky_min;
						$rozsahSplatkyMax    = $barem->rozsah_splatky_max;
						$rozsahUverMin       = $barem->rozsah_uver_min;
						$rozsahUverMax       = $barem->rozsah_uver_max;
						$primaPlatbaProcenta = $barem->rozsah_pp;
					}
				}
											
				$productPrice                         = $vysledek->vysledok->cenaTovaru;
				$this->view->price                    = $vysledek->vysledok->cenaTovaru_FMT;
				$this->view->priceNoEur               = $productPrice;
				$this->view->primaPlatba              = $vysledek->vysledok->priamaPlatba_FMT;
				$this->view->primaPlatbaBezEur        = explode(",",$vysledek->vysledok->priamaPlatba);
				$this->view->primaPlatbaNoEur         = $vysledek->vysledok->priamaPlatba;
				$this->view->pocetSplatok             = $vysledek->vysledok->pocetSplatok;
				$this->view->pocetSplatokMin          = $rozsahSplatkyMin;
				$this->view->pocetSplatokMax          = $rozsahSplatkyMax;
				$this->view->vyskaUveru               = $vysledek->vysledok->vyskaUveru_FMT;
				$this->view->vyskaUveruNoEur          = $vysledek->vysledok->vyskaUveru;
				$this->view->vyskaSplatkyBezPojisteni = $vysledek->vysledok->vyskaSplatkyBezPoistenia_FMT;
				$this->view->vyskaSplatky             = $vysledek->vysledok->vyskaSplatky_FMT;
				$this->view->vyskaSplatkyNoEur        = $vysledek->vysledok->vyskaSplatky;
				$this->view->cenaUveru                = $vysledek->vysledok->CCKZ_FMT;
				$this->view->cenaUveruNoEur           = $vysledek->vysledok->cenaUveru;
				$this->view->rpsn                     = $vysledek->vysledok->RPMN_FMT;
				$this->view->rpsnNo                   = $vysledek->vysledok->RPMN;
				$this->view->urokovaSazba             = $vysledek->vysledok->ursadz_FMT;
				$this->view->urokovaSazbaNo           = $vysledek->vysledok->ursadz;
				$this->view->kodProdejce              = 2648988;
				$this->view->material                 = $vysledek->vysledok->kodMaterialu;

				
				$productPrice = str_replace(",", ".", $productPrice);
				if($primaPlatbaProcenta){
					
					$primaPlatbaProcenta = explode("-",str_replace("%","",$primaPlatbaProcenta));
					$this->view->primaPlatbaMin = number_format(($productPrice/100)*$primaPlatbaProcenta[0],0,","," ");
					
					if(!empty($primaPlatbaProcenta[1])){
						
						$primaPlatbaMax = round((($productPrice)/100)*$primaPlatbaProcenta[1],2);
						
						if($productPrice-$primaPlatbaMax < $rozsahUverMin){
							$primaPlatbaMax = $productPrice - $rozsahUverMin;
						}
						
						$this->view->primaPlatbaMax = number_format($primaPlatbaMax,0,","," ");
					}else{
						$this->view->primaPlatbaMax = number_format(($productPrice/100)*$primaPlatbaProcenta[0],0,","," ");
					}
				}else{
					$this->view->primaPlatbaMin = $primaPlatba;
					$this->view->primaPlatbaMax = $primaPlatba;
				}
			
			}else{
				$this->view->error = $vysledek->sprava;
			}
			
			$this->view->sendData    = $sendData;
			$this->view->send        = $send;
			$this->view->product     = $product;
			$this->view->orderNumber = $orderNumber;
			/*
			$this->view->sendData    = 1;
			$this->view->send        = 1;
			$this->view->product     = "Rohová sedačka CORTINA";
			$this->view->orderNumber = 123456;*/

		}
		
		
	}
	
	public function statusOkAction(){
		
		$this->setDefault();     

        $this->translate = Zend_Registry::get("Zend_Translate");
        	
        //vypise menu
        $this->getMenu(null,null);
        
        $stav = $_GET["stav"];
        if(is_numeric($stav)){
        	
        	$this->view->stav = $this->getStav($stav);
        	$this->view->stavZadosti = false;
        	
        	if($stav == 1){
        		$this->view->cisloAuth = $_GET["numaut"];
        		$this->view->splatka = $_GET["splatka"];
        	}
        	if($stav == 2 || $stav == 5){
        		$this->view->cisloPredajce = $_GET["vdr"];
        	}
        	
        	if($stav == 1 || $stav == 2){
        		$this->view->cisloZadosti = $_GET["numwrk"];
        	}
        	
        	$this->view->jmeno = $_GET["priezvisko"]." ".$_GET["meno"];
        	
        	if($stav == 1 || $stav == 2 || $stav == 2){
        		$this->view->stavZadosti = true;
        	}
        	
        }
        
        
        $this->_response->insert('slider' , $this->view->render('placeholders/empty.phtml'));

		
	}
	
	private function getStav($num){
		
		$text = "Zle zadaný stav úveru.";
		switch($num){
			
				case 1: $text = "predbežne schválená";
				break;
				case 2: $text = "žiadosť sa posudzuje";
				break;
				case 3: $text = "zamietnutá";
				break;
				case 5: $text = " nastala chyba pri spracovaní";
				break;
			
		}
		
		return $text;
		
	}
	
	public function statusKoAction(){
	
		$this->setDefault();
	
		$this->translate = Zend_Registry::get("Zend_Translate");
		 
		//vypise menu
		$this->getMenu(null,null);
		
		$stav = $_GET["stav"];
		if(is_numeric($stav)){
			 
			$this->view->stav = $this->getStav($stav);
			$this->view->stavZadosti = false;
			 
			if($stav == 1){
				$this->view->cisloAuth = $_GET["numaut"];
				$this->view->splatka = $_GET["splatka"];
			}
			if($stav == 2 || $stav == 5){
				$this->view->cisloPredajce = $_GET["vdr"];
			}
			 
			if($stav == 1 || $stav == 2){
				$this->view->cisloZadosti = $_GET["numwrk"];
			}
			 
			$this->view->jmeno = $_GET["priezvisko"]." ".$_GET["meno"];
			 
			if($stav == 1 || $stav == 2 || $stav == 2){
				$this->view->stavZadosti = true;
			}
			 
		}
	
		$this->_response->insert('slider' , $this->view->render('placeholders/empty.phtml'));
	
	
	}

	
}

?>