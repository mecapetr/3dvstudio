<?php
class DayMenuW_IndexController extends Library_WebBase
{
			
	//funkce pr�stupn� v�em action
	function init()
    {    
    	$this->setDefault();	
			
    }
    function indexAction()
	{
		$week = $this->_request->getParam('plusWeek');
		
		$menuFood = new DayMenu_Models_MenuFood();
		$link     = new Content_Models_Link();
		$year	  	= date("Y");
		$weekNumber	= date("W");
		$this->getDays();
		
			
		$date = new Zend_Date();
		$date->setWeekday(1);
		//$date->setYear(date("Y"));
		//$date->setWeek($weekNumber);
		
		if(!empty($week))
			$date->addWeek($week);
		
		$this->view->menuFrom = date("j.n.Y",$date->getTimestamp());
		$date->setWeekday(7);
		$this->view->menuTo = date("j.n.Y",$date->getTimestamp());
		
		$this->getFood($menuFood,$date->toString('w'),$year);	
		
		$linkContent = $link->getOneRow("niceTitle = 'denni-menu'");
		$this->getMenu($linkContent);
		
		$list = array(
			"Denní menu" => ""
		);
		
		$this->setBreadCrumb($list);
		
		$this->_response->insert('whis' , $this->view->render('placeholders/breadcrumb.phtml'));
		$this->view->week = $week;
	}
	
		
	private function getFood($menuFood,$weekNumber,$year){
		$where = "weekNumber = $weekNumber AND year = $year";
		$items = $menuFood->getAllItems($where,array("day","menuFoodID"));
		$list  = array();
		
		foreach($items as $item){
			
			$list[$item->day][] = $item;
		}
		
		$this->view->foodList  = $list;
		
	}
	

	
}

?>