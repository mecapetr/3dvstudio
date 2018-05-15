<?php

 class Library_UploadGraphics {
    
     public $formName;        	// nazev inputu ve formulari pro upload souboru
     public $path;            	// nastaveni cesty k uploadu souboru
     public $fileName;        	// nazev nahravaneho souboru
     public $tmpFileName;       // tmp název nahravaneho souboru
     public $ownName = false; 	// pokud si chceme nastavit vlastni nazev souboru
     public $extension;		  	// pripona souboru
     public $mimeType;		  	// mimetyp souboru
     public $size;		  		// velikost souboru
     public $allowSize = 3000000;// povolená velikost souboru 3M
     public $uploaded = false;	// jestli byl soubor nahrán
     public $error;	            // error při uploadu
     public $allowedExtensions = array("jpg","png","gif","jpeg","bmp","doc","docx","odt","pdf","xls","ods","xlsx","txt","cdr","psd","ai","dwg","zip");  
     public $allowedMimetypes  = array(	"image/jpeg",
     									"image/pjpeg",
     									"image/png",
     									"image/gif",
     									"image/bmp",
     									"application/msword",
     									"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     									"application/vnd.oasis.opendocument.text",
     									"application/x-vnd.oasis.opendocument.text",
     									"application/pdf",
     									"application/excel",
     									"application/vnd.ms-excel",
     									"application/x-excel",
     									"application/x-msexcel",
     									"application/vnd.oasis.opendocument.spreadsheet, application/x-vnd.oasis.opendocument.spreadsheet",
     									"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     									"text/plain",
     									"text/plain",
     									"application/cdr",
     									"application/coreldraw",
     									"application/x-cdr",
     									"application/x-coreldraw",
     									"image/cdr",
     									"image/x-cdr",
     									"zz-application/zz-winassoc-cdr",
     									"image/photoshop",
     									"image/x-photoshop",
     									"image/psd",
     									"application/photoshop",
     									"application/psd",
     									"application/octet-stream",
     									"zz-application/zz-winassoc-psd",
     									"application/postscript",
                                        "application/x-download",
                                        "application/acad",
                                        "image/vnd.dwg",
                                        "image/x-dwg",
                                        "application/x-compressed",
                                        "application/x-zip-compressed",
                                        "application/zip",
                                        "multipart/x-zip");   
    
     public function upload()
    {
    	$fileName  = "";
        if($this->ownName){
        	$fileName = $this->fileName;
        }else{
        	$fileName = $this->getName();  // vybere nazev nahravaneho souboru
        }

        if(!empty($fileName)){
	        
        	$tmpFileName = $this->getTmpName();      // vybere tmp nahravaneho souboru
        	if(!empty($this->tmpFileName))$tmpFileName = $this->tmpFileName;
        	
	        $fileName = $this->checkFile($fileName); // zjisti jestli uz soubor existuje

	        if(!empty($this->mimeType)){
        		if(in_array($this->extension, $this->allowedExtensions) && in_array($this->mimeType, $this->allowedMimetypes)){	// jestli ma soubor spravnou koncovku
        	 	
	                if(move_uploaded_file($tmpFileName,'./'.$this->path.'/'.$fileName)){
	                	
	                	 if($this->getFileSize($fileName) <= $this->allowSize){
		                     $this->fileName = $fileName;
		    				 $this->uploaded = true;
	                	 }else{
	                	 	unlink('./'.$this->path.'/'.$fileName);
	                	 	
	                	 	$this->error = "Soubor s názvem <strong>$fileName</strong> nebyl nahrán, protože jeho velikost je větší než ".$this->allowSize."B.";
	                	 	return "3";
            	
	                	 }
	                }
        		
	         	}else{
	         		$this->error = "Soubor s názvem <strong>$fileName</strong> nebyl nahrán, protože má nepovolenou příponu.";
	                return "3";
	    
	            }
            }else{
            	$this->error = "Soubor s názvem <strong>$fileName</strong> nebyl nahrán, protože jeho velikost je větší než ".ini_get("upload_max_filesize").".";
            	return "3";
            	 
            }
        }
    }
    
    
    private function checkFile($fileName){
        
        if(file_exists('./'.$this->path.'/'.$fileName)){
                                        
            $ide = rand(0,9999);
            $fileName = $ide.'_'.$fileName;
            
        }
        return $fileName;
        
    }
    
    
    private function lowercase($name){
        
        return strtolower($name);
    }
    
    private function getName(){
    
        @$name = $_FILES[$this->formName]['name'];
        
    	if(!empty($name)){
	        $name  = $this->niceFile($name);
	        $this->mimeType = $_FILES[$this->formName]['type'];	// nastavime mime typ souboru
	        return $name;        
    	}else return $name;
    
    }
    
    private function getTmpName(){
    
        @$name = $_FILES[$this->formName]['tmp_name'];
        return $name;
    
    }
    private function getFileSize($fileName){
    
        @$size		= filesize('./'.$this->path.'/'.$fileName);
        $this->size = $size;
        return $size;
    
    }
    
    public function niceFile($file){
	    	$scripts = new Library_Scripts();

	   		$ex = explode('.',$file);
	    	$ext = end($ex);
		    
		    $basename = basename("./".$this->path."/".$ex[0],'.'.$ext);
		    $basename = $scripts->url($basename);
		    $lowerFile = $basename.".".$ext;
			$this->extension = $ext;		// ulozime proponu
	    	return $lowerFile;
    }
    
   
} 