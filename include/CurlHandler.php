<?php

class CurlHandler {


private $curl;
	
    function __construct() {
        
	 require_once dirname(__FILE__) . '/Config.php';
     require_once dirname(__FILE__) . '/class.curlwrapper.php';
	   
	 try {
    $this->curl = new curlwrapper();
} catch (CurlWrapperException $e) {
    echo $e->getMessage();
}
 
    }
	
	
	
	
	
	 	public function getPnrStatus($pnrno) {
	
			//Exec 
			//http://www.indianrail.gov.in/cgi_bin/inet_pnstat_cgi_10521.cgi
			//params
			//lccp_pnrno1:3273827382
//lccp_cap_val:16874
//lccp_capinp_val:16874
//submit:Get Status
		   $url="http://www.indianrail.gov.in/cgi_bin/inet_pnstat_cgi_10521.cgi";
		   
		   //echo rand(5, 15)
		   
		   
		   $curl=$this->curl;
		   
		   
		   
		   $curl->setUserAgent('firefox');
			$curl->setTimeout(3600); // seconds
			$curl->setFollowRedirects(true); // to follow redirects
			
			$ip=mt_rand(50, 255) ."." .mt_rand(50, 255) .".".mt_rand(50, 255) ."." . mt_rand(50, 255) ;
			$cookiefile="cookie" . $ip .".txt";
			
			$myfile = fopen($cookiefile, "w") or die("Unable to open file!");
			fclose($myfile);


			$curl->addHeader('Host', $ip);
			$curl->setCookieFile($cookiefile);
			
			$captcha=rand(10000,29999);
				$params = array(
				"lccp_pnrno1" => "$pnrno",
				"lccp_cap_val" => "$captcha",
				"lccp_capinp_val" => "$captcha",
				"submit" => "Get Status"
				
				
				
			);
			
		   $rawresponse = $curl->post($url, $params);
		   
		   $httpCode = $curl->getTransferInfo('http_code');
		   
		
		if ($httpCode !== 200) {
			$response["status"] = "Error HTTP status" . $httpCode;		
			$response["result"] =$rawresponse;
			
		}else{
		
			$response["status"] = "OK";	

						
			$response["result"]=$rawresponse;	
			
			
		}
			
				return $response;
		
			//return $results;

     
    }
	
 
	   
	  

	
}


?>
