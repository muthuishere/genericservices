<?php

class PythonHandler {



	
    function __construct() {
        
	 require_once dirname(__FILE__) . '/Config.php';
     require_once dirname(__FILE__) . '/class.shellExec.php';
	   
	 
 
    }
	
	
	
	
	 	public function execute($filename,$args) {
	
	
	
		
		$command = PYTHON_APP_PATH . " ".PYTHON_FILE_PATH .$filename;
		$command .= " ".$args." 2>&1";

		//echo $command;
		  $response = array();
           
		   
		$e = shellExec::withCommandLine($command)->launch();
		if ($e->getResultCode() !== 0) {
			$response["status"] = join(PHP_EOL, $e->getOutput());		
			$response["result"] ="";
			
		}else{
		
			$response["status"] = "OK";	

						
			$response["result"]=join(PHP_EOL, $e->getOutput());	
			
			
		}
			
				return $response;
		
			//return $results;

     
    }
	
 
	   
	  

	
}


?>
