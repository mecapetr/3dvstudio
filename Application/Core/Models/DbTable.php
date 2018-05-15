<?php

class Models_DbTable extends Zend_Db_Table
{
	
	protected function setStatement($sql,$data){
	     $db = $this->getDefaultAdapter();
	     $stmt  = new Zend_Db_Statement_Mysqli($db, $sql);  
	     $stmt->execute($data);	
		 return $stmt;
	}
        
}
