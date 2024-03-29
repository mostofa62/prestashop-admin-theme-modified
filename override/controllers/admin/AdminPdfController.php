<?php

class AdminPdfControllerCore extends AdminController
{
    public function postProcess()
    {
        parent::postProcess();

        // We want to be sure that displaying PDF is the last thing this controller will do
        exit;
    }

    public function initProcess()
    {
        parent::initProcess();
        $this->checkCacheFolder();
        $access = Profile::getProfileAccess($this->context->employee->id_profile, (int) Tab::getIdFromClassName('AdminOrders'));
        if ($access['view'] === '1' && ($action = Tools::getValue('submitAction'))) {
            $this->action = $action;
        } else {
            $this->errors[] = $this->trans('You do not have permission to view this.', array(), 'Admin.Notifications.Error');
        }
    }

    public function checkCacheFolder()
    {
        if (!is_dir(_PS_CACHE_DIR_ . 'tcpdf/')) {
            mkdir(_PS_CACHE_DIR_ . 'tcpdf/');
        }
    }

    public function processGenerateInvoicePdf()
    {
        if (Tools::isSubmit('id_order')) {
            $this->generateInvoicePDFByIdOrder(Tools::getValue('id_order'));
        } elseif (Tools::isSubmit('id_order_invoice')) {
            $this->generateInvoicePDFByIdOrderInvoice(Tools::getValue('id_order_invoice'));
        } else {
            die($this->trans('The order ID -- or the invoice order ID -- is missing.', array(), 'Admin.Orderscustomers.Notification'));
        }
    }

    public function processGenerateOrderSlipPDF()
    {
        $order_slip = new OrderSlip((int) Tools::getValue('id_order_slip'));
        $order = new Order((int) $order_slip->id_order);

        if (!Validate::isLoadedObject($order)) {
            die($this->trans('The order cannot be found within your database.', array(), 'Admin.Orderscustomers.Notification'));
        }

        $order->products = OrderSlip::getOrdersSlipProducts($order_slip->id, $order);
        $this->generatePDF($order_slip, PDF::TEMPLATE_ORDER_SLIP);
    }

    public function processGenerateDeliverySlipPDF()
    {
        if (Tools::isSubmit('id_order')) {
            $this->generateDeliverySlipPDFByIdOrder((int) Tools::getValue('id_order'));
        } elseif (Tools::isSubmit('id_order_invoice')) {
            $this->generateDeliverySlipPDFByIdOrderInvoice((int) Tools::getValue('id_order_invoice'));
        } elseif (Tools::isSubmit('id_delivery')) {
            $order = Order::getByDelivery((int) Tools::getValue('id_delivery'));
            $this->generateDeliverySlipPDFByIdOrder((int) $order->id);
        } else {
            die($this->trans('The order ID -- or the invoice order ID -- is missing.', array(), 'Admin.Orderscustomers.Notification'));
        }
    }

    public function processGenerateInvoicesPDF()
    {
        $order_invoice_collection = OrderInvoice::getByDateInterval(Tools::getValue('date_from'), Tools::getValue('date_to'));

        if (!count($order_invoice_collection)) {
            die($this->trans('No invoice was found.', array(), 'Admin.Orderscustomers.Notification'));
        }

        $this->generatePDF($order_invoice_collection, PDF::TEMPLATE_INVOICE);
    }

    public function processGenerateInvoicesPDF2()
    {
        $order_invoice_collection = array();
        foreach (explode('-', Tools::getValue('id_order_state')) as $id_order_state) {
            if (is_array($order_invoices = OrderInvoice::getByStatus((int) $id_order_state))) {
                $order_invoice_collection = array_merge($order_invoices, $order_invoice_collection);
            }
        }

        if (!count($order_invoice_collection)) {
            die($this->trans('No invoice was found.', array(), 'Admin.Orderscustomers.Notification'));
        }

        $this->generatePDF($order_invoice_collection, PDF::TEMPLATE_INVOICE);
    }

    public function processGenerateOrderSlipsPDF()
    {
        $id_order_slips_list = OrderSlip::getSlipsIdByDate(Tools::getValue('date_from'), Tools::getValue('date_to'));
        if (!count($id_order_slips_list)) {
            die($this->trans('No order slips were found.', array(), 'Admin.Orderscustomers.Notification'));
        }

        $order_slips = array();
        foreach ($id_order_slips_list as $id_order_slips) {
            $order_slips[] = new OrderSlip((int) $id_order_slips);
        }

        $this->generatePDF($order_slips, PDF::TEMPLATE_ORDER_SLIP);
    }

    public function processGenerateDeliverySlipsPDF()
    {
        $order_invoice_collection = OrderInvoice::getByDeliveryDateInterval(Tools::getValue('date_from'), Tools::getValue('date_to'));

        if (!count($order_invoice_collection)) {
            die($this->trans('No invoice was found.', array(), 'Admin.Orderscustomers.Notification'));
        }

        $this->generatePDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP);
    }

    public function processGenerateSupplyOrderFormPDF()
    {
        if (!Tools::isSubmit('id_supply_order')) {
            die($this->trans('The supply order ID is missing.', array(), 'Admin.Orderscustomers.Notification'));
        }

        $id_supply_order = (int) Tools::getValue('id_supply_order');
        $supply_order = new SupplyOrder($id_supply_order);

        if (!Validate::isLoadedObject($supply_order)) {
            die($this->trans('The supply order cannot be found within your database.', array(), 'Admin.Orderscustomers.Notification'));
        }

        $this->generatePDF($supply_order, PDF::TEMPLATE_SUPPLY_ORDER_FORM);
    }

    public function generateDeliverySlipPDFByIdOrder($id_order)
    {
        $order = new Order((int) $id_order);
        if (!Validate::isLoadedObject($order)) {
            throw new PrestaShopException('Can\'t load Order object');
        }

        $order_invoice_collection = $order->getInvoicesCollection();
        $this->generatePDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP);
    }

    public function generateDeliverySlipPDFByIdOrderInvoice($id_order_invoice)
    {
        $order_invoice = new OrderInvoice((int) $id_order_invoice);
        if (!Validate::isLoadedObject($order_invoice)) {
            throw new PrestaShopException('Can\'t load Order Invoice object');
        }

        $this->generatePDF($order_invoice, PDF::TEMPLATE_DELIVERY_SLIP);
    }
    //modified mostofa
    public function generateInvoicePDFByIdOrder($id_order)
    {
        $order = new Order((int) $id_order);
        
        if (!Validate::isLoadedObject($order)) {
            die($this->trans('The order cannot be found within your database.', array(), 'Admin.Orderscustomers.Notification'));
        }

        $order_invoice_list = $order->getInvoicesCollection();
        //var_dump($order_invoice_list);die();
        Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));
        $this->generatePDF($order_invoice_list, PDF::TEMPLATE_INVOICE_DELIVERY_SLIP);
    }
    //end modified mostofa

    public function generateInvoicePDFByIdOrderInvoice($id_order_invoice)
    {
        $order_invoice = new OrderInvoice((int) $id_order_invoice);
        if (!Validate::isLoadedObject($order_invoice)) {
            die($this->trans('The order invoice cannot be found within your database.', array(), 'Admin.Orderscustomers.Notification'));
        }

        Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => array($order_invoice)));
        $this->generatePDF($order_invoice, PDF::TEMPLATE_INVOICE);
    }

    public function generatePDF($object, $template)
    {
        $pdf = new PDF($object, $template, Context::getContext()->smarty);
        $pdf->render();
    }
}
