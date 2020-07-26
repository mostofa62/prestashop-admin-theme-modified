<?php

use Symfony\Component\Translation\TranslatorInterface;


class CustomerAddressFormCore extends AbstractForm
{
    private $language;

    protected $template = 'customer/_partials/address-form.tpl';

    private $address;

    private $persister;

    public function __construct(
        Smarty $smarty,
        Language $language,
        TranslatorInterface $translator,
        CustomerAddressPersister $persister,
        CustomerAddressFormatter $formatter
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->language = $language;
        $this->persister = $persister;
    }

    public function loadAddressById($id_address)
    {
        $context = Context::getContext();

        $this->address = new Address($id_address, $this->language->id);

        if ($this->address->id === null) {
            return Tools::redirect('index.php?controller=404');
        }

        if (!$context->customer->isLogged() && !$context->customer->isGuest()) {
            return Tools::redirect('/index.php?controller=authentication');
        }

        if ($this->address->id_customer != $context->customer->id) {
            return Tools::redirect('index.php?controller=404');
        }

        $params = get_object_vars($this->address);
        $params['id_address'] = $this->address->id;

        return $this->fillWith($params);
    }

    public function fillWith(array $params = [])
    {
        // This form is very tricky: fields may change depending on which
        // country is being submitted!
        // So we first update the format if a new id_country was set.
        if (isset($params['id_country'])
            && $params['id_country'] != $this->formatter->getCountry()->id
        ) {
            $this->formatter->setCountry(new Country(
                $params['id_country'],
                $this->language->id
            ));
        }

        return parent::fillWith($params);
    }

    public function validate()
    {
        $is_valid = true;

        if (($postcode = $this->getField('postcode'))) {
            if ($postcode->isRequired()) {
                $country = $this->formatter->getCountry();
                if (!$country->checkZipCode($postcode->getValue())) {
                    $postcode->addError($this->translator->trans(
                        'Invalid postcode - should look like "%zipcode%"',
                        array('%zipcode%' => $country->zip_code_format),
                        'Shop.Forms.Errors'
                    ));
                    $is_valid = false;
                }
            }
        }

        if (($hookReturn = Hook::exec('actionValidateCustomerAddressForm', array('form' => $this))) !== '') {
            $is_valid &= (bool) $hookReturn;
        }

        return $is_valid && parent::validate();
    }

    public function submit()
    {
        if (!$this->validate()) {
            return false;
        }

        $address = new Address(
            Tools::getValue('id_address'),
            $this->language->id
        );

        foreach ($this->formFields as $formField) {
            $address->{$formField->getName()} = $formField->getValue();
        }

        if (!isset($this->formFields['id_state'])) {
            $address->id_state = 0;
        }

        if (empty($address->alias)) {
            $address->alias = $this->translator->trans('My Address', [], 'Shop.Theme.Checkout');
        }

        Hook::exec('actionSubmitCustomerAddressForm', array('address' => &$address));

        $this->setAddress($address);

        return $this->getPersister()->save(
            $address,
            $this->getValue('token')
        );
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return CustomerAddressPersister
     */
    protected function getPersister()
    {
        return $this->persister;
    }

    protected function setAddress(Address $address)
    {
        $this->address = $address;
    }

    public function getTemplateVariables()
    {
        $context = Context::getContext();

        if (!$this->formFields) {
            // This is usually done by fillWith but the form may be
            // rendered before fillWith is called.
            // I don't want to assign formFields in the constructor
            // because it accesses the DB and a constructor should not
            // have side effects.
            $this->formFields = $this->formatter->getFormat();
        }

        $this->setValue('token', $this->persister->getToken());
        $formFields = array_map(
            function (FormField $item) {
                return $item->toArray();
            },
            $this->formFields
        );
        
        /*if (empty($formFields['name']['value'])) {
            $formFields['name']['value'] = $context->customer->name;
        }*/

        return array(
            'id_address' => (isset($this->address->id)) ? $this->address->id : 0,
            'action' => $this->action,
            'errors' => $this->getErrors(),
            'formFields' => $formFields,
        );
    }
}
