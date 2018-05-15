<?php
class DayMenu_HelperController extends Library_Adminbase
{
			
	//funkce pr�stupn� v�em action
	function init()
    {    
    	$this->setDefault();
		$this->view->selected = "Denní menu";	
		
		Zend_Layout::getMvcInstance()->disableLayout();
			
    }
    function getFoodAction()
	{

		$menuFood 	= new DayMenu_Models_MenuFood();
		$scripts    = new Library_Scripts();
		
		$weekNumber	= $this->_request->getPost('weekNumber');
		$year 	  	= $this->_request->getPost('year');
		
		$this->getFood($menuFood,$weekNumber,$year);	
		$this->view->days = $scripts->getDays();
				
	}

	private function getFood($menuFood,$weekNumber,$year){
		
		$where 		= "weekNumber = $weekNumber AND year = $year";
		$items 		= $menuFood->getAllItems($where,array("day","menuFoodID"));
		$list  		= array();
		
		foreach($items as $item){
			
			$list[$item->day][] = $item;
		}
		
		$this->view->foodList  = $list;
		
	}

	
}

?>