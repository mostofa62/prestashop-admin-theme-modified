<?php


use Symfony\Component\Translation\TranslatorInterface;

class CustomerAddressFormatterCore implements FormFormatterInterface
{
    private $country;
    private $translator;
    private $availableCountries;
    private $definition;

    public function __construct(
        Country $country,
        TranslatorInterface $translator,
        array $availableCountries
    ) {
        $this->country = $country;
        $this->translator = $translator;
        $this->availableCountries = $availableCountries;
        $this->definition = Address::$definition['fields'];
    }

    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getFieldsRequired(){

        return array(
                        
            'address1',
            'city',            
        );

    }

    public function getFormat()
    {
        $fields = AddressFormat::getOrderedAddressFields(
            $this->country->id,
            true,
            true
        );

        $context = Context::getContext();
        $customer = $context->customer;
        //var_dump($context->customer);die();

        $required = array_flip(AddressFormat::getFieldsRequired());
        //$required = $this->getFieldsRequired();

        //var_dump($required);die();

        $format = [
            'back' => (new FormField())
                ->setName('back')
                ->setType('hidden'),
            'token' => (new FormField())
                ->setName('token')
                ->setType('hidden'),
            'alias' => (new FormField())
                ->setName('alias')
                ->setLabel(
                    $this->getFieldLabel('alias')
                ),

            'id_country' => (new FormField())
                ->setName('id_country')
                ->setValue($this->country->id)
                ->setType('hidden')
                ->setLabel(
                    $this->getFieldLabel('Country')
                ),

            /*'city' => (new FormField())
                ->setName('city')
                ->setValue("Dhaka")
                ->setType('text')
                ->setLabel(
                    $this->getFieldLabel('City')
                ),
            */
        ];

        foreach ($fields as $field) {
            $formField = new FormField();
            $formField->setName($field);

            if($field == 'name'){
                $formField->setValue($customer->name);
            }

            if($field == 'phone_mobile'){
                $formField->setValue($customer->mobile_no);
            }

            if($field == 'city'){
                $formField->setValue("Dhaka");
            }

            $formField->setLabel($this->getFieldLabel($field));
            if (!$formField->isRequired()) {
                
                $formField->setRequired(
                    array_key_exists($field, $required)
                );
            }

            $format[$formField->getName()] = $formField;
        }


        //To add the extra fields in address form
        
        $additionalAddressFormFields = Hook::exec('additionalCustomerAddressFields', array(), null, true);
        if (is_array($additionalAddressFormFields)) {
            foreach ($additionalAddressFormFields as $moduleName => $additionnalFormFields) {
                if (!is_array($additionnalFormFields)) {
                    continue;
                }

                foreach ($additionnalFormFields as $formField) {
                    $formField->moduleName = $moduleName;
                    $format[$moduleName . '_' . $formField->getName()] = $formField;
                }
            }
        }


        

        return $this->addConstraints(
                $this->addMaxLength(
                    $format
                )
        );
    }

    private function addConstraints(array $format)
    {
        foreach ($format as $field) {
            if (!empty($this->definition[$field->getName()]['validate'])) {
                $field->addConstraint(
                    $this->definition[$field->getName()]['validate']
                );
            }
        }

        /*echo "<pre>";
        print_r($format);
        echo "</pre>";
        die();*/

        return $format;
    }

    private function addMaxLength(array $format)
    {
        foreach ($format as $field) {
            if (!empty($this->definition[$field->getName()]['size'])) {
                $field->setMaxLength(
                    $this->definition[$field->getName()]['size']
                );
            }
        }

        return $format;
    }

    private function getFieldLabel($field)
    {
        // Country:name => Country, Country:iso_code => Country,
        // same label regardless of which field is used for mapping.
        $field = explode(':', $field)[0];

        switch ($field) {
            case 'alias':
                return $this->translator->trans('Alias', [], 'Shop.Forms.Labels');
            case 'firstname':
                return $this->translator->trans('First name', [], 'Shop.Forms.Labels');
            case 'lastname':
                return $this->translator->trans('Last name', [], 'Shop.Forms.Labels');
            case 'name':
                return $this->translator->trans('Name', [], 'Shop.Forms.Labels');
            case 'address1':
                return $this->translator->trans('Address', [], 'Shop.Forms.Labels');
            case 'address2':
                return $this->translator->trans('Address Complement', [], 'Shop.Forms.Labels');
            case 'postcode':
                return $this->translator->trans('Zip/Postal Code', [], 'Shop.Forms.Labels');
            case 'city':
                return $this->translator->trans('City', [], 'Shop.Forms.Labels');
            case 'Country':
                return $this->translator->trans('Country', [], 'Shop.Forms.Labels');
            case 'State':
                return $this->translator->trans('State', [], 'Shop.Forms.Labels');
            case 'phone':
                return $this->translator->trans('Mobile phone 2', [], 'Shop.Forms.Labels');
            case 'phone_mobile':
                return $this->translator->trans('Mobile phone', [], 'Shop.Forms.Labels');
            case 'company':
                return $this->translator->trans('Company', [], 'Shop.Forms.Labels');
            case 'vat_number':
                return $this->translator->trans('VAT number', [], 'Shop.Forms.Labels');
            case 'dni':
                return $this->translator->trans('Identification number', [], 'Shop.Forms.Labels');
            case 'other':
                return $this->translator->trans('Other', [], 'Shop.Forms.Labels');
            default:
                return $field;
        }
    }
}
