<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


class Ps_OrderExtend extends Module
{

	public function __construct()
    {
        $this->name = 'ps_orderextend';        
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

        $this->displayName = $this->l('Arkylus Order Extend');
        $this->description = $this->l('Order, Invoice, Delivery and Return Slip extended.');

       
       
       
    }

    public function install()
    {
        return parent::install() &&
        $this->alterOrderTable() &&
        $this->alterOrderPaymentTable()
        ;

    }

    public function uninstall()
    {
        return parent::uninstall();
         
         
    }


    public function alterOrderTable(){

        $sql = '
            ALTER TABLE `' . pSQL(_DB_PREFIX_) . 'orders` MODIFY reference VARCHAR(30);
        ';

        return Db::getInstance()->execute($sql);
    }


    public function alterOrderPaymentTable(){

        $sql = '
            ALTER TABLE `' . pSQL(_DB_PREFIX_) . 'order_payment` MODIFY order_reference VARCHAR(30);
        ';

        return Db::getInstance()->execute($sql);
    }

    


}