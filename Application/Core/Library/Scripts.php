<?php

 class Library_Scripts {

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
	
	function transformToDiakritic($text){

	     $prvky_z = array("&aacute;","&scaron;","&eacute;","&iacute;","&oacute;","&scaron;","&uacute;","&yacute;","&nbsp;","&Uacute;");
		 $prvky_do = array("á","š","é","í","ó","š","ú","ý"," ","Ú");
		 $vysledne_slovo = str_replace($prvky_z,$prvky_do,$text);
		 
		 return $vysledne_slovo;
	 
	}

	function getAuctionTime($date){
		 
		$str = strtotime($date) - time();
		 
		if($str > 0){
			$days = (int)($str / 86400);
	
			$mod = $str % 86400;
			$hours = (int)($mod / 3600);
	
			$mod = $mod % 3600;
			$minutes = (int)($mod / 60);
	
			$seconds = $mod % 60;
	
			if($hours < 10)$hours     = "0".$hours;
			if($minutes < 10)$minutes = "0".$minutes;
			if($seconds < 10)$seconds = "0".$seconds;
	
			return $days."d ".$hours.":".$minutes.":".$seconds;
		}else{
			return "aukce skončila";
		}
		 
	}
	function createFile($adress){
    	if(!file_exists($adress)){
    		mkdir($adress, 0777);
    	}
    }
    
    function renameFile($adressFrom,$adressTo){
    	
    	rename($adressFrom,$adressTo);
    	
    }
	
	 function deleteDirectory($dirname)  {
	    if (is_dir($dirname))
	       $dir_handle = opendir($dirname);
	    if (!$dir_handle)
	       return false;
	    while($file = readdir($dir_handle)) {
	       if ($file != "." && $file != "..") {
	          if (!is_dir($dirname."/".$file))
	             unlink($dirname."/".$file);
	          else
	             $this->deleteDirectory($dirname.'/'.$file);    
	       }
	    }
	    closedir($dir_handle);
	    rmdir($dirname);
	    return true;
	 }
	
	function wordCutter($text, $numberOfWords) {
	
		 $ex = explode(" ", $text);
		 $i = 0;
		 $count = $numberOfWords-1;
		 
		 while($count >= $i){
		     
		 	 if(!isset($ex[$i]))break;
		     echo $ex[$i].' ';
		     $i++;
			 
		 }
     
	}
	
    function wordCutterNoEcho($text, $numberOfWords) {
    
         $filter = new Zend_Filter_StripTags();
         $text = $filter->filter($text);
         
         $ex = explode(" ", $text);
         $i = 0;
         $count = $numberOfWords-1;
         
         $finalText='';
         
         while($count >= $i && count($ex)>$i){
         
             $finalText.=$ex[$i].' ';
             $i++;
             
         }
         $finalText.=' ...';
         return $finalText;
     
    }
	
	 function getFirstParam($param,$request)
    {
    	$result = $request->getParam($param);
	  	$result = explode("-",$result);
	   	$result = $result[0];
	   	return $result;
    }
	
	function setPeopleCounter(){
		
		$ip = $this->getRealIpAddr();
		$peoples = new Peoples();
		$where = " ipAdress = '$ip' ";
		$count = $peoples->getCount($where);
		$time = Time();
		$time = $time - 7200;
		$time = date("Y-m-d H:i:s",$time);
		$whereDelete = " time <= '$time' ";
		$peoples->deleteData($whereDelete);
		
		if($count == 0){
			
			$number = new Numberofpeople();
			$number->updateData();
			$time = date("Y-m-d H:i:s",Time());
			$data=array('ipAdress'=>$ip,'time'=>$time);
			$peoples->insertData($data);
		}
		
	}
	
    function getRealIpAddr()
    {
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	    {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	    {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
    }

	function autoUTF($s)
	{
	    // detect UTF-8
	    if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $s))
	        return $s;
	    // detect WINDOWS-1250
	    if (preg_match('#[\x7F-\x9F\xBC]#', $s))
	        return iconv('WINDOWS-1250', 'UTF-8', $s);
	    // assume ISO-8859-2
	    return iconv('ISO-8859-2', 'UTF-8', $s);
	}

	function sendMail($to, $predmet, $zprava, $from,$language)
    {  
    	if($from !="")$head="From: ".$from."\n";
    	else $head="";
    	$predmet = "=?utf-8?B?".base64_encode($this->autoUTF ($predmet))."?=";
        $head .= "MIME-Version: 1.0\n";
        $head .= "Content-Type: text/html; charset=\"utf-8\"\n";
        $head .= "Content-Transfer-Encoding: base64\n";
        $zprava = base64_encode ($this->autoUTF ($zprava));
        if($this->check_email($to))
        {
        	if(mail ($to, $predmet, $zprava, $head))
        	{
        		 return $this->getAnswer($language,1);
        	}
        	else
        	{
        		return $this->getAnswer($language,2);
        	}
    	}
    	else
    	{
    		return $this->getAnswer($language,3);
    	}

	}
	
 	function check_email($email) {
	    $pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
        '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';

        return preg_match ($pattern, $email);
	}
	
	function getAnswer($language,$answer)
	{
		if($language == "cz")
		{
			switch ($answer) {
    			case 1:
        				return "E-mail byl úspěšně odeslán.";
        				break;        				
        		case 2:
        				return "Nastala chyba při odeslání emailu !!!";
        				break;
        		case 3:
        				return "Zadali jste nesprávný formát emailu !!!";
        				break;
			}
    				
		}
		else if($language == "en")
		{
			switch ($answer) {
    			case 1:
        				return "E-mail was succesfully sent.";
        				break;        				
        		case 2:
        				return "Error occured while sending email !!!";
        				break;
        		case 3:
        				return "You set wrong format of email !!!";
        				break;
			}
		}
	
	}
	
    public function updatePriority($allItems,$table,$id_text){
		
		$i = 1;
		foreach($allItems as $item){
			
			$id = $item->$id_text;
			$where = " $id_text = '$id' ";
			$data = array('priority'=>$i);
			$table->update($data,$where);
		    $i++;	
		}
	}
	
	public function imageType($ext){
		
		$mime_types = array(

            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml');
		
		if(array_key_exists($ext, $mime_types))return true;else return false;
		
	}
	
	public function getDay($d){
		
		$day = "";
		
		switch($d){
			
			case 1: $day = "Pondělí"; 
			        break;
			case 2: $day = "Úterý"; 
			        break;
			case 3: $day = "Středa"; 
			        break;
			case 4: $day = "Čtvrtek"; 
			        break;
			case 5: $day = "Pátek"; 
			        break;
			case 6: $day = "Sobota"; 
			        break;
			case 0: $day = "Neděle"; 
			        break;
			
		}
		
		return $day ;
		
	}
	
	public function getMonth($month){

		$stringMonth = "";
		switch($month){

			case 1:	
				$stringMonth = "Leden";
				break;	
			case 2:	
				$stringMonth = "Únor";	
				break;	
			case 3:	
				$stringMonth = "Březen";	
				break;	
			case 4:	
				$stringMonth = "Duben";	
				break;	
			case 5:	
				$stringMonth = "Květen";	
				break;	
			case 6:	
				$stringMonth = "Červen";	
				break;	
			case 7:	
				$stringMonth = "Červenec";	
				break;	
			case 8:	
				$stringMonth = "Srpen";	
				break;
			case 9:
				$stringMonth = "Září";	
				break;	
			case 10:	
				$stringMonth = "Říjen";	
				break;	
			case 11:	
				$stringMonth = "Listopad";	
				break;	
			case 12:	
				$stringMonth = "Prosinec";	
				break;

		}

		return $stringMonth;

	}
	
	public function generatePassword($length){
		
		$char     = "abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$password = "";

		while(strlen($password) < $length){
			
			$password .= substr($char,rand(1,strlen($char)),1);
			
		}
		
		return $password;
		
	}
	
	public function getDays(){
	
		$days = array(
			"1" => "Pondělí",
			"2" => "Úterý",
			"3" => "Středa",
			"4" => "Čtvrtek",
			"5" => "Pátek",
			"6" => "Sobota",
			"7" => "Neděle"
		);
		return $days;
	
	}
  
  
	public function removeDiakritic($text){
	
		// i pro multi-byte (napr. UTF-8)
		$prevodni_tabulka = Array(
				'ä'=>'a',
				'Ä'=>'A',
				'á'=>'a',
				'Á'=>'A',
				'à'=>'a',
				'À'=>'A',
				'ã'=>'a',
				'Ã'=>'A',
				'â'=>'a',
				'Â'=>'A',
				'č'=>'c',
				'Č'=>'C',
				'ć'=>'c',
				'Ć'=>'C',
				'ď'=>'d',
				'Ď'=>'D',
				'ě'=>'e',
				'Ě'=>'E',
				'é'=>'e',
				'É'=>'E',
				'ë'=>'e',
				'Ë'=>'E',
				'è'=>'e',
				'È'=>'E',
				'ê'=>'e',
				'Ê'=>'E',
				'í'=>'i',
				'Í'=>'I',
				'ï'=>'i',
				'Ï'=>'I',
				'ì'=>'i',
				'Ì'=>'I',
				'î'=>'i',
				'Î'=>'I',
				'ľ'=>'l',
				'Ľ'=>'L',
				'ĺ'=>'l',
				'Ĺ'=>'L',
				'ń'=>'n',
				'Ń'=>'N',
				'ň'=>'n',
				'Ň'=>'N',
				'ñ'=>'n',
				'Ñ'=>'N',
				'ó'=>'o',
				'Ó'=>'O',
				'ö'=>'o',
				'Ö'=>'O',
				'ô'=>'o',
				'Ô'=>'O',
				'ò'=>'o',
				'Ò'=>'O',
				'õ'=>'o',
				'Õ'=>'O',
				'ő'=>'o',
				'Ő'=>'O',
				'ř'=>'r',
				'Ř'=>'R',
				'ŕ'=>'r',
				'Ŕ'=>'R',
				'š'=>'s',
				'Š'=>'S',
				'ś'=>'s',
				'Ś'=>'S',
				'ť'=>'t',
				'Ť'=>'T',
				'ú'=>'u',
				'Ú'=>'U',
				'ů'=>'u',
				'Ů'=>'U',
				'ü'=>'u',
				'Ü'=>'U',
				'ù'=>'u',
				'Ù'=>'U',
				'ũ'=>'u',
				'Ũ'=>'U',
				'û'=>'u',
				'Û'=>'U',
				'ý'=>'y',
				'Ý'=>'Y',
				'ž'=>'z',
				'Ž'=>'Z',
				'ź'=>'z',
				'Ź'=>'Z'
				);
	
		return strtr($text, $prevodni_tabulka);
	
	}
	
 }