<?php

class AdminAddressesControllerCore extends AdminController
{
    /** @var array countries list */
    protected $countries_array = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->required_database = true;
        //$this->required_fields = array('company', 'address2', 'postcode', 'other', 'phone', 'phone_mobile', 'vat_number', 'dni');
        $this->table = 'address';
        $this->className = 'CustomerAddress';
        $this->lang = false;
        $this->addressType = 'customer';
        $this->explicitSelect = true;

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash',
            ),
        );

        $this->allow_export = true;

        if (!Tools::getValue('realedit')) {
            $this->deleted = true;
        }

        $countries = Country::getCountries($this->context->language->id);
        foreach ($countries as $country) {
            $this->countries_array[$country['id_country']] = $country['name'];
        }

        $this->fields_list = array(
            'id_address' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'filter_key' => 'a!name',
                'maxlength' => 65,
            ),
            /*'lastname' => array(
                'title' => $this->trans('Last Name', array(), 'Admin.Global'),
                'filter_key' => 'a!lastname',
                'maxlength' => 30,
            ),*/
            'address1' => array(
                'title' => $this->trans('Address', array(), 'Admin.Global'),
            ),
            /*
            'postcode' => array(
                'title' => $this->trans('Zip/postal code', array(), 'Admin.Global'),
                'align' => 'right',
            ),*/
            'city' => array(
                'title' => $this->trans('City', array(), 'Admin.Global'),
            ),
            'country' => array(
                'title' => $this->trans('Country', array(), 'Admin.Global'),
                'type' => 'select',
                'list' => $this->countries_array,
                'filter_key' => 'cl!id_country',
            ),
        );

        $this->_select = 'cl.`name` as country';
        $this->_join = '
			LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = ' . (int) $this->context->language->id . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON a.id_customer = c.id_customer
		';
        $this->_where = 'AND a.id_customer != 0 ' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c');
        $this->_use_found_rows = false;
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if (!$this->display && $this->can_import) {
            $this->toolbar_btn['import'] = array(
                'href' => $this->context->link->getAdminLink('AdminImport', true, array(), array('import_type' => 'addresses')),
                'desc' => $this->trans('Import', array(), 'Admin.Actions'),
            );
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_address'] = array(
                'href' => $this->context->link->getAdminLink('AdminAddresses', true, array(), array('addaddress' => 1)),
                'desc' => $this->trans('Add new address', array(), 'Admin.Orderscustomers.Feature'),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Addresses', array(), 'Admin.Orderscustomers.Feature'),
                'icon' => 'icon-envelope-alt',
            ),
            'input' => array(
                array(
                    'type' => 'text_customer',
                    'label' => $this->trans('Customer', array(), 'Admin.Global'),
                    'name' => 'id_customer',
                    'required' => false,
                ),/*
                array(
                    'type' => 'text',
                    'label' => $this->trans('Identification number', array(), 'Admin.Orderscustomers.Feature'),
                    'name' => 'dni',
                    'required' => false,
                    'col' => '4',
                    'hint' => $this->trans('The national ID card number of this person, or a unique tax identification number.', array(), 'Admin.Orderscustomers.Feature'),
                ),
                
                array(
                    'type' => 'text',
                    'label' => $this->trans('Address alias', array(), 'Admin.Orderscustomers.Feature'),
                    'name' => 'alias',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info') . ' &lt;&gt;;=#{}',
                ),*/
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    //'required' => in_array('phone_mobile', $required_fields),
                    'required' =>false,
                    'col' => '4',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Mobile phone', array(), 'Admin.Global'),
                    'name' => 'phone_mobile',
                    //'required' => in_array('phone_mobile', $required_fields),
                    'required' =>false,
                    'col' => '4',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Mobile phone 2', array(), 'Admin.Global'),
                    'name' => 'phone',
                    //'required' => in_array('phone_mobile', $required_fields),
                    'required' =>false,
                    'col' => '4',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Address', array(), 'Admin.Global'),
                    'name' => 'address1',
                    'col' => '6',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('City', array(), 'Admin.Global'),
                    'name' => 'city',
                    'col' => '4',
                    'required' => false,
                ),                
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Other', array(), 'Admin.Global'),
                    'name' => 'other',
                    'required' => false,
                    'cols' => 15,
                    'rows' => 3,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info') . ' &lt;&gt;;=#{}',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_order',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'address_type',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'back',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_country'                    
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            ),
        );

        $this->fields_value['address_type'] = (int) Tools::getValue('address_type', 1);

        $this->fields_value['city'] = "Dhaka";

        $this->fields_value['id_country'] = (int) Configuration::get('PS_COUNTRY_DEFAULT');

        $id_customer = (int) Tools::getValue('id_customer');
        if (!$id_customer && Validate::isLoadedObject($this->object)) {
            $id_customer = $this->object->id_customer;

        }
        if ($id_customer) {
            $customer = new Customer((int) $id_customer);
            $this->fields_value['name'] = $customer->name;            
            $this->fields_value['phone_mobile'] = $customer->mobile_no;
        }

        $this->tpl_form_vars = array(
            'customer' => isset($customer) ? $customer : null,
            'customer_view_url' => $this->context->link->getAdminLink('AdminCustomers', true, [], [
                'viewcustomer' => 1,
                'id_customer' => $id_customer,
            ]),
            'back_url' => urldecode(Tools::getValue('back')),
        );

        // Order address fields depending on country format
        //$addresses_fields = $this->processAddressFormat();
        // we use  delivery address
        //$addresses_fields = $addresses_fields['dlv_all_fields'];

        // get required field
        //$required_fields = AddressFormat::getFieldsRequired();

        // Merge with field required
        //$addresses_fields = array_unique(array_merge($addresses_fields, $required_fields));
        /*
        $temp_fields = array();

        foreach ($addresses_fields as $addr_field_item) {
            
            if ($addr_field_item == 'company') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('Company', array(), 'Admin.Global'),
                    'name' => 'company',
                    'required' => in_array('company', $required_fields),
                    'col' => '4',
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info') . ' &lt;&gt;;=#{}',
                );
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('VAT number', array(), 'Admin.Orderscustomers.Feature'),
                    'col' => '2',
                    'name' => 'vat_number',
                    'required' => in_array('vat_number', $required_fields),
                );
            } elseif ($addr_field_item == 'lastname') {
                if (isset($customer) &&
                    !Tools::isSubmit('submit' . strtoupper($this->table)) &&
                    Validate::isLoadedObject($customer) &&
                    !Validate::isLoadedObject($this->object)) {
                    $default_value = $customer->lastname;
                } else {
                    $default_value = '';
                }

                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('Last Name', array(), 'Admin.Global'),
                    'name' => 'lastname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info') . ' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:',
                    'default_value' => $default_value,
                );
            } elseif ($addr_field_item == 'firstname') {
                if (isset($customer) &&
                    !Tools::isSubmit('submit' . strtoupper($this->table)) &&
                    Validate::isLoadedObject($customer) &&
                    !Validate::isLoadedObject($this->object)) {
                    $default_value = $customer->firstname;
                } else {
                    $default_value = '';
                }

                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('First Name', array(), 'Admin.Global'),
                    'name' => 'firstname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info') . ' 0-9!&amp;lt;&amp;gt;,;?=+()@#"�{}_$%:',
                    'default_value' => $default_value,
                );
            } elseif ($addr_field_item == 'address1') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('Address', array(), 'Admin.Global'),
                    'name' => 'address1',
                    'col' => '6',
                    'required' => true,
                );
            } elseif ($addr_field_item == 'address2') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('Address', array(), 'Admin.Global') . ' (2)',
                    'name' => 'address2',
                    'col' => '6',
                    'required' => in_array('address2', $required_fields),
                );
            } elseif ($addr_field_item == 'postcode') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('Zip/postal code', array(), 'Admin.Global'),
                    'name' => 'postcode',
                    'col' => '2',
                    'required' => true,
                );
            } elseif ($addr_field_item == 'city') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('City', array(), 'Admin.Global'),
                    'name' => 'city',
                    'col' => '4',
                    'required' => true,
                );
            } elseif ($addr_field_item == 'country' || $addr_field_item == 'Country:name') {
                $temp_fields[] = array(
                    'type' => 'select',
                    'label' => $this->trans('Country', array(), 'Admin.Global'),
                    'name' => 'id_country',
                    'required' => in_array('Country:name', $required_fields) || in_array('country', $required_fields),
                    'col' => '4',
                    'default_value' => (int) $this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name',
                    ),
                );
                $temp_fields[] = array(
                    'type' => 'select',
                    'label' => $this->trans('State', array(), 'Admin.Global'),
                    'name' => 'id_state',
                    'required' => false,
                    'col' => '4',
                    'options' => array(
                        'query' => array(),
                        'id' => 'id_state',
                        'name' => 'name',
                    ),
                );
            } /*elseif ($addr_field_item == 'phone') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('Home phone', array(), 'Admin.Global'),
                    'name' => 'phone',
                    'required' => in_array('phone', $required_fields),
                    'col' => '4',
                );
            } elseif ($addr_field_item == 'phone_mobile') {
                $temp_fields[] = array(
                    'type' => 'text',
                    'label' => $this->trans('Mobile phone', array(), 'Admin.Global'),
                    'name' => 'phone_mobile',
                    //'required' => in_array('phone_mobile', $required_fields),
                    'required' =>false,
                    'col' => '4',
                );
            }
        }*/

        // merge address format with the rest of the form
        //array_splice($this->fields_form['input'], 3, 0, $temp_fields);

        return parent::renderForm();
    }

    public function processSave()
    {
        if (Tools::getValue('submitFormAjax')) {
            $this->redirect_after = false;
        }

        // Transform e-mail in id_customer for parent processing
        if (Validate::isEmail(Tools::getValue('email'))) {
            $customer = new Customer();
            $customer->getByEmail(Tools::getValue('email'), null, false);
            if (Validate::isLoadedObject($customer)) {
                $_POST['id_customer'] = $customer->id;
            } else {
                $this->errors[] = $this->trans('This email address is not registered.', array(), 'Admin.Orderscustomers.Notification');
            }
        } elseif ($id_customer = Tools::getValue('id_customer')) {
            $customer = new Customer((int) $id_customer);
            if (Validate::isLoadedObject($customer)) {
                $_POST['id_customer'] = $customer->id;
            } else {
                $this->errors[] = $this->trans('This customer ID is not recognized.', array(), 'Admin.Orderscustomers.Notification');
            }
        } else {
            $this->errors[] = $this->trans('This email address is not valid. Please use an address like bob@example.com.', array(), 'Admin.Orderscustomers.Notification');
        }
        

        /* If the selected country does not contain states */
        //$id_state = (int) Tools::getValue('id_state');
        /*$id_country = (int) Tools::getValue('id_country');
        $country = new Country((int) $id_country);*/
        
        /* If this address come from order's edition and is the same as the other one (invoice or delivery one)
        ** we delete its id_address to force the creation of a new one */
        if ((int) Tools::getValue('id_order')) {
            $this->_redirect = false;
            if (isset($_POST['address_type'])) {
                $_POST['id_address'] = '';
                $this->id_object = null;
            }
        }
        
        // Check the requires fields which are settings in the BO
        $address = new Address();
        $address->name = !empty(Tools::getValue('name'))?Tools::getValue('name'):$customer->name;
        $address->phone_mobile = !empty(Tools::getValue('phone_mobile'))?Tools::getValue('phone_mobile'):$customer->mobile_no;

        //var_dump($this->object);die();

        //var_dump($address->name);die();
        $this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

        $return = false;
        if (empty($this->errors)) {
            $return = parent::processSave();
        } else {
            // if we have errors, we stay on the form instead of going back to the list
            $this->display = 'edit';
        }

        /* Reassignation of the order's new (invoice or delivery) address */
        $address_type = (int) Tools::getValue('address_type') == 2 ? 'invoice' : 'delivery';

        if ($this->action == 'save' && ($id_order = (int) Tools::getValue('id_order')) && !count($this->errors) && !empty($address_type)) {
            if (!Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'orders SET `id_address_' . bqSQL($address_type) . '` = ' . (int) $this->object->id . ' WHERE `id_order` = ' . (int) $id_order)) {
                $this->errors[] = $this->trans('An error occurred while linking this address to its order.', array(), 'Admin.Orderscustomers.Notification');
            } else {
                //update order shipping cost
                $order = new Order($id_order);
                $order->refreshShippingCost();

                // update cart
                $cart = Cart::getCartByOrderId($id_order);
                if (Validate::isLoadedObject($cart)) {
                    if ($address_type == 'invoice') {
                        $cart->id_address_invoice = (int) $this->object->id;
                    } else {
                        $cart->id_address_delivery = (int) $this->object->id;
                    }
                    $cart->update();
                }
                // redirect
                Tools::redirectAdmin(urldecode(Tools::getValue('back')) . '&conf=4');
            }
        }

        return $return;
    }

    public function processAdd()
    {
        if (Tools::getValue('submitFormAjax')) {
            $this->redirect_after = false;
        }

        return parent::processAdd();
    }

    

    /**
     * Method called when an ajax request is made.
     *
     * @see AdminController::postProcess()
     */
    public function ajaxProcess()
    {
        if (Tools::isSubmit('email')) {
            $email = pSQL(Tools::getValue('email'));
            $customer = Customer::searchByName($email);
            if (!empty($customer)) {
                $customer = $customer['0'];
                echo json_encode(array('infos' => pSQL($customer['name']) . '_' . pSQL($customer['mobile_no']) . '_' ));
            }
        }

        if (Tools::isSubmit('dni_required')) {
            echo json_encode(['dni_required' => Address::dniRequired((int) Tools::getValue('id_country'))]);
        }

        die;
    }

    /**
     * Object Delete.
     */
    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            /** @var Address $object */
            if (!$object->isUsed()) {
                $this->deleted = false;
            }
        }

        $res = parent::processDelete();

        if ($back = Tools::getValue('back')) {
            $this->redirect_after = urldecode($back) . '&conf=1';
        }

        return $res;
    }

    /**
     * Delete multiple items.
     *
     * @return bool true if succcess
     */
    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $deleted = false;
            foreach ($this->boxes as $id) {
                $to_delete = new Address((int) $id);
                if ($to_delete->isUsed()) {
                    $deleted = true;

                    break;
                }
            }
            $this->deleted = $deleted;
        }

        return parent::processBulkDelete();
    }




}
