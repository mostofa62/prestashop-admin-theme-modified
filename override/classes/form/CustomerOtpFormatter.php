<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerOtpFormatter implements FormFormatterInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFormat()
    {
        return [
            
            'mobile_no' => (new FormField())
                ->setName('mobile_no')
                ->setType('number')
                ->setRequired(true)
                ->setLabel($this->translator->trans(
                    'Mobile No',
                    [],
                    'Shop.Forms.Labels'
                ))
                ->addConstraint('isPhoneNumber'),
            'otp' => (new FormField())
                ->setName('otp')
                ->setType('number')
                ->setRequired(true)
                ->addAvailableValue('class', 'hide')
                ->setLabel($this->translator->trans(
                    'Code',
                    [],
                    'Shop.Forms.Labels'
                ))
                ->addConstraint('isUnsignedInt')
        ];
    }
}
