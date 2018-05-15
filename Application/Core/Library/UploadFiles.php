<?php

 class Library_UploadFiles {
    
     public $formName;         // nazev inputu ve formulari pro upload souboru
     public $path;             // nastaveni cesty k uploadu souboru
     public $smallWidth;         // nastaveni sirky pro zmenseni maleho obrazku
     public $smallHeight;        // nastaveni vysky pro zmenseni maleho obrazku
     public $middleWidth;      // nastaveni sirky pro zmenseni stredne velkeho obrazku
     public $middleHeight;     // nastaveni vysky pro zmenseni stredne velkeho obrazku
     public $largeWidth;      // nastaveni sirky pro zmenseni velkeho obrazku
     public $largeHeight;     // nastaveni vysky pro zmenseni velkeho obrazku
     public $maxiWidth;      // nastaveni sirky pro zmenseni nejvetsiho  obrazku
     public $maxiHeight;     // nastaveni vysky pro zmenseni nejvetsiho obrazku
     public $maxHeight;        // nastaveni maximalni vysky pro uploadovany obrazek
     public $maxWidth;         // nastaveni maximalni sirky pro uploadovany obrazek
     public $minHeight;        // nastaveni minimalni vysky pro uploadovany obrazek
     public $minWidth;         // nastaveni minimalni sirky pro uploadovany obrazek
     public $widthSide;        // pomer sirky
     public $heightSide;       // pomer vysky
     public $fileName;         // nazev nahravaneho souboru
     public $tmpFileName;      // tmp nahravaného souboru
     public $ownName = false;  // pokud si chceme nastavit vlastni nazev souboru
     public $checkFile = true; // kontroluje zda nazev souboru uz existuje nebo ne
     public $resizeByLongerSide = false;	//zda se bude fotka resizovat podle delsi strany
     public $resizeByShorterSide= false;	//zda se bude fotka resizovat podle kratsi strany
     public $uploaded = false;	// jestli byl soubor nahrán
     public $error;	            // error při uploadu
     public $allowSize = 200000000;   // povolena velikost souboru 200M

     public $autoCropPhoto		= false;	//cropne automaticky fotky na stred
          
    public function upload()
    {
        $fileName = $this->getName();  // vybere nazev nahravaneho souboru
        if($this->ownName)$fileName = $this->fileName;
        
        if(empty($this->tmpFileName))$this->tmpFileName = $this->getTmpName();
                
        if($this->checkFile){
        	$fileName = $this->checkFile($fileName); // zjisti jestli uz soubor existuje
        }

        
        if(!empty($fileName) && !empty($this->tmpFileName)){
                      
                if(move_uploaded_file($this->tmpFileName,'./'.$this->path.'/'.$fileName)){
                    if($this->checkMaxSize($fileName)){
                        if($this->checkMinSize($fileName)){
                            if($this->checkImageSize($fileName)){
                            	
                            	if($this->checkFileSize($fileName)){
                                          
	                                $this->fileName = $fileName;
	                                
	                                if(!empty($this->smallHeight) && !empty($this->smallWidth) || is_numeric($this->smallHeight) || is_numeric($this->smallWidth)) {
	                                    
	                                    $this->imageResize("mala",$this->smallHeight,$this->smallWidth,$this->resizeByLongerSide,$this->resizeByShorterSide);
	
	                                
	                                }
	                                if(!empty($this->middleHeight) && !empty($this->middleWidth) || is_numeric($this->middleHeight) || is_numeric($this->middleWidth))
	                                {
	                                    $this->imageResize("stredni",$this->middleHeight,$this->middleWidth);
	                                }
	                                
	                                if(!empty($this->largeHeight) && !empty($this->largeWidth) || is_numeric($this->largeHeight) || is_numeric($this->largeWidth))
	                                {
	                                    $this->imageResize("velka",$this->largeHeight,$this->largeWidth);
	                                }
	                                
	                                if(!empty($this->maxiHeight) && !empty($this->maxiWidth) || is_numeric($this->maxiHeight) || is_numeric($this->maxiWidth))
	                                {
	                                    $this->imageResize("maxi",$this->maxiHeight,$this->maxiWidth);
	                                }
	                                
	                                $this->uploaded = true;
                                
                                }else{
                                	$this->error =  "Obrázek s názvem <strong>$fileName</strong> nebyl nahrán, protože jeho velikost je větší než ".$this->allowSize."B.";
                                 }
                                
                            }else{
                                  $this->error =  "Obrázek s názvem <strong>$fileName</strong> nebyl nahrán, protože neměl poměr stran <strong>$this->widthSide : $this->heightSide</strong>. Nahrejte prosím jiný obrázek.";
                            }
                        }else{
                        	$this->error =  "Obrázek s názvem <strong>$fileName</strong> nebyl nahrán, protože velikost obrázku je menší než <strong>$this->minWidth x $this->minHeight</strong>. Nahrejte prosím jiný obrázek."; 
                        }
                    }else{
                    	  $this->error =  "Obrázek s názvem <strong>$fileName</strong> nebyl nahrán, protože velikost obrázku je větší než <strong>$this->maxWidth x $this->maxHeight</strong>. Nahrejte prosím jiný obrázek.";
                    }
    
                }

            
        }
    
    }

    

    /*

     * $newWidth 	- nova sirka obrazku (zadana uzivatelem)

     * $newHeight	- nova vyska obrazku (zadana uzivatelem)

     * $width		- originalni obrazek

     * $height		- originanlni obrazek     * 

     */
 private function calculateCropDimensions($newWidth,$newHeight,$width,$height){    	

    	$ratioNew = $newWidth / $newHeight;  		
    	$ratio    = $width / $height;

    	$cropWidth = $width ;
    	$cropHeight = $height ;
 
    	if($width > $height){

       		if($ratio >= $ratioNew)

       			$cropWidth = $cropHeight * $ratioNew;

       		else if($ratio < $ratioNew)

       			$cropHeight = $cropWidth / $ratioNew;
       

      	}else{

       		if($ratio <= $ratioNew)

        		$cropHeight = $cropWidth / $ratioNew;

       		else

        		$cropWidth = $cropHeight * $ratioNew;

      	}

    	$x1 = ($width / 2)  - ($cropWidth / 2);

    	$y1 = ($height / 2) - ($cropHeight / 2);

		return array("x1" => $x1,"y1" => $y1,"cropWidth" => $cropWidth,"cropHeight" => $cropHeight);

    }
    public function imageResize($prefix,$resizedHeight,$resizedWidth,$resizeByLongerSide = false,$resizeByShorterSide = false,$x1=0,$y1=0,$x2=0,$y2=0){
		

        //zjisteni udaju o zmensovanem obrazku

        list($width, $height, $type, $atr) = getimagesize('./'.$this->path.'/'.$this->fileName);

            		
        $newHeight = $resizedHeight;
        $newWidth = $resizedWidth;
        
        if($newHeight > $height)$newHeight = $height;
        if($newWidth  > $width)$newWidth   = $width;

    	if($resizeByLongerSide){
            $sideSize = 0;
            if($newWidth == 0)
                 $sideSize = $newHeight;
            else
                 $sideSize = $newWidth;

            if ($width < $height)
            {
                $newHeight = $sideSize;
                $newWidth = $width * $newHeight / $height;
            }else{
                $newWidth = $sideSize;
                $newHeight = $height * $newWidth / $width;
            }

         }else if($resizeByShorterSide){
            $sideSize = 0;
            if($newWidth == 0)
                 $sideSize = $newHeight;
            else
                 $sideSize = $newWidth;

            if ($height < $width)
            {
                $newHeight = $sideSize;
                $newWidth = $width * $newHeight / $height;
            }else{
                $newWidth = $sideSize;
                $newHeight = $height * $newWidth / $width;
            }

         }else{
	        if($newHeight == 0){
	            
	            $aspectRatio = $width / $newWidth;
	            $newHeight = $height / $aspectRatio;
	            
	        }
	        
	        if($newWidth == 0){
	            
	            $aspectRatio = $height / $newHeight;
	            $newWidth = $width / $aspectRatio;
	            
	        }
        }
        
        $out = ImageCreateTrueColor ($newWidth, $newHeight);
        touch('./'.$this->path.'/'.$prefix.'-'.$this->fileName);//vytvori predem nejaky soubor a do neho ulozi ten zmenseny
    

    	if($this->autoCropPhoto){

    		$cropDimensions = $this->calculateCropDimensions($newWidth,$newHeight,$width,$height);     		

    		$x1 = $cropDimensions["x1"];

    		$y1 = $cropDimensions["y1"];  		

    		$x2 = 0;

    		$y2 = 0;

    		$width 	= $cropDimensions["cropWidth"];

    		$height = $cropDimensions["cropHeight"];

    	}

    	
        //jestli to je gif
        if ($type == 1)
        {
            $source = imagecreatefromgif ('./'.$this->path.'/'.$this->fileName);
            imagecopyresampled ($out, $source,$x2,$y2,$x1,$y1,$newWidth,$newHeight,$width,$height);//zmensi obrazek
            ImageGif ($out,'./'.$this->path.'/'.$prefix.'-'.$this->fileName, 75);
        }
        //jestli to je jpg
        elseif ($type == 2)
        {
            $source = ImageCreateFromJpeg ('./'.$this->path.'/'.$this->fileName);
            imagecopyresampled ($out, $source,$x2,$y2,$x1,$y1,$newWidth,$newHeight,$width,$height);
            ImageJpeg ($out,'./'.$this->path.'/'.$prefix.'-'.$this->fileName, 75);
        }
        //jestli to je png
        elseif ($type == 3)
        {
            
            imagealphablending($out, false);
            imagesavealpha($out, true);
            $transparent = imagecolorallocatealpha($out, 255, 255, 255, 127);
            imagefilledrectangle($out, 0, 0, $width, $height, $transparent);

            $source = ImageCreateFromPng ('./'.$this->path.'/'.$this->fileName);
            imagecopyresampled ($out, $source,$x2,$y2,$x1,$y1,$newWidth,$newHeight,$width,$height);
            ImagePng ($out,'./'.$this->path.'/'.$prefix.'-'.$this->fileName);
        }
        ImageDestroy($out);
        ImageDestroy($source);
        
    }
    
    private function checkFile($fileName){
        
        if(file_exists('./'.$this->path.'/'.$fileName)){
                                        
            $ide = rand(0,9999);
            $fileName = $ide.'_'.$fileName;
            
        }
        return $fileName;
        
    }
    
    private function checkImageSize($fileName){
        
        if(!empty($this->widthSide) && !empty($this->heightSide)){
            
            list($width, $height, $type, $atr) = getimagesize('./'.$this->path.'/'.$fileName);
            
            $aspectRatioWidth = $width / $this->widthSide;
            $aspectRatioHeight = $height / $this->heightSide;
            
            if(abs($aspectRatioWidth - $aspectRatioHeight) <= 2){
                return true;
            }else{
                unlink('./'.$this->path.'/'.$fileName);
                return false;
            }
            
        }else{
            
            return true;
            
        }
        
    }
    
    private function checkFileSize($fileName){
        
    	$fileSize = filesize("./".$this->path."/".$fileName);

    	if($fileSize <= $this->allowSize){
    		return true;
    	}else{
    		unlink('./'.$this->path.'/'.$fileName);
    		return false;
    	}
    
    }
    
    private function checkMaxSize($fileName){
        
        list($width, $height, $type, $atr) = getimagesize('./'.$this->path.'/'.$fileName);
        
        if(!empty($this->maxHeight) && !empty($this->maxWidth)){
            if($width <= $this->maxWidth && $height <= $this->maxHeight){
                return true;
            }else{
                unlink('./'.$this->path.'/'.$fileName);
                return false;
            }
        }else{
            return true;
        }
        
    }
    
    private function checkMinSize($fileName){
        
        list($width, $height, $type, $atr) = getimagesize('./'.$this->path.'/'.$fileName);
        
        if(!empty($this->minHeight) && !empty($this->minWidth)){
            if($width >= $this->minWidth && $height >= $this->minHeight){
                return true;
            }else{
                unlink('./'.$this->path.'/'.$fileName);
                return false;
            }
        }else{
            return true;
        }
        
    }
    
    private function lowercase($name){
        
        return strtolower($name);
    }
    
    private function getName(){
    
        @$name = $_FILES[$this->formName]['name'];
        if(is_array($name))$name = $name[0];
        $name = $this->niceFile($name);
        return $name;
    
    }
    
    private function getTmpName(){
    
        @$name = $_FILES[$this->formName]['tmp_name'];
        if(is_array($name))$name = $name[0];
        return $name;
    
    }
    
    public function niceFile($file){
    	
    	$scripts = new Library_Scripts();    	
	    $ex = explode('.',$file);
	    $ext = end($ex);
	    $basename = basename("./".$this->path."/".$ex[0],'.'.$ext);
	    $basename = $scripts->url($basename);
	    $lowerFile = $basename.".".$ext;

    	return $lowerFile;
    }
    
   
} 