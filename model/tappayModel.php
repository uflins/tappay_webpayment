<?php
/*** 
*@copyright  Copyright (c) 2017 UFan
*@mail uf.lins1128@gmail.com  
***/

class TAPPAY_Payment_Model {

    protected $vatnumber;
    protected $partnerkey;
    protected $merchantid;
    protected $tappayAPI;
    protected $prime;
    //protected $postdata;

	/* Get value from tappay.conf.php */
    function __construct( $obj_cfgp ) {
        $this->vatnumber = $obj_cfgp["vatnumber"];
        $this->partnerkey = $obj_cfgp["partnerkey"];
        $this->merchantid = $obj_cfgp["merchantid"];
        $this->tappayAPI = $obj_cfgp["tappayAPI"];
    }

	/* request the transaction 
	   $inputprime is the prime token from tappay. 
	   $inputdata is the transaction data with total grand,item description, customer name,... etc.
	*/
   function sendTransRequest($inputprime, $inputdata) {
	    //initial transaction data 
        $managejson = (array) json_decode($inputdata);
		
		// add tappay params 
        $managejson['prime'] = $inputprime;
        $managejson['vatnumber'] = $this->vatnumber;
        $managejson['partnerkey'] = $this->partnerkey;
        $managejson['merchantid'] = $this->merchantid;
        $managejson['instalment'] = 0;
        $managejson['authtocapperiodinday'] = 0;
        $data_string = json_encode($managejson);   

		//send POST request to transaction API
        $ch = curl_init( $this->tappayAPI );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
            'Content-Type: application/json',  
            'x-api-key: ' . $this->partnerkey )  
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
   }

   function log_file($flag, $logtxt){
       $filename = 'log_tappay_response'.date("Ym");
       if($flag) {$filename = $filename.'.sucess.log';} else{ $filename = $filename.'.fail.log';}
       file_put_contents("logs/".$filename, $logtxt."\n", FILE_APPEND | LOCK_EX);

   }

   function spec_confirm($input){
	   // define your own validate key function
	   return hash('sha256', $input);
   }
}
?>
