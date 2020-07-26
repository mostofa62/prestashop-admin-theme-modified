<?php

use Symfony\Component\Translation\TranslatorInterface;

/**
 * StarterTheme TODO: B2B fields, Genders, CSRF.
 */
class CustomerFormCore extends AbstractForm
{
    protected $template = 'customer/_partials/customer-form.tpl';

    private $context;
    private $urls;

    private $customerPersister;
    private $guest_allowed;
    private $passwordRequired = true;

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        CustomerFormatter $formatter,
        CustomerPersister $customerPersister,
        array $urls
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->context = $context;
        $this->urls = $urls;
        $this->customerPersister = $customerPersister;
    }

    public function setGuestAllowed($guest_allowed = true)
    {
        $this->formatter->setPasswordRequired(!$guest_allowed);
        $this->guest_allowed = $guest_allowed;

        return $this;
    }

    public function setPasswordRequired($passwordRequired)
    {
        $this->passwordRequired = $passwordRequired;

        return $this;
    }

    public function fillFromCustomer(Customer $customer)
    {
        $params = get_object_vars($customer);
        $params['birthday'] = $customer->birthday === '0000-00-00' ? null : Tools::displayDate($customer->birthday);

        return $this->fillWith($params);
    }

    /**
     * @return \Customer
     */
    public function getCustomer()
    {
        $customer = new Customer($this->context->customer->id);

        foreach ($this->formFields as $field) {
            $customerField = $field->getName();
            if (property_exists($customer, $customerField)) {
                $customer->$customerField = $field->getValue();
            }
        }

        return $customer;
    }

    public function validate()
    {
        $emailField = $this->getField('email');
        $id_customer = Customer::customerExists($emailField->getValue(), true, true);
        $customer = $this->getCustomer();

        //var_dump($customer->name);die();

        if ($id_customer && $id_customer != $customer->id) {
            $emailField->addError($this->translator->trans(
                'The email is already used, please choose another one or sign in',
                array(),
                'Shop.Notifications.Error'
            ));
        }


        $mobilenoField = $this->getField('mobile_no');
        //var_dump($mobilenoField);die();
        $id_customer_mob = Customer::customerExistsByMobile($mobilenoField->getValue(), true, true);
        if ($id_customer_mob && $id_customer_mob != $customer->id) {
            $mobilenoField->addError($this->translator->trans(
                'The mobile no is already used, please choose another one or sign in',
                array(),
                'Shop.Notifications.Error'
            ));
        }

        // check birthdayField against null case is mandatory.
        /*
        $birthdayField = $this->getField('birthday');
        if (!empty($birthdayField) &&
            !empty($birthdayField->getValue()) &&
            Validate::isBirthDate($birthdayField->getValue(), $this->context->language->date_format_lite)
        ) {
            $dateBuilt = DateTime::createFromFormat(
                $this->context->language->date_format_lite,
                $birthdayField->getValue()
            );
            $birthdayField->setValue($dateBuilt->format('Y-m-d'));
        }*/
        //var_dump($this->getField('name'));
        //die();
        $this->validateFieldsLengths();
        $this->validateByModules();

        return parent::validate();
    }

    protected function validateFieldsLengths()
    {
        $this->validateFieldLength('email', 255, $this->getEmailMaxLengthViolationMessage());

        $this->validateFieldLength('name', 255, $this->getNameMaxLengthViolationMessage());
        /*
        $this->validateFieldLength('firstname', 255, $this->getFirstNameMaxLengthViolationMessage());
        $this->validateFieldLength('lastname', 255, $this->getLastNameMaxLengthViolationMessage());*/
    }

    /**
     * @param $fieldName
     * @param $maximumLength
     * @param $violationMessage
     */
    protected function validateFieldLength($fieldName, $maximumLength, $violationMessage)
    {
        $emailField = $this->getField($fieldName);
        if (strlen($emailField->getValue()) > $maximumLength) {
            $emailField->addError($violationMessage);
        }
    }

    /**
     * @return mixed
     */
    protected function getEmailMaxLengthViolationMessage()
    {
        return $this->translator->trans(
            'The %1$s field is too long (%2$d chars max).',
            array('email', 255),
            'Shop.Notifications.Error'
        );
    }
    /*
    protected function getFirstNameMaxLengthViolationMessage()
    {
        return $this->translator->trans(
            'The %1$s field is too long (%2$d chars max).',
            array('first name', 255),
            'Shop.Notifications.Error'
        );
    }

    protected function getLastNameMaxLengthViolationMessage()
    {
        return $this->translator->trans(
            'The %1$s field is too long (%2$d chars max).',
            array('last name', 255),
            'Shop.Notifications.Error'
        );
    }*/
    protected function getNameMaxLengthViolationMessage(){

        return $this->translator->trans(
            'The %1$s field is too long (%2$d chars max).',
            array('name', 255),
            'Shop.Notifications.Error'
        );

    }

    public function submit()
    {
        

        if ($this->validate()) {
            $clearTextPassword = $this->getValue('password');
            $newPassword = $this->getValue('new_password');

            
            $customer = $this->getCustomer();
            $customer->name = $this->getValue('name');

            $ok = $this->customerPersister->save(
                $customer,                
                $clearTextPassword,
                $newPassword,
                $this->passwordRequired
            );

            if (!$ok) {
                foreach ($this->customerPersister->getErrors() as $field => $errors) {
                    $this->formFields[$field]->setErrors($errors);
                }
            }

            return $ok;
        }

        return false;
    }

    public function getTemplateVariables()
    {
        return [
            'action' => $this->action,
            'urls' => $this->urls,
            'errors' => $this->getErrors(),
            'hook_create_account_form' => Hook::exec('displayCustomerAccountForm'),
            'formFields' => array_map(
                function (FormField $field) {
                    return $field->toArray();
                },
                $this->formFields
            ),
        ];
    }

    /**
     * This function call the hook validateCustomerFormFields of every modules
     * which added one or several fields to the customer registration form.
     *
     * Note: they won't get all the fields from the form, but only the one
     * they added.
     */
    private function validateByModules()
    {
        $formFieldsAssociated = array();
        // Group FormField instances by module name
        foreach ($this->formFields as $formField) {
            if (!empty($formField->moduleName)) {
                $formFieldsAssociated[$formField->moduleName][] = $formField;
            }
        }
        // Because of security reasons (i.e password), we don't send all
        // the values to the module but only the ones it created
        foreach ($formFieldsAssociated as $moduleName => $formFields) {
            if ($moduleId = Module::getModuleIdByName($moduleName)) {
                // ToDo : replace Hook::exec with HookFinder, because we expect a specific class here
                $validatedCustomerFormFields = Hook::exec('validateCustomerFormFields', array('fields' => $formFields), $moduleId, true);

                if (is_array($validatedCustomerFormFields)) {
                    array_merge($this->formFields, $validatedCustomerFormFields);
                }
            }
        }
    }
}
