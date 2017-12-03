/*** 
*@copyright  Copyright (c) 2017 UFan
*@mail uf.lins1128@gmail.com  
***/
var mrltool = {
    xhr: (
             (window.XMLHttpRequest && (window.location.protocol !== "file:" || !window.ActiveXObject)) ?
             function() {
                 return new window.XMLHttpRequest();
             } :
             function() {
                 try {
                     return new window.ActiveXObject("Microsoft.XMLHTTP");} 
                 catch(e) {}
    }),

    sendlog: function (results){
        console.log(results);
    },

    sendtrans: function (p,d,o) {
        var mrlxhr = mrltool.xhr();
        var uapi = "req_auth.php";
        var params = "p=" + p + "&d=" + d + "&o=" + o;
        mrlxhr.open("POST", uapi, true);
        mrlxhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=UTF-8");
        mrlxhr.withCredentials = true;
        mrlxhr.onreadystatechange = function() {
            if(mrlxhr.readyState == 4 && mrlxhr.status == 200) {
                console.log(mrlxhr.responseText);
                var res_json = JSON.parse(mrlxhr.responseText); 
                document.getElementById("weboid").value = res_json.weboid;
                document.getElementById("authcode").value = res_json.authcode;
                document.getElementById("mills").value = res_json.time;
                document.getElementById("status").value = res_json.status;

                document.getElementById('myResultForm').submit();
            }else {
				document.getElementById("status").value = '123456';
				document.getElementById('myResultForm').submit();
            }
        }
        mrlxhr.send(params); 
    },
  
    go2pay: function(ca,na,mo,ye,cv,weboid,amount,items){
		  // disable button
		  document.getElementById('paymentbtn').setAttribute("disabled", "disabled"); 
		  
		  // request prime token 
          TPDirect.card.createToken(ca, mo, ye, cv, function (result) {
          // if get token success or not
		  if(result.status == '0'){
			  var spinner = mrltool.showSpinBar();
              var mrldata = '{"ptradeid":"'+weboid+'", "amount": '+amount+',"currency": "TWD","details": "'+items+'","cardholder": {"phonenumber": "", "name": "'+na+'", "email": "","zip": "", "addr": "","nationalid": ""},"remember": false }';
              // request a transaction 
			  mrltool.sendtrans(result.card.prime,mrldata,weboid);
              
          }else {
              alert('卡片資訊輸入錯誤,授權被拒(Card number is denied)');
			  // enable button
			  document.getElementById('paymentbtn').removeAttribute("disabled"); 
          }
      });
    },

    submittrans: function(){
       var oo=document.getElementById('usercheck');
       if(!oo.checked){
           alert('請勾選[確認須知]');
           return false;
       }

       if(document.getElementById('cardnumber').value.length < 19){
           alert('卡號輸入錯誤(Card number is wrong)');
           return false;
       }
       var mrlcard = document.getElementById('cardnumber').value;
       while(mrlcard.indexOf(" ")>=0){mrlcard=mrlcard.replace(" ","");};

       if(document.getElementById('cardholder').value.length < 1){
           alert('請填寫信用卡人名');
           return false;
       }
       var mrlname = document.getElementById('cardholder').value;

       if(document.getElementById('monyear').value.trim().length < 5 ){
           alert('請檢查MM/YY是否輸入錯誤');
           return false;
       }
       var mrlmonth = document.getElementById('monyear').value.split('/')[0].trim();
       var mrlyear = document.getElementById('monyear').value.split('/')[1].trim();

       if(document.getElementById('cvcnum').value.trim().length < 3){
           alert('請檢查CVC是否輸入錯誤');
           return false;
       }


       mrltool.go2pay(mrlcard, mrlname, mrlmonth, mrlyear, document.getElementById('cvcnum').value.trim(),document.getElementById('mrloid').value,document.getElementById('mrlamount').value,'Furnitures');
  
    },
    showSpinBar: function(){
        var opts = {
            lines: 11 // The number of lines to draw
            , length: 14 // The length of each line
            , width: 5 // The line thickness
            , radius: 29 // The radius of the inner circle
            , scale: 1.25 // Scales overall size of the spinner
            , corners: 1 // Corner roundness (0..1)
            , color: '#000' // #rgb or #rrggbb or array of colors
            , opacity: 0.25 // Opacity of the lines
            , rotate: 0 // The rotation offset
            , direction: 1 // 1: clockwise, -1: counterclockwise
            , speed: 1 // Rounds per second
            , trail: 83 // Afterglow percentage
            , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
            , zIndex: 2e9 // The z-index (defaults to 2000000000)
            , className: 'spinner' // The CSS class to assign to the spinner
            , top: '1%' // Top position relative to parent
            , left: '55%' // Left position relative to parent
            , shadow: false // Whether to render a shadow
            , hwaccel: false // Whether to use hardware acceleration
            , position: 'relative' // Element positioning
        };

    var target = document.getElementById('spinbar');
    var tradingtxt = document.getElementById('trading-table');
    tradingtxt.style.display = "block";
    var basictxt = document.getElementById('basic-table');
    basictxt.style.display = "none";
    return new Spinner(opts).spin(target);
    }
}



