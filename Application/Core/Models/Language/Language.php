<?php 
/**
 * @desc trida pro vkladani dat do slovniku
 * @author Petr Meca
 */
class Models_Language_Language
{
	
	private $languages;   		// prekladanz jayzk
	private $data;              // vkladana data
	private $path;              // cesta k souborům
	private $operationType;     // typ operace (pridani,editace)
	private $tableParamIDName;     // typ operace (pridani,editace)
	private $tableObject;       // nazev tabulky do ktere vkladame, mazeme editujeme
	private $tableRowID;        // ID radku v tabulce
		
    function __construct(){    	
    }

	public function setLanguages($languages){
		$this->languages = $languages;
	}
	public function setData($data){
		$this->data = $data;
	}
	public function setOperationType($operationType){
		$this->operationType = $operationType;
	}
	public function setTableObject($tableObject){
		$this->tableObject = $tableObject;
	}
	public function setTableRowID($tableRowID){
		$this->tableRowID = $tableRowID;
	}
	public function setTableParamIDName($tableParamIDName){
		$this->tableParamIDName = $tableParamIDName;
	}
    
	public function changeLanguageText(){

		if(!empty($this->tableObject)){
			if(	($this->operationType == 'add' || $this->operationType == 'edit') && count($this->languages) > 0 && count($this->data) > 0){
				foreach ($this->languages as $l){
					if(isset($this->data[$l])){
						if(	$this->operationType == 'add'){	
							$this->tableObject->insertData($this->data[$l]);
						}elseif($this->operationType == 'edit'){
							$editDataExist = $this->tableObject->getOneRow("$this->tableParamIDName = $this->tableRowID AND lang = '$l'");
						  if($editDataExist){
							    $this->tableObject->updateData($this->data[$l],"$this->tableParamIDName = $this->tableRowID AND lang = '$l'");
						  }else{
						      $this->tableObject->insertData($this->data[$l]);
						  }
						}
					}
				}
			}else if($this->operationType == 'delete'){
				$this->tableObject->deleteData("$this->tableParamIDName = $this->tableRowID");
			}
		}
		
	}	
	
	public function getDbLanguages(){
		$languageMutation = new Models_Language_DB_LanguageMutation();
		return $languageMutation->getAllItems("enabled = 1");
	}
	
	public function getMainLang(){
		$languageMutation = new Models_Language_DB_LanguageMutation();
		return $languageMutation->getOneRow("enabled = 1 AND main = 1");
	}
	
}

?>
