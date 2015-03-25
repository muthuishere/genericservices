<?php

class PdoHandler {

	private $db;

    function __construct() {
        
		   require_once dirname(__FILE__) . '/Config.php';
     require_once dirname(__FILE__) . '/class.db.php';
	   
	   //$this->db = Database::obtain(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME); 
	   
	     $this->db = new db("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);

// connect to the server 
 //$this->db->connect(); 
 
 
    }
	
	function close(){
	
	 $this->db->close();
	
	}
	
	 	public function getStation($code) {
	
	
			$bind = array(
				":code" => "$code"
			);
		
			
			$results = $this->db->run("SELECT code,name,location,totaltrains FROM stations WHERE code=:code", $bind);

			if(count($results) > 0)
				return $results[0];
			else
				return NULL;
		
			//return $results;

     
    }
	
 
	   
	  

	
}


?>
