<?php    
require_once("Rest.inc.php");    
class API extends REST {
     
    public $data = "";
      
    private $db = NULL;
 
    public function __construct(){
        parent::__construct();              // Init parent contructor
        $this->dbConnect();                 // Initiate Database connection
}
     
private function dbConnect(){
       /* $this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
        if($this->db)
            mysql_select_db(self::DB,$this->db);*/
		
    include_once 'config/Database.php';
	//include_once 'class/Projects.php';
	$database = new Database();
    $db = $database->getConnection();
	
	return $db;
}
     
    /*
     * Public method for access api.
     * This method dynmically call the method based on the query string
     *
     */
public function processApi(){
        $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
        if((int)method_exists($this,$func) > 0)
            $this->$func();
        else
            $this->response('Error code 404, Page not found',404);   // If the method not exist with in this class, response would be "Page not found".
}
 /*Below are 3 API end point 
 How to call them http://localhost/****** /confirmation    - replace *** with folder
 http://localhost/****** /confirmation 
 */    
 
private function validation_url(){
	/*No validation currently */
	echo '{"ResultCode":0, "ResultDesc":"Success", "ThirdPartyTransID": 0}';
	//echo '{"ResultCode":1, "ResultDesc":"Failed", "ThirdPartyTransID": 0}';	
}
private function confirmation(){    
   
//$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB);
$con  = $this->dbConnect();

if (!$con) 
{
die("Connection failed: " . mysqli_connect_error());
}
$request = file_get_contents("php://input");
//print_r($data);
//Put the json string that we received from Safaricom to an array
//echo '{"ResultCode":0,"ResultDesc":"$request"}';//exit("F33");
$array = json_decode($request, true);
//print_r($array);
$transactiontype= mysqli_real_escape_string($con,$array['TransactionType']); 
$transid=mysqli_real_escape_string($con,$array['TransID']); 
$transtime= mysqli_real_escape_string($con,$array['TransTime']); 

$transamount= mysqli_real_escape_string($con,$array['TransAmount']); 
$businessshortcode=  mysqli_real_escape_string($con,$array['BusinessShortCode']); 
$billrefno=  mysqli_real_escape_string($con,$array['BillRefNumber']); 

$invoiceno=  mysqli_real_escape_string($con,$array['InvoiceNumber']); 
$msisdn=  mysqli_real_escape_string($con,$array['MSISDN']); 
$orgaccountbalance=   mysqli_real_escape_string($con,$array['OrgAccountBalance']); 
$firstname=mysqli_real_escape_string($con,$array['FirstName']); 
$middlename=mysqli_real_escape_string($con,$array['MiddleName']); 
$lastname=mysqli_real_escape_string($con,$array['LastName']); 

 $sql1 = "insert into raw_so19 set data='$request'";// Log raw request 
if (!mysqli_query($con,$sql1)) 
{ 
// log error in an appropriate channel
} 
	 
 
$sql="INSERT INTO mpesa_payments
( 
TransactionType,
TransID,
TransTime,
TransAmount,
BusinessShortCode,
BillRefNumber,
InvoiceNumber,
MSISDN,
FirstName,
MiddleName,
LastName,
OrgAccountBalance

)  
VALUES  
( 
'$transactiontype', 
'$transid', 
'$transtime', 
'$transamount', 
'$businessshortcode', 
'$billrefno', 
'$invoiceno', 
'$msisdn',
'$firstname', 
'$middlename', 
'$lastname', 
'$orgaccountbalance' 
)";

 
if (!mysqli_query($con,$sql)) 
{ 
//Log the error
} 
 
 
else 

{ 
echo '{"ResultCode":0,"ResultDesc":"Confirmation received successfully"}';
}
 
mysqli_close($con); 
    $this->response($param, 200);    
}

private function confirmationLipa(){    
 $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB);
if (!$con) 
{
	// log(. mysqli_connect_error())
//exit("Connection failed: ");
}
$request = file_get_contents("php://input");
//print_r($data);
//Put the json string that we received from Safaricom to an array
//echo '{"ResultCode":0,"ResultDesc":"$request"}';//exit("F33");
$array = json_decode($request, true);
//print_r($array);
$transactiontype= mysqli_real_escape_string($con,$array['TransactionType']); 
$transid=mysqli_real_escape_string($con,$array['TransID']); 
$transtime= mysqli_real_escape_string($con,$array['TransTime']); 

$transamount= mysqli_real_escape_string($con,$array['TransAmount']); 
$businessshortcode=  mysqli_real_escape_string($con,$array['BusinessShortCode']); 
$billrefno=  mysqli_real_escape_string($con,$array['BillRefNumber']); 

$invoiceno=  mysqli_real_escape_string($con,$array['InvoiceNumber']); 
$msisdn=  mysqli_real_escape_string($con,$array['MSISDN']); 
$orgaccountbalance=   mysqli_real_escape_string($con,$array['OrgAccountBalance']); 
$firstname=mysqli_real_escape_string($con,$array['FirstName']); 
$middlename=mysqli_real_escape_string($con,$array['MiddleName']); 
$lastname=mysqli_real_escape_string($con,$array['LastName']); 

 $sql1 = "insert into raw_li19 set data='$request'";
if (!mysqli_query($con,$sql1)) 
{ 
echo mysqli_error($con); 
} 
	 
 
 
else 

{ 
echo '{"ResultCode":0,"ResultDesc":"Confirmation received successfully"}';
}
 
mysqli_close($con); 
    //$this->response($param, 200);    
}

 
     
    /*
     *  Encode array into JSON
    */
    private function json($data){
        if(is_array($data)){
            return json_encode($data);
        }
    }
    private function hela(){
$url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
$credentials = base64_encode('lCORTMh9tu0ktt1************:HX7VxaF*******');// Get Consumer Keys from Daraja
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$cyy = curl_exec($curl);
if($cyy === false)
{
    echo 'Curl error: ' . curl_error($curl);
    exit("ended in error");
}
else
{
    //echo 'Operation completed without any errors';
}
 $curl_response = json_decode($cyy);
 
 $expires_in = $curl_response->expires_in;
  $access_token = $curl_response->access_token;

  $request = file_get_contents("php://input");
  $array = json_decode($request, true);
  
$transamount= $array['TransAmount']; 
$billrefno= $array['BillRefNumber']; 
$msisdn= $array['MSISDN']; 

$url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token.''));  //setting custom header
date_default_timezone_set('Africa/Nairobi');
$date=date("YmdHis");
$today_start_ts = $timestamp = strtotime($date);
$passkey = "e5697c9153eb50f*********59af12a58beca97c1c1e704d5d0c";
 $mystring = "9271**"."$passkey".$date;
 
 //echo "<br>";
 $pin =  base64_encode($mystring);
$curl_post_data = array(
  //Fill in the request parameters with valid values
  'BusinessShortCode' => '927***',
  'Password' => $pin,
  'Timestamp' => $date,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $transamount,
  'PartyA' => $msisdn,
  'PartyB' => '927183',
  'PhoneNumber' => $msisdn,
  'CallBackURL' => 'https://domain.com/M-PESA/confirmation',
  'AccountReference' => $billrefno,
  'TransactionDesc' => 'Paying the Loan'
);
$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);
print_r($curl_response);
echo $curl_response;
  }
}
 
    // Initiiate Library
     
    $api = new API;
    $api->processApi();
?>