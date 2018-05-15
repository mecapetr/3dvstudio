<?php
class Eshop_CustomerEmailsController extends Eshop_Library_WholeEshop
{
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	parent::init();	
    }
    function indexAction()
	{
		
		
		if($this->_request->getPost("enter")){
			$customer = new Eshop_Models_Customer();
			$allCustomers = $customer->getAllItems("deleted = 0");
			
			$emails = array();
			
			foreach($allCustomers as $cust){
				$emails[] = array($cust->email);
			}
					
			$this->download_send_headers("email_uzivatelu_" . date("j.n.Y") . ".csv");
			echo $this->array2csv($emails);
			die();
		}
		
	}
	
	function array2csv(array &$array)
	{
		if (count($array) == 0) {
			return null;
		}
		ob_start();
		$df = fopen("php://output", 'w');
		foreach ($array as $row) {
			fputcsv($df, $row);
		}
		fclose($df);
		return ob_get_clean();
	}
	
	function download_send_headers($filename) {
		// disable caching
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");
	
		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
	
		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename={$filename}");
		header("Content-Transfer-Encoding: binary");
	}

	

}

?>