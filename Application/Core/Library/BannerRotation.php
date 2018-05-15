<?php
/**
 * Trida pro rotaci banneru bez priorit. Banery po sobe navazujou jeden podruhem ale nejdrive se objevi ten, ktery ma nejblize ke svemu ukonceni.
 * Pokud se jich takovych vyskytuje vice, zobrazi se nejdrive ten, ktery ma mensi pocet zhlednuti. Kontrola, jestli byl banner zhlednut se uchovava v
 * session a kontrola poctu zhlednuti jednoho banneru v cookies.
 *
 */
class Library_BannerRotation{
	
	//seznam prijatych banneru ktere budou rotovat
    protected $bannerList;
    
    //pristup k databazi
    protected $db;
    
    //session pro kontrolu jestli uz byl banner zhlednuty a nebo nebyl.
    protected $viewed;
    
    //pocet sekund kdy potom expiruje cookies
    protected $second = 3600;
    	
	function Mecovi_BannerRotation($bannerList,$db){
		
		$this->db = $db;
		$this->bannerList = $bannerList;
		$this->viewed = new Zend_Session_Namespace('viewed');
        				
	}
	
	function getBanner(){

		
		if(count($this->bannerList) > 0){

			$watched = 0;	
			foreach($this->bannerList as $banner){
				
				$bannerID = $banner->bannerID;
				
				if($this->viewed->$bannerID == 0 || !isset($this->viewed->$bannerID)){	
			        				
			    	//$inputSQL = "UPDATE reklamnibanner SET Views = Views+1 WHERE id = '$bannerID' AND PayPer = '0' ";	
			    	$inputSQL = "";		       	
				    $result = $this->setIP($bannerID,$inputSQL,0);
					
				    if($result == 'YES'){
						
					    return $banner;
					    
					}elseif($result == 'NO'){
						if($watched+1 == count($this->bannerList)){
							
						    return '';
							
						}else{
							continue;
						}
					}
			    }
				$watched++;
			}
			
			//jestlize byly zhlednuty vsechny bannery, tak jejich zhlednuti nastavi na 0, coz znamena, ze se mohou znovu zobrazit
			if($watched == count($this->bannerList)){
				return $this->nullSessionValue($this->bannerList);
			}
			
		}else{
	    	return '';
	    }
	}
	
    protected function setIP($advertisementID,$inputSQL,$maxViewIP){
    	
    	$cookie = $this->getCookie($advertisementID); 
    	$this->setSession($advertisementID,'1');  //nastavi session prislusnemu id banneru na zhlednuto, cili 1   	
    	
    	if(!$cookie){

    		$this->setCook($advertisementID,'1',Time() + $this->second);  //nastavi cookies hodnotu zhlednuti 1 k prislusnemu id banneru
    		
    		//$this->db->query($inputSQL);
    		 
    		return 'YES';
    		
    	}elseif($cookie < $maxViewIP || $maxViewIP == 0){

    		$cookie++;
    		$this->setCook($advertisementID,$cookie,Time() + $this->second);  		
    		//$this->db->query($inputSQL);
    	    		
    		return 'YES';
    		
    	}else{
    		
    		return 'NO';
       	}
    	
    }
        
    protected function getCookie($advertisementID){
    	
    	if(!isset($_COOKIE[$advertisementID])){
    	    return false;
    	    
    	}else{
    		
    		return $_COOKIE[$advertisementID];
    		
    	}
    	
    }
    protected function setCook($advertisementID,$value,$time){
    	
    	setcookie($advertisementID,$value,$time,'/');
    	
    }
    
    protected function nullSessionValue($banners){
    	
    	foreach($banners as $value){
    		$bannerID = $value->bannerID;
    		$this->viewed->$bannerID = 0;
    		
    	}
    	
    	return $this->getBanner();
    	
    }
    
    protected function setSession($AdvertisementID,$value){
    	
    	$this->viewed->$AdvertisementID = $value;
    	
    }
    
}

?>