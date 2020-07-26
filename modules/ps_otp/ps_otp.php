<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


class Ps_Otp extends Module
{

	public function __construct()
    {
        $this->name = 'ps_otp';        
        $this->version = '1.0.0';
        $this->tab = 'administration';
        $this->author = 'Golam Mostofa';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = [
            'min' => '1.7.6',
            'max' => _PS_VERSION_
        ];
        //$this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Arkylus OTP tables for mobile');
        $this->description = $this->l('One time passsword facilites for mobile');

       

       
    }

    public function install()
    {
        return parent::install() &&
        $this->createCustomerOtpTable()  &&
        /*$this->registerHook('actionOrderStateMessage') &&*/
        $this->registerHook('actionCustomerAccountAdd') &&
        $this->registerHook('actionValidateOrder') &&
        $this->registerHook('actionOrderStatusUpdate')
        ;

    }

    public function uninstall()
    {
        return parent::uninstall() &&
        $this->dropCustomerOtpTable();
         
    }

    public function createCustomerOtpTable(){

        
        $db = Db::getInstance();

        $engine =pSQL(_MYSQL_ENGINE_);
        $db_prefix =pSQL(_DB_PREFIX_); 
        
        $sql  = "CREATE TABLE IF NOT EXISTS `{$db_prefix}customer_otp` (                
                `mobile_no` VARCHAR(20) NOT NULL,
                `password` INT(6) NULL,                
                `is_sent` TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`mobile_no`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8";


        return  $db->execute($sql);

    }

    public function dropCustomerOtpTable(){

        $db = Db::getInstance();
        $db_prefix =pSQL(_DB_PREFIX_); 
        
        $sql = "DROP TABLE IF EXISTS
            `{$db_prefix}customer_otp`";

        return $db->execute($sql);



    }
    //hook codes
    /*public function hookActionOrderStateMessage(array $params){

        //file_put_contents(__DIR__.'/d.json', json_encode($params));

    }*/


    public function hookActionCustomerAccountAdd($params) {

        //file_put_contents(__DIR__.'/newcustomer.json', json_encode($params));
        
        $customer = $params['newCustomer'];
        $name = $customer->name;        
        $mobileNo  = $customer->mobile_no;

        $msg = "Welcome $name, a new account is created with this Mobile No($mobileNo), AUTHENTIC.";
        $this->sendSms(array(
          'mobile' => $mobileNo,
          'msg'=>$msg
        )); 

        
            
        
    }


    public function hookActionValidateOrder($params) {

        if(!empty($params['orderStatus'])) {
            
            if ($params['orderStatus']->id == Configuration::get('PS_OS_PREPARATION')){

                $order = $params['order'];
                $customer = $params['customer'];
                /*$orderStatus = $params['orderStatus'];*/
                $name = $customer->name;
                $reference = $order->reference;
                $mobileNo  = $customer->mobile_no;

                $msg = "Dear $name, your order($reference) is Processing in progress, AUTHENTIC.";
                $this->sendSms(array(
                  'mobile' => $mobileNo,
                  'msg'=>$msg
                )); 

               //file_put_contents(__DIR__.'/neworder.json', json_encode($params));
            }
        }
    }

    public function hookActionOrderStatusUpdate($params)
    {        
        if(!empty($params['newOrderStatus'])) {
            if ($params['newOrderStatus']->id == Configuration::get('PS_OS_SHIPPING')){

                    $order = new Order((int)$params['id_order']);
                    $customer = new Customer((int)$order->id_customer);
                    $name = $customer->name;
                    $reference = $order->reference;
                    $mobileNo  = $customer->mobile_no;
                    $msg = "Dear $name, your order no: $reference is on its way, AUTHENTIC.";
                    $this->sendSms(array(
                      'mobile' => $mobileNo,
                      'msg'=>$msg
                    )); 

                    //file_put_contents(__DIR__.'/orderUpdate.json', json_encode($order));
                }
            }
    }

    public function sendSms(array $params){

      //file_put_contents(__DIR__.'/x.json', json_encode($params));

      $url = "http://www.btssms.com/smsapi";
      
      $data = [
        "api_key" => "C20000755f156db0840d66.55754787",
        "type" => "text",
        "contacts" => $params['mobile'],
        "senderid" => "8809601001084",
        "msg" => $params['msg'],
      ];
      
      $query = http_build_query($data);
      
      $url = "$url?$query";
      
      
      
      $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));
        
      $msg = file_get_contents($url, false, $context);
      
      //file_put_contents(__DIR__.'/x.json', json_encode($params));
        
      return $msg;
    }


}