<?php
/*** 
*@copyright  Copyright (c) 2017 UFan
*@mail uf.lins1128@gmail.com  
***/
?>
<!DOCTYPE html>
<html>
    <head>
    <?php 
	 /**Require the configuration and model **/
     require_once "conf/pay.conf.php";
     require_once "model/redis.model.php";
	 require_once "model/tappayModel.php";
	 
    ?>
        <title>訂單交易確認頁面 | <?php echo $obj_cfgp["vendor_name"]; ?></title>
        <meta charset="UTF-8">
		<meta http-equiv="expires" content="0"> 
		<meta http-equiv="cache-control" content="no-cache"> 
		<meta http-equiv="pragma" content="no-cache"> 
        <meta name="viewport" content="width=device-width">
        <link rel="icon" type="image/x-icon" href="<?php echo $obj_cfgp["vendor_icon_url"]; ?>">
        <link  rel="stylesheet" type="text/css"  media="all" href="css/bootstrap.min.css" />
        <style>
        p {font-size: x-large;}
        .cinfo1 {font-size: x-large;margin-bottom: 10px;margin-left: 113px;height: 40px;width: 300px;}

        input[type=checkbox]
        {
         /* Double-sized Checkboxes */
         -ms-transform: scale(2); /* IE */
         -moz-transform: scale(2); /* FF */
         -webkit-transform: scale(2); /* Safari and Chrome */
         -o-transform: scale(2); /* Opera */
         /*padding: 10px;  */
        }

        /* Might want to wrap a span around your checkbox text */
        .checkboxtext
        {
        /* Checkbox text */
        font-size: 130%;
        display: inline;
        }
        </style>
		<script type="text/javascript"> 
		    window.history.forward(1);
		</script>
        <!-- Tappay sdk-->
        <script src='https://js.tappaysdk.com/tpdirect/v2_3_2'></script>
        <!-- Tappay sandbox-->
		<script> TPDirect.setupSDK(<?php echo $obj_cfgp["appID"]; ?>, '<?php echo $obj_cfgp["appKEY"]; ?>', '<?php echo $obj_cfgp["serverType"]; ?>') </script>
        <script src='payment_js/mrltappay.js'></script>
        <script src='payment_js/spin.min.js'></script>
    </head>
    <body>
    <?php
	 /** a hashkey to confirm **/
     $security_key = $obj_cfgp["security_key"];
     /** if hash key is validated, then would set true. Default is false.**/
	 $pass_security = false;
	 
	 /** a check params, if the order number is already paid in redis key or not. Default is false.**/
     $already_payorder = false;
	 
	 /** a check params, if the order number was canceled inredis key or not. Default is false.**/
     $cancel_order = false;

     /** Params from GET (or POST) */
     $order_id = $_GET["oid"];
     $total_price = $_GET["price"];
	 
	 /** a hash value to confirm **/
     $security_code = $_GET["key"];
	 
	 /** welcom user name that you want to show on the page**/
     $uname = $_GET["uname"];

     /** Confirming hash value is correct with your hash rule **/
	 $tappay = new TAPPAY_Payment_Model($obj_cfgp);
	 $make_security_code = $tappay->spec_confirm($security_key);
	 if( $security_code == $make_security_code ){
         $pass_security = true;
     }

     /** Create redis obj. **/
     $redis = new Redis_PDO("127.0.0.1");
	 
	 /** if payment is paid or not**/
     if($redis->ifKeyExists($order_id)){
         $already_payorder = true;
     }
	 
	 /** if order is canceled or not**/
     if($redis->ifKeyExists($order_id."-cancel")){
         $cancel_order = true;
     }

    ?>
    <div style="margin:0;padding:0;color:#777;font-family:'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;font-style:normal;font-weight:normal;line-height:1.4;font-size:13px;text-align:left;background-color:#f5f5f5">
    <table class="m_tablecss" style="border-collapse:collapse;margin:0 auto;text-align:left;width:660px" align="center">
    <tr>
        <td style="font-family:'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;vertical-align:top;padding:22.5px;background-color:#f5f5f5">
        <a href="<?php echo $obj_cfgp["vendor_domain_url"]; ?>" style="color:#08c;text-decoration:none" target="_blank" >
        <img src="<?php echo $obj_cfgp["vendor_logo_url"]; ?>" alt="LOGO <?php echo $obj_cfgp["vendor_name"]; ?>" style="border:0;height:auto;line-height:100%;outline:0;text-decoration:none" class="CToWUd" width="167" height="31" border="0"></a>
        </td>
    </tr>
    <tr>
        <td style="font-family:'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;vertical-align:top;background-color:#fff;padding:22.5px">
            <table id="basic-table" style="border-collapse:collapse">
                <tbody>
                <?php 
                /**Confirm hash value success & order is not paid or canceled**/
                if($pass_security && ($already_payorder == false && $cancel_order == false) ){?>
                <tr>
                    <td>
                    <p>親愛的客戶 <?php if($uname !== "Guest" ) echo $uname; ?> 您好，</p>
                    <p>感謝您選購<?php echo $obj_cfgp["vendor_name"]; ?>的商品，以下為您的刷卡金額:  </p>
                    <p></p>
                    </td>
                    
                </tr>
                <tr>
                    <td style="font-family:'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:18px">
                    <p><b>結帳金額 (新台幣) </b></p>  <p><b> <?php echo $total_price; ?></b></p><input type="hidden" id="mrlamount" name="amount" value="<?php echo $total_price; ?>"><br />
                    <p><b>網路訂單編號 </b></p>  <p><?php echo "<b>".$order_id."</b>"; ?></p><input type="hidden" id="mrloid" name="orderid" value="<?php echo $order_id; ?>">
                    <div class="card-wrapper" style="padding-top: 30px; padding-bottom: 40px;"></div>
                    <div class="form-container active">
                    
                    <form action="">
                        <input class="cinfo1" id="cardnumber" placeholder="信用卡號" type="tel" name="number" autocomplete="off"><br/>
                        <input class="cinfo1" id="cardholder" placeholder="持卡人名" type="text" name="name"><br/>
                        <input id="monyear" placeholder="MM/YY" type="tel" name="expiry" autocomplete="off" style="font-size: x-large;margin-bottom: 10px;margin-left: 113px;height: 40px;width: 120px;">
                        <input id="cvcnum" placeholder="卡片背後3碼" type="number" name="cvc" autocomplete="off" style="font-size: x-large;margin-bottom: 10px;margin-left: 15px;height: 40px;width: 160px;">
                        <p style="font-size: x-large;margin-bottom: 10px;margin-left: 113px;height: 40px;">自動帶入卡號需注意格式為每四碼帶空格</p>
                        <p style="font-size: x-large;margin-bottom: 10px;margin-left: 113px;height: 40px;">EX: 0000 1111 2222 3333</p>
                    </form>
                    <div class="checkbox" style="margin-bottom: 25px;margin-left: 10px;">
                        <label>
                            <input id="usercheck" type="checkbox" value="">
                            <span class="checkboxtext" style="margin-left: 10px;">[確認須知] 我已確認購物須知。</span>
                        </label>
                    </div>
                    <button id="paymentbtn" onclick="mrltool.submittrans();" class="btn btn-info btn-block" style="font-size: x-large;height: 80px;">確認付款</button>
                    <p></p>
                    </td>
                </tr>
                <?php } 
                /**If First Secure Checking is Fail : **/
                else {?>
                <tr>
                    <td style="font-family:'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:18px">
                    <?php if($already_payorder){
                          /* avoid order re-payment*/
                          echo "<p>此筆訂單已完成付款程序，如有疑問，</p>";
                          } 
                          else if($cancel_order){
                          echo "<p>此筆訂單號已被銷單，請您重新下單</p>" ;
                          }
                          else {
                          /* fail of confirming hash value*/
                          echo "<p>交易訂單授權失敗，</p>";
                          }
                    ?>
                    <p>請與客服人員 <a href="mailto:<?php echo $obj_cfgp["service_mail"]; ?>"><?php echo $obj_cfgp["service_mail"]; ?></a> 確認您的訂單資訊</p>
                    </td>
                </tr>
                <?php }?>
                </tbody>
            </table>
            <table id="trading-table" style="border-collapse:collapse;display: none;">
                <tbody>
                <tr>
                    <td style="font-family:'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:18px">
                        <p style="padding-left: 55px;padding-bottom: 100px;">==========交易進行中，請稍後==========</p>
                        <div id="spinbar" style="padding-bottom: 100px;"></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>

    <tr>
        <td style="font-family:'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;vertical-align:top;padding:22.5px;background-color:#0c0c0c;">
            <table style="border-collapse:collapse;width:100%"><tbody><tr>
                <td style="font-family:'Open Sans','Helvetica Neue',Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:22.5px;width:33%">
                    <div class="block" style="padding-bottom: 15px;">
                        <div class="block-title"><p><strong style="color: #ffffff;">購物須知與客服聯絡</strong></p></div>
                        <ul class="social-icons">
                            <li><p><a href="<?php echo $obj_cfgp["service_term_url"]; ?>" style="color:#ffffff;text-decoration:none" target="_blank">購物服務條款</a></p></li>
                            <li><a style="background-color: #0c0c0c;width: 100px;" href="<?php echo $obj_cfgp["line_contact_url"]; ?>" target="_blank"><img height="38" border="0" alt="好友人數" src="https://biz.line.naver.jp/line_business/img/btn/addfriends_zh-Hant.png"></a></li>
                            <li><p><span style="color: #ffffff;">本公司採用喬睿科技TapPay SSL交易系統，通過PCI-DSS國際信用卡組織Visa、MasterCard等產業資料安全Level 1最高等級。本公司不會留下您的信用卡資料,以保障你的權益,資料傳輸過程採用嚴密的 SSL 2048 加密技術保護,請你放心。</span></p><a style="background-color: #0c0c0c;width: 100px;" href="https://www.tappaysdk.com/tch" target="_blank"><img height="38" border="0" src="images/tappay_logo.jpg"></a></li>
                        </ul>
                        </div>
                    </div>
                </td>
                </tr>
              </tbody>
            </table>
        </td>
    </tr>
    </table>
	
    <form id="myResultForm" action="auth_check.php" method="post">
        <input type="hidden" id="weboid" name="weboid" value="">
        <input type="hidden" id="authcode" name="authcode" value="">
        <input type="hidden" id="mills" name="mills" value="">
        <input type="hidden" name="flag" value="<?php echo $tappay->spec_confirm($obj_cfgp["security_key"].$order_id);?>">
        <input type="hidden" id="status" name="status" value="">
    </form>
    </div>
    <!-- CSS is included via this JavaScript file -->
    <script src="cmsdk/card.js"></script>
    <script>
    try {
    var card = new Card({
    form: '.active form', // *required*
    container: '.card-wrapper', // *required*

    width: 400, // optional — default 350px
    formatting: true, // optional - default true
    // Strings for translation - optional
    messages: {
        monthYear: 'mm/yy' // optional - default 'month/year'
    },

    // Default placeholders for rendered fields - optional
    /*placeholders: {
        number: '•••• •••• •••• ••••',
        expiry: '••/••',
        cvc: '•••'
    },*/

    masks: {
        cardNumber: '•' // optional - mask card number
    },
    debug: true
    });
    } catch (e) {
        console.log('gen card fields stopping');
    }
   </script>
  </body>
</html>
