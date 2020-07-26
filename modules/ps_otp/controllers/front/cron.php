<?php


class Ps_OtpCronModuleFrontController extends ModuleFrontController
{

	  public $auth = false;

	  public $ajax;


    public function display()
    {
        $this->ajax = 1;

        if (php_sapi_name() !== 'cli') {
            $this->ajaxDie('Forbidden call.');
        }

        $customerOtp = new CustomerOtp();

        $customerOtp = $customerOtp->getByStatus();

        $mobileNo = null;

        //Hook::exec('actionOrderStateMessage', array('customer' => $customerOtp));

        

        if($customerOtp){
            
            $mobileNo = $customerOtp->mobile_no;
            $customerOtp->is_sent = 1;
            $customerOtp->update();
            $code = $customerOtp->password; 
            //$this->send_sms($mobileNo,$customerOtp->password);
            $msg = "6-Digit Code ( $code ) for login, AUTHENTIC.";
            $this->module->sendSms(array(
              'mobile' => $mobileNo,
              'msg'=>$msg
            ));


        }
        

        $this->ajaxDie("$mobileNo\n");
    }
    
    
    /*public function send_sms($mobile, $code) {
      
      $url = "http://www.btssms.com/smsapi";
      
      $data = [
        "api_key" => "C20000755f0a9e4832aa13.75391278",
        "type" => "text",
        "contacts" => "$mobile",
        "senderid" => "8809601001084",
        "msg" => "6-Digit Code ( $code ) for login, AUTHENTIC.",
      ];
      
      $query = http_build_query($data);
      
      $url = "$url?$query";
      
      
      
      $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));
        
      $msg = file_get_contents($url, false, $context);
        
      return $msg;
     
    }*/


    

	

}