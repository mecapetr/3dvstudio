<?php 
class Library_AclPlugin extends Zend_Controller_Plugin_Abstract{
	
    private $_auth;
    private $_acl;

    private $_noacl = array(
	    'module' => 'core',
	    'controller' => 'error',
	    'action' => 'index'
    );

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	
        $this->_auth = Zend_Auth::getInstance();

        if(isset($this->_auth->getStorage()->read()->adminUserID)){
        	
        	
        	
        	
		    $acl         = new Library_Acl();
		    $this->_acl  = $acl->getAcl();
	
		    if ($this->_auth->hasIdentity()) {
			    $role = $this->_auth->getStorage()->read()->type;
			}else{
			    $role = 'guest';
			}
			
		    $module = $request->getModuleName();
		    $controller = $request->getControllerName();
		    $action = $request->getActionName();
		    $resource = "{$module}-{$controller}";
		    
			if ($this->_acl->has($resource)){
			    if (!$this->_acl->isAllowed($role, $resource, $action)) {
				  	
				    $module     = $this->_noacl['module'];
					$controller = $this->_noacl['controller'];
					$action     = $this->_noacl['action'];
					
				}
			}
			
			$request->setModuleName($module);
			$request->setControllerName($controller);
			$request->setActionName($action);	
		
        }
	     
    }

  }
  
 ?>