<?php
error_reporting(E_ALL|E_STRICT);
date_default_timezone_set('Europe/Prague');


set_include_path( PATH_SEPARATOR . 'Library'
. PATH_SEPARATOR . __DIR__ . '/Library'
. PATH_SEPARATOR . 'Application/Models/'
. PATH_SEPARATOR . 'Application/Extra/'
. PATH_SEPARATOR . 'Application/Core/'
. PATH_SEPARATOR . 'Application/Modules/'
. PATH_SEPARATOR . get_include_path());
   
require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);


Zend_Loader::loadClass('Zend_Controller_Front');
Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Layout');
Zend_Loader::loadClass('Zend_Controller_Router_Route_Regex');

// load configuration

$config = new Zend_Config_Ini('Application/config.ini', 'general');
$registry = Zend_Registry::getInstance();
$registry->set('config', $config);

//databaze
$db = Zend_Db::factory($config->db->adapter,$config->db->config->toArray());
$db->setFetchMode(Zend_Db::FETCH_OBJ);
Zend_Db_Table::setDefaultAdapter($db);
$registry->set('db', $db);
       
// setup controller
$frontController = Zend_Controller_Front::getInstance();       
//error
$plugin = new Zend_Controller_Plugin_ErrorHandler(array('module' => 'core',"controller"=>"error","action"=>"error"));
$frontController->registerPlugin($plugin);

$router = $frontController->getRouter();

$frontController->throwExceptions(true);
$root = dirname(__FILE__);
$frontController->setControllerDirectory(array(
     'default'    => $root.'/Application/Extra/Web/Controllers',
	 'admin-ex'   => $root.'/Application/Extra/Admin/Controllers',
	 'core'       => $root.'/Application/Core/Controllers',
	 'content'    => $root.'/Application/Modules/Content/Admin/Controllers',
	 'content-w'  => $root.'/Application/Modules/Content/Web/Controllers',
	 'eshop'      => $root.'/Application/Modules/Eshop/Admin/Controllers',
	 'eshop-w'    => $root.'/Application/Modules/Eshop/Web/Controllers',
	 'day-menu'   => $root.'/Application/Modules/DayMenu/Admin/Controllers',
	 'day-menu-w' => $root.'/Application/Modules/DayMenu/Web/Controllers',
     'install'    => $root.'/Application/Modules/Install/Controllers',
     'slider'     => $root.'/Application/Modules/Slider/Admin/Controllers',
     'newsletter' => $root.'/Application/Modules/Newsletter/Admin/Controllers',
	 'user'       => $root.'/Application/Modules/User/Admin/Controllers',
	 'user-w'     => $root.'/Application/Modules/User/Web/Controllers',
	 'option'     => $root.'/Application/Modules/Options/Admin/Controllers',
	 'auth'       => $root.'/Application/Modules/Auth/Admin/Controllers',
	 'auth-w'     => $root.'/Application/Modules/Auth/Web/Controllers',
	 'seo-sitemap' => $root.'/Application/Modules/Seo/Sitemap/Controllers',
	 'seo-robots'  => $root.'/Application/Modules/Seo/Robots/Controllers'
     
));

$db->query('SET NAMES UTF8');
$db->query('SET CHARACTER SET UTF8');

Zend_Layout::startMvc(array('layoutPath'=>'Application/Layouts'));

$isModuleTable = true;
try{
       $result = $db->describeTable('module'); //throws exception
}catch(Exception $e){
       $isModuleTable = false;
}
if($isModuleTable){
	$module = new Models_Module();
	$langRow = $module->getOneRow("moduleID = '12' AND enabled = '1'");
}
//web
$router->addRoute('content-w',	    new Zend_Controller_Router_Route('/',        	                		   array('module' => 'content-w','controller' =>'index')));
if(isset($langRow)){
 $router->addRoute('content-wll',    new Zend_Controller_Router_Route('/:l',                                array('module' => 'content-w','controller' =>'index')));	
 $router->addRoute('content-wl',     new Zend_Controller_Router_Route('/:l/:@link',                                array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('content-wls',    new Zend_Controller_Router_Route('/:l/:@link/:@sublink',                    array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('content-wlssd',  new Zend_Controller_Router_Route('/:l/:@link/:@sublink/:@subsublink',         array('module' => 'content-w','controller' =>'index','action' => 'detail')));
 $router->addRoute('content-wlsd',   new Zend_Controller_Router_Route('/:l/:@link/:@sublink/:@subsublink/:detail', array('module' => 'content-w','controller' =>'index','action' => 'detail')));
 $router->addRoute('content-wlsp',   new Zend_Controller_Router_Route('/:l/:@link/:@sublink/@strana/:page',        array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('content-wlsp2',   new Zend_Controller_Router_Route('/:l/:@link/:@sublink/w/:w/by/:by/ad/:ad/strana/:page',       array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('content-wlsp3',   new Zend_Controller_Router_Route('/:l/:@link/w/:w/by/:by/ad/:ad/strana/:page',       array('module' => 'content-w','controller' =>'index')));
 
 $router->addRoute('content-wlp',    new Zend_Controller_Router_Route('/:@link/@strana/:page',                  array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('dayMenu-w',      new Zend_Controller_Router_Route('/@denni-menu',                           array('module' => 'day-menu-w','controller' =>'index')));
}else{
 $router->addRoute('content-wl',     new Zend_Controller_Router_Route('/:link',                            array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('content-wls',    new Zend_Controller_Router_Route('/:link/:sublink',                    array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('content-wlsd',   new Zend_Controller_Router_Route('/:link/:sublink/:detail',            array('module' => 'content-w','controller' =>'index','action' => 'detail')));
 $router->addRoute('content-wlsp',   new Zend_Controller_Router_Route('/:link/:sublink/strana/:page',       array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('content-wlsp1',   new Zend_Controller_Router_Route('/:link/:sublink/w/:w/by/:by/ad/:ad/strana',       array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('content-wlsp2',   new Zend_Controller_Router_Route('/:link/:sublink/w/:w/by/:by/ad/:ad/strana/:page',       array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('content-wlp',    new Zend_Controller_Router_Route('/:link/strana/:page',                array('module' => 'content-w','controller' =>'index')));
 $router->addRoute('dayMenu-w',      new Zend_Controller_Router_Route('/denni-menu',                        array('module' => 'day-menu-w','controller' =>'index')));
}

//admin zaklad
$router->addRoute('adminC',  		new Zend_Controller_Router_Route('/admin/:controller',         		       array('module' => 'admin-ex')));
$router->addRoute('adminCA',  	    new Zend_Controller_Router_Route('/admin/:controller/:action', 		       array('module' => 'admin-ex')));
$router->addRoute('adminCAP',  		new Zend_Controller_Router_Route('/admin/:controller/:action/*', 		   array('module' => 'admin-ex')));

//hotové moduly
$router->addRoute('core',  		    new Zend_Controller_Router_Route('/core/:controller',         		       array('module' => 'core')));
$router->addRoute('coreA',  	    new Zend_Controller_Router_Route('/core/:controller/:action', 		       array('module' => 'core')));
$router->addRoute('coreAP',  		new Zend_Controller_Router_Route('/core/:controller/:action/*', 		   array('module' => 'core')));


$router->addRoute('extra',  		    new Zend_Controller_Router_Route('/extra/:controller',         		   array('module' => 'default')));
$router->addRoute('extraA',  	    new Zend_Controller_Router_Route('/extra/:controller/:action', 		       array('module' => 'default')));
$router->addRoute('extraAP',  		new Zend_Controller_Router_Route('/extra/:controller/:action/*', 		   array('module' => 'default')));

$router->addRoute('admin',          new Zend_Controller_Router_Route('/admin',                     		        array('module' => 'auth')));
$router->addRoute('adminL',         new Zend_Controller_Router_Route('/admin/logout',              		        array('module' => 'auth', 'controller' => 'index', 'action' => 'logout')));

$router->addRoute('installC',    	new Zend_Controller_Router_Route('/admin/install/:controller',              array('module' => 'install')));
$router->addRoute('installA',   	new Zend_Controller_Router_Route('/admin/install/:controller/:action',      array('module' => 'ïnstall')));

$router->addRoute('content',  	    new Zend_Controller_Router_Route('/admin/obsah/:controller',                array('module' => 'content')));
$router->addRoute('contentA', 	    new Zend_Controller_Router_Route('/admin/obsah/:controller/:action',        array('module' => 'content')));
$router->addRoute('contentAP',	    new Zend_Controller_Router_Route('/admin/obsah/:controller/:action/*',      array('module' => 'content')));

$router->addRoute('eshop',  	    new Zend_Controller_Router_Route('/admin/eshop/:controller',                array('module' => 'eshop')));
$router->addRoute('eshopA', 	    new Zend_Controller_Router_Route('/admin/eshop/:controller/:action',        array('module' => 'eshop')));
$router->addRoute('eshopAP',	    new Zend_Controller_Router_Route('/admin/eshop/:controller/:action/*',      array('module' => 'eshop')));

$router->addRoute('dayMenu',  	    new Zend_Controller_Router_Route('/admin/denni-menu',                       array('module' => 'day-menu')));
$router->addRoute('dayMenuA', 	    new Zend_Controller_Router_Route('/admin/denni-menu/:action',               array('module' => 'day-menu')));
$router->addRoute('dayMenuAP',	    new Zend_Controller_Router_Route('/admin/denni-menu/:action/*',             array('module' => 'day-menu')));
$router->addRoute('dayMenuH', 	    new Zend_Controller_Router_Route('/admin/denni-menu/helper/:action',        array('module' => 'day-menu','controller' => 'helper')));
$router->addRoute('dayMenuHP',	    new Zend_Controller_Router_Route('/admin/denni-menu/helper/:action/*',      array('module' => 'day-menu','controller' => 'helper')));

$router->addRoute('user',       	new Zend_Controller_Router_Route('/admin/uzivatele/:controller',            array('module' => 'user','controller' => 'uzivatel')));
$router->addRoute('userA',      	new Zend_Controller_Router_Route('/admin/uzivatele/:controller/:action',    array('module' => 'user','controller' => 'uzivatel')));
$router->addRoute('userAP',     	new Zend_Controller_Router_Route('/admin/uzivatele/:controller/:action/*',  array('module' => 'user','controller' => 'uzivatel')));

$router->addRoute('options',    	new Zend_Controller_Router_Route('/admin/nastaveni/:controller',  		    array('module' => 'option')));
$router->addRoute('optionsA',   	new Zend_Controller_Router_Route('/admin/nastaveni/:controller/:action',    array('module' => 'option')));
$router->addRoute('optionsAP',  	new Zend_Controller_Router_Route('/admin/nastaveni/:controller/:action/*',  array('module' => 'option')));

$router->addRoute('slider',     	new Zend_Controller_Router_Route('/admin/slider',               			array('module' => 'slider','controller' => 'slider')));
$router->addRoute('sliderA',    	new Zend_Controller_Router_Route('/admin/slider/:action',       			array('module' => 'slider','controller' => 'slider')));
$router->addRoute('sliderAP',   	new Zend_Controller_Router_Route('/admin/slider/:action/*',     			array('module' => 'slider','controller' => 'slider')));

$router->addRoute('newsletter', 	new Zend_Controller_Router_Route('/admin/newsletter',          				array('module' => 'newsletter','controller' => 'email', 'action' => 'index')));
$router->addRoute('newsletterC', 	new Zend_Controller_Router_Route('/admin/newsletter/:controller/',          array('module' => 'newsletter')));
$router->addRoute('newsletterCA',   new Zend_Controller_Router_Route('/admin/newsletter/:controller/:action',   array('module' => 'newsletter')));
$router->addRoute('newsletterCAP',  new Zend_Controller_Router_Route('/admin/newsletter/:controller/:action/*', array('module' => 'newsletter')));

//seo
$router->addRoute('seoSitemap',     new Zend_Controller_Router_Route('/admin/seo/sitemap',                      array('module' => 'seo-sitemap')));
$router->addRoute('seoSitemapA',    new Zend_Controller_Router_Route('/admin/seo/sitemap/:action',              array('module' => 'seo-sitemap')));

$router->addRoute('seoRobots',      new Zend_Controller_Router_Route('/admin/seo/robots',                      array('module' => 'seo-robots')));
$router->addRoute('seoRobotsA',     new Zend_Controller_Router_Route('/admin/seo/robots/:action',              array('module' => 'seo-robots')));


//web zaklad
$router->addRoute('registration',   new Zend_Controller_Router_Route('/registrace',                             array('module' => 'user-w','controller' => 'registrace')));
$router->addRoute('registrationA', 	new Zend_Controller_Router_Route('/registrace/:action',                     array('module' => 'user-w','controller' => 'registrace')));
$router->addRoute('registrationAP',	new Zend_Controller_Router_Route('/registrace/:action/:id/:regcode',        array('module' => 'user-w','controller' => 'registrace')));

$router->addRoute('login',          new Zend_Controller_Router_Route('/login',                                  array('module' => 'auth-w','controller' => 'index')));
$router->addRoute('logout',         new Zend_Controller_Router_Route('/odhlasit',                               array('module' => 'auth-w','controller' => 'index', 'action' => 'logout')));

$router->addRoute('zapHeslo',  	 	new Zend_Controller_Router_Route('/zapomenute-heslo',          		          array('module' => 'auth-w', 'controller' => 'zapomenute-heslo')));
$router->addRoute('zapHesloA',	 	new Zend_Controller_Router_Route('/zapomenute-heslo/:action', 			      array('module' => 'auth-w', 'controller' => 'zapomenute-heslo')));
$router->addRoute('zapHesloP',      new Zend_Controller_Router_Route('/zapomenute-heslo/potvrzeni/:id/:passcode', array('module' => 'auth-w', 'controller' => 'zapomenute-heslo', 'action' => 'potvrzeni')));

$router->addRoute('helper',      	new Zend_Controller_Router_Route('/eshop/helper/:action',   				  array('module' => 'eshop-w', 'controller' => 'helper')));


$router->addRoute('vyhledat',     new Zend_Controller_Router_Route('/v/:searchQuery', 								  array('module' => 'default', 'controller' => 'vyhledat')));
$router->addRoute('vyhledatP',    new Zend_Controller_Router_Route('/v/:searchQuery/strana/:page', 					  array('module' => 'default', 'controller' => 'vyhledat')));
$router->addRoute('vyhledatPS',   new Zend_Controller_Router_Route('/v/:searchQuery/w/:w/by/:by/ad/:ad/strana/:page', array('module' => 'default','controller' =>'vyhledat')));

$router->addRoute('objednavka',   new Zend_Controller_Router_Route('/@nakupni-kosik', 	         array('module' => 'eshop-w', 'controller' => 'objednavka')));
$router->addRoute('objednano',    new Zend_Controller_Router_Route('/@nakupni-kosik/@objednano', array('module' => 'eshop-w', 'controller' => 'objednavka','action' => 'ordered')));

$router->addRoute('nezavazneObjednat',    new Zend_Controller_Router_Route('/@nezavazne-objednat', array('module' => 'default', 'controller' => 'nezavazna-objednavka')));
$router->addRoute('nezavazneObjednatO',   new Zend_Controller_Router_Route('/@nezavazne-objednat/@objednano', array('module' => 'default', 'controller' => 'nezavazna-objednavka','action' => 'ordered')));


$router->addRoute('cetelem',           new Zend_Controller_Router_Route('/cetelem/:price',  			                   array('module' => 'eshop-w','controller' =>'cetelem','action' =>'index')));
$router->addRoute('cetelemData',       new Zend_Controller_Router_Route('/cetelem/:price/:product/:orderNumber/:sendData', array('module' => 'eshop-w','controller' =>'cetelem','action' =>'index')));
$router->addRoute('cetelem-status-ok', new Zend_Controller_Router_Route('/cetelem/status-ok',  			                   array('module' => 'eshop-w','controller' =>'cetelem','action' =>'status-ok')));
$router->addRoute('cetelem-status-ko', new Zend_Controller_Router_Route('/cetelem/status-ko',  			                   array('module' => 'eshop-w','controller' =>'cetelem','action' =>'status-ko')));



$frontController->registerPlugin(new Models_Language_LanguagePlugin());
//$frontController->registerPlugin(new Library_AclPlugin());
$frontController->registerPlugin(new Models_Test_TestBrowser());
$frontController->registerPlugin(new Models_Test_TestRedirect());

// run!
$frontController->dispatch();