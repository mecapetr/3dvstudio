<?php

 class Library_CropPhotos {
    
 	private $file;    	     // ID fotky na kropnuti
 	private $crop;      	 // jestli se bude kropovat nebo ne
 	private $path;      	 // cesta k fotce
 	private $request;   	 // request na zběr dat
 	private $smallHeight; 	 // vyska male fotky
 	 
 	private $x1;
	private $y1;
	private $newWidth;
	private $newHeight;
     
 	public function execute(){

 		$photo = new Models_Photo();
 		if(!empty($this->crop) && $this->crop != "undefined"){
	 			
		    //data pro kropnuti
			$this->getCropData();
		
			if($this->newHeight!=="" && $this->newWidth!=="" && $this->x1!=="" && $this->y1!==""){
			    //kropnuti originální fotky
				$this->imageResize("stredni-".$this->file,$this->newHeight,$this->newWidth,$this->x1,$this->y1);
				    	
				//vytvoreni nahledu z kropnute fotky
				$upload = new Library_UploadFiles();
				$upload->fileName = "stredni-".$this->file;
				$upload->path     = $this->path;
				$upload->imageResize("mala",$this->smallHeight,0);
				unlink("./".$this->path."/mala-".$this->file);
				rename("./".$this->path."/mala-stredni-".$this->file,"./".$this->path."/mala-".$this->file);
				    				    
			}

		}
		
		return array(
			'width'  => $this->newWidth,
			'height' => $this->newHeight
		);
	
 	}
 	
 	private function getCropData(){
		
		$this->x1        = $this->request->getPost("x1");
	    $this->y1        = $this->request->getPost("y1");
	    $this->newWidth  = $this->request->getPost("newWidth");
	    $this->newHeight = $this->request->getPost("newHeight");
		
	}
    
	private function imageResize($fileName,$height,$width,$x1,$y1){

        list($oldWidth, $oldHeight, $type, $atr) = getimagesize('./'.$this->path.'/'.$fileName);
                
        $out = ImageCreateTrueColor ($width, $height);
        touch('./'.$this->path.'/'.$fileName);//vytvori predem nejaky soubor a do neho ulozi ten zmenseny
        
        //jestli to je gif
        if ($type == 1)
        {
            $source = imagecreatefromgif ('./'.$this->$this->path.'/'.$fileName);
            imagecopyresampled ($out, $source,0,0,$x1,$y1,$width,$height,$width,$height);//zmensi obrazek
            ImageGif ($out,'./'.$this->path.'/'.$fileName, 100);
        }
        //jestli to je jpg
        elseif ($type == 2)
        {
            $source = ImageCreateFromJpeg ('./'.$this->path.'/'.$fileName);
            imagecopyresampled ($out, $source,0,0,$x1,$y1,$width,$height,$width,$height);
            ImageJpeg ($out,'./'.$this->path.'/'.$fileName, 100);
        }
        //jestli to je png
        elseif ($type == 3)
        {
            $source = ImageCreateFromPng ('./'.$this->path.'/'.$fileName);
            imagecopyresampled ($out, $source,0,0,$x1,$y1,$width,$height,$width,$height);
            ImagePng ($out,'./'.$this->path.'/'.$fileName);
        }
        ImageDestroy($out);
        ImageDestroy($source);
        
    }
    
   public function setFile($file){
       $this->file = $file;
   }
   public function setCrop($crop){
       $this->crop = $crop;
   }
   public function setPath($path){
       $this->path = $path;
   }
   public function setRequest($request){
       $this->request = $request;
   }
   public function setSmallHeight($smallHeight){
       $this->smallHeight = $smallHeight;
   }
   
} 