# tappay_webpayment
A php sample code for Tappay payment
## Sample Running ENV. 
Ubuntu 16.04.1 LTS
    + PHP7
    + Redis server v3.2

## Config conf/pay.conf.php
* Tappay: appID, appKEY, serverType, merchantid, tappayAPI, vatnumber, partnerkey.

* This sample code: security_key, logs_file, vendor_name, vendor_domain_url, vendor_logo_url, vendor_icon_url, line_contact_url,　service_term_url,service_mail

## Create a payment url to start
https://<your.domain.com>/<app_path>/go2pay.php?oid=000000838&price=41400&uname=Test&key=5bf3dabb3aab6e72e9904fb03c4520be1843d2648562043fbeb434397529d336&dv=mobile
 
