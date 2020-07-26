<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerLoginFormatterCore implements FormFormatterInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFormat()
    {
        return [
            'back' => (new FormField())
                ->setName('back')
                ->setType('hidden'),
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
            'password' => (new FormField())
                ->setName('password')
                ->setType('password')
                ->setRequired(true)
                ->setLabel($this->translator->trans(
                    'Password',
                    [],
                    'Shop.Forms.Labels'
                ))
                ->addConstraint('isPasswd'),
        ];
    }
}
