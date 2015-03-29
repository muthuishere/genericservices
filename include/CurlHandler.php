<?php

class CurlHandler {


private $curl;
private $db;
private $api_key;
private $api_url;
	
    function __construct() {
        
	 require_once dirname(__FILE__) . '/Config.php';
     require_once dirname(__FILE__) . '/class.curlwrapper.php';
	   
	      
		$this->api_key=RAIL_API_KEY;
		$this->api_url=RAIL_API_URL;
		
	 try {
    $this->curl = new curlwrapper();
} catch (CurlWrapperException $e) {
    echo $e->getMessage();
}
 	
 $this->db=new PdoHandler();
 
    }
	
	
	
	//Function to Retrieve the data
public function makeWebCall($urlto,$postData = null,$refer=null){

			//create cURL connection
			$curl_connection = curl_init($urlto);
			//set options
			curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl_connection, CURLOPT_USERAGENT,
			  "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
			curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, true);
			//if 
			if(isset($postData)){
				//set data to be posted
				curl_setopt($curl_connection, CURLOPT_POST,true);	
				curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $postData);
			}


			if(isset($refer)){
			 //set referer
			 curl_setopt($curl_connection, CURLOPT_REFERER, $refer);
			}
			//perform our request
			$result = curl_exec($curl_connection);
			// Debug -Data
			//show information regarding the request
			//var_dump(curl_getinfo($curl_connection));
			// echo curl_errno($curl_connection) . '-' .
			//                curl_error($curl_connection);

			//close the connection
			curl_close($curl_connection);
			return $result;
}

// Function to construct the post request
function createPostString($postArray){
		//traverse array and prepare data for posting (key1=value1)
		foreach ( $postArray as $key => $value) {
			$post_items[] = $key . '=' . $value;
		}
		//create the final string to be posted using implode()
		return implode ('&', $post_items);
}
	
public function array_push_assoc($array, $key, $value){
	$array[$key] = $value;
	return $array;
}


public function getClassname($code){

$status=$code;

/*
First class Air-Conditioned (AC) (Code:1A). The Executive class in Shatabdi type trains is also treated as Ist AC.
AC 2-tier sleeper (Code:2A)
First class (Code:FC)
AC 3 Tier (Code:3A)
3 E - AC 3 Tier Economy
AC chair Car (Code:CC)
Sleeper Class (Code:SL)
Second Sitting (Code:2S)

*/

switch ($code) {
    case "1A":
        $status="First AC";
        break;
    case "2A":
         $status="AC 2-tier sleeper";
        break;
    case "FC":
         $status="First class";
        break;
	  case "3A":
         $status="AC 3 Tier";
        break;
  case "3E":
  case "3 E":
         $status="AC 3 Tier Economy";
        break;
  case "CC":
         $status="AC chair Car";
        break;
  case "SL":
         $status="Sleeper Class";
        break;
  case "2S":
         $status="Second Sitting";
        break;
 default:
         $status=$code;
        break;		
  
}
return $status;




}
public function getStatusname($code){

$status=$code;



switch ($code) {
    case "CAN":
        $status="CANCELLED";
        break;
    case "CNF":
         $status="CONFIRMED";
        break;
    case "WL":
         $status="WAITLIST";
        break;
	  case "RAC":
         $status="RESERVATION AGAINST CANCELLATION";
        break;
  case "RLWL":
         $status="REMOTE LOCATION WAITLIST";
        break;
  case "PQWL":
         $status="POOLED QUOTA WAITLIST";
        break;
  case "RSWL":
         $status="ROAD SIDE WAITLIST";
        break;
  case "REL":
         $status="RELEASED";
        break;
  case "NR":
         $status="NOROOM";
        break;		
  
}
return $status;




}

	public function getStationname($code){
	

	 static $stations = array();
    $station =& $stations[$code];
    if(!$station) {
       
	   
            // fetch task
            $result = $this->db->getStation($code);
 
 
            if ($result != NULL) {
                $station=$result["name"];  				
				$stations=$this->array_push_assoc($stations, $code, $station);
				
				
					
				}
			 else{
				$station=$code;  
				}
    }else{
	//echo "feteched from cache" . $station;
	}
    return $station;
	
 
				
	}
	
	
	public function getTrainDetails($trainno,$from,$to,$curdate) {
	
	
	
		$url=$this->api_url ."trains?key=" . $this->api_key . "&stnfrom=" .  $from . "&stnto=" . $to . "&date=" . $curdate;
		
	
		
	$result = $this->makeWebCall($url );
	

	
	 $jfo=json_decode($result);
	 
	 
	
	$status = $jfo->status;
	
	$buffer=null;
	
	
	if($status === "OK"){
// copy the posts array to a php var


	/*
	"trainno" : "22638",
			"name" : "WEST COAST EXP",
			"cls" : "2A 3A SL",
			"rundays" : "Daily",
			"from" : "SGE",
			"fromname" : "Sankaridrug",
			"dep" : "08.40",
			"to" : "MAS",
			"toname" : "Chennai Central",
			"arr" : "14.40",
			"pantry" : 0,
			"type" : "SUPERFAST",
			"datefrom" : "28-Mar-2015",
			"dateto" : "29-Mar-2020",
			"traveltime" : "06.00"
	
	
	*/

		 
			 $trains = $jfo->result;
			// listing posts
			foreach ($trains as $train) {
				
							if($train->trainno==$trainno){
							
							
							//print_r($train);
							 $buffer=array(
								"trainno"=> $train->trainno,
								"name" => $train->name,
								"classes" => $train->cls,							
								"rundays" => $train->rundays,
								"fromcode" => $train->from,
								"from" => $train->fromname,
								"departuretime" => $train->dep,
								"to" => $train->toname,
								"tocode" => $train->to,
								"arrivaltime" => $train->arr,
								"pantrycount" => $train->pantry,
								"traintype" => $train->type,
								"datefrom" => $train->datefrom,
								"dateto" => $train->dateto,
								"traveltime" => $train->traveltime
							 
							 );
							
							}
							
			

					
				
			}


		 
		 }
		  
		 
		 $response["status"] = $status;		
			$response["result"] =$buffer;
		
		//print_r($response);
	return $response;
	
	}
	public function getPnrStatus($pnrno) {
	
	
	
	$url_pnr=$this->api_url ."pnr?key=" . $this->api_key ."&pnr=" . $pnrno;
		
		
	
		
	$result = $this->makeWebCall($url_pnr );
	
//	echo $result;
	
	 $jfo=json_decode($result);
	 
	 
	 
	/*
	  {
        "status" : "OK",
        "result" :  { 
                      "pnr" : "6533543051",
					  "cls":"3A",
                      "eticket" : true,
                      "journey" : "09-Sep-2014",
                      "trainno" : "12898",
                      "name" : "BBS PDY EXPRESS",
                      "from" : "VZM",
                      "to" : "MS",
                      "brdg" : "VZM",
                      "passengers" : 
                          [
                            {
                                "bookingstatus" : "RAC 14GNWL",
                                "currentstatus" : "B3 14",
                                "coach" : ""
                            },
                            ....
                         ],
                      "chart" : "CHART PREPARED",
                      "error" : ""
                  }
    }
	
	*/
	
	
	$status = $jfo->status;
	
	$tmpValue=null;
	
	
	if($status === "OK"){
// copy the posts array to a php var




	 $tmpValue =array(
					  "pnr" => $jfo->result->pnr,
					  "class" => $this->getClassname($jfo->result->cls),	
					   "classcode" => $jfo->result->cls,		
					  
					  "iseticket" => $jfo->result->eticket,				  
					  "train_name" => $jfo->result->name,
					  "train_number" => $jfo->result->trainno,
					  "from" => $this->getStationname($jfo->result->from),
					  "to" => $this->getStationname($jfo->result->to),
					  "fromcode" => $jfo->result->from,
					  "tocode" => $jfo->result->to,					
					   "chart" => $jfo->result->chart,
					  "board" => $this->getStationname($jfo->result->brdg),
					  "boardcode" => $jfo->result->brdg,
					  "travel_date" => $jfo->result->journey,
					  "passenger" => array()
			 );
				 

		$trainresponse=$this->getTrainDetails($jfo->result->trainno,$jfo->result->brdg,$jfo->result->to,$jfo->result->journey) ;

				 
		if($trainresponse["status"] === "OK"){

			$traindetail=$trainresponse["result"];
			
			//print_r($traindetail);
			
		$tmpValue=$this->array_push_assoc($tmpValue, "departuretime", $traindetail["departuretime"]);
		$tmpValue=$this->array_push_assoc($tmpValue, "arrivaltime", $traindetail["arrivaltime"]);
		$tmpValue=$this->array_push_assoc($tmpValue, "traveltime", $traindetail["traveltime"]);
		$tmpValue=$this->array_push_assoc($tmpValue, "traintype", $traindetail["traintype"]);
		$tmpValue=$this->array_push_assoc($tmpValue, "pantrycount", $traindetail["pantrycount"]);


		}
			 
			 $passengers = $jfo->result->passengers;
			// listing posts
			foreach ($passengers as $passenger) {
				
							
			array_push($tmpValue["passenger"],array(
					  "bookingstatus" => $passenger->bookingstatus,
					  "currentstatus" => $passenger->currentstatus,				  
					  "coach" => $passenger->coach
					));

					
				
			}


		 
		 }
		  
		 
		$response["status"] = $status;		
		$response["result"] =$tmpValue;
		
	return $response;
	
	}
	public function getirctcPnrStatus($pnrno) {
	
	$url_captch = 'http://www.indianrail.gov.in/pnr_Enq.html';
$url_pnr = 'http://www.indianrail.gov.in/cgi_bin/inet_pnstat_cgi_10521.cgi';
// Submit the captcha and PNR
//create array of data to be posted
$post_data['lccp_pnrno1'] = $pnrno;
$post_data['lccp_cap_val'] = 12345; //dummy captcha
$post_data['lccp_capinp_val'] = 12345;
$post_data['submit'] = "Get Status";
$post_string = createPostString($post_data);

$result = $this->makeWebCall($url_pnr,$post_string,$url_captch );

//Debug
//var_dump($result);
// Parse Logic
// I have not used DOM lib it is simple regEx parse.
//Change here when the Page layout of the page changes.
$matches = array();
preg_match_all('/<td class="table_border_both">(.*)<\/td>/i',$result,$matches);
//DEBUG
//var_dump($matches);
$resultVal = array(
    'status'    =>    "INVALID",
    'data'      =>    array()                
);

		if (count($matches)>1&&count($matches[1])>8) {
		 $arr = $matches[1];
		 $i=0;
		 $j=0;
		 $tmpValue =array(
				  "pnr" => $pnt_no,
				  "train_name" => "",
				  "train_number" => "",
				  "from" => "",
				  "to" => "",
				  "reservedto" => "",
				  "board" => "",
				  "class" => "",
				  "travel_date" => "",
				  "passenger" => array()
		 );
		 
		 $tmpValue['train_number'] = $arr[0];
		 $tmpValue['train_name'] = $arr[1];
		 $tmpValue['travel_date'] = $arr[2];
		 $tmpValue['from'] = $arr[3];
		 $tmpValue['to'] = $arr[4];
		 $tmpValue['reservedto'] = $arr[5];
		 $tmpValue['board'] = $arr[6];
		 $tmpValue['class'] = $arr[7];
		 
		$stnum="";
		 foreach ($arr as $value) {
		 
		  $i++;
		  if($i>8){
		   $value=trim(preg_replace('/<B>/', '', $value));
		   $value=trim(preg_replace('/<\/B>/', '', $value));
		   
		   $ck=$i%3;
			if($ck==1){      
			 $stnum = $value;
			}
			else if($ck==2) {
			  array_push($tmpValue["passenger"],array(
				   "seat_number" => $stnum, 
				   "status" => $value 
				));
			}
		  }
		 }
		 $resultVal['data'] = $tmpValue;
		 $resultVal['status'] = 'OK';
		}
 
		$response["status"] = $resultVal['status'];		
			$response["result"] =$resultVal['data'];
			return $response;
	}
	 	public function getOldPnrStatus($pnrno) {
	
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
