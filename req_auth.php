<?php 
/*** 
*@copyright  Copyright (c) 2017 UFan
*@mail uf.lins1128@gmail.com  
***/

require_once "conf/pay.conf.php";
require_once "model/tappayModel.php";
require_once "model/redis.model.php";

$prime = false;
$tdata  = false;
$weboid = "n";

if(isset($_POST["p"])) {
    $prime = $_POST["p"];
}
if(isset($_POST["d"])) {
    $tdata = $_POST["d"];
}
if(isset($_POST["o"])) {
    $weboid = $_POST["o"];
}

//send transacrtion to tappay 
$tappay = new TAPPAY_Payment_Model($obj_cfgp);
$jsonstr = json_encode($tappay->sendTransRequest($prime, $tdata));
$jsonstr_rp = str_replace('\\','', substr($jsonstr,1,strlen($jsonstr)-2) );
$jsonobj = json_decode( $jsonstr_rp );

// parsing result from tappay response
$lastfour = $jsonobj->cardinfo->lastfour;
$authcode = $jsonobj->authcode; 

// save in redis memo and log file
// return valudation back to fonrt-end
$rtn = 9999;
if(array_key_exists('status', $jsonobj)){ $rtn = $jsonobj->status; }
$logtxt = '{"postdata":'.$tdata.',"response":'.$jsonstr_rp.'}';
if($rtn == 0){
    /** If transaction success, put it in redis key**/
    $redis = new Redis_PDO("127.0.0.1");
    $redis->setKeyValue( $weboid, $logtxt);

	if($obj_cfgp["logs_file"]){$tappay->log_file(true,$logtxt);}
	
    echo '{"status": 0,"weboid":"'.$weboid.'","authcode":"'.$authcode.'","time":"'.$jsonobj->millis.'"}';
} else {
    if($obj_cfgp["logs_file"]){$tappay->log_file(false,$logtxt);}
    echo '{"status": '.$rtn.',"weboid":"'.$weboid.'","authcode":" ","time":" "}';
}

?>
