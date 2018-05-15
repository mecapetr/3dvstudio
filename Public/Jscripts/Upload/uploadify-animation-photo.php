<?php
/*
Uploadify v2.1.0
Release Date: August 24, 2009

Copyright (c) 2009 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
				
if (!empty($_FILES)) {
	
		$link = mysql_connect('localhost:3306', 'root', 'garage');
		if (!$link) {
		    die('Nepřihlášen : ' . mysql_error());
		}
		
		// make foo the current db
		$db_selected = mysql_select_db('maxim', $link);
		if (!$db_selected) {
		    die ('Nenalezena databáze : ' . mysql_error());
		}
	
		$formName  = 'Filedata';
		$folder    = $_REQUEST['folder'];
		$ext       = array("gif","jpg","png","jpeg","GIF","JPG","PNG","JPEG");
		$fileParts = pathinfo($_FILES[$formName]['name']);
		$folderExp = explode("/",$folder);
		$folderExp = end($folderExp);
		$galleryID = explode("-",$folderExp);
		
	    $upload = new Uploader();
	    $upload->connect();
	    $upload->formName  = $formName;
	    $upload->path      = $_SERVER['DOCUMENT_ROOT'] . $folder;
	    if(in_array($fileParts['extension'],$ext)){
		    $upload->newWidth  = 140;
		    $upload->newHeight = 0;
		    $upload->middleWidth  = 0;
		    $upload->middleHeight = 550;
	    }
	    $upload->upload();
        $upload->insertToDb($upload->fileName);
		echo "1";
		
		
}

class Uploader {
    
     public $formName;         // nazev inputu ve formulari pro upload souboru
     public $path;             // nastaveni cesty k uploadu souboru
     public $newWidth;         // nastaveni sirky pro zmenseni obrazku
     public $newHeight;        // nastaveni vysky pro zmenseni obrazku
     public $middleWidth;      // nastaveni sirky pro zmenseni stredne velkeho obrazku
     public $middleHeight;     // nastaveni vysky pro zmenseni stredne velkeho obrazku
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
          
    public function upload()
    {
        if(!($this->ownName))$fileName = $this->getName();  // vybere nazev nahravaneho souboru
        else $fileName = $this->fileName;
        
        $this->tmpFileName = $this->getTmpName();      // vybere tmp nahravaneho souboru
                
        if($this->checkFile){
        	$fileName = $this->checkFile($fileName); // zjisti jestli uz soubor existuje
        }
        if(!empty($fileName) && !empty($this->tmpFileName)){
                      
                if(move_uploaded_file($this->tmpFileName,$this->path.'/'.$fileName)){
                    if($this->checkMaxSize($fileName)){
                        if($this->checkMinSize($fileName)){
                            if($this->checkImageSize($fileName)){
                                          
                                $this->fileName = $fileName;
                                
                                if(!empty($this->newHeight) && !empty($this->newWidth) || is_numeric($this->newHeight) || is_numeric($this->newWidth)) {
                                    
                                    $this->imageResize("mala",$this->newHeight,$this->newWidth);
                                
                                }
                                if(!empty($this->middleHeight) && !empty($this->middleWidth) || is_numeric($this->middleHeight) || is_numeric($this->middleWidth))
                                {
                                    $this->imageResize("stredni",$this->middleHeight,$this->middleWidth);
                                }
                            }else{
                                    
                                  echo"<script>alert('Obrázek s názvem $fileName nebyl nahrán, protože neměl poměr stran $this->widthSide : $this->heightSide. Můžete ho znovu upravený nahrát v úpravě nabídky')</script>";
            
                            }
                        }else{
                            
                              echo"<script>alert('Obrázek s názvem $fileName nebyl nahrán, protože velikost obrázku je menší než $this->minWidth x $this->minHeight. Můžete ho znovu upravený nahrát v úpravě nabídky')</script>";
    
                        }
                    }else{
                            
                          echo"<script>alert('Obrázek s názvem $fileName nebyl nahrán, protože velikost obrázku je větší než $this->maxWidth x $this->maxHeight. Můžete ho znovu upravený nahrát v úpravě nabídky')</script>";
    
                    }
    
                }

            
        }
    
    }

	public function imageResize($prefix,$resizedHeight,$resizedWidth){

        $newHeight = $resizedHeight;
        $newWidth = $resizedWidth;
        //zjisteni udaju o zmensovanem obrazku
        list($width, $height, $type, $atr) = getimagesize($this->path.'/'.$this->fileName);

        if($newHeight > $height)$newHeight = $height;
        if($newWidth  > $width)$newWidth   = $width;
        
        if($newHeight == 0){
            
            $aspectRatio = $width / $newWidth;
            $newHeight = $height / $aspectRatio;
            
        }
        
        if($newWidth == 0){
            
            $aspectRatio = $height / $newHeight;
            $newWidth = $width / $aspectRatio;
            
            if($newWidth > 1000){
            	$newWidth  = 1000;
            	$aspectRatio = $width / $newWidth;
            	$newHeight = $height / $aspectRatio;
            }
            
        }
        
        $out = ImageCreateTrueColor($newWidth, $newHeight);
        touch($this->path.'/'.$prefix.'-'.$this->fileName);//vytvori predem nejaky soubor a do neho ulozi ten zmenseny
        $source = "";
        
        //jestli to je gif
        if ($type == 1)
        {
            $source = ImageCreateFromGif($this->path.'/'.$this->fileName);
            ImageCopyResampled($out, $source,0,0,0,0,$newWidth,$newHeight,$width,$height);//zmensi obrazek
            ImageGif($out,$this->path.'/'.$prefix.'-'.$this->fileName,100);
        }
        //jestli to je jpg
        elseif ($type == 2)
        {
            $source = ImageCreateFromJpeg($this->path.'/'.$this->fileName);
            ImageCopyResampled($out, $source,0,0,0,0,$newWidth,$newHeight,$width,$height);
            ImageJpeg($out,$this->path.'/'.$prefix.'-'.$this->fileName,100);
        }
        //jestli to je png
        elseif ($type == 3)
        {
            $source = ImageCreateFromPng($this->path.'/'.$this->fileName);
            ImageCopyResampled($out, $source,0,0,0,0,$newWidth,$newHeight,$width,$height);
            ImagePng($out,$this->path.'/'.$prefix.'-'.$this->fileName);
        }
        ImageDestroy($out);
        ImageDestroy($source);
        
    }
    
    private function checkFile($fileName){
        
        if(file_exists($this->path.'/'.$fileName)){
                                        
            $ide = rand(0,9999);
            $fileName = $ide.'_'.$fileName;
            
        }
        return $fileName;
        
    }
    
    private function checkImageSize($fileName){
        
        if(!empty($this->widthSide) && !empty($this->heightSide)){
            
            list($width, $height, $type, $atr) = getimagesize($this->path.'/'.$fileName);
            
            $aspectRatioWidth = $width / $this->widthSide;
            $aspectRatioHeight = $height / $this->heightSide;
            
            if(abs($aspectRatioWidth - $aspectRatioHeight) <= 2){
                return true;
            }else{
                unlink($this->path.'/'.$fileName);
                return false;
            }
            
        }else{
            
            return true;
            
        }
        
    }
    
    private function checkMaxSize($fileName){
        
        list($width, $height, $type, $atr) = getimagesize($this->path.'/'.$fileName);
        
        if(!empty($this->maxHeight) && !empty($this->maxWidth)){
            if($width <= $this->maxWidth && $height <= $this->maxHeight){
                return true;
            }else{
                unlink($this->path.'/'.$fileName);
                return false;
            }
        }else{
            return true;
        }
        
    }
    
    private function checkMinSize($fileName){
        
        list($width, $height, $type, $atr) = getimagesize($this->path.'/'.$fileName);
        
        if(!empty($this->minHeight) && !empty($this->minWidth)){
            if($width >= $this->minWidth && $height >= $this->minHeight){
                return true;
            }else{
                unlink($this->path.'/'.$fileName);
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
        $name = $this->niceFile($name);
        return $name;
    
    }
    
    private function getTmpName(){
    
        @$name = $_FILES[$this->formName]['tmp_name'];
        return $name;
    
    }
    
    private function niceFile($file){
    	
    	$lowerFile = strtolower($file);
	    $ex = explode('.',$lowerFile);
	    $ext = end($ex);
	    $basename = basename("./".$this->path."/".$ex[0],'.'.$ext);
	    $basename = $this->url($basename);
	    $lowerFile = $basename.".".$ext;

    	return $lowerFile;
    }
    
	function url($title)
    {
		setlocale(LC_ALL, 'en_US.UTF8');
    	
		$url = $title;
		$url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
		$url = trim($url, "-");
		$url = iconv("utf-8", "ASCII//TRANSLIT", $url);
		$url = strtolower($url);
		$url = preg_replace('~[^-a-z0-9_]+~', '', $url);
		return $url;
    }
    
    function insertToDb($file){
    	        
        list($width, $height, $type, $atr) = getimagesize($this->path."/stredni-".$file);
        if($type == 1 || $type == 2 || $type == 3 || $type == 6){
	         mysql_query("
	        	INSERT INTO animationphoto (file,footage,type,width,height)
	        	VALUES('$file','0','photo','$width','$height')
	        ");
        }else{
        	 mysql_query("
	        	INSERT INTO animationphoto (file,footage,type,width,height)
	        	VALUES('$file','0','video','0','0')
	        ");
        }
        
    	
    }
    
    function connect(){
    	
    	
    	
    }
    
       
} 

?>