<?php
class DayMenu_IndexController extends Library_Adminbase
{
			
	//funkce pr�stupn� v�em action
	function init()
    {    
    	$this->setDefault();	
    	$this->setLinks();
		$this->view->selected = "Denní menu";	
			
    }
    function indexAction()
	{

		$menuFood = new DayMenu_Models_MenuFood();
		
		$this->view->weekNumberSelected = date("W");
		$this->view->yearSelected 		= date("Y");
		
		if($this->_request->getPost("enter")){	
		
		    $weekNumber = $this->_request->getPost("weekNumberInput");
		    $year 		= $this->_request->getPost("yearInput");
		    $month 		= $this->_request->getPost("monthInput");
		
		    if($month == 12 && $weekNumber == 1){
		    	$year++;	
		    }
		        	 	
			$this->addFood($menuFood,$weekNumber,$year);
			$this->updateDate();
			
			$dayOfweek = 1;
		    	if ($weekNumber == 1)		    
		    		$dayOfweek = 7;	
			
			$this->view->weekNumberSelected = $weekNumber;
			$this->view->yearSelected 		= $year;
			
			$date = new Zend_Date();
			$date->setYear($year)
			     ->setWeek($weekNumber)
			     ->setWeekDay($weekNumber);
			$this->view->monthSelected			= date('n', $date->getTimestamp());
			$this->view->daySelected			= date('j', $date->getTimestamp());
			$this->view->weekNumberSelected 		= $weekNumber;
			$this->view->yearSelected 			= $year;			
		}

		$this->addPlaceholders();
		
	}
	
	private function addFood($menuFood,$weekNumber,$year){

		$scripts = new Library_Scripts();
		$where 		= "weekNumber = $weekNumber AND year = $year";
		$menuFood->deleteData($where);
		
		$days = $scripts->getDays();		
		foreach($days as $key => $day){
			
			$count = $this->_request->getPost("count-".$key);	
			$supe  = $this->_request->getPost("supe-".$key);
			
			for($i = 1; $i <= $count; $i++){
				
				$weight = $this->_request->getPost("weight-".$key."-".$i);
				$food   = $this->_request->getPost("food-".$key."-".$i);
				$price  = $this->_request->getPost("price-".$key."-".$i);
				
				if(!is_numeric($price))  $price  = 0;
				if(!is_numeric($weight)) $weight = 0;
				
				if(!empty($food)){
					$data = array(
						"day"    		=> $key,
						"weekNumber"    => $weekNumber,
						"year"   		=> $year,
						"supe"   		=> $supe,
						"weight" 		=> $weight,
						"food"   		=> $food,
						"price"  		=> $price
					);
					
					$menuFood->insertData($data);
					$this->view->message = "Denní menu úspěšně uloženo.";
				
				}else{
					//$this->view->error = "V jednom ze dnů nebylo vyplněné jídlo, a proto nebylo přidáno. Zkontrolujte si prosím denní menu.";
				}
				
			}
			
		}
				
	}
	
	private function updateDate(){
		
		$menuDate = new DayMenu_Models_MenuDate();
		
		$dateFrom = date("Y-m-d",strtotime($this->_request->getPost("dateFrom")));
		$dateTo   = date("Y-m-d",strtotime($this->_request->getPost("dateTo")));
		
		$data = array(
			"menuFrom" => $dateFrom,
			"menuTo"   => $dateTo
		);
		
		$menuDate->updateData($data,"menuDateID = '1'");
		
	}
	
	
	
	private function setLinks(){
	
		$links = array();
		
		$this->view->links = $links;
	
	}

	
}

?>