<?php
/**
 * Třída exportující data do CSV formátu
 * @author Petr Meca
 * @return CSV soubor do příslušně nastavené cesty
 */

class Library_CSVExport 
{    
    private $data;      // dvourozměrné pole dat pro export do CSV formátu
    private $path;      // cesta kde má být daný soubor uložený
    private $delimiter; // oddělovač jednotlivých buněk
    private $quotes;    // uvozovač, v kterém bude daný text buňky. Je tam z důvodu toho kdyby text obsahoval prvek stejný s oddělovačem
    	
	function __construct($data = array(),$path = "",$delimiter = ";",$quotes = '"')
	{
        $this->data      = $data; 
        $this->path      = $path; 	 
        $this->delimiter = $delimiter;
        $this->quotes    = $quotes;    
	}

	public function execute(){
		
		if(!empty($this->path)){
			$content = "\"Název školy\";\"Region\";\"Město\";\"Trenér\";\"Email\";\"Telefón\";\"Mobil\"\n";
			$count = count($this->data);
			for($i = 0; $i < $count;$i++){
				$count2 = count($this->data[$i]);
				for($j = 0; $j < $count2;$j++){
					
					if($count2 == $j+1){
						$content .= $this->quotes.$this->data[$i][$j].$this->quotes."\n";
						break;
					}
					$content .= $this->quotes.$this->data[$i][$j].$this->quotes.$this->delimiter;
					
				}
			}
			$this->createFile($content);
		}else{
			throw new Exception($e);
		}
	}
	
	private function createFile($content){
		
		file_put_contents("./".$this->path,$content);
		
	}
	
	public function setData($data){
		
		$this->data = $data;
		
	}
	public function setPath($path){
		
		$this->path = $path;
		
	}
	public function setDelimiter($delimiter){
		
		$this->delimiter = $delimiter;
		
	}
		
}