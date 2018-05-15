<?php

class Install_Library_InstallModules 
{

	private $title;
	private $link;
	private $class;
	private $last;
		
	public function installContent($request){
    	
		$this->title = "Obsah";
        $this->link  = "/admin/obsah/clanky";
        $this->class = " ";
        $this->last  = " ";
		
		$width  = $request->getPost('width');
		$height = $request->getPost('height');

    	$install = new Install_Models_Content();
    	$install->setWidth(10);
    	$install->setHeight(10);
    	//$install->setPath("./Application/Models/AdminBase/Content/content.xml");
        $install->execute();
                
        
    }
    
	public function installBanner($request){
    	
    	$this->title = "Banner";
        $this->link  = "/admin/banner";
        $this->class = " ";
        $this->last  = " ";
        
        $install = new Install_Models_Banner();
        $install->execute();

    	
    }
    
	public function installUsers($request){
    	
    	$this->title = "Uživatelé";
        $this->link  = "/admin/uzivatele/uzivatel";
        $this->class = " ";
        $this->last  = " ";
        
        $install = new Install_Models_Users();
        $install->execute();

    	
    }
        
    public function installSlider($request){
    
    	$this->title = "Slider";
    	$this->link  = "/admin/slider";
    	$this->class = " ";
    	$this->last  = " ";
    
    	$install = new Install_Models_Slider();
    	$install->execute();

    
    }
    
    public function installNewsletter($request){
    
    	$this->title = "Newsletter";
    	$this->link  = "/admin/newsletter";
    	$this->class = " ";
    	$this->last  = " ";
    
    	$install = new Install_Models_Newsletter();
    	$install->execute();

    
    }
    public function installDayMenu($request){
    
    	$this->title = "Denní menu";
    	$this->link  = "/admin/denni-menu";
    	$this->class = " ";
    	$this->last  = " ";
    
    	$install = new Install_Models_DayMenu();
    	$install->execute();

    
    }
                            
}
