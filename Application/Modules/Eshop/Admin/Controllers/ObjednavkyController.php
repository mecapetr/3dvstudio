<?php
class Eshop_ObjednavkyController extends Eshop_Library_WholeEshop
{

	protected $name;
	protected $shortcut;
	
	protected $allLanguageMutations;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();	
    }
    function indexAction()
	{
		$this->_redirect("/admin/eshop/objednavky/seznam");		
	}

	
	
		
	function seznamAction()
    {  
    			
		$this->view->subSelected  	= "Objednávky";
		$order	    				= new Eshop_Models_Order();
   		$paging  					= new Library_Paging();
		$this->view->sortableList 	= false;        
		$this->view->action       	= '/admin/eshop/objednavky/seznam';

		$count		= $order->getCount();
		$page    	= $this->_request->getParam("strana");
		
		if($page)$paging->setPage($page);
		$paging->setAllItemCount($count);
		$paging->setPageItemCount(50);
		$paging->setBlockID("main-paging");
		$paging->setNumberClass("number");
		$paging->showActualPageNumber(false);
		$paging->setJquery(false);
		$paging->setShowArrows(true);
		$paging->setShowNumbers(true);
		$paging->setAddBootstrapClass(true);
		$paging->setPageTitle("/admin/eshop/objednavky/seznam");
		
		$limit = $paging->execute();
		$allOrders = array();
		
		if($limit){
				
			$allOrders = $order->getAllOrdersLimit(null,"orderDate DESC",$limit[1],$limit[0]);
			foreach ($allOrders as $key => $val){
				$allOrders[$key]->orderDate = date("j.n.Y H:i",strtotime($allOrders[$key]->orderDate));
			}
		}

		$this->view->paging = $paging->getPaging();
		
		$this->view->allOrders = $allOrders;

	    //vlozime placeholdery
	    $this->addPlaceholders();

    }

    function detailAction()
    {

    	$order	  	= new Eshop_Models_Order();
    	$orderItem	= new Eshop_Models_OrderItem();
    	$id       	= $this->_request->getParam('id');
    	$where    	= "O.orderID = '$id'";
    
    	 
    	$this->view->action = "/admin/eshop/vyrobce/detail/id/".$id;
    
    
    	//nastavime hlavni data
    	$allItems 	= $this->allItems = $order->getRow($where);

    	$allItems->orderDate = date("j.n.Y H:i",strtotime($allItems->orderDate));
    
    	if($allItems->currencyID == 1){
    		$allItems->shippingTypePrice = number_format($allItems->shippingTypePrice,0,","," ");
    	}else{
    		$allItems->shippingTypePrice = number_format($allItems->shippingTypePrice,2,","," ");
    	}
    	
    	$this->view->allItems = $allItems;
    	
    	$allOrderedItems = $orderItem->getAllItems("orderID = $allItems->orderID");
    	
    	foreach ($allOrderedItems as $key => $val){
    		if($allItems->currencyID == 1){
    			$allOrderedItems[$key]->priceNoVat 	= number_format($val->priceNoVat,0,","," ");
    			$allOrderedItems[$key]->price 		= number_format($val->price,0,","," ");
    		}else{
    			$allOrderedItems[$key]->priceNoVat 	= number_format($val->priceNoVat,2,","," ");
    			$allOrderedItems[$key]->price 		= number_format($val->price,2,","," ");
    		}
    	}
    	
    	$this->view->allOrderedItems = $allOrderedItems;
    	
    	//vlozime placeholdery
    	$this->addPlaceholders();
    } 

}

?>