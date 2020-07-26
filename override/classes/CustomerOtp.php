<?php

use PrestaShop\PrestaShop\Adapter\CoreException;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;

/***
 * Class CustomerOtp
 */
class CustomerOtp extends ObjectModel
{
    

    /** @var int $id_shop Shop ID */
    public $mobile_no;

    /** @var int $id_shop_group ShopGroup ID */
    public $password;

    /** @var string Secure key */
    public $secure_key;

    
    /** @var int Gender ID */
    public $is_sent = 0;

    
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'customer_otp',
        'primary' => 'mobile_no',
        'fields' => array(
            'mobile_no' => array('type' => self::TYPE_STRING,'validate' => 'isPhoneNumber', 'required' => true, 'size' => 20),
             /** end register phone number **/

            'password' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false, 'size' => 6),
            'is_sent' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    
    

    public static function getByMobileNo($mobile_no){
        if (!Validate::isPhoneNumber($mobile_no)){
            die(Tools::displayError());
        }

        $sql = new DbQuery();
        $sql->select('c.`mobile_no`');
        $sql->from('customer_otp', 'c');
        $sql->where('c.`mobile_no` = \'' . pSQL($mobile_no) . '\'');

        //$sql->where('c.`is_sent` = 0');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        //var_dump($result);die();

        if (!$result) {
            return false;
        }

        //$this->id = $result['id_customer'];

        return true;
    }

    public function __construct($id = null)
    {
      
        parent::__construct($id);
    }

    public function getByStatus(){

        $sql = new DbQuery();
        $sql->select('c.*');
        $sql->from('customer_otp', 'c');
        $sql->where('c.`is_sent` = 0');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if (!$result) {
            return false;
        }

        $this->id = $result['mobile_no'];
        foreach ($result as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }


    



 
}
