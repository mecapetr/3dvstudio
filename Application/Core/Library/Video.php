<?php

class Library_Video {        

	private $second;              // v ktere sekunde bude vytvoreny nahled
	private $file;                // nazev souboru videa
	private $previewWidth;        // sirka velkeho nahledu videa
	private $previewHeight;       // v�ska velkeho nahledu videa
	private $smallPreviewWidth;   // sirka maleho nahledu videa
	private $smallPreviewHeight;  // v�ska maleho nahledu videa
	private $web;                 // root adresar webu
	private $videoDuration;       // delka videa (vypocitava se ve skriptu)
	private $folder;              // adresar v Public/Images/Previews/  ktery oznacuje k cemu dane nahledy patri. Napriklad aktuality, clanky, videogalerie atd. 
	
    function __construct($previewWidth = "512",$previewHeight = "288",$smallPreviewWidth = "128",$smallPreviewHeight = "0",$second = 5){
        $this->previewWidth       = $previewWidth;
        $this->previewHeight      = $previewHeight;
        $this->smallPreviewWidth  = $smallPreviewWidth;
        $this->smallPreviewHeight = $smallPreviewHeight;	
        $this->second             = $second;   
    }
    
    public function makePreview(){

    	if($this->web){
	    	$url = "Public/Videos/Temp/".$this->file.".mp4";
	    	
			ob_start();
		    passthru("/usr/bin/ffmpeg -i \"{$url}\" 2>&1");
		    $duration = ob_get_contents();
		    ob_end_clean();
		      
		    $search              = '/Duration: (.*?),/';
		    $duration            = preg_match($search, $duration, $matches, PREG_OFFSET_CAPTURE, 3);
		    $videoDuration       = $matches[1][0];
	        $dur                 = explode(":",$videoDuration);
		    
	        //delka videa
	        $this->videoDuration = $dur[0]*3600+$dur[1]*60+(int)$dur[2];
	    	    
	        //nahled
		    $command="ffmpeg -itsoffset -".$this->second." -i ".$url." -vcodec png -vframes 1 -an -f rawvideo -s ".$this->previewWidth."x".$this->previewHeight." /var/www/".$this->web."/Public/Images/Previews/".$this->folder."/".$this->file.".png";
		    exec($command);
		    
		    // resizne maly nahled na pozadovanou velikost
		    $this->resizePreview();
		    
    	}else{
    		
    	}
    	
    }
    
    private function resizePreview(){
    	$resize = new Library_UploadFiles();
    	$resize->path     = "Public/Images/Previews/".$this->folder;
    	$resize->fileName = $this->file.".png";
    	$resize->imageResize("mala",$this->smallPreviewHeight,$this->smallPreviewWidth);
    }
        
    public function setPreviewWidth($previewWidth){
    	$this->previewWidth  = $previewWidth;
    }
    
    public function setPreviewHeight($previewHeight){
    	$this->previewHeight  = $previewHeight;
    }
    
	public function setFile($file){
    	$this->file = $file;
    }
    
	public function setPath($path){
    	$this->path = $path;
    }
	public function setSecond($second){
    	$this->second = $second;
    }
	public function setWeb($web){
    	$this->web = $web;
    }
	public function setFolder($folder){
    	$this->folder = $folder;
    }
    
	public function getVideoDuration(){
    	return $this->videoDuration;
    }
	
} 