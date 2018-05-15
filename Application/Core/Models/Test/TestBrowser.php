<?php 
class Models_Test_TestBrowser extends Zend_Controller_Plugin_Abstract{
	
    
    public $Agent;
    public $Name;
    public $Version;

   

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	
	    	$browsers = array("firefox", "msie", "opera", "chrome", "safari",
	                            "mozilla", "seamonkey",    "konqueror", "netscape",
	                            "gecko", "navigator", "mosaic", "lynx", "amaya",
	                            "omniweb", "avant", "camino", "flock", "aol");
	
	        $this->Agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	
	        foreach($browsers as $browser)
	        {
	            if (preg_match("#($browser)[/ ]?([0-9.]*)#", $this->Agent, $match))
	            {
	                $this->Name = $match[1] ;
	                $this->Version = $match[2] ;
	                break ;
	            }
	        }
	        
			if($this->Name=="msie" && ($this->Version == "6.0" || $this->Version == "7.0")){
				
				$request->setModuleName("core");
				$request->setControllerName("noie6");
				if($this->Version == "7.0")$request->setControllerName("noie7");
				$request->setActionName("index");
				
			}

    }

  }
  
 ?>