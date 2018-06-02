<?php
class AdminEx_PatickaController extends Library_Adminbase
{

	protected $title;
	
	//funkce pr�stupn� v�em action
	function init()
    {    
    	$this->setDefault();
		$this->view->selectedMenu = "Patička";	
    	
    }
    function indexAction()
	{	
    	$this->_redirect("/admin/paticka/socialni-site");
	}
	
	function socialniSiteAction()
	{
	    
	    $socialniSite			= new Models_SocialIcons();
	    $socialniSiteLangDb	    = new Models_SocialIconsLang();
	    $language 				= new Models_Language_Language();
	    
	    $enter    = $this->_request->getPost("enter");
	    
	    $this->view->subSelected			= "Sociální sítě";
	    
	    $this->setSublinks();
	    
	    $this->view->action = "/admin/paticka/socialni-site/";
	    
	    
	    //vybereme vsechny jazykove mutace
	    $this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
	    
	    
	    if($enter){
	        
	        $script = new Library_Scripts();
	        $filter = $this->setData();
	        
	        
	        for($i = 1; $i <= 6; $i++){
	            $param 		= "url" . $i;
	            $showParam 	= "showIcon" . $i;
	            $data = array("url" => $this->{$param}['cz'],"showIcon" => $this->{$showParam});
	            $socialniSite->updateData($data, "socialIconsID = $i");
	            
	            foreach($this->allLanguageMutations as $val){
	            	
	            	$count = $socialniSiteLangDb->getCount("socialIconsID = $i AND lang = '$val->suffix'");
	            	
	            	if($count > 0){
		                $socialniSiteLangDb->updateData(array(
		                    "url" => 	$this->{$param}[$val->suffix]
		                ), "socialIconsID = $i AND lang = '$val->suffix'");
	            	}else{
	            		$socialniSiteLangDb->insertData(array(
	            			"socialIconsID" => $i,
	            			"lang"          => $val->suffix,
	            			"url"           => $this->{$param}[$val->suffix]
	            		));
	            	}
	            }
	        }
	        $this->view->message = "Texty úspěšně upraveny";
	        
	    }
	    
	    $allTranslates 			= $socialniSiteLangDb->getAllItems(null,"lang");
	    $allTranslatesArr		= array();
	    foreach ($allTranslates as $val){
	        $allTranslatesArr[$val->lang][$val->socialIconsID] = $val;
	    }
	    
	    $dataArr = $socialniSite->getAll("cz",1,"priority");
	    //nastavime vsechny jazyky
	    //jazyky vzdy prelozime a ulozime do pole
	    foreach($dataArr as $key => $item){
	        $dataArr[$key]->url = array();
	        foreach($this->allLanguageMutations as $val){
	            (isset($allTranslatesArr[$val->suffix][$item->socialIconsID])) ? $dataArr[$key]->url[$val->suffix] = $allTranslatesArr[$val->suffix][$item->socialIconsID]->url : $dataArr[$key]->url[$val->suffix] = "";
	            
	        }
	    }
	    
	    $data = array();
	    foreach($dataArr as $key => $item){
	        $data[$item->socialIconsID] = $item;
	    }
	    $this->view->allItems = $data;
	    //vlozime placeholdery
	    $this->addPlaceholders();
	}
	function textAction()
	{

		$homepageText			= new Models_HomepageText();
		$homoepageTextLangDb	= new  Models_HomepageTextLang();
		$language 				= new Models_Language_Language();
		
		$id       = $this->_request->getParam('id');
		$added    = $this->_request->getParam('added');
		$enter    = $this->_request->getPost("enter");

		$this->view->subSelected = "Text";
	
		$this->setSublinks($id);
		 
		$this->view->action = "/admin/paticka/text/";
		 
		$dataArr = $homepageText->getAll("cz",1,"HT.homepageTextID");
				
		//vybereme vsechny jazykove mutace
		$this->allLanguageMutations = $this->view->allLanguageMutations = $language->getDbLanguages();
	
		if($enter){
	
			$script = new Library_Scripts();
			$filter = $this->setData();
			 
			for($i = 1; $i <= 3; $i++){
				$param = "text" . $i;
				$data = array("text" => $this->{$param}['cz']);
				$homepageText->updateData($data, "homepageTextID = $i");

				foreach($this->allLanguageMutations as $val){
					
					$count = $homoepageTextLangDb->getCount("homepageTextID = $i AND lang = '$val->suffix'");
					
					if($count > 0){
						$homoepageTextLangDb->updateData(array(
							"text" => 	$this->{$param}[$val->suffix]
						), "homepageTextID = $i AND lang = '$val->suffix'");
					}else{
						$homoepageTextLangDb->insertData(array(
							"text"           => $this->{$param}[$val->suffix],
							"homepageTextID" => $i,
							"lang"           => $val->suffix
						));
					}
				}
			}

			$this->view->message = "Text úspěšně upraven";
			 
		}
		
		$allTranslates 			= $homoepageTextLangDb->getAllItems(null,"lang");
		$allTranslatesArr		= array();
		foreach ($allTranslates as $val){
			$allTranslatesArr[$val->lang][$val->homepageTextID] = $val;
		}
			
		//nastavime vsechny jazyky
		//jazyky vzdy prelozime a ulozime do pole
		foreach($dataArr as $key => $item){
			$dataArr[$key]->text = array();
			foreach($this->allLanguageMutations as $val){
			    (isset($allTranslatesArr[$val->suffix][$item->homepageTextID])) ? $dataArr[$key]->text[$val->suffix] = $allTranslatesArr[$val->suffix][$item->homepageTextID]->text : $dataArr[$key]->text[$val->suffix] = "";
				
			}
		}
		
		$data = array();
		foreach($dataArr as $key => $item){
			$data[$item->homepageTextID] = $item;
		}
				
		$this->view->allItems = $data;
		//vlozime placeholdery
		$this->addPlaceholders();
	}	

    
	private function getData(){
		
		$data = array(
			"showIcon1"     					=> $this->_request->getPost("showIcon1"),
			"showIcon2"     					=> $this->_request->getPost("showIcon2"),
			"showIcon3"     					=> $this->_request->getPost("showIcon3"),
			"showIcon4"     					=> $this->_request->getPost("showIcon4"),
			"showIcon5"     					=> $this->_request->getPost("showIcon5"),
			"showIcon6"     					=> $this->_request->getPost("showIcon6")
        );
		
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
				$data["text1-".$val->suffix] 		= $this->_request->getPost("text1-".$val->suffix);
				$data["text2-".$val->suffix] 		= $this->_request->getPost("text2-".$val->suffix);
				$data["text3-".$val->suffix] 		= $this->_request->getPost("text3-".$val->suffix);
				$data["text4-".$val->suffix] 		= $this->_request->getPost("text4-".$val->suffix);
				$data["text5-".$val->suffix] 		= $this->_request->getPost("text5-".$val->suffix);
				$data["text6-".$val->suffix] 		= $this->_request->getPost("text6-".$val->suffix);
				$data["text7-".$val->suffix] 		= $this->_request->getPost("text7-".$val->suffix);
				$data["text8-".$val->suffix] 		= $this->_request->getPost("text8-".$val->suffix);
				$data["text9-".$val->suffix] 		= $this->_request->getPost("text9-".$val->suffix);
				$data["text10-".$val->suffix] 		= $this->_request->getPost("text10-".$val->suffix);
				$data["text11-".$val->suffix] 		= $this->_request->getPost("text11-".$val->suffix);
				$data["text12-".$val->suffix] 		= $this->_request->getPost("text12-".$val->suffix);
				$data["text13-".$val->suffix] 		= $this->_request->getPost("text13-".$val->suffix);
				$data["text14-".$val->suffix] 		= $this->_request->getPost("text14-".$val->suffix);
				$data["text15-".$val->suffix] 		= $this->_request->getPost("text15-".$val->suffix);
				$data["text16-".$val->suffix] 		= $this->_request->getPost("text16-".$val->suffix);
				$data["text17-".$val->suffix] 		= $this->_request->getPost("text17-".$val->suffix);
				$data["text18-".$val->suffix] 		= $this->_request->getPost("text18-".$val->suffix);
				$data["text19-".$val->suffix] 		= $this->_request->getPost("text19-".$val->suffix);
				$data["url1-".$val->suffix] 		= $this->_request->getPost("url1-".$val->suffix);
				$data["url2-".$val->suffix] 		= $this->_request->getPost("url2-".$val->suffix);
				$data["url3-".$val->suffix] 		= $this->_request->getPost("url3-".$val->suffix);
				$data["url4-".$val->suffix] 		= $this->_request->getPost("url4-".$val->suffix);
				$data["url5-".$val->suffix] 		= $this->_request->getPost("url5-".$val->suffix);
				$data["url6-".$val->suffix] 		= $this->_request->getPost("url6-".$val->suffix);
				
		}    
		
        return $data;

	}
	
	private function setData(){
		
		$filters    = $this->setFilters();
	    $validators = $this->setValidators();
	    $data       = $this->getData();
	    $script		= new Library_Scripts();
	    $filter = new Zend_Filter_Input($filters, $validators, $data);

	    $this->showIcon1    					= $filter->getUnescaped("showIcon1");
	    $this->showIcon2    					= $filter->getUnescaped("showIcon2");
	    $this->showIcon3    					= $filter->getUnescaped("showIcon3");
	    $this->showIcon4    					= $filter->getUnescaped("showIcon4");
	    $this->showIcon5    					= $filter->getUnescaped("showIcon5");
	    $this->showIcon6    					= $filter->getUnescaped("showIcon6");
	    
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$this->text1[$val->suffix] 			= $filter->getUnescaped("text1-".$val->suffix);
			$this->text2[$val->suffix] 			= $filter->getUnescaped("text2-".$val->suffix);
			$this->text3[$val->suffix] 			= $filter->getUnescaped("text3-".$val->suffix);
			$this->text4[$val->suffix] 			= $filter->getUnescaped("text4-".$val->suffix);
			$this->text5[$val->suffix] 			= $filter->getUnescaped("text5-".$val->suffix);
			$this->text6[$val->suffix] 			= $filter->getUnescaped("text6-".$val->suffix);
			$this->text7[$val->suffix] 			= $filter->getUnescaped("text7-".$val->suffix);
			$this->text8[$val->suffix] 			= $filter->getUnescaped("text8-".$val->suffix);
			$this->text9[$val->suffix] 			= $filter->getUnescaped("text9-".$val->suffix);
			$this->text10[$val->suffix] 		= $filter->getUnescaped("text10-".$val->suffix);
			$this->text11[$val->suffix] 		= $filter->getUnescaped("text11-".$val->suffix);
			$this->text12[$val->suffix] 		= $filter->getUnescaped("text12-".$val->suffix);
			$this->text13[$val->suffix] 		= $filter->getUnescaped("text13-".$val->suffix);
			$this->text14[$val->suffix] 		= $filter->getUnescaped("text14-".$val->suffix);
			$this->text15[$val->suffix] 		= $filter->getUnescaped("text15-".$val->suffix);
			$this->text16[$val->suffix] 		= $filter->getUnescaped("text16-".$val->suffix);
			$this->text17[$val->suffix] 		= $filter->getUnescaped("text17-".$val->suffix);
			$this->text18[$val->suffix] 		= $filter->getUnescaped("text18-".$val->suffix);
			$this->text19[$val->suffix] 		= $filter->getUnescaped("text19-".$val->suffix);
			$this->url1[$val->suffix] 			= $filter->getUnescaped("url1-".$val->suffix);
			$this->url2[$val->suffix] 			= $filter->getUnescaped("url2-".$val->suffix);
			$this->url3[$val->suffix] 			= $filter->getUnescaped("url3-".$val->suffix);
			$this->url4[$val->suffix] 			= $filter->getUnescaped("url4-".$val->suffix);
			$this->url5[$val->suffix] 			= $filter->getUnescaped("url5-".$val->suffix);
			$this->url6[$val->suffix] 			= $filter->getUnescaped("url6-".$val->suffix);
		}		
		
	    return $filter;
	}
	
	
	private function setFilters(){
		
		$filters = array(
        );
		
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$validators["text1-".$val->suffix] 			= 'StripTags';
			$validators["text2-".$val->suffix] 			= 'StripTags';
			$validators["text3-".$val->suffix] 			= 'StripTags';
			$validators["text4-".$val->suffix] 			= 'StripTags';
		}
		
        return $filters;
		
	}
	
	private function setValidators(){
	
			
		$validators = array(
			'showIcon1' => array(
					'allowEmpty' => true
			),
			'showIcon2' => array(
					'allowEmpty' => true
			),
			'showIcon3' => array(
					'allowEmpty' => true
			),
			'showIcon4' => array(
					'allowEmpty' => true
			),
			'showIcon5' => array(
					'allowEmpty' => true
			),
			'showIcon6' => array(
					'allowEmpty' => true
			)
				
        );
        //nastavime vsechny jazykove verze
		foreach($this->allLanguageMutations as $val){
			$validators["text1-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text2-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text3-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text4-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text5-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text6-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text7-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text8-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text9-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text10-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text11-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text12-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text13-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text14-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text15-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text16-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text17-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text18-".$val->suffix]['allowEmpty'] 		= true;
			$validators["text19-".$val->suffix]['allowEmpty'] 		= true;
			$validators["url1-".$val->suffix]['allowEmpty'] 		= true;
			$validators["url2-".$val->suffix]['allowEmpty'] 		= true;
			$validators["url3-".$val->suffix]['allowEmpty'] 		= true;
			$validators["url4-".$val->suffix]['allowEmpty'] 		= true;
			$validators["url5-".$val->suffix]['allowEmpty'] 		= true;
			$validators["url6-".$val->suffix]['allowEmpty'] 		= true;
			$validators["showIcon1-".$val->suffix]['allowEmpty'] 		= true;
			$validators["showIcon2-".$val->suffix]['allowEmpty'] 		= true;
			$validators["showIcon3-".$val->suffix]['allowEmpty'] 		= true;
			$validators["showIcon4-".$val->suffix]['allowEmpty'] 		= true;
			$validators["showIcon5-".$val->suffix]['allowEmpty'] 		= true;
			$validators["showIcon6-".$val->suffix]['allowEmpty'] 		= true;
		}
		
        return $validators;
		
	}
	
	

	private function setSublinks($id = 0){
		
		$this->view->subLinks = array();

		$this->view->subLinks[] = array(
				"title" => 	"Kontakty v hlavičce",
				"url"	=>	"/admin/homepage-texty/kontakty-hlavicka/"
		);
		$this->view->subLinks[] = array(
				"title" => 	"Bloky pod banery",
				"url"	=>	"/admin/homepage-texty/bloky-pod-banery/"
		);
		$this->view->subLinks[] = array(
				"title" => 	"Text pod bočním menu",
				"url"	=>	"/admin/homepage-texty/text-pod-bocnim-menu/"
		);
		$this->view->subLinks[] = array(
				"title" => 	"Texty v patičce",
				"url"	=>	"/admin/homepage-texty/paticka/"
		);
		$this->view->subLinks[] = array(
				"title" => 	"Sociální sítě",
				"url"	=>	"/admin/homepage-texty/socialni-site/"
		);
	
	}
    
}

?>