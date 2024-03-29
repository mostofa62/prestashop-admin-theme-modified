<?php

class OrderPaymentCore extends ObjectModel
{
    public $order_reference;
    public $id_currency;
    public $amount;
    public $payment_method;
    public $conversion_rate;
    public $transaction_id;
    public $card_number;
    public $card_brand;
    public $card_expiration;
    public $card_holder;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_payment',
        'primary' => 'id_order_payment',
        'fields' => array(
            'order_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 30),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'payment_method' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'conversion_rate' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'transaction_id' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'card_number' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'card_brand' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'card_expiration' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'card_holder' => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function add($autodate = true, $nullValues = false)
    {
        if (parent::add($autodate, $nullValues)) {
            Hook::exec('actionPaymentCCAdd', array('paymentCC' => $this));

            return true;
        }

        return false;
    }

    /**
     * Get the detailed payment of an order.
     *
     * @deprecated 1.5.3.0
     *
     * @param int $id_order
     *
     * @return array
     */
    public static function getByOrderId($id_order)
    {
        Tools::displayAsDeprecated();
        $order = new Order($id_order);

        return OrderPayment::getByOrderReference($order->reference);
    }

    /**
     * Get the detailed payment of an order.
     *
     * @param int $order_reference
     *
     * @return array
     *
     * @since 1.5.0.13
     */
    public static function getByOrderReference($order_reference)
    {
        return ObjectModel::hydrateCollection(
            'OrderPayment',
            Db::getInstance()->executeS(
                'SELECT *
			    FROM `' . _DB_PREFIX_ . 'order_payment`
			    WHERE `order_reference` = \'' . pSQL($order_reference) . '\''
            )
        );
    }

    /**
     * Get Order Payments By Invoice ID.
     *
     * @param int $id_invoice Invoice ID
     *
     * @return PrestaShopCollection Collection of OrderPayment
     */
    public static function getByInvoiceId($id_invoice)
    {
        $payments = Db::getInstance()->executeS('SELECT id_order_payment FROM `' . _DB_PREFIX_ . 'order_invoice_payment` WHERE id_order_invoice = ' . (int) $id_invoice);
        if (!$payments) {
            return array();
        }

        $payment_list = array();
        foreach ($payments as $payment) {
            $payment_list[] = $payment['id_order_payment'];
        }

        $payments = new PrestaShopCollection('OrderPayment');
        $payments->where('id_order_payment', 'IN', $payment_list);

        return $payments;
    }

    /**
     * Return order invoice object linked to the payment.
     *
     * @param int $id_order Order Id
     *
     * @since 1.5.0.13
     */
    public function getOrderInvoice($id_order)
    {
        $res = Db::getInstance()->getValue('
		SELECT id_order_invoice
		FROM `' . _DB_PREFIX_ . 'order_invoice_payment`
		WHERE id_order_payment = ' . (int) $this->id . '
		AND id_order = ' . (int) $id_order);

        if (!$res) {
            return false;
        }

        return new OrderInvoice((int) $res);
    }
}
