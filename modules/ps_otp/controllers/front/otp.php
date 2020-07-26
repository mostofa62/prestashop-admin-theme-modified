<?php


class Ps_OtpOtpModuleFrontController extends ModuleFrontController
{

	public $auth = false;

	//public $ajax;



    

    public function initContent()
    {

        $this->ajax = true;
        parent::initContent();

    }

	/*

	public function initContent()
    {

    	parent::initContent();    	

    	ob_end_clean();
        header('Content-Type: application/json');
        die(json_encode([            
            'id_banner' =>Tools::getValue('id_banner'),
        ]));
    }*/

    /*public function display()
    {
        //$this->ajax = 1;
        
        
        

       

        $this->ajaxDie(json_encode([            
            'id_banner' =>Tools::getValue('id_banner'),
        ]));
    }*/

    public function displayAjax()
    {
        
        $data=['result'=>0,'msg'=>null];

        $mobile_no = Tools::getValue('mobile_no');
        $customer = new Customer();
        $authentication = $customer->getByMobileNo(
           $mobile_no                
        );

        if (isset($authentication->active) && !$authentication->active) {
            $data['result']=0;
            $data['msg'] = $this->trans('Your account isn\'t available at this time, please contact us', [], 'Shop.Notifications.Error');
        } elseif (!$authentication || !$customer->id || $customer->is_guest) {
            $data['result']=0;
            $data['msg'] = $this->trans('Authentication failed, account is not available please create one.', [], 'Shop.Notifications.Error');
        } else {

                $found = CustomerOtp::getByMobileNo($mobile_no);

                $customerOtp = $found? new CustomerOtp($mobile_no): new CustomerOtp();

                //var_dump($customerOtp);die();

                $customerOtp->mobile_no = $mobile_no;
                $customerOtp->password = rand(111111,666666);
                $customerOtp->is_sent = 0;
                
                if($found){
                    $customerOtp->update();
                    $data['result']=1;
                    
                }else{
                    $data['result']=1;
                    $customerOtp->add();
                }
                //sent sms
                $mobileNo = $customerOtp->mobile_no;
                $code = $customerOtp->password;
                $msg = "6-Digit Code ( $code ) for login, AUTHENTIC.";
                
                $this->module->sendSms(array(
                  'mobile' => $mobileNo,
                  'msg'=>$msg
                ));
                //end sent sms

                $data['msg'] = $this->trans("A 6-digit code is sent to this mobile no: $mobile_no", [], 'Shop.Notifications.Error');

        }


        
        header('Content-type: application/json');

        die(Tools::jsonEncode(
            $data
        ));
    }

	

}