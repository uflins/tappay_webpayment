<!DOCTYPE html>
<?php
/*** 
*@copyright  Copyright (c) 2017 UFan
*@mail uf.lins1128@gmail.com  
***/
        require_once "conf/pay.conf.php";
        require_once "model/redis.model.php";
		require_once "model/tappayModel.php";
?>
<html>
    <head>
        <title>刷卡狀態確認 |<?php echo $obj_cfgp["vendor_name"]; ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link rel="icon" type="image/x-icon" href="<?php echo $obj_cfgp["vendor_icon_url"]; ?>">
        <link  rel="stylesheet" type="text/css"  media="all" href="css/bootstrap.min.css" />
        <style>
            p {font-size: x-large;}
        </style>
        <script language="JavaScript">
        </script>
    </head>
    <body>
    <?php
    	$status = 1;
        $weboid = " ";
        $authcode = " ";
        $mills=" ";
        $post_flag = " ";

        if(isset($_POST["weboid"])) {
            $weboid = $_POST["weboid"];
        }
        if(isset($_POST["authcode"])) {
            $authcode = $_POST["authcode"];
        }
        if(isset($_POST["flag"])) {
            $post_flag = $_POST["flag"];
        }
        if(isset($_POST["mills"])) {
            $mills = $_POST["mills"];
        }
        if(isset($_POST["status"])) {
            $status = $_POST["status"];   //My define 9999 is timeout error.
        }

        $tappay = new TAPPAY_Payment_Model($obj_cfgp);
		$flag = $tappay->spec_confirm($obj_cfgp["security_key"].$weboid);
        /**If the request flag is invalid, redirect back to website, disallow to access this page***/
        if( $flag !== $post_flag){
             
            header("Location: ".$obj_cfgp["vendor_domain_url"], true, 303);
            die();           
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
            <table style="border-collapse:collapse">
                <tbody>
                <?php if($status == 0 && strlen( $authcode) > 3 ){
                 ?>
                <p>親愛的<?php echo $obj_cfgp["vendor_name"]; ?>客戶，感謝您的訂購，您已完成本訂單的交易程序，我們會盡快為您排單生產與出貨。 </p>
                <hr />
                <tr>
                    <td>
                    <p>交易結果:</p>
                    </td>
                    <td>
                     <p>刷卡交易成功 </p>
                    </td>
                </tr>
                <tr>
                    <td><p>官網訂單編號:</p> </td>
                    <td><p><?php echo $weboid; ?></p> </td>
                </tr>
                <tr>
                    <td><p>交易日期:</p> </td>
                    <td><p><?php
                           $mills = floor( $mills / 1000 )+28800;
                           $dt = new DateTime("@$mills");
                           echo $dt->format('Y-m-d H:i:s');
                           ?></p> </td>
                </tr>
                <tr>
                    <td><p>授權碼:</p> </td>
                    <td><p><?php echo $authcode?></p> </td>
                </tr>
                <?php    
                 } else {
                 // Fail transaction
                 ?>
                <tr>
                    <td>
                    <p>交易結果:</p>
                    <p>刷卡交易失敗，取消交易。確認卡片資訊輸入正確，或網路連線順暢,稍後再嘗試(Transaction is fail, please check card info. and internet connection)。 </p>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php if($status == 0 && strlen( $authcode) > 3){
                  echo "<a href=\"".$obj_cfgp["vendor_domain_url"]."\" class=\"btn btn-info btn-block\" style=\"font-size: x-large;height: 60px;\">離開此頁</a>";
                  } else {
                  echo '<button onclick="window.location.href =document.referrer;" class="btn btn-info btn-block" style="font-size: x-large;height: 80px;">回前一頁</button>';
                  }
            ?>
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
    </div>
    </body>
</html>
	
